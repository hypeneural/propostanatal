# V5-COPY-REVIEW-ATUALIZADO — Revisão Consolidada de Copy WhatsApp

**Data:** 26/10/2026  
**Status Editorial:** `V5_EDITORIAL_READY_FOR_APPROVAL` (Rascunho consolidado sem publicação)  
**Base Canônica:** `editorial/whatsapp-v5.draft.json`  
**Fontes Históricas Utilizadas:** Google Drive (Propostas 2024/2025, Master 100 Motivos)  
**Documentação Técnica:** Z-API Docs + Contrato Laravel V4 (`app/Services/Proposals/Natal2026ProposalManifest.php`)

---

## 1. Diretrizes Técnicas de Redação no WhatsApp

1. **Formatação Nativa Estrita:** Uso exclusivo de `*negrito*` para ênfase de benefícios, `_itálico_` para notas breves/humanizadas, quebras de linha (`\n`) para respiro visual e emojis discretos (0–2 por bloco).
2. **Eliminação de Sintaxes Inadequadas:** Zero Markdown de GitHub (`**`, ````), zero HTML (`<br>`, `<b>`), zero tags de sintetizador de voz ElevenLabs (`[excited]`) e zero aspas triplas (`'''`).
3. **Mapeamento Rigoroso de Campos:**
   - **`action.message` (Body):** Texto principal que acompanha os botões em mensagens do tipo `action`.
   - **`image/video.title` (Caption):** Legenda entregue pela Z-API sob imagens e vídeos. Teto técnico estrito: **200 caracteres Unicode** (meta editorial: 80–160 caracteres).
   - **`attachment.title` (fileName):** Nome do arquivo exibido para download no WhatsApp (exige extensão `.pdf`).
   - *Atenção:* O campo `description` em mensagens de mídia não é transmitido como legenda pela Z-API.

---

## 2. Textos Finais Propostos por Etapa e Campo Técnico

### Etapa 01: Inicial T0 (`role: initial`)
- **Campanhas:** 415 (Com Nome) e 416 (Sem Nome) | **Offset:** 0s (`immediate`)
- **Ação com Botões:** Ação 1017 (415) / 1036 (416)
- **Mensagem Imagem:** ID `1446` (415) / `1462` (416) — `natal-2026-pacotes-r1.png`
- **Mensagem PDF:** ID `1447` (415) / `1463` (416) — `natal2026-rev357-v1-proposta.pdf`

#### Texto de Abertura (`intro_cta`) — Campo: `action.message`
**Campanha 415 (Com Primeiro Nome — 340 caracteres Unicode):**
```text
Oii, *{primeiro_nome}*! 🎄🥰

Na *Evydência*, em Tijucas, preparamos *2 cenários* na mesma sessão: *Natal + Ano Novo*! ✨

Estamos aqui *desde 2011*, fotografando histórias de família. ❤️

Já te envio a imagem dos pacotes e o PDF, a partir de *2x R$ 98*.

*Prefere semana ou fim de semana?*

_Para parar lembretes, responda PARARNATAL2026._
```

**Campanha 416 (Sem Nome — 316 caracteres Unicode):**
```text
Oii! 🎄🥰

Na *Evydência*, em Tijucas, preparamos *2 cenários* na mesma sessão: *Natal + Ano Novo*! ✨

Estamos aqui *desde 2011*, fotografando histórias de família. ❤️

Já te envio a imagem dos pacotes e o PDF, a partir de *2x R$ 98*.

*Prefere semana ou fim de semana?*

_Para parar lembretes, responda PARARNATAL2026._
```
*Justificativa Editorial:* Elimina os triple-backticks do V4, traz credibilidade factual desde 2011 em Tijucas, clareza de preço imediato (a partir de 2x R$ 98), convite conversacional de baixa fricção e opt-out regulamentar.

