# 13 — Missão local Antigravity: fechar o kit V5 e viabilizar gestor sem VPS

**Data:** 26/10/2026 · **Escopo:** PREPARAÇÃO, AUDITORIA e PREVIEW · **Sem envio, deploy, alteração SQL ou mutação no gestor.**

## O que mudou no Git desde sua consolidação local
Os materiais `10`, `11`, `12` e `scripts/review_v4_assets.py` foram **adicionados** sem sobrescrever seus scripts recém-editados no Windows. Isso é deliberado: o walkthrough dizia que correções de PowerShell/UTF-8 e quatro relatórios existiam localmente, **mas ainda não confirmou seus SHAs publicados no Git**. Não sobrescreva trabalho local; sincronize com cuidado.

1. `docs/EDITORIAL-V5-20261026/10-PUBLICACAO-PELO-GESTOR-SEM-VPS.md`
2. `docs/EDITORIAL-V5-20261026/11-AUDITORIA-DE-ANEXOS-E-AUDIOS.md`
3. `docs/EDITORIAL-V5-20261026/12-SMS-RESEND-VOZ-TELEFONE.md`
4. `scripts/review_v4_assets.py`

## Execução local objetiva — prioridade mais alta
```powershell
git status --short
git diff --stat
git fetch origin main
# Somente se árvore limpa ou alterações locais cuidadosamente conciliadas:
git pull --ff-only origin main
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/run-editorial-checks.ps1
python scripts/review_v4_assets.py --self-test
```
Se houver alterações locais, **não** executar reset --hard/checkout -f nem sobrescrever os rascunhos. Se a rotina `run-editorial-checks.ps1` falhar apenas por terminal Windows, corrigir somente a causa, e publicar correções sanitizadas no `propostanatal`.

## Tarefa A — terminar a inspeção de mídia sem repetir downloads
- Usar o manifesto V4 local do repositório CRM e `scratch/video_cache/` já auditado com 8 MP4, sem downloads redundantes.
- Rodar `python scripts/review_v4_assets.py --manifest "<path-v4>" --media-dir "<pasta-da-midia-real>"`. Este script checa os 11 arquivos distintos da V4. Arquivos não presentes LOCALMENTE geram `MISSING_LOCAL_FILE`, não significa que sumiram da CDN.
- Para imagem/PDF T0 que ainda não estão no cache, obter cópia autorizada do CRM local (sem expor senha/URL assinada em Git). Abrir e registrar todos os preços, parcelas, páginas, 1/5/10 jogos, galeria, direitos de uso, QR e mobilidade. Se necessário, projetar PDF leve para 4G, sem reduzir legibilidade.
- Verificar D+10 PDF: se conteúdo ainda imita comprovante Pix, elevar P0 para operação autorizada e verificar quantos runs realmente continuam pendentes. **Rótulo `blocked` no JSON não cancela envio**.
- Ouvir os 8 vídeos integralmente; SHA+3 frames comprovam bytes mas não qualidade de áudio, copy sobreposta ou clareza da cena. Registrar cada inconformidade por role/timestamp; não OCR em massa, não publicar fotos de crianças/cliente.

## Tarefa B — pauta de arquivos novos, sem introduzir etapas no manifesto
- Gravar 3 tomadas de áudio humano do fotógrafo (14–22 s) conforme roteiro no documento 11. Escolher 1. Publicar/masterizar áudio somente em armazenamento apropriado e com autorização.
- Preparar roteiro Papa Noel no ElevenLabs v4 `[laughs]`, `[excited]`, `[whispers]`, mas não afirmar que áudio gravado é telefonema.
- Criar proposta de vídeo real 15–20 s de jogos+galeria no smartphone para D+3, e clip 12–18 s de medalhinha/certificado/livrinho/botão mágico de bolhas para D+4. Gravar só se mídia existente insuficiente.
- Planejar **substituição**, não adição, de materiais para evitar sobrecarga. `Natal2026ProposalManifest::validate` fixa quantidade/tipos de mensagens na V4; novo áudio em T0/H+1 implica revisão de contrato/política antes de entrar no fluxo.
- Não alegar Papai/Mamãe Noel presentes no ensaio quando o vídeo apenas apresenta personagem promocional.

