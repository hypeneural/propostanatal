# 06 — Handoff para Antigravity que está executando a revisão V5
**Adendo isolado | 26/10/2026 | Nenhuma mudança de produção.**

## Por que este documento existe
Enquanto o Antigravity revisa a V5 original, a direção trouxe **novos destinos públicos de demonstração**, entrega universal declarada de **galeria**, materiais físicos novos (**livrinho de colorir**) e detalhes de experiência presencial (**entrada interativa e bolhas acionadas por botão mágico**). O repositório foi atualizado com NOVOS arquivos sem modificar diretamente os rascunhos que o agente anterior está usando.

## Arquivos novos desta rodada
1. `docs/EDITORIAL-V5-20261026/04-SITES-LINKS-E-EXPERIENCIA.md` — URLs, evidências, testes reais de UX, discrepâncias do catálogo.
2. `docs/EDITORIAL-V5-20261026/05-COPY-CURTA-JOGOS-GALERIA-CONFIANCA.md` — copies alternativas curtas com formatação WhatsApp, encaixe por papel T0 → D+10 e pós-reserva.
3. `editorial/experience-links.v5.addendum.json` — catálogo de links/claims e sugestões **não executáveis**, sem substituir `editorial/whatsapp-v5.draft.json`.
4. `scripts/check_public_links.py` — status HTTP público e read-only; browser obrigatório para games/galeria/calendário/checkout.

## Sequência de execução — evitar trabalho duplicado
1. `git pull --ff-only origin main` antes de iniciar a revisão do próximo papel, sem descartar mudanças locais do agente.
2. Leia os quatro arquivos acima; não copie automaticamente o adendo JSON para o manifesto de produção.
3. `python scripts/check_public_links.py --list-only` para conferir endereços; `python scripts/check_public_links.py` para sondar HTTP. Em seguida confirme por **Chrome real** jogos, galeria e fotos demo (SPA, mobile e privacidade). Se falhar GET em Instagram, não concluir “perfil fora do ar”; abrir navegador.
4. Confirme a regra comercial: site público diz 1/5/10 jogos por pacote, galeria expressa em apenas um; direção diz galeria em todos. Não mudar preço/pacote sem evidência do CRM e aprovação.
5. Faça revisão PONTO A PONTO do T0, H+1, H+90, D+1, D+2, D+3, D+4, D+5, D+10. Para cada novo elemento `04`/`05`, registre `KEEP`, `REPLACE` ou `DEFER`, a mídia/URL e o motivo. Prioridade: T0 curto + PDF + arte + 3 botões; D+3 DEMONSTRAÇÃO jogos+galeria; D+4 efeito bolhas/brindes; D+5 confiança e desde 2011.
6. Se o vídeo D+4 não mostra bolhas/medalhinha, **não colar legenda falsa**: gere demanda por vídeo real autoral/clip de estúdio e mantenha outro texto.
7. Continuar documento de diferenças V4 → V5 em pasta local `reports/`, sem commits de dados de produção; só publicar aqui decisões editoriais sem PII.
8. Preservar SMS, e-mail Resend e SmartVoice configurados como estão enquanto a revisão WhatsApp acontece. Ligação real ≠ áudio gravado WhatsApp.
9. A aprovação de texto não autoriza envio: contrato do CRM verifica `configuration_hash`; snapshots legados podem prevalecer; D+10 PDF de aparência Pix permanece BLOQUEIO OPERACIONAL até supressão autorizada dos pendentes.
10. Se Antigravity já mudou `README`, `AGENTS` ou draft JSON durante este trabalho, **não sobrescrever**. Fazer `git status`, reconciliar diferenças e incluir uma nota de integração só após conflito resolvido.

## Condições de aceitação específicas
- As sete URLs editoriais novas estão listadas e cada uma tem status `verified` / `partial` / `needs browser`, sem invenção de testes realizados.
- Links de demo testados em celular com dados públicos/fictícios e sem vazamento de fotos/nomes reais.
- `desde 2011` confirmado; **não afirmar “10 mil ensaios”** nem “mesmo endereço desde 2011” até o CRM comprová-los (a direção citou >8.500 e >10.000 em momentos distintos).
- Status da galeria universal explicitamente registrado como **direção confirmou, catálogo incompleto a ajustar**.
- Boas práticas WhatsApp: consentimento, uma ideia por contato, 0–3 emojis, até 2–4 linhas de legenda (quando possível), 1 CTA contextual por toque, STOP e supressão em respostas/compras.
- Nenhuma mídia fictícia ou demonstração com foto de cliente privado publicada indevidamente.

## Prompt de continuação (colar no Antigravity)
> Antes de continuar a revisão V5, atualize `hypeneural/propostanatal` e leia os novos arquivos 04, 05, 06 e `editorial/experience-links.v5.addendum.json`. Confronte os links de jogos/galeria, horários, Instagram, site Natal, checkout e como chegar. No Chrome real valide funcionalidade, segurança dos demos e mobile. Reconcile as regras dos três pacotes, em especial a galeria universal confirmada pela direção e 1/5/10 jogos conforme catálogo. Revise a proposta por etapa, escrevendo legendas WhatsApp curtas e demonstrando mimos, botão mágico de bolhas e tradição desde 2011 somente com mídia real. Preserve SMS, Resend, SmartVoice, hash e snapshots; não altere o CRM nem dispare mensagens. Entregue diffs claros e resolva T0 antes de seguir D+1 → D+10.
