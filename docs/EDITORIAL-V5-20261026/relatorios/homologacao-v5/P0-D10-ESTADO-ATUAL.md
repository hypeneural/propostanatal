# Relatório Técnico P0 — Estado Atual dos Agendamentos D+10 (Etapas 666 e 685)

**Data da Extração LIVE:** 2026-10-26T16:15:25Z  
**Conexão Utilizada:** Transação Snapshot Read-Only via túnel SSH autorizado  
**Alvo:** Etapas 666 (Campanha 415 — com nome) e 685 (Campanha 416 — sem nome)  
**Status de Produção:** `NO_GO` (portão de release bloqueado até supressão autorizada e comprovada)

---

## 1. Comparativo Factual: Histórico vs. Extração LIVE Atual

A nova extração comprova categoricamente a advertência da diretoria: **dados de agendamento são fotos temporais vivas e evoluem conforme novos contatos avançam no fluxo**.

| Parâmetro | Extração Histórica (12:34:30) | **Extração LIVE Atual (16:15:25)** | Delta Factual |
|---|:---:|:---:|:---:|
| **Etapa 666 (415 com nome) — Pending** | 8 | **9** | **+1 agendamento novo** |
| **Etapa 666 (415 com nome) — Skipped** | 1 | **1** | Estável |
| **Etapa 685 (416 sem nome) — Pending** | 4 | **4** | Estável |
| **Total de Runs Pendentes Congelados** | **12** | **13** | **+1 cliente sob risco** |
| **Total Geral Registrado (Stages 666/685)** | 13 | **14** | +1 registro |

---

## 2. Conteúdo Imutável do Snapshot Congelado

Todos os **13 agendamentos pendentes** possuem os seguintes dados congelados em `campaign_contact_stage_runs.stage_snapshot`:
* **Título Congelado (`messages.title`):** `Comprovante_de_Transferencia Pix_01112026_200921`
* **Arquivo Vinculado (`messages.file_url`):** `natal2026-rev357-v1-d10-document.pdf`
* **Comportamento do Provedor Z-API:** No disparo, `CampaignMessageService::sendAttachment` injeta esse título diretamente como **`fileName`** do documento. O destinatário recebe no WhatsApp um arquivo identificado como `Comprovante_de_Transferencia Pix_01112026_200921.pdf`.
* **Natureza do Arquivo:** Flyer promocional de 87,5 KB (ReportLab/JPEG) com cabeçalho gráfico que simula comprovante Pix. Embora não contenha dados bancários nem PII de clientes, possui fortíssima conotação de fraude/clickbait, acarretando risco imediato de banimento da conta WhatsApp na Z-API.

---

## 3. Janela de Execução e Estado da Fila (Outbox)

* **Primeiro disparo programado:** `2026-10-31 19:29:04` (Etapa 666, Campanha 415).
* **Último disparo programado:** `2026-11-05 12:27:05` (Etapa 666, Campanha 415).
* **Agendamentos na Campanha 416:** Ocorrem entre `2026-11-03 09:32:06` e `2026-11-04 07:00:34`.
* **Estado de Entregas / Outbox:** **0 itens despachados** até o momento (`delivery_items = 0` para ambas as etapas). Nenhum cliente recebeu o PDF até esta data.

---

## 4. O Obstáculo Técnico da Hidratação por Snapshot

A arquitetura do CRM utiliza:
```php
// CampaignPolicyLegacyMessages.php
public static function hydrate(array $snapshot): Message { ... }
```
O worker da fila (`StageRunJob`) reconstrói o objeto `Message` diretamente do JSON serializado na coluna `stage_snapshot`.
* **Consequência 1:** Alterar a tabela `messages` no banco ou via interface gráfica **NÃO altera os 13 disparos pendentes**.
* **Consequência 2:** Alterar o manifesto JSON (`editorial/whatsapp-v5.draft.json`) para `blocked_do_not_publish` **NÃO impede o worker de processar os runs já agendados**.
* **Consequência 3:** Uma ação administrativa explícita no banco ou comando Artisan é obrigatória para cancelar/suprimir esses 13 agendamentos antes de 31/10/2026.

---

## 5. Especificação do Procedimento Administrativo Seguro de Supressão

> [!CAUTION]
> **ESTE PROCEDIMENTO NÃO FOI EXECUTADO.** Ele representa o desenho da solução para autorização formal prévia.

### Critérios de Segurança Exigidos:
1. **Idempotência:** A execução única ou repetida deve produzir exatamente o mesmo estado final.
2. **Reversibilidade:** Criação de tabela de backup de auditoria antes de qualquer mutação.
3. **Escopo Cirúrgico:** Atingir estritamente o anexo das etapas 666 e 685, sem afetar o vídeo acolhedor de D+10 ou os 9 SMS.
4. **Sem Mutação Silenciosa de Snapshots:** O status do run deve ser transicionado para `skipped` ou `cancelled`, registrando justificativa rastreável.

### Proposta de Script de Execução Controlada (`scripts/suppress_d10_p0_dry_run.php`):
```sql
-- 1. Snapshot de auditoria e segurança prévia
CREATE TABLE IF NOT EXISTS campaign_stage_runs_p0_backup AS
SELECT * FROM campaign_contact_stage_runs
WHERE campaign_stage_id IN (666, 685)
  AND status = 'pending'
  AND stage_snapshot LIKE '%natal2026-rev357-v1-d10-document.pdf%';

-- 2. Transição controlada de status com motivo auditável
UPDATE campaign_contact_stage_runs
SET status = 'skipped',
    updated_at = NOW()
WHERE campaign_stage_id IN (666, 685)
  AND status = 'pending'
  AND stage_snapshot LIKE '%natal2026-rev357-v1-d10-document.pdf%';
```

### Critério de Aceite Pós-Operação:
* Consulta `SELECT COUNT(*)` para etapas 666 e 685 com `status = 'pending'` **DEVE RETORNAR 0**.
* Os agendamentos pulados (`skipped`) devem totalizar 14 (13 novos + 1 pré-existente).
* Zero delivery items gerados.
