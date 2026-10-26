# 12 — Auditoria de SMS, Resend e ligação telefônica REAL (SmartVoice)

**Data: 26/10/2026 · Plano editorial de canais complementares, não implantação.**
Fonte de horários/textos: `hypeneural/evydencia-crm/docs/active/natal-2026-proposta/manifesto-natal-2026.v4.json`; para voz, `NatalProposalVoiceSeries.php`, registry versionado de `hypeneural/smartdialer-platform` em 26/10.

## 1. SMS — 9 slots V4, muito intensos nos primeiros dias
Estão definidos os seguintes envios comerciais adicionais, ancorados ao WhatsApp inicial aceito. *Agendado não significa entregue.*

| Janela | Argumento atual resumido do manifesto V4 | Risco | Alternativa para futura revisão |
|---|---|---|---|
| +2h | “NÃO DEIXE PRA DEPOIS”, WhatsApp da equipe | **P1: pressão logo após 2 vídeos e oferta** | Preferir suprimir este slot ou torná-lo um convite de suporte, após medir resposta H+90 |
| D+1 12:30 | Dois cenários, um ensaio | Duplicado com WhatsApp D+1/D+2 | `EVYDENCIA: Natal e Ano Novo na mesma sessão. Veja valores e horarios: https://fotosdenatal.com` |
| D+2 19:30 | Fotos reveladas e calendário + emoticon literal `<3` | Visual pouco natural e horário noturno | `EVYDENCIA: Fotos reveladas e calendario pra guardar este Natal. Confira: https://fotosdenatal.com` |
| D+3 09:45 | Tijucas, Vila, Instagram | Repete WhatsApp jogos D+3 | `EVYDENCIA: Quer ver fotos reais da nossa Vila de Natal? https://instagram.com/estudioevydenciaa` |
| D+4 13:30 | Roupas e acessórios | Complementar, mas muito frequente | Manter só se WhatsApp não tiver engajamento e houver consentimento |
| D+5 18:45 | “HO HO HO!”, atendimento | Repetitivo com vídeo mágico D+5 | Avaliar suprimir |
| D+10 10:15 | “Já agendou?”, dúvidas | Bom gancho de suporte | `EVYDENCIA: Ainda quer ajuda com pacotes ou horarios? Fale com a equipe: [LINK_OFICIAL_APROVADO]` |
| D+15 19:15 | O Natal passa, fotos ficam | Muito tardio ou após temporada | Só manter se sessão ainda disponível e consentimento |
| D+20 12:45 | “ÚLTIMO LEMBRETE!” | Urgência precisa ser real; pode ser obsoleto | Só se há última data de reserva válida, nunca como ficção |

A V5 WhatsApp atual **preserva** os nove slots de SMS. As versões acima são **IDEIAS**, não aplicadas e não totalmente prontas porque URLs, nome de emissor, desconto/STOP e horários do provedor precisam de homologação.

### Mínimo técnico antes de mudar SMS
- Confirmar pelo banco o `channel=sms`, provedor/conta Velip, situação ativa/pausada de cada ação, limite real de caracteres e codificação GSM-7/Unicode.
- Testar encurtamento seguro de URL **sem** encurtador duvidoso; ver custos de múltiplos segmentos SMS por emoji/acentos e proibição de falsos links.
- Examinar recibos por status: queued, aceito, delivered, failed/unknown; parar depois da compra e opt-out em outros canais conforme escopo e contrato.
- Conferir se o descadastro `PARARNATAL2026` no WhatsApp suspende todos os canais de marketing necessários; não assumir que todas as nove mensagens têm opt-out SMS dedicado.
- Os links `wa.me` de materiais históricos divergem; usar somente número de atendimento atual, validado no próprio CRM e no WhatsApp real, **nunca** copiar um número de outra campanha.

## 2. Resend — e-mail do orçamento vs e-mail após COMPRA
**T0 V4:** há ação de e-mail comercial na etapa `initial` quando o CRM considera o destinatário elegível. O manifesto aprovado traz **`body_mode: text`**, `subject: Sua proposta Natal + Ano Novo 2026 — Evydência`, três valores e links. **Não há prova de PDF como anexo do e-mail no V4**; o PDF é um anexo WhatsApp.

**Após a compra:** uma automação diferente, do pedido, pode enviar confirmação/boas-vindas com pacote, data, horário, valor/parcelas conforme contrato e link https://ir.fotosdenatal.com/ para como chegar. Não disparar e-mail de orçamento após contrato fechado. Este fluxo pós-compra precisa de evento de pedido real e idempotência, não só `cliente já cadastrado`.

