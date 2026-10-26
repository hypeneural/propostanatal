# 01 — Cadência WhatsApp V5 | roteiro completo e curto
**RASCUNHO PARA APROVAÇÃO — não está em produção.** Referências: campanha 415 (com nome), 416 (sem nome), fluxo V4 de 19 papéis por variante. Não criar 19 etapas novas: **editar o conteúdo das existentes via publicação versionada**, quando validado. É uma revisão editorial, não um arquivo de provisionamento aprovado.

## Leitura rápida
- Todos os textos são **string literal do WhatsApp**; *negrito*, _itálico_, emojis e quebras de linha precisam sobreviver ao serializador.
- `message` = corpo para mensagem `action` ou `text`; `title` de `image`/`video` = **caption realmente enviada** no caminho legado. Não confiar no `description` como caption. `title` de `attachment` = **fileName**. Confirmar parser final.
- Não enviar a mesma frase na legenda e em mensagem consecutiva. Um CTA por etapa, com links somente em botão/ação já existente. `PARARNATAL2026` continua visível nos pontos contratuais e o registro de opt-out continua ativo.
- Regras antes de cada toque: consentimento adequado, não comprou, não pediu STOP, sem conversa humana em andamento se houver lógica de pausa homologada, destino válido, rate-limit e janela comercial. A V4 atual pode não implementar toda a pausa por resposta: **não inferir que “silêncio” significa elegibilidade**.
- O vídeo salvo no banco precisa ser assistido antes de escolher a legenda: descrições propostas abaixo são pautas, não descrição factual do frame.

## E01 / T0 | abertura + imagem pacotes + PDF: converter sem demora
**Objetivo:** identificar Tijucas, oferecer dois cenários e receber o orçamento completo imediatamente. 
**Sem alongar:** mensagem de 4–6 linhas; os detalhes ficam no PDF e na arte.

### 415 — `intro_cta.message`
```text
Oii, *{primeiro_nome}*! 🥰🎄
A *Vila de Natal* da Evydência, em Tijucas, está linda!

✨ *Natal + Ano Novo em uma única sessão.*
E a magia continua em casa, com fotos reveladas e lembranças personalizadas.

*Desde 2011 em Tijucas*, registrando histórias de família. ❤️

Já vou te mandar os pacotes e o PDF completo. Começam em *2x R$ 98*.

Vocês preferem *semana ou fim de semana*?

_Para parar lembretes, responda PARARNATAL2026._
```

### 416 — `intro_cta.message`
```text
Oii! 🥰🎄
A *Vila de Natal* da Evydência, em Tijucas, está linda!

✨ *Natal + Ano Novo em uma única sessão.*
E a magia continua em casa, com fotos reveladas e lembranças personalizadas.

*Desde 2011 em Tijucas*, registrando histórias de família. ❤️

Já vou te mandar os pacotes e o PDF completo. Começam em *2x R$ 98*.

Vocês preferem *semana ou fim de semana*?

_Para parar lembretes, responda PARARNATAL2026._
```

### T0 — `cenarios_image.title` (caption)
```text
🎁 *Três jeitos de viver esse Natal!*
Todos incluem os *dois cenários* e fotos reveladas.
Olha os pacotes na imagem e me diz qual combina com vocês. ❤️
```
Só manter “todos incluem calendário” quando SKU confirmar; ver fonte única.

### T0 — `proposta_pdf.title` (fileName, **não** caption)
```text
Proposta-Natal-AnoNovo-Evydencia-2026.pdf
```
Manter anexo atual e validá-lo visualmente; não renomear sem contrato de Z-API e manifesto. Se for necessário texto explicativo, inserir mensagem curta separada **sem duplicar** as ofertas.

### T0 — botões já existentes `intro_cta.buttons`
1. `Reservar agora` → https://reserva.fotosdenatal.com/ (**checkout**)
2. `Ver horários` → https://horarios.fotosdenatal.com/ (**agenda**)
3. `Ver no Instagram` → https://instagram.com/estudioevydenciaa (**prova visual**)
Verificar limite de largura/3 botões da instância e preservar parser `id;URL;url;label`. Não trocar rotas sem testar destino. A resposta “Fiquei com dúvida” pode ser preferível ao Instagram se o provedor limitar botões; isso é escolha a validar.

