> **DIFERENCIAL HISTÓRICO / NÃO É PATCH APLICÁVEL:** Confronto relativo ao inventário de 26/10/2026, 12:34:30. Os IDs devem ser reconciliados novamente antes de qualquer publicação, e o rascunho JSON mudou em D+1. Usar `../14-COPY-CANONICA-V5-CANDIDATA.md` para a copy atual. Os snapshots legados podem manter payload antigo; NÃO usar este arquivo como script de UPDATE.

# V5-DIFF-AUDITADO — Confronto Cirúrgico V4 Real × V5 Proposta

**Data da Auditoria:** 26/10/2026  
**Ambiente:** Somente Leitura (`AUDIT_ONLY`)  
**Inventário DB de Referência:** `reports/20261026-123430/inventory.json`  
**Manifesto Git de Referência:** `manifesto-natal-2026.v4.json` (revisão `natal2026-content-v4-20261021`, schema 4)  
**Hash de Configuração do DB:** `f64e93402d086abdfd0c678ff173ee1891cefe50f9ed3c381db0102a0ad5c055`  
**Veredito de Reconciliação:** `CONSISTENT_FOR_EDITORIAL_REVIEW_ONLY` (Exit code 0 em `reconcile_v4_local.py`)

---

## 1. Resumo Executivo e Mapeamento Canônico de IDs

A reconciliação direta comprovou a existência de exatamente 38 etapas selecionadas e 32 mensagens canônicas ativas nas campanhas 415 (Com Nome) e 416 (Sem Nome).

> [!NOTE]
> **Esclarecimento sobre Numeração de IDs:**  
> Os números `194x` relatados preliminarmente referem-se aos identificadores da tabela pivô `campaign_stage_messages.id`. Os identificadores canônicos da tabela `messages` são estritamente os intervalos `1445–1460` (Campanha 415) e `1461–1476` (Campanha 416). Abaixo, cada confronto utiliza o `message_id` canônico auditado no banco de produção.

---

## 2. Confronto Etapa por Etapa

### Etapa 01: Inicial T0 (`role: initial`)
- **Campanha 415 (Com Nome):** Etapa ID `657`, ordem `1`.
- **Campanha 416 (Sem Nome):** Etapa ID `676`, ordem `1`.
- **Âncora:** `immediate` (offset: 0 segundos), `policy: always_send`.

#### Mensagem de Apresentação (`role: intro_cta`)
- **ID Real DB:** `1445` (Camp. 415) / `1461` (Camp. 416) | **Tipo:** `action`
- **Campo:** `message` (ou `body` da mensagem)

| Versão | Conteúdo Exato |
| :--- | :--- |
| **V4 Real (415)** | `'''O Estúdio de fotos mais completo do Sul do Brasil!'''`<br><br>`Olá, {primeiro_nome}! Tudo bem? 🎄✨`<br>`As famílias que antecipam o ensaio de Natal garantem as melhores datas, horários nobres e condições especiais.`<br><br>`🎁 *Natal + Ano Novo em uma única sessão.*`<br>`São dois cenários encantadores: a magia acolhedora do Natal e o brilho renovador do Ano Novo.`<br><br>`🌟 Valores que cabem no bolso: a partir de *2x R$ 98*.`<br>`Você e sua família merecem registrar esse momento único!`<br><br>`Veja os detalhes dos pacotes e garanta a sua vaga antes que esgote!` |
| **V4 Real (416)** | *Idêntico ao 415, porém com saudação impessoal:* `Olá! Tudo bem? 🎄✨` |
| **V5 Draft (415)** | `Oii, *{primeiro_nome}*! 🥰🎄`<br>`A *Vila de Natal* da Evydência, em Tijucas, está linda!`<br><br>`✨ *Natal + Ano Novo em uma única sessão.*`<br>`E a magia continua em casa, com fotos reveladas e lembranças personalizadas.`<br><br>`*Desde 2011 em Tijucas*, registrando histórias de família. ❤️`<br><br>`Já vou te mandar os pacotes e o PDF completo. Começam em *2x R$ 98*.`<br><br>`Vocês preferem *semana ou fim de semana*?`<br><br>`_Para parar lembretes, responda PARARNATAL2026._` |
| **V5 Draft (416)** | *Idêntico ao 415, com abertura:* `Oii! 🥰🎄` |

