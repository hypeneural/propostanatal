# Auditoria Forense e Análise Técnica Aprofundada — Campanhas de Natal 2026 (415 e 416)

> **Documento Canônico de Referência e Auditoria Multicanal**  
> **Status:** AUDITORIA FORENSE CONCLUÍDA (Base real de produção comprovada)  
> **Data:** 26 de Outubro de 2026  
> **Origem dos Fatos:** `PRODUCAO_DB_READ_ONLY` (Conexão MySQL autorizada, Host e banco omitidos por segurança (artefatos de conexão somente locais), Transação SNAPSHOT READ-ONLY)  
> **Manifesto Ativo:** `natal2026-content-v4-20261021` (Content Profile v4, Status: `approved`, Hash: `05528208bbcb94c3d3b2c9b0d8069fe3c8f883626bc3a35c510506aea5fc6cce`)  
> **Repositório Independente:** `hypeneural/propostanatal`  
> **Modo Operacional:** AUDIT_ONLY (Sem DML, sem deploy, sem disparos, sem modificações produtivas)

---

## 1. Visão Geral da Arquitetura e Escopo Reconciliado

A régua de relacionamento comercial da temporada de **Natal 2026** do Estúdio Evydência é orquestrada através do fluxo de proposta `natal` (`proposal_flows.slug = 'natal'`), bifurcado em duas campanhas isoladas:
1. **Campanha 415:** `Proposta Natal 2026 — com nome` (leads/clientes com primeiro nome identificado).
2. **Campanha 416:** `Proposta Natal 2026 — sem nome` (leads/clientes sem primeiro nome válido).

### Reconciliação do Catálogo no Banco de Dados
A extração forense direta do MySQL identifica **89 etapas cadastradas**, rigorosamente classificadas nas seguintes categorias:

| Classe do Catálogo | Cadastradas | Ativas (`is_active=1`) | Descrição e Papel Operacional |
|---|---:|---:|---|
| **`catalog_current`** | **38** | **38** | **Catálogo V4 Ativo:** 19 etapas na 415 e 19 etapas na 416 (T0 até D+20). |
| **`catalog_retained`** | **12** | **12** | **Catálogo Histórico Retido:** 6 etapas na 415 e 6 na 416, preservadas da revisão R2/R3 anterior. |
| **`smartvoice_series`** | **10** | **10** | **Série SmartVoice:** 5 etapas na 415 (647–651) e 5 na 416 (652–656), vinculadas a ações VoIP. |
| **`legacy_inactive`** | **29** | **0** | **Etapas Legadas Inativas:** 19 na 415 e 10 na 416 (prefixos N25CN/N25SN com zero mensagens e zero ações). |
| **Total Geral** | **89** | **60** | **54 mensagens cadastradas e 34 ações ativas.** |

---

## 2. Catálogo Oficial de Mídias e URLs Absolutas

No banco de dados (`messages.url`), armazena-se a chave do arquivo no S3. O CRM (`CampaignMessageService` e `WhatsappResourceController`) serve essas mídias através da CDN oficial Cloudflare.

Todas as URLs foram auditadas diretamente contra a CDN, confirmando integridade e acessibilidade:

