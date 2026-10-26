# 3. Matriz de Importação pelo Gestor (Diff V5.1) — Campanhas 415 e 416

**Data:** 2026-10-26T17:00:44Z  
**Origem do Plano:** `python scripts/plan_manager_v5.py`  
**Escopo:** 30 operações de campo previstas (15 na Campanha 415 com nome + 15 na Campanha 416 sem nome)  
**Status de Execução:** `READ_ONLY_PREVIEW_NO_PRODUCTION_WRITE` (Nenhum `UPDATE` executado)

---

## 1. Por Que a Importação Não Pode Ser Feita pelo Editor Atual

1. **Incompatibilidade com o Manifesto Aprovado:** No painel do CRM (`/gestao/campanhas/415/fluxo` e `/416/fluxo`), o botão "Salvar" do editor de mensagens executa `MessageController.php::update`. Essa rota altera apenas a tabela `messages`, mas **não recalcula nem atualiza o `flow.settings.readiness.manifest.configuration_hash`**.
2. **Disparo de Mismatch:** Na próxima checagem do CRM, `InspectNatalProposalReadiness` detecta divergência entre o banco e o hash do manifesto, marcando o fluxo como quebrado (`natal_manifest_configuration_mismatch`).
3. **Agendamentos Congelados:** A alteração via editor tradicional não atinge nenhum dos snapshots congelados em `campaign_contact_stage_runs`.

---

## 2. Matriz Completa das 30 Operações de Campo (V5.1)

Abaixo está o mapeamento exato de cada campo que deverá ser atualizado quando a importação atômica for autorizada:

| # | Campanha | Stage Role | Message Role | Tipo | Campo Destino | Asset Esperado | Resumo da Mudança V5.1 |
|---|:---:|---|---|:---:|:---:|---|---|
| 1 | 415 | `initial` | `intro_cta` | action | `message` | — | Saudação com `{primeiro_nome}`, 2 cenários, desde 2011, R$ 196 (2x R$ 98), pergunta dia. **SEM rodapé STOP**. |
| 2 | 416 | `initial` | `intro_cta` | action | `message` | — | Variante neutra (sem nome). **SEM rodapé STOP**. |
| 3 | 415 | `initial` | `cenarios_image` | image | `title` | `natal-2026-pacotes-r1.png` | Caption ativa dos 3 pacotes e 2 cenários (150 car. <= 200). |
| 4 | 416 | `initial` | `cenarios_image` | image | `title` | `natal-2026-pacotes-r1.png` | Idêntico na 416. |
| 5 | 415 | `initial` | `proposta_pdf` | attachment | `title` | `natal2026-rev357-v1-proposta.pdf`| `fileName`: `Proposta-Natal-AnoNovo-Evydencia-2026.pdf`. |
| 6 | 416 | `initial` | `proposta_pdf` | attachment | `title` | `natal2026-rev357-v1-proposta.pdf`| Idêntico na 416. |
| 7 | 415 | `whatsapp_h1` | `video_1hora` | video | `title` | `natal-2026-1hora-r3.mp4` | Caption sobre o encanto da Vila (106 car.). |
| 8 | 416 | `whatsapp_h1` | `video_1hora` | video | `title` | `natal-2026-1hora-r3.mp4` | Idêntico na 416. |
| 9 | 415 | `whatsapp_h1` | `duvidas_reply` | action | `message` | — | Pergunta de suporte (46 car.). **SEM rodapé textual; botão REPLY STOP preservado.** |
| 10| 416 | `whatsapp_h1` | `duvidas_reply` | action | `message` | — | Idêntico na 416. |
| 11| 415 | `whatsapp_90min`| `video_90min` | video | `title` | `natal-2026-90min-r3.mp4` | Caption dos 2 cenários na mesma sessão (85 car.). |
| 12| 416 | `whatsapp_90min`| `video_90min` | video | `title` | `natal-2026-90min-r3.mp4` | Idêntico na 416. |
| 13| 415 | `whatsapp_90min`| `reserva_cta` | action | `message` | — | CTA para botão de horário (58 car.). **SEM rodapé STOP.** |
| 14| 416 | `whatsapp_90min`| `reserva_cta` | action | `message` | — | Idêntico na 416. |
| 15| 415 | `whatsapp_d1` | `video_d1` | video | `title` | `natal2026-rev357-v1-d01.mp4` | Caption estática literal (102 car.). Token dinâmico removido. |
| 16| 416 | `whatsapp_d1` | `video_d1` | video | `title` | `natal2026-rev357-v1-d01.mp4` | Idêntico na 416. |
| 17| 415 | `whatsapp_d2` | `video_2dias` | video | `title` | `natal-2026-2dias-r3.mp4` | Caption cenário Ano Novo (90 car.). |
| 18| 416 | `whatsapp_d2` | `video_2dias` | video | `title` | `natal-2026-2dias-r3.mp4` | Idêntico na 416. |
| 19| 415 | `whatsapp_d2` | `lembrete_2dias`| action | `message` | — | Pergunta sobre horários (54 car.). **SEM rodapé STOP.** |
| 20| 416 | `whatsapp_d2` | `lembrete_2dias`| action | `message` | — | Idêntico na 416. |
| 21| 415 | `whatsapp_d3` | `video_d3` | video | `title` | `natal2026-rev357-v1-jogos.mp4` | Jogos + link demo + **primeiro aviso textual STOP** (166 car. <= 200). |
| 22| 416 | `whatsapp_d3` | `video_d3` | video | `title` | `natal2026-rev357-v1-jogos.mp4` | Idêntico na 416. |
| 23| 415 | `whatsapp_d4` | `video_4dias` | video | `title` | `natal-2026-4dias-r3.mp4` | Figurinos e bolhas de sabão reais (99 car.). |
| 24| 416 | `whatsapp_d4` | `video_4dias` | video | `title` | `natal-2026-4dias-r3.mp4` | Idêntico na 416. |
| 25| 415 | `whatsapp_d4` | `lembrete_4dias`| action | `message` | — | **Lista dos 10 motivos para agendar** + CTA + aviso STOP discreto (510 car.). |
| 26| 416 | `whatsapp_d4` | `lembrete_4dias`| action | `message` | — | Idêntico na 416. |
| 27| 415 | `whatsapp_d5` | `video_d5` | video | `title` | `natal2026-rev357-v1-d05.mp4` | Pergaminho iluminado dos duendes/fada (127 car.). |
| 28| 416 | `whatsapp_d5` | `video_d5` | video | `title` | `natal2026-rev357-v1-d05.mp4` | Idêntico na 416. |
| 29| 415 | `whatsapp_d10`| `video_d10` | video | `title` | `natal2026-rev357-v1-d10.mp4` | Fechamento acolhedor de dúvidas + aviso STOP (174 car. <= 200). |
| 30| 416 | `whatsapp_d10`| `video_d10` | video | `title` | `natal2026-rev357-v1-d10.mp4` | Idêntico na 416. |

---

## 3. Botões e Regras Contratuais Preservadas

* **Botões T0 (initial):**
  1. `natal2026-reservar` (URL) → `https://evydencia.com/catalogo/natal`
  2. `natal2026-horarios` (URL) → `https://horarios.fotosdenatal.com/`
  3. `natal2026-instagram` (URL) → `https://instagram.com/estudioevydenciaa`
* **Botão REPLY H+1 (whatsapp_h1):**
  * `PARARNATAL2026` (REPLY) → Obrigatório pelo contrato V4; **não foi excluído**.
* **Documento D+10 (document_d10):**
  * Marcado como `blocked_do_not_publish`; **não faz parte da importação**.
* **SMS (9), Resend (1) e SmartVoice (5):**
  * Preservados sem qualquer alteração.

---

## 4. Especificação da Evolução do Publicador no CRM

Para que a importação possa ser realizada pelo gestor no navegador sem terminal SSH:
1. **Endpoint de Importação:** `POST /gestao/campanhas/natal2026/importar-rascunho` recebendo o arquivo JSON editorial.
2. **Simulador de Telas:** Exibição do balão de WhatsApp renderizado (estilo Z-API) para conferência visual prévia da diretoria.
3. **Publicação com Compare-And-Swap (CAS):**
   * Parâmetro obrigatório: `expected_configuration_hash`.
   * Geração de backup automático (`rollback_snapshot`).
   * Recálculo atômico do hash em transação de banco (`DB::transaction`).
   * Decisão explícita na tela sobre como tratar execuções legadas agendadas.
