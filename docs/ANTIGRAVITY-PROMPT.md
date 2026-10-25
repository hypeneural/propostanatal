# Prompt operacional: Antigravity

> Acesse SOMENTE o projeto atual `hypeneural/propostanatal` como workspace de mudanças e auditoria. Fonte de consulta externa: `hypeneural/evydencia-crm` (somente leitura).

Faça o seguinte sem perguntar novamente:

1. Rode `php scripts/audit.php --self-test` e resolva eventuais problemas na ferramenta local.
2. Localize a conexão MySQL de produção já autorizada na máquina/servidor, sem mostrar valores de `.env`, credenciais ou tokens ao modelo, terminal compartilhado ou GitHub. Se não houver credencial SELECT-only, informe o impedimento; não crie permissões nem modifique o CRM.
3. Configure `.env.local` local (já ignorado pelo Git). Rode `php scripts/audit.php` e abra os relatórios em `reports/`.
4. Valide o resultado contra esquema real no CRM, investigando questões de coluna ausente/erro SQL e corrigindo apenas `scripts/audit.php` neste repositório.
5. Faça a auditoria técnica entre DB, `manifesto-natal-2026.v4.json`, snapshots R2/R3/V4, `CampaignMessageService`, `MessageVariableService`, `CampaignStageScheduleResolver`, histórico e VoIP. Não confunda etapa criada com execução efetivamente ativa. Não trate `description` como caption enviado sem evidência do serializer.
6. Extraia uma matriz completa de mensagens e periodicidade, destacando vídeos D+1/D+3/D+5/D+10, countdown, títulos técnicos, PDF D+10 suspeito, botões, destino de links, status ativo/pausado, janelas de envio e números de tentativas/aceites **somente agregados e anonimizados**.
7. Gere localmente `reports/<timestamp>/AUDITORIA-FINAL.md` incluindo riscos e sugestões ordenadas para o futuro, sem executar correções produtivas.
8. **Não criar PR para o CRM, deploy, release, SQL UPDATE, disparo, provisionamento, POST, restore ou diagnóstico que altera health.** Corrigir apenas o kit independente quando necessário. O pedido atual termina na fotografia fiel do banco.

Concluir com `AUDITORIA CONCLUÍDA` se houver evidência real do DB; caso contrário `AUDITORIA PARCIAL` ou `BLOQUEADA`. Incluir origem e limitações, sem mascarar erros. Minimizar testes: apenas self-test e validação do inventário produzidos.
