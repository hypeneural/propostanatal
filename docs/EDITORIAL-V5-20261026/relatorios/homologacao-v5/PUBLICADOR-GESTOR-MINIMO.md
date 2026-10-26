> **REVISÃO DE SEGURANÇA (26/10):** A opção de interface para “cancelar todos os agendamentos pendentes” é ampla demais. O publicador futuro deve identificar exatamente **qual documento/asset/role** é perigoso, proteger worker/outbox e impedir novos runs. Não basta transicionar runs por ID de etapa. Editar `MessageController.update` não reaplica hash; vide [19 Handoff](../../19-HANDOFF-ANTIGRAVITY-GESTOR.md). Nenhum publicador foi desenvolvido no CRM nesta rodada.

# Especificação Arquitetural: Publicador Versionado pelo Gestor (Sem VPS)

**Data:** 2026-10-26T16:15:25Z  
**Objetivo:** Permitir que a diretoria comercial e operadores aprovem e publiquem revisões de campanhas pelo painel web, eliminando a dependência recorrente de comandos de terminal SSH e scripts na VPS.  
**Princípio de Engenharia:** Transações atômicas com Compare-And-Swap (CAS), hash de configuração e rollback criptografado.

---

## 1. O Diagnóstico Técnico: Por Que o Painel Atual Não Publica com Segurança

A auditoria no código-fonte do CRM Laravel revelou:
1. **Divergência de Hash:** `InspectNatalProposalReadiness.php` valida a integridade da proposta comparando o hash calculado das mensagens com `flow.settings.readiness.manifest.configuration_hash`.
2. **Edição Não Atômica:** Quando um operador clica em "Salvar" no editor de mensagens (`MessageController.php::update`), apenas a tabela `messages` é atualizada. O manifesto não é reaprovado e o hash da campanha não é recalculado, disparando o erro operacional `natal_manifest_configuration_mismatch`.
3. **Inocuidade para Agendamentos Existentes:** Como comprovado no P0 de D+10, editar mensagens na tela não altera os dados serializados em `campaign_contact_stage_runs.stage_snapshot`.
4. **Provisionador Restrito ao Terminal:** O provisionamento atômico atual (`ProvisionNatal2026ProposalCampaignCommand.php`) só existe como comando de console Artisan CLI (`--apply --expect=<state_hash>`).

---

## 2. Desenho da Arquitetura do Publicador no Painel Web

A solução consiste em uma única evolução estrutural no CRM Laravel, viabilizando o ciclo de vida completo pelo navegador:

```
[Rascunho Editorial] ──> [Validador de Contratos] ──> [Diff Lado a Lado] ──> [Publicação Atômica CAS] ──> [Rollback Seguro]
```

### 2.1. Modelo de Rascunho (`campaign_flow_drafts`)
* **Isolamento Total:** As alterações de copy, legendas e botões são salvas em uma tabela de rascunho (`campaign_flow_drafts`), sem tocar nas mensagens ativas da campanha 415/416.
* **Simulador Visual Integrado:** O operador visualiza no navegador o balão nativo do WhatsApp com quebras de linha reais, negrito e itálico renderizados exatamente como na Z-API.

### 2.2. Validador Automático de Contratos de Provedor
Antes de permitir a aprovação, o sistema executa regras em tempo real na tela:
* Limite estrito de **200 caracteres Unicode** para legendas de vídeos e imagens (`mb_strlen`).
* Validação da allowlist de botões T0 (IDs fixos e URLs oficiais autorizadas).
* Verificação de fechamento de tags `*` e `_`.
* Alerta visual caso um token dinâmico (ex: `{natal_countdown_dias_descricao}`) não tenha fallback comprovado.

### 2.3. Comparador de Diferenças (Diff Auditado)
* Exibição clara lado a lado (Antes vs. Depois) campo a campo por etapa.
* Destaque em cores para alterações de textos, mídias vinculadas e cronogramas.

### 2.4. Publicação Atômica com Controle de Concorrência (CAS)
Ao clicar no botão **"Publicar Nova Versão da Campanha"**:
1. **Lock Otimista (CAS):** O backend confere se `current_configuration_hash == expected_configuration_hash`. Se outro operador fez alterações simultâneas, a publicação é bloqueada com aviso.
2. **Snapshot de Rollback:** O CRM gera um backup criptografado do estado imediatamente anterior (`proposals_history_snapshots`).
3. **Transação Atômica:** Em uma única transação de banco (`DB::transaction`):
   * Clona/atualiza os registros de mensagens e vínculos.
   * Recalcula e grava o novo `configuration_hash`.
   * Atualiza `flow.settings.readiness.manifest`.
   * Registra o log de auditoria com o usuário autenticado, data e diff.
4. **Tratamento de Runs Congelados:** A tela exibe um checkbox obrigatório: *"Deseja aplicar esta revisão apenas para novos clientes ou cancelar agendamentos pendentes com a versão anterior?"*, evitando incidentes como o de D+10.

---

## 3. Plano de Transição Imediato (Enquanto o Recurso é Desenvolvido)

1. **Fase Atual:** Nenhuma alteração manual isolada deve ser salva no painel das campanhas 415 e 416.
2. **Homologação:** O trabalho editorial permanece centralizado no repositório `propostanatal` (`editorial/whatsapp-v5.draft.json`), validado via `run-editorial-checks.ps1`.
3. **Publicação Controlada:** Quando a homologação for autorizada pela diretoria, será executada **uma única publicação controlada** via provisionador oficial do CRM com hash esperado e rollback garantido.
