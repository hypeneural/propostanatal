# 27 — Escopo definitivo: Antigravity SOMENTE Proposta Natal | CRM somente Codex/ChatGPT

**Data:** 27/10/2026. **Documento atual prevalece sobre instruções anteriores** em 23, 24, 25 e 26 que equivocadamente pediam ao Antigravity acesso/validação do CRM.

## Propriedade irrevogável das tarefas
| Executor | Repositório autorizado | Pode fazer | Não pode fazer |
|---|---|---|---|
| **Antigravity** | `hypeneural/propostanatal` | Revisar e editar copy canônica, legenda dos 8 vídeos, scripts Python de preview/validação, mídia local já anexada, documentação, executar CI editorial, fornecer dossiê para Codex | **NÃO abrir/alterar/clonar `hypeneural/evydencia-crm`; NÃO SSH/VPS/MySQL/SQL/Laravel/PHPUnit/manifesto privado de produção; NÃO editar Issue #434 ou PR CRM; NÃO ativar Z-API/SMS/Resend/voz** |
| **ChatGPT** | `hypeneural/evydencia-crm`, `hypeneural/propostanatal` | Pequenas correções técnicas, auditá-las e organizar entregas | Não executar deploy/produção sem autorização |
| **Codex** | `hypeneural/evydencia-crm` | Executar/analisar hotfix Laravel, PHP/MySQL sintético, release controlado após aprovação, obter hash LIVE por acesso autorizado, manifesto executável e rollback | Não interpretar dossiê editorial como aprovação de produção |

**CRM:** [Issue #434](https://github.com/hypeneural/evydencia-crm/issues/434) · branch preparada `codex/N-434-natal-v52-editorial-successor`. PR histórica #435 foi **fechada sem merge** por usar nome de branch equivocado. Codex assumirá criando PR nova quando autorizado na etapa posterior. Não pedir agora ao Antigravity qualquer etapa envolvendo CRM.

## Decisão D+10 — claro e separado
O requisito de UX é: **D+10 envia uma única mensagem do tipo `attachment` (PDF), SEM mensagem de texto adicional, SEM legenda, SEM descrição, SEM botões no próprio anexo.**
- `message = null`; `description = null`; `buttons = []`.
- O atributo `title` é obrigatório como **nome de arquivo `.pdf`** na Z-API, não legenda de marketing.
- O anexo **não pode ser o histórico que simula um comprovante de transferência Pix**. A falta de legenda não torna um comprovante simulado autêntico. Na V5.2, o candidato seguro usa o **PDF legítimo da proposta T0** como documento isolado em D+10.
- A etapa `document_d10` histórica do draft permanece `blocked_do_not_publish`; não tocar em snapshots antigos. Codex tratará contenção dos itens congelados/outbox antigos no CRM. **Aprovação comercial do layout isolado não é aprovação para enviar documento enganoso**.
- Regra editorial documentada em `editorial/whatsapp-v5.draft.json` → `stage_edits[document_d10].delivery_layout`. Gerador `scripts/build_v52_release_candidate.py` verifica `message=null`, `description=null`, apenas um PDF, title filename e reutilização segura.

## Uma única sequência Antigravity: somente editorial

1. No **clone propostanatal**, `git fetch origin main`, `git pull --ff-only origin main` preservando worktree; ler AGENTS.md e este documento 27.
2. Executar **um comando local, sem parâmetros CRM**:
   ```powershell
   powershell -NoProfile -ExecutionPolicy Bypass -File scripts/prepare-v52-local.ps1
   ```
   Não adicionar `-CrmRoot`, `-ValidateCRM`, `-CurrentHash`, `-CommercialApproved`, `-P0Contained` nem `-ManifestOutput`. O script foi deliberadamente simplificado para verificar apenas documentos e ferramentas locais.
3. Revisar o arquivo `editorial/whatsapp-v5.draft.json` (fonte única): duas variantes 415/416, intro com `{saudacao}` e `{primeiro_nome}`, T0 PNG+PDF, 8 vídeos e captions nativas da Z-API, H+1 STOP reply, D+3 jogos/galeria, D+4 dez motivos/STOP, D+1 countdown estático até canário, D+10 vídeo e PDF isolado seguro, 9 SMS preservados no contrato. **Não alterar canais adicionais ou horários/IDs de mídia**.
4. Validar captions de imagens/vídeos com teto CRM 200 caracteres; as legendas ficam em `image/video.title`. Para `attachment`, `title` é **filename**, sem caption; `description` não gera legenda para documento.
5. Caso haja mídia disponível **somente no propostanatal** e já com autorização editorial, conferir coerência da legenda com o conteúdo real (D+3 jogos; D+4 bolhas; D+5 pergaminho). Fotos/áudios novos do fotógrafo/Papai Noel são opções futuras, **não** inseri-los na V5.2 sem reaprovação comercial.
6. Se os testes identificarem problema real de texto, corrigir **somente aqui**, regenerar cópia canônica com o utilitário existente e rodar os checks uma única vez. Não mexer no CRM para “corrigir” hashes/URLs/variáveis.
7. Gerar dossiê de handoff **sem dados de clientes, segredos ou hash LIVE**. Contar **30 alterações textuais** (15 para cada variante), 19 papéis por variante, 9 SMS preservados, 8 legendas de vídeos por variante, T0 imagem/PDF e D+10 PDF único. Não gerar manifesto executável final para produção: essa responsabilidade é do Codex.
8. Entregar SHA de `propostanatal`, link de CI PASS/FAIL, mudanças editoriais, lista de assets/gravacoes **realmente** faltantes e observações explícitas para o Codex. **Parar aí.**

### Checklist de entrega do Antigravity
- [ ] `EDITORIAL CHECKS OK` (somente propostanatal)
- [ ] Comparação WhatsApp T0, H+1, H+90, D+1…D+10 com nomes/legendas formatadas corretamente
- [ ] D+10 isolamento `attachment`, `message/description=null`, sem legenda; candidato seguro no novo manifesto
- [ ] Textos não prometem personagens presenciais, valores/ou brindes não confirmados, falsos prazos ou escassez
- [ ] Sem toque no CRM, VPS, MySQL, filas ou contatos
- [ ] Dossiê curto com handoff para Codex / Issue #434
- [ ] `NO_GO_RELEASE_UNTIL_CODEX_HIGH_P0` explícito

## Para o Codex depois (não enviar agora ao Antigravity)
Conferir [Issue #434](https://github.com/hypeneural/evydencia-crm/issues/434) e branch `codex/N-434-natal-v52-editorial-successor`. Executar testes PHP/MySQL de HIGH risk, corrigir snapshots/catalogue/rollback, validar P0 antigo, gerar manifesto integral usando `scripts/build_v52_release_candidate.py` apenas em ambiente autorizado, obter hash V4 atual, e emitir dry-run e diff no código efetivamente instalado. Sem deploy sem autorização. A PR histórica #435 está fechada.
