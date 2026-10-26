# 17 — Dossiê Mestre de Homologação V5 e Estratégia de Próximos Passos (Para Validação pelo ChatGPT)

**Data:** 26/10/2026  
**Status do Projeto:** `GO_OFFLINE_REVIEW_ONLY` (Produção: `NO_GO`)  
**Repositório Independente:** `https://github.com/hypeneural/propostanatal`  
**Repositório Canônico:** `hypeneural/evydencia-crm`  
**Destinatário da Auditoria:** ChatGPT / Diretoria Comercial e Engenharia do Estúdio Evydência  

---

## 1. Missão Deste Dossiê

Este documento consolida a **Missão de Homologação Local V5**, integrando a nova extração read-only do banco de dados de produção (26/10/2026 16:15:25Z), a matriz de decisão editorial para todos os 19 papéis, a auditoria dos canais complementares (SMS, Resend e SmartVoice) e a especificação da publicação versionada sem VPS.

Solicitamos que o **ChatGPT** realize uma auditoria independente deste material e forneça **dicas e recomendações estratégicas para o PRÓXIMO PASSO** (transição segura da homologação offline para a publicação controlada em produção).

---

## 2. Nova Extração Read-Only LIVE e Diagnóstico Forense P0 (D+10)

Uma nova consulta somente leitura foi realizada diretamente no MySQL de produção (`reports/20261026-161525/`), revelando que **os dados de agendamento são fotos temporais que continuam evoluindo**:

### 2.1. Comparativo Factual de Execuções Congeladas (Etapas 666 e 685)
| Parâmetro | Coleta Histórica Anterior (12:34:30) | **Coleta LIVE Atual (16:15:25)** | Variação Real |
|---|:---:|:---:|:---:|
| **Etapa 666 (Campanha 415 com nome) — Pending** | 8 | **9** | **+1 cliente novo agendado** |
| **Etapa 666 (Campanha 415 com nome) — Skipped** | 1 | **1** | Estável |
| **Etapa 685 (Campanha 416 sem nome) — Pending** | 4 | **4** | Estável |
| **Total de Agendamentos Pendentes Congelados** | **12** | **13** | **+1 cliente sob risco** |
| **Total Geral Registrado nas Etapas D+10** | 13 | **14** | +1 registro |

### 2.2. Detalhes Técnicos Críticos
* **Título Congelado no Snapshot:** `Comprovante_de_Transferencia Pix_01112026_200921`.
* **Arquivo Congelado:** `natal2026-rev357-v1-d10-document.pdf` (flyer que simula comprovante Pix).
* **Mapeamento Z-API:** O CRM repassa `$message->title` como `fileName` no WhatsApp (`Comprovante_de_Transferencia Pix_01112026_200921.pdf`), com alto potencial de denúncia de spam e banimento da conta comercial.
* **Janela Temporal:**
  * **Primeiro disparo programado:** `2026-10-31 19:29:04` (Etapa 666).
  * **Último disparo programado:** `2026-11-05 12:27:05`.
  * **Outbox e Recibos:** **0 entregas realizadas** até o momento (`delivery_items = 0`).
* **Imutabilidade do Snapshot:** O CRM utiliza `CampaignPolicyLegacyMessages::hydrate` para ler do JSON congelado em `campaign_contact_stage_runs.stage_snapshot`. Alterações no banco de dados na tabela `messages` ou no manifesto JSON **não alteram esses 13 disparos pendentes**.

### 2.3. Solução Administrativa Desenhada (Aguardando Autorização)
Especificada em [P0-D10-ESTADO-ATUAL.md](relatorios/homologacao-v5/P0-D10-ESTADO-ATUAL.md): procedimento idempotente que cria tabela de backup (`campaign_stage_runs_p0_backup`) e transiciona os 13 runs pendentes para `status = 'skipped'`, com justificativa auditável, antes de 31/10/2026.

---

## 3. Síntese Editorial e de Contratos da Proposta V5 (WhatsApp)

A proposta V5 consolidada em `editorial/whatsapp-v5.draft.json` e refletida em [14-COPY-CANONICA-V5-CANDIDATA.md](14-COPY-CANONICA-V5-CANDIDATA.md) cumpre rigorosamente todos os critérios de engenharia e persuasão:

1. **T0 — Initial (Com e Sem Nome):**
   * Acolhimento natural, longevidade comprovada ("Estamos aqui desde 2011, fotografando histórias de família"), clareza de pacotes a partir de 2x R$ 98, pergunta de engajamento ("Prefere semana ou fim de semana?") e opt-out regulamentar `PARARNATAL2026`.
   * Imagem de pacotes com legenda ativa via `title` (150 caracteres Unicode).
   * PDF nomeado como `Proposta-Natal-AnoNovo-Evydencia-2026.pdf`.
   * Botões fixos na allowlist V4: `natal2026-reservar` (Catálogo), `natal2026-horarios` (Horários) e `natal2026-instagram` (Instagram). Checkout direto mantido em gate de código.
2. **D+1 — Contagem Regressiva Segura:**
   * Substituição do token dinâmico `{natal_countdown_dias_descricao}` por caption estática emocional literal: *"🥹 O tempo voa... mas as fotos desta fase ficam pra sempre. ❤️ / Que tal guardar esse Natal em família? 🎄"* (102 caracteres).
3. **D+5 — Alinhamento Fiel ao Vídeo:**
   * Legenda descritiva do pergaminho luminoso do duende/fada (127 caracteres), eliminando qualquer promessa de personagem presencial no estúdio.
