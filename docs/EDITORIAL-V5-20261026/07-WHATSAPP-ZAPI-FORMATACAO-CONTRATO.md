# 07 — WhatsApp + Z-API: formatação, campos e contrato efetivo V4
**Data:** 26/10/2026 · **Estado:** referência técnica para V5 DRAFT, SEM PUBLICAÇÃO · **Base de código:** hypeneural/evydencia-crm (main, consultada 26/10).

## 1. Mensagem WhatsApp não é Markdown do GitHub
A formatação é feita com caracteres **literais** no texto enviado; não transformar em HTML, Markdown renderizado, nem escapar símbolos de WhatsApp ao montar payload JSON.

| Formato | Sintaxe a ser enviada literalmente | Uso recomendado |
|---|---|---|
| Negrito | `*Natal + Ano Novo*` | Um único benefício ou CTA importante |
| Itálico | `_feito com carinho_` | Tom humano / observação breve |
| Tachado | `~valor antigo~` | **Não usar** sem preço anterior verdadeiro e válido |
| Monoespaçado | três crases antes/depois | Evitar em mensagens comerciais; usar somente em código |
| Lista com marcadores | `- Dois cenários` em linha própria | Útil em FAQ curto, não em todas as mensagens |
| Lista numerada | `1. Natal` em linha própria | Raramente útil em propostas |
| Citação | `> texto` no início da linha | Apenas citação verdadeira e autorizada |
| Código em linha | uma crase de cada lado | Evitar, pode transformar valores em aparência técnica |
| Emoji | Unicode normal `🎄❤️📅` | Em geral 0–2 por mensagem |
| Quebra de linha | `\n` no JSON (renderiza nova linha) | Frases curtas, espaços entre blocos |
| Link | URL literal `https://...` | Links são reconhecidos no WhatsApp; `[Rótulo](URL)` não vira botão nativo |

**Fontes oficiais:** https://faq.whatsapp.com/539178204879377/?cms_platform=web&locale=pt_BR . A documentação ressalva disponibilidade de algumas formatações novas por plataforma/cliente. Padronizar como **mais portáveis** apenas negrito, itálico, emoji e quebra de linha.

**Não confundir sintaxes:**
- Variável `{primeiro_nome}` é interpretada pelo **CRM**, não pelo WhatsApp.
- `{natal_countdown_dias_descricao}` vem do `MessageVariableService` do CRM; o token só produz conteúdo se o envio realmente passar pelo parser.
- `[excited]`, `[whispers]` pertencem ao **ElevenLabs v4**, NUNCA à mensagem/caption do WhatsApp.
- `<b>...</b>`, `<br>`, `**negrito**` e links Markdown de GitHub NÃO são o formato desejado.
- Três aspas `'''` NÃO formatam WhatsApp; as três crases ```` geram **código monoespaçado**, visual inadequado para uma oferta de Natal.

## 2. API Z-API: capacidades confirmadas
| Recurso | Endpoint da Z-API | Campos do provedor | O que o CRM V4 efetivamente faz |
|---|---|---|---|
| Texto | `/send-text` | `message` | `Message.type=text`: envia `message.message` |
| Imagem | `/send-image` | `image`, `caption` | `type=image`: `messages.title` → legenda |
| Vídeo | `/send-video` | `video`, `caption` | `type=video`: `messages.title` → legenda |
| Documento PDF | `/send-document` | `document`, `fileName`, opção `caption` no provedor | **CRM atual** passa `messages.title` como `fileName`, **não** envia `description` como caption |
| Botões de ação | `/send-button-actions` | `message`, `title`, `footer`, `buttonActions` | `type=action`: `message`, `title`, `description` e botões (parser separado) |
| Áudio de WhatsApp | `/send-audio` | `audio`, `waveform`, `viewOnce` | CRM possui `type=audio`, mas **não adiciona** áudio novo ao manifesto V4 sem contrato/revisão |
| Chamada telefônica | não confundir com áudio de WhatsApp | SmartDialer/VoIP é infraestrutura distinta | Preservar série SmartVoice, não afirmar que áudio enviado = ligação realizada |

**Documentação técnica Z-API:** https://z-api-docs.vercel.app/message/send-message-image ; https://z-api-docs.vercel.app/message/send-message-video ; https://z-api-docs.vercel.app/message/send-message-document ; https://z-api-docs.vercel.app/message/send-button-actions ; https://z-api-docs.vercel.app/message/send-message-audio .
**Fonte CRM:** `app/Services/Messages/CampaignMessageService.php::send`, `app/Services/Messages/MessageService.php`.

Receber `messageId` de `/send-*` **não é** sinônimo de `delivered` nem `read`: requer recibos/webhooks, como explica https://v2.developer.z-api.io/ .

### Observações técnicas especiais
- Z-API suporta `caption` em documento em sua **API pública**, mas no caminho `CampaignMessageService::sendAttachment` atual o CRM não popula esse campo. Não prometer legenda no documento sem validar/adaptar código.
- Campos de `action` usam `message` (corpo), `title` (cabeçalho) e `description` (rodapé); não tratar `description` de *action* como ignorado da mesma forma que é no vídeo.
- Conteúdo `description` de image/video é interno no serializer legado, não caption. Não editar `description` esperando que o WhatsApp mostre.
- A Z-API alerta sobre combinações de botões CALL, URL e REPLY numa só mensagem. **T0 só possui botões URL**, mas ainda necessita prova no WhatsApp Web/telefone receptor real para confirmar labels/cliques.

