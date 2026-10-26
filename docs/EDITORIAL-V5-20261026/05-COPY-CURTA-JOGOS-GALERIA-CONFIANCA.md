> **BANCO DE ALTERNATIVAS EDITORIAIS, NÃO V5 EXECUTÁVEL:** as frases a seguir são ideias/experimentos. O texto canônico atual fica em `editorial/whatsapp-v5.draft.json` e é reproduzido integralmente em `14-COPY-CANONICA-V5-CANDIDATA.md`. Alterações futuras devem começar pelo JSON, gerar o doc com o script e revalidar.

# 05 — Complemento de copy WhatsApp: demonstração, confiança e experiência

**26/10/2026 | IDEIAS CURTAS, RASCUNHO SEM EXECUÇÃO.**
As mensagens abaixo são ALTERNATIVAS de baixa fricção para **substituir ou melhorar** itens existentes na V5. **Não criar disparos adicionais** enquanto o Antigravity estiver revisando a cadência. Campos `title` de vídeo/imagem são captions; `action.message` é corpo; `description` não é caption de vídeo neste serializer.

## Estratégia de persuasão: o que a mãe deve sentir
- **Segurança:** estúdio em Tijucas desde 2011, endereço e equipe reais, portfólio atualizado.
- **Desejo:** duas decorações numa visita e uma experiência afetiva com brincadeiras, fotos e mimos.
- **Prova:** jogos demonstráveis, galeria pública de exemplo, fotos reais de figurinos/bolhas, comentários autorizados.
- **Conveniência:** horários em tempo real, estacionamento/localização a conferir, checkout sem conversa obrigatória, PDF de valores enviado sem demora.
- **Urgência responsável:** temporada e entrega de fotos precisam de data real; não repetir `95% ocupado`, cronômetro falso ou oferta fictícia.
- **Tom:** caloroso, pessoal e confiável, não infantilizado nem anúncio gritado. Uma ideia por mensagem, 0–2 emojis quando possível, poucas linhas.

## Abertura T0 | reforçar desejo e confiança sem ficar longa
**NÃO adicionar tudo ao T0.** PDF + foto dos pacotes + botões já são o argumento completo. Experimento A/B **alternativo** ao texto V5 atual:

**415 (com nome):**
```text
Oii, *{primeiro_nome}*! 🥰🎄
Que bom que você quer viver nosso Natal em família!

São *2 cenários numa única sessão*, com fotos reveladas e lembranças especiais. ❤️

Já te envio os pacotes e o PDF, a partir de *2x R$ 98*.
*Prefere vir na semana ou fim de semana?*

_Para parar lembretes, responda PARARNATAL2026._
```

**416 (sem nome):** remover `, *{primeiro_nome}*` da primeira frase; restante idêntico. Não presumir que a pessoa tem filhos.

## Vídeo H+1 — Vila de Natal (substitui caption atual se mídia compatível)
```text
🎄 Não é só uma sessão de fotos...
É viver um pedacinho do Natal com quem você ama. ❤️
```
**Mídia:** vídeo real da Vila, sem citar bolhas se não aparecerem.

## Vídeo H+90 — fácil agendar (avaliar reduzir redundância)
```text
✨ Dois cenários. Uma experiência em família.
*Qual dia ficaria melhor pra vocês?*
```
**Destino:** botão `Ver horários` já existente; não expor 3 links em todas as mensagens.

## D+1 — sentimento / mãe no retrato
```text
🥹 Daqui a alguns anos, é essa fase dos filhos que você vai querer rever.
*Você também merece estar nas fotos.* ❤️
```
**Fallback para família sem filhos identificados:** `O tempo passa, mas o abraço em família fica nas fotografias. ❤️`

## D+2 — brinquedos, figurinos e experiências
```text
🎁 Aqui, a infância também faz parte da magia.
Tem brinquedos, figurinos e muitos detalhes esperando a família. ✨
```
**Gate:** não usar se `video_2dias` mostra só o cenário de Ano Novo e não esses elementos. Se incompatível, **preservar caption do segundo cenário** na V5 e reservar a fala para D+4.

## D+3 — jogos com demonstração REAL
**Caption do vídeo atual `video_d3.title`:**
```text
🧩 Imagina brincar com as *próprias fotos do ensaio*?
Olha esses jogos que preparamos pra família! 🎮
```
**Texto contextual opcional no MESMO bloco, se existir capacidade de action e não houver contato redundante:**
```text
Quer testar como funciona? 😍
https://jogos.fotosdenatal.com/
```
**Regra:** não prometer 10 jogos a todos. O catálogo vigente indica 1/5/10, conforme pacote. A galeria universal é declaração atual da direção, mas exigir alinhamento do catálogo/contrato.

**Alternativa à mensagem do link jogos, para galeria (não disparar ambos automaticamente):**
```text
📸 E ainda tem uma *galeria personalizada* pra rever as fotos com carinho. ❤️
Olha um exemplo:
https://jogos.fotosdenatal.com/demo/fotos
```
Usar apenas quando a URL funcionar em mobile e o exemplo for público e autorizado.