| Papel no Fluxo | Tipo | ID Msg (415 / 416) | Nome do Arquivo no DB | URL Absoluta Canônica | MIME / Tamanho | Status HTTP |
|---|---|:---:|---|---|:---:|:---:|
| **Cenários & Pacotes (T0)** | `image` | 1446 / 1462 | `natal-2026-pacotes-r1.png` | `https://evydencia.com/resources/image/natal-2026-pacotes-r1.png` | `image/png` (2,7 MB) | **200 OK** |
| **Proposta Completa (T0)** | `attachment` | 1447 / 1463 | `natal2026-rev357-v1-proposta.pdf` | `https://evydencia.com/resources/attachment/natal2026-rev357-v1-proposta.pdf` | `application/pdf` (9,8 MB) | **200 OK** |
| **Vídeo 1h (T+60m)** | `video` | 1448 / 1464 | `natal-2026-1hora-r3.mp4` | `https://evydencia.com/resources/video/natal-2026-1hora-r3.mp4` | `video/mp4` | **200 OK** |
| **Vídeo 90m (T+90m)** | `video` | 1450 / 1466 | `natal-2026-90min-r3.mp4` | `https://evydencia.com/resources/video/natal-2026-90min-r3.mp4` | `video/mp4` | **200 OK** |
| **Vídeo 2 dias (D+2)** | `video` | 1452 / 1468 | `natal-2026-2dias-r3.mp4` | `https://evydencia.com/resources/video/natal-2026-2dias-r3.mp4` | `video/mp4` | **200 OK** |
| **Vídeo 4 dias (D+4)** | `video` | 1454 / 1470 | `natal-2026-4dias-r3.mp4` | `https://evydencia.com/resources/video/natal-2026-4dias-r3.mp4` | `video/mp4` | **200 OK** |
| **Vídeo D+1 (D+1)** | `video` | 1456 / 1472 | `natal2026-rev357-v1-d01.mp4` | `https://evydencia.com/resources/video/natal2026-rev357-v1-d01.mp4` | `video/mp4` (15,2 MB) | **200 OK** |
| **Vídeo Jogos (D+3)** | `video` | 1457 / 1473 | `natal2026-rev357-v1-jogos.mp4` | `https://evydencia.com/resources/video/natal2026-rev357-v1-jogos.mp4` | `video/mp4` | **200 OK** |
| **Vídeo D+5 (D+5)** | `video` | 1458 / 1474 | `natal2026-rev357-v1-d05.mp4` | `https://evydencia.com/resources/video/natal2026-rev357-v1-d05.mp4` | `video/mp4` | **200 OK** |
| **Vídeo D+10 (D+10)** | `video` | 1459 / 1475 | `natal2026-rev357-v1-d10.mp4` | `https://evydencia.com/resources/video/natal2026-rev357-v1-d10.mp4` | `video/mp4` | **200 OK** |
| **Documento D+10 (D+10)** | `attachment` | 1460 / 1476 | `natal2026-rev357-v1-d10-document.pdf` | `https://evydencia.com/resources/attachment/natal2026-rev357-v1-d10-document.pdf` | `application/pdf` (87,5 KB) | **200 OK** |
| **PDF Proposta R2/R3 (Hist)** | `attachment` | 1425 / 1436 | `natal-2026-documento-demonstracao.pdf` | `https://evydencia.com/resources/attachment/natal-2026-documento-demonstracao.pdf` | `application/pdf` | **200 OK** |

---

## 3. Achado Crítico P0: PDF D+10 e a Imutabilidade dos Snapshots Congelados

### 3.1 Inspeção do Conteúdo Binário do PDF D+10
Foi realizada a extração e auditoria forense do binário `natal2026-rev357-v1-d10-document.pdf` (87.583 bytes):
- **Gerador:** ReportLab PDF Library v1.4.
- **Estrutura:** O PDF encapsula um fluxo ASCII85 contendo uma imagem JPEG de alta resolução (600 × 1026 pixels, 24-bit RGB).
- **Conteúdo Visual:**
  - O cabeçalho gráfico simula um comprovante de transferência bancária via PIX ("Pagamento via PIX", "Transação: 6842715", "Destinatário"). **Não há nenhum dado pessoal (PII), valor real ou conta bancária vazada.**
  - O corpo principal exibe o **Papai Noel na Vila de Natal do Estúdio Evydência** com o texto:  
    *"Você ainda não agendou suas Fotos de Natal no Estúdio Evydência! O estúdio de Natal mais completo do Sul do Brasil. Garanta seu horário >"*.
  - **Natureza do Arquivo:** Trata-se de uma peça de marketing com gancho de curiosidade (clickbait) para reengajar contatos que esfriaram após 10 dias de cadência.

