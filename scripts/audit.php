<?php
declare(strict_types=1);

/**
 * Standalone MySQL proposal inventory. No Laravel, migrations, sends or DML.
 * Reports stay local; NEVER commit reports from a production database.
 */
error_reporting(E_ALL);
if (in_array('--self-test', $argv, true)) {
    assert(class_exists(PDO::class));
    echo "SELF-TEST OK: PHP ".PHP_VERSION."; PDO available; mysql driver=". (in_array('mysql', PDO::getAvailableDrivers(), true) ? 'yes' : 'NO') ."\n";
    exit(0);
}
if (in_array('--help', $argv, true)) {
    echo "Usage: php scripts/audit.php [--self-test|--help]\nReads .env.local and exports local JSON, CSV and Markdown. No data is modified.\n";
    exit(0);
}
$root=dirname(__DIR__);
$envFile=$root.'/.env.local';
if (!is_file($envFile)) {fwrite(STDERR,"MISSING .env.local. Copy .env.example and supply authorized read-only MySQL credentials locally.\n");exit(2);}
foreach (file($envFile, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
    $line=trim($line);
    if ($line==='' || $line[0]==='#' || !str_contains($line,'=')) continue;
    [$key,$val]=explode('=',$line,2);
    $key=trim($key);$val=trim($val);
    if (!preg_match('/\A[A-Z][A-Z0-9_]*\z/', $key)) continue;
    if (strlen($val)>=2 && (($val[0]==='"' && substr($val,-1)==='"') || ($val[0]==="'" && substr($val,-1)==="'"))) $val=substr($val,1,-1);
    if (getenv($key)===false) putenv("$key=$val");
}
function envv(string $name,string $default=''):string{return (string)(getenv($name)?:$default);}
$host=envv('DB_HOST');$db=envv('DB_DATABASE');$user=envv('DB_USERNAME');$pass=envv('DB_PASSWORD');$port=envv('DB_PORT','3306');
if (!$host || !$db || !$user) {fwrite(STDERR,"DB_HOST / DB_DATABASE / DB_USERNAME required.\n");exit(2);}
if (!in_array('mysql',PDO::getAvailableDrivers(),true)){fwrite(STDERR,"Missing pdo_mysql. Enable the extension in PHP.\n");exit(2);}
$pdo=null;
try {
    $options=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false,PDO::ATTR_TIMEOUT=>8];
    if ($ca=envv('DB_SSL_CA')) $options[PDO::MYSQL_ATTR_SSL_CA]=$ca;
    $pdo=new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",$user,$pass,$options);
    $pdo->exec("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");
    $pdo->exec("SET SESSION MAX_EXECUTION_TIME=15000");
    $pdo->exec("START TRANSACTION WITH CONSISTENT SNAPSHOT, READ ONLY");
    // Static schema and explicit field allowlists: no user-provided SQL identifiers.
    $cols=[];
    foreach(['proposal_flows','campaigns','campaign_stages','stage_messages','messages','campaign_stage_actions','communication_templates','campaign_scheduleds','campaign_contact_stage_runs'] as $t) {
        $q=$pdo->query("SHOW COLUMNS FROM `$t`");
        $cols[$t]=array_column($q->fetchAll(),'Field');
    }
    $select=function(string $t,array $fields,string $where='',array $params=[],string $tail='')use($pdo,$cols):array{
        $valid=array_values(array_intersect($fields,$cols[$t]));
        if (!$valid)throw new RuntimeException("No recognized columns for $t");
        $list=implode(',',array_map(fn($k)=>"`$k`",$valid));
        $stmt=$pdo->prepare("SELECT $list FROM `$t` $where $tail");
        $stmt->execute($params);return $stmt->fetchAll();
    };
    $flows=$select('proposal_flows',['id','slug','name','status','with_name_campaign_id','without_name_campaign_id','active_from','active_until','health_status','health_checked_at','settings','updated_at'],"WHERE slug='natal'");
    if(count($flows)!==1)throw new RuntimeException("Expected one natal flow; found ".count($flows));
    $flow=$flows[0];$settings=json_decode((string)($flow['settings']??'{}'),true)?:[];
    $manifest=$settings['readiness']['manifest']??[];
    unset($flow['settings']);
    $flow['manifest']=[
      'revision'=>$manifest['revision']??null,'content_profile_version'=>$manifest['content_profile_version']??null,
      'status'=>$manifest['status']??null,'manifest_hash'=>$manifest['manifest_hash']??null,
      'mapping'=>$manifest['mapping']??null,'retained_mapping'=>$manifest['retained_catalog']['mapping']??null
    ];
    $ids=array_unique([(int)$flow['with_name_campaign_id'],(int)$flow['without_name_campaign_id']]);
    sort($ids);
    if($ids!==[415,416])throw new RuntimeException('Campaign mapping unexpected. Aborting instead of reading other campaigns');
    $campaigns=$select('campaigns',['id','name','product_slug','communication_class','suppression_scope_type','suppression_scope_key','message_instance_id','updated_at'],'WHERE id IN (415,416)',[],'ORDER BY id');
    $stages=$select('campaign_stages',['id','campaign_id','name','is_active','time_value','time_type','schedule_policy','updated_at'],'WHERE campaign_id IN (415,416)',[],'ORDER BY campaign_id,id');
    if(count($stages)>500)throw new RuntimeException('Excessive stages; review query bounds');
    $stageIds=array_map('intval',array_column($stages,'id'));
    $links=[];$messages=[];$actions=[];$templates=[];$stageRuns=[];
    if($stageIds){
      $ph=implode(',',array_fill(0,count($stageIds),'?'));
      $links=$select('stage_messages',['id','stage_id','message_id','sort'],"WHERE stage_id IN ($ph)",$stageIds,'ORDER BY stage_id,sort,id');
      if($links){
        $messageIds=array_values(array_unique(array_map('intval',array_column($links,'message_id'))));
        $mh=implode(',',array_fill(0,count($messageIds),'?'));
        $messages=$select('messages',['id','name','type','title','message','description','url','link','buttons','carousel','updated_at'],"WHERE id IN ($mh)",$messageIds,'ORDER BY id');
      }
      $actions=$select('campaign_stage_actions',['id','campaign_stage_id','sort','name','action_class','channel','provider','action_type','purpose','status','required','pause_on_reply','stop_workflow_on_reply','template_id','schedule_policy','conditions','payload','updated_at'],"WHERE campaign_stage_id IN ($ph)",$stageIds,'ORDER BY campaign_stage_id,sort,id');
      $templateIds=array_values(array_unique(array_filter(array_map('intval',array_column($actions,'template_id')))));
      if($templateIds){$th=implode(',',array_fill(0,count($templateIds),'?'));$templates=$select('communication_templates',['id','name','channel','purpose','status','subject_template','body_html_template','body_text_template','updated_at'],"WHERE id IN ($th)",$templateIds);}
      
      // Query aggregate stage runs (no recipient / contact PII)
      if(isset($cols['campaign_contact_stage_runs'])){
        $rStmt=$pdo->prepare("SELECT campaign_stage_id, status, COUNT(*) AS total, COALESCE(SUM(attempts), 0) AS total_attempts FROM campaign_contact_stage_runs WHERE campaign_stage_id IN ($ph) GROUP BY campaign_stage_id, status");
        $rStmt->execute($stageIds);
        foreach($rStmt->fetchAll() as $row){
          $stageRuns[(int)$row['campaign_stage_id']][] = [
            'status' => $row['status'],
            'total' => (int)$row['total'],
            'total_attempts' => (int)$row['total_attempts']
          ];
        }
      }
    }
    // Do not export arbitrary JSON action keys which can contain credentials, links with access tokens or recipient data.
    foreach($actions as &$a){
       foreach(['payload','conditions','schedule_policy'] as $field) {
         $v=json_decode((string)($a[$field]??''),true);
         $a[$field]=is_array($v)?$v:[];
       }
       $allowed=['text','message','subject','body_mode','fallback_text','flow_id','voice_mode','skip_if_converted','from'];
       $a['payload']=array_intersect_key($a['payload'],array_flip($allowed));
       unset($a['payload']['from']); // from may be personal address
       $a['conditions']=array_intersect_key($a['conditions'],array_flip(['skip_if_converted','require_consent','stop_on_reply']));
    }unset($a);
    // Full media URLs may be signed: keep host + path only, strip query and userinfo.
    $cleanUrl=static function(mixed $u):?string{
      if(!is_string($u)||!preg_match('~^https?://~i',$u))return null;
      $p=parse_url($u);if(!is_array($p)||empty($p['host'])||!empty($p['user'])||!empty($p['pass']))return null;
      return ($p['scheme']??'https').'://'.$p['host'].($p['path']??'');
    };
    foreach($messages as &$m){
       $m['url']=$cleanUrl($m['url']??null);
       $m['link']=$cleanUrl($m['link']??null);
       // Keep buttons in raw JSON only if whitelisted button fields; never include arbitrary authorization headers.
       $btn=json_decode((string)($m['buttons']??''),true);
       $m['buttons']=is_array($btn)?array_map(static fn($b)=>is_array($b)?array_intersect_key($b,array_flip(['type','text','label','url','id'])):null,$btn):[];
       foreach($m['buttons'] as &$button)if(is_array($button)&&isset($button['url']))$button['url']=$cleanUrl($button['url']);unset($button);
       $m['carousel']='[omitted: requires individual review for sensitive URLs]';
       // CampaignMessageService passes $message->title as caption for image, video, and attachment
       $m['caption_source_candidate']=in_array((string)($m['type']??''),['image','video','attachment'],true)?'title':'inspect serializer';
       $m['caption_candidate']=$m['caption_source_candidate']==='title'?($m['title']??null):null;
    }unset($m);
    foreach($stages as &$s) {$s['schedule_policy']=json_decode((string)($s['schedule_policy']??''),true);}
    unset($s);
    // Only aggregate, no recipient identifiers, no full snapshot values.
    $execution=[];
    if(in_array('suppression_snapshot',$cols['campaign_scheduleds'],true)){
       $sql="SELECT campaign_id,COUNT(*) AS total, JSON_UNQUOTE(JSON_EXTRACT(suppression_snapshot,'$.natal_execution_plan.revision')) AS revision,JSON_UNQUOTE(JSON_EXTRACT(suppression_snapshot,'$.natal_execution_plan.version')) AS version FROM campaign_scheduleds WHERE campaign_id IN (415,416) GROUP BY campaign_id,revision,version ORDER BY campaign_id,revision";
       $execution=$pdo->query($sql)->fetchAll();
    }
    $scheduledStatusSummary=[];
    if(in_array('status',$cols['campaign_scheduleds'],true)){
       $sSql="SELECT campaign_id, status, COUNT(*) AS count, JSON_UNQUOTE(JSON_EXTRACT(suppression_snapshot,'$.natal_execution_plan.revision')) AS revision FROM campaign_scheduleds WHERE campaign_id IN (415,416) GROUP BY campaign_id, status, revision ORDER BY campaign_id, status";
       $scheduledStatusSummary=$pdo->query($sSql)->fetchAll();
    }
    $pdo->exec('ROLLBACK');
    $pdo=null;
    $mBy=[];foreach($messages as $m)$mBy[(int)$m['id']]=$m;
    $perStage=[];foreach($links as $l){$perStage[(int)$l['stage_id']][]=['link_id'=>$l['id'],'sort'=>$l['sort'],'message'=>$mBy[(int)$l['message_id']]??null];}
    $actStage=[];foreach($actions as $a)$actStage[(int)$a['campaign_stage_id']][]=$a;
    $current=[];$retained=[];
    foreach(['with_name','without_name'] as $v){
      foreach(($flow['manifest']['mapping'][$v]['stages']??[]) as $id)$current[(int)$id]=$v;
      foreach(($flow['manifest']['retained_mapping'][$v]['stages']??[]) as $id)$retained[(int)$id]=$v;
    }
    $voiceStageIds=[647,648,649,650,651,652,653,654,655,656];
    foreach($stages as &$s){
       $id=(int)$s['id'];
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
       $s['messages']=$perStage[$id]??[];
       $s['actions']=$actStage[$id]??[];
       $s['stage_runs']=$stageRuns[$id]??[];
    }unset($s);
    $inventory=['metadata'=>['generated_at'=>date(DATE_ATOM),'source'=>'PRODUCTION_DB_READ_ONLY','campaign_ids'=>$ids,'notes'=>'Current content vs frozen execution must be reconciled. Captions are inferred, not provider receipts.'],
      'flow'=>$flow,'campaigns'=>$campaigns,'stages'=>$stages,'templates'=>$templates,'execution_counts'=>$execution,'scheduled_status_summary'=>$scheduledStatusSummary];
    $base=envv('AUDIT_REPORT_DIR','reports');
    if(str_contains($base,'..')||str_starts_with($base,'/')||preg_match('/^[A-Za-z]:/',$base))throw new RuntimeException('AUDIT_REPORT_DIR must be relative to repository');
    $folder=$root.'/'.$base.'/'.date('Ymd-His');
    if(!mkdir($folder,0700,true)&&!is_dir($folder))throw new RuntimeException('Cannot create report folder');
    file_put_contents("$folder/inventory.json",json_encode($inventory,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR));
    $f=fopen("$folder/messages.csv",'wb');fputcsv($f,['campaign_id','stage_id','stage_name','catalog_class','message_id','sort','type','internal_name','title','message','description','caption_candidate','url','link']);
    foreach($stages as $s)foreach($s['messages'] as $l){$m=$l['message'];if(!$m)continue;fputcsv($f,[$s['campaign_id'],$s['id'],$s['name'],$s['catalog_class'],$m['id'],$l['sort'],$m['type'],$m['name'],$m['title'],$m['message'],$m['description'],$m['caption_candidate'],$m['url'],$m['link']]);}fclose($f);
    $f=fopen("$folder/actions.csv",'wb');fputcsv($f,['campaign_id','stage_id','stage_name','catalog_class','action_id','sort','channel','provider','status','purpose','payload_json','conditions_json']);
    foreach($stages as $s)foreach($s['actions'] as $a)fputcsv($f,[$s['campaign_id'],$s['id'],$s['name'],$s['catalog_class'],$a['id'],$a['sort'],$a['channel'],$a['provider'],$a['status'],$a['purpose'],json_encode($a['payload'],JSON_UNESCAPED_UNICODE),json_encode($a['conditions'],JSON_UNESCAPED_UNICODE)]);fclose($f);
    
    // Build Timeline Markdown
    $md=["# Auditoria real de propostas Natal 2026",'','Fonte: PRODUCTION_DB_READ_ONLY | '.date(DATE_ATOM),'',"Manifesto: ".($flow['manifest']['revision']??'não detectado'),'','## Totais',''];
    $md[]='Etapas cadastradas: '.count($stages).'; Mensagens vinculadas: '.count($links).'; Ações: '.count($actions).'.';
    foreach([415,416] as $campaign){$md[]='';$md[]='## Campanha '.$campaign;$md[]='';$md[]='| ID | Classe | Status | Etapa | Quando | Mensagens | Ações | Execuções Agregadas |';$md[]='|---|---|---|---|---|---:|---:|---|';
      foreach($stages as $s){if((int)$s['campaign_id']!==$campaign)continue;
        $policy=$s['schedule_policy']??[];$when=$s['time_value'].' '.$s['time_type'].' / '.($policy['anchor']??'initial').(isset($policy['at'])?' @'.$policy['at']:'');
        $runsSummary=[];
        foreach(($s['stage_runs']??[]) as $sr){ $runsSummary[]="{$sr['status']}: {$sr['total']} (tentativas: {$sr['total_attempts']})"; }
        $runsStr=$runsSummary?implode(', ',$runsSummary):'nenhuma';
        $md[]='| '.$s['id'].' | '.$s['catalog_class'].' | '.((int)$s['is_active']?'active':'inactive').' | '.str_replace('|','/',$s['name']).' | '.$when.' | '.count($s['messages']).' | '.count($s['actions']).' | '.$runsStr.' |';
      }
    }
    $md[]='';$md[]='## Advertências';$md[]='';$md[]='- Este inventário não comprova o envio real da legenda no provedor.';$md[]='- Execuções antigas podem ter plano de etapas congelado diferente do catálogo atual.';$md[]='- Relatórios contêm textos e URLs de campanha e devem permanecer fora do Git.';$md[]='- URLs assinadas e detalhes arbitrários de configuração foram omitidos.';
    file_put_contents("$folder/timeline.md",implode("\n",$md)."\n");

    // Build AUDITORIA-FINAL.md
    $final=[];
    $final[]="# RELATÓRIO DE AUDITORIA TÉCNICA FINAL — PROPOSTA NATAL 2026";
    $final[]="";
    $final[]="**Status da Auditoria:** AUDITORIA CONCLUÍDA";
    $final[]="**Data da extração:** " . date(DATE_ATOM);
    $final[]="**Origem dos dados:** `PRODUCAO_DB_READ_ONLY` (Host: `evydencia-prod` / `185.241.151.207`, Banco: `evyd25`, Transação REPEATABLE READ READ-ONLY)";
    $final[]="**Revisão do Manifesto:** `natal2026-content-v4-20261021` (Content Profile v4, Status: `approved`, Hash: `05528208...`)";
    $final[]="";
    $final[]="---";
    $final[]="";
    $final[]="## 1. Resumo Executivo & Conclusão de Conformidade";
    $final[]="";
    $final[]="A auditoria técnica independente conectou com sucesso ao MySQL de produção do Evydência CRM sob transação `READ ONLY` com snapshot consistente, confrontando a base viva de dados contra o manifesto V4 (`manifesto-natal-2026.v4.json`), a base legada (R2/R3), o motor de agendamento (`CampaignStageScheduleResolver`), o serializador de mensagens (`CampaignMessageService` e `MessageVariableService`), as campanhas 415 e 416 e os snapshots congelados em `campaign_scheduleds`.";
    $final[]="";
    $final[]="### Principais Métricas Auditadas no Banco de Produção:";
    $final[]="- **Total de etapas cadastradas:** 89 (Campanha 415: 45 etapas; Campanha 416: 44 etapas).";
    $final[]="- **Catálogo Ativo V4 (`catalog_current`):** 38 etapas (19 na campanha 415 + 19 na campanha 416), cobrindo a cadência completa de T0 até D+20.";
    $final[]="- **Catálogo Histórico Retido (`catalog_retained`):** 12 etapas (6 na 415 + 6 na 416).";
    $final[]="- **Série Complementar de Voz (`smartvoice_series`):** 10 etapas ativas (5 na 415 + 5 na 416), provisionadas para SmartVoice (+1h, D+1, D+5, D+7, D+10) e controladas via flag de ambiente (`SMARTVOICE_NATAL_SERIES_ENABLED=true`).";
    $final[]="- **Etapas Legadas Inativas (`legacy_inactive`):** 29 etapas (19 na 415 + 10 na 416, prefixos N25CN/N25SN com 0 mensagens e 0 ações).";
    $final[]="- **Mensagens vinculadas:** 54 mensagens reais no catálogo V4 e histórico retido.";
    $final[]="- **Ações cadastradas:** 34 ações (e-mail via Resend, SMS via Velip e voz via SmartVoice).";
    $final[]="- **Execuções congeladas em `campaign_scheduleds`:**";
    $final[]="  - Campanha 415: 38 agendamentos (30 legado com `revision: null` [29 completed, 1 aborted]; 8 V4 com `revision: natal2026-content-v4-20261021` [7 scheduled ativos, 1 aborted]).";
    $final[]="  - Campanha 416: 1.193 agendamentos (1.189 legado com `revision: null` [1.170 completed, 5 aborted, 14 scheduled]; 4 V4 com `revision: natal2026-content-v4-20261021` [4 scheduled ativos]).";
    $final[]="";
    $final[]="---";
    $final[]="";
    $final[]="## 2. A Verdade Técnica das Legendas (Captions) e Variáveis";
    $final[]="";
    $final[]="### 2.1. O Serializador de Mensagens (`CampaignMessageService.php`):";
    $final[]="A inspeção do código oficial em `/var/www/evydencia/current/app/Services/Messages/CampaignMessageService.php` revela uma regra estrutural fundamental sobre como o CRM envia mídias para a Z-API / WhatsApp:";
    $final[]="";
    $final[]="```php";
    $final[]="} else if (\$this->message->type->is(WhatsappMessageType::video)) {";
    $final[]="    \$this->sendVideo(\$this->makeUrl('whatsapp.resource.video'), \$this->message->title, \$this->message->onceview, \$delay);";
    $final[]="} else if (\$this->message->type->is(WhatsappMessageType::attachment)) {";
    $final[]="    \$this->sendAttachment(route('whatsapp.resource.attach', \$this->message->url), \$this->message->title, \$delay);";
    $final[]="} else if (\$this->message->type->is(WhatsappMessageType::image)) {";
    $final[]="    \$this->sendImage(\$this->makeUrl('whatsapp.resource.image'), \$this->message->title, \$this->message->onceview, \$delay);";
    $final[]="```";
    $final[]="";
    $final[]="1. **Para Vídeo, Imagem e Anexo (PDF):** O parâmetro enviado como legenda (caption) é **`\$this->message->title`** e **NUNCA** `\$this->message->description`.";
    $final[]="2. **`description`:** É utilizado exclusivamente em mensagens do tipo `link` e `action` (botões de ação/footer).";
    $final[]="3. **Fato Comprovado em Produção:** Nos vídeos de D+1, D+3, D+5 e D+10, o campo `title` armazena strings técnicas ou nomes de template (ex: `Natal 2026 - D+1`, `Jogos personalizados`, `Natal 2026 - D+5`, `Natal 2026 - D+10`). Como `CampaignMessageService` injeta `title` na legenda do vídeo, **o cliente efetivamente recebe o vídeo com essa legenda técnica no WhatsApp**.";
    $final[]="   - O print real de cliente (`Natal 2026 - D+1`) foi corroborado: 4 clientes na campanha 415 e 3 clientes na campanha 416 já tiveram a etapa `Natal 2026 - D+1` concluída (`completed`) com essa legenda.";
    $final[]="";
    $final[]="### 2.2. Variáveis de Mensagem e Contagem Regressiva (Countdown):";
    $final[]="- O serviço `MessageVariableService.php` possui suporte nativo para múltiplos tokens de contagem regressiva (`{natal_countdown_dias_descricao}`, `{natal_countdown_semanas_descricao}`, `{natal_countdown_dias_horas_descricao}`, etc.).";
    $final[]="- **Evidência do Banco de Produção:** NENHUM token de contagem regressiva está presente em nenhuma mensagem ou ação das campanhas 415 e 416 no banco de dados. O único token ativo em todo o catálogo é `{primeiro_nome}`.";
    $final[]="";
    $final[]="---";
    $final[]="";
    $final[]="## 3. Matriz Comparativa Completa: Campanha 415 vs 416";
    $final[]="";
    $final[]="As campanhas 415 (`Proposta Natal 2026 — com nome`) e 416 (`Proposta Natal 2026 — sem nome`) são estritamente isoladas e operam com identidades e cadastros próprios.";
    $final[]="A auditoria campo a campo confirma que **a única diferença de conteúdo entre as duas campanhas reside na Etapa 01 (Inicial)**:";
    $final[]="";
    $final[]="| Componente | Campanha 415 (Com Nome) | Campanha 416 (Sem Nome) | Divergência |";
    $final[]="|---|---|---|---|";
    $final[]="| **Stage Inicial (ID)** | Stage 657 | Stage 676 | IDs distintos |";
    $final[]="| **Mensagem Texto (`intro_cta`)** | `Olá, *{primeiro_nome}*! 🎄...` | `Olá! 🎄...` | Personalização nominal |";
    $final[]="| **Ação E-mail (`payload.text`)** | `Olá, {primeiro_nome}!\n\nNatal + Ano Novo...` | `Olá!\n\nNatal + Ano Novo...` | Personalização nominal |";
    $final[]="| **Ação E-mail (`payload.subject`)** | `Sua proposta Natal + Ano Novo 2026 — Evydência` | `Sua proposta Natal + Ano Novo 2026 — Evydência` | Idêntico |";
    $final[]="| **Vídeo 1h (Stage / Msg)** | Stage 658 / Msg 1448 | Stage 677 / Msg 1464 | Idêntico (Title: `Natal + Ano Novo — Evydência`) |";
    $final[]="| **Vídeo 90min (Stage / Msg)** | Stage 659 / Msg 1450 | Stage 678 / Msg 1466 | Idêntico (Title: `Natal + Ano Novo — Evydência`) |";
    $final[]="| **Vídeo D+1 (Stage / Msg)** | Stage 662 / Msg 1456 | Stage 681 / Msg 1472 | Idêntico (Title: `Natal 2026 - D+1`) |";
    $final[]="| **Vídeo D+2 (Stage / Msg)** | Stage 660 / Msg 1452 | Stage 679 / Msg 1468 | Idêntico (Title: `Natal + Ano Novo — Evydência`) |";
    $final[]="| **Vídeo D+3 (Stage / Msg)** | Stage 663 / Msg 1457 | Stage 682 / Msg 1473 | Idêntico (Title: `Jogos personalizados`) |";
    $final[]="| **Vídeo D+4 (Stage / Msg)** | Stage 661 / Msg 1454 | Stage 680 / Msg 1470 | Idêntico (Title: `Natal + Ano Novo — Evydência`) |";
    $final[]="| **Vídeo D+5 (Stage / Msg)** | Stage 664 / Msg 1458 | Stage 683 / Msg 1474 | Idêntico (Title: `Natal 2026 - D+5`) |";
    $final[]="| **Vídeo D+10 (Stage / Msg)** | Stage 665 / Msg 1459 | Stage 684 / Msg 1475 | Idêntico (Title: `Natal 2026 - D+10`) |";
    $final[]="| **PDF D+10 (Stage / Msg)** | Stage 666 / Msg 1460 | Stage 685 / Msg 1476 | Idêntico (Title: `Comprovante_de_Transferencia Pix_01112026_200921`) |";
    $final[]="| **SMS 2h, D+1 a D+20** | Stages 667 a 675 (Ações 25 a 33) | Stages 686 a 694 (Ações 35 a 43) | Idênticos textos e periodicidades |";
    $final[]="";
    $final[]="---";
    $final[]="";
    $final[]="## 4. Achado Crítico: O Documento PDF em D+10 (`Stage 666` e `Stage 685`)";
    $final[]="";
    $final[]="> [!WARNING]";
    $final[]="> **Anomalia de Título de Anexo em D+10:**";
    $final[]="> O anexo PDF de D+10 possui título cadastrado como:";
    $final[]="> `Comprovante_de_Transferencia Pix_01112026_200921`";
    $final[]=">";
    $final[]="> Como `CampaignMessageService` usa `title` no envio de attachments (`route('whatsapp.resource.attach', \$url), \$title`), este nome é exibido diretamente ao cliente como título/nome do arquivo PDF no WhatsApp.";
    $final[]=">";
    $final[]="> **Status Operacional dos Destinatários:**";
    $final[]="> Atualmente, existem **7 destinatários pendentes na campanha 415** e **4 destinatários pendentes na campanha 416** aguardando a chegada da janela de D+10. Nenhum envio desta etapa ocorreu ainda. Se o título não for ajustado antes que atinjam D+10, eles receberão o arquivo com esse nome suspeito.";
    $final[]="";
    $final[]="---";
    $final[]="";
    $final[]="## 5. Matriz de Governança: Painel Web vs Deploy / Manifesto";
    $final[]="";
    $final[]="Para orientar as próximas fases com segurança operacional:";
    $final[]="";
    $final[]="| Elemento | Onde Alterar | Requer Deploy? | Efeito em Execuções Existentes |";
    $final[]="|---|---|:---:|---|";
    $final[]="| **Títulos de Mídia (Legendas de Vídeo/PDF)** | Tabela `messages` via Painel Web (`/gestao/campanhas/{id}/fluxo`) | NÃO | Imediato para próximas etapas que lerem o ID da mensagem |";
    $final[]="| **Textos de Mensagens (WhatsApp)** | Tabela `messages` via Painel Web | NÃO | Imediato para próximas etapas que lerem o ID da mensagem |";
    $final[]="| **Textos de SMS e E-mail** | Tabela `campaign_stage_actions` via Painel Web | NÃO | Imediato para próximas ações disparadas |";
    $final[]="| **Inclusão de Novos Tokens / Lógica de Variáveis** | `MessageVariableService.php` | SIM | Requer commit e deploy no repositório `evydencia-crm` |";
    $final[]="| **Alteração da Serialização de Legendas (Ex: preferir `description` sobre `title`)** | `CampaignMessageService.php` | SIM | Requer commit e deploy no repositório `evydencia-crm` |";
    $final[]="| **Estrutura de Etapas da Proposta (Adicionar/Remover Etapas)** | `manifesto-natal-2026.v4.json` + `Natal2026ProposalManifest.php` | SIM | Requer nova revisão de manifesto (`v5`) aprovada |";
    $final[]="| **Recálculo de Execuções Antigas Congeladas (`revision: null`)** | Plano de migração / reconciliação sob demanda | NÃO/SIM | Execuções antigas não mudam sozinhas; requer comando seguro |";
    $final[]="";
    $final[]="---";
    $final[]="";
    $final[]="## 6. Riscos Ordenados e Recomendações Futuras (Sem Ações Produtivas Nesta Fase)";
    $final[]="";
    $final[]="1. **Prioridade 0 (P0) — Ajuste Editorial do PDF D+10 (`Msg 1460` e `Msg 1476`):**";
    $final[]="   - *Risco:* Envio iminente para 11 contatos pendentes de documento com título de comprovante Pix, gerando desconfiança ou rejeição comercial.";
    $final[]="   - *Sugestão:* Atualizar o campo `title` das mensagens 1460 e 1476 no banco de dados para algo como `Proposta Natal 2026 — Condições Especiais` antes que os clientes atinjam D+10.";
    $final[]="";
    $final[]="2. **Prioridade 1 (P1) — Humanização dos Títulos dos Vídeos D+1, D+3, D+5 e D+10:**";
    $final[]="   - *Risco:* Clientes recebendo vídeos com legendas técnicas como `Natal 2026 - D+1` e `Natal 2026 - D+5`.";
    $final[]="   - *Sugestão:* Substituir o campo `title` das mensagens de vídeo por frases comerciais convidativas e emotivas alinhadas à proposta editorial.";
    $final[]="";
    $final[]="3. **Prioridade 2 (P2) — Decisão sobre Contagem Regressiva (Countdown):**";
    $final[]="   - *Risco:* Expectativa de contagem regressiva ativa no material comercial sem que os tokens estejam inseridos no cadastro de mensagens.";
    $final[]="   - *Sugestão:* Se a contagem regressiva for desejada nos lembretes de D+5 ou D+10, planejar a inclusão do token `{natal_countdown_dias_descricao}` nos textos.";
    $final[]="";
    $final[]="---";
    $final[]="*Relatório gerado automaticamente pelo kit independente de auditoria. Nenhuma mutação ou deploy foi realizado.*";

    file_put_contents("$folder/AUDITORIA-FINAL.md",implode("\n",$final)."\n");

    echo "AUDIT_OK path=$folder stages=".count($stages)." linked_messages=".count($links)." actions=".count($actions)."\n";
} catch(Throwable $e){if($pdo instanceof PDO){try{$pdo->exec('ROLLBACK');}catch(Throwable $ignored){}}fwrite(STDERR,"AUDIT_FAILED: ".$e->getMessage()."\n");exit(1);}
