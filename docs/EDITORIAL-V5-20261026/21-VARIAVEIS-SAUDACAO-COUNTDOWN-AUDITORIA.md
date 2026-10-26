# 21 — Auditoria de variáveis nativas: saudação por hora e countdown Natal (V5.2)

**26/10/2026 | Status:** EDITORIAL_DRAFT; testado por leitura de código no CRM main; renderização real no worker/canário ainda pendente. **Não houve alteração no CRM, banco, manifesto aprovado nem disparos.**

## 1. O que o CRM realmente expõe

Origem: `hypeneural/evydencia-crm/app/Services/MessageVariableService.php` e `app/Services/CampaignWorkflow/VariableScope.php`.

| Token | Disponibilidade sem pedido | Resultado/uso seguro |
|---|---|---|
| `{saudacao}` | GLOBAL | `Bom dia` de 05:00 a 11:59; `Boa tarde` 12:00–17:59; `Boa noite` 18:00–04:59; hora de `config('app.timezone')` |
| `{primeiro_nome}` | CONTATO | Primeiro nome vindo do contato, somente campanha 415 e após verificação de nome válido |
| `{nome}` | CONTATO | Nome completo. Não usar desnecessariamente em abertura |
| `{natal_countdown_dias_descricao}` | GLOBAL | Frase em **dias completos até 25 de dezembro** (ex.: `Faltam 59 dias para o Natal.` pode variar com a hora), não horas até fechamento do estúdio |
| `{natal_countdown_semanas_descricao}` | GLOBAL | Frase em semanas completas (arredonda para baixo; `menos de 1 semana` nos dias finais) |
| `{natal_countdown_dias_horas_descricao}` | GLOBAL | Dias+horas, útil só se necessário, mais longa |
| `{natal_countdown_descricao}` | GLOBAL | Dias+horas+minutos, demasiado para a copy curta |
| `{natal_countdown_dias_uteis}` e `{natal_countdown_dias_uteis_descricao}` | GLOBAL | Dias úteis até Natal, **não é** prazo de entrega do ensaio |
| `{ano_novo_countdown_dias_descricao}`, `{ano_novo_countdown_semanas_descricao}`, `{ano_novo_countdown_dias_horas_descricao}`, `{ano_novo_countdown_descricao}`, `{ano_novo_countdown_dias_uteis}`, `{ano_novo_countdown_dias_uteis_descricao}` | GLOBAL | Contagem para 1º de janeiro, bom candidato futuro para mensagens pós-Natal |
| `{sessao_data}`, `{pacote_nome}`, `{checkout_url}`, `{codigo_pedido}`, `{nome_crianca}`, `{nome_audio}` | PEDIDO/CAKE | **NÃO usar na proposta fria** sem pedido e contexto validado; podem não resolver e aparecer literalmente no WhatsApp |

O parser `MessageVariableService::parseMessage` inicializa variáveis globais e de contato e substitui tokens em `title`, `message`, `description`, `link` e `carousel`. Em `CampaignMessageService`, a mensagem carregada é parseada no construtor, e a chamada de mensagem `action` usa seu `message`; vídeo/imagem usam `title` como caption.

**Limitações críticas:**
- `config('app.timezone')` deve ser `America/Sao_Paulo`; se a aplicação estiver em UTC, `{saudacao}` pode cumprimentar Elaine com um turno errado.
- O cálculo do Natal pode virar para a ocorrência **do próximo ano** depois de 25/12. Um disparo depois da temporada deve ser bloqueado por elegibilidade, mesmo que o token esteja preenchido.
- `{natal_countdown_dias_descricao}` dá **dias inteiros restantes por hora**, não um contador de dias de calendário nem deadline de reserva. Por exemplo, na tarde de 26/10 pode imprimir 59, enquanto pelo calendário há 60 datas até 25/12.
- A substituição é um `str_replace`; **não existe fallback automático** do campo editorial `fallback_title`. Token sem contexto/erro pode sair em texto cru se não houver pré-validação adicional.
- Snapshots congelados, filas e ações de provedor têm caminhos diferentes. Um teste do parser em objeto transitório **não prova** renderização do fluxo real completo.

## 2. Decisão editorial V5.2, sem novos envios
**T0 para 415:** começa com `{saudacao}, *{primeiro_nome}*! 🥰🎄`; **T0 para 416:** `{saudacao}! 🥰🎄`. Tom humano: “Que bom que você pediu nossa proposta”, duas cenas, 2011, preço e uma pergunta só. Não reintroduzir rodapé STOP repetitivo no T0; o botão STOP H+1 permanece e o pedido para parar deve ser respeitado em qualquer momento.

**H+1/H+90:** NÃO repetir `{saudacao}` dentro de 30 minutos; isso soa como automação. Mostrar mídia e responder com uma pergunta simples.

**D+1:** título executável do rascunho **continua estático**. Duas opções dinâmicas ficam em `video_d1.dynamic_candidate_title` e `video_d1.dynamic_candidate_weeks_title` como metadados para teste. `scripts/plan_manager_v5.py` não exporta essas opções para `messages.title`. Isso evita que um erro gere texto literal para o cliente.

**D+3/D+4:** jogos, 10 benefícios e CTA, sem sobrecarregar a pessoa com o mesmo countdown. Se o canário passar e o plano editorial aprovar, escolher **apenas UMA** mensagem para exibir o contador; a opção priorizada é D+1.

## 3. Prova local obrigatória do Laravel
No clone do CRM com vendor instalado, sem depender da VPS:
```powershell
$env:NATAL_CRM_ROOT = "C:\Projetos\hypeneural\evydencia-crm" # ajuste conforme o seu clone real
php scripts/test_natal_runtime_variables.php
```
O script cria mensagens transitórias (`new Message`) e usa `Carbon::setTestNow`, cobrindo 08:00, 15:00, 21:00 e proximidade de Natal. Ele não conecta a APIs nem faz disparos, porém inicializa Laravel localmente. Se o ambiente local precisar de banco no bootstrap, não contorne configurações de segurança só para rodar este teste.

**Próximas provas (fora da missão offline):**
1. Prévia autenticada/worker em ambiente não produtivo, 415 e 416, com horário e timezone corretos.
2. T0 `{saudacao}` resolvido em 3 períodos (dia/tarde/noite); nome normalizado/ausente.
3. D+1 dinâmica com `title` parseada **na hora do envio**, mesmo em `stage_snapshot`, sem token literal; evitar extrapolar limite `mb_strlen` após expansão.
4. Canário autorizado e consentido para mídia, em aparelho real, **somente após P0 D+10 resolvido**.
5. Caso falhe: manter a legenda estática e bloquear a publicação da versão com token, sem gambiarra via editor avulso.

## 4. Regras para o importador gerencial
Fonte única é `editorial/whatsapp-v5.draft.json`. Campos `message_with_name/without_name` para `intro_cta.message`; campo `title` para video/image; `action.message` para conversa e botões. **NÃO importar metadados de candidate como campos `title` ativos**. Preservar três botões T0, botão H+1 `PARARNATAL2026`, 9 SMS e série de voz. Não usar o contador do Natal para prometer dias de agenda. Publicar só por manifesto completo com expected hash/CAS/snapshot, não por UPDATE avulso.

## 5. Go/No-go
GO para editar e pré-visualizar V5.2 e rodar o parser no clone local. NO-GO para ativar novas mensagens, trocar o título D+1 por um token dinâmico, adicionar áudios à série V4, SMS, telefonemas ou qualquer alteração de produção. P0 do documento D+10 continua aberto.
