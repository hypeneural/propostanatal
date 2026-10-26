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