### 3.2 O Mecanismo de Envio no WhatsApp (`CampaignMessageService.php`)
A auditoria do código-fonte em `/var/www/evydencia/current/app/Services/Messages/CampaignMessageService.php` comprova a regra técnica de serialização:
```php
} else if ($this->message->type->is(WhatsappMessageType::attachment)) {
    $this->sendAttachment(route('whatsapp.resource.attach', $this->message->url), $this->message->title, $delay);
}
```
O método `sendAttachment` repassa `$this->message->title` como segundo argumento, que na classe `WhatsappPayload::attachment` é mapeado exclusivamente para a propriedade **`fileName`**.
Portanto, o WhatsApp do cliente final exibe o documento com o nome:
> 📄 **`Comprovante_de_Transferencia Pix_01112026_200921.pdf`**

**Risco Comercial e Operacional:** Embora o conteúdo gráfico interno seja lúdico, o nome do arquivo recebido simula um comprovante financeiro inesperado, com elevado risco de o cliente considerar fraude ou spam e denunciar/bloquear o número do estúdio na Z-API.

### 3.3 A Imutabilidade dos Snapshots Congelados (`stage_snapshot`)
Ao agendar uma proposta, o CRM executa `NatalProposalExecutionPlan::captureNew`, gravando um snapshot congelado das mensagens na coluna `stage_snapshot` da tabela `campaign_contact_stage_runs`:
```php
if (array_key_exists('legacy_messages', $run->stage_snapshot)) {
    return $run->stage_snapshot['legacy_messages'];
}
```
Durante o disparo, `CampaignPolicyLegacyMessages::hydrate` reconstrói a mensagem **a partir dos dados congelados no snapshot**, ignorando a tabela `messages`.

### 3.4 Censo Factual das Execuções em Produção
A consulta forense em `campaign_contact_stage_runs` revela:

| Etapa | Campanha | Status | Título Congelado (`stage_snapshot`) | Quantidade | Primeiro Disparo Agendado |
|:---:|:---:|:---:|---|---:|:---:|
| **Stage 666** | 415 (Com Nome) | `pending` | `Comprovante_de_Transferencia Pix_01112026_200921` | **7** | **2026-10-31 19:29:04** |
| **Stage 666** | 415 (Com Nome) | `skipped` | `Comprovante_de_Transferencia Pix_01112026_200921` | 1 | 2026-11-04 16:09:05 |
| **Stage 685** | 416 (Sem Nome) | `pending` | `Comprovante_de_Transferencia Pix_01112026_200921` | **4** | **2026-11-03 09:32:06** |

- **Total de Execuções Pendentes:** Exatamente **11 contatos** (7 na 415 e 4 na 416).
- **Recibos / Delivery Items:** **Zero** envios realizados até o momento (nenhum cliente recebeu ainda).
- **Conclusão Forense:** **Alterar o campo `messages.title` no banco ou via Painel Web afetará apenas novos clientes gerados após a edição.** Os 11 contatos já agendados continuarão recebendo o título antigo congelado, a menos que uma rotina de reconciliação autorizada no CRM atualize seus snapshots antes de 31/10/2026 19:29:04.

---

## 4. Auditoria Técnica das Legendas de Vídeos e Imagens

### 4.1 Regra Canônica de Serialização
Em `CampaignMessageService.php`:
- Para `video` e `image`, o CRM repassa `$message->title` como parâmetro de **`caption`** na Z-API.
- O campo `description` é **ignorado** pelo serializador de mídia (utilizado apenas para previews de link e botões de ação).

### 4.2 Títulos Cadastrados no Banco
| Momento | Mensagem (415 / 416) | Tipo | Título no Banco (`caption` enviada) |
|---|:---:|:---:|---|
| **D+1** | 1456 / 1472 | `video` | `Natal 2026 - D+1` |
| **D+3** | 1457 / 1473 | `video` | `Jogos personalizados` |
| **D+5** | 1458 / 1474 | `video` | `Natal 2026 - D+5` |
| **D+10** | 1459 / 1475 | `video` | `Natal 2026 - D+10` |

