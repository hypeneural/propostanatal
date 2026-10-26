# 2. Relatório Técnico P0 — Diagnóstico LIVE e Contenção Segura D+10

**Data da Extração LIVE:** 2026-10-26T17:00:44Z  
**Conexão Utilizada:** Transação Snapshot Read-Only via túnel SSH temporário  
**Alvo:** Etapas 666 (Campanha 415 — com nome) e 685 (Campanha 416 — sem nome)  
**Status de Liberação:** `P0_PRODUCTION_NO_GO` (nenhuma ação DML executada nesta missão)

---

## 1. Estado Atual dos Agendamentos LIVE (Etapas 666 e 685)

A reconsulta confirmou a estabilidade momentânea da fila em relação à medição das 16:15Z:

| Métrica / Parâmetro | Campanha 415 (Etapa 666) | Campanha 416 (Etapa 685) | Total Consolidado LIVE |
|---|:---:|:---:|:---:|
| **Agendamentos Pendentes (`status = 'pending'`)** | **9** | **4** | **13 clientes sob risco** |
| **Agendamentos Pulados (`status = 'skipped'`)** | 1 | 0 | 1 |
| **Agendamentos Concluídos (`completed`)** | 0 | 0 | 0 |
| **Total Geral Registrado** | 10 | 4 | 14 registros |
| **Primeiro Disparo Programado** | **2026-10-31 19:29:04** | 2026-11-03 09:32:06 | **31/10/2026 19:29:04** |
| **Último Disparo Programado** | 2026-11-05 12:27:05 | 2026-11-04 07:00:34 | 05/11/2026 12:27:05 |
| **Entregas Realizadas (`delivery_items`)** | 0 | 0 | **0 entregas até o momento** |

* **Conteúdo Congelado no Snapshot:** Título `Comprovante_de_Transferencia Pix_01112026_200921` associado ao arquivo `natal2026-rev357-v1-d10-document.pdf`.
* **Semântica Z-API:** Esse título é injetado como `fileName` no WhatsApp (`Comprovante_de_Transferencia Pix_01112026_200921.pdf`), com risco crítico de denúncia e banimento da linha de atendimento do estúdio.

---

## 2. O Risco Dinâmico: Geração de Novos Agendamentos pela V4 Ativa

> [!WARNING]
> Enquanto a campanha V4 permanecer ativa em produção recebendo novos leads, **o scheduler do CRM continua gerando novos agendamentos para D+10 com o PDF Pix congelado no snapshot**.
> Portanto, uma solução que trate apenas os 13 agendamentos existentes é insuficiente: é indispensável estancar a geração de novos agendamentos perigosos.

---

## 3. Motivos da Rejeição do Script SQL Anterior

A proposta preliminar de executar `CREATE TABLE AS SELECT ... UPDATE status='skipped'` foi **formalmente rejeitada** no Documento 18 pelos seguintes vícios de engenharia:
1. **Ausência de Transacionalidade:** `CREATE TABLE` causa commit implícito no MySQL, quebrando o isolamento transacional.
2. **Corrida com o Worker (Race Condition):** Se o job do worker estiver com lease ativo ou lock em um run no momento do `UPDATE`, pode ocorrer conflito de concorrência ou envio duplo.
3. **Inocuidade para Novos Leads:** O comando trataria apenas a foto do instante passado, deixando novos leads desprotegidos.
4. **Falta de Idempotência e Auditoria:** Não gera logs estruturados no subsistema de auditoria do Laravel CRM.

---

## 4. Desenho da Contenção Segura em Duas Frentes (Sem Mutações Agora)

Para neutralizar o risco P0 de forma definitiva e auditável, a engenharia do CRM deverá implementar a contenção em duas frentes:

```
                  ┌──────────────────────────────────────────────┐
                  │          CONTENÇÃO DUPLA DO P0 D+10          │
                  └──────────────────────┬───────────────────────┘
                                         │
                 ┌───────────────────────┴───────────────────────┐
                 ▼                                               ▼
   [FRENTE 1: BLOQUEIO NA ENTRADA]              [FRENTE 2: SUPRESSÃO TRANSACIONAL]
   Desativar ação de anexo nas etapas          Comando Artisan com Lock/CAS para
   666 e 685 para novos leads da V4            cancelar os 13 runs já congelados
```

### 4.1. Frente 1: Bloqueio na Entrada (Novos Leads)
* No CRM, desativar o despacho do anexo da etapa D+10 na campanha V4 ativa (ou aplicar guard no agendador de propostas que ignore o `attachment` de D+10).
* Isso garante que novos clientes que solicitarem proposta entre hoje e o lançamento da V5.1 não recebam o anexo Pix.

### 4.2. Frente 2: Supressão Transacional dos Runs Antigos Congelados
Implementação de um comando Artisan dedicado (`php artisan proposals:suppress-pix-attachment`):

```php
// Arquitetura recomendada para a engenharia do CRM:
DB::transaction(function () {
    // 1. Lock pessimista nos runs elegíveis
    $runs = CampaignContactStageRun::whereIn('campaign_stage_id', [666, 685])
        ->where('status', 'pending')
        ->where('stage_snapshot', 'LIKE', '%natal2026-rev357-v1-d10-document.pdf%')
        ->lockForUpdate()
        ->get();

    foreach ($runs as $run) {
        // 2. Verifica se não há lease ativo do worker
        if ($run->isCurrentlyLeased()) {
            throw new \RuntimeException("Run {$run->id} está em execução. Abortando.");
        }

        // 3. Registra auditoria antes da alteração
        AuditLog::record('stage_run_suppressed', [
            'run_id' => $run->id,
            'stage_id' => $run->campaign_stage_id,
            'reason' => 'P0_PIX_FLYER_SUPPRESSION',
            'original_snapshot' => $run->stage_snapshot,
        ]);

        // 4. Transiciona status para skipped de forma limpa
        $run->update([
            'status' => 'skipped',
            'skip_reason' => 'P0_PIX_FLYER_SUPPRESSION',
            'cancelled_at' => now(),
        ]);
    }
});
```

### 4.3. Critérios de Aceite Obrigatórios para Liberação:
1. `SELECT COUNT(*)` para etapas 666 e 685 com `status = 'pending'` deve retornar **rigorosamente 0**.
2. Prova de zero jobs na outbox ou fila de despacho para o arquivo `natal2026-rev357-v1-d10-document.pdf`.
3. Preservação integral do vídeo acolhedor de D+10 (`whatsapp_d10`) e dos SMS D+10, D+15 e D+20.
