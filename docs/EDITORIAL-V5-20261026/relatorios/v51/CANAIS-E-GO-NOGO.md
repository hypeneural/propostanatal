# 4. Situação Multicanal e Matriz GO / NO-GO V5.1 — Natal 2026

**Data da Auditoria:** 2026-10-26T17:00:44Z  
**Versão Auditada:** Proposta Natal V5.1  
**Status Oficial:** **`V51_EDITORIAL_READY_FOR_DIRECTOR_APPROVAL / P0_PRODUCTION_NO_GO`**  

---

## 1. Situação dos Canais Complementares

### 1.1. Canal SMS (9 Slots Homologados na V4)
* **Status:** Mantidos integralmente em **`preserve_existing_v4`**. Nenhuma mensagem ou cronograma foi alterado.
* **Avaliação de Risco:** Alta intensidade inicial (+2h, D+1, D+2). Recomenda-se para fases futuras atenuar os primeiros disparos para evitar fadiga do contato.
* **Economia e Codificação GSM-7:** A redação dos SMS deve ser mantida livre de emojis ou acentos atípicos para evitar o chaveamento automático para UCS-2 (Unicode), o que reduziria o limite de 160 para 70 caracteres e triplicaria o custo de envio na operadora Velip.
* **Opt-Out Cruzado:** Confirmar que o descadastro voluntário `PARARNATAL2026` via WhatsApp suspende automaticamente a esteira de SMS do mesmo lead.

### 1.2. Canal Resend (E-mail Transacional de Orçamento T0)
* **Status:** Mantido em **`preserve_existing_t0`**.
* **Modo de Envio:** Opera em `body_mode: text`, com o assunto `Sua proposta Natal + Ano Novo 2026 — Evydência`.
* **Fato Forense:** **Não há anexo de PDF via e-mail no V4.** O arquivo de 9,43 MiB é entregue exclusivamente pelo WhatsApp, preservando a reputação de IP do domínio e a taxa de entrega na caixa de entrada.
* **Desacoplamento Pós-Compra:** A confirmação de compra, contrato, recibo e instruções de como chegar (`https://ir.fotosdenatal.com/`) pertencem a outro fluxo de pedido (`order.completed`), não devendo ser misturados com a proposta de orçamento.

### 1.3. Canal SmartVoice (Telefonia Real VoIP / Asterisk / Discador)
* **Status:** Classificado como **`VOICE_STATUS_INDETERMINADO`** e mantido em **`preserve_existing_configuration_no_activation`**.
* **Reconciliação Técnica Crucial:**
  * Eventos `delivered` no log do provedor indicam apenas entrega do pacote SIP à operadora de telecomunicações.
  * Isso **NÃO comprova** que a chamada foi atendida por uma pessoa (`answered`), que a mensagem foi ouvida ou que houve interação humana.
* **Registry e WAVs:** O repositório Git do SmartDialer declara `enabled: false`. Nenhuma ligação deve ser acionada sem confirmação do registry efetivo em runtime, conferência de CDRs, detecção de secretária eletrônica (AMD) e consentimento prévio para chamadas telefônicas.
* **Recomendação:** Implementação de rota somente leitura `GET /gestao/natal/proposta/voice-health` para permitir à diretoria auditar a saúde da telefonia pelo navegador sem SSH na VPS.

### 1.4. Mídias e Demonstrações da Experiência
* **8 Vídeos MP4 Locais:** Hashes SHA-256 e bytes conferidos contra o catálogo do manifesto aprovado.
* **Visualização Mobile T0:**
  * Imagem dos pacotes (`natal-2026-pacotes-r1.png`, 2,64 MiB): legível em viewport mobile de 390px, com valores de 2x R$ 98 e diferenciação clara entre 1, 5 e 10 jogos por pacote.
  * PDF da proposta (`natal2026-rev357-v1-proposta.pdf`, 9,43 MiB): íntegro e apresentando a proposta completa.
* **Sites de Demonstração:**
  * Jogos personalizados: `https://jogos.fotosdenatal.com/` (funcional e integrado em D+3).
  * Galeria e localização: `https://fotosdenatal.com/` e `https://ir.fotosdenatal.com/`.
* **Novos Materiais:** Roteiro de 17 segundos para áudio real do fotógrafo estruturado no Documento 11 como proposta de substituição futura de contatos repetitivos, sem criar novo slot automático na régua.

---

## 2. Matriz Consolidada de Portões de Homologação

| Portão / Item | Condição Técnica de Aceite | Status | Ação Requerida / Parecer |
|---|---|:---:|---|
| **1. Copy WhatsApp V5.1** | Opt-out diferido para D+3, 10 motivos em D+4, T0/H+1/H+90/D+2 limpos | **GO** | Pronto para apresentação e assinatura da diretoria comercial. |
| **2. Botão REPLY H+1** | Preservação obrigatória de `PARARNATAL2026` pelo contrato V4 | **GO** | Mantido intacto no rascunho V5.1. |
| **3. Legenda Estática D+1** | Remoção de dependência de token dinâmico no worker | **GO** | Validado bit a bit com texto emocional seguro. |
| **4. Alinhamento D+5** | Coerência com a cena real do pergaminho iluminado | **GO** | Sem falsas promessas de personagens presenciais. |
| **5. Allowlist de Botões T0** | URLs oficiais exatas: catálogo, horários e Instagram | **GO** | Conformidade 100% com `Natal2026ProposalManifest.php`. |
| **6. Mídias Aprovadas** | 8 MP4, 1 PNG e 2 PDFs conferidos por SHA-256 e bytes | **GO** | Ativos locais íntegros. |
| **7. Canais Preservados** | 9 SMS, Resend T0 e 5 slots SmartVoice sem mutações | **GO** | Inalterados conforme diretriz do projeto. |
| **8. Portão P0 D+10** | Contenção dupla: supressão dos 13 pendentes e barreira a novos leads V4 | **NO-GO** | **Bloqueio de Produção.** Requer execução de comando seguro antes de 31/10. |
| **9. Importador pelo Gestor** | Publicação atômica com CAS hash, rollback e sem SSH | **NO-GO** | Requer desenvolvimento prévio de endpoint no CRM antes de liberar uso web. |
| **10. Release em Produção** | Provisionamento seguro com zero pendências P0 | **NO-GO** | Bloqueado deliberadamente (`scripts/check_forensic_gates.py` sai código 2). |

---

## 3. Veredito Final Oficial

```
================================================================================
STATUS: V51_EDITORIAL_READY_FOR_DIRECTOR_APPROVAL / P0_PRODUCTION_NO_GO
================================================================================
```

* **HOMOLOGAÇÃO EDITORIAL (GO):** A copy V5.1 está aprovada tecnicamente, enxuta, humanizada, com alta persuasão comercial e 100% aderente aos limites de 200 caracteres e contratos de Z-API e CRM.
* **PRODUÇÃO (NO-GO):** Nenhuma publicação ou disparo em produção pode ser realizado enquanto persistirem os **13 agendamentos pendentes congelados** de D+10 e a ausência de trava para novos leads na V4 ativa.
