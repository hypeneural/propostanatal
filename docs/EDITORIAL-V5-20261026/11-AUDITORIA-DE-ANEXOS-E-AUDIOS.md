# 11 — Auditoria dos anexos atuais e novos materiais a produzir

**Data:** 26/10/2026 · **Fonte:** manifesto V4 aprovado do CRM `natal2026-content-v4-20261021` + relato do Antigravity; nenhum arquivo binário foi importado para este Git.

## Catálogo real do manifesto (variantes compartilham os mesmos arquivos)
O fluxo aprovado associa **11 mídias distintas**: 8 MP4, 1 imagem e 2 PDFs. Não há `audio` como `message` no catálogo núcleo V4; isso não contradiz os cinco slots SmartVoice, que são telefonia separada.

| Etapa | Tipo | Basename V4 | Tamanho original | SHA-256 esperado | Critério |
|---|---|---|---:|---|---|
| `initial` | image | `natal-2026-pacotes-r1.png` | 2.64 MiB | `3b7e333d56b3…` | **REVISÃO VISUAL T0** |
| `initial` | attachment | `natal2026-rev357-v1-proposta.pdf` | 9.43 MiB | `463a3c26807b…` | **REVISÃO VISUAL T0** |
| `whatsapp_h1` | video | `natal-2026-1hora-r3.mp4` | 18.90 MiB | `ff7123f9cc05…` | Mídia existente: legenda/áudio |
| `whatsapp_90min` | video | `natal-2026-90min-r3.mp4` | 5.86 MiB | `7b286ad9a3f9…` | Mídia existente: legenda/áudio |
| `whatsapp_d2` | video | `natal-2026-2dias-r3.mp4` | 10.44 MiB | `8b9d24e7f892…` | Mídia existente: legenda/áudio |
| `whatsapp_d4` | video | `natal-2026-4dias-r3.mp4` | 11.25 MiB | `b41c33b6d8b3…` | Mídia existente: legenda/áudio |
| `whatsapp_d1` | video | `natal2026-rev357-v1-d01.mp4` | 14.52 MiB | `ed9aa188911b…` | Mídia existente: legenda/áudio |
| `whatsapp_d3` | video | `natal2026-rev357-v1-jogos.mp4` | 16.90 MiB | `2ac284bf7ad3…` | Mídia existente: legenda/áudio |
| `whatsapp_d5` | video | `natal2026-rev357-v1-d05.mp4` | 8.36 MiB | `c31886fc4b71…` | Mídia existente: legenda/áudio |
| `whatsapp_d10` | video | `natal2026-rev357-v1-d10.mp4` | 4.13 MiB | `25dbbe82f7d5…` | Mídia existente: legenda/áudio |
| `document_d10` | attachment | `natal2026-rev357-v1-d10-document.pdf` | 0.08 MiB | `24656a3902f3…` | **P0 BLOQUEAR** |

**Limites do que foi provado:** Antigravity relatou que os **8 MP4 locais** correspondem em bytes e SHA-256 ao manifesto, e extraiu frames em 15%/50%/85%. Isso NÃO garante que todos foram ouvidos até o final, que são acessíveis no celular do lead ou que o conteúdo é correto para anúncio. Não há cópia binária desses MP4, PDF ou imagem anexada ao repositório público nem acessível nesta revisão. Tentativa independente de abrir os arquivos pela URL pública falhou por indisponibilidade de fetch; não interpretar como offline no navegador real.

## P0 — PDF de D+10
- `natal2026-rev357-v1-d10-document.pdf` continua no manifesto aprovado com título de comprovante Pix. Houve relato de flyer visual com aparência de transação financeira. Não trocar só o `fileName`; é necessário revisar conteúdo **integral do binário** e suspender/suprimir de modo autorizado, inclusive execuções com snapshot anterior.
- Último relatório Antigravity: **12** execuções pendentes em 26/10. Retestar apenas o estado atual; **não** criar script genérico `UPDATE` que ignore status, recebedor, idempotência e histórico.

