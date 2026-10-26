# 21 — Dossiê Mestre V5.1: Homologação Final, Contenção P0 e Gestor (Para Validação pelo ChatGPT)

**Data da Auditoria:** 2026-10-26T17:00:44Z  
**Versão Editorial:** V5.1 (`editorial/whatsapp-v5.draft.json`)  
**Status Oficial:** **`V51_EDITORIAL_READY_FOR_DIRECTOR_APPROVAL / P0_PRODUCTION_NO_GO`**  
**Repositório de Auditoria:** `https://github.com/hypeneural/propostanatal`  
**Repositório Canônico:** `hypeneural/evydencia-crm`  
**Destinatário da Auditoria:** ChatGPT / Diretoria Comercial e Engenharia do Estúdio Evydência  

---

## 1. Sumário Executivo da Missão V5.1

Esta missão conclui a homologação da esteira **V5.1 de Natal 2026**, incorporando a nova política comercial aprovada pela diretoria (redução da densidade de opt-out textual nos primeiros contatos, preservação do botão REPLY regulamentar em H+1 e inserção dos 10 motivos para agendar em D+4), acompanhada da reconsulta LIVE do banco de dados e do plano de contenção do flyer Pix (P0 D+10) em duas frentes.

---

## 2. Inovações e Ajustes Comerciais da V5.1 (WhatsApp)

1. **Remoção de Rodapés Textuais Repetitivos:**
   * Eliminada a frase de descadastro repetitiva em T0 (`initial`), H+1 (`whatsapp_h1`), H+90 (`whatsapp_90min`) e D+2 (`whatsapp_d2`). O lead acabou de solicitar a proposta e deve focar na experiência e valores.
2. **Preservação Obrigatória do Botão REPLY em H+1:**
   * Mantido o botão de resposta rápida `PARARNATAL2026` em H+1, cumprindo estritamente a exigência do contrato V4 (`Natal2026ProposalManifest.php`), sem poluir o corpo do texto.
3. **Primeiro Aviso Textual em D+3:**
   * O primeiro opt-out textual na esteira ocorre na legenda do vídeo de jogos (`video_d3.title`), totalizando 166 caracteres Unicode (limite máximo do CRM: 200).
4. **Os 10 Motivos Curtos em D+4:**
   * Aproveitamento da ação já existente `lembrete_4dias.message` para listar os 10 benefícios da Vila de Natal (dois cenários, fotos reveladas, jogos digitais, galeria online, figurinos inclusos, bolhas de sabão, família toda, hora marcada, tradição desde 2011, entrada em 2x R$ 98) com aviso discreto de STOP, **sem criar novos envios ou etapas**.
5. **D+10 com Fechamento Discreto:**
   * Caption de suporte e acolhimento com aviso de opt-out (`video_d10.title`, 174 caracteres).
6. **Legenda Estática em D+1 e Pergaminho em D+5:**
   * Mantida a caption literal segura em D+1 (`video_d1.title`) e o alinhamento com a cena do pergaminho iluminado em D+5 (`video_d5.title`).

---

## 3. Diagnóstico LIVE P0 D+10 e Contenção Segura em Duas Frentes

A nova extração read-only do banco de produção em 2026-10-26T17:00:44Z confirmou:
* **Fila Atual Congelada:** **13 agendamentos pendentes** (9 na campanha 415 com nome + 4 na campanha 416 sem nome) e **1 skipped** retendo o flyer Pix (`natal2026-rev357-v1-d10-document.pdf`) em `stage_snapshot`.
* **Janela Temporal:** Primeiro disparo agendado para **31/10/2026 19:29:04**. Zero entregas realizadas até o momento (`delivery_items = 0`).
* **Risco Dinâmico da V4 Ativa:** Enquanto a campanha V4 seguir ativa recebendo novos leads, **o CRM continuará criando agendamentos para D+10 com o flyer Pix**.
* **Rejeição do SQL Simplificado:** O comando `CREATE TABLE AS SELECT ... UPDATE` foi rejeitado por quebrar o isolamento transacional, não tratar concorrência com o worker e ignorar novos leads.
* **Solução de Contenção em Duas Frentes:**
  1. *Frente 1 (Entrada):* Desativação da ação de anexo nas etapas 666 e 685 no CRM para estancar novos agendamentos da V4.
  2. *Frente 2 (Fila Congelada):* Comando Artisan dedicado (`php artisan proposals:suppress-pix-attachment`) com transação `DB::transaction()`, lock pessimista, verificação de lease do worker, registro em log de auditoria e transição dos 13 agendamentos para `status = 'skipped'`, preservando o vídeo D+10 e os SMS.

