# 16 — Antigravity: missão local cirúrgica após parecer do ChatGPT V5

**Data:** 26/10/2026 · **Uso:** copiar prompt para Antigravity no Windows · **Produção:** NO-GO.

## Contexto agora já resolvido no Git
1. D+1 foi trocado no **JSON canônico** para legenda estática, sem dependência de `fallback_title` em runtime.
2. `14-COPY-CANONICA-V5-CANDIDATA.md` deriva unicamente do JSON, com validador `scripts/render_canonical_v5.py --check`.
3. O relatório `AUDITORIA-FINAL.md` foi corrigido: **12 pending (8 na campanha 415 + 4 na 416), 1 skipped adicional**, valor apenas da extração histórica.
4. Contradição de voz reconciliada como `VOICE_STATUS_INDETERMINADO`: eventos `delivered` históricos não são CDR atendido; registry Git enabled=false não é registry implantado.
5. Relatórios datados, dossiê e diffs foram rotulados como históricos, NÃO copy executável.
6. `scripts/check_forensic_gates.py` checa deriva da copy gerada, P0 histórico e evento de voz, e **falha intencionalmente** para qualquer `--require-production-go`. Não desbloquear alterando guard.

## Comandos exatos — uma rodada, sem loops
```powershell
git status --short
git fetch origin main
# concilie/stash APENAS se houver alterações locais; nunca descartá-las
git pull --ff-only origin main
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/run-editorial-checks.ps1
python scripts/render_canonical_v5.py --check
python scripts/check_forensic_gates.py
```
Exigir `EDITORIAL CHECKS OK` e `offline_integrity=PASS`; `production_release=NO_GO` é esperado. Se o render acusar diferença, rodar `python scripts/render_canonical_v5.py --write` apenas **localmente**, revisar o diff, e subir o doc revisado junto ao JSON correspondente.

## Missão específica em cinco frentes
**A — Conteúdo aprovado:** ler doc 15 e doc 14 em ordem, revisar cada papel de T0 a D+10, verificar campanhas com/sem nome. Não copiar do Dossiê 13 ou de `relatorios/V5-COPY-REVIEW-ATUALIZADO.md` (históricos e divergentes). Marcar `APPROVE`/`FIX`/`HOLD` por role/campo.

**B — Anexos e experiência:** reutilizar cache local dos oito MP4; comparar SHA quando necessário e ouvir integralmente só D+3, D+4, D+5, D+10. Checar imagem/PDF T0 em celular, jogos demonstrativos, galeria, link de horários, autorização de imagem. Propor UM áudio de fotógrafo (14–22s) e UM microvídeo de jogos/bolhas se faltarem — não incluir sem ajustar contrato e cadência.

**C — P0 D+10 PRIORIDADE ABSOLUTA:** fazer UMA extração SELECT read-only atualizada do banco autorizado. Identificar **somente os runs ainda elegíveis com PDF de aparência Pix no snapshot**, não inferir que ainda são 12. Cruzar run ID, stage ID (666/685), status, scheduled_at com timezone, delivery/outbox e cancellation path suportado. **NÃO fazer UPDATE, não reescrever snapshots, não enviar e não apagar históricos**. Escrever plano com comando administrativo reversível/idempotente para aprovação técnica separada e prova de zero elegíveis após futura operação.

**D — SMS/Resend/SmartVoice:** não modificar nove SMS, e-mail T0 ou série de cinco chamadas. Consultar apenas contagens agregadas e provider statuses; cross-channel STOP/compra e limitação de frequência. Para voz, conferir **registry EFETIVO da instância**, presença e SHA dos WAV, Asterisk no discador remoto se houver acesso já autorizado, CDR/provider ACK/answered/retries; reconciliar `timeline.md` vs `AUDITORIA-FINAL.md` sem prometer ligação atendida. Não refazer auditoria inteira do tronco.

**E — Publicação pelo gestor sem VPS:** confirmar que o editor atual não faz publicação atômica com `configuration_hash`; registrar limitações. Preparar **uma especificação mínima de publicador** rascunho→preview→CAS expected_hash→snapshot/rollback→ativação e preservação de runs antigos, para eventual implementação autorizada no CRM. Enquanto inexistente, planejar publicação única com provisionador validado; **não salvar mensagens na 415/416 agora**.

## Entregáveis objetivos LOCAIS (não subir informações privadas)
- `reports/<timestamp>/V5-DECISAO-EDITORIAL.md`: uma linha por role, copy canônica, aprovado/hold/por quê; link da mídia.
- `reports/<timestamp>/P0-D10-ESTADO-ATUAL.md`: somente agregados sem telefone/nome, 8+4 como referência **histórica**, sem alegar fix de produção.
- `reports/<timestamp>/CANAIS-STATUS-EFETIVO.md`: SMS/e-mail/voz, flags/acks/delivery/answered por data.
- `reports/<timestamp>/PUBLICADOR-GESTOR-MINIMO.md`: proposta arquitetural sem programar CRM nem VPS.
- `reports/<timestamp>/HOMOLOGACAO-GO-NOGO.md`: passos restantes, dono e evidência; **GO apenas offline**.

## Prompt pronto para Antigravity
> Sincronize hypeneural/propostanatal preservando minhas mudanças locais. Leia docs/EDITORIAL-V5-20261026/15-PARECER-VALIDACAO-GO-NOGO.md, 14-COPY-CANONICA-V5-CANDIDATA.md e este arquivo 16. A ÚNICA fonte de copy editável é editorial/whatsapp-v5.draft.json; relatórios e dossiê 13 são históricos. Rode powershell -NoProfile -ExecutionPolicy Bypass -File scripts/run-editorial-checks.ps1; corrija apenas falhas verdadeiras. D+1 já tem caption estática. Extraia UMA vez estado read-only atual do banco para P0 D+10, distinguindo 12 pendentes históricos (8+4) de 1 skipped e cruzando snapshots/outbox e timezone. Prepare supressão administrativa específica, auditável e reversível para aprovação, SEM executar. Reaproveite os vídeos/frames já validados; confira PDF+imagem T0, D+3 jogos e galeria, D+4 bolhas, D+5 pergaminho, D+10. Verifique logs agregados SMS/Resend e voz: “delivered” não é ligação atendida; checar registry efetivo, WAV e CDR. Prepare diff único V5 por role e desenho de publicação pelo gestor com hash/CAS/snapshot, sem salvar na 415/416. Não execute --apply, doctor, deploy, SQL UPDATE, canário, envio ou chamada. Entregue só cinco relatórios locais objetivos, sem PII, e finalize em GO_OFFLINE_REVIEW_ONLY/PRODUCTION_NO_GO com bloqueios concretos. Só publique scripts e copy genérica após validar e reconciliar com main.
