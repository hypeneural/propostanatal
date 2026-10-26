# Auditoria independente — relatório Antigravity de confronto V5, 26/10/2026

**Status: AUDITORIA PARCIAL / PROVENIÊNCIA PENDENTE.**
Escopo da revisão: log e artefato `Plano Editorial V5 — Confronto Técnico e Forense com a Produção Real` fornecidos pela direção nesta conversa em 26/10/2026; comparação com o `manifesto-natal-2026.v4.json` e código Laravel na main de `hypeneural/evydencia-crm` consultados no mesmo dia; conteúdo do repositório `hypeneural/propostanatal` na main antes deste registro.

## Veredito executivo
A inspeção visual descrita pelo Antigravity é útil. O achado de que D+5 mostra um personagem com pergaminho em vez de retrospectiva de 2011 levou a uma **correção editorial** no V5 draft. Contudo, o relatório mistura **IDs e URLs de outra origem ainda não conciliada** com a V4 publicada, traz números e estados de provedores sem trilha reprodutível e propõe copy longa com promessas não comprovadas. NÃO usá-lo como script de alteração nem decidir publicação com base nele. O log termina em relatório local `file:///`, e no momento da inspeção esse documento **não estava versionado neste repo**.

## Prioridades e provas

| Severidade | Afirmação do relatório local | Referência auditável do CRM | Decisão |
|---|---|---|---|
| P0 | T0 mensagens 1941, 1942, 1943 / 1979, 1980, 1981 | Inventário de produção anterior neste repo identifica imagem 1446/1462 e PDF 1447/1463; manifesto V4 `cenarios_image` basename `natal-2026-pacotes-r1.png` e `proposta_pdf` basename `natal2026-rev357-v1-proposta.pdf` | IDs 194x podem ser de outro corte, mas **NÃO há prova** de remapeamento da V4. Reexecutar extração local e mostrar `manifest.mapping[variant].stage_roles/messages`, vínculo de pivô e stage; se mudou, demonstrar revisão/horário do DB e hash. Não atualizar por ID lido em prosa. |
| P0 | URL T0 imagem `/resources/img/natal2026/cenarios-natal-ano-novo-2026.png`; PDF `/resources/pdf/natal2026/proposta-natal-2026.pdf` | Basenames no manifesto V4 são `natal-2026-pacotes-r1.png` e `natal2026-rev357-v1-proposta.pdf`; URLs canônicas anteriores `/resources/image/...`, `/resources/attachment/...` | Contradição. Conferir S3 key `messages.url`, URL no serializer e digest real da mídia. Não presumir que HTTP 200 prova vínculo da V4. |
| P0 | Snapshot `campaign_execution_items.snapshot_title` | Modelo `CampaignContactStageRun` usa `campaign_contact_stage_runs.stage_snapshot`; `CampaignPolicyLegacyMessages::hydrate` lê `legacy_messages[].attributes` desse snapshot, ou `CommunicationEvent.metadata` no fallback | Tabela/campo descritos NÃO fazem parte da cadeia comprovada na main. Validar `SHOW COLUMNS` real. Não usar nenhuma SQL para mutação de snapshots. |
| P0 | D+10 PDF alternativo `proposta-d10.pdf` / IDs 1969 e 2007 | V4 ativo referencia `natal2026-rev357-v1-d10-document.pdf`, título `Comprovante_de_Transferencia Pix_01112026_200921`; auditoria anterior encontrou snapshots pendentes nos stages 666/685 (7 + 4 em 26/10) | O relatório não comprova que o risco acabou. **Bloqueio editorial não suspende envios de produção.** Reconciliar snapshots em momento próximo ao disparo; proposta segura de supressão/cancelamento por mecanismo autorizado; NÃO renomear apenas. |
| P1 | T0 original teria `title = '''O Estúdio de fotos mais completo do Sul do Brasil!'''` | No manifesto V4 `intro_cta.title = "Fotos de Natal + Ano Novo"`; os caracteres de formatação longa são `\\`\\`\\`O Estúdio...\\`\\`\\`` dentro de `intro_cta.message` | Diferença de CAMPO. Revisar `action.message` e `action.title` antes de propor patches. Não fazer diff comparando rótulos com body. |
| P1 | Nove SMS teriam link `wa.me/5548991316694` e opt-out `SAIR` | Manifesto V4 usa `wa.me/5548996553954` nos SMS e `PARARNATAL2026` nas mensagens de proposta, mais link de opt-out no e-mail; não equivale a dados de runtime atual | Não alterar telefone ou opt-out até cotejar DB, tipo/canal e política do provedor. Redação de SMS preservada nesta V5. |
| P1 | Resend inicial: `HTML + PDF anexo`, assunto `Proposta de Natal + Ano Novo 2026 - Estúdio Evydência` | Manifesto V4 declara `body_mode:"text"`, assunto `Sua proposta Natal + Ano Novo 2026 — Evydência`, `text` e remetente `propostas@fotosdenatal.com`; PDF está no WhatsApp inicial | Não confundir e-mail histórico R3 nem prévias com payload Resend T0 V4. Sem prova de anexo PDF no e-mail V4. |
| P1 | H+90 `até 10x`, "figurinos higienizados" | V4 anuncia entradas e 3x saldo para pacotes principais; 10x não está no texto aprovado. Higienização precisa prova operacional | Não adicionar benefícios/parcelamento não homologados. |
| P1 | D+10 "quase esgotados", "seguro uma data prévia" | Ocupação/hold de data não foram verificados na agenda real nem identificados como feature | Tirar escassez falsa e promessas de bloqueio de horário. |
| P1 | D+3 jogo "Trinca de Natal" e recorde | Frame reportado do vídeo, mas o jogo/recorde da demo pública não foi validado end-to-end nesta sessão | Texto neutro focado em demonstração real; verificar link jogo e galeria no Chrome/mobile antes de prometer recorde. |
| P1 | D+5 personagem teatral com pergaminho, 22,2s; 8 vídeos amostrados em 15/50/85% | Artefato descreve frames locais e tarefas OpenCV, mas **nenhum frame, vídeo, digest ou relatório ffprobe foi anexado ao GitHub** | Achado **relatado, plausível e útil**; usar caption que dialoga com magia, mas concluir prova forense só depois de SHA256 da CDN, arquivo original, duração/dimensões e frames com timestamps em relatório local. |

