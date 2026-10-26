# Antigravity — retomar auditoria V5 em modo cirúrgico (26/10/2026)

**Prioridade: prova da versão real, sem perguntas repetidas, testes mínimos, leitura apenas.**

### Estado de partida
O relatório `Plano Editorial V5 — Confronto Técnico e Forense` foi fornecido à direção como um arquivo `file:///...` local do Antigravity. Ele NÃO estava commitado na main quando a revisão independente começou. Contém observações visuais úteis, mas **IDs, URLs, nome da tabela de snapshot, dados Resend/SMS e links de mídia contradizem a versão V4 verificada**. Consulte `docs/FORENSE-ANTIGRAVITY-CONFRONTO-20261026.md`.

O novo `editorial/whatsapp-v5.draft.json` foi revisado: D+5 menciona o pergaminho mágico relatado, e “desde 2011” passou para T0. Os IDs de mídia no JSON são **historical_media_id_hint_415/416**, **nunca** autoridade para UPDATE ou upload.

### Uma única rodada de validação local — PowerShell
Executar no workspace `hypeneural/propostanatal` sem descartar alterações locais do agente:

```powershell
git status --short
git fetch origin main
git diff --stat
# Se houver alterações locais, preserve ou faça stash explícito e documentado;
# não usar reset --hard, checkout -f ou overwrite em massa.
git pull --ff-only origin main
python scripts/validate_editorial.py
python scripts/reconcile_v4_local.py --self-test
python scripts/inspect_local_media.py --self-test
python scripts/check_public_links.py --list-only
```

NÃO repetir instalação de ffprobe/winget, não procurar vídeos indefinidamente na VPS. O script `inspect_local_media.py` funciona com vídeo local e SHA256; só precisa de `opencv-python-headless` se `--frames` solicitado.

### Reconciliar DB vs manifesto aprovado antes de qualquer ID
1. Localizar no clone **somente leitura** do CRM o arquivo `docs/active/natal-2026-proposta/manifesto-natal-2026.v4.json`. Não baixar nenhum dado pessoal em diretórios públicos.
2. Configurar `.env.local` (já existente no ambiente, se autorizado; não revelar host/usuário/senha). Rodar `php scripts/audit.php` e capturar a pasta real `reports/<timestamp>`.
3. Reconciliação offline obrigatória:

```powershell
python scripts/reconcile_v4_local.py --manifest "C:\Projetos\hypeneural\evydencia-crm\docs\active\natal-2026-proposta\manifesto-natal-2026.v4.json" --inventory "reports\<timestamp>\inventory.json" > "reports\<timestamp>\reconciliacao-v4.json"
if ($LASTEXITCODE -ne 0) { Write-Error 'DIVERGÊNCIA: NÃO APROVAR PUBLICAÇÃO'; exit 2 }
```

**Substituir caminhos de exemplo pelos arquivos REAIS.** O script verifica revisão, 38 IDs atuais, `stage_roles`, tipo e basename de cada mensagem, sem acessar servidor, sem exportar contato. Para a classificação `CONSISTENT_FOR_EDITORIAL_REVIEW_ONLY`, ainda não há autorização de aplicação.

Se sair `BLOCKED_DIVERGENCE`, guardar os IDs/URLs do **inventário recém-gerado**, o SHA/revisão do manifesto, datas e cada incompatibilidade, e corrigir primeiro o diagnóstico. Nunca resolver divergência inventando IDs ou substituindo o arquivo de origem.

### Recuperar inspeção visual do Antigravity SEM duplicar esforço
Artefatos locais que apareceram no log: `inspect_v4_inventory.py`, `v4_inventory_inspected.txt`, `natal_2026_video_manifest.json`, `check_videos_head.py`, `inspect_video_contents.py`, `extract_subtitles.py`, `H_1_50.jpg`, `D_5_50.jpg`, `d05_sec_08.jpg`, `plano_editorial_v5_confronto.md`.
- Identificar exatamente onde foram salvos. O log não demonstra que estão em Git ou completos; **não publicar prints de crianças ou fotos de clientes no Git público**.
- Se MP4 locais já foram baixados, executar `python scripts/inspect_local_media.py --manifest "<manifesto-v4-local>" --media-dir "<pasta-mp4>" --frames`. Confirma tamanho+SHA e salva amostras de frame em `reports/media-inspection/`.
- Conferir no player local **toda** a faixa de vídeo+áudio e overlays; ffprobe não é pré-requisito. Capturar resultado local com `D+5 pergaminho`, `D+4 bolhas`, `D+3 jogos`, `D+10 personagem`, indicando timestamps e SHA. Evitar OCR.
- Se vídeo de D+5 for realmente o pergaminho, manter a legenda nova; se não for, restaurar uma alternativa comprovada. Vídeo teatral NÃO prova presença de personagens no ensaio.

