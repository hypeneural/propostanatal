> **GO/NOGO HISTÓRICO:** O relatório foi produzido antes da V5.1 editorial. A copy nova começa texto STOP em D+3, contém dez motivos em D+4 e preserva botão STOP H+1 do manifesto. O P0 não é só cancelar runs existentes: é impedir novas programações do PDF enganoso enquanto a V4 estiver ativa. Ver documentos [18 Parecer](../../18-PARECER-DOSSIE17-E-IMPLEMENTACAO.md) e [19 Importação](../../19-HANDOFF-ANTIGRAVITY-GESTOR.md). **Produção permanece NO-GO** até supressão/controles comprovados e publicação por hash aprovada.

# Matriz de Homologação Final e Veredito GO / NO-GO — Natal 2026

**Data:** 2026-10-26T16:15:25Z  
**Versão Auditada:** Proposta Natal V5 (`editorial/whatsapp-v5.draft.json`)  
**Status Oficial:** **`GO_OFFLINE_REVIEW_ONLY`** (Produção: **`NO_GO`**)

---

## 1. Matriz Consolidada de Portões de Decisão

| Portão / Componente | Critério Técnico de Aceite | Estado Atual | Parecer / Próximo Passo |
|---|---|:---:|---|
| **1. Integridade da Copy V5** | 19 papéis validados, opt-out em actions, paridade com Doc 14 | **PASS (GO)** | Validado por `scripts/render_canonical_v5.py --check` e `validate_editorial.py`. |
| **2. Contrato Z-API & CRM** | Legendas <= 200 caracteres Unicode, negrito/itálico nativos | **PASS (GO)** | Todas as captions entre 85 e 150 caracteres; zero rich text quebrado. |
| **3. Botões e URLs de T0** | Ordem exata: Catálogo, Horários, Instagram; IDs estáveis | **PASS (GO)** | Aprovado pela allowlist V4. Checkout direto mantido sob gate de código. |
| **4. Contagem Regressiva D+1** | Eliminação de dependência de token não testado no worker | **PASS (GO)** | Rascunho consolidado com legenda estática literal de alto apelo emocional. |
| **5. Cena Visual D+5** | Alinhamento com a filmagem real | **PASS (GO)** | Texto describe o pergaminho iluminado, sem alegar presença física de personagens. |
| **6. Ativos de Mídia** | 8 MP4 com hashes e bytes conferidos, PDF e imagem verificados | **PASS (GO)** | Mídias locais íntegras. Recomendada gravação do áudio real do fotógrafo (17s). |
| **7. Preservação de SMS e E-mail** | 9 SMS e Resend T0 inalterados em `preserve_existing` | **PASS (GO)** | Sem mutações de canal nesta rodada. |
| **8. Portão P0: Documento Pix D+10** | Supressão comprovada de agendamentos com flyer de aparência Pix | **NO-GO** | **13 pendentes LIVE**. Operação de cancelamento administrativo necessária antes de 31/10. |
| **9. Portão SmartVoice** | Comprovação de chamadas atendidas (`answered`), CDR e registry | **NO-GO** | Manter em `no_activation` até testes consentidos e painel de monitoramento. |
| **10. Release em Produção** | Provisionamento seguro com CAS hash, rollback e aprovação | **NO-GO** | Bloqueado deliberadamente (`scripts/check_forensic_gates.py` sai código 2). |

---

## 2. Veredito Executivo

### O que está APROVADO (GO):
1. A **proposta editorial V5 do WhatsApp** está 100% pronta, testada, validada e recomendada para homologação comercial pela diretoria.
2. Os textos são acolhedores, naturais, curtos para telas móveis e cumprem rigorosamente os limites do CRM Laravel e do provedor Z-API.
3. As dúvidas técnicas sobre o contador de D+1 e o vídeo de D+5 foram solucionadas com segurança.

### O que está BLOQUEADO (NO-GO):
1. **Disparos em Produção:** Qualquer publicação de novo manifesto está permanentemente bloqueada até a resolução do portão P0 de D+10.
2. **Edição Solta no Painel do CRM:** Operadores não devem alterar mensagens no painel atual para evitar divergência de `configuration_hash`.
3. **Telefonia SmartVoice:** Permanece desativada até validação do tronco, registry efetivo e CDRs de atendimento real.

---

## 3. Roteiro Crítico para Liberação de Produção

```
[1. Aprovação Comercial da Copy V5] 
               │
               ▼
[2. Autorização e Execução da Supressão P0 D+10 (13 runs)]
               │
               ▼
[3. Re-consulta SELECT comprovando 0 runs elegíveis do PDF Pix]
               │
               ▼
[4. Geração do Manifesto V5 com novo configuration_hash]
               │
               ▼
[5. Publicação Controlada via Provisionador com Rollback]
```