## Correção editorial aplicada à V5 draft
- `whatsapp_d5.video_d5.title`: troca a legenda da retrospectiva por referência curta ao recado/pergaminho mágico; não implica personagem físico incluído na sessão. Necessita confirmação local do frame.
- `initial.intro_cta.message_with_name/without_name`: a credibilidade “desde 2011 em Tijucas” foi trazida para T0 como uma linha simples. **Nunca** afirmar “no mesmo endereço desde 2011” ou “mais de dez mil ensaios” antes de provar.
- Os três botões T0 continuam propostos: checkout rápido, horários e Instagram. **ID interno de botão deve ser preservado**, não substituir `btn_insta` por novo ID sem checar idempotência/contrato. O link checkout é recomendação comercial, só publicar depois de teste mobile sem compra.
- As nove ações SMS, o Resend inicial e a série SmartVoice ficam intocados neste ciclo. Áudio Z-API pré-gravado do Papai Noel é distinto da chamada SmartVoice.

## O que o log de execução realmente permite concluir
1. `git pull`, leitura de arquivos, `validate_editorial.py` e `inspect_v4_inventory.py` aparecem no log; duas execuções do inspector falharam, e a terceira foi concluída, mas o código final e o arquivo `v4_inventory_inspected.txt` NÃO estão publicados neste repositório.
2. `ffprobe` falhou no PC e na VPS, o agente instalou OpenCV e relata extração de frames. Isso é **amostragem visual** (15%, 50%, 85%), **não** verificação de cada frame nem auditoria de áudio. Não rotular “100% alinhado” sem assistir ao filme e escutar a faixa.
3. Houve erros de streaming `daily-cloudcode-pa.googleapis.com` durante a sessão; isso pode interromper etapas da investigação. Não indica falha do CRM/Z-API nem corrompimento da mídia.
4. Os arquivos `check_videos_head.py`, `inspect_video_contents.py`, `extract_subtitles.py` e `plano_editorial_v5_confronto.md` foram criados **localmente** segundo o log, mas não constam nesta main. Recuperar código/saídas através da pasta local do Antigravity; não presumir SHA ou execução reprodutível.
5. Relatório local fala em aprovação de T0 e exige opções A/B para D+5, mas a direção **ainda não aprovou publicação**. A recomendação A foi adotada apenas como **rascunho editorial**.

## Método de reconciliação obrigatório antes de aprovar
1. No PC local, `git status --short` e `git pull --ff-only` se sem conflitos; guardar artefato do agente fora do Git público quando contiver dados sensíveis.
2. Extrair novamente DB em modo leitura `php scripts/audit.php`, capturar `manifest.revision`, `configuration_hash`, `stage_roles` e IDs do dia; usar **somente** SQL SELECT `SHOW COLUMNS` autorizados.
3. Rodar `python scripts/reconcile_v4_local.py --inventory reports/<data>/inventory.json --manifest <caminho-para-manifesto-v4-local>`, que compara roles, stage IDs, tipos, basenames e links em **modo estritamente read-only**.
4. Gerar relatório local JSON/Markdown de comparação com origem `MANIFESTO_GIT` / `BANCO_REAL` / `VIDEO_CDN_AMOSTRA` / `RELATO_AGENT`; se discrepância permanecer, `BLOCKED`, com IDs concretos e evidência, sem suprimir diferença.
5. Executar `python scripts/validate_editorial.py` na V5 atualizada e capturar o exit code.
6. Abrir vídeos originais no PC; confirmar D+5 pergaminho, D+3 interface de jogos, D+4 bolhas e D+10 personagem. Extrair SHA256/duração/resolução e observar ruídos/dizeres. **Não fazer OCR em massa**; leitura visual humana e notas.
7. Capturar do gestor somente GET/preview autenticados das campanhas e conferir T0 (com/sem nome), labels/URLs de botões, asset, PDF, opt-out e e-mail (sem salvar).
8. Resolver P0 D+10 com aprovação operacional específica, independente da evolução editorial. O `blocked_do_not_publish` no JSON é apenas controle local.

## Critério de conclusão
**GO** apenas para revisão local de copy, vídeos e dados de demonstração, com as evidências disponíveis.
**NO-GO** para atualizar `messages`, `campaign_stage_actions`, `manifest.mapping` ou envios de produção antes de:
- correspondência DB ↔ manifesto por role/ID/mídia;
- prova de compatibilidade do manifesto/hash e do provisioner em dry-run;
- estratégia de tratamento dos snapshots ativos e do PDF D+10;
- aprovação comercial dos benefícios, CTA, prazo real de sessões e frequência;
- validação de STOP/conversões e rollback.

**Não foi feito nenhum deploy, envio ou mutação de CRM nesta auditoria.**