---

## 4. Matriz do Gestor e Publicador sem VPS (Diff V5.1)

O script `python scripts/plan_manager_v5.py` gerou com sucesso o mapeamento de **30 operações de campo** (15 na Campanha 415 e 15 na Campanha 416).
* **Alerta Crítico:** Operadores **NÃO devem clicar em "Salvar"** no editor de mensagens atual das campanhas 415/416. O editor comum altera apenas a tabela `messages`, causando divergência de `configuration_hash` (`natal_manifest_configuration_mismatch`) e não alterando os snapshots congelados.
* **Arquitetura da Solução:** Desenho da evolução do painel com Rascunho Isolado → Simulador WhatsApp → Validador de Contratos (teto 200 caracteres e botões fixos) → Diff Lado a Lado → Publicação Atômica CAS (`expected_configuration_hash`) e Rollback.

---

## 5. Status dos Canais Complementares

* **SMS (9 slots):** Preservados em `preserve_existing_v4`. Recomenda-se para fase posterior a padronização em GSM-7 para conter custos e integração do descadastro.
* **Resend T0:** Preservado em `preserve_existing_t0` em modo texto, sem anexo PDF. Fluxo pós-compra mantido como jornada separada de pedido.
* **SmartVoice (5 fluxos):** Mantido em `no_activation` (`VOICE_STATUS_INDETERMINADO`). Documentada a distinção técnica de que eventos `delivered` não comprovam chamadas atendidas (`answered`). Proposta da rota `GET /gestao/natal/proposta/voice-health`.

---

## 6. Índice dos Novos Relatórios Versionados

Os 4 relatórios analíticos objetivos da Missão V5.1 estão publicados em `docs/EDITORIAL-V5-20261026/relatorios/v51/`:
1. [V51-APROVACAO-COMERCIAL.md](relatorios/v51/V51-APROVACAO-COMERCIAL.md): Matriz completa de aprovação comercial dos 19 papéis, contagens de caracteres e claims.
2. [P0-D10-TRAVA-SEGURA.md](relatorios/v51/P0-D10-TRAVA-SEGURA.md): Diagnóstico LIVE dos 13 agendamentos pendentes e especificação da contenção dupla (novos leads + fila antiga) sem DML.
3. [GESTOR-IMPORT-DIFF.md](relatorios/v51/GESTOR-IMPORT-DIFF.md): Mapeamento das 30 operações de campo geradas pelo `plan_manager_v5.py`.
4. [CANAIS-E-GO-NOGO.md](relatorios/v51/CANAIS-E-GO-NOGO.md): Situação multicanal e matriz de portões de release.

---

## 7. Roteiro e Perguntas para Auditoria do ChatGPT

Solicitamos que o **ChatGPT** audite os seguintes aspectos e forneça direcionamento:

1. **Validação da Esteira V5.1:**
   * A nova cadência de opt-out (início do aviso textual em D+3, preservação do botão REPLY em H+1 e 10 motivos em D+4) equilibra adequadamente a conversão comercial e a conformidade regulatória com as políticas da Meta/WhatsApp?
2. **Auditoria da Contenção P0 D+10:**
   * Você aprova o desenho da contenção em duas frentes (estancar novos leads da V4 + comando Artisan transacional com lock para os 13 runs congelados)?
3. **Recomendações e Próximos Passos:**
   * Qual deve ser o cronograma de homologação e implantação recomendado para a equipe até 31/10/2026?
   * Quais cuidados específicos devem ser observados na aprovação comercial da lista dos 10 motivos junto à gerência do estúdio?

---

**FIM DO DOSSIÊ 21 — PRONTO PARA VALIDAÇÃO E AUDITORIA PELO CHATGPT.**