#### Imagem dos Pacotes (`cenarios_image`) — Campo: `image.title` (Caption)
**Contagem:** 150 caracteres Unicode (limite: 200)
```text
🎁 *Três jeitos de viver esse Natal!*
Todos incluem os *dois cenários* e fotos reveladas.
Olha os pacotes na imagem e me diz qual combina com vocês. ❤️
```

#### Documento PDF da Proposta (`proposta_pdf`) — Campo: `attachment.title` (fileName)
**Contagem:** 42 caracteres
```text
Proposta-Natal-AnoNovo-Evydencia-2026.pdf
```

#### Botões Contratuais V4 Preservados:
- **Botão 1:** `id: natal2026-reservar` | `label: 🎄 Reservar Agora` | `url: https://evydencia.com/catalogo/natal`
- **Botão 2:** `id: natal2026-horarios` | `label: 📆 Ver Horários` | `url: https://horarios.fotosdenatal.com/`
- **Botão 3:** `id: natal2026-instagram` | `label: 📸 Nosso Instagram` | `url: https://instagram.com/estudioevydenciaa`

---

### Etapa 02: H+1 (`role: whatsapp_h1`)
- **Offset:** 3600s (60 min)
- **Mensagem Vídeo:** ID `1448` (415) / `1464` (416) — `natal-2026-1hora-r3.mp4`
- **Campo:** `video.title` (Caption Z-API)
- **Contagem:** 104 caracteres Unicode (limite: 200)
```text
🎄 Um pedacinho da nossa Vila de Natal...
É aqui que as famílias brincam e criam memórias lindas. ❤️
```
- **Ação Complementar (`duvidas_reply`):** ID `1449` / `1465` — Campo: `action.message` (103 caracteres):
```text
Ficou alguma dúvida sobre os pacotes ou sobre as fotos de Ano Novo? Pode me perguntar por aqui! 🥰
```
*Justificativa Editorial:* O vídeo exibe a menina de mãos dadas com Papai e Mamãe Noel na Vila de Natal. A legenda trata a cena como vivência afetiva, sem alegar falsamente que os personagens estão incluídos na contratação do ensaio.

---

### Etapa 03: H+90 (`role: whatsapp_90min`)
- **Offset:** 5400s (90 min)
- **Mensagem Vídeo:** ID `1450` (415) / `1466` (416) — `natal-2026-90min-r3.mp4`
- **Campo:** `video.title` (Caption Z-API)
- **Contagem:** 94 caracteres Unicode (limite: 200)
```text
✨ Dois cenários numa única sessão (Natal e Ano Novo)!
Qual dia ficaria melhor pra vocês? 🎅
```
- **Ação Complementar (`reserva_cta`):** ID `1451` / `1467` — Campo: `action.message` (76 caracteres):
```text
Se quiser garantir seu dia preferido antes que fechem a grade, me avisa! 🗓️
```
*Justificativa Editorial:* Enfatiza a conveniência prática de fazer duas decorações numa visita. Removeu-se qualquer menção não comprovada a "10 parcelas" ou promessas não homologadas.

---

### Etapa 04: D+1 (`role: whatsapp_d1`)
- **Offset:** 86400s (24h)
- **Mensagem Vídeo:** ID `1456` (415) / `1472` (416) — `natal2026-rev357-v1-d01.mp4` (Full HD 1080p)
- **Campo:** `video.title` (Caption Z-API)
- **Contagem:** 116 caracteres Unicode (com token dinâmico)
```text
*{natal_countdown_dias_descricao}* 🎄
Seus filhos crescem rápido... As lembranças deste Natal ficam pra sempre. ❤️
```
- **Fallback Estático (Instrução Editorial):**
```text
O tempo voa... Mas as lembranças deste Natal ficam pra sempre. ❤️
```
*Justificativa Editorial:* Gatilho clássico da infância passando rápido. A variável dinâmica é auditada no portão `COUNTDOWN-RENDER-GATE.md`.

---

