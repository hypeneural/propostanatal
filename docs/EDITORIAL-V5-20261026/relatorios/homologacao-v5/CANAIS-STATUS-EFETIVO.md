# Status Efetivo dos Canais Complementares: SMS, Resend e SmartVoice

**Data:** 2026-10-26T16:15:25Z  
**Escopo:** Canais integrados não-WhatsApp nas campanhas 415 e 416  
**Princípio Operacional:** Preservação estrita das configurações V4 homologadas; nenhuma ativação sem evidência técnica e consentimento.

---

## 1. Canal SMS — 9 Disparos Comerciais V4

### 1.1. Status Editorial
A proposta V5 mantém todos os 9 papéis de SMS sob a diretiva **`preserve_existing_v4`** em `editorial/whatsapp-v5.draft.json`. Nenhuma mensagem ou cronograma foi alterado.

### 1.2. Mapeamento dos 9 Slots e Avaliação de Risco

| Janela / Etapa | Horário Âncora | Argumento Atual V4 | Risco Identificado | Recomendação para Fase Posterior |
|---|:---:|---|---|---|
| **sms_h2** | +2h | "NÃO DEIXE PRA DEPOIS", urgência de atendimento | **P1: Alta pressão comercial** logo após T0, H+1 e H+90 | Avaliar supressão ou converter em suporte receptivo. |
| **sms_d1** | D+1 12:30 | 2 cenários na mesma sessão | Redundância com WhatsApp D+1 | Manter texto curto em GSM-7 puro: `EVYDENCIA: Natal e Ano Novo na mesma sessao. Veja valores: https://fotosdenatal.com` |
| **sms_d2** | D+2 19:30 | Fotos reveladas e calendário | Horário noturno e emoticon literal `<3` | Limpar caracteres para GSM-7 e ajustar envio para horário comercial. |
| **sms_d3** | D+3 09:45 | Vila de Natal e Instagram | Repetição do gancho de jogos do WhatsApp D+3 | Direcionar para o Instagram oficial `@estudioevydenciaa`. |
| **sms_d4** | D+4 13:30 | Roupas e acessórios | Excesso de frequência acumulada | Disparar somente se não houver resposta no WhatsApp. |
| **sms_d5** | D+5 18:45 | "HO HO HO!", atendimento | Tom caricato repetitivo com o vídeo do pergaminho | Avaliar supressão. |
| **sms_d10** | D+10 10:15 | "Já agendou?", dúvidas de pacotes | Bom suporte para indecisos | Manter como suporte sem escassez fictícia. |
| **sms_d15** | D+15 19:15 | O Natal passa, fotos ficam | Muito tardio na jornada | Manter somente se ainda houver datas válidas na agenda. |
| **sms_d20** | D+20 12:45 | "ÚLTIMO LEMBRETE!" | Urgência deve ser estritamente verdadeira | Disparar apenas se houver prazo limite real de fechamento da agenda. |

### 1.3. Requisitos Técnicos para Alteração de SMS
* **Codificação GSM-7:** A inclusão de emojis ou caracteres especiais força a codificação UCS-2 (Unicode), reduzindo a capacidade por segmento de 160 para 70 caracteres e triplicando o custo da rota Velip.
* **Opt-Out Integrado:** O descadastro `PARARNATAL2026` via WhatsApp deve suspender compulsoriamente os agendamentos de SMS do mesmo contato.

---

## 2. Canal Resend — E-mail Transacional de Orçamento (T0)

### 2.1. Status Editorial
* Mantido em **`preserve_existing_t0`**.
* No manifesto V4 aprovado, a ação de e-mail opera em **`body_mode: text`**, com o assunto `Sua proposta Natal + Ano Novo 2026 — Evydência`, contendo os 3 valores de pacotes e links.
* **Fato Forense Comprovado:** **Não há anexo de PDF via e-mail no V4.** O arquivo de 9,43 MiB trafega exclusivamente pelo WhatsApp, evitando bloqueios por filtros antispam e garantindo alta entregabilidade.

### 2.2. Desacoplamento da Jornada Pós-Compra
* O e-mail da etapa `initial` é uma proposta comercial de orçamento.
* O fluxo de confirmação pós-compra (voucher, orientações de endereço `https://ir.fotosdenatal.com/`, contrato e recibo) pertence a outra automação disparada por evento real de pagamento (`order.paid`), e não deve interferir na régua comercial de proposta.

---

## 3. Canal SmartVoice — Telefonia VoIP Real (Asterisk / Discador)

### 3.1. Reconciliação Técnica: "Delivered" vs. "Answered"
* O status de SmartVoice permanece formalmente classificado como **`VOICE_STATUS_INDETERMINADO`**.
* **Divergência Esclarecida:**
  * O log do provedor de SMS/Voz registra eventos como `delivered` (pacote SIP entregue à operadora de destino).
  * Isso **NÃO comprova** que a chamada foi atendida por uma pessoa (`answered`), que a gravação foi ouvida ou que o cliente interagiu.
* **Registry Versionado vs. Efetivo:**
  * No repositório Git `smartdialer-platform`, o arquivo `config/voice_flows/registry.json` declara `enabled: false` para os 5 fluxos de Natal.
  * O ambiente implantado na VPS pode conter configurações ativas locais.
  * **Conclusão:** Nenhuma ligação deve ser considerada ativa sem a verificação do registry efetivo em runtime e a consulta aos CDRs (Call Detail Records).

### 3.2. Os 5 Fluxos Mapeados no CRM

| Flow ID | Momento | Roteiro / Função Teatral | Requisito de Homologação |
|---|:---:|---|---|
| `natal2026-proposta-1h` | H+1 | Acolhimento breve (12–18s) com opção de transbordo humano | Áudio WAV nítido; respeitar horário comercial (09h–20h). |
| `natal2026-proposta-1d` | D+1 | Mensagem lúdica de Papai Noel | Não afirmar presença física de personagens no estúdio. |
| `natal2026-proposta-5d` | D+5 | Benefícios da Vila e esclarecimento de horários | Pausar se o lead já conversou com atendente humano. |
| `natal2026-proposta-7d` | D+7 | Convite de suporte sem insistência | Respeitar opt-out telefônico. |
| `natal2026-proposta-10d`| D+10 | Encerramento de régua | Disparar apenas se houver agenda aberta. |

### 3.3. Requisitos para Futuro GO Operacional de Voz
1. **Consentimento Específico:** Garantia de consentimento prévio do contato para chamadas automatizadas.
2. **Reconciliação de CDR:** Prova de taxa de atendimento, detecção de caixa postal (AMD) e duração média.
3. **Supressão Automática:** Flag `skip_if_converted = true` comprovada em banco.
4. **Endpoint de Prontidão no Painel:** Implementação recomendada da rota `GET /gestao/natal/proposta/voice-health` para permitir auditoria pelo navegador sem login na VPS.
