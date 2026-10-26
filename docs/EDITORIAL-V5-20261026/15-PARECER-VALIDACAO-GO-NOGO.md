# 15 — Parecer técnico/comercial independente e liberação de homologação V5

**Data:** 26/10/2026 · **Estado:** `GO_OFFLINE_REVIEW_ONLY` · **Produção:** `NO_GO`.
**Este documento substitui APENAS as CONCLUSÕES editoriais antigas, não os dados brutos de extração dos relatórios.** Não autoriza escrita no CRM, envio Z-API, SMS, e-mail, voz, SSH ou alteração de snapshot.

## 1. Fontes efetivamente confrontadas
- Base comercial de 2024/25 e de 100 motivos: docs 00, 04, 08.
- **Fonte única de copy por role/campo:** `editorial/whatsapp-v5.draft.json` (status `DRAFT_NOT_APPROVED`).
- Visão para aprovação **gerada automaticamente:** `14-COPY-CANONICA-V5-CANDIDATA.md`; comando `python scripts/render_canonical_v5.py --check` impede drift.
- O dossiê 13 e `relatorios/V5-COPY-REVIEW-ATUALIZADO.md` são **artefatos históricos de revisão**, com textos divergentes em 6 de 8 legendas; NÃO copiar deles para o painel ou para o manifesto.
- `relatorios/AUDITORIA-FINAL.md` (dados da coleta em 26/10 12:34:30 com offset +00:00), `timeline.md`, `MIDIA-EVIDENCIAS.md`, `V5-DIFF-AUDITADO.md`, `GO-NOGO.md`.
- CRM Laravel main: `app/Services/Proposals/Natal2026ProposalManifest.php`, `InspectNatalProposalReadiness.php`, `CampaignPolicyLegacyMessages.php`, `MessageVariableService.php`; manifesto `docs/active/natal-2026-proposta/manifesto-natal-2026.v4.json`.
- Z-API: documentação oficial https://v2.developer.z-api.io/ e https://developer.z-api.io/; política de não spam, retorno inicial de aceite do provedor **não equivale** a delivered/read.
- SmartDialer main: `config/voice_flows/registry.json` versionado mostra enabled:false, **não** estado efetivo implantado.

## 2. Divergências corrigidas nesta passagem
| Ponto | Antes | Após correção |
|---|---|---|
| Fonte editorial | Dossiê 13, relatório de copy e JSON com 6/8 captions diferentes | JSON é **única fonte executável proposta**; doc 14 é gerado e comparado bit a bit por script. Relatórios antigos preservados como prova histórica |
| D+1 e countdown | Caption com token `{natal_countdown_dias_descricao}` e um `fallback_title` que o worker NÃO usa | A caption ativa **no draft** agora é literalmente: “🥹 O tempo voa... mas as fotos desta fase ficam pra sempre. ❤️ / Que tal guardar esse Natal em família? 🎄” sem token |
| D+10 P0 | Relatório dizia “12 = 7 + 4”; quadro mostrava 8 + 4 e 1 skipped | Errata em `AUDITORIA-FINAL.md`: **8 pending 415 + 4 pending 416 = 12 pending**, **1 skipped separado**, **apenas na data histórica** |
| Voz | Relatório negava chamadas, timeline indicava delivered | `VOICE_STATUS_INDETERMINADO`: delivered no ledger não prova chamada completada/atendida; registry em Git não prova runtime; conferir CDR e consentimento |
| Publicação | Editar cadastro e sugerir adoção imediata | Publicação versionada com hash/CAS/snapshot e rollback, não edição avulsa pelo gestor |
| Vídeos | “100% auditados frame a frame” | Oito MP4 tiveram **SHA/tamanho declarados** e amostras de 15/50/85%; conteúdo/áudio integral, direitos de imagem e renderização mobile requerem inspeção específica |

## 3. Parecer comercial da versão canônica
- **T0 — favorável:** abre com dois cenários, confiança desde 2011, preço inicial e pergunta natural; proposta PDF e imagem enviadas imediatamente. A versão 415 inclui `{primeiro_nome}` e a 416 é neutra. Confirmar 2x R$ 98 no checkout antes do envio.
- **H+1 e H+90 — ajustar pressão futura:** duas peças próximas, mais SMS H+2 e eventual voz H+1; NÃO adicionar mais um áudio automático nessa janela sem eliminar contato redundante.
- **D+1 — seguro como rascunho:** emoção e sem variável de contagem não homologada. O contador oficial do CRM calcula até **25/12**, não até última data do ensaio.
- **D+2 — adequado:** dois cenários; checar se ambos efetivamente aparecem no clipe.
- **D+3 — alto valor:** jogos personalizados e URL demo na caption. A demonstração pública de **até 10** não significa que todos os pacotes incluem dez; o catálogo apresenta 1/5/10.
- **D+4 — adequado:** figurinos e bolhas reais; medalhinha, certificado, livrinho e botão mágico podem ganhar vídeo próprio após validação de execução por pacote.
- **D+5 — adequado:** cena do pergaminho mágico conforme amostra relatada, sem prometer duendes presentes ao ensaio.
- **D+10 — acolhedor:** CTA para dúvidas e datas reais, sem escassez inventada; **documento Pix permanece proibido**.
- **Pós-compra:** encerrar régua comercial e iniciar agenda/retirada/galeria por evento real de pedido, sem misturar e-mail de proposta com e-mail de confirmação.

