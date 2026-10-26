# 18 — Auditoria do Dossiê 17 e Revisão Comercial V5.1 (sem VPS)

**26/10/2026 | Origem:** Dossiê 17 e cinco relatórios em `relatorios/homologacao-v5/`, manifesto aprovado V4 e classes do CRM. **Situação:** alteração apenas do repositório `propostanatal`. O CRM real, MySQL e provedor não foram modificados por esta revisão. **GO offline / NO-GO produção.**

## 1. Respostas ao bloco técnico do Dossiê 17

### P0 — os 13 D+10 e o comando de cancelamento
**Confirmação:** pela arquitetura documentada de `CampaignPolicyLegacyMessages::hydrate` / `NatalProposalExecutionPlan`, mensagens de agendamentos congelados podem ser reconstruídas do `stage_snapshot`. Alterar apenas `messages.title` pelo gestor ou trocar JSON editorial **não elimina** o risco para esses runs. O relatório de 16:15:25Z mostra **9 pendentes na etapa 666 da campanha 415 + 4 na etapa 685 da campanha 416 = 13**, com **1 `skipped`** separado. Trata-se de um instante, não quantidade atual.

**Reprovação da SQL anterior:** a proposta `CREATE TABLE IF NOT EXISTS ... AS SELECT` seguida de `UPDATE ... SET status='skipped'` **não é suficiente nem segura** para execução. A cópia não representa rollback atômico, há possível corrida com jobs já reivindicados/geração de outbox, o status `pending` é uma fotografia e a V4 ainda gera novos runs perigosos a cada nova proposta. A meta “14 skipped” também não é invariante do sistema.

**Critérios de solução:** (a) bloquear novos agendamentos/despachos do PDF enganoso enquanto a V4 seguir ativa; (b) fazer reconsulta LIVE autorizada com ID/run, status, snapshots, in-flight e outbox, sem publicar PII; (c) serviço administrativo transacional e idempotente com lock/CAS, backups seguros, auditoria e exclusão apenas do documento; (d) provar **zero envios elegíveis** depois, sem suprimir o vídeo D+10 e SMS. O mecanismo exato tem de ser revisado e implementado no CRM, não neste repositório editorial. **Nenhum comando DML executado.**

### Z-API / D+1 / consentimento
**Aprovo** o título D+1 estático, pois o campo `fallback_title` do JSON não cria fallback no Laravel. A variável `{natal_countdown_dias_descricao}` calcula até 25/12, não o prazo final da agenda.

O contrato CRM V4 limita `image/video.title` a **200 caracteres** via `mb_strlen`; isso **não é limite universal da Z-API**. No draft atualizado, nove captions de mídia ficam dentro do teto. Formatação WhatsApp com `*...`/`_...` e emojis é adequada, mas **nenhuma análise de caracteres garante ausência de spam**: opt-in/consentimento, relevância, elegibilidade, respostas, frequência e reclamações são indispensáveis. Botões Z-API exigem teste real quando o canário for autorizado.

**Nova decisão comercial de texto STOP (26/10):**
- T0, H+1, H+90 e D+2 **sem rodapé textual repetitivo**: o lead solicitou orçamento e deve ver primeiro a proposta.
- **D+3**: primeiro rodapé textual curto no próprio caption do vídeo de jogos, dentro de 200 caracteres. **D+4** e **D+10** repetem discretamente o opt-out em contatos posteriores.
- **Obrigação técnica que não se pode retirar na V4:** `Natal2026ProposalManifest::validate` exige um botão `REPLY` de ID `PARARNATAL2026` na segunda etapa `whatsapp_h1` (índice 1). Isso é diferente de repetir a frase no body. A primeira aparição de opt-out **não pode ser inteiramente ocultada do cliente** sem mudar esse contrato e reavaliar conformidade.
- **Nunca alterar** o reconhecimento de STOP a qualquer instante. `PARARNATAL2026`, `PARAR`, `SAIR`, pedidos em linguagem natural, bloqueios e negativas devem ser acolhidos imediatamente por atendimento e motor de supressão; verificar a implementação/normalização real antes de afirmar reconhecimento de todas essas variantes. Preferências em outros canais também devem ser respeitadas.
- O próprio pedido de orçamento **não deve ser presumido** como consentimento para SMS, telemarketing e 20 dias de remarketing; validar origem e escopo da permissão de contato.

Fonte externa: https://whatsappbusiness.com/policy/ (política oficial, atualizada em setembro de 2026). A escolha de NÃO repetir o rodapé em T0 é editorial; **não é dispensa de consentimento**.

## 2. Nova V5.1 — Copy aprovada para REVISÃO COMERCIAL, não produção

**Única fonte editável:** `editorial/whatsapp-v5.draft.json`, com prévia `14-COPY-CANONICA-V5-CANDIDATA.md` gerada pelo script. O Dossiê 17 e `V5-DECISAO-EDITORIAL.md` passaram a ser evidências históricas (avaliavam rodapé em todas as actions).