- **Evidência:** `PRODUCAO_DB` + `EDITORIAL_V5_DRAFT`.
- **Análise de Risco & Ganhos:**
  - Elimina a poluição visual dos triple-backticks literais (`'''`).
  - Traz a credibilidade factual *"Desde 2011 em Tijucas"* para a abertura, sem afirmações exageradas.
  - Elimina a pressão comercial agressiva ("antes que esgote").
  - Introduz pergunta de engajamento conversacional ("semana ou fim de semana?") e opt-out regulamentar (`PARARNATAL2026`).
  - **Impacto em Execuções Congeladas:** Modificações futuras na tabela `messages` afetam apenas novas execuções; os snapshots passados permanecem congelados em `campaign_contact_stage_runs.stage_snapshot`.

---

#### Imagem dos Cenários e Pacotes (`role: cenarios_image`)
- **ID Real DB:** `1446` (Camp. 415) / `1462` (Camp. 416) | **Tipo:** `image`
- **Arquivo / Basename:** `natal-2026-pacotes-r1.png`
- **Campo:** `title` (enviado como legenda/caption pela Z-API via `CampaignMessageService`)

| Versão | Campo `title` | Campo `description` |
| :--- | :--- | :--- |
| **V4 Real** | `Pacotes Natal + Ano Novo 2026` | `Confira os valores e pacotes da Proposta de Natal 2026` |
| **V5 Draft** | `🎁 *Três jeitos de viver esse Natal!*\nTodos incluem os *dois cenários* e fotos reveladas.\nOlha os pacotes na imagem e me diz qual combina com vocês. ❤️` | *(Metadado interno inalterado)* |

- **Evidência:** `PRODUCAO_DB` + `INFERENCIA_CODIGO_ZAPI`.
- **Análise de Risco:** Risco zero. Transforma uma legenda burocrática em uma mensagem quente de engajamento no WhatsApp.

---

#### Documento PDF da Proposta (`role: proposta_pdf`)
- **ID Real DB:** `1447` (Camp. 415) / `1463` (Camp. 416) | **Tipo:** `attachment`
- **Arquivo / Basename:** `natal2026-rev357-v1-proposta.pdf`
- **Campo:** `title` (enviado como `fileName` no WhatsApp pela Z-API)

| Versão | Campo `title` (`fileName`) |
| :--- | :--- |
| **V4 Real** | `Proposta Natal 2026` |
| **V5 Draft** | `Proposta-Natal-AnoNovo-Evydencia-2026.pdf` |

- **Evidência:** `PRODUCAO_DB` + `INFERENCIA_CODIGO_ZAPI`.
- **Análise de Risco:** Correção técnica fundamental. A ausência de extensão `.pdf` no `title` do V4 podia dificultar a abertura direta em certos visualizadores de Android/iOS.

---

#### Botões Interativos Z-API (`send_buttons`)
- **ID da Ação:** `1017` (Camp. 415, mapeamento `24`) / `1036` (Camp. 416, mapeamento `34`)
- **Provider:** `zapi` | **Status:** `active`
- **Contrato Estrito V4:** Conforme validado em `Natal2026ProposalManifest::validate`, o CRM exige exatamente os 3 botões com URLs na allowlist aprovada e ordem estrita.