### 4.3 Comprovação de Entrega Real (`campaign_stage_action_delivery_items`)
A auditoria dos itens de entrega na Z-API comprova que contatos em produção já receberam esses vídeos com as legendas técnicas:
- **Etapa 662 (D+1, Camp 415):** 4 clientes entregues (`delivered`), 2 clientes leram (`read`).
- **Etapa 681 (D+1, Camp 416):** 3 clientes leram (`read`).

---

## 5. Auditoria Integral dos Nove SMS (Velip)

A cadência V4 define 9 disparos SMS por campanha, via integração HTTP Velip:

| # | Etapa (415 / 416) | Horário / Âncora | Chars | Padrão | Link / Telefone no Texto | Status Factual de Entrega |
|---|---|---|---:|:---:|---|---|
| **01** | Stage 667 / 686 | T+2h relativo | 141 | GSM-7 | `https://wa.me/5548996553954` | 415: 7 entregues; 416: 4 entregues |
| **02** | Stage 668 / 687 | D+1 @ 12:30 | 130 | GSM-7 | `https://fotosdenatal.com` | 415: 6 entregues; 416: 3 entregues |
| **03** | Stage 669 / 688 | D+2 @ 19:30 | 114 | GSM-7 | `https://fotosdenatal.com` (contém `<3` ASCII) | 415: 3 entregues |
| **04** | Stage 670 / 689 | D+3 @ 09:45 | 132 | GSM-7 | `https://instagram.com/estudioevydenciaa` | 415: 1 entregue |
| **05** | Stage 671 / 690 | D+4 @ 13:30 | 120 | GSM-7 | `https://fotosdenatal.com` | 415: 1 enviado |
| **06** | Stage 672 / 691 | D+5 @ 18:45 | 125 | GSM-7 | `https://wa.me/5548996553954` | Aguardando cronograma |
| **07** | Stage 673 / 692 | D+10 @ 10:15 | 118 | GSM-7 | `https://wa.me/5548996553954` | Aguardando cronograma |
| **08** | Stage 674 / 693 | D+15 @ 19:15 | 120 | GSM-7 | `https://fotosdenatal.com` (contém `<3` ASCII) | Aguardando cronograma |
| **09** | Stage 675 / 694 | D+20 @ 12:45 | 125 | GSM-7 | `https://wa.me/5548996553954` | Aguardando cronograma |

### Achados Técnicos de SMS:
1. **Conformidade GSM-7:** Todos os 9 textos contêm menos de 160 caracteres e utilizam exclusivamente a tabela padrão GSM-7. Os textos de D+2 e D+15 usam o emoticon ASCII `<3` (menor-que e três), que não converte a mensagem para Unicode (UCS-2), mantendo o faturamento em 1 único segmento por disparo.
2. **Número de WhatsApp nos Links:** Os links de contato nos SMS V4 apontam para `https://wa.me/5548996553954` (canal de vendas da equipe comercial). O número de opt-out/parar (`https://wa.me/554898483594?text=PARARNATAL2026`) é reservado para o e-mail e regras de descadastro.
3. **Condições de Envio:** Todas as ações SMS possuem `required = 0` e são abortadas caso o lead realize a compra (`skip_if_converted`).

---

## 6. Auditoria de E-mail (Resend)

- **E-mail Inicial V4 (Etapa 657 na 415 e 676 na 416):**
  - **Provedor:** Resend (`propostas@fotosdenatal.com`).
  - **Assunto:** `"Sua proposta Natal + Ano Novo 2026 — Evydência"`.
  - **Formato:** Texto estruturado (`body_mode: text`), com os valores dos pacotes (Ho-Ho-Ho: R$ 196; Então é Natal: R$ 480; Boas Festas: R$ 885) e links de reserva.
  - **Recibos de Entrega:** 3 e-mails entregues com sucesso via Resend na Campanha 415.
