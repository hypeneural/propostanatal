<?php
declare(strict_types=1);

/**
 * Standalone MySQL proposal inventory. No Laravel, migrations, sends or DML.
 * Reports stay local; NEVER commit reports from a production database.
 */
error_reporting(E_ALL);
if (in_array('--self-test', $argv, true)) {
    $pdoAvailable=class_exists(PDO::class);
    $mysqlAvailable=$pdoAvailable && in_array('mysql', PDO::getAvailableDrivers(), true);
    echo "SELF-TEST ".($mysqlAvailable ? "OK" : "FAILED").": PHP ".PHP_VERSION."; PDO=".($pdoAvailable ? "yes" : "NO")."; mysql driver=".($mysqlAvailable ? "yes" : "NO")."\n";
    exit($mysqlAvailable ? 0 : 1);
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
       $allowed=['text','message','subject','body_mode','html','body_html','body_text','fallback_text','flow_id','voice_mode','skip_if_converted','caption','fileName','buttons','natal_email','from'];
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
    $resolveMediaUrl=static function(?string $rawUrl, ?string $type) use ($cleanUrl): ?string {
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
    foreach($messages as &$m){
       $m['url']=$resolveMediaUrl($m['url']??null, $m['type']??null);
       $m['link']=$cleanUrl($m['link']??null);
       // Keep buttons in raw JSON only if whitelisted button fields; never include arbitrary authorization headers.
       $buttonRaw=(string)($m['buttons']??'');
       $btn=json_decode($buttonRaw,true);
       if (is_array($btn)) {
           $m['buttons']=array_values(array_filter(array_map(static fn($b)=>is_array($b)?array_intersect_key($b,array_flip(['type','text','label','url','id','phone'])):null,$btn)));
       } else {
           $m['buttons']=[];
           foreach (preg_split('/\R/u',$buttonRaw) as $line) {
               $line=trim($line);
               if ($line==='')continue;
               $parts=explode(';',$line,4);
               if(count($parts)>1) {
                   $kind=strtoupper(trim($parts[1]));
                   $m['buttons'][]=['id'=>trim($parts[0]),'type'=>$kind,'url'=>$kind==='URL'?($parts[2]??null):null,
                      'phone'=>$kind==='CALL'?('[PHONE OMITTED]'):null,
                      'label'=>trim($parts[$kind==='URL'||$kind==='CALL'?3:2]??'')];
               } else {
                   $plain=explode(',',$line,2);
                   if(count($plain)===2)$m['buttons'][]=['id'=>trim($plain[0]),'label'=>trim($plain[1])];
               }
           }
       }
       foreach($m['buttons'] as &$button) {
           if(is_array($button) && isset($button['url']))$button['url']=$cleanUrl($button['url']);
       }unset($button);
       $m['carousel']='[omitted: requires individual review for sensitive URLs]';
       // CampaignMessageService passes $message->title as caption for image, video, and attachment
       $m['caption_source_candidate']=in_array((string)($m['type']??''),['image','video'],true)?'title -> caption':(((string)($m['type']??''))==='attachment'?'title -> fileName':'inspect serializer');
       $m['caption_candidate']=str_starts_with($m['caption_source_candidate'],'title -> ')?($m['title']??null):null;
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

    // Evidence-driven summary. Never freeze past database statistics in source code.
    $classCounts = [];
    $activeClassCounts = [];
    foreach ($stages as $s) {
        $class = (string) $s['catalog_class'];
        $classCounts[$class] = ($classCounts[$class] ?? 0) + 1;
        if ((int) $s['is_active'] === 1) {
            $activeClassCounts[$class] = ($activeClassCounts[$class] ?? 0) + 1;
        }
    }
    $final = [];
    $final[] = '# AUDITORIA INDEPENDENTE — INVENTÁRIO DE PROPOSTA NATAL 2026';
    $final[] = '';
    $final[] = '**Status:** AUDITORIA PARCIAL (inventário de banco; sem comprovante de entrega externa)';
    $final[] = '**Extração:** '.date(DATE_ATOM);
    $final[] = '**Origem:** conexão MySQL informada em .env.local, em transação READ ONLY. Ambiente de produção deve ser comprovado pelo operador.';
    $final[] = '**Revisão lida no banco:** '.($flow['manifest']['revision'] ?? 'NÃO INFORMADA');
    $final[] = '**Estado do manifesto:** '.($flow['manifest']['status'] ?? 'NÃO INFORMADO');
    $final[] = '';
    $final[] = '## Quantidades derivadas da extração';
    $final[] = '';
    $final[] = '- Etapas: **'.count($stages).'** (415: '.count(array_filter($stages,fn($s)=>(int)$s['campaign_id']===415)).'; 416: '.count(array_filter($stages,fn($s)=>(int)$s['campaign_id']===416)).').';
    $final[] = '- Vínculos etapa/mensagem: **'.count($links).'**, mensagens únicas: **'.count($messages).'**; ações: **'.count($actions).'**.';
    foreach ($classCounts as $class=>$n) {
        $final[] = '- '. $class .': **'.$n.'** cadastradas, **'.($activeClassCounts[$class] ?? 0).'** com is_active=1 (isto NÃO prova envio).';
    }
    $final[] = '';
    $final[] = '## Execuções por revisão (somente agregados)';
    $final[] = '';
    $final[] = '| Campanha | Versão | Revisão congelada | Agendamentos |';
    $final[] = '|---|---|---|---:|';
    foreach ($execution as $row) {
        $final[] = '| '.$row['campaign_id'].' | '.($row['version'] ?? 'não informado').' | '.($row['revision'] ?? 'legado/nulo').' | '.$row['total'].' |';
    }
    if (!$execution) $final[] = 'Sem dados de revisão no schema disponível.';
    $final[] = '';
    $final[] = '## Status dos agendamentos (agregado)';
    $final[] = '';
    $final[] = '| Campanha | Status | Revisão | Total |';
    $final[] = '|---|---|---|---:|';
    foreach ($scheduledStatusSummary as $row) {
        $final[] = '| '.$row['campaign_id'].' | '.$row['status'].' | '.($row['revision'] ?? 'legado/nulo').' | '.$row['count'].' |';
    }
    if (!$scheduledStatusSummary) $final[] = 'Não disponível nesta extração.';
    $final[] = '';
    $final[] = '## Mídias com títulos que exigem revisão editorial';
    $final[] = '';
    foreach ($messages as $m) {
        $title=(string)($m['title']??'');
        if (preg_match('/comprovante|transfer[eê]ncia|pix|D\+\d+|jogos personalizados/i',$title)) {
            $type=(string)($m['type']??'');
            $field = $type==='attachment' ? 'fileName' : (in_array($type,['image','video'],true)?'caption':'campo não verificado');
            $final[]='- Mensagem #'.$m['id'].' ('.$type.'): **'.$title.'** → '.$field.'.';
        }
    }
    $final[] = '';
    $final[] = '## Provas e limitações';
    $final[] = '';
    $final[] = '- Verificar inventory.json, messages.csv e actions.csv para todos os textos, policies e status capturados.';
    $final[] = '- Atributos congelados em natal_execution_plan podem prevalecer sobre o catálogo atual; edições no painel não são automaticamente retroativas.';
    $final[] = '- campaign_contact_stage_runs.completed NÃO demonstra entregue/lido; requer delivery items e recibos de provedores.';
    $final[] = '- Para PDF, title vira fileName no WhatsappPayload; para vídeo/imagem, title vira caption.';
    $final[] = '- O conteúdo real do PDF D+10 não foi aberto ou inspecionado por este script. HTTP 200, quando comprovado, não assegura adequação do documento.';
    $final[] = '- Registro da série SmartVoice não demonstra feature flag nem registry efetivo do discador ou atendimento da ligação.';
    $final[] = '- Tokens de countdown precisam de teste por canal/contexto e tempo de renderização.';
    $final[] = '- Esta coleta não executa deploy, atualização, mensagem, diagnóstico de saúde ou chamada.';
    $final[] = '';
    $final[] = '## Próximo passo';
    $final[] = '';
    $final[] = 'Reconciliar snapshots de mensagens, ações e delivery_items e validar o conteúdo dos anexos antes de planejar alterações produtivas. Ver docs/REVISAO-FORENSE-INDEPENDENTE-20261025.md.';
    file_put_contents("$folder/AUDITORIA-FINAL.md",implode("\n",$final)."\n");

    echo "AUDIT_OK path=$folder stages=".count($stages)." linked_messages=".count($links)." actions=".count($actions)."\n";
} catch(Throwable $e){if($pdo instanceof PDO){try{$pdo->exec('ROLLBACK');}catch(Throwable $ignored){}}fwrite(STDERR,"AUDIT_FAILED: ".$e->getMessage()."\n");exit(1);}
