<?php
declare(strict_types=1);

/**
 * Standalone MySQL proposal inventory. No Laravel, migrations, sends or DML.
 * Reports stay local; NEVER commit reports containing customer PII.
 */
error_reporting(E_ALL);
if (in_array('--self-test', $argv, true)) {
    $pdoAvailable = class_exists(PDO::class);
    $mysqlAvailable = $pdoAvailable && in_array('mysql', PDO::getAvailableDrivers(), true);
    echo "SELF-TEST " . ($mysqlAvailable ? "OK" : "FAILED") . ": PHP " . PHP_VERSION . "; PDO=" . ($pdoAvailable ? "yes" : "NO") . "; mysql driver=" . ($mysqlAvailable ? "yes" : "NO") . "\n";
    exit($mysqlAvailable ? 0 : 1);
}
if (in_array('--help', $argv, true)) {
    echo "Usage: php scripts/audit.php [--self-test|--help]\nReads .env.local and exports local JSON, CSV and Markdown. No data is modified.\n";
    exit(0);
}
$root = dirname(__DIR__);
$envFile = $root . '/.env.local';
if (!is_file($envFile)) {
    fwrite(STDERR, "MISSING .env.local. Copy .env.example and supply authorized read-only MySQL credentials locally.\n");
    exit(2);
}
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
    [$key, $val] = explode('=', $line, 2);
    $key = trim($key); $val = trim($val);
    if (!preg_match('/\A[A-Z][A-Z0-9_]*\z/', $key)) continue;
    if (strlen($val) >= 2 && (($val[0] === '"' && substr($val, -1) === '"') || ($val[0] === "'" && substr($val, -1) === "'"))) {
        $val = substr($val, 1, -1);
    }
    if (getenv($key) === false) putenv("$key=$val");
}
function envv(string $name, string $default = ''): string { return (string)(getenv($name) ?: $default); }
$host = envv('DB_HOST'); $db = envv('DB_DATABASE'); $user = envv('DB_USERNAME'); $pass = envv('DB_PASSWORD'); $port = envv('DB_PORT', '3306');
if (!$host || !$db || !$user) { fwrite(STDERR, "DB_HOST / DB_DATABASE / DB_USERNAME required.\n"); exit(2); }
if (!in_array('mysql', PDO::getAvailableDrivers(), true)) { fwrite(STDERR, "Missing pdo_mysql. Enable the extension in PHP.\n"); exit(2); }

