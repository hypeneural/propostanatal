# Referência técnica confirmada — não é cópia do banco

Consulta ao repositório `hypeneural/evydencia-crm` em 25/10/2026: main `42c29edd287b6e5531331d3da0cca61906679672`. Documento `docs/active/natal-2026-proposta/manifesto-natal-2026.v4.json`, revisão `natal2026-content-v4-20261021` (approved), possui 19 etapas por variante.

**Autoridades distintas:** `proposal_flows.settings.readiness.manifest.mapping` seleciona IDs de estágio atuais para novas propostas; `retained_catalog` mantém conteúdo antigo; `campaign_scheduleds.suppression_snapshot.natal_execution_plan` congela execuções já criadas. As campanhas 415 e 416 não são intercambiáveis.

| Papel | Referência de relógio | Conteúdo |
|---|---|---|
| initial | T0 | texto + imagem + PDF; e-mail condicionado |
| whatsapp_h1 | T+60 min | vídeo + texto botões |
| whatsapp_90min | T+90 min | vídeo + reserva |
| sms_h2 | T+2 horas | SMS |
| whatsapp_d1 | D+1 relativo | vídeo D+1, title técnico e description vazia |
| sms_d1 | D+1 12:30 | SMS |
| whatsapp_d2 | D+2 relativo | vídeo e reserva |
| sms_d2 | D+2 19:30 | SMS |
| whatsapp_d3 | D+3 relativo | vídeo jogos; description vazia |
| sms_d3 | D+3 09:45 | SMS |
| whatsapp_d4 | D+4 relativo | vídeo e reserva |
| sms_d4 | D+4 13:30 | SMS |
| whatsapp_d5 | D+5 relativo | vídeo; description vazia |
| sms_d5 | D+5 18:45 | SMS |
| whatsapp_d10 | D+10 relativo | vídeo; description vazia |
| document_d10 | D+10 relativo | PDF com título suspeito Comprovante_de_Transferencia Pix_01112026_200921 — investigar adequação |
| sms_d10 | D+10 10:15 | SMS |
| sms_d15 | D+15 19:15 | SMS |
| sms_d20 | D+20 12:45 | SMS |

A série SmartVoice `NatalProposalVoiceSeries` tem slots adicionais +1h, D+1, D+5, D+7, D+10, cujo estado deve ser comprovado no banco e na feature flag. Históricos podem ter versão R2/R3, e-mail/voz e outras ações.

## Localização do código fonte
- `app/Services/Proposals/NatalProposalContentProfile.php`
- `app/Services/Proposals/Natal2026ProposalManifest.php`
- `app/Services/Proposals/NatalProposalExecutionPlan.php`
- `app/Services/Proposals/Natal2026ProposalProvisioningState.php`
- `app/Services/Proposals/ProposalReadinessSnapshot.php`
- `app/Services/CampaignScheduledService.php`
- `app/Services/Messages/CampaignMessageService.php`
- `app/Services/MessageVariableService.php`
- `app/Services/Proposals/NatalProposalVoiceSeries.php`

Contagem regressiva: tokens `{natal_countdown_dias_descricao}`, `{natal_countdown_semanas_descricao}`, `{natal_countdown_dias_horas_descricao}`, `{natal_countdown_descricao}`. A existência do token no motor não comprova que ele esteja usado no manifesto ou no payload de produção.

Páginas de consulta (requer autenticação): `/gestao/campanhas/415/fluxo`, `/gestao/campanhas/416/fluxo`; detalhes de itens via GET. Não clicar em salvar, disparar ou diagnosticar.

## Interpretação de dados
- `catalog_current`: ID explicitamente mapeado na V4 aprovada persistida.
- `catalog_retained`: ID presente no catálogo histórico guardado.
- `voice_or_other`: etapa adicional; não concluir que esteja ativa sem verificar `is_active` e status da ação.
- `unmapped`: divergência a auditar (jamais ignorar).
- Agendamento de política relativa é âncora T0 ou etapa anterior; calendário pode especificar hora e janela. Horário programado não equivale a entrega.
- O print real do cliente mostra caption `Natal 2026 - D+1`, compatível com campo `title` do manifesto. Conferir tabela de mensagens e serializer antes de editar.
