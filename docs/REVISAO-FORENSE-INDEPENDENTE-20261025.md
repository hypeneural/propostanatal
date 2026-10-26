# Revisão forense independente da auditoria Antigravity — 25/10/2026

> **Estado:** revisão de código/documentação realizada. NÃO é uma nova extração independente do MySQL, nem certificação de entrega de todos os canais. **O projeto segue em AUDIT_ONLY.**
>
> **Origens verificadas:** `hypeneural/propostanatal` @ `29dd659204936e405f9aa6471037cc33cb99b833`; classes das campanhas na main de `hypeneural/evydencia-crm`; `hypeneural/smartdialer-platform/config/voice_flows/registry.json`; documentos da Z-API. Resultados privados em `reports/` **não foram recebidos/reexecutados nesta revisão**.

## P0 — Retificações obrigatórias antes de editar produção

1. **Documento D+10: NÃO equiparar `title` a `caption`.** `CampaignMessageService::sendAttachment(..., title)` chama `MessageService::sendAttachment`; `WhatsappPayload::attachment` traduz esse parâmetro para **`fileName`**. Para vídeos/imagens, `title` é realmente `caption`. Portanto o nome `Comprovante_de_Transferencia Pix_01112026_200921` pode aparecer como nome do PDF no WhatsApp. Auditar **o conteúdo binário**: HTTP 200/MIME apenas indicam acessibilidade, não que o PDF seja comercial, seguro ou desprovido de dados financeiros/pessoais. Se for comprovante real, suspender uso comercial por mecanismo autorizado até revisão. Não presumir que mudar apenas `title` troque o arquivo.

2. **Snapshots imutáveis invalidam a afirmação de “edição imediata para os 11 pendentes”.** `NatalProposalExecutionPlan::captureNew` salva `stages[].legacy_messages` e `stages[].actions` no `campaign_scheduleds.suppression_snapshot.natal_execution_plan`. `CampaignPolicyLegacyMessages::hydrate` reconstrói os atributos salvos e `CampaignScheduledService` utiliza mensagens capturadas. A edição de `messages.title` no catálogo pode afetar novas execuções, **mas não deve ser apresentada como correção comprovada das capturadas**. Para as etapas 666/685, apurar quantas têm snapshot com `title` antigo e itens já materializados; definir procedimento seguro autorizado que preserve integridade, auditoria, cancelamentos, idempotência e histórico. Nunca alterar snapshots diretamente em SQL.

3. **`campaign_contact_stage_runs.status=completed` não equivale a entrega lida.** Reconstruir cadeia `run → delivery → delivery item → provider_request_attempts/communication_events` e separar `queued/submitting`, `provider_accepted`, `sent`, `delivered`, `read`, `failed`. Não afirmar “sete clientes receberam com sucesso” usando apenas contagens de runs. Um print do WhatsApp comprova aquele caso específico, não os sete.

4. **A saída `AUDITORIA-FINAL.md` contém métricas e conclusões literalmente fixadas dentro de `scripts/audit.php`.** O gerador escreve valores 89, 38, 12, 10, 54, 34, revisão/hash, contagens históricas e até host/data como strings estáticas. Isso gera um relatório falsamente “atual” após qualquer mudança de banco. Substituir por valores derivados de `$inventory` ou exibir `NÃO VERIFICADO`; jamais marcar automaticamente “AUDITORIA CONCLUÍDA”. Se faltar uma fonte/campo, registrar a lacuna.

5. **SmartVoice: estágio cadastrado e ativo não prova chamada habilitada.** A configuração de CRM lê `SMARTVOICE_NATAL_SERIES_ENABLED`, mas o script de auditoria MySQL não consegue provar sozinho o valor da flag do runtime. No `smartdialer-platform/config/voice_flows/registry.json` consultado, os cinco `natal2026-proposta-{1h,1d,5d,7d,10d}` aparecem com `enabled: false`; a configuração implantada pode ser diferente. Reconcilie feature flag CRM, registry efetivo do discador, áudio WAV real, consentimento, ledger de tentativas, origem AMI e desfecho da ligação. Não inferir TTS/transcrição do simples `flow_id`.

## P1 — Gaps de conteúdo e completude

6. **O inventário não preserva corretamente todos os botões.** No legado, `CampaignMessageService::makeActionButtons` lê `messages.buttons` como linhas delimitadas por `;` (e `makeButtons` usa CSV por linha). `scripts/audit.php` apenas tenta `json_decode` deste campo, descartando botões não JSON. Extrair o texto bruto sanitizado e interpretar por tipo, preservando rótulo, destino, ordem e status.

7. **SMS: recuperar 100% do texto, da condição e da entrega.** A V4 tem nove ações Velip por variante (H+2, D+1, D+2, D+3, D+4, D+5, D+10, D+15, D+20), mas o relatório canônico não apresenta a tabela completa de conteúdo, fallback, comprimento GSM/Unicode, transformação no provider, bloqueio por resposta/compra e recibos. Há texto `<3` potencialmente pouco natural e URLs diferentes em mensagens; conferir destino oficial. Relacionar `status` real e todas as flags `required` e `skip_if_converted`.