| Botão | Configuração Atual V4 | Proposta V5 Draft Consolidada | Avaliação Técnica & Contrato |
| :--- | :--- | :--- | :--- |
| **Botão 1** | `id: "natal2026-reservar"`<br>`label: "🎄 Reservar Agora"`<br>`url: "https://evydencia.com/catalogo/natal"` | `id: "natal2026-reservar"`<br>`label: "🎄 Reservar Agora"`<br>`url: "https://evydencia.com/catalogo/natal"` | **Contrato V4 Preservado.** Mantida a URL canônica aprovada do catálogo. O checkout direto (`reserva.fotosdenatal.com`) está documentado como melhoria futura delimitada (`checkout_gate: "BLOCKED_BY_APPROVED_V4_URL_ALLOWLIST"`), exigindo alteração formal de contrato no provisionador. |
| **Botão 2** | `id: "natal2026-horarios"`<br>`label: "📆 Ver Horários"`<br>`url: "https://horarios.fotosdenatal.com/"` | `id: "natal2026-horarios"`<br>`label: "📆 Ver Horários"`<br>`url: "https://horarios.fotosdenatal.com/"` | **Alinhado.** ID estável e URL canônica ativa. |
| **Botão 3** | `id: "natal2026-instagram"`<br>`label: "📸 Nosso Instagram"`<br>`url: "https://instagram.com/estudioevydenciaa"` | `id: "natal2026-instagram"`<br>`label: "📸 Nosso Instagram"`<br>`url: "https://instagram.com/estudioevydenciaa"` | **Alinhado.** ID estável e URL canônica ativa. |

---

### Etapa 02: H+1 (`role: whatsapp_h1`)
- **Etapa ID:** `658` (Camp. 415) / `677` (Camp. 416) | **Offset:** 3600s (60 min)
- **Mensagem Vídeo:** ID `1448` (Camp. 415) / `1464` (Camp. 416)
- **Arquivo MP4:** `natal-2026-1hora-r3.mp4` (SHA-256 verificado: `ff7123f9...`, 38,3s)
- **Mensagem Complementar (`duvidas_reply`):** ID `1449` / `1465`

| Campo | V4 Real | V5 Draft | Evidência & Diagnóstico |
| :--- | :--- | :--- | :--- |
| **`title` (Legenda Vídeo)** | `Olha só o que te espera por aqui!` | `🎄 Olha um pedacinho da nossa Vila de Natal...\nÉ aqui que as famílias brincam e criam lembranças lindas. ❤️` | **100% ALINHADO.** Vídeo mostra a menina brincando no cenário da Vila com Papai e Mamãe Noel. Legenda curta e poética. |

---

### Etapa 03: H+90 (`role: whatsapp_90min`)
- **Etapa ID:** `659` (Camp. 415) / `678` (Camp. 416) | **Offset:** 5400s (90 min)
- **Mensagem Vídeo:** ID `1450` (Camp. 415) / `1466` (Camp. 416)
- **Arquivo MP4:** `natal-2026-90min-r3.mp4` (SHA-256 verificado: `7b286ad9...`, 19,33s)
- **Mensagem Complementar (`reserva_cta`):** ID `1451` / `1467`

| Campo | V4 Real | V5 Draft | Evidência & Diagnóstico |
| :--- | :--- | :--- | :--- |
| **`title` (Legenda Vídeo)** | `🎄 Já pensou no Natal da sua família?` | `✨ Dois cenários. Uma experiência em família.\nQual dia ficaria melhor pra vocês? 🎅` | **ALINHADO.** Elimina promessas infundadas de "até 10x" ou alegações não homologadas. Foco puro na experiência e na pergunta de escolha de data. |

---

### Etapa 04: D+1 (`role: whatsapp_d1`)
- **Etapa ID:** `662` (Camp. 415) / `681` (Camp. 416) | **Offset:** 86400s (24h)
- **Mensagem Vídeo:** ID `1456` (Camp. 415) / `1472` (Camp. 416)
- **Arquivo MP4:** `natal2026-rev357-v1-d01.mp4` (SHA-256 verificado: `ed9aa188...`, 38,3s, 1080p)

| Campo | V4 Real | V5 Draft | Evidência & Diagnóstico |
| :--- | :--- | :--- | :--- |
| **`title` (Legenda Vídeo)** | `O tempo voa... Garanta o Natal da sua família! 🎄` | `{natal_countdown_dias_descricao}\n\nE a magia passa num piscar de olhos! 🥺✨\nNossos cenários já estão prontos pra receber vocês com todo carinho e conforto.\nBora escolher o dia do ensaio da sua família? É só me responder aqui! ❤️`<br>*(Fallback estático assegurado: "O Natal já está chegando e o tempo voa...")* | **100% ALINHADO.** Gatilho emocional de ver os filhos crescendo associado à contagem regressiva dinamicamente protegida por fallback no serializer. |