## 3. Restrição CONTRATUAL do manifesto V4 — achado P0
No CRM, `app/Services/Proposals/Natal2026ProposalManifest.php::validate` (linhas ~163–187) exige em V4:
- `messages.title`: máximo **200 caracteres Unicode** via `mb_strlen`; `message` até 10.000; `description` até 1.000. Definir teto **editorial** de 160 caracteres para cada caption por conforto visual; o teto **técnico** é 200. O validador V5 antigo permitia 500, corrigir.
- `intro_cta` usa **EXATAMENTE três botões** tipo `URL` e URLs **nesta ordem**:
  1. `natal2026-reservar` → `https://evydencia.com/catalogo/natal` (label `🎄 Reservar Agora`);
  2. `natal2026-horarios` → `https://horarios.fotosdenatal.com/` (label `📆 Ver Horários`);
  3. `natal2026-instagram` → `https://instagram.com/estudioevydenciaa` (label `📸 Nosso Instagram`).
- O manifesto rejeita URL de checkout direto `https://reserva.fotosdenatal.com/` em `initial` com `button_url` e `button_destinations`, embora o endereço possa existir/funcionar. **Manter a URL permitida na V5 rascunho**; para checkout direto, fazer uma **mudança independente, versionada e aprovada** do contrato/manifesto, não editar apenas no gestor.
- IDs `btn_reservar`, `btn_horarios`, `btn_insta` descritos pelo último relatório do agente **NÃO correspondem** aos IDs acima no JSON canônico V4. Exigir prova do estado de DB e de `manifest.mapping` antes de qualquer migração. Nunca reescrever IDs para “normalizar”.
- Toda etapa `action` possui suas próprias regras de count/type; mudar REPLY em H+1 não é a mesma coisa que mudar URL em T0.
- `intro_cta` T0 tem conteúdo em `message.message`, não em `title`. O manifesto V4 armazena o bloco com os triple-backticks **dentro do corpo**; portanto trocar só `title` não os removerá.
- O `configuration_hash` aprovado pela prontidão muda com conteúdo; alterações isoladas no gestor podem causar `natal_manifest_configuration_mismatch`.
- `fallback_title` no `editorial/whatsapp-v5.draft.json` é **apenas campo de planejamento**. O CRM não lê nem aplica essa alternativa automaticamente. O Antigravity precisa provar substituição dinâmica do token no instante do envio ou selecionar fallback literal no manifesto antes da publicação.

## 4. Receita editorial para WhatsApp de famílias
- **Abertura:** até 300–380 caracteres se viável, duas promessas claras: dois cenários + experiência e PDF enviado agora; 2011 em uma frase; botão leva ao catálogo/horários; preço de entrada transparente. O opt-out continua.
- **Legenda de mídia:** meta editorial 80–160 caracteres, 1 benefício relevante à cena + pergunta/ação natural; não 4 parágrafos.
- **H+1 + H+90:** duas mídias em 30 minutos é intenso; usar texto complementar somente se agrega informação nova; medir resposta/opt-out antes de aumentar.
- **D+3 jogos/galeria:** demo pública https://jogos.fotosdenatal.com/ e galeria https://jogos.fotosdenatal.com/demo/fotos — testar carregamento real/privacidade; não prometer 10 jogos em todos os pacotes, catálogo base 1/5/10.
- **D+4 bolhas/brindes:** escrever legenda sobre o que aparece no vídeo real; não associar aos personagens presencialmente. Medalhinha/certificado/livrinho têm regra informada pela gestão a confirmar em catálogo/estoque.
- **D+5 pergaminho:** legenda de magia **sem** alegar personagem incluso nos pacotes; “desde 2011” já está na abertura.
- **D+10:** sem “últimas vagas”, “guardei uma data”, “agenda 95%” sem dados atuais reais.
- **STOP/compra:** preservar `PARARNATAL2026` e supressão efetiva. Textos bonitos não substituem conformidade.
- **PDF Pix D+10:** marcado `blocked_do_not_publish` no projeto, mas isso NÃO bloqueia 12 execuções congeladas identificadas no relatório de 26/10.

## 5. Checklist antes de produzir manifesto executável
- [ ] 19 roles/variante com 9 SMS preservados, nomes e mídias reconciliados.
- [ ] Todos os captions até 200 caracteres Unicode (preferível até 160) e sem HTML/SSML/tags de voz.
- [ ] T0 com três IDs, tipos, rótulos e URLs exatos do contrato V4, enquanto não aprovado novo contrato.
- [ ] 415 com `{primeiro_nome}` renderizado; 416 sem token.
- [ ] Countdown D+1 renderizado no **title do vídeo** no momento certo; sem placeholder cru no receptor. Fallback é decisão humana antes da publicação, não automática.
- [ ] Variantes testadas em GET/preview e, se autorizado mais tarde, canário real.
- [ ] Ações e mídias congeladas em snapshots antigos preservadas; P0 D+10 tratado separadamente.
- [ ] Sem reescrever histórico, SMS, e-mail Resend ou SmartVoice nesta primeira revisão.
