# 24 — Veredito forense urgente: publicar V5.2 sem deploy no CRM V4 ativo

**Data:** 27/10/2026 · **Escopo:** repositório CRM main `60ba4e2de4fecee290f9b51ac85fac679e220315` e editorial main `8dbd53d43f989c2d35718a72f98c4ecf21d94a97` verificados por código. O conector CRM LIVE disponibiliza prévia e envio oficial, NÃO função de publicação de catálogo. **Sem mutação real nesta auditoria.**

## Decisão técnica (corrige hipótese anterior)

**NÃO EXISTE CAMINHO NO CÓDIGO ATUAL COMPROVADAMENTE SEGURO PARA PUBLICAR UMA SEGUNDA V4/V5.2 SOBRE UMA V4 JÁ APROVADA, SEM DEPLOY/HOTFIX.** Isso é uma restrição específica e verificável, não uma preferência de processo. O usuário prefere sem deploy; se não houver uma capacidade operacional já implantada fora da main, será necessário **um único hotfix pequeno com liberação controlada** ou adiar a troca automática.

| Via | Resultado | Prova |
|---|---|---|
| Editar 415/416 pelo painel / `MessageController.update` | **NO-GO** para a proposta homologada | `MessageService::updateMessage` chama `fillMessage` e grava `messages` isoladamente; não atualiza revisão/hash/attestation. `CheckProposalFlowHealth::assertReady` revalida fingerprint e `InspectNatalProposalReadiness::inspectManifest` exige `configuration_hash` coerente. Pode bloquear **novas** propostas, mesmo que os textos apareçam salvos |
| `UPDATE messages...` no MySQL | **NO-GO** | Mesmo descompasso de hash + risco de escrever mensagens compartilhadas, caches, IDs errados e alterações não auditadas |
| Ajustar manualmente `proposal_flows.settings.readiness.manifest.configuration_hash` + Doctor | **NO-GO** | Não é publicação contratual; omite journal/snapshot/attestation e pode mascarar incoerências; health/attestation precisam do mesmo estado de runtime |
| Rodar `proposals:provision-natal-2026 --apply` com V5.2 | **NO-GO na main atual V4 ativa** | `Natal2026ProposalCampaignProvisioner::contentPlan` só suporta segunda execução **idêntica** como `no_op`; para conteúdo novo exige `content_profile_version === 2`, caso contrário `provision_r4_base_mismatch` + `provision_flow_must_be_paused`. JSON editorial V5.2 NÃO É manifesto provisionável |
| Importador web versionado existente | **NÃO COMPROVADO/ausente no código inspecionado** | Nenhuma função atual de `MessageController` faz release transacional com mapping, snapshots, CAS e reattestation. Verificar apenas se operador implantou funcionalidade além da main, em read-only |
| Pequeno hotfix **versionado** para troca de catálogo V4→V5 mantendo clones e histórico | **ÚNICA ROTA AUTOMÁTICA VIÁVEL CASO NÃO EXISTA FUNÇÃO JÁ IMPLANTADA** | Reaproveitar `writeContentClones`, `fenceContentExecutions`, `assertContentPreserved`, `snapshot` criptografado, hash/CAS e mapeamento; não reimplementar em SQL. **Exige deploy pontual** e autorização da gestão, contrária à preferência inicial mas mais segura |

## O que acontece com quem já está na campanha

`NatalProposalContentProfile::capture` fixa `revision` + `stage_ids` dentro de `natal_content_revision_v4` na transação inicial. `NatalProposalExecutionPlan::captureNew` salva `natal_execution_plan` em `campaign_scheduleds.suppression_snapshot` com **19 etapas e suas `legacy_messages` (conteúdos completos), actions e horários**. `CampaignPolicyStageLifecycle::materializePolicy` copia esse plano para cada `campaign_contact_stage_runs.stage_snapshot`; `CampaignPolicyLegacyMessages::forContext/hydrate` reconstitui mensagens do snapshot, NÃO do cadastro atualizado.

**Consequência:** para campanhas V4 corretamente capturadas, editar `messages.message/title` **não atualiza automaticamente o texto de quem já agendou**. O cliente pré-corte permanece V4, inclusive o PDF D+10 congelado, por isso o P0 continua **mesmo após o lançamento V5**. Para execuções antigas sem plano/snapshot ou em trilha de compatibilidade, há salvaguardas adicionais e a revisão por conjunto é obrigatória.

**Exceções importantes:** pausar etapa/ação, bloquear status e mudar fluxo/políticas pode impactar elegibilidade, mesmo com payload antigo congelado. A captura de plano foi projetada para preservar fila durante mudanças de versão, mas NÃO equivale a garantia operacional de entrega. Efeito da pausa administrativo no `proposal_flow.status`, workers e marketing suppression deve ser testado.

### Previsão por grupo — somente se houver PUBLICAÇÃO VERSIONADA segura