## P1 — Imagem dos pacotes e PDF T0: o que verificar
1. **Imagem (2,64 MiB):** 3 pacotes, preço/entrada, 1/5/10 jogos conforme contrato vigente, duas decorações, ano 2026, fotos legíveis em tela de 390px de largura, marca correta `Evydência` com Y e botão visual não confundível com URL clicável.
2. **PDF (9,43 MiB):** renderizar cada página no notebook; checar páginas antigas, preços desatualizados, personagens não inclusos, condição de pagamento, calendário de entregas, QR/link que funcione no celular e composição Natal + Ano Novo.
3. **Tamanho:** imagem e PDF são aceitáveis como base de qualidade, mas PDF de 9,43 MiB pode ser pesado no 4G. Avaliar variante otimizada de 2–4 MiB **somente** se mantiver fontes/fotos legíveis, links e alta qualidade e passar no contrato de mídia/manifesto. Otimizar imagem para <1,5 MiB em WebP/JPEG quando adequado e aceito pela rota/tipo CRM; não mudar formato automaticamente.
4. Validar se todo pacote realmente recebe jogos e galeria, medalhinha, certificado, livrinho de colorir e efeitos interativos; quando divergirem site/PDF/checkout/estoque, corrigir a comunicação comercial a partir da regra aprovada. Não afirmar personagem físico incluído.
5. Não precisa produzir PDF novo antes de encontrar um problema factual ou de legibilidade. **Melhoria prioritária:** legenda curta `cenarios_image.title` e botão `Ver horários`.

## P1 — Oito vídeos: prioridade de melhoria sem refazer tudo
| Vídeo | Interpretação dos frames reportados | Próxima ação |
|---|---|---|
| H+1 | criança e personagens numa Vila natalina | Legenda de emoção; NÃO prometer personagens incluídos |
| H+90 | cena com crianças / reserva | Legenda curta + CTA não repetitivo |
| D+1 | vídeo emocional Natal | Aprovar título sem token cru; fallback literal se não homologar contador |
| D+2 | criança/cenário Réveillon | Mostrar benefício do segundo cenário |
| D+3 | demonstração de jogos personalizados | Manter vídeo; validar URL de demo e galeria no mobile, privacidade dos exemplos |
| D+4 | figurino e bolinhas de sabão | Legenda compatível; filmar botão mágico separado somente se faltarem frames reais |
| D+5 | personagem teatral com pergaminho iluminado | Legenda V5 corrigida para o pergaminho; não alegar presença física incluída |
| D+10 | personagem com aceno | Caption acolhedora; sem urgência fictícia |

## NOVOS MATERIAIS: esforço × impacto

| Prioridade | Material novo | Duração/formato sugerido | Fala/ação | Encaixe (sem aumentar régua automaticamente) |
|---|---|---|---|---|
| **P1** | **Áudio real do fotógrafo ou da equipe** | 14–22s; WAV/MP3 master, depois OGG/Opus conforme teste | Acolher e explicar “2 cenários + PDF e horários” como pessoa real | T0 ou H+1 **substituindo** item repetitivo; na V4 não há áudio no manifesto e exigirá novo contrato de publicação |
| **P1** | **Vídeo dos jogos + galeria demo** | 15–22s, mobile 9:16, legenda externa curta | Captura real tocando jogos/galeria; foto de exemplo autorizada | D+3, pode substituir vídeo existente se for inferior |
| **P1** | **Microvídeo da experiência infantil** | 12–18s, mobile 9:16 | Recepção → botão mágico → bolhas/luzes → medalhinha/certificado/livrinho | D+4 ou H+1 substituindo mídia; depende de disponibilidade real |
| P2 | Áudio gravado do Papai Noel | 12–18s, voz teatral autorizada, sem nome da criança | Convite lúdico sem falsificar ligação real ou presença em pacote | D+3/D+5 apenas se não duplicar chamadas autorizadas; exige nova etapa ou troca expressa de item |
| P2 | Vídeo curto da equipe desde 2011 | 12–18s, rosto do fotógrafo, estúdio real | Mostrar confiança/atendimento e endereço em Tijucas | Alternativa D+5 se pergaminho não estiver coerente com propósito comercial |
| P2 | Foto real de medalhinha/certificado/livro | Imagem vertical ou quadrada nítida | Exibir os três itens e disponibilidade | Melhor no site/PDF ou dentro de mídia D+4, não adicionar T0 obrigatório |