### Encerramento editorial por etapas (não reiniciar toda auditoria)
- **T0**: comparar mensagens atuais 415/416 de fonte DB com o V5; prefixo com nome válido ou sem nome, só uma frase de credibilidade “desde 2011”; PDF e arte imediatamente. Para botões, preservar IDs aprovados quando possível; recomendação primária checkout direto se realmente leva à reserva; secundários horários e Instagram.
- **H+1/H+90**: olhar vídeos antes das legendas, reduzir fricção/repetição. NÃO adicionar “até 10x” sem condição comercial homologada.
- **D+1/D+2**: emoções/duas cenas + countdown se token for renderizado no envio.
- **D+3**: demo dos jogos em https://jogos.fotosdenatal.com/ e galeria em https://jogos.fotosdenatal.com/demo/fotos; verificar Chrome mobile, dados públicos/autorizados, links funcionais e regra de jogos por pacote (1/5/10), galeria universal informada pela direção.
- **D+4**: figurinos, bolhas/botão mágico se constarem mesmo no vídeo. Medalhinha/certificado/livrinho exigem estoque e entregabilidade.
- **D+5**: personagem e pergaminho (sem prometer personagem incluído na compra). Legenda curta em `video_d5.title`. Credibilidade desde 2011 está no T0.
- **D+10**: título curto sem escassez fabricada; **P0** documento com aparência de comprovante Pix requer tratamento operacional isolado, inclusive snapshots ativos (NO-GO).
- **SMS Resend SmartVoice**: preservar escopo da campanha enquanto copy WhatsApp é revista; não dizer que a V4 é HTML/anexo ou que existe voz ativa sem logs de produção. A direção afirma estar perto da homologação de voz, que deve ser confirmada em trilha técnica separada.

### Artefatos finais locais — somente estes
- `reports/<timestamp>/reconciliacao-v4.json` e síntese de mudanças por ID real.
- `reports/<timestamp>/V5-DIFF-AUDITADO.md`: `campaign_id`, `role`, `stage_id`, `message_id`, canal, campo exato, antes/depois, gate, fonte, confidence, impacto em novos vs frozen.
- `reports/<timestamp>/MIDIA-EVIDENCIAS.md`: SHA, duração, resolução, frame-timestamp e resumo do áudio, sem imagens publicadas.
- `reports/<timestamp>/GO-NOGO.md`: situação T0, D+5, D+10, manifesto hash, snapshot, mídia, botão de reserva, produtos, opt-out, antispam.

Somente os arquivos de **copy genérica não sensível** ficam no Git público. **Nenhum comando `--apply`, `proposals:doctor`, UPDATE, envio real, edição no gestor ou cancelamento produtivo nesta missão.**

### Critérios de parada e escopo
- Use **no máximo** uma reextração de DB e uma amostragem de cada MP4 local se fontes estiverem acessíveis. Repetir somente a verificação específica que falhou e dizer por quê.
- Erros de conexão do modelo `daily-cloudcode` não são erro da Z-API/CRM; não refazer deploy por isso.
- Se fonte não existir ou o hash de mídia diferir, declarar `BLOQUEADA` com evidência; não presumir sucesso.
- Se todos os checks de role/media passarem, liberar apenas `PRONTO PARA REVISÃO EDITORIAL`, nunca `PUBLICADO EM PRODUÇÃO`.

## Prompt compacto para o Antigravity
> Sincronize hypeneural/propostanatal preservando alterações locais. Leia docs/FORENSE-ANTIGRAVITY-CONFRONTO-20261026.md e este documento. O relatório anterior tem IDs/URLs/tabelas contraditórios com V4; não os utilize para alterações. Rode validate_editorial.py, reconcile_v4_local.py --self-test e inspect_local_media.py --self-test. Em uma rodada read-only, atualize o inventário real das campanhas 415/416 e rode reconcile_v4_local.py com o manifesto V4 local, bloqueando divergências. Recupere as amostras OpenCV existentes e compare o SHA dos MP4 antes de aceitar suas conclusões; D+5 deve ter legenda curta coerente com pergaminho, e “desde 2011” já foi movido para T0 no draft. Não reutilize 10x, claims de personagem presencial, contagens de ocupação ou ID extraído apenas da prosa. Prepare um V5-DIFF-AUDITADO.md local por ID real, sem dados de cliente. Continue por T0→H+1→D+10, preserve SMS/Resend/SmartVoice e mantenha D+10 PDF como NO-GO. Não faça alterações no CRM ou envios. Informe os 3 resultados: consistência do banco, prova dos vídeos e aprovação editorial pendente.