### E-mail Resend no T0
**MANTER** ação V4 para cliente elegível com e-mail consentido; NÃO misturar “e-mail depois da compra” nessa etapa. Editar assunto e conteúdo só na passagem multicanal, com dry-run do hash.

---

## H+1 | `whatsapp_h1`: mostrar o cenário
**Tipo:** vídeo `video_1hora` + ação `duvidas_reply`.
**Meta:** gerar desejo pela experiência; não repetir preços.
### `video_1hora.title` (caption)
```text
🎄 Olha um pedacinho da nossa Vila de Natal...
É aqui que as famílias brincam e criam lembranças lindas. ❤️
```
### `duvidas_reply.message` (texto com botões, se mantido)
```text
Quer que eu te ajude a escolher um horário? 🥰
```
Verificar se a ação já inclui botão STOP e sua obrigatoriedade; não apagar opt-out por conta própria.

## H+90 min | `whatsapp_90min`: remover fricção
**Tipo:** vídeo `video_90min` + `reserva_cta`.
**Meta:** facilidade de reserva, não outro anúncio longo.
### `video_90min.title`
```text
✨ São *dois cenários na mesma sessão*.
Qual dia seria mais tranquilo pra sua família?
```
### `reserva_cta.message`
```text
Se preferir, dá pra escolher o horário direto no botão. 📅
```
**Experimento:** +60m e +90m podem ser próximos demais; testar eliminar/reagendar H+90 somente após análise de conversão. Não mexer no schedule nesta revisão editorial.

## D+1 | `whatsapp_d1`: emoção + countdown comedidamente
**Tipo:** vídeo `video_d1`.
### `video_d1.title`
```text
🥹 Seus filhos crescem, mas as fotos deste Natal ficam.

*{natal_countdown_dias_descricao}* 🎄
Ainda dá tempo de escolher uma data especial pra vocês.
```
**Gate:** só usar token se renderizar corretamente **no momento do envio** e não ficar estático em snapshot T0. Fallback aprovado sem contagem: “A temporada de ensaios passa rapidinho.” Nunca confundir countdown do Natal com a data de encerramento dos ensaios.

## D+2 | `whatsapp_d2`: segundo cenário
**Tipo:** vídeo `video_2dias` + ação `lembrete_2dias`.
### `video_2dias.title`
```text
🎆 Uma surpresa que vale por duas:
além das fotos de Natal, tem *cenário de Ano Novo* na mesma sessão! ✨
```
### `lembrete_2dias.message`
```text
Quer ver os horários que ainda funcionam pra vocês? 📅
```
Não manter a ação com CTA duplicado caso a legenda já tenha link.

## D+3 | `whatsapp_d3`: jogos + galeria com DEMO
**Tipo:** vídeo `video_d3` já mapeado para “jogos”.
**Material:** captura real vertical do celular navegando na galeria, jogo da memória e quebra-cabeça feitos com fotos autorizadas da família.
### `video_d3.title`
```text
🎮 A parte que as crianças amam!
As fotos viram *jogos personalizados* e uma galeria pra família reviver tudo. ❤️
```
**Gate comercial:** direção afirma jogos+galeria em todas as sessões; validar entrega real nos 3 pacotes e não citar quantidade de jogos enquanto catálogo/checkout divergirem.

## D+4 | `whatsapp_d4`: roupinhas e tranquilidade da mãe
**Tipo:** vídeo `video_4dias` + ação `lembrete_4dias`.
### `video_4dias.title`
```text
👗 Já pensou nas roupinhas?
Aqui temos figurinos e acessórios infantis conforme tamanho e disponibilidade. ✨
```
### `lembrete_4dias.message`
```text
Se quiser, te conto como preparar os looks da família. 🥰
```
Não garantir roupa para qualquer idade/tamanho.

