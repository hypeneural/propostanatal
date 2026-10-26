# 09 — Crítica forense do último relatório Antigravity e GO/NO-GO real
**26/10/2026 | Apêndice de revisão | Base do usuário: V5-DIFF-AUDITADO.md, MIDIA-EVIDENCIAS.md, GO-NOGO.md, Walkthrough + Plano Técnico (arquivos no chat).**
**SEM nova consulta direta ao DB nesta sessão; as extrações foram relatadas pelo Antigravity.**

## Achados conciliados que podem ser usados com confiança adequada
- O Antigravity relatou `scripts/reconcile_v4_local.py` → `CONSISTENT_FOR_EDITORIAL_REVIEW_ONLY`, com **38 etapas** (19/variante), **32 mensagens** (1445–1476) e nenhum problema.
- Relatou `scripts/inspect_local_media.py` → `MEDIA_MATCH` com **8 MP4**, SHA-256 e bytes do manifesto, frames em 15/50/85%. **Isso verifica integridade e 3 amostras por vídeo, NÃO “cada frame” nem prova auditiva automática.** Descrições da trilha/voz exigem escuta humana registrada.
- Reportou **12 agendamentos/snapshots pendentes D+10** em 26/10 (antes havia 11). Tratar contagens como fotos do tempo, não quantidade fixa para cancelamento posterior. Verificar estado exato no dia, data e item antes de qualquer decisão autorizada.
- A divergência preliminar dos IDs 194x vs 144x parece ter vindo dos IDs de vínculo (pivô); o Git V4/DB reconciliados e `messages.id` são referências primárias para diff. Contudo, `campaign_stage_messages` do relatório não deve ser dado como nome canônico sem `SHOW TABLES`; o CRM Laravel usa `stage_messages`.

## Novas CORREÇÕES P0 identificadas ao auditar o código da main CRM
1. **URLs/IDs de botões:** o relatório chama de aprovados `btn_reservar`, `btn_horarios`, `btn_insta` e checkout direto `reserva.fotosdenatal.com`. Entretanto o `Natal2026ProposalManifest.php` exige em V4 **IDs do manifesto** `natal2026-reservar`, `natal2026-horarios`, `natal2026-instagram` e URLs `evydencia.com/catalogo/natal`, `horarios.fotosdenatal.com`, `instagram.com/estudioevydenciaa` nesta ordem. **Checkout direto NÃO PASSA validador V4**, mesmo que a URL dê HTTP 200. Corrigido no `editorial/whatsapp-v5.draft.json` e `scripts/validate_editorial.py`.
2. **Teto de captions:** o validador V5 anterior aceitava **500 caracteres**, mas o manifesto Laravel `messages.title` limita a **200 Unicode**. Corrigido; preferência editorial 160.
3. **Cadeia WhatsApp:** `CampaignMessageService` usa `message.message` para `action`, `title` para image/video caption e `title` para PDF fileName; no action, o `description` vira footer. O provedor tem `caption` de documento, mas o wrapper V4 atual **não** passa esse campo. Textos em `description` de vídeo são invisíveis no WhatsApp.
4. **Formatação:** negrito nativo `*texto*`, itálico `_texto_`; três crases (monospace) não são equivalentes a aspas triplas `'''`. O relato diverge do conteúdo exato do manifesto V4: no Git, triple-backticks estão no **campo `intro_cta.message`** (corpo). Uma revisão de `title` isoladamente não remove esse elemento.
5. **Fallback D+1:** `fallback_title` no JSON do rascunho é apenas uma instrução editorial; nenhuma rotina comprovada alterna esse título automaticamente no envio. O teste de renderização de `{natal_countdown_dias_descricao}` via `MessageVariableService::parseMessage` em fluxo de mídia é **gate obrigatório**.

## Pontos que o relatório anterior classificou com confiança excessiva
- `GO` não é `PUBLISHED`. A reconciliação e o SHA não provam entrega dos SMS, Resend ou SmartVoice, apenas que **foram preservados** editorialmente.
- HTTP 200 de jogos/galeria/calendário/checkout só prova resposta HTTP. Não comprova funcionalidade dos jogos, privacidade da galeria demo ou disponibilidade real de horários.
- “Até 10x”, “quase esgotado”, “posso segurar uma data” e “personagem incluído” são claims sem prova contratual. Não reutilizar.
- “PDF com extensão .pdf corrige erros em Android/iOS” é hipótese de UX: melhora nome legível, mas sem teste A/B/cliente receptor não se pode afirmar falha corrigida.
- A legenda de D+1 com contador dinâmico exige prova runtime. A V5 não tem fallback automático — classificar como `PENDING_RENDER_GATE`, não `100% pronto`.
- `12` snapshots pendentes de D+10 em 26/10 necessitam **consulta atualizada**; o PDF Pix é um bloqueio de produção independente de todo o trabalho editorial.

## Roteiro de saída do Antigravity para esta rodada
1. Concluir V5 WhatsApp com mensagens **curtas** e prova por cena real, título técnico < 200, ideal 80–160, sem tags Eleven.
2. Inspecionar o novo documento de contrato `docs/EDITORIAL-V5-20261026/07-WHATSAPP-ZAPI-FORMATACAO-CONTRATO.md`.
3. Validar localmente `python scripts/validate_editorial.py` e confirmar `GO` apenas para textos/editorial; **sem DB writes**.
4. Comparar V5 botões T0 IDs+URLs com **manifesto V4 do Git** e **estado live `manifest.mapping`**. Se querem checkout direto, abrir uma mudança contratual separada (sem execução agora).
5. Usar relatório Antigravity como evidência de mídia conferida **por integridade+amostras**; confirmar áudios com escuta humana (e consentimento/publicidade de retratos).
6. Produzir diff final por **campo exato**, com evidencia `PRODUCAO_DB` vs `MANIFESTO_GIT` vs `CONTEUDO_HISTORICO` vs `EDITORIAL_DRAFT`.
7. Respeitar o `configuration_hash`, snapshots e P0 D+10; NÃO editar/ativar voz/SMS/Resend, nem publicar a V5 sem aprovação.

**NENHUMA ação no CRM ou envio real foi feita por este documento.**