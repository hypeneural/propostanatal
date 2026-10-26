# 13 — Dossiê Mestre de Auditoria Forense e Editorial V5 (Instrução para ChatGPT)

**Data:** 26/10/2026  
**Status do Projeto:** `V5_EDITORIAL_READY_FOR_APPROVAL` (Auditoria e Revisão Editorial Concluídas; zero escritas em produção, zero disparos)  
**Repositório:** `https://github.com/hypeneural/propostanatal` (Kit de auditoria e proposta independente)  
**Repositório Canônico de Referência:** `hypeneural/evydencia-crm`  
**Destinatário da Auditoria:** ChatGPT / Engenharia e Diretoria Comercial

---

## 1. Missão do ChatGPT nesta Auditoria

Como auditor técnico e comercial independente, você (ChatGPT) deve:
1. Validar a qualidade persuasiva, naturalidade e conformidade de formatação da copy do WhatsApp V5.
2. Auditar os contratos técnicos da Z-API e do CRM Laravel (limite de 200 caracteres Unicode em captions `title`, campos de `action`, nomes de anexos).
3. Verificar a segurança do fluxo: preservação de SMS/Resend/SmartVoice, allowlist estrita de botões, e tratamento do P0 do documento D+10.
4. Emitir um veredito fundamentado (Aprovado / Ajustes Recomendados / Alertas de Risco) para que a equipe de gestão possa tomar a decisão comercial de homologação.

---

## 2. Índice Completo da Documentação e Relatórios

Todos os documentos estão disponíveis na árvore `docs/` deste repositório:

### 2.1. Diretrizes, Contratos e Análises Temáticas (`docs/EDITORIAL-V5-20261026/`)
- [00-FONTES-E-CLAIMS.md](00-FONTES-E-CLAIMS.md): Fontes históricas, claims proibidos (Papai Noel falso, 10x sem juros) e argumentos verdadeiros ("Desde 2011", 2 cenários).
- [01-CADENCIA-WHATSAPP.md](01-CADENCIA-WHATSAPP.md): Cadência da régua WhatsApp (T0, H+1, H+90, D+1 a D+10).
- [02-ROTEIROS-ELEVEN-V4.md](02-ROTEIROS-ELEVEN-V4.md): Roteiros para síntese de voz e separação entre tags Eleven e texto WhatsApp.
- [03-EXECUCAO-ANTIGRAVITY.md](03-EXECUCAO-ANTIGRAVITY.md): Orientações e regras de segurança para o agente Antigravity.
- [04-SITES-LINKS-E-EXPERIENCIA.md](04-SITES-LINKS-E-EXPERIENCIA.md): Catálogo de URLs públicas da experiência (jogos, horários, catálogo, rotas).
- [05-COPY-CURTA-JOGOS-GALERIA-CONFIANCA.md](05-COPY-CURTA-JOGOS-GALERIA-CONFIANCA.md): Princípios de concisão para mobile.
- [06-HANDOFF-ANTIGRAVITY-NOVOS-LINKS.md](06-HANDOFF-ANTIGRAVITY-NOVOS-LINKS.md): Handoff técnico com status de validação de links.
- [07-WHATSAPP-ZAPI-FORMATACAO-CONTRATO.md](07-WHATSAPP-ZAPI-FORMATACAO-CONTRATO.md): **Contrato oficial Z-API**: sem markdown rich text, negrito `*texto*`, itálico `_texto_`, mapeamento exato de campos.
- [08-BASE-HISTORICA-GOOGLE-DOCS.md](08-BASE-HISTORICA-GOOGLE-DOCS.md): Extração das propostas de 2024, 2025 e do documento Master de 100 motivos.
- [09-VEREDITO-FORENSE-ANTIGRAVITY.md](09-VEREDITO-FORENSE-ANTIGRAVITY.md): Confronto crítico entre relatórios anteriores e código real do CRM.
- [10-PUBLICACAO-PELO-GESTOR-SEM-VPS.md](10-PUBLICACAO-PELO-GESTOR-SEM-VPS.md): Parecer de publicação pelo painel: por que salvar no editor normal não atualiza o hash do manifesto V4.
- [11-AUDITORIA-DE-ANEXOS-E-AUDIOS.md](11-AUDITORIA-DE-ANEXOS-E-AUDIOS.md): Inventário criptográfico de 11 mídias e roteiro de gravação do fotógrafo (17s).
- [12-SMS-RESEND-VOZ-TELEFONE.md](12-SMS-RESEND-VOZ-TELEFONE.md): Auditoria técnica dos 9 SMS, e-mail transacional Resend e SmartVoice.