## D+5 | `whatsapp_d5`: pergaminho mágico no vídeo real (relato Antigravity)
**Tipo:** vídeo `video_d5`. A inspeção local por OpenCV do Antigravity relatou uma personagem teatral com pergaminho mágico iluminado; os arquivos/frames/SHAs ainda precisam ser arquivados **localmente** para comprovação independente.
### `video_d5.title` (caption curta)
```text
✨ Um recadinho mágico apareceu na nossa Vila de Natal! 📜🎄
Olha esse encanto... Quer ver os horários pra viver essa experiência?
```
**Gate:** assistir ao clipe inteiro e confirmar o pergaminho. A personagem no vídeo NÃO significa participação presencial incluída no ensaio; pessoas/personagens não estão incluídos nos pacotes por padrão. A história “desde 2011” foi deslocada para o T0 (credibilidade em mensagem própria), evitando legenda desconexa.

## D+10 | `whatsapp_d10`: última ajuda gentil
**Tipo:** vídeo `video_d10`.
### `video_d10.title`
```text
🎄 Ainda pensando nas fotos de Natal?
Se a dúvida for pacote, roupinha ou horário, me chama por aqui. ❤️
```
### `document_d10` — NÃO REPUBLICAR
**BLOCKED:** documento que se apresenta como `Comprovante_de_Transferencia Pix...` deve ser **suprimido por operação autorizada**, inclusive dos 11 snapshots pendentes identificados na última extração. NÃO mascarar ou renomear e continuar enviando. A antiga peça publicitária com aparência financeira pode gerar desconfiança. **Não** aplicar atualização manual nos snapshots.

## Slots SMS e voz | manter no mapa da V4, NÃO escrever por cima nesta passagem
| Papel | Momento | Ação editorial agora |
|---|---|---|
| `sms_h2` | +2h | preservar, verificar duplicação com +90m |
| `sms_d1` | D+1 12h30 | preservar, revisar após confirmação do WhatsApp |
| `sms_d2` | D+2 19h30 | preservar |
| `sms_d3` | D+3 09h45 | preservar |
| `sms_d4` | D+4 13h30 | preservar |
| `sms_d5` | D+5 18h45 | preservar |
| `sms_d10` | D+10 10h15 | preservar |
| `sms_d15` | D+15 19h15 | preservar, elegibilidade por calendário |
| `sms_d20` | D+20 12h45 | preservar, somente com temporada ativa |
| SmartVoice extra | H+1, D+1, D+5, D+7, D+10 | preservar, sem habilitar/desabilitar; revisão técnica isolada |

## Novas experiências sugeridas — NÃO adicionar etapas automaticamente
1. **Áudio breve da equipe** no início ou após vídeo: falar sobre dois cenários e conforto, 15–22 s (ver `02-ROTEIROS-ELEVEN-V4.md`). Melhor encaixar substituindo item redundante, não somando.
2. **Recadinho gravado do Papai Noel** (áudio WhatsApp, diferente de ligação telefônica): preferir D+3/D+4 se cliente não o recebeu por ligação autorizada; usar só com direitos e aviso de personagem.
3. **Vídeo certificado/medalhinha**: encaixar no momento D+3/D+4 somente quando itens universais forem homologados para 2026.
4. **Oferta de recuperação** D+10/D+15/D+20 apenas se não comprou e cupom/2 calendários tiverem aprovação, estoque, validade real e regra no checkout. Jamais dizer “preparei só pra você” se for massa.
5. **Prova social autorizada** ou link da reportagem publicitária pode substituir repetição de vídeo D+5.

## Critérios de aprovação
- 415 usa primeiro nome só quando validado; 416 sem token de nome.
- Nada de D+N, títulos internos, descrições longas, arquivo Pix, 85% ou 95% antigos ao cliente.
- Cada vídeo tem caption curta; PDF entregue no T0; mínimo de 3 CTAs conferidos.
- O fluxo não reenvia se já comprou, pediu parada ou optou por atendimento humano; verificar como funciona hoje no código, não assumir.
- Comparar cadência na V4 **por papel**, não copiar IDs sem extrair o novo inventory.json.
