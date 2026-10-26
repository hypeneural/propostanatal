# 23 — Antigravity: única missão local V5.2, variáveis e gravações

**26/10/2026 · GO EDITORIAL OFFLINE / NO-GO CRM PRODUÇÃO.**
A equipe pediu humanização da abertura, `{saudacao}` por horário, possibilidades de countdown natalino e roteiros graváveis do fotógrafo/Papai Noel.

## O que o ChatGPT já alterou no Git
- `editorial/whatsapp-v5.draft.json`: introduziu `{saudacao}` somente no T0 para as campanhas 415/416; o resto das mensagens não ganhou cumprimento repetitivo.
- `video_d1.dynamic_candidate_title` e `dynamic_candidate_weeks_title`: opções de Natal **somente metadata**. `video_d1.title` continua **estático**, sem riscos de token literal.
- `scripts/validate_editorial.py`: exigência de saudação, candidato de countdown e portão de renderização, mantendo D+10 bloqueado/STOP H+1.
- `docs/EDITORIAL-V5-20261026/14-COPY-CANONICA-V5-CANDIDATA.md`: re-gerado da fonte única.
- `docs/EDITORIAL-V5-20261026/21-VARIAVEIS-SAUDACAO-COUNTDOWN-AUDITORIA.md`: matriz de tokens conferida nas classes da main Laravel.
- `docs/EDITORIAL-V5-20261026/22-ROTEIROS-DE-AUDIO-FOTOGRAFO-PAPAI-NOEL.md`: textos prontos, checklist de gravação e API.
- `scripts/test_natal_runtime_variables.php`: prova do parser Laravel **local**, sem escrita, para 08:00, 15:00, 21:00 e virada de Natal.

## Execute UMA rodada objetiva — PowerShell
```powershell
git status --short
git fetch origin main
# Se tiver alterações, conciliá-las SEM reset --hard
git pull --ff-only origin main

powershell -NoProfile -ExecutionPolicy Bypass -File scripts/run-editorial-checks.ps1
python scripts/render_canonical_v5.py --check
python scripts/plan_manager_v5.py --check
python scripts/check_forensic_gates.py

# Com CRM clonado e dependências locais instaladas:
$env:NATAL_CRM_ROOT = "C:\Projetos\hypeneural\evydencia-crm"
php scripts/test_natal_runtime_variables.php
```
O caminho do CRM acima é um **exemplo**: descobrir o diretório real no notebook. Verificar `config('app.timezone')` sem alterá-lo. NÃO forçar conexão DB para o script rodar.

## Revisão editorial
1. T0: verificar `{saudacao}, *{primeiro_nome}*!` em 415 e `{saudacao}!` em 416, com pontuação/nome corretos. Humanização: bom dia/tarde/noite só na abertura e CTA único “semana ou fim de semana”.
2. H+1 e H+90: não duplicar saudação nem áudio; vídeo e pergunta já existentes, callback humano para quem responder.
3. D+1: ensaiar candidatos do countdown dias/semanas no script local, validar texto exatamente como o serviço resolve e comprimento final do `title` <= 200; NÃO mudar `video_d1.title` em produção nesta missão.
4. D+3 e D+4: manter demonstração jogos, dez motivos e footer STOP conforme V5.1; não acrescentar outro contador em cada peça.
5. Escassez verdadeira: data de Natal **não** equivale a fechamento dos ensaios; usar horários reais, sem afirmação de agenda quase encerrada sem fonte.
6. Usar campanha 416 para contatos sem nome; se contato “nome” for um telefone, espaço ou placeholder inválido, sugerir fallback neutro sem exibir “Olá, 4899…”.

## Áudios — entregar apenas os masters para aprovação
- Gravar 3 tomadas do roteiro A do fotógrafo (~18–22s), num local silencioso; ouvir no celular e escolher 1.
- Produzir 1 variante do Papai Noel no ElevenLabs v4, com tags `[laughs]`, `[excited]`, `[whispers]`; testar a pronúncia e tamanho. O texto fica no doc 22; não usar SSML.
- Gerar SHA-256, duração, codec, tamanho, nome e localização **privada** dos WAV/MP3/OGG; não subir áudio de cliente, áudio de teste ou dados pessoais no Git público.
- Registrar proposta de substituição futura de uma mídia redundante, não novo envio H+1 simultâneo a WhatsApp, SMS e SmartVoice.
- Z-API `send-audio` aceita URL/Base64; confirmar link público de mídia apenas em teste consentido APÓS P0, nunca disparar usando script local.

## Exigências de segurança
- D+10 PDF enganoso: verificar proteção tanto dos agendamentos congelados quanto dos NOVOS runs da V4 ativa antes de qualquer canário/disparo. **Nenhuma limpeza de runs foi feita neste projeto.**
- Envio comercial precisa opt-in/contato esperado, resposta humana acessível, supressão STOP/compra, e telefonia com consentimento próprio.
- Manter nove SMS, Resend inicial e cinco SmartVoice intactos.
- Editar o JSON ou criar áudio no disco NÃO publica mensagem no CRM. Não fazer alteração avulsa no painel 415/416 enquanto `configuration_hash` aprovado não puder ser versionado de forma atômica.

## Entrega do Antigravity
`reports/<timestamp>/V52-RUNTIME-VARIABLES.md` com 5 casos reais do parser, dia/tarde/noite, datas/contagens, caso sem nome e erro zero/falha; `V52-AUDIO-RECORDING-QA.md` com durations/checks e decisão de substituição; e `V52-GO-NOGO.md` com status por canal/P0/publicador.
Sem deploy, --apply, Doctor, banco DML, envios Z-API, SMS ou ligações reais. Subir no Git apenas scripts e documentação sanitizados. Concluir como **GO offline / production NO-GO** até critérios efetivos do CRM.
