# Auditoria e Análise Técnica Aprofundada — Campanhas de Natal 2026 (415 e 416)

> **Documento Canônico de Referência e Auditoria**  
> **Status:** AUDITORIA CONCLUÍDA  
> **Data:** 25 de Outubro de 2026  
> **Fonte Primária dos Fatos:** `PRODUCAO_DB_READ_ONLY` (Host: `evydencia-prod` / `185.241.151.207`, Banco: `evyd25`, Transação REPEATABLE READ READ-ONLY)  
> **Manifesto Ativo:** `natal2026-content-v4-20261021` (Content Profile v4, Status `approved`, Hash: `05528208bbcb94c3d3b2c9b0d8069fe3c8f883626bc3a35c510506aea5fc6cce`)  
> **Repositório Independente:** `hypeneural/propostanatal`  

---

## 1. Visão Geral da Arquitetura e Escopo

A comunicação comercial da temporada de **Natal 2026** do Estúdio Evydência é orquestrada através do fluxo de proposta `natal` (`proposal_flows.slug = natal`), bifurcado em duas campanhas isoladas:
1. **Campanha 415:** `Proposta Natal 2026 — com nome` (para clientes com primeiro nome identificado).
2. **Campanha 416:** `Proposta Natal 2026 — sem nome` (para leads/clientes sem primeiro nome válido).

### Contagem de Etapas e Elementos em Produção
- **Total de etapas cadastradas:** 89 etapas no banco de dados.
- **Catálogo Ativo V4 (`catalog_current`):** **38 etapas** (19 na 415 + 19 na 416), cobrindo a cadência integral de T0 até D+20.
- **Catálogo Histórico Retido (`catalog_retained`):** **12 etapas** (6 na 415 + 6 na 416) da revisão R2/R3.
- **Série Complementar SmartVoice (`smartvoice_series`):** **10 etapas** ativas (647–651 na 415 e 652–656 na 416), ativadas via `SMARTVOICE_NATAL_SERIES_ENABLED=true`.
- **Etapas Legadas Inativas (`legacy_inactive`):** **29 etapas** (19 na 415 e 10 na 416, prefixos N25CN/N25SN com zero mensagens e zero ações).
- **Mensagens ativas:** 54 mensagens cadastradas na tabela `messages`.
- **Ações ativas:** 34 ações em `campaign_stage_actions` (e-mail, SMS e chamadas de voz).

---

## 2. Catálogo Oficial de Mídias e URLs Absolutas

No banco de dados MySQL (`tabela messages`), a coluna `url` armazena o identificador de arquivo na nuvem (S3). O código do CRM em `CampaignMessageService` e `WhatsappResourceController` resolve essas mídias através das rotas canônicas servidas via CDN oficial (`https://evydencia.com/resources/...`).

Todas as URLs abaixo foram testadas via sonda HTTP contra a CDN/Cloudflare, retornando **`HTTP 200 OK`**:

| Papel no Fluxo | Tipo | ID Msg (415 / 416) | Nome do Arquivo no DB | URL Absoluta Canônica | Tamanho / MIME | Status HTTP |
|---|---|:---:|---|---|:---:|:---:|
| **Cenários & Pacotes (T0)** | `image` | 1446 / 1462 | `natal-2026-pacotes-r1.png` | `https://evydencia.com/resources/image/natal-2026-pacotes-r1.png` | 2,7 MB (`image/png`) | **200 OK** |
| **Proposta Completa (T0)** | `attachment` | 1447 / 1463 | `natal2026-rev357-v1-proposta.pdf` | `https://evydencia.com/resources/attachment/natal2026-rev357-v1-proposta.pdf` | 9,8 MB (`application/pdf`) | **200 OK** |
| **Vídeo 1h (T+60m)** | `video` | 1448 / 1464 | `natal-2026-1hora-r3.mp4` | `https://evydencia.com/resources/video/natal-2026-1hora-r3.mp4` | MP4 (`video/mp4`) | **200 OK** |
| **Vídeo 90m (T+90m)** | `video` | 1450 / 1466 | `natal-2026-90min-r3.mp4` | `https://evydencia.com/resources/video/natal-2026-90min-r3.mp4` | MP4 (`video/mp4`) | **200 OK** |
| **Vídeo 2 dias (D+2)** | `video` | 1452 / 1468 | `natal-2026-2dias-r3.mp4` | `https://evydencia.com/resources/video/natal-2026-2dias-r3.mp4` | MP4 (`video/mp4`) | **200 OK** |
| **Vídeo 4 dias (D+4)** | `video` | 1454 / 1470 | `natal-2026-4dias-r3.mp4` | `https://evydencia.com/resources/video/natal-2026-4dias-r3.mp4` | MP4 (`video/mp4`) | **200 OK** |
| **Vídeo D+1 (D+1)** | `video` | 1456 / 1472 | `natal2026-rev357-v1-d01.mp4` | `https://evydencia.com/resources/video/natal2026-rev357-v1-d01.mp4` | 15,2 MB (`video/mp4`) | **200 OK** |
| **Vídeo Jogos (D+3)** | `video` | 1457 / 1473 | `natal2026-rev357-v1-jogos.mp4` | `https://evydencia.com/resources/video/natal2026-rev357-v1-jogos.mp4` | MP4 (`video/mp4`) | **200 OK** |
| **Vídeo D+5 (D+5)** | `video` | 1458 / 1474 | `natal2026-rev357-v1-d05.mp4` | `https://evydencia.com/resources/video/natal2026-rev357-v1-d05.mp4` | MP4 (`video/mp4`) | **200 OK** |
| **Vídeo D+10 (D+10)** | `video` | 1459 / 1475 | `natal2026-rev357-v1-d10.mp4` | `https://evydencia.com/resources/video/natal2026-rev357-v1-d10.mp4` | MP4 (`video/mp4`) | **200 OK** |
| **Documento D+10 (D+10)** | `attachment` | 1460 / 1476 | `natal2026-rev357-v1-d10-document.pdf` | `https://evydencia.com/resources/attachment/natal2026-rev357-v1-d10-document.pdf` | 87,5 KB (`application/pdf`) | **200 OK** |
| **PDF Proposta R2/R3 (Hist)** | `attachment` | 1425 / 1436 | `natal-2026-documento-demonstracao.pdf` | `https://evydencia.com/resources/attachment/natal-2026-documento-demonstracao.pdf` | PDF (`application/pdf`) | **200 OK** |

---

## 3. Comprovação Técnica do Envio de Legendas (Captions)

### Como o CRM Envia Mídias para a Z-API (`CampaignMessageService.php`)
A auditoria minuciosa do código em `/var/www/evydencia/current/app/Services/Messages/CampaignMessageService.php` comprova a regra canônica de serialização do WhatsApp:

```php
} else if ($this->message->type->is(WhatsappMessageType::video)) {
    $this->sendVideo($this->makeUrl('whatsapp.resource.video'), $this->message->title, $this->message->onceview, $delay);
} else if ($this->message->type->is(WhatsappMessageType::attachment)) {
    $this->sendAttachment(route('whatsapp.resource.attach', $this->message->url), $this->message->title, $delay);
} else if ($this->message->type->is(WhatsappMessageType::image)) {
    $this->sendImage($this->makeUrl('whatsapp.resource.image'), $this->message->title, $this->message->onceview, $delay);
```

### Conclusões Invariantes:
1. **Vídeo, Imagem e PDF usam `$message->title` como Legenda:** A Z-API recebe o segundo argumento de `sendVideo` e `sendAttachment` como legenda. Esse argumento é estritamente **`$this->message->title`**.
2. **O campo `description` é ignorado para mídias:** É utilizado apenas em mensagens de `link` (preview de URL) e `action` (footer/botões).
3. **Evidência dos Vídeos D+1, D+3, D+5 e D+10:** 
   - No banco de dados, o campo `title` das mensagens de vídeo armazena:
     - D+1 (`Msg 1456 / 1472`): `title = "Natal 2026 - D+1"`
     - D+3 (`Msg 1457 / 1473`): `title = "Jogos personalizados"`
     - D+5 (`Msg 1458 / 1474`): `title = "Natal 2026 - D+5"`
     - D+10 (`Msg 1459 / 1475`): `title = "Natal 2026 - D+10"`
   - Como o serializer passa `title` como caption, **o cliente final recebe o vídeo no WhatsApp com essa legenda técnica**.
   - **Comprovação Factual:** Na tabela `campaign_contact_stage_runs`, já existem **4 disparos concluídos na 415** e **3 disparos concluídos na 416** com essa legenda enviada com sucesso ao cliente.