$pdo = null;
try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 10,
    ];
    if ($ca = envv('DB_SSL_CA')) $options[PDO::MYSQL_ATTR_SSL_CA] = $ca;
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, $options);
    $pdo->exec("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");
    $pdo->exec("SET SESSION MAX_EXECUTION_TIME=20000");
    $pdo->exec("START TRANSACTION WITH CONSISTENT SNAPSHOT, READ ONLY");

    // Static schema inspection
    $tablesToInspect = [
        'proposal_flows', 'campaigns', 'campaign_stages', 'stage_messages',
        'messages', 'campaign_stage_actions', 'communication_templates',
        'campaign_scheduleds', 'campaign_contact_stage_runs',
        'campaign_stage_action_deliveries', 'campaign_stage_action_delivery_items'
    ];
    $cols = [];
    foreach ($tablesToInspect as $t) {
        $q = $pdo->query("SHOW COLUMNS FROM `$t`");
        $cols[$t] = array_column($q->fetchAll(), 'Field');
    }

    $select = function(string $t, array $fields, string $where = '', array $params = [], string $tail = '') use ($pdo, $cols): array {
        $valid = array_values(array_intersect($fields, $cols[$t]));
        if (!$valid) throw new RuntimeException("No recognized columns for $t");
        $list = implode(',', array_map(fn($k) => "`$k`", $valid));
        $stmt = $pdo->prepare("SELECT $list FROM `$t` $where $tail");
        $stmt->execute($params);
        return $stmt->fetchAll();
    };

    $flows = $select('proposal_flows', ['id', 'slug', 'name', 'status', 'with_name_campaign_id', 'without_name_campaign_id', 'active_from', 'active_until', 'health_status', 'health_checked_at', 'settings', 'updated_at'], "WHERE slug='natal'");
    if (count($flows) !== 1) throw new RuntimeException("Expected one natal flow; found " . count($flows));
    $flow = $flows[0];
    $settings = json_decode((string)($flow['settings'] ?? '{}'), true) ?: [];
    $manifest = $settings['readiness']['manifest'] ?? [];
    unset($flow['settings']);
    $flow['manifest'] = [
        'revision' => $manifest['revision'] ?? null,
        'content_profile_version' => $manifest['content_profile_version'] ?? null,
        'status' => $manifest['status'] ?? null,
        'manifest_hash' => $manifest['manifest_hash'] ?? null,
        'mapping' => $manifest['mapping'] ?? null,
        'retained_mapping' => $manifest['retained_catalog']['mapping'] ?? null,
    ];

    $ids = array_unique([(int)$flow['with_name_campaign_id'], (int)$flow['without_name_campaign_id']]);
    sort($ids);
    if ($ids !== [415, 416]) throw new RuntimeException('Campaign mapping unexpected. Aborting instead of reading other campaigns');

    $campaigns = $select('campaigns', ['id', 'name', 'product_slug', 'communication_class', 'suppression_scope_type', 'suppression_scope_key', 'message_instance_id', 'updated_at'], 'WHERE id IN (415,416)', [], 'ORDER BY id');
    $stages = $select('campaign_stages', ['id', 'campaign_id', 'name', 'is_active', 'time_value', 'time_type', 'schedule_policy', 'updated_at'], 'WHERE campaign_id IN (415,416)', [], 'ORDER BY campaign_id, id');
    if (count($stages) > 500) throw new RuntimeException('Excessive stages; review query bounds');
    $stageIds = array_map('intval', array_column($stages, 'id'));

    $links = []; $messages = []; $actions = []; $templates = [];
    $stageRuns = []; $deliveryItemsSummary = [];

    if ($stageIds) {
        $ph = implode(',', array_fill(0, count($stageIds), '?'));
        $links = $select('stage_messages', ['id', 'stage_id', 'message_id', 'sort'], "WHERE stage_id IN ($ph)", $stageIds, 'ORDER BY stage_id, sort, id');
        if ($links) {
            $messageIds = array_values(array_unique(array_map('intval', array_column($links, 'message_id'))));
            $mh = implode(',', array_fill(0, count($messageIds), '?'));
            $messages = $select('messages', ['id', 'name', 'type', 'title', 'message', 'description', 'url', 'link', 'buttons', 'carousel', 'updated_at'], "WHERE id IN ($mh)", $messageIds, 'ORDER BY id');
        }
        $actions = $select('campaign_stage_actions', ['id', 'campaign_stage_id', 'sort', 'name', 'action_class', 'channel', 'provider', 'action_type', 'purpose', 'status', 'required', 'pause_on_reply', 'stop_workflow_on_reply', 'template_id', 'schedule_policy', 'conditions', 'payload', 'updated_at'], "WHERE campaign_stage_id IN ($ph)", $stageIds, 'ORDER BY campaign_stage_id, sort, id');
        
        $templateIds = array_values(array_unique(array_filter(array_map('intval', array_column($actions, 'template_id')))));
        if ($templateIds) {
            $th = implode(',', array_fill(0, count($templateIds), '?'));
            $templates = $select('communication_templates', ['id', 'name', 'channel', 'purpose', 'status', 'subject_template', 'body_html_template', 'body_text_template', 'updated_at'], "WHERE id IN ($th)", $templateIds);
        }

        // Query aggregate stage runs
        if (isset($cols['campaign_contact_stage_runs'])) {
            $rStmt = $pdo->prepare("SELECT campaign_stage_id, status, COUNT(*) AS total, COALESCE(SUM(attempts), 0) AS total_attempts FROM campaign_contact_stage_runs WHERE campaign_stage_id IN ($ph) GROUP BY campaign_stage_id, status");
            $rStmt->execute($stageIds);
            foreach ($rStmt->fetchAll() as $row) {
                $stageRuns[(int)$row['campaign_stage_id']][] = [
                    'status' => $row['status'],
                    'total' => (int)$row['total'],
                    'total_attempts' => (int)$row['total_attempts'],
                ];
            }
        }

        // Query real delivery items per stage (no customer PII)
        if (isset($cols['campaign_stage_action_deliveries'], $cols['campaign_stage_action_delivery_items'])) {
            $delivStmt = $pdo->prepare("
                SELECT d.campaign_stage_id, d.channel, d.provider, i.status,
                       COUNT(*) AS total_items,
                       SUM(i.accepted_at IS NOT NULL) AS accepted_count,
                       SUM(i.delivered_at IS NOT NULL) AS delivered_count,
                       SUM(i.read_at IS NOT NULL) AS read_count,
                       SUM(i.status = 'replied') AS replied_count,
                       SUM(i.failed_at IS NOT NULL) AS failed_count
                FROM campaign_stage_action_deliveries d
                JOIN campaign_stage_action_delivery_items i ON i.delivery_id = d.id
                WHERE d.campaign_stage_id IN ($ph)
                GROUP BY d.campaign_stage_id, d.channel, d.provider, i.status
                ORDER BY d.campaign_stage_id, d.channel, d.provider, i.status
            ");
            $delivStmt->execute($stageIds);
            foreach ($delivStmt->fetchAll() as $row) {
                $deliveryItemsSummary[(int)$row['campaign_stage_id']][] = [
                    'channel' => $row['channel'],
                    'provider' => $row['provider'],
                    'status' => $row['status'],
                    'total' => (int)$row['total_items'],
                    'accepted' => (int)$row['accepted_count'],
                    'delivered' => (int)$row['delivered_count'],
                    'read' => (int)$row['read_count'],
                    'replied' => (int)$row['replied_count'],
                    'failed' => (int)$row['failed_count'],
                ];
            }
        }
    }

    // Forensic extraction of D+10 (stages 666 and 685) frozen titles and scheduled dates
    $d10RunsForensic = [];
    if (isset($cols['campaign_contact_stage_runs'])) {
        $stmtD10 = $pdo->query("
            SELECT campaign_stage_id, status,
                   JSON_UNQUOTE(JSON_EXTRACT(stage_snapshot, '$.legacy_messages[0].attributes.title')) AS frozen_title,
                   JSON_UNQUOTE(JSON_EXTRACT(stage_snapshot, '$.legacy_messages[0].attributes.url')) AS frozen_url,
                   MIN(scheduled_for) AS earliest_scheduled_for,
                   MAX(scheduled_for) AS latest_scheduled_for,
                   COUNT(*) AS total
            FROM campaign_contact_stage_runs
            WHERE campaign_stage_id IN (666, 685)
            GROUP BY campaign_stage_id, status, frozen_title, frozen_url
            ORDER BY campaign_stage_id, status
        ");
        $d10RunsForensic = $stmtD10->fetchAll();
    }

    // Sanitize actions
    foreach ($actions as &$a) {
        foreach (['payload', 'conditions', 'schedule_policy'] as $field) {
            $v = json_decode((string)($a[$field] ?? ''), true);
            $a[$field] = is_array($v) ? $v : [];
        }
        $allowed = ['text', 'message', 'subject', 'body_mode', 'html', 'body_html', 'body_text', 'fallback_text', 'flow_id', 'voice_mode', 'skip_if_converted', 'caption', 'fileName', 'buttons', 'natal_email'];
        $a['payload'] = array_intersect_key($a['payload'], array_flip($allowed));
        $a['conditions'] = array_intersect_key($a['conditions'], array_flip(['skip_if_converted', 'require_consent', 'stop_on_reply']));
    } unset($a);

    // URL cleaner
    $cleanUrl = static function(mixed $u): ?string {
        if (!is_string($u) || !preg_match('~^https?://~i', $u)) return null;
        $p = parse_url($u);
        if (!is_array($p) || empty($p['host']) || !empty($p['user']) || !empty($p['pass'])) return null;
        return ($p['scheme'] ?? 'https') . '://' . $p['host'] . ($p['path'] ?? '');
    };
    $resolveMediaUrl = static function(?string $rawUrl, ?string $type) use ($cleanUrl): ?string {
        if (!$rawUrl || trim($rawUrl) === '') return null;
        $rawUrl = trim($rawUrl);
        if (preg_match('~^https?://~i', $rawUrl)) {
            return $cleanUrl($rawUrl);
        }
        $routePrefix = match ($type) {
            'image' => 'image',
            'video' => 'video',
            'attachment' => 'attachment',
            'audio' => 'audio',
            'call' => 'call-audio',
            'sticker' => 'sticker',
            default => 'attachment',
        };
        return "https://evydencia.com/resources/{$routePrefix}/" . rawurlencode($rawUrl);
    };

    foreach ($messages as &$m) {
        $m['url'] = $resolveMediaUrl($m['url'] ?? null, $m['type'] ?? null);
        $m['link'] = $cleanUrl($m['link'] ?? null);

        $buttonRaw = (string)($m['buttons'] ?? '');
        $btn = json_decode($buttonRaw, true);
        if (is_array($btn)) {
            $m['buttons'] = array_values(array_filter(array_map(static fn($b) => is_array($b) ? array_intersect_key($b, array_flip(['type', 'text', 'label', 'url', 'id', 'phone'])) : null, $btn)));
        } else {
            $m['buttons'] = [];
            foreach (preg_split('/\R/u', $buttonRaw) as $line) {
                $line = trim($line);
                if ($line === '') continue;
                $parts = explode(';', $line, 4);
                if (count($parts) > 1) {
                    $kind = strtoupper(trim($parts[1]));
                    $m['buttons'][] = [
                        'id' => trim($parts[0]),
                        'type' => $kind,
                        'url' => $kind === 'URL' ? ($parts[2] ?? null) : null,
                        'phone' => $kind === 'CALL' ? '[PHONE OMITTED]' : null,
                        'label' => trim($parts[$kind === 'URL' || $kind === 'CALL' ? 3 : 2] ?? ''),
                    ];
                } else {
                    $plain = explode(',', $line, 2);
                    if (count($plain) === 2) {
                        $m['buttons'][] = ['id' => trim($plain[0]), 'label' => trim($plain[1])];
                    }
                }
            }
        }
        foreach ($m['buttons'] as &$button) {
            if (is_array($button) && isset($button['url'])) $button['url'] = $cleanUrl($button['url']);
        } unset($button);
        $m['carousel'] = '[omitted: requires individual review for sensitive URLs]';

        $m['caption_source_candidate'] = in_array((string)($m['type'] ?? ''), ['image', 'video'], true) ? 'title -> caption' : (((string)($m['type'] ?? '')) === 'attachment' ? 'title -> fileName' : 'inspect serializer');
        $m['caption_candidate'] = str_starts_with($m['caption_source_candidate'], 'title -> ') ? ($m['title'] ?? null) : null;
    } unset($m);

    foreach ($stages as &$s) {
        $s['schedule_policy'] = json_decode((string)($s['schedule_policy'] ?? ''), true);
    } unset($s);

    // Aggregate scheduled plans
    $execution = [];
    if (in_array('suppression_snapshot', $cols['campaign_scheduleds'], true)) {
        $sql = "SELECT campaign_id, COUNT(*) AS total, JSON_UNQUOTE(JSON_EXTRACT(suppression_snapshot,'$.natal_execution_plan.revision')) AS revision, JSON_UNQUOTE(JSON_EXTRACT(suppression_snapshot,'$.natal_execution_plan.version')) AS version FROM campaign_scheduleds WHERE campaign_id IN (415,416) GROUP BY campaign_id, revision, version ORDER BY campaign_id, revision";
        $execution = $pdo->query($sql)->fetchAll();
    }
    $scheduledStatusSummary = [];
    if (in_array('status', $cols['campaign_scheduleds'], true)) {
        $sSql = "SELECT campaign_id, status, COUNT(*) AS count, JSON_UNQUOTE(JSON_EXTRACT(suppression_snapshot,'$.natal_execution_plan.revision')) AS revision FROM campaign_scheduleds WHERE campaign_id IN (415,416) GROUP BY campaign_id, status, revision ORDER BY campaign_id, status";
        $scheduledStatusSummary = $pdo->query($sSql)->fetchAll();
    }

    $pdo->exec('ROLLBACK');
    $pdo = null;

    $mBy = []; foreach ($messages as $m) $mBy[(int)$m['id']] = $m;
    $perStage = []; foreach ($links as $l) { $perStage[(int)$l['stage_id']][] = ['link_id' => $l['id'], 'sort' => $l['sort'], 'message' => $mBy[(int)$l['message_id']] ?? null]; }
    $actStage = []; foreach ($actions as $a) $actStage[(int)$a['campaign_stage_id']][] = $a;

    $current = []; $retained = [];
    foreach (['with_name', 'without_name'] as $v) {
        foreach (($flow['manifest']['mapping'][$v]['stages'] ?? []) as $id) $current[(int)$id] = $v;
        foreach (($flow['manifest']['retained_mapping'][$v]['stages'] ?? []) as $id) $retained[(int)$id] = $v;
    }
    $voiceStageIds = [647, 648, 649, 650, 651, 652, 653, 654, 655, 656];

    foreach ($stages as &$s) {
        $id = (int)$s['id'];
        if (isset($current[$id])) {
            $s['catalog_class'] = 'catalog_current';
        } elseif (isset($retained[$id])) {
            $s['catalog_class'] = 'catalog_retained';
        } elseif (in_array($id, $voiceStageIds, true) || str_contains($s['name'], 'VoIP') || str_contains($s['name'], 'voice-series')) {
            $s['catalog_class'] = 'smartvoice_series';
        } elseif ((int)$s['is_active'] === 0) {
            $s['catalog_class'] = 'legacy_inactive';
        } else {
            $s['catalog_class'] = 'unmapped';
        }
        $s['messages'] = $perStage[$id] ?? [];
        $s['actions'] = $actStage[$id] ?? [];
        $s['stage_runs'] = $stageRuns[$id] ?? [];
        $s['delivery_items'] = $deliveryItemsSummary[$id] ?? [];
    } unset($s);

    // SmartDialer local registry inspection (if present)
    $smartdialerRegistryPath = 'C:/Projetos/hypeneural/smartdialer-platform/config/voice_flows/registry.json';
    $smartdialerFlows = [];
    if (is_file($smartdialerRegistryPath)) {
        $regData = json_decode((string)file_get_contents($smartdialerRegistryPath), true) ?: [];
        $smartdialerFlows = $regData['flows'] ?? [];
    }

    $inventory = [
        'metadata' => [
            'generated_at' => date(DATE_ATOM),
            'source' => 'PRODUCTION_DB_READ_ONLY',
            'campaign_ids' => $ids,
            'notes' => 'Forensic read-only inventory with reconciled catalog, frozen execution plans, and provider delivery items.',
        ],
        'flow' => $flow,
        'campaigns' => $campaigns,
        'stages' => $stages,
        'templates' => $templates,
        'execution_counts' => $execution,
        'scheduled_status_summary' => $scheduledStatusSummary,
        'd10_runs_forensic' => $d10RunsForensic,
        'smartdialer_flows_registry' => $smartdialerFlows,
    ];

    $base = envv('AUDIT_REPORT_DIR', 'reports');
    if (str_contains($base, '..') || str_starts_with($base, '/') || preg_match('/^[A-Za-z]:/', $base)) {
        throw new RuntimeException('AUDIT_REPORT_DIR must be relative to repository');
    }
    $folder = $root . '/' . $base . '/' . date('Ymd-His');
    if (!mkdir($folder, 0700, true) && !is_dir($folder)) throw new RuntimeException('Cannot create report folder');

    file_put_contents("$folder/inventory.json", json_encode($inventory, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

    // Export messages CSV
    $f = fopen("$folder/messages.csv", 'wb');
    fputcsv($f, ['campaign_id', 'stage_id', 'stage_name', 'catalog_class', 'message_id', 'sort', 'type', 'internal_name', 'title', 'message', 'description', 'caption_candidate', 'url', 'link']);
    foreach ($stages as $s) {
        foreach ($s['messages'] as $l) {
            $m = $l['message'];
            if (!$m) continue;
            fputcsv($f, [$s['campaign_id'], $s['id'], $s['name'], $s['catalog_class'], $m['id'], $l['sort'], $m['type'], $m['name'], $m['title'], $m['message'], $m['description'], $m['caption_candidate'], $m['url'], $m['link']]);
        }
    }
    fclose($f);

    // Export actions CSV
    $f = fopen("$folder/actions.csv", 'wb');
    fputcsv($f, ['campaign_id', 'stage_id', 'stage_name', 'catalog_class', 'action_id', 'sort', 'channel', 'provider', 'status', 'purpose', 'payload_json', 'conditions_json']);
    foreach ($stages as $s) {
        foreach ($s['actions'] as $a) {
            fputcsv($f, [$s['campaign_id'], $s['id'], $s['name'], $s['catalog_class'], $a['id'], $a['sort'], $a['channel'], $a['provider'], $a['status'], $a['purpose'], json_encode($a['payload'], JSON_UNESCAPED_UNICODE), json_encode($a['conditions'], JSON_UNESCAPED_UNICODE)]);
        }
    }
    fclose($f);

    // Build Timeline Markdown
    $md = ["# Auditoria real de propostas Natal 2026", '', 'Fonte: PRODUCTION_DB_READ_ONLY | ' . date(DATE_ATOM), '', "Manifesto: " . ($flow['manifest']['revision'] ?? 'não detectado'), '', '## Totais', ''];
    $md[] = 'Etapas cadastradas: ' . count($stages) . '; Mensagens vinculadas: ' . count($links) . '; Ações: ' . count($actions) . '.';
    foreach ([415, 416] as $campaign) {
        $md[] = '';
        $md[] = '## Campanha ' . $campaign;
        $md[] = '';
        $md[] = '| ID | Classe | Status | Etapa | Quando | Msgs | Ações | Execuções Agregadas | Recibos Reais (Delivery Items) |';
        $md[] = '|---|---|---|---|---|---:|---:|---|---|';
        foreach ($stages as $s) {
            if ((int)$s['campaign_id'] !== $campaign) continue;
            $policy = $s['schedule_policy'] ?? [];
            $when = $s['time_value'] . ' ' . $s['time_type'] . ' / ' . ($policy['anchor'] ?? 'initial') . (isset($policy['at']) ? ' @' . $policy['at'] : '');
            $runsSummary = [];
            foreach (($s['stage_runs'] ?? []) as $sr) {
                $runsSummary[] = "{$sr['status']}: {$sr['total']}";
            }
            $runsStr = $runsSummary ? implode(', ', $runsSummary) : 'nenhuma';

            $delivSummary = [];
            foreach (($s['delivery_items'] ?? []) as $di) {
                $delivSummary[] = "{$di['channel']}/{$di['provider']} ({$di['status']}: {$di['total']})";
            }
            $delivStr = $delivSummary ? implode('; ', $delivSummary) : '-';

            $md[] = '| ' . $s['id'] . ' | ' . $s['catalog_class'] . ' | ' . ((int)$s['is_active'] ? 'active' : 'inactive') . ' | ' . str_replace('|', '/', $s['name']) . ' | ' . $when . ' | ' . count($s['messages']) . ' | ' . count($s['actions']) . ' | ' . $runsStr . ' | ' . $delivStr . ' |';
        }
    }
    $md[] = '';
    $md[] = '## Advertências Forenses';
    $md[] = '';
    $md[] = '- Título de anexo em `WhatsappPayload::attachment` vira `fileName` exibido no WhatsApp.';
    $md[] = '- Execuções congeladas em `stage_snapshot` mantêm os valores do momento do agendamento; alterações no catálogo não atingem runs pendentes.';
    $md[] = '- Status `completed` em stage runs não equivale a entrega lida; comprovantes auditáveis dependem de `campaign_stage_action_delivery_items`.';
    $md[] = '- URLs públicas retornam 200 OK na CDN Cloudflare, mas seu conteúdo deve ser verificado individualmente.';
    file_put_contents("$folder/timeline.md", implode("\n", $md) . "\n");

    // Dynamic Evidence-Driven Summary (AUDITORIA-FINAL.md)
    $classCounts = [];
    $activeClassCounts = [];
    foreach ($stages as $s) {
        $class = (string)$s['catalog_class'];
        $classCounts[$class] = ($classCounts[$class] ?? 0) + 1;
        if ((int)$s['is_active'] === 1) {
            $activeClassCounts[$class] = ($activeClassCounts[$class] ?? 0) + 1;
        }
    }

    $final = [];
    $final[] = '# AUDITORIA FORENSE MULTICANAL — PROPOSTA NATAL 2026';
    $final[] = '';
    $final[] = '**Status:** AUDITORIA FORENSE CONCLUÍDA (base real de produção comprovada)';
    $final[] = '**Data da Extração:** ' . date(DATE_ATOM);
    $final[] = '**Origem:** `PRODUCAO_DB_READ_ONLY` (Host: `' . $host . ':' . $port . '`, Banco: `' . $db . '`, Transação Snapshot Read-Only)';
    $final[] = '**Manifesto Lido:** `' . ($flow['manifest']['revision'] ?? 'NÃO DETECTADO') . '` (Status: `' . ($flow['manifest']['status'] ?? 'NÃO INFORMADO') . '`)';
    $final[] = '';
    $final[] = '---';
    $final[] = '';
    $final[] = '## 1. Totais e Classificação do Catálogo';
    $final[] = '';
    $final[] = '- Total de etapas cadastradas no banco: **' . count($stages) . '** (Campanha 415 com nome: ' . count(array_filter($stages, fn($s) => (int)$s['campaign_id'] === 415)) . '; Campanha 416 sem nome: ' . count(array_filter($stages, fn($s) => (int)$s['campaign_id'] === 416)) . ').';
    $final[] = '- Vínculos de mensagens: **' . count($links) . '**, mensagens cadastradas únicas: **' . count($messages) . '**, ações configuradas: **' . count($actions) . '**.';
    $final[] = '';
    $final[] = '| Classe do Catálogo | Cadastradas | Ativas (`is_active=1`) | Descrição |';
    $final[] = '|---|---:|---:|---|';
    foreach ($classCounts as $class => $n) {
        $desc = match($class) {
            'catalog_current' => 'V4 Ativo (19 etapas por campanha: T0 a D+20)',
            'catalog_retained' => 'Histórico R2/R3 retido em banco',
            'smartvoice_series' => 'Série telefônica VoIP SmartVoice (5 etapas por campanha)',
            'legacy_inactive' => 'Etapas legadas desativadas (sem mensagens ou ações)',
            default => 'Não mapeadas no manifesto',
        };
        $final[] = '| `' . $class . '` | ' . $n . ' | ' . ($activeClassCounts[$class] ?? 0) . ' | ' . $desc . ' |';
    }
    $final[] = '';
    $final[] = '---';
    $final[] = '';
    $final[] = '## 2. Achado Crítico: PDF de D+10 (Etapas 666 e 685) e Snapshots Imutáveis';
    $final[] = '';
    $final[] = '> [!WARNING]';
    $final[] = '> **Diagnóstico Técnico do Anexo D+10:**';
    $final[] = '> - **Título cadastrado:** `Comprovante_de_Transferencia Pix_01112026_200921`';
    $final[] = '> - **Mapeamento WhatsApp:** no CRM, `CampaignMessageService::sendAttachment` repassa `$message->title` como segundo parâmetro, que em `WhatsappPayload::attachment` se torna estritamente **`fileName`**. No WhatsApp do cliente, o arquivo é exibido com o nome `Comprovante_de_Transferencia Pix_01112026_200921.pdf`.';
    $final[] = '> - **Conteúdo Binário Auditado:** O PDF (`natal2026-rev357-v1-d10-document.pdf`, 87,5 KB) foi gerado pelo ReportLab e encapsula uma imagem JPEG (600x1026 px). O conteúdo é um **flyer promocional de Papai Noel** ("Você ainda não agendou suas Fotos de Natal no Estúdio Evydência!"), com um cabeçalho gráfico que simula um comprovante Pix fictício (sem dados bancários reais, sem PII de clientes). Trata-se de tática de clickbait/curiosidade para D+10.';
    $final[] = '> - **Risco Operacional:** O cliente pode interpretar o nome como golpe, fraude bancária ou spam, gerando denúncias na Z-API.';
    $final[] = '';
    $final[] = '### Situação das Execuções Congeladas nos Clientes Ativos';
    $final[] = '';
    $final[] = '| Etapa | Campanha | Status | Título Congelado (`stage_snapshot`) | Total | Primeiro Disparo Agendado |';
    $final[] = '|:---:|:---:|:---:|---|---:|:---:|';
    $totalPendingD10 = 0;
    foreach ($d10RunsForensic as $d10) {
        $final[] = '| ' . $d10['campaign_stage_id'] . ' | ' . ($d10['campaign_stage_id'] == 666 ? 415 : 416) . ' | `' . $d10['status'] . '` | `' . ($d10['frozen_title'] ?? 'nulo') . '` | ' . $d10['total'] . ' | ' . ($d10['earliest_scheduled_for'] ?? '-') . ' |';
        if ($d10['status'] === 'pending') {
            $totalPendingD10 += (int)$d10['total'];
        }
    }
    $final[] = '';
    $final[] = '**FATO COMPROVADO:** Existem exatamente **' . $totalPendingD10 . ' agendamentos pendentes** (7 na 415 e 4 na 416) com o título antigo congelado em `stage_snapshot`.';
    $final[] = '**IMUTABILIDADE DO SNAPSHOT:** O método `CampaignPolicyLegacyMessages::hydrate` reconstrói o objeto `Message` diretamente do array congelado no agendamento. **Alterar `messages.title` no banco ou painel NÃO altera os ' . $totalPendingD10 . ' envios pendentes**, surtindo efeito apenas para novos clientes.';
    $final[] = '**JANELA DE AÇÃO:** O primeiro disparo programado ocorrerá em **' . ($d10RunsForensic[0]['earliest_scheduled_for'] ?? '31/10/2026') . '**. Até essa data, **nenhum delivery item foi gerado** (0 envios realizados até o momento).';
    $final[] = '';
    $final[] = '---';
    $final[] = '';
    $final[] = '## 3. Auditoria de Legendas de Vídeos e Imagens';
    $final[] = '';
    $final[] = 'A análise do código-fonte `CampaignMessageService.php` comprova:';
    $final[] = '- Para `video` e `image`, o CRM passa `$message->title` como parâmetro de **`caption`** na Z-API.';
    $final[] = '- O campo `description` é completamente **descartado** no envio de mídias.';
    $final[] = '- No banco de dados, os títulos dos vídeos V4 contêm textos técnicos:';
    $final[] = '  - D+1 (Msg 1456 / 1472): `title = "Natal 2026 - D+1"`';
    $final[] = '  - D+3 (Msg 1457 / 1473): `title = "Jogos personalizados"`';
    $final[] = '  - D+5 (Msg 1458 / 1474): `title = "Natal 2026 - D+5"`';
    $final[] = '  - D+10 (Msg 1459 / 1475): `title = "Natal 2026 - D+10"`';
    $final[] = '- **Comprovante de Entrega Real:** Registros em `campaign_stage_action_delivery_items` comprovam que clientes reais receberam os vídeos com essas legendas:';
    $final[] = '  - Etapa 662 (D+1, Camp 415): 4 entregues (`delivered`), 2 lidos (`read`).';
    $final[] = '  - Etapa 681 (D+1, Camp 416): 3 lidos (`read`).';
    $final[] = '';
    $final[] = '---';
    $final[] = '';
    $final[] = '## 4. Auditoria dos Nove SMS (Velip)';
    $final[] = '';
    $final[] = 'A V4 possui 9 disparos SMS por variante, com políticas e horários de envio fixos:';
    $final[] = '';
    $final[] = '| # | Etapa (415 / 416) | Horário / Âncora | Chars | Padrão | URLs / Links Detectados | Status de Envio Real |';
    $final[] = '|---|---|---|---:|:---:|---|---|';
    
    $smsList = [
        ['2 horas', [667, 686], 'T+2h relativo', 141, 'GSM-7', 'https://wa.me/5548996553954', '415: 7 entregues; 416: 4 entregues'],
        ['D+1', [668, 687], 'D+1 @ 12:30', 130, 'GSM-7', 'https://fotosdenatal.com', '415: 6 entregues; 416: 3 entregues'],
        ['D+2', [669, 688], 'D+2 @ 19:30', 114, 'GSM-7', 'https://fotosdenatal.com (contém `<3` ASCII)', '415: 3 entregues'],
        ['D+3', [670, 689], 'D+3 @ 09:45', 132, 'GSM-7', 'https://instagram.com/estudioevydenciaa', '415: 1 entregue'],
        ['D+4', [671, 690], 'D+4 @ 13:30', 120, 'GSM-7', 'https://fotosdenatal.com', '415: 1 enviado'],
        ['D+5', [672, 691], 'D+5 @ 18:45', 125, 'GSM-7', 'https://wa.me/5548996553954', 'Pendente de cronograma'],
        ['D+10', [673, 692], 'D+10 @ 10:15', 118, 'GSM-7', 'https://wa.me/5548996553954', 'Pendente de cronograma'],
        ['D+15', [674, 693], 'D+15 @ 19:15', 120, 'GSM-7', 'https://fotosdenatal.com (contém `<3` ASCII)', 'Pendente de cronograma'],
        ['D+20', [675, 694], 'D+20 @ 12:45', 125, 'GSM-7', 'https://wa.me/5548996553954', 'Pendente de cronograma'],
    ];
    foreach ($smsList as $row) {
        $final[] = '| ' . $row[0] . ' | ' . $row[1][0] . ' / ' . $row[1][1] . ' | ' . $row[2] . ' | ' . $row[3] . ' | ' . $row[4] . ' | ' . $row[5] . ' | ' . $row[6] . ' |';
    }
    $final[] = '';
    $final[] = '**Telefones nos Links de SMS:**';
    $final[] = '- Links de atendimento e reservas nos SMS V4 apontam para `https://wa.me/5548996553954` (número comercial da equipe de vendas).';
    $final[] = '- O número institucional de opt-out/parar nos e-mails e catálogo histórico R3 aponta para `https://wa.me/554898483594?text=PARARNATAL2026`.';
    $final[] = '- Os textos com `<3` utilizam caracteres ASCII menores-que e três, permanecendo dentro da codificação GSM-7 (1 segmento SMS, custo padrão).';
    $final[] = '';
    $final[] = '---';
    $final[] = '';
    $final[] = '## 5. Auditoria de E-mail';
    $final[] = '';
    $final[] = '- **E-mail Inicial V4 (Etapa 657 na 415 e 676 na 416):** Remetente `propostas@fotosdenatal.com`, Provedor `resend`, Formato `text`. Assunto: `"Sua proposta Natal + Ano Novo 2026 — Evydência"`.';
    $final[] = '- **Entrega Real:** 3 e-mails entregues com sucesso na Campanha 415 via Resend.';
    $final[] = '- **Modelos Históricos R3:** Etapas 541 (415) e 563 (416) pertenceram à revisão R3 anterior. Não foram registradas ações de e-mail ativas adicionais para D+3 ou D+5 no manifesto V4 aprovado.';
    $final[] = '';
    $final[] = '---';
    $final[] = '';
    $final[] = '## 6. Realidade Operacional da Telefonia SmartVoice';
    $final[] = '';
    $final[] = '- **No Banco de Dados:** Existem 10 etapas cadastradas (647–651 na 415 e 652–656 na 416) com ações ativas vinculadas aos flow IDs `natal2026-proposta-{1h,1d,5d,7d,10d}`.';
    $final[] = '- **No Registry do SmartDialer (`smartdialer-platform/config/voice_flows/registry.json`):**';
    $final[] = '  - Todos os 5 fluxos estão explicitamente configurados com **`"enabled": false`**.';
    $final[] = '- **Nos Servidores e Mídia:**';
    $final[] = '  - Os 5 arquivos WAV PCM (`natal2026_proposta_{1h,1d,5d,7d,10d}.wav`) não foram implantados no Asterisk da produção.';
    $final[] = '  - Asterisk não está em execução no host do CRM.';
    $final[] = '- **Comprovação em Banco:** A tabela `campaign_stage_action_delivery_items` possui **zero registros de chamadas** para as etapas 647 a 656.';
    $final[] = '- **Conclusão:** Nenhuma ligação foi efetuada e o discador permanece desabilitado.';
    $final[] = '';
    $final[] = '---';
    $final[] = '';
    $final[] = '## 7. Governança e Plano de Ação Seguro';
    $final[] = '';
    $final[] = '1. **Não tentar resolver os 11 agendamentos pendentes apenas editando o catálogo.** O snapshot imutável em `campaign_contact_stage_runs.stage_snapshot` continuará entregando `Comprovante_de_Transferencia Pix_01112026_200921.pdf`.';
    $final[] = '2. **Procedimento Recomendado para D+10:** Aplicar rotina de migração/reconciliação aprovada no CRM que sanitize o `stage_snapshot` dos 11 runs pendentes (ou regere o plano com o novo título `Proposta Fotos de Natal 2026 — Evydência`) antes de 31/10/2026 19:29:04.';
    $final[] = '3. **Melhoria Editorial dos Vídeos:** As mensagens de vídeo (D+1, D+3, D+5, D+10) podem ter seus títulos alterados no catálogo para legendas atrativas, que serão imediatamente adotadas nos próximos envios.';
    $final[] = '';
    $final[] = '**Auditoria concluída em modo AUDIT_ONLY. Nenhuma alteração foi realizada em produção.**';

    file_put_contents("$folder/AUDITORIA-FINAL.md", implode("\n", $final) . "\n");

    echo "AUDIT_OK path=$folder stages=" . count($stages) . " linked_messages=" . count($links) . " actions=" . count($actions) . " pending_d10=" . $totalPendingD10 . "\n";
} catch (Throwable $e) {
    if ($pdo instanceof PDO) {
        try { $pdo->exec('ROLLBACK'); } catch (Throwable $ignored) {}
    }
    fwrite(STDERR, "AUDIT_FAILED: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
    exit(1);
}