- **Modelos Históricos R3:** As etapas 541 (415) e 563 (416) eram da revisão R3 anterior. Na 416, 3 envios históricos falharam por supressão de e-mail inválido. O manifesto V4 ativo não inclui e-mails intermediários em D+3 ou D+5.

---

## 7. Realidade Operacional da Telefonia SmartVoice

- **Configuração no CRM:** O banco possui 10 etapas cadastradas (647–651 na 415 e 652–656 na 416), com ações de telefonia ativas vinculadas aos fluxos:
  - `natal2026-proposta-1h`, `natal2026-proposta-1d`, `natal2026-proposta-5d`, `natal2026-proposta-7d`, `natal2026-proposta-10d`.
- **Registry do SmartDialer (`smartdialer-platform/config/voice_flows/registry.json`):**
  - Todos os 5 fluxos estão com o status explícito **`"enabled": false`**.
- **Infraestrutura Telefônica:**
  - Os 5 arquivos WAV PCM homologados (`natal2026_proposta_{1h,1d,5d,7d,10d}.wav`) não foram carregados no PABX da produção.
  - Não há serviço Asterisk ativo na VPS do CRM.
- **Ledger de Chamadas em Produção:** A tabela `campaign_stage_action_delivery_items` registra **zero chamadas** para a série Natal 2026.
- **Conclusão:** A existência das etapas no CRM não representa chamadas efetuadas. A telefonia SmartVoice permaneceu inativa em produção.

---

## 8. Matriz Comparativa Completa: Campanha 415 vs 416

A cadência editorial V4 possui 19 etapas por variante:

| # | Papel | Cronograma / Âncora | Campanha 415 (Com Nome) | Campanha 416 (Sem Nome) | Divergência Factual |
|---|---|---|---|---|---|
| **01** | `initial` | 0 min / inicial | Stage 657 / Msg 1445, 1446, 1447 / Ação 24 | Stage 676 / Msg 1461, 1462, 1463 / Ação 34 | `intro_cta` e E-mail usam `{primeiro_nome}` na 415; texto impessoal na 416. Imagem de pacotes e PDF inclusos. |
| **02** | `whatsapp_h1` | 60 min / relativo | Stage 658 / Msg 1448, 1449 | Stage 677 / Msg 1464, 1465 | Idênticos (Vídeo 1h + Botões) |
| **03** | `whatsapp_90min` | 90 min / relativo | Stage 659 / Msg 1450, 1451 | Stage 678 / Msg 1466, 1467 | Idênticos (Vídeo 90m + Botões) |
| **04** | `sms_h2` | 2 horas / relativo | Stage 667 / Ação 25 | Stage 686 / Ação 35 | Idênticos (SMS Velip) |
| **05** | `whatsapp_d1` | D+1 / relativo | Stage 662 / Msg 1456 | Stage 681 / Msg 1472 | Idênticos (Vídeo D+1, Title: `Natal 2026 - D+1`) |
| **06** | `sms_d1` | D+1 @ 12:30 | Stage 668 / Ação 26 | Stage 687 / Ação 36 | Idênticos (SMS Velip) |
| **07** | `whatsapp_d2` | D+2 / relativo | Stage 660 / Msg 1452, 1453 | Stage 679 / Msg 1468, 1469 | Idênticos (Vídeo 2d + Botões) |
| **08** | `sms_d2` | D+2 @ 19:30 | Stage 669 / Ação 27 | Stage 688 / Ação 37 | Idênticos (SMS Velip) |
| **09** | `whatsapp_d3` | D+3 / relativo | Stage 663 / Msg 1457 | Stage 682 / Msg 1473 | Idênticos (Vídeo D+3, Title: `Jogos personalizados`) |
| **10** | `sms_d3` | D+3 @ 09:45 | Stage 670 / Ação 28 | Stage 689 / Ação 38 | Idênticos (SMS Velip) |
| **11** | `whatsapp_d4` | D+4 / relativo | Stage 661 / Msg 1454, 1455 | Stage 680 / Msg 1470, 1471 | Idênticos (Vídeo 4d + Botões) |
| **12** | `sms_d4` | D+4 @ 13:30 | Stage 671 / Ação 29 | Stage 690 / Ação 39 | Idênticos (SMS Velip) |
| **13** | `whatsapp_d5` | D+5 / relativo | Stage 664 / Msg 1458 | Stage 683 / Msg 1474 | Idênticos (Vídeo D+5, Title: `Natal 2026 - D+5`) |
| **14** | `sms_d5` | D+5 @ 18:45 | Stage 672 / Ação 30 | Stage 691 / Ação 40 | Idênticos (SMS Velip) |
| **15** | `whatsapp_d10`| D+10 / relativo | Stage 665 / Msg 1459 | Stage 684 / Msg 1475 | Idênticos (Vídeo D+10, Title: `Natal 2026 - D+10`) |
| **16** | `document_d10`| D+10 / relativo | Stage 666 / Msg 1460 | Stage 685 / Msg 1476 | Idênticos (PDF D+10, Title: `Comprovante_de_Transferencia Pix_01112026_200921`) |
| **17** | `sms_d10` | D+10 @ 10:15 | Stage 673 / Ação 31 | Stage 692 / Ação 41 | Idênticos (SMS Velip) |
| **18** | `sms_d15` | D+15 @ 19:15 | Stage 674 / Ação 32 | Stage 693 / Ação 42 | Idênticos (SMS Velip) |
| **19** | `sms_d20` | D+20 @ 12:45 | Stage 675 / Ação 33 | Stage 694 / Ação 43 | Idênticos (SMS Velip) |

