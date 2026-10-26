# 04 — Catálogo de links, demonstrações e confiança | Natal 2026

**Atualização complementar de 26/10/2026 | Somente editorial | Sem alterações no CRM.**
**Regra de precedência:** este adendo complementa a V5 já escrita em `00`–`03`; NÃO substitui automaticamente os arquivos que o Antigravity está revisando. Integrar por diff e aprovação.

## 1. Fontes e níveis de evidência
- **DIREÇÃO (26/10):** o gestor informa que todas as famílias recebem galeria personalizada, jogos digitais conforme o pacote, medalhinha, certificado e livrinho de colorir para crianças; o estúdio possui entrada interativa, botão mágico com bolhas/luzes, localização fixa em Tijucas e trajetória desde 2011. Isso **orienta o conteúdo**, mas inclusão irrestrita em todos os pacotes e execução operacional devem ser auditadas antes de publicar promessas contratuais.
- **SITE NATAL 2026 (texto consultado nesta revisão):** https://fotosdenatal.com/ lista 1/5/10 jogos nos pacotes Ho-Ho-Ho/Então é Natal/Boas Festas; galeria explicitamente em Então é Natal, não listada igualmente no Ho-Ho-Ho nem no Boas Festas; dois cenários, desde 2011 e endereço no rodapé.
- **PÁGINA DE COMO CHEGAR (consultada):** https://ir.fotosdenatal.com/ apresenta Google Maps, Waze, Uber, Apple Maps e Rua Mauri Afonso da Silva, 892, Bairro Universitário, Tijucas/SC.
- **DEMONSTRAÇÕES JOGOS/GALERIA:** URLs fornecidas pela direção, mas *a inspeção automática do conteúdo retornou cache miss/falha*. Isso NÃO comprova site fora do ar; executar verificação local no navegador/Antigravity e validar SPA, mobile, ausência de fotos particulares e links de jogos.
- **INSTAGRAM:** URL informada e referenciada no site público; Instagram restringiu consulta automatizada, não foi possível validar as publicações/perfil ou disponibilidade por GET.
- **HORÁRIOS:** página de agendamento acessível no mecanismo de consulta, mas o calendário de horários é renderizado dinamicamente. HTTP/HTML não prova horários disponíveis nem capacidade de reserva.
- **HISTÓRICOS (Google Drive):** MASTER 100 motivos https://docs.google.com/document/d/1rLqtEGrnmqOEKBqB8S8PRKTxJCekP0nc0wbDP-e-HGE/edit ; Proposta 2025 https://docs.google.com/document/d/17JSbsX3mFIJr-7w-CH0a8v9ZZuQG27jo45K6nbulZ-s/edit ; Fluxo 2024 https://docs.google.com/document/d/1QbLCOElCRHkdsG-OBfjfGiAnj1L4sH3il10gz-7qxtY/edit ; 27 motivos https://docs.google.com/document/d/1sSuhRQsiX_cSmXBdFVgrLFG5obXDWxFYyQc2Rxr57bw/edit . Historicamente há certificados, medalhas, jogos, acervo, sons, luzes, bolhas e brindes; NÃO inferir tudo ativo em 2026.
- **META:** políticas e boas práticas https://whatsappbusiness.com/policy/ e https://about.fb.com/news/2024/09/new-ways-for-businesses-to-have-more-meaningful-conversations-on-whatsapp/ : consentimento, relevância, personalização, escolha de frequência e atendimento humano. Regras de templates/24h se aplicam especificamente quando a integração usar Business Platform, não pressupor que Z-API seja Cloud API.

## 2. Catálogo oficial de URLs para o conteúdo editorial
| Identificador | Link canônico HTTPS | Finalidade | Fonte / estado | Melhor uso |
|---|---|---|---|---|
| jogos_demo | https://jogos.fotosdenatal.com/ | Mostrar jogos feitos com fotos | Informado pela direção; **validar navegador real** | D+3, após vídeo de jogos |
| galeria_demo | https://jogos.fotosdenatal.com/demo/fotos | Exemplo de galeria personalizada | Informado pela direção; **validar navegador real e dados fictícios** | D+3/D+4, mensagem ou botão contextual |
| horarios | https://horarios.fotosdenatal.com/ | Disponibilidade/horários em tempo real no produto | Página pública reconhecida, agenda interna dinâmica **não verificada** | T0 como CTA primário ou secundário |
| instagram | https://instagram.com/estudioevydenciaa | Fotos recentes e bastidores | Fornecido pela direção, vinculado ao site Natal; consulta automatizada limitada | T0 botão secundário / D+5 prova visual |
| site_natal | https://fotosdenatal.com/ | Visão completa, pacotes, FAQs, fotos | Conteúdo público consultado | T0 no PDF, em perguntas de detalhes |
| como_chegar | https://ir.fotosdenatal.com/ | Google Maps/Waze/Uber/Apple Maps + endereço | Página pública consultada | D+5 confiança/FAQ, pré-sessão, cidades vizinhas |
| reserva_checkout | https://reserva.fotosdenatal.com/ | Escolha/pagamento/checkout | Link oficial de trabalho e V5 anterior; validar checkout real | T0 botão “Reservar agora” |
| reportagem | https://www.vipsocial.com.br/noticia/estudio-de-tijucas-transforma-natal-em-experiencia-para-toda-a-familia/59957 | Matéria da Vila 2026 | Especial publicitário identificado | D+5, rotular “reportagem especial publicitária” |