### Melhorias planejadas para e-mail (fase 2)
- Assunto simples e personalizado só se nome válido: `Sua experiência de Natal + Ano Novo na Evydência 🎄`.
- Corpo de no máximo 2–3 blocos, tabela dos pacotes com valores atuais, CTA `Ver horários` para o calendário e link ao site, prova real de dois cenários + desde 2011.
- Se optar por HTML, produzir alternativa texto, link de desinscrição conforme categoria e validar SPF/DKIM/DMARC, bounce/delivery/reply-to.
- Evitar PDF de 9,4 MiB em anexo de e-mail sem justificativa (risco de peso/clique); usar URL pública aprovada se permitido.
- Não alterar a ação V4 até existir mecanismo que republique hash/snapshot corretamente.

## 3. SmartVoice — cinco chamadas REAIS distintas de áudio WhatsApp
| Slot | Flow ID no CRM | Oferta recomendada para voz, se homologada |
|---|---|---|
| H+1 | `natal2026-proposta-1h` | 12–18s de acolhimento real, com opção de falar com humano |
| D+1 | `natal2026-proposta-1d` | Papai Noel em recado breve, sem mentir sobre presença física |
| D+5 | `natal2026-proposta-5d` | Benefícios da Vila e dúvida de horários |
| D+7 | `natal2026-proposta-7d` | Convite de suporte, sem insistência com lead que respondeu |
| D+10 | `natal2026-proposta-10d` | Encerramento da régua de voz, apenas se temporada ativa |

**Status factual separado:**
- Existe a série com 5 posições no CRM e código para capturar ações com `skip_if_converted=true`; isto não significa chamada atendida.
- `hypeneural/smartdialer-platform/config/voice_flows/registry.json` na main consultada em 26/10 declara `enabled:false` para os cinco fluxos. **O ambiente implantado pode divergir.** O commit recente do discador liberou/reconciliou tronco, mas não comprova ativação individual destes 5 áudios.
- A direção informou que a telefonia está praticamente boa. Respeitar progresso, porém concluir **OPERACIONAL/ATIVO** só por verificação de `effective registry`, WAV/áudio real no servidor do discador, permissão/consentimento, DTMF, janela, número de origem e ledger de tentativas/atendidas/falhadas.
- Conferir `voice_mode=server_flow` nos payloads, consentimento comercial específico, horário permitido local, limite por destinatário, skip se convertido, fallback humano e `unknown outcome` sem retries inseguros.
- Não requer login manual na VPS do CRM a cada revisão se um endpoint read-only de `voice readiness` no painel puder mostrar tudo; **o dashboard desse tipo ainda não foi comprovado**. Se não existir, uma entrega inicial no CRM deve expor `GET /gestao/natal/proposta/voice-health` com autorização e sem credenciais, unindo fluxo CRM + discador. **Nunca criar um endpoint que ative chamadas só por abrir a página.**

## 4. Ordem recomendada para a campanha V5
1. Primeiro **WhatsApp T0** (texto, PDF, imagem, botões corretos) — que mais impacta compra imediata.
2. Depois **vídeos/legendas** H+1→D+10, principalmente jogos reais e experiência infantil.
3. Só então **áudio do fotógrafo** por WhatsApp, testado em celular, e **voz do Papai Noel** opcional; evitar enviar áudio novo em cima de chamada na mesma hora.
4. Revisar SMS para reduzir redundância, sem habilitar slots adicionais.
5. Melhorar Resend T0 e confirmação pós-compra como jornadas diferentes.
6. Verificar SmartVoice na configuração efetiva e homologar uma chamada consentida de teste, sem disparar cinco chamadas em sequência.

## 5. Estados exigidos para um GO efetivo por canal
| Canal | Condição de GO |
|---|---|
| WhatsApp | Teste com/sem nome, caption renderizada, CTA exato, logs de aceito/entregue e STOP |
| SMS | número/emissor corretos, segmento, URL, consentimento, status real de entrega |
| Resend | ação elegível, domínio autenticado, assunto/texto final, logs de entrega e opt-out |
| SmartVoice | flag CRM + registry efetivo + WAV + DTMF + consentimento + ledger + teste telefônico autorizado |
| D+10 PDF | Supressão verificada nos snapshots ainda ativos e no catálogo futuro |

**NENHUM item acima foi ativado, pausado, cancelado ou alterado no CRM por este documento.**