---

## 9. Governança e Matriz de Ação Segura

| Intervenção Desejada | Local de Execução | Requer Deploy? | Efeito nas Execuções Pendentes |
|---|---|:---:|---|
| **Correção do Título do Catálogo D+10** | Tabela `messages` (via Painel Web) | **NÃO** | **Nenhum efeito** nos 11 pendentes. Afeta apenas novas propostas geradas. |
| **Remediação dos 11 Agendamentos D+10** | Rotina de Reconciliação / Comando Artisan | **SIM** | Atualiza os `stage_snapshot` dos 11 runs pendentes antes de 31/10/2026. |
| **Ajuste de Títulos dos Vídeos (D+1, D+3, D+5, D+10)** | Tabela `messages` (via Painel Web) | **NÃO** | Imediato para os próximos disparos não congelados. |
| **Tokens de Contagem Regressiva** | `MessageVariableService.php` | **SIM** | Tokens como `{natal_countdown_dias_descricao}` já existem no código PHP; novos tokens requerem deploy. |
| **Ativação da Telefonia SmartVoice** | `registry.json` + PABX Asterisk | **SIM** | Exige provisionamento dos arquivos WAV, habilitação do fluxo e deploy no discador. |

---

## 10. Recomendações Técnicas Priorizadas (P0 a P2)

1. **P0 — Reconciliação dos 11 Snapshots D+10:** Planejar comando artisan ou migração de ciclo de vida no CRM para higienizar o título do arquivo no `stage_snapshot` dos 11 agendamentos pendentes antes do primeiro disparo em 31/10/2026 19:29:04, alterando para `Proposta Fotos de Natal 2026 — Evydência`.
2. **P1 — Legendas Atrativas nos Vídeos:** Atualizar via painel administrativo os títulos das mensagens de vídeo (1456, 1457, 1458, 1459 na 415 e equivalentes na 416) para legendas comerciais afetivas em vez dos identificadores técnicos.
3. **P2 — Manutenção da Telefonia Desabilitada:** Manter os fluxos SmartVoice desligados (`enabled: false`) até que os arquivos WAV estejam devidamente homologados no servidor Asterisk dedicado.