8. **E-mail: a V4 inicial e os papéis históricos R3 não são a mesma coisa.** Há e-mail no `initial` de V4; classes e assets R3 antigos também definem `email_h1`, `email_d3`, `email_d5`. Auditar materialização e histórico real por revisão, remetente, assunto, versão HTML/texto, elegibilidade de endereço e estados Resend. O script atual filtra `action.payload` e pode omitir HTML/variáveis críticas. Não deduzir que os e-mails R3 ainda são enviados apenas porque seus arquivos existem.

9. **Countdown já existe no PHP.** O catálogo de `MessageVariableService` inclui `{saudacao}`, `{natal_countdown_dias_descricao}`, `{natal_countdown_semanas_descricao}`, etc. Portanto, inserir tokens suportados em `title`/texto e ações renderizadas **não requer necessariamente código novo**; precisa verificar suporte no contexto, snapshot, momento da renderização, tamanho máximo e canal. Criar variável inédita de “vagas de sábado” requer implementação distinta e dado de agenda fundamentado.

10. **Auditoria não validou o conteúdo dos anexos.** As URLs públicas e HTTP 200 reportados pelo Antigravity não oferecem prova de conteúdo adequado ou segurança, nem demonstram SHA-256, duração/resolução de vídeo, codecs, primeira/última cena, clareza em celular, call-to-action ou metadados do PDF. Incluir uma amostragem de conteúdo antes de aprovar qualquer mídia, especialmente o documento D+10. Guardar prints e hashes localmente; não subir arquivos privados ao GitHub público.

11. **Relatório canônico diz “única divergência na primeira etapa”.** Restringir a conclusão ao catálogo *editorial V4 mapeado* e campos efetivamente comparados. Campanhas/execuções históricas, aceitação de nome válido, elegibilidade de e-mail, série voz e estados reais de ação exigem comparação adicional.

12. **Cautela com segurança operacional:** o repositório `propostanatal` é público e seu documento original menciona host/IP/banco internos. Não publicar credenciais, relatórios de execução com clientes, identificadores de envio, áudios privados ou links assinados. Considerar eliminar detalhes de infraestrutura da documentação pública.

## P1 — Instrumentação e verificações sugeridas (somente leitura)

Em acesso autorizado ao MySQL, conferir a estrutura real por `SHOW COLUMNS` antes de executar consultas.

Exemplo de sumarização de itens de entrega sem destinatários:

```sql
SELECT d.campaign_stage_id, d.channel, d.provider, i.status,
       COUNT(*) AS n_items,
       SUM(i.accepted_at IS NOT NULL) AS accepted_rows,
       SUM(i.provider_message_id IS NOT NULL) AS provider_id_rows
FROM campaign_stage_action_deliveries AS d
JOIN campaign_stage_action_delivery_items AS i ON i.delivery_id = d.id
WHERE d.campaign_stage_id IN (662, 681, 663, 682, 664, 683, 665, 684, 666, 685)
GROUP BY d.campaign_stage_id, d.channel, d.provider, i.status
ORDER BY d.campaign_stage_id, d.channel, d.provider, i.status;
```

Exemplo de conferência de TÍTULO congelado para o PDF D+10, sem dados dos contatos:

```sql
SELECT campaign_stage_id, status,
       JSON_UNQUOTE(JSON_EXTRACT(stage_snapshot,
          '$.legacy_messages[0].attributes.title')) AS frozen_title,
       COUNT(*) AS n
FROM campaign_contact_stage_runs
WHERE campaign_stage_id IN (666, 685)
GROUP BY campaign_stage_id, status, frozen_title;
```

Aviso: a primeira consulta mede estados intermediários, não comprovantes definitivos de entrega; a segunda pode retornar `NULL` quando o legado é resgatado por `communication_events`. Conferir as estruturas e versões antes de inferir ausência.

## Critério de encerramento atualizado

Marcar `AUDITORIA CONCLUÍDA` apenas quando:
- O script produzir um relatório **dinâmico**, sem totals ou cabeçalhos falsamente fixos.
- Capturar todos os textos, botões e descrições do catálogo, ações e templates sem vazamento de dados.
- Conciliar catálogo atual, histórico e snapshots dos envios pendentes.
- Apresentar, por etapa/canal, estados separados de programação, aceite, envio, entrega e falha.
- Explicitar quais conteúdos de voz são comprovados como WAV/áudio e quais são somente IDs.
- Certificar, com prova auditável, o PDF D+10 antes de recomendar alteração pontual de `title`.
- Registrar diferenças/efeitos entre alteração de catálogo para novos agendamentos e execuções congeladas.

**Nenhuma correção produtiva foi autorizada ou feita por este documento.**