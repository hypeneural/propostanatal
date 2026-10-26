# 19 — Como importar a copy V5.1 no gestor do CRM sem quebrar a proposta

**Data: 26/10/2026. STATUS: ESPECIFICAÇÃO DE IMPORTAÇÃO, NÃO FERRAMENTA DE ENVIO.**
A versão atual do gestor permite **consultar/pré-visualizar e editar mensagens individuais**, mas não foi encontrada publicação atômica de um manifesto Natal V4 com reaprovação do `configuration_hash`. Portanto **NÃO clicar em Salvar** nas mensagens do fluxo 415/416 apenas para copiar a V5.1.

## 1. Fontes e contratos
- Origem da copy: `editorial/whatsapp-v5.draft.json` (19 roles; só nove roles WhatsApp ajustáveis).
- Prévia para direção: `14-COPY-CANONICA-V5-CANDIDATA.md` gerado, nunca digitar dois lugares.
- Prévia técnica de campos por campanha: `python scripts/plan_manager_v5.py` — imprime JSON **NÃO EXECUTÁVEL**; `--check` valida o mapeamento.
- Contraexemplo NÃO executável: Dossiê 17 e relatórios históricos não devem ser importados como payload.
- Modelo CRM: `Message` de tipo `action` usa `messages.message` como corpo; tipo `video/image` usa `messages.title` como caption Z-API; `attachment` usa `messages.title` como `fileName`. `description` **não é caption** do caminho identificado.
- V4 exige **19 estágios e ordem fixa**, ações de e-mail/SMS originais, tipos de mensagem e botões com IDs/URLs aprovados; ação H+1 possui um botão REPLY `PARARNATAL2026` que **não pode ser excluído** pelo provisionador V4.
- Provisão atual: `php artisan proposals:provision-natal-2026 --manifest=<path> --dry-run` e `--apply --expect=<state_hash>` no CRM, com snapshot/rollback. O JSON editorial **não é** um manifesto provisionável.

## 2. Fluxo ideal do importador no painel (uma evolução no CRM)
```text
Gerenciar Natal 2026
  -> Editor em RASCUNHO (não toca nas campanhas atuais)
  -> Importar JSON editorial v5.1 (somente texto, 415/416)
  -> Resolver cada stage_role/message_role no manifesto ativo + IDs atuais
  -> Preview WhatsApp real por aparelho / canal
  -> Validar legendas 200, botões exatos, STOP, tokens, mídia, contrato, timezone
  -> Diff antes/depois por CAMPO + hash esperado
  -> [Aprovação da gestão]
  -> Publicação ATÔMICA versionada (CAS + locks + snapshot de rollback)
  -> Revalidar readiness e elegibilidade de novos versus frozen runs
  -> [Canário com número autorizado, somente depois de P0 D+10]
```

**Backend deve rejeitar:** IDs históricos usados diretamente, 19 roles com mensagem extra, URL de checkout diferente da allowlist V4, ausência do botão de stop H+1, anexo D+10 perigoso, texto longo na caption, token não suportado no worker, versão de configuração divergente, payload de SMS/e-mail/voz modificado sem aprovação específica, importação de dados de clientes/contatos.

**Botão “Publicar” NÃO significa executar os mesmos `UPDATE` atuais do editor:** é um endpoint novo restrito a gestão autorizada que reaproveita as verificações do provisionador e seu CAS/snapshot; opera preferencialmente com cópia imutável e revisão declarada, não mutações avulsas em mensagens selecionadas. Projeto no CRM depende de autorização e uma entrega inicial de software (aqui apenas especificado).

## 3. Matriz comercial V5.1 para o gestor

| Momento | Canal/tipo V4 | Campo alterado | Ideia principal |
|---|---|---|---|
| T0 | `intro_cta` action | `message` com nome e sem nome | Que bom que pediu! Dois cenários, R$196 em 2×R$98, desde 2011 e pergunta natural. Sem footer repetitivo |
| T0 | `cenarios_image` image | `title` | Legenda dos três pacotes, fotos e cenários |
| T0 | `proposta_pdf` attachment | `title` | Nome de arquivo legível .pdf |
| H+1 | `video_1hora` video e `duvidas_reply` action | `title`/`message` | Experiência real e disponibilidade; preservar botão REPLY STOP V4 |
| H+90 | `video_90min` video e `reserva_cta` action | `title`/`message` | Escolher data com facilidade |
| D+1 | `video_d1` video | `title` | Memórias / texto estático sem contador não homologado |
| D+2 | `video_2dias` e `lembrete_2dias` | `title`/`message` | Segundo cenário e pergunta curta |
| D+3 | `video_d3` video | `title` | Jogos demonstrativos + primeiro footer STOP textual |
| D+4 | `video_4dias` video e `lembrete_4dias` action | `title`/`message` | Bolhas/figurinos no vídeo e 10 motivos em texto de apoio + STOP |
| D+5 | `video_d5` video | `title` | Pergaminho mágico |
| D+10 | `video_d10` video | `title` | Dúvidas e STOP discreto |
| D+10 PDF | `document_d10` attachment | **NÃO IMPORTAR** | Peça Pix em NO-GO, revisar run congelado E novos runs |
| SMS, Resend, SmartVoice | outros canais | **SEM ALTERAÇÃO** | Fase separada, somente auditada |

## 4. Controles mínimos por etapa (testes sem disparo)
- Variantes 415 (nome válido) e 416 (neutra); não substituir `{primeiro_nome}` por um texto genérico no banco por engano.
- Mensagem T0 contém URL de horário via botão fixo, PDF e arte; arquivos reais e consentimentos antes de produção.
- Opt-out textual começa em D+3 conforme direção; botão H+1 continua obrigatório V4; o usuário pode pedir STOP em qualquer ponto — verificar toda a régua Z-API/SMS/voz, não assumir que há supressão transversal sem prova.
- Lista D+4 ocupa uma **ação existente**, sem nova mensagem ou reprogramação; detalhes de itens dependem do contrato vigente e execução real.
- Não colocar “10 jogos em todos os pacotes” (são 1/5/10 conforme pacote); galeria universal declarada pela gestão ainda precisa espelhar checkout/PDF.
- Novos áudios/imagens exigem cadastro de asset, SHA, payload e revisão de contrato da campanha; **não associar mídia extra** silenciosamente.
- A alteração do texto dos botões T0 preserva IDs e URLs; novos destinos requerem evolução do validador Laravel.

## 5. Caminho mais rápido sem abrir a VPS regularmente

**AGORA:** aprovar o JSON e prévias no `propostanatal`, usar gestor apenas em modo GET para comparar, testar links em Chrome, validar 9 SMS/Resend/voz e corrigir P0 D+10 por rotina operacional autorizada.

**UMA ÚNICA ENTREGA DE ENGENHARIA NO CRM:** construir o publicador web versionado a partir dos componentes já existentes (`Natal2026ProposalManifest`, `Provisioner`, `ProposalReadinessSnapshot`), com CAS/hash, lock, snapshot e rollback. Só então o gestor poderá importar a copy e publicar sem acesso à VPS.

**ALTERNATIVA MAIS RÁPIDA SEM NOVO FRONT:** gerar manifesto completo aprovado e usar **uma publicação controlada pelo provisionador já existente** em ambiente operacional autorizado, após P0 comprovado. Isso reduz engenharia, mas não é publicação 100% pelo gestor e não habilita copy/paste seguro no editor atual.

**Conclusão:** disponibilizar *prévia* não é disponibilizar *publicação*. O plano atual permite revisar e aprovar, mas não importar automaticamente ao vivo. No cenário de 26/10, **P0 NO-GO**.
