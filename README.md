# Proposta Natal 2026 — auditoria independente

Ferramentas **separadas do código do CRM** para inventariar, conferir e preparar melhorias na comunicação comercial de Natal 2026 do Estúdio Evydência. O projeto **não faz deploy, não modifica o CRM e não envia mensagens**.

## Executar no Antigravity — caminho rápido

1. Abra este repositório no Antigravity. Leia [AGENTS.md](AGENTS.md).
2. Execute `php scripts/audit.php --self-test` para verificar o utilitário, sem conexão ao banco.
3. Crie localmente um arquivo `.env.local` usando `.env.example` como modelo; preencha a conexão MySQL autorizada de leitura. **Nunca cole senhas no chat, commit ou issue.**
4. Execute `php scripts/audit.php`. O PHP deverá ter extensão `pdo_mysql`. A ferramenta utiliza transação MySQL `READ ONLY`, com snapshot consistente.
5. Abra `reports/<data>/timeline.md`, `inventory.json`, `messages.csv` e `actions.csv`. A pasta reports está ignorada pelo Git.
6. Compare o banco produtivo com [docs/REFERENCE.md](docs/REFERENCE.md) e prepare sugestões, **sem executar mudanças na campanha**.

Windows PowerShell: `powershell -ExecutionPolicy Bypass -File scripts/run-audit.ps1`.

## Escopo efetivo

- Campanhas 415 (com nome) e 416 (sem nome), fluxo `proposal_flows.slug = natal`.
- Todas as etapas cadastradas, separando catálogo V4 selecionado, histórico retido, VoIP complementar, inativos e demais registros.
- `stage_messages`, `messages`, `campaign_stage_actions`, `communication_templates`: IDs, tipo, texto completo, títulos, descrições, mídia, botões, condições e periodicidade.
- Sinais **agregados**, limitados, de versões capturadas em execuções recentes (sem telefones, destinatários ou contatos).
- Legenda inferida pelo fluxo de código conhecido; o conteúdo realmente enviado exige confronto com snapshots e recibos do provedor.

## Segurança em primeiro lugar, sem burocracia

Este repositório foi criado como **público**. Nunca publique nele `.env.local`, relatórios de produção, URLs assinadas, credenciais, exportações de clientes ou os arquivos brutos do banco. Os resultados gerados ficam somente no computador que executou o script; `reports/` é ignorado.

Prefira conta de banco restrita a `SELECT` no schema autorizado. O script também força transação `READ ONLY` e usa exclusivamente consultas de leitura. Não usa comandos Artisan que possam persistir estados, não chama webhooks, não dispara mensagens e não inclui clientes no inventário.

**O que este script não faz:** ele não afirma que o cadastro atual será transmitido para todas as execuções anteriores. As versões de campanha podem estar congeladas em snapshots por destinatário. Também não valida entregabilidade Z-API sem leitura dos recibos.

## Se não houver conexão ao banco

Não invente host, senha ou túnel. Use o acesso à VPS/credenciais já autorizado no ambiente do Antigravity; caso não exista, extraia somente as páginas autenticadas de leitura no CRM (`/gestao/campanhas/415/fluxo` e `/gestao/campanhas/416/fluxo`) e declare a auditoria parcial.

## Ordem da iniciativa

**Fase 1:** inventário real → **Fase 2:** melhorar linguagem, captions e variáveis → **Fase 3:** decidir edição por painel/manifesto/hotfix → **Fase 4:** validar um envio explicitamente autorizado.

Não introduzir PRs ou deploys no projeto CRM por iniciativa própria. Veja [docs/REFERENCE.md](docs/REFERENCE.md) para base editorial V4 e [AGENTS.md](AGENTS.md) para critérios de conclusão.
