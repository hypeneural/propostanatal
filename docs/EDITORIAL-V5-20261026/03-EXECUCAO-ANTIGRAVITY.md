# 03 — Execução cirúrgica Antigravity | revisar V5 sem tocar no CRM
**26/10/2026 — scope EDITORIAL_ONLY.** Fonte principal `editorial/whatsapp-v5.draft.json`; leitura humana `01-CADENCIA-WHATSAPP.md`; roteiro ElevenLabs `02-ROTEIROS-ELEVEN-V4.md`; fatos `00-FONTES-E-CLAIMS.md`.

## Passo 0: clone e verificação local
```powershell
git pull --ff-only origin main
python scripts/validate_editorial.py
php scripts/audit.php --self-test
```
Se o extrator local precisar de `.env.local`, usar somente no ambiente autorizado; jamais commitar artefatos do banco. Os três comandos acima não escrevem no CRM.

## Passo 1: comparar editorial vs runtime (somente leitura)
- Partir do inventory.json local mais recente (atualizar com `php scripts/audit.php` se autorizado) e reconstruir para campanhas 415 e 416 os IDs exatos de `stage_messages`, `messages`, `actions`.
- Não confiar em IDs de mídia do JSON sem cruzá-los com o snapshot do dia; JSON é sugestão de texto por `role`, nunca migração baseada em posição.
- Conferir `messages.type`, semântica `image/video.title -> caption`, `attachment.title -> fileName`, `action.message` e botões CSV/semicolon legados. A `description` do vídeo não chega como caption nesse caminho de envio.
- Conferir benefícios: preços e parcelamentos no checkout, galeria/jogos em todos os pacotes (direção informou; operacional pendente), certificado/medalhinha por pacote, integração de voz e disponibilidade real.
- Assistir integralmente aos 8 vídeos atuais e abrir imagem/PDF T0; não presumir que o que a legenda descreve aparece no frame. Criar um registro local `reports/<data>/AVALIACAO-MIDIAS.md`.
- Proteger dados de clientes, não citar ou armazenar telefone/CPF/nomes reais em commit do repositório público.

## Passo 2: aprovar primeiro T0, depois H+1...
Trabalhar na sequência:
1. `initial`: com/sem nome, imagem de pacotes, PDF e botões `Reservar agora`, `Ver horários`, `Ver no Instagram`.
2. `whatsapp_h1`: vídeo com visita pela Vila e convite natural.
3. `whatsapp_90min`: facilitar agendamento, avaliar se precisa de dois contatos no mesmo intervalo.
4. `whatsapp_d1`: memória afetiva e token de countdown, com fallback sem token.
5. `whatsapp_d2`: segundo cenário.
6. `whatsapp_d3`: demonstração de jogos e galeria, somente benefícios comprovados.
7. `whatsapp_d4`: figurinos, orientação e conforto.
8. `whatsapp_d5`: tradição desde 2011, fotos reais e reportagem com indicação de especial publicitário.
9. `whatsapp_d10`: acompanhamento gentil; documento Pix `document_d10` deve permanecer **P0 BLOQUEADO**, inclusive snapshots pendentes.
10. Só então rever as ações complementares SMS/e-mail/voz e agenda D+15/D+20, sem adicionar novos envios por padrão.

Fazer aprovação **um papel por vez** em `docs/EDITORIAL-V5-20261026` e gerar arquivo de decisão local. Evitar “versão final” sem homologação.

