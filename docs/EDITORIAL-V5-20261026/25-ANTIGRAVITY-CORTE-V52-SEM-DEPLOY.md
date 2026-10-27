# 25 — Prompt Antigravity URGENTE: no-deploy feasibility + migração de conteúdo V4→V5.2

**Objetivo do operador:** colocar já no ar os textos humanizados V5.2 nas campanhas 415 e 416, **preferencialmente SEM deploy/release**, preservando todos os clientes que já estão na V4 e sem agravar o PDF D+10. **Prioridade: realizar uma verificação objetiva e tomar uma decisão, sem loops.**

## INFORMAÇÃO CRÍTICA JÁ PROVADA NO GITHUB — NÃO RECOMEÇAR AUDITORIA
- CRM main `60ba4e2de4fecee290f9b51ac85fac679e220315`, `Natal2026ProposalCampaignProvisioner::contentPlan`: só passa V2→V4, **não V4→V5.2**. Rodar o mesmo V4 como `no_op` é possível; V4 diferente dá `provision_r4_base_mismatch`. Fazer rollback à V2 falha se V4 tiver execuções referenciando catálogo.
- `MessageController.update → MessageService::fillMessage`: grava mensagem avulsa; **não** recalcula automaticamente `readiness.manifest.configuration_hash`, fingerprint de saúde nem attestation. `CheckProposalFlowHealth::assertFreshReady` pode bloquear **novas** propostas após editar painel ou SQL.
- `NatalProposalExecutionPlan::captureNew` e `CampaignPolicyStageLifecycle::materializePolicy`: propostas ativas V4 capturam revision, 19 roles, mensagem/asset e horários em snapshots. `CampaignPolicyLegacyMessages` hidrata do snapshot no dispatch. Old V4 deve permanecer V4 até o fim, exceto supressões de segurança; reconsultar exceções sem frozen plan.
- P0 D+10 continua vivo mesmo depois de uma nova versão: relatório de 26/10 16:15:25Z mostrou 13 `pending` (9+4), 1 `skipped`. O número ATUAL é desconhecido. Existe risco de novos runs congelarem o anexo Pix se V4 ficar ativa.
- `editorial/whatsapp-v5.draft.json` **não é** manifesto executável Laravel. Inclui saudação nativa e candidato countdown D+1 **não executável**. `schema_version:4` existente não pode ser editado e reaplicado sobre V4 ativa.

## FASE 1 — DESCOBERTA em LEITURA, uma única rodada
1. `git status --short`, `git fetch origin main`, comparar **o build efetivamente implantado** na VPS/gestor com CRM main citada. Não presumir versão de produção igual à main.
2. Ler `docs/EDITORIAL-V5-20261026/24-URGENCIA-PUBLICAR-SEM-DEPLOY-FORENSE.md`, `14-COPY-CANONICA-V5-CANDIDATA.md`, `21-VARIAVEIS-SAUDACAO-COUNTDOWN-AUDITORIA.md`.
3. Em leitura autenticada, verificar `proposal_flows` para slug `natal`: status, revision, `content_profile_version`, health, configuração/hash, attestation, journal (sem exportar segredos), mapping 415/416 e ID de stage inicial ativo. Consultar quantidade agregada de V4 planejados/frozen, exceções legadas, e runs/outbox de D+10 por etapa/role.
4. Checar se há no **build implantado** uma função administrativa já autorizada que publique uma NOVA revisão mantendo clones/novas IDs/expected hash/rollback (não há evidência na main). **Não confundir editor de `messages` com publicador.**
5. Se autorizado a executar CLI read-only, rodar SOMENTE o comando de **dry-run do manifesto V4 atual**:
```bash
php artisan proposals:provision-natal-2026 --manifest=docs/active/natal-2026-proposta/manifesto-natal-2026.v4.json --dry-run --json
```
Registrar `errors` e `operation`; se o manifesto local não corresponder ao estado implantado, não improvisar `--apply`. NUNCA usar o JSON editorial V5.2 como argumento `--manifest`.
6. Validar em clone local `python scripts/validate_editorial.py`, `python scripts/render_canonical_v5.py --check`, `python scripts/plan_manager_v5.py --check` e variáveis de saudação no Laravel. Sem SMS, Z-API ou canário real nesta fase.