## D+4 — medalhinha, certificado, livrinho de colorir e botão mágico
**Proposta para VÍDEO NOVO, não relegendagem cega do ativo:**
```text
🎅 A magia não acaba quando a câmera para!
Tem lembrancinhas para as crianças e até *bolinhas de sabão* no nosso botão mágico. 🫧
```
**Vídeo autoral sugerido 12–18s:** (0–4s) recepção/entrada interativa; (4–8s) criança acionando botão mágico, bolhas e luzes; (8–12s) medalhinha + certificado; (12–18s) livrinho de colorir. Filmar com autorização de imagem e sem prometer presença física de Papai Noel nos pacotes.
**Legenda de alternativa apenas com os presentes, se confirmado estoque/regra:**
```text
🎁 Tem medalhinha, certificado e livrinho de colorir!
Pensamos nos pequenos detalhes pra criança sair sorrindo. ❤️
```
**Gate:** a distinção certificado digital/impresso por pacote tem que ficar correta.

## D+5 — confiança para famílias de outras cidades
**Vídeo da equipe/bastidores real:**
```text
📸 *Desde 2011 em Tijucas*, fotografando histórias de família.
É aqui que a gente cuida de cada detalhe com carinho. ❤️
```
**Mensagem contextual de localização, SOMENTE quando a dúvida for onde fica:**
```text
📍 Estamos em Tijucas, no bairro Universitário!
Aqui tem como chegar pelo Maps ou Waze:
https://ir.fotosdenatal.com/
```
**Alternativa de prova visual:** 
```text
Quer conhecer melhor nosso estúdio? 🥰
No Instagram mostramos fotos e bastidores atualizados:
https://instagram.com/estudioevydenciaa
```
**Reportagem:** se mostrar matéria da VipSocial, rotular como `especial publicitário`; não usar “reportagem nos escolheu o melhor”.

## D+7 a D+10 — acolher dúvida, não repetir pressão
```text
🎄 Ainda tá em dúvida?
Pode me perguntar sobre pacotes, roupinhas ou horários. Te ajudo por aqui! ❤️
```
**Urgência permitida apenas com última data de ensaio e slots reais:** `Nossa temporada termina em {data_sessao_final_confirmada}. Se quiser, posso mostrar os horários que ainda estão livres.` *O placeholder de data é editorial e NÃO existe como variável operacional pronta.* Não enviá-lo cru.

## Pós-reserva (NÃO é etapa do fluxo 415/416)
Quando a compra/entrada estiver confirmada, encerre automação de proposta e envie informações de preparação, horário e local pelo fluxo de agendamento existente, com:
```text
Aaa, que alegria! 🎄❤️
Sua sessão está confirmada. Aqui está o caminho mais fácil pra chegar ao estúdio:
https://ir.fotosdenatal.com/
```
Só enviar após confirmação **real** da reserva. Não usar esse texto para quem ainda não pagou ou teve a reserva cancelada.

## Áudios curtos para ElevenLabs v4 (NÃO sair com tags em WhatsApp)
**Equipe, 13–17s — gerar com voz autorizada:**
```text
[excited] Oi! Olha, esse ano a gente caprichou na experiência.
São dois cenários, os jogos com as fotos da família e várias surpresinhas pras crianças.
Vem conhecer a nossa Vila de Natal em Tijucas!
```
**Papai Noel, 12–17s — personagem fictício gravado:**
```text
[laughs] Ho, ho, ho!
[excited] Olá, família! Sou o Papai Noel, com um recadinho especial.
Na Vila de Natal da Evydência tem muita magia esperando vocês!
Um abraço do bom velhinho!
```
**Não confundir:** áudio/vídeo gravado no WhatsApp ≠ chamada telefônica SmartVoice, que requer gatilho e consentimento próprios.

## Segmentação sem engenharia desnecessária
| Situação | CTA dominante |
|---|---|
| Lead pediu proposta agora | `Reservar agora` / `Ver horários` |
| Lead quer comparar benefícios | Site completo e PDF já enviado |
| Lead perguntou se jogos valem a pena | `jogos_demo` ou `galeria_demo` (um por contato) |
| Lead novo de fora de Tijucas | Fotos do estúdio + `como_chegar` apenas se relevante |
| Lead inseguro sobre confiança | `desde 2011`, site, Instagram e material real |
| Cliente que já reservou | SAIR do fluxo comercial; usar confirmações e endereço |
| Lead respondeu/STOP ou em atendimento | Não insistir; respeitar consentimento e regras de cancelamento |
| Lead sem filhos identificados | Versão neutra: família/casal/lembrança, sem 'seu filho' |

## Metas para avaliação depois de homologado
- Respostas úteis, cliques na agenda/checkout, compra confirmada, descadastros, bloqueios/reclamações.
- Não otimizar só taxa de leitura; WhatsApp com muitas mensagens pode elevar rejeição.
- Se a integração atual não fornece eventos de clique de cada botão, não prometer métricas por URL sem novo tracking.
- Conteúdo novo deve **substituir** legenda ou ação existente, não multiplicar touchpoints automaticamente.
