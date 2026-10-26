# Antigravity — próximo passo objetivo: V5 WhatsApp e Z-API
**26/10/2026 | Ordem atual prioritária.** O usuário quer proposta curta, humanizada, vídeo/áudio coerentes e CTA de reserva sem quebrar o CRM. Fase: **EDITORIAL DRAFT / AUDIT_ONLY**.

## O que já está pronto no Git
- Novo inventário relatado pelo próprio Antigravity: `reports/20261026-123430/`, 38 etapas V4 reconciliadas, 32 mensagens, 8 MP4 `MEDIA_MATCH`. **Artefatos são locais/ignorados, não confundir com conteúdo versionado**.
- Base WhatsApp V5 atualizada: `editorial/whatsapp-v5.draft.json`, 19 papéis incluindo 9 SMS intocados e D+10 bloqueado, T0 415/416 curtos (340 caracteres), legendas ≤200 Unicode.
- Documentos novos: 
  - `docs/EDITORIAL-V5-20261026/07-WHATSAPP-ZAPI-FORMATACAO-CONTRATO.md`
  - `docs/EDITORIAL-V5-20261026/08-BASE-HISTORICA-GOOGLE-DOCS.md`
  - `docs/EDITORIAL-V5-20261026/09-VEREDITO-FORENSE-ANTIGRAVITY.md`.
- `scripts/preview_whatsapp_v5.py`: preview puro, substitui nome/contagem **por exemplos**, nunca envia; `scripts/run-editorial-checks.ps1`: checks locais sem credenciais.
- CRM source `app/Services/Proposals/Natal2026ProposalManifest.php`: **T0 v4 só aceita botões** com IDs `natal2026-reservar`, `natal2026-horarios`, `natal2026-instagram` e URLs fixas catálogo/horários/Instagram. **Checkout direto `reserva.fotosdenatal.com` NÃO PASSA no validador atual!** O JSON draft foi corrigido para rota aprovada; plano de checkout direto deve ser chamado `CONTRACT_CHANGE_REQUIRED`.

## Comandos, em ordem — Windows PowerShell
```powershell
git status --short
git fetch origin main
# Preserve qualquer alteração local. Somente use pull --ff-only quando árvore estiver apta:
git pull --ff-only origin main
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/run-editorial-checks.ps1
python scripts/preview_whatsapp_v5.py --role whatsapp_d3
python scripts/preview_whatsapp_v5.py --role whatsapp_d5
```

**Não** reconsultar todo o banco e baixar todos os MP4 de novo se `reports/20261026-123430/` e `scratch/video_cache/` ainda existirem e corresponderem ao mesmo `revision/configuration_hash`. Revalidar *somente* fonte divergente.

## Etapa 1 — Aprovar texto e campo correto sem alterar DB
1. Ler `07`, `08`, `09`; usar o manifesto V4 e `V5-DIFF-AUDITADO.md` local como evidência, observando as correções do adendo 09.
2. Revisar no simulador e no Chrome os conteúdos T0, H+1, H+90, D+1, D+2, D+3, D+4, D+5, D+10. Escolher 1 argumento + 1 CTA por etapa. PDF + arte T0 imediatamente; jogo/galeria D+3; bolhas D+4; pergaminho D+5.
3. Em `action`: corrigir `message.message` (body), `title` (header) e `description` (footer) conforme contrato; `title` de vídeos/imagens = caption; `title` de PDF = fileName. **Não** editar `description` de vídeo e esperar caption.
4. WhatsApp nativo: `*texto*`, `_texto_`, emojis e linha em branco. Não usar `**texto**`, HTML, triple-quotes, triple-backticks, nem tags `[excited]` de ElevenLabs no WhatsApp.
5. Os textos V5 são editoriais, não campos persistidos. Exportar diff por `role / stage_id / message_id / field / 415-416 / original / proposta / evidência / status`.
6. **D+1 token countdown:** verificar no código Laravel e no preview legítimo que `{natal_countdown_dias_descricao}` é renderizado em `messages.title` quando worker envia. O `fallback_title` no JSON draft **NÃO** é automático; se não houver prova, usar `fallback_title` literal como caption aprovada na versão executável.