**Não tratar `jogos_demo` como a galeria PRIVADA de um cliente:** demonstração pública somente com imagens autorizadas ou fictícias. Nunca usar links/tokens de sessão real num disparo geral. **Normalizar `http://` fornecido pela direção para `https://` apenas nos domínios onde HTTPS foi reconhecido** (Instagram, Natal e Como Chegar), validando redirecionamento final no ambiente real.

## 3. Problemas comerciais de conteúdo que exigem sincronização
1. **Galeria em todos os pacotes:** diretriz da gestão 26/10; o site apresenta galeria expressamente apenas em um pacote. CRIAR TAREFA para alinhar catálogo, PDF, checkout e regras de produção. O site pode estar incompleto; não alterar o produto por inferência.
2. **De 1 a 10 jogos por pacote:** catálogo apresenta 1, 5, 10. O catálogo de demonstração pode mostrar 10 jogos, mas **“10 jogos em todos os pacotes” é incorreto**. Preferir “jogos digitais com as fotos da família” em copy geral ou “*até 10 jogos* conforme o pacote”.
3. **Certificado e medalhinha:** direção informou que todas as crianças ganham, mas catálogo cita certificado **digital no Então é Natal, impresso no Boas Festas e ausente na lista do Ho-Ho-Ho**. Separar: certificado do pacote, certificado/brinde universal, suporte digital/impresso, idades elegíveis. Homologar regra operacional antes de frase universal.
4. **Livrinho de colorir:** benefício novo confirmado verbalmente pela direção; não documentado como item no catálogo atual. Conferir estoque, faixa etária, quem recebe e custo e transformar em foto/vídeo autoral.
5. **Entrada interativa, luzes, bolhas e botão mágico:** histórico do Drive comprova concepção e edições passadas; direção confirma experiência atual. Conferir equipamento em operação, restrições de segurança para crianças e se faz parte de todas as sessões. Mostrar **vídeo real, 5–8 segundos**, em vez de longa enumeração.
6. **História e solidez:** “em Tijucas desde 2011” está no site. “No MESMO endereço desde 2011” é informação da direção sem documento público independente; auditar endereço/continuidade antes de afirmar. “Mais de 8.500” e “mais de 10 mil ensaios” estão em versões diferentes; CRM pode confirmar quantidade com filtro e período. Até lá usar “desde 2011” e “milhares de famílias”.
7. **Escassez sazonal:** último dia para realizar a sessão, prazo de revelação, calendário de dezembro e slots remanescentes são campos diferentes. Não dizer “faltam seis semanas para encerrar” sem data real de fechamento. Data de Natal em 2026: 25/12/2026. Criar atualização dinâmica baseada em dias de sessão realmente livres.

## 4. Testes de links a serem feitos NO AMBIENTE DO ANTIGRAVITY (não presumir sucesso)
- Abrir URLs em Chrome desktop e celular (390×844). Seguir redirecionamentos e confirmar HTTPS, DNS, TLS, carregamento JS e toque.
- `jogos_demo`: demo pública abre catálogo e inicia um jogo sem 404/login; telas reais usam fotos apenas fictícias/autorizadas; sem dados reais expostos.
- `galeria_demo`: `/demo/fotos` carrega as fotos, miniaturas, rolagem, lightbox e retorno; identificar se a rota contém PII e se os dados são inteiramente sintéticos.
- `horarios`: verificar disponibilidade real com calendário de datas, sem completar pagamento ou ocupar sessão; confirmar não haver vagas fictícias.
- Instagram: perfil certo, publicações recentes e `@estudioevydenciaa` sem redirect para outro perfil; link deve ser preferido a `@estudioevydencia` antigo.
- `ir`: Google Maps e Waze abrem com endereço **Rua Mauri Afonso da Silva, 892 - Universitário, Tijucas/SC**; testar navegação no mobile.
- Checkout: ver pacote real e valores, **não concluir pedido**. Preservar consentimento e redirecionamentos.
- Medir `HTTP` e URL final, mas **HTTP 200 não prova UX nem ausência de erros JS**. Veja `scripts/check_public_links.py` para uma sondagem leve de disponibilidade.

## 5. Orientação de botões e relevância
**T0 — 3 ações priorizadas:** `Reservar agora` (checkout) / `Ver horários` / `Ver Instagram` — manter V5 existente, sem inserir 8 botões. Um link para o PDF, já entregue em anexo, não precisa virar botão redundante.
**D+3 — demonstrações:** mostrar o vídeo de jogos, seguido de UM link para catálogo demo; galeria via segundo contato somente se a cadência permitir sem pressão comercial e sem somar mensagem extra automaticamente.
**D+5 — confiança:** desde 2011, imagens reais da estrutura; reportagem/publicação Instagram/como chegar conforme perfil da pessoa, em vez de enviar tudo de uma vez.
**Após a reserva:** `ir` é excelente na confirmação da sessão. Antes da compra, usar como resposta “Onde fica?”, para não competir com o CTA de compra.

## 6. Estado
Documentação/ideias prontas para review; nenhum CTA foi cadastrado no banco, nenhum snapshot tocado, nenhuma mensagem enviada.