---

## 4. Achado Crítico: O Anexo PDF de D+10 (`Stage 666` e `Stage 685`)

> [!WARNING]
> **Anomalia de Título no PDF de D+10:**
> O documento cadastrado para D+10 (`Msg 1460` na 415 e `Msg 1476` na 416) possui no banco de dados o seguinte título:
> **`Comprovante_de_Transferencia Pix_01112026_200921`**
> 
> Como o CRM utiliza o campo `title` como nome do arquivo/legenda ao chamar `sendAttachment`, o cliente final receberá no WhatsApp um documento PDF nomeado como comprovante de transferência Pix.
> 
> **Status dos Clientes em Produção:**
> - Existem **7 clientes na Campanha 415** e **4 clientes na Campanha 416** atualmente pendentes (`status: pending`) aguardando o alcance da janela D+10.
> - **Nenhum cliente recebeu ainda essa etapa.** Há uma janela de oportunidade imediata para corrigir esse título no banco ou painel antes que o cronograma atinja D+10.

---

## 5. Matriz Comparativa Completa: Campanha 415 vs 416

A auditoria cruzada etapa a etapa demonstra que **a única divergência entre as duas campanhas reside na Etapa 01 (Inicial)**:

| # | Papel | Cronograma / Âncora | Campanha 415 (Com Nome) | Campanha 416 (Sem Nome) | Divergência |
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

## 6. Governança Operacional: Painel Web vs Código / Deploy

| Mudança Desejada | Local de Execução | Requer Deploy? | Efeito nas Execuções Ativas |
|---|---|:---:|---|
| **Correção do Título do PDF D+10** | Tabela `messages` (via Painel Web `/gestao/campanhas/{id}/fluxo`) | **NÃO** | Imediato para os 11 clientes pendentes |
| **Substituição dos Títulos dos Vídeos (D+1, D+3, D+5, D+10)** | Tabela `messages` (via Painel Web) | **NÃO** | Imediato para os próximos disparos |
| **Ajustes nos Textos de WhatsApp / SMS / E-mail** | Tabelas `messages` e `campaign_stage_actions` (Painel Web) | **NÃO** | Imediato para os próximos disparos |
| **Inclusão de Novos Tokens de Contagem Regressiva** | `MessageVariableService.php` | **SIM** | Exige commit e deploy no repositório `evydencia-crm` |
| **Alteração do Serializador de Mídias (`CampaignMessageService.php`)** | `CampaignMessageService.php` | **SIM** | Exige commit e deploy no repositório `evydencia-crm` |
| **Criação ou Exclusão Estrutural de Etapas** | Manifesto V5 + `Natal2026ProposalManifest.php` | **SIM** | Exige provisionamento e migração de manifesto |

---

## 7. Recomendações e Próximos Passos (Sem Ações Produtivas Nesta Fase)

1. **Ação P0 Recomendada:** Corrigir pelo painel administrativo o título da mensagem 1460 (Campanha 415) e da mensagem 1476 (Campanha 416) para `Proposta Fotos de Natal 2026 — Evydência`, evitando o envio do comprovante Pix no D+10.
2. **Ação P1 Recomendada:** Substituir os títulos dos vídeos D+1, D+3, D+5 e D+10 no painel por legendas de engajamento emocional (ex: *"A magia do Natal te espera ✨"*).
3. **Ação P2 Recomendada:** Avaliar se tokens de contagem regressiva (como `{natal_countdown_dias_descricao}`) devem ser inseridos nos textos de SMS ou WhatsApp para reforçar urgência a partir de D+5.