| Situação | Conteúdo esperado |
|---|---|
| Proposta agendada **antes** da troca, plano completo V4 capturado | Texto e anexos V4 até terminar, salvo supressões legítimas/erro operacional |
| Já existe `stage_run` V4, ainda `pending/available` | Snapshot legado V4; **não** vira V5.2 sozinho |
| Candidato registrado antes, mas sem plano nem frozen payload | Caso especial: averiguar origem e state antes de afirmar comportamento |
| Nova proposta agendada **após** o corte completo e o novo `mapping` aprovado | **Somente então** V5.2, se health/attestation/eligibilidade passarem |
| Contato com compra/STOP | Não deve receber cadência comercial, mesmo se já tinha plano congelado; validar supressão transversal |

## Risco P0 paralelo e prioritário

O extrato mais recente disponibilizado em `relatorios/homologacao-v5/P0-D10-ESTADO-ATUAL.md` foi de **26/10 16:15:25Z**, com **13 runs pending (9 campanha 415 e 4 campanha 416), +1 skipped**, **não atual**. Não é seguro acreditar que P0 tenha sido resolvido ao instalar V5.2. O PDF de aparência de comprovante Pix pode continuar em snapshots legados e em novos planos V4 ativos.

**Dois bloqueios:** (1) proteger **novos** agendamentos desse anexo específico; (2) suprimir/cancelar **somente** anexos ainda elegíveis nos runs congelados, com locks, in-flight/outbox, log e prova zero depois da execução. **Não** usar `CREATE TABLE AS SELECT` e `UPDATE ... status='skipped'` da versão rejeitada; não mexer manualmente em JSON de snapshot.

## Caminho de menor fricção para o Antigravity AGORA, read-only

1. Confirmar SHA/release instalada e estado atual do fluxo 415/416; verificar existência de editor-publicador **já implantado** com aprovação de hash (não há evidência dele na main). Não assumir que frontend editável suporta o contrato.
2. Revalidar em produção, somente leitura: `proposal_flows.settings.readiness.manifest.content_profile_version/revision/mapping/configuration_hash`, health/attestation, estado do `natal_2026_provisioning` journal e flow.status; nunca listar telefones/clientes nos relatórios do Git.
3. Gerar V5.2 completa a partir de `editorial/whatsapp-v5.draft.json`, com variantes 415/416 e **mesmos 19 papéis, mídias, SMS, e-mail e botões**, sem reativar documento D+10; não publicar artefato como `schema_version:4` sem versão contratual compatível.
4. Se CLI operacional autorizado já estiver presente: executar apenas **dry-run** do manifesto V4 vigente, para verificar identidade/no-op e a causa de qualquer mismatch. NÃO rodar `--apply` do manifesto novo sem verificador explícito de migração V4→V5.
5. Provar que todos os novos agendamentos passam por revisão de saúde (hash) e que os antigos estão congelados: uma amostra privada e **sem PII no Git** dos três grupos V4 antigo/capturado, V4 novo, testes sem agendamento real.
6. Preparar uma **única** alteração pontual no CRM que aceite publicação de **novo conteúdo a partir de V4**, versionando revisão e catálogo sem tocar nos IDs antigos, com snapshot + CAS, travas e verificação de integridade. Rodar testes focalizados e CI; não executá-la/implantá-la até autorização do operador.
7. Coordenar o lançamento com P0 e hora exata/stop de sessão. Se publicação sem deploy for requisito ABSOLUTO, **reportar BLOQUEADO COM PROVA**, NÃO compensar escrevendo hashes ou SQL manualmente.

## Checklist de aprovação de release quando permitido
- [ ] Novo método V4→V5.2 com prévia e hash esperado, sem reaproveitar nomes/IDs de mensagens antigas.
- [ ] Diffs textuais da abertura `{saudacao}` e `{primeiro_nome}`, captions D+1 estático (countdown só após canário), D+3 STOP, D+4 dez benefícios, D+10 vídeo sem Pix.
- [ ] V4 já capturadas mantidas com conteúdo original, exceto supressões D+10 necessárias.
- [ ] Zero novo run elegível do PDF enganoso; zero outbox perigosa legada elegível.
- [ ] Manter SMS/Resend/SmartVoice e contratos de botão; horários/escopos iguais.
- [ ] `InspectNatalProposalReadiness`, `CheckProposalFlowHealth` e runtime attestation passam **depois** da troca, não só durante dry-run.
- [ ] Um canário consentido (somente após P0), ambos 415/416, vídeo/pdf/CTA reais e STOP; validação independente de SMS/voz.
- [ ] Snapshot cifrado guardado em local privado e rollback testado SEM tocar no catálogo antigo referenciado.
- [ ] Nada de chamadas/WhatsApp para base durante implementação ou testes offline.

## Veredito
**NO-GO para atualização individual no painel ou SQL manual. NO-GO para aplicar outra V4 via CLI na main atual.** A main já possui infraestrutura de captura imutável e provisionamento seguro, mas sua função de contentPlan não permite segunda revisão de V4. **Não se consegue dar GO seguro para V5.2 em produção sem alguma capacidade adicional**; a rota prática é um hotfix mínimo versionado (quando autorizado) ou usar um publicador existente que consiga demonstrar os mesmos portões. Esta revisão não pediu/executou mutações na VPS.
