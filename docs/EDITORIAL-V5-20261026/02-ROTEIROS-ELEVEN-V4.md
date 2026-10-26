# 02 — Áudio e vídeo | ElevenLabs Eleven v4, Z-API e Papai Noel
**Versão editorial V5 / 26/10/2026 — rascunhos para locução, NÃO são áudios já gerados ou enviados.**
Objetivo: transformar vídeos/áudios existentes em prova visual e atendimento simpático, sem aumentar o número de mensagens à toa.

## Padrão ElevenLabs v4 (confirmado na documentação oficial)
- Escolher modelo `eleven_v4` para conteúdo de alta expressividade ou `eleven_v4_turbo` quando baixa latência for relevante, com voz licenciada/autorizada.
- V4 aceita `[whispers]`, `[laughs]`, `[curious]`, `[excited]` e outras audio tags. São orientação de **interpretação**, não parte do texto exibido no WhatsApp.
- Não usar `<break time="..."/>` com v4/v3; SSML break não é suportado neles. Pausas: pontuação, divisão em frases e entonação.
- Escrever números por extenso na locução, evitar emojis e abreviações em fala; testar a pronúncia de “Evydência” e “Tijucas”. Não inventar sotaque infantil nem voz de pessoa real sem autorização.
- Se gerar áudio com nome/idade de criança, nunca colocar transcrição ou dados pessoais em repositório público. Guardar MP3/Opus/OGG em armazenamento privado aprovado.
- Preservar a verdade do canal: **um recado gravado do Papai Noel não é uma ligação telefônica**. Não anunciar chamada garantida enquanto SmartVoice não estiver homologado.

Referências oficiais:
- https://elevenlabs.io/docs/pt/help-center/product/core-capabilities/text-to-speech/how-do-audio-tags-work-with-eleven-v3-and-v4
- https://elevenlabs.io/docs/pt/overview/capabilities/text-to-speech/best-practices
- https://elevenlabs.io/docs/pt/overview/models

## Roteiro A — boas-vindas humano, opcional T0 (15–20s)
**Canal:** áudio gravado da equipe no WhatsApp, não substituir por robotização sem critério. Criar só se couber no bloco inicial sem atrasar imagem e PDF.

### Texto natural para gravar
> Oii! Que bom ter você por aqui! Nossa Vila de Natal em Tijucas tá linda. E esse ano a família fotografa no cenário de Natal e também no de Ano Novo, na mesma sessão. Dá uma olhadinha nos pacotes que acabei de te mandar!

### Eleven v4 — texto de síntese
```text
[excited] Oi! Que bom ter você por aqui!
Nossa Vila de Natal, em Tijucas, tá linda!
E olha que legal: são dois cenários na mesma sessão... Natal e Ano Novo.
Dá uma olhadinha nos pacotes que te mandamos!
```
**Voz:** acolhedora, adulta, ritmo conversado, sem locutor de varejo, sem narrar 'dois por um' artificialmente. A direção pode preferir gravação humana real.

## Roteiro B — vídeo tour do cenário, H+1 (15–20s)
**Canal:** narração sobre vídeo real. Se o vídeo existente já tem locução clara, NÃO empilhar nova voz.
```text
[curious] Você imagina seu filho entrando numa Vila de Natal assim?
As luzes, os detalhes, a brincadeira...
[excited] E o melhor: tem também um cenário de Ano Novo pra família.
São lembranças de duas festas, numa sessão só!
```
**Captação sugerida:** planos de luzes + crianças brincando com autorização + transição para cenário de Ano Novo. Não afirmar que as imagens são dos clientes atuais se não forem.

## Roteiro C — jogos e galeria, D+3 (15–25s)
**Canal:** narração sobre captura real de celular autorizada. Prioridade de produção criativa.
```text
[excited] A diversão continua depois das fotos!
Olha só... o sorriso da criança vira peça de quebra-cabeça, jogo da memória, e uma galeria pra família rever esses momentos.
É Natal que dá pra reviver em casa!
```
**Vídeo:** 0–3s foto revelada/celular; 3–9s quebra-cabeça; 9–15s jogo da memória; 15–20s galeria. Mostrar jogo **realmente funcionando**. Aprovar quantidades e condições por pacote antes de prometer universalidade.