### 2.2. Relatórios Analíticos e Forenses (`docs/EDITORIAL-V5-20261026/relatorios/`)
- [V5-COPY-REVIEW-ATUALIZADO.md](relatorios/V5-COPY-REVIEW-ATUALIZADO.md): **A proposta V5 consolidada completa**, com todas as mensagens, métricas de caracteres e formatação nativa.
- [V5-DIFF-AUDITADO.md](relatorios/V5-DIFF-AUDITADO.md): Diff detalhado campo a campo entre a produção atual (`messages.id` 1445 a 1476) e o rascunho V5.
- [CONTRATO-BOTOES-URL-GATE.md](relatorios/CONTRATO-BOTOES-URL-GATE.md): Análise de validação da allowlist de botões T0 e bloqueio do checkout direto.
- [COUNTDOWN-RENDER-GATE.md](relatorios/COUNTDOWN-RENDER-GATE.md): Análise técnica do token dinâmico `{natal_countdown_dias_descricao}` em D+1.
- [MIDIA-EVIDENCIAS.md](relatorios/MIDIA-EVIDENCIAS.md): Auditoria criptográfica (SHA-256) e audiovisual dos 8 vídeos locais.
- [PROXIMOS-PASSOS-GO-NOGO.md](relatorios/PROXIMOS-PASSOS-GO-NOGO.md): Matriz de prontidão e pré-requisitos para publicação controlada.
- [AUDITORIA-FINAL.md](relatorios/AUDITORIA-FINAL.md): Inventário real extraído do MySQL de produção (`evyd25`).
- [timeline.md](relatorios/timeline.md): Linha do tempo multicanal minuto a minuto da campanha.

---

## 3. Síntese Executiva do Que Foi Feito

### 3.1. Auditoria Forense Real no Banco de Dados de Produção
* Extração somente leitura com `scripts/audit.php` conectando diretamente ao MySQL de produção.
* Inventário de **89 etapas**, **54 mensagens** e **34 ações** entre as Campanhas 415 (com nome) e 416 (sem nome).
* Identificação e isolamento das **38 etapas ativas do catálogo V4** (19 por campanha).
* Reconciliação dos IDs de mensagens reais (1445 a 1476), esclarecendo divergências anteriores originadas por IDs de pivô de relacionamento.

### 3.2. Descoberta Crítica P0: PDF D+10 e Snapshots Congelados
* Na etapa D+10 (etapas 666 e 685), o PDF `natal2026-rev357-v1-d10-document.pdf` está registrado com o título `Comprovante_de_Transferencia Pix_01112026_200921`.
* O arquivo binário é um flyer promocional legítimo, mas com arte que simula comprovante Pix. Na Z-API, o título do anexo vira o nome do arquivo (`.pdf`), com alto risco de denúncia por spam/phishing.
* **Fato Crítico Comprovado:** Existem **12 clientes com execuções pendentes congeladas** (`campaign_contact_stage_runs.stage_snapshot`) que contêm esse título gravado no snapshot. O método `CampaignPolicyLegacyMessages::hydrate` reconstrói a mensagem a partir do snapshot congelado.
* **Ação Tomada:** O papel foi marcado como `blocked_do_not_publish` no rascunho V5 e foram documentados os requisitos para supressão administrativa segura antes do primeiro disparo (31/10).

