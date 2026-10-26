> **V5 CANÔNICA / AUDITORIA CORRIGIDA (26/10/2026)** — [Parecer GO/NO-GO](docs/EDITORIAL-V5-20261026/15-PARECER-VALIDACAO-GO-NOGO.md) · [Copy gerada a partir do JSON](docs/EDITORIAL-V5-20261026/14-COPY-CANONICA-V5-CANDIDATA.md) · [Missão local do Antigravity](docs/EDITORIAL-V5-20261026/16-ANTIGRAVITY-MISSAO-LOCAL.md). Fonte única de edição: `editorial/whatsapp-v5.draft.json`. Os documentos em `relatorios/` e o dossiê 13 são históricos, NÃO fontes finais de copy. Os 12 D+10 pendentes eram **8+4 na extração de 26/10** (1 skipped à parte); P0 em produção continua aberto. **GO apenas para homologação offline; NO-GO para canário ou disparo.** Rodar `powershell -NoProfile -ExecutionPolicy Bypass -File scripts/run-editorial-checks.ps1`.

> **NOVA AUDITORIA MULTICANAL — 26/10/2026:** [Gestor sem VPS](docs/EDITORIAL-V5-20261026/10-PUBLICACAO-PELO-GESTOR-SEM-VPS.md) · [Anexos e áudios a gravar](docs/EDITORIAL-V5-20261026/11-AUDITORIA-DE-ANEXOS-E-AUDIOS.md) · [SMS, Resend e SmartVoice](docs/EDITORIAL-V5-20261026/12-SMS-RESEND-VOZ-TELEFONE.md) · [Prompt para Antigravity](docs/ANTIGRAVITY-HANDOFF-MIDIAS-PAINEL-20261026.md). Validação de todos os 11 anexos locais: `python scripts/review_v4_assets.py --self-test`. O conteúdo da V5 ainda é **rascunho**, não publicado. A edição direta de mensagens aprovadas no gestor pode invalidar o hash do fluxo.

> **ATUALIZAÇÃO V5 / WHATSAPP E Z-API — 26/10/2026:** [Formato nativo e contrato exato do CRM](docs/EDITORIAL-V5-20261026/07-WHATSAPP-ZAPI-FORMATACAO-CONTRATO.md) · [Aprendizados Google Docs 2024/25](docs/EDITORIAL-V5-20261026/08-BASE-HISTORICA-GOOGLE-DOCS.md) · [Correções forenses do relatório Antigravity](docs/EDITORIAL-V5-20261026/09-VEREDITO-FORENSE-ANTIGRAVITY.md) · [Handoff local](docs/ANTIGRAVITY-HANDOFF-ZAPI-WHATSAPP-V5-20261026.md). O botão checkout direto está **BLOQUEADO pela allowlist V4**; V5 draft preserva URLs/IDs aprovados. Validar offline: `powershell -NoProfile -ExecutionPolicy Bypass -File scripts/run-editorial-checks.ps1`. Simular: `python scripts/preview_whatsapp_v5.py --role initial`.

> **AUDITORIA FORENSE DO RELATÓRIO ANTIGRAVITY — 26/10:** [Divergências verificadas](docs/FORENSE-ANTIGRAVITY-CONFRONTO-20261026.md) · [Retomada local com comandos prontos](docs/ANTIGRAVITY-RETOMADA-CIRURGICA-20261026.md). O relatório local recente apresenta IDs/URLs e cadeia de snapshots conflitantes com V4; não o tratar como manifesto. Para nova verificação: `python scripts/reconcile_v4_local.py --self-test`, `python scripts/inspect_local_media.py --self-test`; usar arquivo de manifesto e inventário locais para análise completa. A V5 atualizada mantém D+5 alinhado ao pergaminho e inclui “desde 2011” no T0.

> **NOVO — fase editorial V5 (26/10/2026):** [Fontes e benefícios](docs/EDITORIAL-V5-20261026/00-FONTES-E-CLAIMS.md) · [Cadência WhatsApp completa](docs/EDITORIAL-V5-20261026/01-CADENCIA-WHATSAPP.md) · [Roteiros de áudio Eleven v4](docs/EDITORIAL-V5-20261026/02-ROTEIROS-ELEVEN-V4.md) · [Execução Antigravity](docs/EDITORIAL-V5-20261026/03-EXECUCAO-ANTIGRAVITY.md) · [JSON de revisão por etapa](editorial/whatsapp-v5.draft.json). Os materiais são **RASCUNHO**, não conteúdo de produção. Validação offline: `python scripts/validate_editorial.py`.

> **Revisão forense adicional:** [docs/REVISAO-FORENSE-INDEPENDENTE-20261025.md](docs/REVISAO-FORENSE-INDEPENDENTE-20261025.md) documenta correções importantes em PDF, snapshots, relatórios dinâmicos, SMS, e-mail e voz. Leia antes de alterar o CRM.

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
