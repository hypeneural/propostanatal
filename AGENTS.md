## ORDEM ATUAL V5: WhatsApp + Z-API + história Google Docs (26/10/2026)
PRIMEIRO ler `docs/ANTIGRAVITY-HANDOFF-ZAPI-WHATSAPP-V5-20261026.md` e `docs/EDITORIAL-V5-20261026/07-WHATSAPP-ZAPI-FORMATACAO-CONTRATO.md`, `08-BASE-HISTORICA-GOOGLE-DOCS.md`, `09-VEREDITO-FORENSE-ANTIGRAVITY.md` antes de alterar a copy. Validar com `scripts/run-editorial-checks.ps1` (somente leitura) e `scripts/preview_whatsapp_v5.py`. V4 **REJEITA** o checkout direto como botão T0 e exige IDs `natal2026-reservar/horarios/instagram` e 3 URLs catálogo/horários/Instagram nessa ordem; não confundir `btn_reservar` do relatório antigo com manifesto aprovado. Legendas `title` de image/video têm cap 200 chars Unicode; `title` de PDF é fileName; `message` do action é corpo e `description` é footer. Usar *negrito* e _itálico_ literais WhatsApp; jamais tags Eleven v4 na mensagem ao cliente. `fallback_title` não é selecionado automaticamente. Não fazer deploy, --apply, Doctor, updates de DB, enviar WhatsApp, ou acionar voz; P0 PDF D+10 tem runs congelados até supressão autorizada.

## Prioridade P0 — auditoria forense em 26/10/2026
Leia [docs/FORENSE-ANTIGRAVITY-CONFRONTO-20261026.md](docs/FORENSE-ANTIGRAVITY-CONFRONTO-20261026.md) e [docs/ANTIGRAVITY-RETOMADA-CIRURGICA-20261026.md](docs/ANTIGRAVITY-RETOMADA-CIRURGICA-20261026.md) ANTES de reaproveitar o último relatório local. IDs 194x, URLs /resources/img/ e /resources/pdf/, tabela campaign_execution_items.snapshot_title, email HTML/PDF e SMS 10x/SAIR não devem ser considerados fatos V4 sem prova por inventário atual. O código do CRM utiliza `campaign_contact_stage_runs.stage_snapshot` / `legacy_messages` para execuções congeladas. `scripts/reconcile_v4_local.py` compara DB+manifesto por papel/etapa/mensagem. `scripts/inspect_local_media.py` confere SHA de MP4 locais sem FFmpeg. D+5 agora usa pergaminho mágico na V5 draft; “desde 2011” está em T0. Preservar SMS/Resend/SmartVoice, no production writes.

## Ordem atual de trabalho — EDITORIAL_V5_DRAFT
Quando a solicitação envolver melhorar copy/caption/vídeo/áudio, leia PRIMEIRO os quatro arquivos de `docs/EDITORIAL-V5-20261026/`, então `editorial/whatsapp-v5.draft.json`. Rode `python scripts/validate_editorial.py` e revise o conteúdo papel a papel, iniciando em `initial` (T0) e avançando para H+1, H+90, D+1, D+2, D+3, D+4, D+5, D+10. Nove papéis SMS ficam como `preserve_existing_v4`, Resend T0 e SmartVoice devem ser mantidos sem alteração nesta primeira revisão.

**Duas proteções críticas:** (1) `editorial/whatsapp-v5.draft.json` NÃO é um manifesto compatível com o provisionador do CRM, nem autorização para aplicar mudanças. (2) `document_d10` é `blocked_do_not_publish` porque simula comprovante Pix, com snapshots antigos potencialmente pendentes — revalidar e tratar exclusivamente por mecanismo seguro/autorizado. Não executar DML, aplicar manifesto, reenviar ou alterar status. O repositório é público; nenhum relatório com dados de clientes pode entrar aqui.

---

# Guia operacional Antigravity — Proposta Natal 2026

## Missão imediata — AUDIT_ONLY
Não modificar o CRM, não disparar WhatsApp, SMS, e-mail ou VoIP. Não executar migrations, deploy, provisionamento, botão salvar, comandos de diagnóstico que persistam status nem SQL DML. Não criar branches/PRs adicionais se não necessários. Este repositório é um kit isolado que só lê o CRM.

1. Execute `php scripts/audit.php --self-test`.
2. Configure `.env.local` no seu ambiente autorizado (nunca no repositório). Se já houver acesso seguro ao banco na VPS, use este; não tente inventar senhas.
3. Execute `php scripts/audit.php` com PHP PDO MySQL e leia os artefatos em `reports/`.
4. Compare inventário completo com o manifesto V4 de `hypeneural/evydencia-crm`, que é apenas referência — banco em produção tem precedência factual. Consulte `docs/REFERENCE.md`.
5. Entregue: mapa cronológico completo, todos os textos/legendas/URLs não sensíveis, ações ativas e pausadas, vínculos de mídias, cronogramas, variáveis, diferenças entre campanhas 415 e 416, cadastro V4 versus histórico e classificação das incertezas.
6. Verifique tecnicamente o envio: `MessageVariableService::parseMessage` substitui em `title`, `message`, `description`; o caminho legado de `CampaignMessageService` pode usar `title` como caption de mídia. Evite dizer que description aparece no WhatsApp sem confirmar.
7. **Atenção:** `php artisan proposals:doctor natal` atualiza o estado de saúde no banco. Não rodar nessa fase.
8. Compare os snapshots de execução congelados separadamente, com segurança e agregados. Não copiar campos com dados de contato nem emitir payloads de clientes.
9. Faça relatório de diferenças e proposta de execução futura. Não aplicar alterações em produção nesta fase.

## Critérios de aceite
- Sem PII/credenciais em commit, terminal compartilhado ou relatório enviado.
- Cada etapa tem ID, campanha, status, policy, ordem e cronograma com âncora.
- Cada mensagem tem título, corpo, description, tipo, mídia e legenda **inferida** (não confundir com fato de entrega).
- Cada ação informa provider, canal, status, texto de campanha e condições.
- Versões de execuções classificadas como agregados quando o schema permitir.
- Itens antigos e voz não contados como etapas novas V4.
- Comprovar origem: `PRODUCAO_DB`, `MANIFESTO_GIT` ou `INFERENCIA_CODIGO`.
- Relatório final identifica prontamente o que pode mudar via painel sem deploy e o que requer alteração de código/versionamento.

Não afirmar que já acessou produção se não houver arquivo gerado por uma conexão real e exit code 0.
