# AUDITORIA FORENSE MULTICANAL — PROPOSTA NATAL 2026

**Status:** EXTRAÇÃO FORENSE HISTÓRICA + ERRATA CRÍTICA 26/10/2026; não é autorização para produção.
**Data da Extração:** 2026-10-26T12:34:30+00:00
**Origem:** `PRODUCAO_DB_READ_ONLY` (Host: `127.0.0.1:3306`, Banco: `evyd25`, Transação Snapshot Read-Only)
**Manifesto Lido:** `natal2026-content-v4-20261021` (Status: `approved`)

---

## 1. Totais e Classificação do Catálogo

- Total de etapas cadastradas no banco: **89** (Campanha 415 com nome: 49; Campanha 416 sem nome: 40).
- Vínculos de mensagens: **54**, mensagens cadastradas únicas: **54**, ações configuradas: **34**.

| Classe do Catálogo | Cadastradas | Ativas (`is_active=1`) | Descrição |
|---|---:|---:|---|
| `catalog_retained` | 12 | 12 | Histórico R2/R3 retido em banco |
| `legacy_inactive` | 29 | 0 | Etapas legadas desativadas (sem mensagens ou ações) |
| `smartvoice_series` | 10 | 10 | Série telefônica VoIP SmartVoice (5 etapas por campanha) |
| `catalog_current` | 38 | 38 | V4 Ativo (19 etapas por campanha: T0 a D+20) |

---

## 2. Achado Crítico: PDF de D+10 (Etapas 666 e 685) e Snapshots Imutáveis

> [!WARNING]
> **Diagnóstico Técnico do Anexo D+10:**
> - **Título cadastrado:** `Comprovante_de_Transferencia Pix_01112026_200921`
> - **Mapeamento WhatsApp:** no CRM, `CampaignMessageService::sendAttachment` repassa `$message->title` como segundo parâmetro, que em `WhatsappPayload::attachment` se torna estritamente **`fileName`**. No WhatsApp do cliente, o arquivo é exibido com o nome `Comprovante_de_Transferencia Pix_01112026_200921.pdf`.
> - **Conteúdo Binário Auditado:** O PDF (`natal2026-rev357-v1-d10-document.pdf`, 87,5 KB) foi gerado pelo ReportLab e encapsula uma imagem JPEG (600x1026 px). O conteúdo é um **flyer promocional de Papai Noel** ("Você ainda não agendou suas Fotos de Natal no Estúdio Evydência!"), com um cabeçalho gráfico que simula um comprovante Pix fictício (sem dados bancários reais, sem PII de clientes). Trata-se de tática de clickbait/curiosidade para D+10.
> - **Risco Operacional:** O cliente pode interpretar o nome como golpe, fraude bancária ou spam, gerando denúncias na Z-API.

### Situação das Execuções Congeladas nos Clientes Ativos

| Etapa | Campanha | Status | Título Congelado (`stage_snapshot`) | Total | Primeiro Disparo Agendado |
|:---:|:---:|:---:|---|---:|:---:|
| 666 | 415 | `pending` | `Comprovante_de_Transferencia Pix_01112026_200921` | 8 | 2026-10-31 19:29:04 |
| 666 | 415 | `skipped` | `Comprovante_de_Transferencia Pix_01112026_200921` | 1 | 2026-11-04 16:09:05 |
| 685 | 416 | `pending` | `Comprovante_de_Transferencia Pix_01112026_200921` | 4 | 2026-11-03 09:32:06 |

**FATO DA EXTRAÇÃO DE 26/10/2026:** A tabela acima indica **12 execuções `pending` (8 na 415 e 4 na 416)**, além de **1 `skipped`** separado. Esses quantitativos são históricos e exigem nova consulta antes da intervenção.
**IMUTABILIDADE DO SNAPSHOT:** O método `CampaignPolicyLegacyMessages::hydrate` reconstrói o objeto `Message` diretamente do array congelado no agendamento. **Alterar `messages.title` no banco ou painel NÃO altera os 12 envios pendentes**, surtindo efeito apenas para novos clientes.
**JANELA DE AÇÃO:** O primeiro horário exibido no extrato é **2026-10-31 19:29:04**, mas o fuso da coluna não está demonstrado no relatório. Revalidar o timezone e a fila atual antes da supressão; “zero delivery items” é apenas o retrato da coleta e não uma garantia futura.

---

## 3. Auditoria de Legendas de Vídeos e Imagens

A análise do código-fonte `CampaignMessageService.php` comprova:
- Para `video` e `image`, o CRM passa `$message->title` como parâmetro de **`caption`** na Z-API.
- O campo `description` é completamente **descartado** no envio de mídias.
- No banco de dados, os títulos dos vídeos V4 contêm textos técnicos:
  - D+1 (Msg 1456 / 1472): `title = "Natal 2026 - D+1"`
  - D+3 (Msg 1457 / 1473): `title = "Jogos personalizados"`
  - D+5 (Msg 1458 / 1474): `title = "Natal 2026 - D+5"`
  - D+10 (Msg 1459 / 1475): `title = "Natal 2026 - D+10"`
- **Comprovante de Entrega Real:** Registros em `campaign_stage_action_delivery_items` comprovam que clientes reais receberam os vídeos com essas legendas:
  - Etapa 662 (D+1, Camp 415): 4 entregues (`delivered`), 2 lidos (`read`).
  - Etapa 681 (D+1, Camp 416): 3 lidos (`read`).