## 4. Conformidade técnica
- `image.title` e `video.title` alimentam a caption no caminho identificado em `CampaignMessageService`; `description` não a substitui. `attachment.title` vai como `fileName` e deve conter `.pdf`.
- **200 caracteres Unicode é limite do validador Laravel V4, não limite universal da Z-API**. Teste final em celular/versão real do provedor ainda obrigatório.
- `*negrito*`, `_itálico_`, quebras e 0–2 emojis nos modelos curtos são aceitáveis no padrão WhatsApp; parser precisa revalidar nome e STOP. Não enviar tags ElevenLabs no texto.
- Botões T0: IDs `natal2026-reservar`, `natal2026-horarios`, `natal2026-instagram`; URLs em ordem exata do V4. Checkout direto `reserva.fotosdenatal.com` **NÃO** é substituição autorizada no V4.
- Preservação de **9 SMS**, **Resend T0** e **5 slots de SmartVoice** é não-mutação, **não homologação de entrega**. Não reativar voz pelo registry ou painel sem consentimento/ledger.
- Z-API rejeita uso para spam, e aceite inicial na fila não demonstra entrega. Validar status `provider_accepted` → `delivered` → `read` por eventos reais e sem duplicações.

## 5. P0: regra de segurança operacional D+10
Relatório de 26/10 mostra **8 pending na etapa 666 (415) + 4 pending na etapa 685 (416) = 12**, com **1 skipped** adicional na 666. É um retrato temporal; o valor LIVE precisa ser reconsultado.
1. Revalidar com SELECT o estado atual de run, snapshot do título/PDF, envio/delivery e próximo horário com timezone; não exportar dados de clientes ao Git público.
2. Pela rotina administrativa **já suportada** ou a ser revisada e autorizada no CRM, cancelar/suprimir **somente o documento D+10** que ainda possa ser despachado, preservando o restante da experiência, status históricos, idempotência e recibos.
3. Nunca corrigir **apenas o nome** e continuar mandando um material que aparenta Pix. Não reescrever silenciosamente snapshots, nem executar UPDATE genérico.
4. Fazer reconsulta pós-operação e obter prova de **zero entregas ainda elegíveis** daquele documento, sem cancelar SMS/vídeo D+10 corretos.
5. Primeira data **registrada na coleta**: 31/10/2026, 19:29:04. Conferir fuso e agenda REAL para estabelecer prazo de resposta.

**A anotação `blocked_do_not_publish` no JSON não bloqueia os runs da produção.**

## 6. Portões e teste mínimo
| Portão | Estado atual | Próxima prova |
|---|---|---|
| Integridade do rascunho JSON e 19 roles | Pronto para check local | `python scripts/validate_editorial.py` |
| Identidade doc 14 ↔ JSON | Pronto para check local | `python scripts/render_canonical_v5.py --check` |
| Inconsistências históricas P0/voz | Errata documentada | `python scripts/check_forensic_gates.py` |
| Imagem, PDF e oito vídeos | SHA reportado; inspeção incompleta | Preview mobile, links, direitos, frames/timestamps/áudio dos clipes de risco |
| SMS, e-mail, voz | Configs preservadas | Logs por provedor, STOP, elegibilidade, efetivo SmartVoice e CDR |
| Execuções D+10 ainda elegíveis | **NO-GO** | Consulta e supressão autorizadas COM evidência |
| Manifesto V5 aplicável | **Não existe versão aprovada** | Preparar diff completo/validador, `--dry-run` CAS, rollback; só depois autorização |
| GO para homologação offline sem envio | **GO condicionado** | Check local, aprovação comercial dos claims |
| GO para canário/produção | **NO-GO** | Todos os portões de produção, receptor autorizado e controle antispam |

## 7. Execução recomendada SEM FRICÇÃO
No notebook local, com repo atualizado e sem descartar alterações locais:
```powershell
git status --short
git fetch origin main
git pull --ff-only origin main
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/run-editorial-checks.ps1
python scripts/render_canonical_v5.py --check
python scripts/check_forensic_gates.py
```
**Nunca interpretar saída 0 como homologação de produção.** Para verificar o bloqueio deliberado: `python scripts/check_forensic_gates.py --require-production-go` **DEVE sair 2**.

Saídas permitidas: rascunho canônico revisado, lista de claims homologáveis, dados atuais somente em pasta privada, proposta de supressão P0 e pequeno plano de publicador gerencial. Não publicar um manifesto em produção nem editar o banco com essa missão.

## Parecer
**Favorável à aprovação comercial de T0 e às legendas V5 canônicas como rascunho, com ajustes finais de material e consentimento. GO para homologação OFFLINE. NO-GO para qualquer disparo/republicação em produção até supressão D+10, reconciliação multicanal e procedimento de hash.**
