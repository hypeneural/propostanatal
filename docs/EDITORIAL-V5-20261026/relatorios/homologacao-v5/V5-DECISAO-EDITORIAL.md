# Decisão Editorial V5 por Papel e Campo — Natal 2026

**Data da Auditoria:** 2026-10-26T16:15:25Z  
**Fonte Canônica Única:** `editorial/whatsapp-v5.draft.json`  
**Referência Comparativa:** `14-COPY-CANONICA-V5-CANDIDATA.md` (renderizada e verificada bit a bit)  
**Status do Catálogo:** `GO_OFFLINE_REVIEW_ONLY` (Produção: `NO_GO`)

---

## 1. Tabela de Decisão Papel a Papel (19 Roles V4)

| Role | Canal | Tipo Campo | Mídia Associada | Contagem Unicode | Decisão Editorial | Justificativa / Parecer |
|---|---|---|---|---:|:---:|---|
| **initial** | WhatsApp | `action.message` (com nome) | — | 337 | **APROVADO** | Acolhedor, credibilidade ("Desde 2011 em Tijucas"), clareza de entrada (2x R$ 98), pergunta natural de dia e opt-out. |
| **initial** | WhatsApp | `action.message` (sem nome) | — | 318 | **APROVADO** | Variante neutra idêntica em persuasão para base não higienizada. |
| **initial** | WhatsApp | `image.title` | `natal-2026-pacotes-r1.png` | 150 | **APROVADO** | Legenda clara e envolvente dentro do teto de 200 caracteres (`mb_strlen`). |
| **initial** | WhatsApp | `attachment.title` | `natal2026-rev357-v1-proposta.pdf` | 41 | **APROVADO** | `fileName` legível com extensão `.pdf` explícita (`Proposta-Natal-AnoNovo-Evydencia-2026.pdf`). |
| **initial** | WhatsApp | `buttons` (3 URLs) | — | — | **APROVADO** | Ordem exata da allowlist V4: `natal2026-reservar`, `natal2026-horarios`, `natal2026-instagram`. Checkout direto mantido em gate. |
| **whatsapp_h1** | WhatsApp | `video.title` | `natal-2026-1hora-r3.mp4` | 106 | **APROVADO** | Emoção e encanto sem prometer presença de personagens no estúdio. |
| **whatsapp_h1** | WhatsApp | `action.message` | — | 95 | **APROVADO** | Pergunta de suporte com opt-out regulamentar. |
| **whatsapp_90min** | WhatsApp | `video.title` | `natal-2026-90min-r3.mp4` | 85 | **APROVADO** | Reforço objetivo dos 2 cenários na mesma sessão. |
| **whatsapp_90min** | WhatsApp | `action.message` | — | 107 | **APROVADO** | CTA direto para botão de calendário. |
| **sms_h2** | SMS | `sms.body` | — | — | **PRESERVADO** | Mantido em `preserve_existing_v4`. Ideias de atenuação em doc 12. |
| **whatsapp_d1** | WhatsApp | `video.title` | `natal2026-rev357-v1-d01.mp4` | 102 | **APROVADO** | Caption estática literal sem token `{natal_countdown_dias_descricao}`, eliminando risco de quebra no worker. |
| **sms_d1** | SMS | `sms.body` | — | — | **PRESERVADO** | Mantido em `preserve_existing_v4`. |
| **whatsapp_d2** | WhatsApp | `video.title` | `natal-2026-2dias-r3.mp4` | 90 | **APROVADO** | Foco visual no segundo cenário (Ano Novo/Réveillon). |
| **whatsapp_d2** | WhatsApp | `action.message` | — | 103 | **APROVADO** | Chamada amigável para checagem de horários. |
| **sms_d2** | SMS | `sms.body` | — | — | **PRESERVADO** | Mantido em `preserve_existing_v4`. |
| **whatsapp_d3** | WhatsApp | `video.title` | `natal2026-rev357-v1-jogos.mp4` | 95 | **APROVADO (RESSALVA)** | Link funcional `https://jogos.fotosdenatal.com/`. Alinhar comercialmente que pacotes trazem 1, 5 ou 10 jogos. |
| **sms_d3** | SMS | `sms.body` | — | — | **PRESERVADO** | Mantido em `preserve_existing_v4`. |
| **whatsapp_d4** | WhatsApp | `video.title` | `natal-2026-4dias-r3.mp4` | 99 | **APROVADO** | Alinhado aos figurinos infantis e bolinhas de sabão reais do clipe. |
| **whatsapp_d4** | WhatsApp | `action.message` | — | 106 | **APROVADO** | Oferece ajuda personalizada com looks da família. |
| **sms_d4** | SMS | `sms.body` | — | — | **PRESERVADO** | Mantido em `preserve_existing_v4`. |
| **whatsapp_d5** | WhatsApp | `video.title` | `natal2026-rev357-v1-d05.mp4` | 127 | **APROVADO** | Alinhado com fidelidade à cena real do pergaminho luminoso. |
| **sms_d5** | SMS | `sms.body` | — | — | **PRESERVADO** | Mantido em `preserve_existing_v4`. |
| **whatsapp_d10** | WhatsApp | `video.title` | `natal2026-rev357-v1-d10.mp4` | 103 | **APROVADO** | Fechamento acolhedor sem urgência ou escassez artificial. |
| **document_d10** | WhatsApp | `attachment.title` | `natal2026-rev357-v1-d10-document.pdf` | — | **BLOQUEADO (P0)** | Anexo de flyer Pix bloqueado para publicação. Exige supressão no banco antes de qualquer release. |
| **sms_d10** | SMS | `sms.body` | — | — | **PRESERVADO** | Mantido em `preserve_existing_v4`. |
| **sms_d15** | SMS | `sms.body` | — | — | **PRESERVADO** | Mantido em `preserve_existing_v4`. |
| **sms_d20** | SMS | `sms.body` | — | — | **PRESERVADO** | Mantido em `preserve_existing_v4`. |

---

## 2. Conformidade com Validações Automatizadas
* `python scripts/render_canonical_v5.py --check` → **BIT A BIT IDÊNTICO** ao `14-COPY-CANONICA-V5-CANDIDATA.md`.
* `python scripts/validate_editorial.py` → **PASS** (19 papéis, opt-out em todos os actions, botões aprovados, limites <= 200).
* `python scripts/check_forensic_gates.py` → **PASS OFFLINE** (rejeição automática para flag `--require-production-go`).