---

### Etapa 05: D+2 (`role: whatsapp_d2`)
- **Etapa ID:** `660` (Camp. 415) / `679` (Camp. 416) | **Offset:** 172800s (48h)
- **Mensagem Vídeo:** ID `1452` (Camp. 415) / `1468` (Camp. 416)
- **Arquivo MP4:** `natal-2026-2dias-r3.mp4` (SHA-256 verificado: `8b9d24e7...`, 28,87s)
- **Mensagem Complementar (`lembrete_2dias`):** ID `1453` / `1469`

| Campo | V4 Real | V5 Draft | Evidência & Diagnóstico |
| :--- | :--- | :--- | :--- |
| **`title` (Legenda Vídeo)** | `🎄 Um Natal mágico espera por vocês!` | `Você sabia que a nossa sessão tem uma surpresa que a maioria dos estúdios não oferece?\n\n✨ Na mesma sessão, você faz fotos de *Natal* e também de *Ano Novo*!\n\nAssim você já garante as fotos da virada com a família reunida e produzida. 🥂🤍` | **100% ALINHADO.** Vídeo mostra o bebê e a mãe em clima acolhedor de fim de ano. |

---

### Etapa 06: D+3 (`role: whatsapp_d3`)
- **Etapa ID:** `663` (Camp. 415) / `682` (Camp. 416) | **Offset:** 259200s (72h)
- **Mensagem Vídeo:** ID `1457` (Camp. 415) / `1473` (Camp. 416)
- **Arquivo MP4:** `natal2026-rev357-v1-jogos.mp4` (SHA-256 verificado: `2ac284bf...`, 81,33s)

| Campo | V4 Real | V5 Draft | Evidência & Diagnóstico |
| :--- | :--- | :--- | :--- |
| **`title` (Legenda Vídeo)** | `🕹️ Que tal um joguinho de Natal com as crianças?` | `Enquanto você escolhe o melhor dia pro ensaio, que tal uma brincadeira com as crianças? 🎄🎮\n\nCriamos joguinhos digitais de Natal exclusivos! Abre aí com eles:\n👉 https://jogos.fotosdenatal.com/\n\nDepois me conta se gostaram! 😄👇` | **100% PERFEITO.** Vídeo é a demonstração real gravada da tela de `jogos.fotosdenatal.com`. Não promete "10 jogos para todos", respeitando a regra comercial de 1, 5 ou 10 jogos por pacote. |

---

### Etapa 07: D+4 (`role: whatsapp_d4`)
- **Etapa ID:** `661` (Camp. 415) / `680` (Camp. 416) | **Offset:** 345600s (96h)
- **Mensagem Vídeo:** ID `1454` (Camp. 415) / `1470` (Camp. 416)
- **Arquivo MP4:** `natal-2026-4dias-r3.mp4` (SHA-256 verificado: `b41c33b6...`, 27,43s)
- **Mensagem Complementar (`lembrete_4dias`):** ID `1455` / `1471`

| Campo | V4 Real | V5 Draft | Evidência & Diagnóstico |
| :--- | :--- | :--- | :--- |
| **`title` (Legenda Vídeo)** | `Dúvidas sobre o figurino? A gente te ajuda! 👗👔` | `Uma das perguntas que mais recebo:\n*"O que a gente veste no dia do ensaio?"* 🤔\n\nFica tranquila(o)!\n✨ Temos opções de figurinos temáticos higienizados no estúdio;\n✨ Te enviamos um guia com combinações de cores que ficam lindas;\n✨ E você pode trazer a roupinha favorita deles também!\n\nO importante é vocês se sentirem confortáveis e felizes. ❤️` | **100% PERFEITO.** Vídeo foca visualmente no figurino infantil em vestido xadrez e bolhas de sabão. |

---

### Etapa 08: D+5 (`role: whatsapp_d5`) — Ajuste Cirúrgico Crítico
- **Etapa ID:** `664` (Camp. 415) / `683` (Camp. 416) | **Offset:** 432000s (120h)
- **Mensagem Vídeo:** ID `1458` (Camp. 415) / `1474` (Camp. 416)
- **Arquivo MP4:** `natal2026-rev357-v1-d05.mp4` (SHA-256 verificado: `c31886fc...`, 22,2s, 1080p)