### Mudanças pontuais já feitas
| Momento | Campo exato | Comportamento V5.1 |
|---|---|---|
| T0 com nome/sem nome | `intro_cta.message_*` | Saudação curta, dois cenários, desde 2011, 2× R$98, pergunta semana/fim de semana; **sem rodapé STOP repetitivo** |
| H+1 | `duvidas_reply.message` | Pergunta de suporte, sem rodapé; preservar botão STOP obrigatório V4 |
| H+90 | `reserva_cta.message` | Escolha de horário sem repetir opt-out |
| D+2 | `lembrete_2dias.message` | Pergunta sobre horários, sem rodapé |
| D+3 | `video_d3.title` | Jogos + link demo, **primeiro rodapé textual STOP** |
| D+4 | `lembrete_4dias.message` | Substituição da ação já existente por **10 motivos curtos com emojis** + CTA + STOP, sem criar nova etapa/disparo |
| D+10 | `video_d10.title` | Convite para esclarecer dúvidas + rodapé STOP |
| Restante | mídia/cronogramas | Sem alterações de tipo, etapa, anexo, botões, SMS, e-mail ou SmartVoice |

### O que foi incluído na lista de dez motivos
Dois cenários em uma visita, Natal + Ano Novo, fotos reveladas, jogos digitais com fotos, galeria personalizada, figurinos infantis, bolhas de sabão, retratos de família, tradição em Tijucas desde 2011 e horários on-line. **Observações de claims:** galeria universal informada pela gestão ainda requer alinhar catálogo, checkout e PDF; disponibilidade de figurinos/bolhas depende do atendimento do dia. Não inserir brinde, certificado, música ou telefonema como promessa universal sem confirmação de estoque/escopo.

**Por que D+4:** já existe um `action.message` com botões aprovados nessa etapa. Consegue-se apresentar as dez razões sem novo disparo, enquanto o vídeo D+4 demonstra figurinos/bolhas. O novo texto é maior que uma caption, mas cada motivo cabe em uma linha curta. A lista deve ser reavaliada com leitura em aparelho real para não ficar excessivamente longa.

### Urgência com integridade
Os argumentos emocionais podem ser intensos, mas não inserir “últimos horários”, “só 6 semanas”, “90% ocupado” ou promoção R$20 sem data de encerramento/estoque/checkout aprovados. Em 26/10 faltam 60 dias para o Natal de 25/12, mas isso **não informa** o último dia possível de sessão e entrega. Usar somente disponibilidade real/URL do calendário, nunca falsos contadores.

## 3. Respostas ao bloco de próximos passos

### Plano até o P0 de 31/10
- **26/10:** auditamos Dossiê 17, corrigimos o rascunho, criamos verificações e rejeitamos SQL direto do P0.
- **27/10:** Antigravity deve confirmar o estado LIVE novamente, verificar worker/outbox e trazer uma proposta segura de supressão para engenharia do CRM; gerenciar também **novos runs** enquanto a V4 está ativa.
- **28/10:** aprovação explícita da operação por responsável técnico, com prévia dos IDs internos (não neste Git), testes de precondição/rollback e contenção do scheduler do documento.
- **29/10:** execução controlada do bloqueio P0 por responsável autorizado e reconsulta das entregas elegíveis, sem interferir no vídeo D+10.
- **30/10:** revisão final da cópia e dry-run do manifesto no ambiente operacional autorizado; canário de WhatsApp somente após P0 comprovado, com contato de teste consentido e sem afetar a base.
- **31/10:** último portão antes do primeiro horário histórico de 19:29:04 (**fuso a conferir**). Se não houver garantia de ausência de entrega perigosa, **manter NO-GO** e priorizar contenção operacional do documento.

Essas datas são **marcos recomendados**, não agendamentos já criados nem garantia de execução.

### Aprovação comercial em cinco minutos
Apresentar apenas `14-COPY-CANONICA-V5-CANDIDATA.md` com T0, H+1/90m, D+1/D+3/D+4/D+5/D+10, mídia e CTA; marcar `APROVADO`, `AJUSTAR` ou `DEPENDENTE DE MATERIAL` por role. A diretoria não precisa ler tabelas de snapshots para aprovar redação, mas a publicação fica bloqueada até as provas técnicas.

### Teste canário (não autorizado ainda)
Quando e somente quando os bloqueios forem resolvidos: um número de teste autorizado e opt-in, variante 415 e 416 com nome válido/sem nome, confirmação de tempo/âncora, textos formatados, mídia/hash e caption, PDF correto, botões no aparelho, link de jogo, STOP voluntário, resposta humana, supressão de SMS/voz após STOP/compra, delivery ACK vs delivered/read e **nenhum D+10 Pix**. Não ativar chamadas reais no mesmo canário de WhatsApp sem consentimento telefônico separado.

### Áudio humano do fotógrafo e SmartVoice
Recomendo **gravar o áudio humano de 15–20 segundos agora**, mas mantê-lo no acervo privado. Não adicionar automaticamente em T0/H+1, pois a V4 já entrega várias mídias e o contrato de papéis fixa a sequência. Experimentar depois substituição de um contato redundante por áudio humano, após revisão de contrato e avaliação comercial. SmartVoice permanece `VOICE_STATUS_INDETERMINADO`, separado da homologação da copy; ativar só depois da fase inicial WhatsApp estável, com registry efetivo, WAVs, CDR/answered, política de telefonemas e controle de opt-out.

## 4. Parecer definitivo desta rodada
**GO:** homologação editorial offline da V5.1 e preparação do pacote de importação versionada.  
**NO-GO:** colar conteúdo direto nas mensagens selecionadas no painel sem recalcular hash, aplicar manifesto, disparar canário/produção, executar SQL de supressão genérico ou ativar voz.  
**Bloqueios:** P0 D+10 em duas frentes; aprovação comercial da lista de 10 motivos e galeria; cronograma ativo/consentimento; pacote de publicação Laravel com CAS, snapshot e plano de retorno; teste de renderização e supressão end-to-end.
