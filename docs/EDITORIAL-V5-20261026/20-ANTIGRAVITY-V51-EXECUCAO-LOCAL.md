# 20 — PROMPT ÚNICO ANTI-GRAVITY: homologação local V5.1 e preparação do gestor (26/10)

**Concluído no Git:** alteração de copy V5.1, política de opt-out diferido, lista de dez motivos em D+4, plano de importação sem DML, errata P0 D+10 e suíte de regressão. **Produção não alterada.**

## Missão principal
Usar o trabalho existente e finalizar a aprovação comercial e o procedimento técnico de publicação **sem ficar mexendo manualmente na VPS**, com mínima fricção e em uma rodada. A entrega no CRM, se necessária, é uma fase de engenharia **separada**, não autorizada por este prompt.

## 1. Sincronização segura e checagens
No clone local `hypeneural/propostanatal`:
```powershell
git status --short
git fetch origin main
# Se houver alterações locais, preserve-as e reconcilie o diff.
git pull --ff-only origin main
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/run-editorial-checks.ps1
python scripts/render_canonical_v5.py --check
python scripts/plan_manager_v5.py --check
python scripts/check_forensic_gates.py
```
**Aceite da suíte:** zero falhas offline; `production_release: NO_GO` é o resultado esperado. Nunca desbloquear teste removendo check de P0 ou mudando `--require-production-go`.

## 2. Ler apenas as referências atualizadas
1. `docs/EDITORIAL-V5-20261026/18-PARECER-DOSSIE17-E-IMPLEMENTACAO.md`
2. `docs/EDITORIAL-V5-20261026/19-HANDOFF-ANTIGRAVITY-GESTOR.md`
3. `docs/EDITORIAL-V5-20261026/14-COPY-CANONICA-V5-CANDIDATA.md`
4. `editorial/whatsapp-v5.draft.json`
5. `docs/EDITORIAL-V5-20261026/relatorios/homologacao-v5/P0-D10-ESTADO-ATUAL.md` (errata no topo)

O Dossiê 17 e os antigos relatórios de decisão são evidências **históricas**, não copy atual. Não reescrever tudo com base em texto antigo.

## 3. Validar comercialmente por etapas, sem gerar novos envios
- T0: saudação direta com/sem nome; PDF + imagem + 3 botões da V4, preço, dois cenários e tradição desde 2011. SEM rodapé textual STOP repetitivo.
- H+1: vídeo da Vila e pergunta; **preservar botão REPLY `PARARNATAL2026` exigido no contrato V4**. Não apagar/simular ausência de possibilidade de parar.
- H+90 e D+2: CTA curto, sem rodapé textual repetido.
- D+1: legenda estática já homologável offline.
- D+3: vídeo de jogos + URL demo + primeiro texto discreto “Se preferir parar os lembretes, responda PARARNATAL2026.” Conferir caption <= 200 e renderização mobile.
- D+4: vídeo dos figurinos/bolhas + **ação de texto de 10 motivos curtos**, CTA e STOP; uma ação existente, nenhum novo disparo. Rever se os 10 motivos estão comprovados em pacote/checkout/PDF (galeria, roupas, bolhas).
- D+5: pergaminho mágico, sem afirmar personagem presencial.
- D+10: vídeo acolhedor + STOP; **PDF com aparência de Pix continua PROIBIDO**.
- Responder STOP em qualquer instante deve interromper acompanhamentos comerciais conforme escopo, inclusive SMS/voz se aplicável. Validar mecanismo real de supressão e consentimento, sem presumir que primeira solicitação de orçamento autoriza 20 dias de mensagens.
- Urgência/escassez: só usar dia de fechamento ou disponibilidade extraída da agenda; não inventar percentuais, prazos ou cupons.

## 4. Auditoria do importador/gestor
Execute `python scripts/plan_manager_v5.py` para gerar na saída uma matriz de campo/etapa/campanha **NÃO EXECUTÁVEL**. Compare por páginas autenticadas GET das campanhas 415/416 e não use IDs históricos como alvo de escrita.
Documente qual parte o gestor já consegue visualizar (mensagens/botões/preview) e o que falta construir para importar sem SSH: rascunho isolado, identificadores reais por role, preview por canal, validação de contrato, CAS `expected_configuration_hash`, snapshot/rollback, trilha de auditoria e revalidação de readiness.

**NÃO SALVAR** direto na 415/416: `MessageController.update` não atualiza o hash do manifesto aprovado. A versão V5.1 deve ser integrada ao **manifesto completo aprovado** e publicada futuramente por provisionador; editor/importador web é evolução estrutural de outra missão.

## 5. P0 D+10 — priorizar solução CIRÚRGICA sem SQL ingênuo
A extração de 26/10 às 16:15:25Z encontrou **13 pending (9 na 415 + 4 na 416) e 1 skipped**. Não assumir esse número agora.
- Reconsultar uma única vez via acesso read-only autorizado status, snapshots, título/asset, in-flight/outbox e horários com fuso.
- Auditar a criação de NOVOS runs do PDF na V4 ainda ativa, além dos pendentes antigos.
- Remover definitivamente do plano o modelo `CREATE TABLE AS SELECT` + `UPDATE status='skipped'` em massa; foi rejeitado por segurança.
- Especificar operação administrativa por serviço CRM com locks/CAS, idempotência, logs, contenção de corrida com o worker e verificação antes/depois. **Não executar** sem autorização.
- Provar zero anexos Pix elegíveis e ausência de nova geração, preservando os SMS e o vídeo correto do D+10.

## 6. Canais e materiais complementares
Não reabrir pesquisa geral. Conferir apenas horários/recibos dos nove SMS, e-mail Resend T0, consentimento/STOP, e os cinco SmartVoice por registry efetivo/WAV/CDR. `delivered` de evento não é chamada atendida. Não ativar voz.
Ouvir os vídeos D+3, D+4 e D+5 se já estão no cache com hash. Sugerir áudio humano do fotógrafo e material de demonstração **somente como substituição futura de mídia**, não novo slot na régua.

## 7. Entregáveis locais mínimos
Criar APENAS:
1. `reports/<data>/V51-APROVACAO-COMERCIAL.md`: role, campo, mensagem final, claims confirmados, status APPROVE/HOLD.
2. `reports/<data>/P0-D10-TRAVA-SEGURA.md`: estado LIVE e especificação de contenção antiga+nova, sem DML.
3. `reports/<data>/GESTOR-IMPORT-DIFF.md`: por 415/416, role/message_role/campo/texto antigo/novo, IDs atuais lidos somente com GET, hash e restrições de botão/STOP; **não executar**.
4. `reports/<data>/CANAIS-E-GO-NOGO.md`: SMS/Resend/SmartVoice e portões claros.

Não publicar PII, nomes de clientes, número, tokens de mídia privada ou credenciais em Git. Apenas correções de código/roteiros genéricos depois de passar validações.

## Resultado final esperado
**`V51_EDITORIAL_READY_FOR_DIRECTOR_APPROVAL / P0_PRODUCTION_NO_GO`** ou **`BLOCKED`** indicando evidência. Não dizer “publicado no CRM” nem fazer deploy, `--apply`, `proposals:doctor`, SQL UPDATE, canário real, ligação/WhatsApp, SMS ou mudança de configuração nesta missão.
