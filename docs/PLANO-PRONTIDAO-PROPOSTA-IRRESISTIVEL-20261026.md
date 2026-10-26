# Plano de prontidão da proposta Natal 2026
Data 26/10/2026. Estado: PROPOSTA DE MELHORIAS; sem alterações no CRM de produção.

## Veredito
- Pronto para redigir toda a V5 editorial e testar as prévias.
- Não está comprovado que salvar diretamente pelo gestor preserve a prontidão do perfil V4: InspectNatalProposalReadiness compara o hash aprovado com ProposalReadinessSnapshot::configurationHash, incluindo messages e actions.
- Não modificar 11 runs pendentes do PDF D+10 por SQL; a cópia fica em stage_snapshot. O primeiro agendamento relatado era 31/10/2026 19:29:04 (reconsultar antes de agir).
- SmartVoice ainda não homologado em produção, registry versionado com enabled=false e zero delivery items na auditoria.
- Z-API online confirmada pelo CRM Live em 26/10/2026 07:48 BRT.

## Bloqueios principais
1. PDF D+10 simula comprovante Pix para promover ensaio. Recomendação: impedir esse envio por operação autorizada, sem simplesmente renomear o arquivo. Verificar função de cancelar somente a etapa, preservar idempotência e outras etapas, e revalidar estado de pending em produção.
2. O manifesto editorial aprovado V4 possui configuration_hash de conteúdo. Editar messages.title/caption ou actions.payload pode causar natal_manifest_configuration_mismatch e inviabilizar novas propostas. Primeiro descobrir processo de republicação/reaprovação, sem prometer efeito imediato no catálogo nem nos snapshots.
3. O catálogo em evydencia.com/catalogo/natal, consultado em 26/10, informa 2 jogos base para Então é Natal e 4 para Boas Festas; a descrição V4 anterior citava 5 e 10. Tratar bônus da pré-venda e por data de sessão separadamente. Não prometer quantidade ou benefício sem verificar a regra comercial vigente.
4. E-mail no início da proposta V4 é condicionado a cadastro/elegibilidade; e-mail após fechamento/pagamento é outra automação baseada em pedido. Não confundir.
5. Z-API WhatsApp: para image/video o campo title vira caption; para attachment o title vira fileName; para action os campos message/title/description e buttons têm semânticas diferentes.

## Catálogo base para orientar comunicação (confirmar contratação e bônus)
- Ho-Ho-Ho: R$196; 15 minutos; 4 fotos 15x21; 1 calendário; entrada R$98 + R$98 no dia.
- Então é Natal: R$480; 30 minutos; 20 fotos 15x21; 1 calendário, certificado digital, galeria e 2 jogos base; entrada R$120 e até 3x R$120.
- Boas Festas: R$885; 60 minutos; 35 fotos 15x21; 5 calendários, 4 jogos base, certificado impresso, álbum e caixa; entrada R$180 e até 3x R$235.
- Dois cenários Natal + Ano Novo; Papai Noel/personagens não inclusos, salvo contratação expressa.
- Bônus pré-venda vencidos não devem ser apresentados como ofertas atuais. Crédito em fotos extras depende da data da sessão.

## Design editorial por etapa
- T0: saudação natural, preços e parcelas transparentes, convite aos dois cenários, pergunta curta sobre dia útil/fim de semana. Distinguir cliente conhecido vs sem nome.
- +1h: vídeo com tour real pela Vila de Natal; caption de emoção em vez de título técnico.
- +90min: facilitar escolha de data; verificar necessidade de segundo vídeo tão cedo.
- +2h SMS: considerar redução da pressão comercial; pergunta útil ao invés de urgência vazia.
- D+1: lembrança e vínculo familiar; countdown existente se renderizar no contexto.
- D+2: mostrar segundo cenário de Ano Novo.
- D+3: jogos/galeria; comunicar somente benefícios efetivos por pacote.
- D+4: roupinhas, conforto e trabalho fotográfico, sem superlativos sem prova.
- D+5: dúvidas frequentes e suporte humano, sem múltiplos CTAs.
- D+10: convite final sem documento enganoso; considerar desacelerar régua.
- D+15/D+20: respeitar calendário real de sessões, STOP, resposta e pedido finalizado.

## Canais e consentimento
- WhatsApp com texto, imagem, vídeo, documento e botões da Z-API: prévia do payload final e checagem de links/telefones.
- Resend: cliente com e-mail válido e permissão; T0 comercial elegível na V4; depois do fechamento, mensagem operacional exclusiva se acionada por evento de pedido.
- SmartVoice: 5 slots (1h, D1, D5, D7, D10), mas nenhum uso confirmado; habilitação condicionada a WAV real, consentimento para chamadas, registry efetivo, janela comercial, canário e recibo.
- Consentimento marketing/opt-out e handoff humano; não pressionar ou mascarar conteúdo de arquivo.

## Procedimento de publicação sem deploy recorrente
1. Congelar inventário e hash atual; não publicar relatórios privados no GitHub público.
2. Retirar com segurança o PDF D+10 perigoso dos runs pendentes, por mecanismo aprovado; reconsultar status antes.
3. Validar cenário de publicação no comando existente: php artisan proposals:provision-natal-2026 --manifest=<arquivo-local> --dry-run --json. Não executar --apply antes de revisão/aprovação.
4. Se o manifesto compatível passar dry-run, usar processo de --apply --expect=<hash-do-dry-run> com snapshot/rollback autorizado; a necessidade de deploy depende de compatibilidade do código instalado.
5. Se não for possível só com manifesto, construir único editor/publicador seguro no painel com reaprovação de configuração, validação de mídia, versionamento e snapshots em transação.
6. Preview sem envio de cada etapa/variante; testes de cliente com/sem nome, e-mail, opt-out, compra e frozen snapshots.
7. Obter aprovação editorial e operacional, canário consentido e acompanhar recibos.

## Critérios de NÃO PUBLICAR
- Configuration hash inválido, benefícios conflitantes, descadastro incorreto, CTA não verificado, snapshots pendentes perigosos, ausência de rollback, voz sem pré-requisitos.