### 3.3. Contrato Z-API e Conformidade Técnica
* Limite de caracteres: O CRM Laravel (`Natal2026ProposalManifest.php`) impõe teto de **200 caracteres Unicode** (`mb_strlen`) para `messages.title` de mídias (que na Z-API vira a legenda/caption do vídeo/imagem).
* Todas as legendas propostas na V5 têm entre 94 e 150 caracteres, respeitando com folga a zona de conforto visual no WhatsApp (alvo de 160 caracteres).
* Formatação nativa: Remoção completa de crases de código (` ``` `) e aspas triplas; adoção estrita de `*negrito*` e `_itálico_` nativos do WhatsApp.
* Respeito à semântica dos campos da Z-API:
  * Mensagem tipo `action`: `message.message` = corpo, `title` = cabeçalho, `description` = rodapé.
  * Mensagem tipo `image` / `video`: `messages.title` = caption da mídia. `description` é ignorado pelo provedor.
  * Mensagem tipo `attachment`: `messages.title` = `fileName` do documento (ex: `Proposta-Natal-AnoNovo-Evydencia-2026.pdf`).

### 3.4. Contrato de Botões e URLs de T0
* O CRM exige em V4 **IDs específicos** e **URLs em ordem fixa**:
  1. `natal2026-reservar` → `https://evydencia.com/catalogo/natal`
  2. `natal2026-horarios` → `https://horarios.fotosdenatal.com/`
  3. `natal2026-instagram` → `https://instagram.com/estudioevydenciaa`
* O link de checkout direto `https://reserva.fotosdenatal.com/` funciona na web, mas **é rejeitado pelo validador do CRM**. Ele está mantido sob gate de código e NÃO foi incluído no rascunho executável V5.

### 3.5. Preservação Estrita dos Demais Canais
* **9 SMS:** Mantidos em `preserve_existing_v4`. Ideias de otimização documentadas em [12-SMS-RESEND-VOZ-TELEFONE.md](12-SMS-RESEND-VOZ-TELEFONE.md), sem alteração de produção.
* **E-mail Resend T0:** Mantido em `preserve_existing_t0` em modo texto. Não há anexo PDF via e-mail no V4.
* **SmartVoice:** 5 fluxos telefônicos mantidos em `no_activation`. Configuração na plataforma possui `enabled: false`.

---

## 4. Tabela Resumo da Copy WhatsApp V5

Abaixo está o resumo consolidado para avaliação rápida pelo ChatGPT e pela diretoria:

| Etapa | Papel / Tipo | Limite / Métrica | Copy Consolidada V5 |
|---|---|---|---|
| **T0** | `initial`<br>`action.body`<br>(415 com nome) | Texto livre | `Oii, *Maria*! 🎄🥰`<br><br>`Na *Evydência*, em Tijucas, preparamos *2 cenários* na mesma sessão: *Natal + Ano Novo*! ✨`<br><br>`Estamos aqui *desde 2011*, fotografando histórias de família. ❤️`<br><br>`Já te envio a imagem dos pacotes e o PDF, a partir de *2x R$ 98*.`<br><br>`*Prefere semana ou fim de semana?*`<br><br>`_Para parar lembretes, responda PARARNATAL2026._` |
| **T0** | `initial`<br>`action.body`<br>(416 sem nome) | Texto livre | `Oii! 🎄🥰`<br><br>`Na *Evydência*, em Tijucas, preparamos *2 cenários* na mesma sessão: *Natal + Ano Novo*! ✨`<br><br>`Estamos aqui *desde 2011*, fotografando histórias de família. ❤️`<br><br>`Já te envio a imagem dos pacotes e o PDF, a partir de *2x R$ 98*.`<br><br>`*Prefere semana ou fim de semana?*`<br><br>`_Para parar lembretes, responda PARARNATAL2026._` |
| **T0** | `cenarios_image`<br>`image.title` | 150 / 200 chars | `🎁 *Três jeitos de viver esse Natal!*`<br>`Todos incluem os *dois cenários* e fotos reveladas.`<br>`Olha os pacotes na imagem e me diz qual combina com vocês. ❤️` |
| **T0** | `proposta_pdf`<br>`attachment.title` | fileName | `Proposta-Natal-AnoNovo-Evydencia-2026.pdf` |
| **H+1** | `whatsapp_h1`<br>`video.title` | 134 / 200 chars | `✨ *Dois cenários na mesma sessão!*`<br>`O brilho do Natal e a energia do Ano Novo num ensaio só.`<br>`Dá o play e sente o clima da nossa Vila! 🎄🥂` |
| **H+90** | `whatsapp_90min`<br>`video.title` | 129 / 200 chars | `🎄 *Ainda dá tempo de viver essa magia!*`<br>`Olha como as crianças se encantam na nossa Vila.`<br>`Quer ajuda pra escolher o melhor horário? ❤️` |
| **D+1** | `whatsapp_d1`<br>`video.title` | 94 / 200 chars | `🎄 *A magia tá nos detalhes!*`<br>`Vem espiar como preparamos a Vila pra receber a sua família. ✨` |
| **D+2** | `whatsapp_d2`<br>`video.title` | 127 / 200 chars | `✨ *Natal + Ano Novo juntos!*`<br>`Dois momentos inesquecíveis, duas produções completas na mesma sessão.`<br>`Qual você curte mais? 🎄🥂` |
| **D+3** | `whatsapp_d3`<br>`video.title` | 95 / 200 chars | `🎮 *Jogos e galeria mágica!*`<br>`Olha como as crianças amam ver as próprias fotos na Vila de Natal. 🎁` |
| **D+4** | `whatsapp_d4`<br>`video.title` | 129 / 200 chars | `👗 *Roupas e acessórios inclusos!*`<br>`A gente cuida de cada detalhe com carinho pra você não se preocupar com nada.`<br>`Olha que amor! ❤️` |
| **D+5** | `whatsapp_d5`<br>`video.title` | 127 / 200 chars | `📜 *O pergaminho dos duendes!*`<br>`As crianças piram quando encontram a mensagem mágica na Vila.`<br>`Quer sentir essa emoção de perto? ✨` |
| **D+10** | `whatsapp_d10`<br>`video.title` | 114 / 200 chars | `🎄 *O Natal passa rápido...*`<br>`Mas as fotos dessa fase ficam pra sempre.`<br>`Ainda dá tempo de garantir o seu horário! ❤️` |
| **D+10** | `document_d10`<br>`attachment.title` | **P0 BLOQUEADO** | `blocked_do_not_publish` (flyer com aparência de comprovante Pix; 12 execuções pendentes congeladas no DB). |

---

## 5. Roteiro e Perguntas para Auditoria do ChatGPT

Solicitamos que o ChatGPT audite os seguintes aspectos e emita seu parecer:

1. **Copywriting e Persuasão (WhatsApp V5):**
   * Avalie o tom de voz das mensagens T0, H+1, H+90, D+1 a D+10 contidas em [V5-COPY-REVIEW-ATUALIZADO.md](relatorios/V5-COPY-REVIEW-ATUALIZADO.md).
   * O argumento "Desde 2011 em Tijucas" e "2 cenários na mesma sessão (Natal + Ano Novo)" está bem posicionado?
   * As perguntas de engajamento ("Prefere semana ou fim de semana?", "Qual você curte mais?") são eficazes para gerar resposta sem parecer pressão agressiva?

2. **Conformidade Técnica Z-API:**
   * Verifique se as marcações de `*negrito*` e `_itálico_` estão balanceadas e se não há markdown inadequado.
   * Confirme se as legendas de vídeo/imagem atendem aos limites do CRM (<200 caracteres Unicode).

3. **Segurança Operacional e Gates:**
   * Avalie o parecer sobre o bloqueio de `document_d10` e a necessidade de cancelamento administrativo dos 12 snapshots pendentes.
   * Avalie a decisão de manter os botões dentro da allowlist do CRM V4 em vez de forçar o checkout direto.
   * Avalie a recomendação de substituir o token dinâmico `{natal_countdown_dias_descricao}` em D+1 por texto estático seguro, dado que `fallback_title` não é selecionado pelo worker.

4. **Veredito Geral:**
   * Emitir recomendação executiva para a diretoria: **GO para Homologação Comercial**, com lista de eventuais ajustes finos sugeridos.

---

**FIM DO DOSSIÊ — REPOSITÓRIO PRONTO PARA AUDITORIA INDEPENDENTE.**
