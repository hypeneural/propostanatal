# 1. Aprovação Comercial V5.1 por Papel e Campo — Natal 2026

**Data da Auditoria:** 2026-10-26T17:00:44Z  
**Versão Editorial:** V5.1 (`editorial/whatsapp-v5.draft.json`)  
**Documento Canônico:** `14-COPY-CANONICA-V5-CANDIDATA.md` (verificado bit a bit via `scripts/render_canonical_v5.py --check`)  
**Status da Versão:** `V51_EDITORIAL_READY_FOR_DIRECTOR_APPROVAL` (Produção: `P0_PRODUCTION_NO_GO`)

---

## 1. Matriz de Aprovação Comercial Papel a Papel (19 Roles)

| Etapa / Role | Canal / Tipo | Campo CRM | Tamanho | Texto e Decisão V5.1 | Status Comercial | Justificativa e Claims Confirmados |
|---|---|---|---:|---|:---:|---|
| **initial** | WhatsApp `action` | `message` (415 com nome) | 277 car. | Saudação curta com `{primeiro_nome}`, 2 cenários, desde 2011, 2x R$ 98, pergunta natural ("semana ou fim de semana?"). **SEM rodapé STOP repetitivo.** | **APPROVE** | O lead acabou de pedir o orçamento; foco imediato na proposta. Claim "Desde 2011" verdadeiro. |
| **initial** | WhatsApp `action` | `message` (416 sem nome) | 258 car. | Variante idêntica sem interpolação de nome. **SEM rodapé STOP repetitivo.** | **APPROVE** | Neutra e segura para base sem nome higienizado. |
| **initial** | WhatsApp `image` | `title` (caption) | 150 car. | `🎁 *Três jeitos de viver esse Natal!* Todos incluem os *dois cenários* e fotos reveladas. Olha os pacotes na imagem e me diz qual combina com vocês. ❤️` | **APPROVE** | Legenda limpa no teto de 200 caracteres (`mb_strlen`). |
| **initial** | WhatsApp `attachment`| `title` (fileName) | 41 car. | `Proposta-Natal-AnoNovo-Evydencia-2026.pdf` | **APPROVE** | Nome legível com extensão explícita `.pdf`. |
| **initial** | WhatsApp `buttons` | 3 URLs | — | 1. `natal2026-reservar` → Catálogo<br>2. `natal2026-horarios` → Horários<br>3. `natal2026-instagram` → Instagram | **APPROVE** | Ordem exata da allowlist V4. Checkout direto mantido em gate de código. |
| **whatsapp_h1**| WhatsApp `video` | `title` (caption) | 106 car. | `🎄 Olha um pedacinho da nossa Vila de Natal... É aqui que as famílias brincam e criam lembranças lindas. ❤️` | **APPROVE** | Clipe `natal-2026-1hora-r3.mp4`. Emoção sem promessa de personagem presencial. |
| **whatsapp_h1**| WhatsApp `action` | `message` | 46 car. | `Quer que eu te ajude a escolher um horário? 🥰` (**SEM rodapé textual; PRESERVADO botão REPLY `PARARNATAL2026`**). | **APPROVE** | Cumpre a exigência técnica do contrato V4 (botão REPLY) sem poluir o texto. |
| **whatsapp_90min**| WhatsApp `video`| `title` (caption) | 85 car. | `✨ São *dois cenários na mesma sessão*. Qual dia seria mais tranquilo pra sua família?` | **APPROVE** | Reforço dos dois cenários no clipe `natal-2026-90min-r3.mp4`. |
| **whatsapp_90min**| WhatsApp `action`| `message` | 58 car. | `Se preferir, dá pra escolher o horário direto no botão. 📅` (**SEM rodapé textual**). | **APPROVE** | CTA objetivo sem repetição prematura de STOP. |
| **sms_h2** | SMS | `sms.body` | — | Mantido original da V4 (`preserve_existing_v4`). | **PRESERVADO** | Nenhuma alteração nesta rodada. |
| **whatsapp_d1**| WhatsApp `video` | `title` (caption) | 102 car. | `🥹 O tempo voa... mas as fotos desta fase ficam pra sempre. ❤️ Que tal guardar esse Natal em família? 🎄` | **APPROVE** | Legenda estática literal. Elimina risco do token dinâmico `{natal_countdown_dias_descricao}`. |
| **sms_d1** | SMS | `sms.body` | — | Mantido original da V4 (`preserve_existing_v4`). | **PRESERVADO** | Preservado sem mutação. |
| **whatsapp_d2**| WhatsApp `video` | `title` (caption) | 90 car. | `🎆 *Natal + Ano Novo*, num ensaio só! Dois cenários pra guardar mais momentos da família. ✨` | **APPROVE** | Foco visual no cenário Réveillon (`natal-2026-2dias-r3.mp4`). |
| **whatsapp_d2**| WhatsApp `action` | `message` | 54 car. | `Quer ver os horários que ainda funcionam pra vocês? 📅` (**SEM rodapé textual**). | **APPROVE** | Pergunta conversacional leve. |
| **sms_d2** | SMS | `sms.body` | — | Mantido original da V4 (`preserve_existing_v4`). | **PRESERVADO** | Preservado sem mutação. |
| **whatsapp_d3**| WhatsApp `video` | `title` (caption) | 166 car. | `🎮 As fotos do ensaio viram *jogos personalizados*! Quer testar? https://jogos.fotosdenatal.com/ _Se preferir parar os lembretes, responda PARARNATAL2026._` | **APPROVE** | **Primeiro aviso textual STOP da régua** no vídeo. Link de jogos funcional. Limite 166 <= 200 car. |
| **sms_d3** | SMS | `sms.body` | — | Mantido original da V4 (`preserve_existing_v4`). | **PRESERVADO** | Preservado sem mutação. |
| **whatsapp_d4**| WhatsApp `video` | `title` (caption) | 99 car. | `👗 Figurinos, brincadeiras e até *bolhinhas de sabão*! 🫧 Aqui, o ensaio também é uma experiência. ❤️` | **APPROVE** | Alinhado com figurinos infantis e bolhas reais do clipe `natal-2026-4dias-r3.mp4`. |
| **whatsapp_d4**| WhatsApp `action` | `message` | 510 car. | **10 Motivos Curtos para Agendar** (dois cenários, fotos reveladas, jogos digitais, galeria online, figurinos inclusos, bolhas de sabão, família toda, hora marcada, desde 2011, 2x R$ 98) + CTA + opt-out discreto. | **APPROVE** | Substituição na ação existente `lembrete_4dias`, sem criar novo disparo. Claims alinhados à operação. |
| **sms_d4** | SMS | `sms.body` | — | Mantido original da V4 (`preserve_existing_v4`). | **PRESERVADO** | Preservado sem mutação. |
| **whatsapp_d5**| WhatsApp `video` | `title` (caption) | 127 car. | `✨ Um recadinho mágico apareceu na nossa Vila de Natal! 📜🎄 Olha esse encanto... Quer ver os horários pra viver essa experiência?` | **APPROVE** | Totalmente coerente com a cena do pergaminho iluminado (`natal2026-rev357-v1-d05.mp4`). |
| **sms_d5** | SMS | `sms.body` | — | Mantido original da V4 (`preserve_existing_v4`). | **PRESERVADO** | Preservado sem mutação. |
| **whatsapp_d10**| WhatsApp `video`| `title` (caption) | 174 car. | `🎄 Ainda pensando nas fotos de Natal? Se a dúvida for pacote, roupinha ou horário, me chama por aqui. ❤️ _Se preferir parar os lembretes, responda PARARNATAL2026._` | **APPROVE** | Fechamento acolhedor com aviso discreto de opt-out. Limite 174 <= 200 car. |
| **document_d10**| WhatsApp `attachment`| `title` | — | Flyer com aparência de comprovante Pix. | **BLOQUEADO (P0)** | **`blocked_do_not_publish`**. Proibido importar ou disparar. |
| **sms_d10..d20**| SMS (3 etapas) | `sms.body` | — | Mantidos originais da V4 (`preserve_existing_v4`). | **PRESERVADO** | Preservados sem mutação. |