## Roteiro D — Papai Noel, áudio gravado de WhatsApp (12–20s)
**Canal:** mensagem de áudio pré-gravada e opcional de voz natalina; NÃO alegar ligação individualizada.
```text
[excited] Ho, ho, ho!
Olá, família! Aqui é o Papai Noel com um recadinho especial.
[whispers] Soube que tem uma Vila de Natal encantada esperando vocês em Tijucas...
[excited] Quero ver muitos sorrisos por lá!
Um abraço apertado, e até breve!
```
**Atenção:** a campanha não deve confundir esse material com um contato realmente originado do Polo Norte, uma ligação ao vivo ou participação presencial de personagem na sessão. Evitar dados de crianças sem consentimento. Se houver telefonia real autorizada, o roteiro de voz no SmartDialer deve ser independente (consentimento, DTMF, horários e resultado).

## Roteiro E — credibilidade desde 2011 + reportagem (16–22s)
**Canal:** sobre imagens de bastidores reais e rosto da equipe, opcional D+5.
```text
[softly] Desde dois mil e onze, a gente tem um privilégio aqui em Tijucas...
fotografar crianças que crescem e voltam com a família, Natal após Natal.
[excited] E a nossa Vila de Natal deste ano está especial. Quer ver como ficou?
```
**Prova:** reportagem comercial publicada 30/09/2026 em https://www.vipsocial.com.br/noticia/estudio-de-tijucas-transforma-natal-em-experiencia-para-toda-a-familia/59957. Não apresentar como premiação/julgamento de imprensa independente. A matéria é declarada especial publicitário.

## Roteiro F — vídeo/áudio de atendimento para objeções (12–18s)
```text
[curious] Tá em dúvida sobre as roupinhas, o valor ou o horário?
Pode me perguntar! Temos opções de ensaio diferentes, e a equipe te ajuda a escolher o que combina com a família.
```
**Priorizar:** gravação com pessoa real da equipe, pois reforça autenticidade.

## Legenda WhatsApp != script de fala
| Elemento | Exemplo | Onde preencher |
|---|---|---|
| Legenda curta de vídeo | `🎮 As fotos viram *jogos personalizados*! ❤️` | `messages.title` para `video`, no caminho legado |
| Texto de áudio gerado | `[excited] A diversão continua depois das fotos!` | Somente no ElevenLabs |
| Nome do arquivo de áudio | `natal26-v5-papai-noel-recado-v01.ogg` | Asset aprovado no CRM; verificar codec/duração |
| Link de ação | `Ver horários` | Botão do CRM/WhatsApp |
| Áudio/voz via chamada telefônica | Fluxo SmartVoice e WAV implantado | Mecanismo de ligação, não Z-API send-audio |

## Checklist para cada mídia
- [ ] Conteúdo real visto/ouvido integralmente, inclusive início/fim.
- [ ] Formato vertical 9:16 ou enquadramento mobile bem legível.
- [ ] Arquivo compatível com Z-API, tamanho, MIME/codec e duração revisados.
- [ ] Crianças/retratos e músicas com direitos de uso comercial.
- [ ] Legenda corresponde às imagens; sem `D+1`, `D+5`, `role` ou tag Eleven v4 no payload.
- [ ] Nome/Título/caption/URL e SHA-256 auditados; preview do conteúdo realmente renderizado.
- [ ] Nenhum material adicional atrasa envio do PDF e preços no T0.
- [ ] Nenhum envio se compra/STOP ou ausência de consentimento aplicável.
- [ ] Sem geração de voz em nome de pessoa real sem autorização.
- [ ] Vídeo da reportagem deve ser trecho usado com direito/licença apropriada; preferir vídeo próprio e **link** para a matéria.