## Etapa 2 — Contrato de publicação: apontar menor mudança, sem executar
- Comparar hash `configuration_hash` de V4 aprovado com manifesto/DB. Modificar o conteúdo isoladamente pelo gestor pode tornar o hash inválido.
- Se a direção desejar **checkout direto no botão 1**, planejar **um único patch contratual versionado** no CRM: allowlist, validador, testes de URLs/IDs, manifesto/editor/publicador, rollback. Não mudar somente uma coluna ou o destino do botão em DB.
- Confirmar docs da Z-API: image/video caption, attachment filename, actions URL/REPLY/CALL; verificar comportamento no celular; sem Z-API em vivo nesta fase.
- D+3 jogo: link demonstrativo https://jogos.fotosdenatal.com/; galeria demo https://jogos.fotosdenatal.com/demo/fotos somente se testada no mobile e sem conteúdo privado.
- A V4 atual **não** libera nova etapa de áudio via troca de manifesto arbitrária; áudio de Papai Noel via Z-API é proposta posterior, separada de SmartVoice.
- Não renomear IDs de botão para `btn_reservar`, etc. Embora estejam nos relatórios locais, o manifesto Git V4 usa `natal2026-*`.

## Etapa 3 — Safety gates e execução
- D+10: arquivo com aparência de comprovante Pix é **P0**. O relatório Antigravity aponta **12 runs pendentes** em 26/10; confirmar situação atual e agendamento antes de escalonar rotina de supressão autorizada. O draft marcado bloqueado **não** impede o worker de enviar.
- Não adicionar e-mail HTML/PDF à ação Resend T0 sem contrato; não alterar SMS e SmartVoice.
- Não executar `proposals:doctor`, `--apply`, updates SQL, edição painel com save, disparo real ou cancelamento até aprovação específica.
- Se houver divergência, apresentar a linha exata e bloquear só aquela publicação, sem loops de auditoria.

## Entrega esperada (máximo 4 arquivos locais de evidência, sem dados pessoais)
- `reports/20261026-123430/V5-COPY-REVIEW-ATUALIZADO.md`: versão curta final com campos exatos, nome/sem nome, captions reais, caracteres, formatação WhatsApp.
- `reports/20261026-123430/CONTRATO-BOTOES-URL-GATE.md`: provas do parser V4 e duas alternativas: manter catálogo (compatível) x liberar checkout direto (exige patch).
- `reports/20261026-123430/COUNTDOWN-RENDER-GATE.md`: teste local do token na legenda e decisão sobre fallback.
- `reports/20261026-123430/PROXIMOS-PASSOS-GO-NOGO.md`: por etapa e canal, diferenças/responsável/risco e plano controlado de publicação. Publicar no Git apenas copy sem PII após revisão.

**Estado final permitido:** `V5_EDITORIAL_READY_FOR_APPROVAL`, nunca `PRODUCTION_APPLIED`.

## Prompt resumido para execução local
> Atualize sem perder alterações locais o repo hypeneural/propostanatal. Leia 07, 08, 09 e este handoff. Use os relatórios locais reports/20261026-123430 sem repetir DB/vídeos se ainda consistentes. Rode scripts/run-editorial-checks.ps1 e preview_whatsapp_v5.py. V4 só aceita 3 botões URLs catálogo/horários/Instagram com IDs natal2026-*, então não troque checkout nem ID no gestor. Revise V5 T0→D+10 com formatação WhatsApp *negrito*, _itálico_, emojis e captions ≤200. Confira cena real, legenda no title, ação no message, PDF title como fileName. D+1 fallback não é automático: teste renderização, caso contrário use literal. Documente diferenças e bloqueios de hash/snapshots/PDF Pix D+10 (12 pendentes relatados). Preserve SMS/Resend/SmartVoice, sem apply, sem Doctor, sem envio e sem salvar no CRM. Entregue apenas 4 artefatos locais para aprovação comercial e técnica.