---

## 2. Auditoria dos 10 Motivos de D+4 e Alinhamento de Claims

A lista de 10 motivos foi inserida em `lembrete_4dias.message` (ação já existente no catálogo V4), evitando criação de novos disparos:
1. **2 cenários completos na mesma sessão:** Comprovado (Natal + Ano Novo no estúdio de Tijucas).
2. **Fotos reveladas no papel:** Comprovado (todos os pacotes incluem fotos impressas).
3. **Jogos digitais personalizados:** Comprovado (1, 5 ou 10 jogos conforme o pacote escolhido).
4. **Galeria online:** Comprovado (acesso digital para visualização).
5. **Figurinos infantis inclusos:** Comprovado na operação real do estúdio.
6. **Experiência lúdica com bolhas de sabão:** Comprovado no clipe real de D+4.
7. **Retratos de família juntos:** Comprovado (pais, irmãos e avós participam do ensaio).
8. **Atendimento com hora marcada:** Comprovado na agenda do estúdio.
9. **Tradição desde 2011 em Tijucas:** Comprovado historicamente (13+ anos de mercado).
10. **Valores a partir de 2x R$ 98:** Comprovado no pacote inicial (R$ 196 total).

> [!NOTE]
> Nenhum brinde incerto (como medalhinhas ou certificados físicos universais) ou falsa escassez ("últimos 3 horários") foi incluído. A urgência se dá pelo avanço natural do calendário.
