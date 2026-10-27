> ⚠️ **ESCOPO CORRIGIDO EM 27/10/2026:** O Antigravity trabalha **exclusivamente** em `hypeneural/propostanatal` e nunca executa comandos/inspeções/commits no `evydencia-crm`. O CRM pertence somente a **ChatGPT/Codex**. O fluxo autoritativo atual é [documento 27](27-ANTIGRAVITY-SO-PROPOSTANATAL-CODEX-CRM.md), que substitui instruções conflitantes abaixo. A [PR CRM #435](https://github.com/hypeneural/evydencia-crm/pull/435) foi **fechada sem merge**; branch futura `codex/N-434-natal-v52-editorial-successor`. D+10 permite só um **PDF legítimo isolado**, sem texto/legenda; o documento histórico semelhante a comprovante Pix continua bloqueado. **Não reativar anexo enganoso.**

# 26 — PACOTE DE PUBLICAÇÃO V5.2 PRONTO NO GITHUB: handoff cirúrgico Antigravity

**27/10/2026 · BUILD-READY PARA VALIDAÇÃO LOCAL, NÃO DEPLOYED.**  
**CRM Issue:** https://github.com/hypeneural/evydencia-crm/issues/434  
**CRM PR DRAFT:** https://github.com/hypeneural/evydencia-crm/pull/435  
**Branch:** `antigravity/N-434-natal-v52-editorial-successor` (base `main` SHA `60ba4e2de4fecee290f9b51ac85fac679e220315`)  
**Copy canônica:** `editorial/whatsapp-v5.draft.json`  
**Script de preparação:** `scripts/prepare-v52-local.ps1`  
**Gerador de manifesto privado:** `scripts/build_v52_release_candidate.py`

## 1. Entrega efetiva nos dois repositórios

**No CRM (PR #435):**
- `Natal2026ProposalCampaignProvisioner::contentPlan` passa a aceitar sucessor R4→R4 aprovado a partir da revisão/hash da V4 **somente com fluxo pausado**, sem sobrescrever histórico; clones de 38 etapas e novas mensagens/ações; conserva CAS, lock, snapshot cifrado e rollback já existentes.
- `NatalProposalContentProfile` retém catálogo V2 e identifica todos os catálogos V4 substituídos, excluindo seus IDs apenas da seleção de **novos** agendamentos. `natal_execution_plan` e `stage_snapshot` já capturados continuam com conteúdo/horários antigos.
- `NatalUnsafeD10DocumentGuard` impede o anexo perigoso no caminho legacy Z-API pelo basename original exato ou título Pix, independentemente de o tipo estar como enum numérico `6` ou `attachment`. **É barreira final de envio; não prova cancelamento de outbox já requisitada ou entrega aceita.**
- Testes adicionados: `NatalProposalContentSuccessorTest.php`, `NatalUnsafeD10DocumentGuardTest.php`, casos de sucessor no `NatalProposalContentProvisionerTest.php`.
- **Nenhuma alteração de banco de produção, workers ou deploy foi feita.**

**No propostanatal:**
- Script gera **manifesto integral schema 4** local a partir do manifesto V4 versionado no clone privado do CRM + JSON V5.2. Mantém 19 etapas x 2, 9 SMS por campanha, Resend T0, três botões da V4, textos e captions coerentes com os MP4 reais, `{saudacao}` com/sem `{primeiro_nome}`, D+1 estático até canário, D+3 STOP e D+4 dez motivos.
- Stage obrigatório `document_d10`: na **nova revisão** troca PDF de aparência Pix por cópia do **PDF legítimo da proposta inicial** (mesmo SHA, bytes e MIME) e title. A V4 schema 4 obriga um documento nesse papel. Isso **evita o PDF perigoso novo**, porém repete a proposta no D+10; aprovação comercial explícita necessária. Os PDFs antigos seguem congelados e exigem análise de elegibilidade/outbox.
- `build_v52_release_candidate.py --self-test` está incluído no GitHub Actions, que testa invariantes **sintéticos**, sem acessar CRM privado.
- `prepare-v52-local.ps1` realiza check editorial, catálogo e scripts, depois lint PHP + PHPUnit focal ao receber `-ValidateCRM`. Pode gerar arquivo **privado e não sobrescrito**, sempre em draft por padrão. Marca aprovado apenas com duas flags de aprovação expressas; **não executa apply**.

## 2. Comandos exatos: uma rodada local sem deploy

No checkout do `propostanatal`, PowerShell:
```powershell
git fetch origin main
git pull --ff-only origin main
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/prepare-v52-local.ps1 -CrmRoot "C:\CAMINHO_REAL\crm"
```

No checkout CRM, **antes de qualquer merge**: seguir contrato `AGENTS.md`, `docs/AGENTS.override.md`, Issue #434 e proteção de ownership/worktrees; inspecionar o PR #435, comparar base/HEAD reais com GitHub. **Não descartar nenhuma mudança local.**

Para validar PHP/testes com branch #435 em worktree isolado e DB MySQL **SINTÉTICO `crm_agent_*`**, com configuração de teste isolada:
```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/prepare-v52-local.ps1 -CrmRoot "C:\CAMINHO_REAL\crm" -ValidateCRM
```
Os paths de arquivo no script PowerShell são relativos ao repo `propostanatal`, NÃO ao CRM. Se o PHPUnit exigir credenciais do banco sintético, usar a configuração de testes e nunca a credencial de produção.

**Obter hash:** ler SOMENTE `proposal_flows.settings.readiness.manifest.configuration_hash` na instância autorizada, após confirmar revision atual igual à `natal2026-content-v4-20261021`. NÃO usar `base_configuration_hash` guardado no arquivo original (ele pertence à V2, não é o hash da V4 em produção). Consultar state/health/attestation somente leitura. Ações operacionais sobre pausar flow precisam ser coordenadas com workers e podem afetar marketing em andamento.

Gerar candidato **DRAFT**, no diretório temporário privado e sem sobrescrita:
```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/prepare-v52-local.ps1 -CrmRoot "C:\CAMINHO_REAL\crm" -CurrentHash "HASH_V4_ATUAL_DE_64_HEX" -ManifestOutput "$env:TEMP\natal-v52-candidato.json"
```
> Comando em uma única linha. O caminho `C:\CAMINHO_REAL\crm` é ilustrativo.

Conferir `base_revision`, `base_configuration_hash`, os 30 campos textuais alterados (15 por variante), os 8 vídeos/captions, T0 PNG/PDF, D+10 PDF legítimo, hash e mídias existentes. **NÃO alterar `status` à mão.**

## 3. Portão antes de qualquer publicação

1. Aprovação comercial escrita da V5.2 e do *PDF legítimo repetido no D+10*.
2. Preflight P0: consultar LIVE runs 666/685 e anexos/payload/outbox/worker status, **não** apenas `pending`; evidência de zero itens perigosos ainda elegíveis e de nenhum envio perigoso já em voo. O relatório anterior 13 pendentes é de 26/10 e não prova estado atual.
3. PR #435 testado e aprovado por engenharia, HIGH local completo no SHA final, fonte/schema/estado produção reconciliados, backup restaurável, operador autorizado a merge/deploy. **Acesso à VPS é permitido apenas para uma janela controlada após autorização do release**, não rotina manual recorrente.
4. Snapshot antigo V4 e novos stage IDs devem ser disjuntos. Validar nova seleção por `approvedCoreStageIds`, `superseded_catalogues`, health e `configuration_hash`.
5. Só após essas provas gerar manifesto `approved` usando as flags `-CommercialApproved -P0Contained` (o script apenas verifica flags declaradas, não acessa a VPS para conferir). Não declarar P0 contido sem evidência.
6. Com o novo código implantado e estado operacional aprovado, dry-run do provisionador **instalado** sobre manifesto privado (não o JSON draft) para obter `state_hash`. O `--apply --expect` só na janela separadamente autorizada e após revalidar hash/estado. Não executar automaticamente nem manualmente antes de todos os gates.
7. Após aplicação: revalidar Doctor/readiness/attestation, 415/416 com número de teste consentido, anexos Z-API, botões, opt-out imediato e que nenhuma entrega legada perigosa escapou. Reverter usando snapshot correto somente com precondições satisfeitas.

## 4. No-GO que não devem virar atalhos
- Editar `messages.title/message` no gestor ou MySQL, forçar `configuration_hash`, sobrescrever snapshots antigos.
- Aplicar `--manifest` antes do código da PR #435 estar instalado; a main atual responde `provision_r4_base_mismatch`.
- Atualizar por IDs 1445–1476 extraídos de outubro sem reconciliar mapping live.
- Desativar/remover `PARARNATAL2026` H+1, habilitar áudio de fotógrafo/Eleven v4, alterar SMS/Resend/SmartVoice sem contrato.
- Enviar teste real antes do risco P0 estar contido.
- Rebaixar risco HIGH para QUICK por pressa ou interpretar GitHub Actions do repositório **editorial** como testes PHPUnit CRM.

## 5. Entregável esperado do Antigravity
Relatório conciso (sem PII) contendo: GH Issue/PR, base/HEAD exatos, nome dos testes e PASS/FAIL, hash aprovado atual (pode registrar prefixo em documento público), prova do plano V4 capturado e do novo catálogo V5.2 isolados, status P0 atual e evento de liberação autorizado, arquivo privado do manifesto gerado, `dry-run` com operation/diff/erros (quando aplicável), e veredito:
- `LOCAL_HOTFIX_VALIDATED_WAITING_RELEASE_AUTHORIZATION`, ou
- `BLOCKED_WITH_EXACT_REASON`.

**Foco:** máximo de trabalho já feito, mínima execução local; sem novos scripts improvisados.