**NÃO publicar arte fake de benefício real**: fotografar os objetos/efeitos verdadeiros do estúdio e usar somente imagem autorizada de crianças/clientes. Imagens novas devem ser geradas/editadas fora do Git quando houver pedido específico de criação e aprovação.

## Roteiro curto para gravação sua, fotógrafo (aprox. 17 segundos)
> “Oi! Aqui é do Estúdio Evydência, em Tijucas. Que bom ter você por aqui! Preparei uma Vila de Natal muito especial, com cenários de Natal e Ano Novo na mesma sessão. Dá uma olhada nos pacotes que te mandei e, se quiser, eu te ajudo a escolher o melhor horário.”

**Por que real:** a própria voz do fotógrafo pode aumentar credibilidade. Gravar sem música alta, num local silencioso, tom acolhedor e sorrindo, sem pressa. Gravar três tentativas curtas e escolher a mais natural. Não afirmar personalização individual se o arquivo for enviado para toda a base.

## Recado do Papai Noel para WhatsApp (opcional, ElevenLabs v4)
Texto para a ferramenta de voz — **não** é caption:
```text
[laughs] Ho, ho, ho!
[excited] Olá, família! Sou o Papai Noel, com um recadinho muito especial.
[whispers] Uma Vila de Natal encantada está esperando vocês em Tijucas...
[excited] Espero ver muitos sorrisos por lá!
```
Usar voz de personagem/locutor com permissão. O Eleven v4 suporta tags de atuação como `[laughs]` e `[whispers]`; testar pronúncia Evydência/Tijucas, emoções e duração. Não dizer que o Papai Noel **ligou pessoalmente** quando for áudio via WhatsApp.

## O que a Z-API e o CRM já suportam
- Z-API `/send-audio` aceita link público do áudio ou base64; opcionais `waveform`, `viewOnce`, `delayTyping`. Docs oficiais https://z-api-docs.vercel.app/message/send-message-audio .
- `MessageRequest` no CRM aceita `file_audio` `mp3, opus, ogg, oga, acc`; tipo `audio` existe em `CampaignMessageService`.
- **NÃO** existe nova entrada `audio` no núcleo V4, que tem papéis, quantidade e ordenação estritos. Portanto, upload no gestor ≠ inclusão automática num slot da proposta.
- SmartVoice faz chamada telefônica pela infraestrutura dedicada, e precisa de áudio/registro/habilitação e ledger próprios. Preservar a série atual até homologação.

## Como validar localmente com pouquíssimos testes
1. Rodar `python scripts/review_v4_assets.py --manifest <manifesto-v4-local> --media-dir <pasta-de-ativos-reais>`. Checar SHA/tamanho por todos os 11 assets únicos; arquivos faltantes ficam `MISSING_LOCAL`, não são automaticamente inexistentes na CDN.
2. Abrir o PDF e a imagem no celular 390px; registrar página, preço, comparação e problemas. Não gerar PDF novo sem justificativa.
3. Assistir e escutar integralmente SOMENTE clipes selecionados de risco: D+3, D+4, D+5, D+10 (SHA já amostrado no Antigravity).
4. Ouvir o áudio do fotógrafo em celular antes de associar ao CRM; confirmar codec, duração, tamanho e que `viewOnce` fica desligado.
5. Documentar aprovação da equipe e links de mídia em pasta privada; não versionar PII nem fotos de clientes no repositório público.
