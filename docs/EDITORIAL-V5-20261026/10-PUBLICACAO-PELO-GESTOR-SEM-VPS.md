# 10 — Auditoria de publicação pelo GESTOR, sem acesso à VPS

**Data:** 26/10/2026 · **Escopo:** campanhas 415 e 416 · **Status:** PARECER TÉCNICO, nenhuma escrita em produção.

## Veredito rápido
É possível **preparar, revisar e pré-visualizar integralmente** a V5 pelo repositório local, e **consultar** as etapas/itens do gestor no navegador autenticado. **Não é seguro concluir que “clicar Salvar” publica conteúdo sem impacto**, pois o pipeline V4 exige configuração aprovada com SHA/hash; o editor normal de mensagens não mostra uma reaprovação atômica do manifesto V4.

## Evidências de código (CRM main inspecionada 26/10)
- `app/Services/CampaignFlow/CampaignFlowEditLinkResolver.php`: links de edição de estágio, mensagem e ação.
- `app/Http/Controllers/Manager/MessageController.php`: `update()` chama `MessageService::updateMessage`, sem comprovação de reconciliação simultânea do manifesto/hash. Sucesso na tela != prontidão.
- `app/Services/Proposals/InspectNatalProposalReadiness.php`: compara `ProposalReadinessSnapshot::configurationHash($snapshot)` ao `flow.settings.readiness.manifest.configuration_hash`; divergência causa `natal_manifest_configuration_mismatch`.
- `app/Services/Proposals/Natal2026ProposalManifest.php`: perfis 4 de 19 etapas por variante; `title` de mídia até 200 caracteres; títulos/corpo/rodapé de ação em campos distintos; botão T0 com **IDs e URLs aprovados** (catálogo, horários e Instagram), não checkout direto.
- `app/Services/Proposals/NatalProposalExecutionPlan.php` e `CampaignPolicyLegacyMessages.php`: snapshots de execuções anteriores retêm textos/ações. Editar mensagem selecionada não altera automaticamente esses snapshots.
- `app/Console/Commands/ProvisionNatal2026ProposalCampaignCommand.php`: `--dry-run` padrão, `--apply --expect=<state_hash>`, snapshot criptografado/rollback. É via CLI no runtime atual, **não comprovação de “botão Publicar V5” no gestor**.
- `CheckProposalFlowHealth::diagnose` persiste estado de saúde; `php artisan proposals:doctor natal` NÃO é leitura estrita.
- `MessageRequest` já permite upload de áudio mp3/ogg/opus, vídeos, imagem e documento em mensagens genéricas do CRM, **mas** criar uma mensagem `audio` na biblioteca não a acrescenta automaticamente ao catálogo V4 congelado.

## Classificação dos atos no painel
| Operação | Pode fazer agora sem VPS? | Risco | Instrução |
|---|---|---|---|
| Abrir o fluxo visual e prévias GET 415/416 | SIM | Baixo | `/gestao/campanhas/415/fluxo`, `/416/fluxo`; não clicar em salvar |
| Conferir texto atual, título, URL, botões, asset | SIM | Baixo | Exportar matriz local, sem dados de clientes |
| Redigir V5 e simular WhatsApp no projeto independente | SIM | Baixo | `scripts/preview_whatsapp_v5.py` |
| Gravar áudio próprio e Papai Noel, editar mídia local | SIM | Baixo | Salvar assets em diretório privado, hashes + preview |
| Criar arquivo de mídia na biblioteca sem associar à V4 | SOMENTE após verificar que a ação não dispara, altera hashes ou reutiliza mensagem de produção | Médio | Preferir preparar localmente primeiro |
| Alterar título/caption `messages` selecionada nas etapas V4 | **NÃO AGORA** | Alto | Pode causar `natal_manifest_configuration_mismatch`, e não corrige snapshots existentes |
| Alterar `action.message`, botões, URL ou cronograma 415/416 | **NÃO AGORA** | Alto | Afeta SHA aprovado; botões T0 têm allowlist exata |
| Remover documento D+10 pelo editor | **NÃO É SUFICIENTE** | Crítico | Pode não proteger 12 runs congelados; operação especializada autorizada |
| Ativar ligações ou enviar teste real a contato | NÃO nesta fase | Alto | Exige consentimento, validação de janela, logs e autorização |
| Publicar V5 por painel sem deploy/VPS | **RECURSO NÃO COMPROVADO** | Alto | Implementar um publicador versionado uma vez, OU usar provisioner existente em ambiente autorizado após aprovação |

## O menor caminho sem administrar a VPS rotineiramente
**Opção A — caminho imediato sem alteração de código:** manter V4 ativa. Finalizar V5 no `propostanatal`, revisar no gestor por GET, construir manifesto CRM **completo** em ambiente local e validar seus limites. Quando aprovado, **uma publicação controlada** com provisionador existente e snapshot/hash esperado deve ser executada por ambiente operacional autorizado. Não há prova de que isso possa ser concluído 100% pelo navegador com o código atual.

**Opção B — objetivo “editar tudo pelo painel, nunca SSH”:** uma única evolução do CRM: `EditorialRevision` em rascunho → clonar dados selecionados para novos IDs/versionamento → preview exato por canal → confirmar diffs → gerar manifesto/hash atômico, com CAS `expected_state_hash`, snapshot de rollback e lock transacional → recarregar prontidão → autorizar novas propostas → preservar as antigas congeladas. Exibir avisos de alteração de botão, mídia e SMS. Essa evolução exige **uma entrega de software inicial**, não deploy a cada legenda futura. Não criar esse endpoint fora do CRM ou em scripts públicos com credenciais.

## Como fazer revisão com o gestor HOJE
1. Abrir 415/416 em abas autenticadas; comparar cada role, título, imagem, PDF, CTA, botões e textos com `editorial/whatsapp-v5.draft.json`.
2. Validar imagem de pacotes e PDF T0 abrindo o arquivo, incluindo preços, jogos e galeria de todos os pacotes.
3. Conferir texto do novo áudio/vídeo de D+3/D+4, mas **não associar mídia nova** ao fluxo antes da revisão contratual.
4. Aprovar comercialmente T0, H+1/H+90 e D+1 a D+10; agendar publicação controlada somente quando o P0 do documento Pix estiver resolvido.
5. Solicitar um teste de **prévia sem envio** para 415, 416, cliente sem nome, token countdown, opt-out e compra; registrar outcomes em arquivo local ignorado pelo Git.

## P0 separado: documento D+10
A última auditoria local relatou **12** runs pendentes em `campaign_contact_stage_runs` para o PDF com aparência de comprovante Pix. É uma foto temporal de 26/10, não um número constante. Antes de 31/10 ou do primeiro horário REAL ainda pendente, consultar novamente por SELECT e preparar supressão específica autorizada com reversibilidade, idempotência e preservação de recibos. Mudar `messages.title` ou marcar `blocked` no JSON editorial não interrompe o worker em produção.

## Critérios para publicar
Revisão comercial aprovada; `manifest.configuration_hash` novo válido; todos os roles e IDs reconciliados; URLs T0 no validador; PDFs e vídeos corretos; tokens resolvidos ou trocados por texto estático; frozen snapshots analisados; cancelamento D+10 comprovado; STOP/conversão/resposta humana respeitados; logs e rollback. **Sem essas evidências, GO somente para rascunho/preview, não publicação.**