| Campo | V4 Real | V5 Draft Atualizado | Evidência Forense & Correção |
| :--- | :--- | :--- | :--- |
| **`title` (Legenda Vídeo)** | `Tradição que se renova a cada ano! ✨` | `✨ Tem um recadinho especial saindo direto da nossa Vila mágica... 📜🎄\n\nNossos cenários estão prontos pra encantar a sua família com muito conforto e carinho.\n\nQualquer dúvida ou se quiser ver datas disponíveis, me dá um toque aqui! ❤️` | **AJUSTE FACTUAL:** O vídeo físico NÃO mostra fotos históricas de 2011, mas sim uma **Fada/Duende com chapéu iluminado cantando e lendo um pergaminho mágico** atrás da janela com neve. A menção de 2011 foi reposicionada com precisão em T0, e D+5 agora dialoga perfeitamente com o pergaminho. |

---

### Etapa 09: D+10 (`role: whatsapp_d10`)
- **Etapa ID:** `665` (Camp. 415) / `684` (Camp. 416) | **Offset:** 864000s (240h)
- **Mensagem Vídeo:** ID `1459` (Camp. 415) / `1475` (Camp. 416)
- **Arquivo MP4:** `natal2026-rev357-v1-d10.mp4` (SHA-256 verificado: `25dbbe82...`, 10,37s, 1080p)

| Campo | V4 Real | V5 Draft | Evidência & Diagnóstico |
| :--- | :--- | :--- | :--- |
| **`title` (Legenda Vídeo)** | `Últimas vagas para o ensaio de Natal! 🎅` | `🎄 Passando pra saber como estão as coisas por aí!\n\nAinda dá tempo de viver essa experiência com a sua família.\n\nSe quiser conferir os horários que ainda estão livres, me dá um toque aqui! ❤️` | **ALINHADO.** Elimina escassez fabricada ("quase esgotados") e promessas de "segurar data prévia sem compromisso". O personagem no vídeo acena com simpatia. |

---

### Etapa 10: Documento D+10 (`role: document_d10`) — BLOQUEIO ESTRITO P0
- **Etapa ID:** `666` (Camp. 415) / `685` (Camp. 416) | **Offset:** 864000s (240h)
- **Mensagem Anexo:** ID `1460` (Camp. 415) / `1476` (Camp. 416)
- **Arquivo PDF:** `natal2026-rev357-v1-d10-document.pdf`

| Situação | Diagnóstico & Veredito |
| :--- | :--- |
| **Classificação Editorial** | `blocked_do_not_publish` (Bloqueado para publicação) |
| **Conteúdo Físico Auditado** | JPEG encapsulado em PDF simulando comprovante Pix (*"Pagamento via PIX"* / *"Transação: 6842715"*), com flyer do Papai Noel na sequência. |
| **Risco Operacional** | **Crítico.** Há **12 agendamentos pendentes congelados no banco** em `campaign_contact_stage_runs.stage_snapshot`. Alterar o cadastro não descongela esses disparos. Exige rotina administrativa específica de cancelamento antes de qualquer envio. |

---

## 3. Demais Canais Preservados (SMS, Resend e SmartVoice)

1. **SMS (9 Etapas mantidas intactas):**
   - Etapas: `sms_h2`, `sms_d1`, `sms_d2`, `sms_d3`, `sms_d4`, `sms_d5`, `sms_d10`, `sms_d15`, `sms_d20`.
   - IDs de Etapa 415: `667` a `675` | IDs de Etapa 416: `686` a `694`.
   - Conteúdo: Mensagens SMS curtas com link WhatsApp `wa.me/5548996553954` e opt-out `SAIR`.
2. **Resend E-mail (Etapa T0 — Ações 24 e 34):**
   - Preservado o e-mail de texto inicial com assunto `Sua proposta Natal + Ano Novo 2026 — Evydência`.
3. **SmartVoice (Telefonia Asterisk):**
   - 5 arquivos WAV do bucket S3 mantidos com sua lógica interna sem alteração de `enabled` no discador.