4. **D+3 e D+4 — Experiência Real e Jogos:**
   * Inclusão do link da demonstração pública de jogos interativos (`https://jogos.fotosdenatal.com/`) em D+3.
   * Destaque para figurinos higienizados e bolhas de sabão reais em D+4.
5. **Limites Z-API e CRM:**
   * Todas as legendas de mídias estão entre 85 e 150 caracteres Unicode (limite máximo do CRM: 200 caracteres).
   * Formatação estritamente nativa (`*negrito*`, `_itálico_`), sem crases ou marcações de código.

---

## 4. Auditoria dos Canais Complementares

* **SMS (9 slots):** Mantidos integralmente em `preserve_existing_v4`. Mapeadas recomendações para futuras fases: atenuar densidade nos primeiros dias (+2h e D+1), converter mensagens para GSM-7 puro (sem emojis/acentos especiais para evitar tarifação de múltiplos segmentos) e unificar o opt-out com o WhatsApp.
* **Resend T0:** Mantido em `preserve_existing_t0` em modo texto (`body_mode: text`), sem anexo PDF (que trafega exclusivamente por WhatsApp). A jornada pós-compra foi desacoplada como fluxo de pedido.
* **SmartVoice:** Status classificado como `VOICE_STATUS_INDETERMINADO` e mantido em `no_activation`. Comprovado que eventos `delivered` no log do provedor não equivalem a ligações atendidas (`answered`). Registry no Git declara `enabled: false`. Nenhuma ligação autorizada sem conferência de CDRs e consentimento prévio.

---

## 5. Especificação do Publicador pelo Gestor (Sem VPS)

Projetada a arquitetura técnica para que o painel web do CRM Laravel permita à gestão publicar versões sem depender de comandos no terminal SSH:
* **Draft Isolado:** Rascunho com prévia visual por canal.
* **Validador de Contratos:** Bloqueio na interface para legendas > 200 caracteres ou botões fora da allowlist.
* **Diff Auditado:** Comparador antes/depois campo a campo.
* **Controle de Concorrência (CAS):** Trava otimista exigindo `expected_configuration_hash`.
* **Transação Atômica & Rollback:** Snapshot criptografado de reversão e atualização atômica do hash da proposta.

---

## 6. Índice dos Novos Relatórios de Homologação

Os 5 relatórios técnicos objetivos estão disponíveis na pasta `docs/EDITORIAL-V5-20261026/relatorios/homologacao-v5/`:
1. [V5-DECISAO-EDITORIAL.md](relatorios/homologacao-v5/V5-DECISAO-EDITORIAL.md): Tabela de decisão detalhada para os 19 papéis e métricas de caracteres.
2. [P0-D10-ESTADO-ATUAL.md](relatorios/homologacao-v5/P0-D10-ESTADO-ATUAL.md): Diagnóstico LIVE dos 13 agendamentos pendentes de D+10 e desenho da rotina de supressão.
3. [CANAIS-STATUS-EFETIVO.md](relatorios/homologacao-v5/CANAIS-STATUS-EFETIVO.md): Análise técnica de SMS, Resend e reconciliação CDR/Asterisk do SmartVoice.
4. [PUBLICADOR-GESTOR-MINIMO.md](relatorios/homologacao-v5/PUBLICADOR-GESTOR-MINIMO.md): Desenho arquitetural do publicador web no CRM.
5. [HOMOLOGACAO-GO-NOGO.md](relatorios/homologacao-v5/HOMOLOGACAO-GO-NOGO.md): Matriz executiva de portões de liberação.

---

## 7. Roteiro e Perguntas para Auditoria do ChatGPT

Solicitamos que o **ChatGPT** audite os seguintes aspectos e emita seu parecer e direcionamento estratégico:

### Bloco 1: Validação Técnica e Forense
1. **Auditoria dos 13 Agendamentos LIVE (P0 D+10):**
   * Você valida a conclusão de que alterar tabelas de mensagens ou o manifesto JSON não afeta os 13 disparos pendentes serializados em `stage_snapshot`?
   * O procedimento administrativo de cancelamento proposto (transição para `skipped` com tabela de backup) é seguro, auditável e suficiente para neutralizar o risco antes de 31/10/2026?

2. **Conformidade Z-API e WhatsApp:**
   * Você aprova a decisão de adotar caption estática em D+1 para mitigar a falha do worker em selecionar `fallback_title`?
   * As contagens de caracteres e o tom das mensagens atendem às melhores práticas para conversão mobile sem risco de classificação como spam?

### Bloco 2: Dicas e Recomendações para os Próximos Passos
1. **Transição para Produção:**
   * Qual deve ser o cronograma exato de execução recomendado para a equipe até a data limite de 31/10?
   * Como conduzir a aprovação comercial com a diretoria do estúdio de forma simples, permitindo homologar a copy sem expor a complexidade técnica dos snapshots?
   * Você recomenda realizar um teste canário com número de homologação interno antes de liberar a V5 para toda a base? Quais portões devem ser conferidos nesse canário?

2. **Otimização Multicanal:**
   * Que conselho prático você dá para o estúdio sobre o áudio humano do fotógrafo (17s)? Devemos encaixá-lo imediatamente em T0/H+1 ou manter a esteira atual estável no início?
   * Em relação à telefonia SmartVoice, qual o momento ideal para reavaliar a ativação, considerando que a prioridade máxima agora é a conversão via WhatsApp?

---

**FIM DO DOSSIÊ 17 — PRONTO PARA AUDITORIA INDEPENDENTE E DIRECIONAMENTO ESTRATÉGICO DO CHATGPT.**
