# 22 — Áudio humano do fotógrafo + Papai Noel Eleven v4 | roteiros e validação

**26/10/2026 — ROTEIROS PRONTOS PARA GRAVAR; NÃO CADASTRADOS/ENVIADOS.** O núcleo V4 da proposta não tem mensagem `audio`. A API Z-API suporta áudio via URL/base64, mas isso não significa que adicionar um arquivo no gestor o conecte automaticamente à campanha. Introduzir mídia nova exige versão completa, validação de tipos, horários, elegibilidade e hash.

## 1. Minha recomendação editorial
**Produzir primeiro o áudio real de quem fotografa**, em voz natural de 15–22s. Dê preferência a usá-lo **após** a proposta imediata, em um toque já existente (substituição futura de uma mídia/ação com contrato apropriado, NÃO novo disparo H+1 em paralelo ao vídeo + ligação). O primeiro WhatsApp T0 deve continuar simples, com PDF + pacotes + botões. Esperar a família consumir esses materiais e só então incluir, se fizer sentido, um áudio do profissional ajuda a criar confiança sem causar efeito de spam.

**Não personalize com o nome Elaine em áudio genérico gravado uma única vez.** A saudação e o nome estão disponíveis em texto via `{saudacao}`/`{primeiro_nome}`, mas não são interpolados dentro de um arquivo MP3 pronto. Para o áudio com nome seria necessário gerar arquivos por cliente, regra de consentimento e um pipeline novo específico.

## 2. Roteiro A — sua voz, fotógrafo (principal, ~18–22s)
> Oi, tudo bem? Aqui é o fotógrafo do Estúdio Evydência, de Tijucas! Passei rapidinho pra te dizer que a nossa Vila de Natal ficou linda. A família faz fotos de Natal e Ano Novo na mesma sessão. Dá uma olhadinha nos pacotes que te enviei e, se quiser ajuda com o melhor horário, é só me chamar aqui!

**Como gravar:** celular ou microfone próximo da boca, local silencioso, sem música natalina alta, sorriso na voz e pausas curtas após “Tijucas” e “mesma sessão”. Não ler com voz de anúncio, falar como em resposta a uma mãe interessada. Fazer três tomadas e escolher a mais natural.

**Legenda separada (somente se for entregue como áudio de WhatsApp numa etapa FUTURA):**
```text
🎤 Gravei um recadinho rápido pra te apresentar nossa Vila de Natal. ❤️
```
O CRM atual envia áudio com `sendAudio` e **não com caption anexa como vídeo**; se quiser essa frase, será mensagem textual separada, exigindo análise da cadência. Preferência: enviar só áudio sem mensagem extra quando a mídia já for compreensível.

## 3. Roteiro B — sua voz, bem curto (~11–14s), para substituir um contato repetitivo
> Oii! Aqui é do Estúdio Evydência. Nossa Vila de Natal tem dois cenários na mesma sessão! Quer que eu te ajude a escolher o dia que fica melhor pra família? Me chama aqui.

**Objetivo:** alternativa humana a um lembrete automatizado, sem repetir preços ou enviar mais uma mensagem após múltiplos vídeos.

## 4. Roteiro C — Papai Noel, ElevenLabs V4 (~14–19s)
```text
[laughs] Ho, ho, ho!
[excited] Olá, família! O Natal está chegando e eu tenho um recadinho especial!
[whispers] Lá em Tijucas tem uma Vila de Natal cheia de luzes e sorrisos...
[excited] Que tal guardar essa magia em lindas fotografias?
[laughs] Ho, ho, ho!
```

**Observações:** tags de desempenho pertencem ao prompt Eleven v4, **não** ao corpo do WhatsApp; não usar SSML `<break ...>` no v4. A risada pode alongar muito o áudio; gravar/testar uma versão com apenas a risada inicial se passar de ~20s. Não prometer Papai Noel presencial na sessão nem dizer “estou ligando agora” se enviar por WhatsApp. Não sintetizar/imitar pessoa real sem autorização.

**Opcional sem tags para edição manual:**
> Ho, ho, ho! Olá, família! O Natal está chegando e a Vila de Natal em Tijucas está cheia de encanto. Venham registrar essa fase tão bonita. Ho, ho, ho!

## 5. Técnica de gravação, exportação e validação
1. Gravar master WAV PCM limpo, mono, ambiente seco, taxa 44.1 ou 48 kHz; não precisa de compressão ou trilha agressiva.
2. Exportar variante para envio `MP3` ou `OGG/Opus` após conferência do tipo aceito pelo CRM/Z-API em teste; ideal voz audível no alto-falante do celular e duração curta.
3. Nomear sem cliente: `evydencia-fotografo-natal2026-v1.mp3` e `evydencia-papainoel-natal2026-elevenv4-v1.mp3`.
4. Conferir início/final sem corte, pronúncia **Evydência** / Tijucas, ruído, tamanho, MIME, SHA-256 e consentimento de voz e de destinatário. Salvar arquivos no acervo **privado**, não no GitHub público.
5. Não enviar gravação com opção `viewOnce`; `waveform: true` é opcional, confirmar como o CRM envia o payload real.
6. A Z-API aceita `POST /send-audio`, `phone`, `audio` (URL ou Base64), `viewOnce` e `waveform`. Retorno com `messageId` é aceite, **não garante recebido/lido**.
7. Não mandar simultaneamente áudio do fotógrafo, vídeo, SMS e ligação H+1. A inclusão futura requer aprovação da regra de elegibilidade, consentimento, frequência e supressão por compra, resposta ou STOP.

**Fontes oficiais:** https://developer.z-api.io/message/send-message-audio ; https://elevenlabs.io/docs/pt/help-center/product/core-capabilities/text-to-speech/how-do-audio-tags-work-with-eleven-v3-and-v4 ; https://elevenlabs.io/docs/pt/overview/capabilities/text-to-speech/best-practices

## 6. E quanto ao “torpedo de voz”?
SmartVoice é telefonia real, separada de `audio` enviado via Z-API. O áudio da ligação deve cumprir o contrato WAV/ID próprio no dialer e não pode ser considerado implantado só porque existe um MP3 no notebook. A série atual tem slots H+1, D+1, D+5, D+7, D+10; estado efetivo, WAVs, CDR/answered e opt-in telefônico precisam ser conferidos antes de ativação. **Nesta rodada, não criar nem substituir nenhum arquivo efetivo da telefonia.**