## FASE 2 — DECISÃO AUTOMÁTICA, SEM EXCEÇÕES
**SE** existir no build instalado **um publicador VERSIONADO V4→nova revisão**, que confirme em preview:
- 19 roles/etapas por campanha preservados e mídias idênticas;
- novos IDs de catálogo para futuro, antigos intactos e fingerprints de execução preservados;
- CAS `expected_hash` + snapshot cifrado/rollback e evidência de runtime/release com attestation;
- texto com/sem `{primeiro_nome}`, T0 `{saudacao}`, D+1 estático, D+3 STOP e D+4 10 motivos;
- nenhum PDF Pix em novos planos **e nenhum envio elegível antigo perigoso**;
- nenhuma exclusão/pausa inadvertida de quem já está agendado;
- status de autorização do operador compatível com a operação;
**ENTÃO:** produzir plano de corte e evidência para autorização operacional da publicação. Somente se todas as provas e autorização específica de produção estiverem satisfeitas executar a capacidade **já existente**, preservando snapshot e rechecando health/attestation de imediato. Se qualquer prova falhar, **ABORTAR**. Não imitar um publicador usando Tinker ou SQL manual.

**SE NÃO EXISTIR** — esperado pela main — **NÃO tentar editar pelo painel / SQL ou forçar novo hash**. Entregar decisão `NO_DEPLOY_PATH_BLOCKED_BY_R4_CONTRACT`. Abrir uma única branch/PR com o **menor hotfix de engenharia** para suportar sucessor da V4, reaproveitando o provisionador/captação de snapshots já instalados, com revisão imutável, transação, CAS, snapshot, plano de rollback e testes focados. **NÃO FAZER DEPLOY**, pois o usuário condicionou a operação à ausência de release; explicar que o primeiro deploy pontual será necessário para liberar a V5.2 de maneira segura.

## FASE 3 — Prova do impacto em clientes antigos
Criar matriz agregada SEM PII:
- Cliente com `natal_execution_plan.version=4` + stage IDs antigos, recebido antes do corte: **V4 congelada**.
- Cliente pendente/available com `stage_snapshot.legacy_messages` e `configuration_hash` antigos: **V4 congelada**.
- Cliente cujo novo agendamento foi criado após o corte autorizado com revisão nova: **V5.2 nova**.
- Contato sem plano/etapa legada: **QUARENTENA** para auditoria específica antes de qualquer conclusão.
- Pedido comprado/STOP: supressão prevalece, mesmo sobre snapshots.

Comparar hashes de payload e horários antes/depois **sem enviar mensagens reais nem usar números de clientes**. A nova publicação não deve alterar os bytes dos snapshots históricos. Não pressupor que pausar o fluxo seja neutro; verificar efeito no `active()`/scheduler.

## FASE 4 — P0 D+10, processo independente
Reconsultar os pendentes e outbox em modo read-only, mais novos runs criados desde 26/10. Propor contenção específica e reversível do PDF Pix em **duas frentes**: impedir novas programações e suprimir os itens antigos ainda elegíveis sob worker lock. Não executar comandos DML ad hoc e não declarar P0 resolvido a partir de `blocked_do_not_publish` no Git.

## Entregável final mínimo, sem loops
Escrever apenas:
1. `reports/<timestamp>/DECISAO-SEM-DEPLOY.md`: build LIVE; há publicador versionado? sim/não + prova de código/endpoint; saída do dry-run oficial; impeditivo exato se houver.
2. `reports/<timestamp>/MATRIZ-V4-FROZEN-V52-NOVOS.md`: contagens agregadas/hashes, dados privados fora do Git.
3. `reports/<timestamp>/P0-D10-OUTBOX.md`: elegíveis atuais, novos agendamentos, tempo real BRT, solução segura sem executar.
4. `reports/<timestamp>/APLICACAO-V52-PLANO.md`: diff mínimo para 415/416; se sem deploy impossível, URL do PR proposto, testes e mínimo passo de rollout futuro.

**NÃO fazer** `UPDATE messages`, `UPDATE proposal_flows.settings`, `health_fingerprint`, `configuration_hash` manual, `--apply` com V4 diferente, rollback de V4 referenciada, `proposals:doctor` forçado, script de Tinker sem contrato, enviar Z-API/SMS/voz ou parar toda a régua. **Não implementar alterações em produção à revelia da condição sem deploy.**

Concluir com o veredito real: `GO_EXISTING_VERSIONED_PUBLISHER` com provas suficientes **ou** `NO_DEPLOY_PATH_BLOCKED_BY_R4_CONTRACT`. Não dizer “V5.2 publicada” por causa de edição avulsa no banco.