## Passo 3: decidir publicação sem deploy
- No CRM já existe o editor `/gestao/campanhas/{415|416}/fluxo`, **mas edição individual pode invalidar** `configuration_hash` do manifesto aprovado. NÃO salvar mensagens isoladas sem estratégia de aprovação.
- O CRM possui `php artisan proposals:provision-natal-2026 --manifest=<path-local> --dry-run --json` e modo `--apply --expect=<hash>` com snapshot/rollback. O `editorial/whatsapp-v5.draft.json` **NÃO** é um manifesto aceito por este provisionador; não passá-lo diretamente como `--manifest`.
- Antigravity deve construir o manifesto V5 completo e compatível a partir do V4 do CRM, preservando atributos/IDs, ações e horários não alterados; testar validação documental/contrato em ambiente isolado.
- Só executar dry-run contra production quando puder garantir apenas leitura e estiver no ambiente autorizado. Nunca executar `proposals:doctor` em AUDIT_ONLY porque pode persistir estado.
- Publicação real `--apply`, snapshot/rollback, ajustes no gestor, chamadas externas ou cancelamento de D+10 exigem autorização operacional específica, depois de verificação de resultados e mitigação de P0.

## Passo 4: confirmar prévias renderizadas antes de qualquer disparo
| Checagem | Aprovação necessária |
|---|---|
| T0 415 | nome válido resolve; preço/parcelamento e botões corretos |
| T0 416 | não existe `{primeiro_nome}` literal |
| Imagem/vídeos | captions via `title` correspondem à mídia real; hashtags e tags ElevenLabs ausentes |
| PDF T0 | recebido imediatamente; filename corresponde; PDF correto |
| D+1 | token countdown realmente substituído no instante planejado, senão fallback |
| D+3 | demo jogos/galeria sem prometer benefícios inexistentes |
| D+10 | arquivo Pix com envio bloqueado inclusive para snapshots |
| STOP/compra/resposta | direitos de opt-out, supressão e idempotência permanecem |
| Resend | manter e-mail T0 elegível, não criar e-mail de fechamento da compra sem disparador de pedido |
| SMS | nove slots preservados, não marcar todos como entregues sem recibos |
| SmartVoice | não alterar configuração nem afirmar que está habilitado somente por existir no CRM |

## Entregáveis para revisão final neste repositório
- `editorial/whatsapp-v5.draft.json`: alterações propostas por role, jamais credencial ou cliente.
- `docs/EDITORIAL-V5-20261026/01-CADENCIA-WHATSAPP.md`: textos curtos e de apoio com marcação WhatsApp.
- `docs/EDITORIAL-V5-20261026/02-ROTEIROS-ELEVEN-V4.md`: seis roteiros criativos de áudio.
- Relatório **local** `reports/<data>/DIFF-V4-V5.md`, com IDs, textos antes/depois, hash/risco, decisões e aceite. Relatório real não deve ser publicado automaticamente.
- `docs/EDITORIAL-V5-20261026/00-FONTES-E-CLAIMS.md`: controle da verdade comercial.

## Resultado esperado
Primeira entrega: `T0` completo e validado no preview sem disparo. Segunda: H+1 e H+90. Terceira: D+1 a D+10. Quarta: revisão multicanal. Não mudar disparos já agendados silenciosamente. Se o editor atual não tiver publicação segura, propor hotfix pequeno e único de gestão, **não implementar no CRM sem autorização**.

## Prompt curto para colar no Antigravity
> Atualize a main de hypeneural/propostanatal. Leia AGENTS.md, docs/EDITORIAL-V5-20261026/00-FONTES-E-CLAIMS.md, 01-CADENCIA-WHATSAPP.md, 02-ROTEIROS-ELEVEN-V4.md, 03-EXECUCAO-ANTIGRAVITY.md, e editorial/whatsapp-v5.draft.json. Rode python scripts/validate_editorial.py. Compare somente por leitura com inventário real das campanhas 415/416 e manifesto V4 no CRM. Verifique benefícios, botões, mídia e payload de caption, preserve SMS, Resend e SmartVoice. Analise a segurança de publicação/hash e snapshots. Comece a revisão por T0 e avance papel a papel até D+20, sem mudar o CRM, sem enviar mensagens e sem criar produção. Entregue diffs e dúvidas objetivas de aprovação; não rode deploy nem apply sem autorização. Trate PDF D+10 como bloqueio P0.