---

## 4. Auditoria dos Nove SMS (Velip)

A V4 possui 9 disparos SMS por variante, com políticas e horários de envio fixos:

| # | Etapa (415 / 416) | Horário / Âncora | Chars | Padrão | URLs / Links Detectados | Status de Envio Real |
|---|---|---|---:|:---:|---|---|
| 2 horas | 667 / 686 | T+2h relativo | 141 | GSM-7 | https://wa.me/5548996553954 | 415: 7 entregues; 416: 4 entregues |
| D+1 | 668 / 687 | D+1 @ 12:30 | 130 | GSM-7 | https://fotosdenatal.com | 415: 6 entregues; 416: 3 entregues |
| D+2 | 669 / 688 | D+2 @ 19:30 | 114 | GSM-7 | https://fotosdenatal.com (contém `<3` ASCII) | 415: 3 entregues |
| D+3 | 670 / 689 | D+3 @ 09:45 | 132 | GSM-7 | https://instagram.com/estudioevydenciaa | 415: 1 entregue |
| D+4 | 671 / 690 | D+4 @ 13:30 | 120 | GSM-7 | https://fotosdenatal.com | 415: 1 enviado |
| D+5 | 672 / 691 | D+5 @ 18:45 | 125 | GSM-7 | https://wa.me/5548996553954 | Pendente de cronograma |
| D+10 | 673 / 692 | D+10 @ 10:15 | 118 | GSM-7 | https://wa.me/5548996553954 | Pendente de cronograma |
| D+15 | 674 / 693 | D+15 @ 19:15 | 120 | GSM-7 | https://fotosdenatal.com (contém `<3` ASCII) | Pendente de cronograma |
| D+20 | 675 / 694 | D+20 @ 12:45 | 125 | GSM-7 | https://wa.me/5548996553954 | Pendente de cronograma |

**Telefones nos Links de SMS:**
- Links de atendimento e reservas nos SMS V4 apontam para `https://wa.me/5548996553954` (número comercial da equipe de vendas).
- O número institucional de opt-out/parar nos e-mails e catálogo histórico R3 aponta para `https://wa.me/554898483594?text=PARARNATAL2026`.
- Os textos com `<3` utilizam caracteres ASCII menores-que e três, permanecendo dentro da codificação GSM-7 (1 segmento SMS, custo padrão).

---

## 5. Auditoria de E-mail

- **E-mail Inicial V4 (Etapa 657 na 415 e 676 na 416):** Remetente `propostas@fotosdenatal.com`, Provedor `resend`, Formato `text`. Assunto: `"Sua proposta Natal + Ano Novo 2026 — Evydência"`.
- **Entrega Real:** 3 e-mails entregues com sucesso na Campanha 415 via Resend.
- **Modelos Históricos R3:** Etapas 541 (415) e 563 (416) pertenceram à revisão R3 anterior. Não foram registradas ações de e-mail ativas adicionais para D+3 ou D+5 no manifesto V4 aprovado.

---

## 6. Realidade Operacional da Telefonia SmartVoice

- **No Banco de Dados:** Existem 10 etapas cadastradas (647–651 na 415 e 652–656 na 416) com ações ativas vinculadas aos flow IDs `natal2026-proposta-{1h,1d,5d,7d,10d}`.
- **No Registry do SmartDialer (`smartdialer-platform/config/voice_flows/registry.json`):**
  - Todos os 5 fluxos estão explicitamente configurados com **`"enabled": false`**.
- **Implantação não demonstrada:** O registry versionado do discador foi observado com `enabled:false`, mas **não equivale** ao registry efetivo. A informação sobre WAV implantado e execução Asterisk em host diferente requer prova do ambiente real; Asterisk não precisa rodar no host do CRM.
- **Confronto obrigatório:** O relatório `timeline.md` apresenta eventos `voice/smartvoice (delivered: 2)` nas etapas 647 e 648, incompatíveis com a afirmação anterior de “zero registros”. O que `delivered` representa deve ser confrontado com provider ACK, CDR/answered, número/canal e timestamp; sem essa prova não confirmar telefonema atendido nem negar todos os envios.
- **Conclusão revisada:** `VOICE_STATUS_INDETERMINADO`; preservar configurações e validar registry efetivo, WAVs e ledger, sem ativar ou disparar.

---

## 7. Governança e Plano de Ação Seguro

1. **Não tentar resolver as 12 execuções pendentes observadas nesta extração apenas editando o catálogo.** O snapshot imutável em `campaign_contact_stage_runs.stage_snapshot` continuará entregando `Comprovante_de_Transferencia Pix_01112026_200921.pdf`.
2. **Procedimento Recomendado para D+10:** Reconsultar o estado atual, identificar somente os runs ainda elegíveis e, com autorização operacional, **suprimir/cancelar o envio do anexo específico com trilha auditável, idempotência e reversibilidade**. Evitar reescrever silenciosamente snapshots, trocar apenas nome do PDF ou disparar outra peça que conserve a aparência de Pix. Confirmar fuso e próximo horário real.
3. **Melhoria Editorial dos Vídeos:** Revisar as legendas no draft canônico e publicar somente por procedimento versionado aprovado com `configuration_hash`, snapshots e rollback; editar mensagem do catálogo isoladamente **não garante** adoção nos próximos envios e pode comprometer prontidão.

**Auditoria concluída em modo AUDIT_ONLY. Nenhuma alteração foi realizada em produção.**