## Tarefa C — SMS/Resend/SmartVoice, foco em status real e não reinventar
- Inventário SMS: relacionar 9 papéis, provider, status e recibos agregados (queued/accepted/sent/delivered/failed); confirmar número oficial nos links e política de STOP. **Não editar** 9 ações V4 enquanto WhatsApp não estiver aprovado.
- Resend: verificar a ação T0 efetiva `body_mode=text`, elegibilidade e logs; não tratar e-mail após fechar compra como parte do T0.
- SmartVoice: direção relata ligação quase homologada; checar efetivamente `CRM flag + registry implantado + WAV/áudio + caller ID + consentimento + janela + DTMF + ledger`. Registry versionado consultado no Git marca 5 `enabled:false`, mas pode divergir da configuração implantada. Não reiniciar auditoria geral de tronco, foco somente nos cinco `natal2026-proposta-{1h,1d,5d,7d,10d}`.
- Sinalizar contato H+1 por WhatsApp e chamada H+1 simultâneos: não adicionar áudio WhatsApp no mesmo momento se a pessoa já recebeu mídia ou ligação; controlar elegibilidade/opt-out.

## Tarefa D — preparar publicação segura pelo gestor, sem VPS recorrente
- Conferir GET e prévia das campanhas `/gestao/campanhas/415/fluxo` e `/416/fluxo` e os três botões oficiais.
- Validar que `MessageController.update` NÃO oferece aprovação/hash atômica, e `InspectNatalProposalReadiness` rejeita configuração divergente. **Não clicar salvar** até procedimento estar definido.
- Comparar duas soluções: (A) provisionador existente `--dry-run` + aplicação controlada única por ambiente autorizado, sem novo código; (B) um **único hotfix** criando editor/publicador seguro com revisão+CAS+snapshot/rollback, permitindo futuras alterações só pelo painel. Especificar custo/risco/contrato/rollback, sem implementar no CRM ainda.
- Checkout direto no botão T0 `https://reserva.fotosdenatal.com/` exige alteração do contrato de URLs; **não** substituir `https://evydencia.com/catalogo/natal` agora.
- Para D+1 usar caption estática se não houver prova do token resolvido no runtime. `fallback_title` do JSON é apenas documentação.
- Antes do 1º próximo agendamento D+10, revalidar P0 e propor cancelamento/supressão **específica e reversível**, nunca UPDATE em massa.

## Entregáveis locais mínimos (não enviar automaticamente para Git público)
1. `reports/<timestamp>/ANEXOS-T0-VIDEOS-D10.md`: o que existe, SHA, tamanho, página/clip, o que falta, mídia recomendada, ordem comercial.
2. `reports/<timestamp>/CANAL-SMS-EMAIL-VOZ.md`: status e entrega agregados, com evidência e limites.
3. `reports/<timestamp>/PLANO-PAINEL-SEM-VPS.md`: campos seguros de consulta, como publicar V5 sem romper hash, rota de rollback, impactos de snapshots.
4. `reports/<timestamp>/PROPOSTA-V5-APROVACAO.md`: um diff do T0 até D+10, com copy curta, nomes de mídia e espaços que precisam de aprovação.

Não repetir toda análise do Google Drive; aproveitar documentos 00–09. Não gerar dez alternativas de copy; entregar a melhor por etapa e um fallback quando necessário.

## Prompt pronto para colar
> Atualize hypeneural/propostanatal **sem descartar** os trabalhos locais; leia docs/EDITORIAL-V5-20261026/10-PUBLICACAO-PELO-GESTOR-SEM-VPS.md, 11-AUDITORIA-DE-ANEXOS-E-AUDIOS.md, 12-SMS-RESEND-VOZ-TELEFONE.md e este documento. Rode o bundle editorial já existente e scripts/review_v4_assets.py --self-test. Com o manifesto V4 e scratch/video_cache do relatório 20261026-123430, confira os 11 assets (PDF/Imagem T0, 8 MP4 e PDF D+10) por basename/SHA, abrindo visivelmente o PDF e ouvindo vídeos selecionados. Não rebaixe arquivos comprovadamente íntegros. Priorize a mídia que falta: um áudio real curto do fotógrafo, demonstração jogos/galeria e vídeo real de medalha, certificado, livrinho, botão mágico; propostas de substituição, **não envio adicional**. Confirme 9 SMS e e-mail T0 por leitura e logs agregados e 5 chamadas SmartVoice no registry efetivo/áudios/ledger, sem alterar seus estados. Aponte exatamente o que o painel do gestor pode visualizar hoje e por que não deve salvar 415/416 antes de reaprovação de hash. Prepare opções de publicador sem VPS recorrente e critério de canário, mantendo D+10 Pix como P0. Entregue quatro relatórios locais objetivos, suba apenas scripts/copy genérica sem dados privados, e finalize como PRONTO PARA APROVAÇÃO ou BLOQUEADO, nunca PRODUÇÃO PUBLICADA. Não executar deploy, apply, doctor, SQL UPDATE nem mensagens reais.