### Etapa 05: D+2 (`role: whatsapp_d2`)
- **Offset:** 172800s (48h)
- **Mensagem Vídeo:** ID `1452` (415) / `1468` (416) — `natal-2026-2dias-r3.mp4`
- **Campo:** `video.title` (Caption Z-API)
- **Contagem:** 129 caracteres Unicode (limite: 200)
```text
🥂 Além do Natal, sua família ganha fotos de *Ano Novo* na mesma visita!
Assim a virada já fica registrada com quem você ama. ✨
```
*Justificativa Editorial:* Diálogo harmônico com o vídeo do bebê sorrindo na poltrona e decoração brilhante de Réveillon.

---

### Etapa 06: D+3 (`role: whatsapp_d3`)
- **Offset:** 259200s (72h)
- **Mensagem Vídeo:** ID `1457` (415) / `1473` (416) — `natal2026-rev357-v1-jogos.mp4`
- **Campo:** `video.title` (Caption Z-API)
- **Contagem:** 95 caracteres Unicode (limite: 200)
```text
🎮 As fotos do ensaio viram *jogos personalizados*!
Quer testar? https://jogos.fotosdenatal.com/
```
*Justificativa Editorial:* Demonstração real demonstrada no vídeo de tela vertical. Não promete 10 jogos para todos os clientes, preservando a diferenciação comercial dos pacotes (1, 5 ou 10 jogos).

---

### Etapa 07: D+4 (`role: whatsapp_d4`)
- **Offset:** 345600s (96h)
- **Mensagem Vídeo:** ID `1454` (415) / `1470` (416) — `natal-2026-4dias-r3.mp4`
- **Campo:** `video.title` (Caption Z-API)
- **Contagem:** 114 caracteres Unicode (limite: 200)
```text
🫧 A infância passa num sopro de bolhinhas de sabão...
Cuidamos do figurino e de cada detalhe com carinho! 👗👔
```
*Justificativa Editorial:* Vídeo mostra a menina em vestido clássico xadrez e bolhas de sabão cenográficas.

---

### Etapa 08: D+5 (`role: whatsapp_d5`) — Ajuste Cirúrgico Crítico
- **Offset:** 432000s (120h)
- **Mensagem Vídeo:** ID `1458` (415) / `1474` (416) — `natal2026-rev357-v1-d05.mp4` (Full HD 1080p)
- **Campo:** `video.title` (Caption Z-API)
- **Contagem:** 127 caracteres Unicode (limite: 200)
```text
✨ Um recadinho mágico apareceu na nossa Vila de Natal! 📜🎄
Olha esse encanto... Quer ver os horários pra viver essa experiência?
```
*Justificativa Editorial:* Alinhamento cirúrgico com a cena real comprovada em inspeção física (Fada/Duende desenrolando o pergaminho luminoso). Elimina a menção desconexa a 2011, que foi devidamente alocada em T0.

---

### Etapa 09: D+10 (`role: whatsapp_d10`)
- **Offset:** 864000s (240h)
- **Mensagem Vídeo:** ID `1459` (415) / `1475` (416) — `natal2026-rev357-v1-d10.mp4`
- **Campo:** `video.title` (Caption Z-API)
- **Contagem:** 123 caracteres Unicode (limite: 200)
```text
🎄 Ainda dá tempo de viver esse momento em família!
Se quiser conferir as datas livres da temporada, me avisa por aqui. ❤️
```
*Justificativa Editorial:* Follow-up caloroso com o personagem acenando amigavelmente. Elimina falsa escassez e promessas de "segurar vaga sem compromisso".

---

### Etapa 10: Documento D+10 (`role: document_d10`) — BLOQUEIO ESTRITO P0
- **Mensagem:** ID `1460` (415) / `1476` (416) — `natal2026-rev357-v1-d10-document.pdf`
- **Classificação:** `blocked_do_not_publish`.
- **Risco:** Simulação de comprovante Pix. Manter bloqueio absoluto.
