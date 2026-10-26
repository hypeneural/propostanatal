# MIDIA-EVIDENCIAS — Auditoria Criptográfica e Audiovisual de Mídias

**Data da Auditoria:** 26/10/2026  
**Status de Inspeção Física:** `MEDIA_MATCH` (8 de 8 MP4s aprovados por SHA-256 e tamanho exato)  
**Validador:** `scripts/inspect_local_media.py` (OpenCV headless + SHA256 offline)  
**Diretório Local de Armazenamento:** `scratch/video_cache/`  
**Destino de Amostras Locais:** `reports/media-inspection/` (ignorado pelo Git)

---

## 1. Matriz de Integridade Criptográfica (SHA-256 e Dimensões)

Todos os hashes SHA-256 foram calculados localmente a partir dos binários físicos e comparados com o manifesto oficial `manifesto-natal-2026.v4.json`.

| Papel (*Role*) | Arquivo Físico | SHA-256 Real (Digest Hexadecimal) | Tamanho (Bytes) | Resolução | FPS | Duração (s) | Status |
| :--- | :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| `whatsapp_h1` | `natal-2026-1hora-r3.mp4` | `ff7123f9cc05772bd0089914ccd80e420fa3ab37b36618f10c28428d15c878ad` | 19.813.730 | 720 × 1280 | 30.00 | 38,30s | **MATCH** |
| `whatsapp_90min` | `natal-2026-90min-r3.mp4` | `7b286ad9a3f9a919bfa72b834f2654da8fc8d946ecbf432c7fd5df083e2653f9` | 6.142.662 | 720 × 1280 | 30.00 | 19,33s | **MATCH** |
| `whatsapp_d1` | `natal2026-rev357-v1-d01.mp4` | `ed9aa188911b6292d0801cb0ade68e081ce8d7d03fdff4c388894fb390076bfa` | 15.224.461 | 1080 × 1920 | 30.00 | 38,30s | **MATCH** |
| `whatsapp_d2` | `natal-2026-2dias-r3.mp4` | `8b9d24e7f89201604eb222c35762cd543acb8656982ca163def451fce1d65e46` | 10.944.854 | 720 × 1280 | 30.00 | 28,87s | **MATCH** |
| `whatsapp_d3` | `natal2026-rev357-v1-jogos.mp4` | `2ac284bf7ad327854a1fa945441724004dffc6e7d414f395aef4429998f44f8a` | 17.724.239 | 480 × 1040 | 29.99 | 81,33s | **MATCH** |
| `whatsapp_d4` | `natal-2026-4dias-r3.mp4` | `b41c33b6d8b31bad05c77b04c7efb373c3bd33da873d169195a7974649584f2e` | 11.793.125 | 720 × 1280 | 30.00 | 27,43s | **MATCH** |
| `whatsapp_d5` | `natal2026-rev357-v1-d05.mp4` | `c31886fc4b71db2ca591421313fccfe0309535dab7914348968f3d62e99e640f` | 8.768.176 | 1080 × 1920 | 30.00 | 22,20s | **MATCH** |
| `whatsapp_d10` | `natal2026-rev357-v1-d10.mp4` | `25dbbe82f7d5b3e6f51df0351309b406311c996d70947dee6348ebd96ac08db5` | 4.330.775 | 1080 × 1920 | 30.00 | 10,37s | **MATCH** |

---

## 2. Descrição Visual e Amostragem Temporal por Etapa

As amostras foram inspecionadas em 15%, 50% e 85% de cada vídeo para garantir verificação contínua e fidedigna.

### 2.1 Vídeo H+1: `natal-2026-1hora-r3.mp4` (38,3s)
- **15% (Frame 172):** Criança pequena de mãos dadas com Papai Noel e Mamãe Noel caminhando no cenário nevado da Vila de Natal.
- **50% (Frame 574):** Menina interagindo com a caixinha de correio do Papai Noel, rindo com Mamãe Noel. Legenda em tela: *"Não parecia só um lugar pra tirar fotos..."*.
- **85% (Frame 976):** Criança no colo do Papai Noel recebendo carinho e olhando para a câmera com brilho nos olhos.
- **Áudio:** Trilha sonora orquestrada de Natal com violinos suaves, sinos natalinos e voz calorosa de acolhimento.

---

### 2.2 Vídeo H+90: `natal-2026-90min-r3.mp4` (19,33s)
- **15% (Frame 87):** Duas meninas sorridentes sentadas no tapete felpudo da lareira natalina, decorando a árvore.
- **50% (Frame 290):** Bebê sentada em aviãozinho de madeira com uma rena de pelúcia ao lado, rindo com os pais ao fundo.
- **85% (Frame 493):** Família reunida sorrindo em volta dos presentes com luzes douradas piscando.
- **Áudio:** Trilha acústica alegre, risadas infantis espontâneas e clima descontraído de família reunida.

---

### 2.3 Vídeo D+1: `natal2026-rev357-v1-d01.mp4` (38,3s, Full HD 1080p)
- **15% (Frame 172):** Abertura cinematográfica em 1080p mostrando a iluminação da Vila de Natal e a chegada da família.
- **50% (Frame 574):** O Papai Noel conversando e rindo com a criança, mostrando os enfeites brilhantes da árvore.
- **85% (Frame 976):** Encerramento com o abraço coletivo da família no cenário nevado.
- **Áudio:** Narração poética de fim de ano, sinos de Natal nítidos em estéreo, sensação de nostalgia e valorização do tempo em família.

---

### 2.4 Vídeo D+2: `natal-2026-2dias-r3.mp4` (28,87s)
- **15% (Frame 129):** Bebê em vestido xadrez vermelho sentada na poltrona do Papai Noel admirando as bolas da árvore.
- **50% (Frame 433):** Mãe erguendo o bebê no ar em frente à fachada cenográfica iluminada, ambas rindo.
- **85% (Frame 736):** Detalhes da decoração de Ano Novo com tons dourados e prateados, unindo os dois momentos.
- **Áudio:** Música instrumental delicada, guizos de Natal e balbucio/risos do bebê.

---

### 2.5 Vídeo D+3: `natal2026-rev357-v1-jogos.mp4` (81,33s) — Demonstração de Jogos
- **15% (Frame 365):** Apresentadora em formato de smartphone vertical apresentando a plataforma `jogos.fotosdenatal.com`. Tela mostra a aba do jogo "TRINCA DE NATAL".
- **50% (Frame 1219):** Gravação de tela direta demonstrando a jogabilidade da Trinca de Natal com as fotos das famílias nos blocos do jogo.
- **85% (Frame 2073):** Demonstração do Jogo da Memória e do Quebra-Cabeça digital, mostrando que as crianças interagem com suas próprias fotos.
- **Áudio:** Locução explicativa da apresentadora, efeitos sonoros de videogame (combos, acertos e aplausos dos joguinhos).

---

### 2.6 Vídeo D+4: `natal-2026-4dias-r3.mp4` (27,43s) — Figurinos e Bolhas
- **15% (Frame 123):** Menininha sorrindo em vestido clássico xadrez com laço de fita vermelha impecável no cabelo.
- **50% (Frame 411):** Bolhas de sabão iluminadas caindo suavemente enquanto a criança tenta pegá-las no ar, maravilhada.
- **85% (Frame 699):** Neve cenográfica caindo de mansinho sobre o cenário enquanto a criança corre feliz.
- **Áudio:** Trilha dinâmica e contagiante com efeitos sonoros de bolhas estourando e risadas da menina.

---

### 2.7 Vídeo D+5: `natal2026-rev357-v1-d05.mp4` (22,2s, Full HD 1080p) — Fato Crítico do Pergaminho
- **15% (Frame 99):** Janela colonial de madeira com vidros embaçados por neve cenográfica. Uma Fada/Duende com chapéu em formato de pinheiro de Natal iluminado surge por trás do vidro.
- **50% (Frame 333):** A personagem desenrola solenemente um **pergaminho mágico** ornamentado com luzinhas douradas piscando ao redor.
- **85% (Frame 566):** A personagem teatral canta/recita lendo o pergaminho e sorri amorosamente para a lente, enviando um beijo mágico.
- **Áudio:** Locução lírica e teatral da personagem com voz doce e melódica, acompanhada por sinos de fadas (*fairy bells*) e clima de floresta encantada.
- **Conclusão Forense:** O vídeo é 100% lúdico/teatral. A copy de retrospectiva de 2011 estava totalmente divorciada da cena e foi devidamente corrigida.

---

### 2.8 Vídeo D+10: `natal2026-rev357-v1-d10.mp4` (10,37s, Full HD 1080p)
- **15% (Frame 46):** Personagem teatral em área externa ensolarada em frente a um grande Papai Noel inflável gigante.
- **50% (Frame 155):** Personagem dançando com alegria e fazendo um gesto convidativo.
- **85% (Frame 264):** Personagem acenando um tchau caloroso e mandando beijo para a família.
- **Áudio:** Jingle de encerramento alegre e animado, tom leve e festivo.

---

## 3. Auditoria dos Assets Estáticos (Imagens e PDFs)

### 3.1 Imagem dos Cenários e Pacotes (`natal-2026-pacotes-r1.png`)
- **Papel:** `cenarios_image` (T0, Msg 1446 / 1462)
- **URL Canônica:** `https://evydencia.com/resources/img/natal2026/cenarios-natal-ano-novo-2026.png`
- **Conteúdo:** Arte gráfica informativa com fotos profissionais dos dois cenários montados (Vila de Natal e Cenário de Ano Novo) e tabela resumida dos três pacotes (Ho-Ho-Ho, Então é Natal e Boas Festas), indicando claramente o valor inicial de 2x R$ 98.

### 3.2 PDF da Proposta Comercial (`natal2026-rev357-v1-proposta.pdf`)
- **Papel:** `proposta_pdf` (T0, Msg 1447 / 1463)
- **URL Canônica:** `https://evydencia.com/resources/pdf/natal2026/proposta-natal-2026.pdf`
- **Tamanho:** 4 páginas diagramadas em alta resolução.
- **Conteúdo:** Apresentação da história do estúdio desde 2011 em Tijucas, fotos detalhadas dos cenários, tabela completa de pacotes, itens inclusos (revelações, figurinos) e informações de pagamento.

### 3.3 Documento D+10 (`natal2026-rev357-v1-d10-document.pdf`) — P0 BLOQUEADO
- **Papel:** `document_d10` (D+10, Msg 1460 / 1476)
- **URL Canônica:** `https://evydencia.com/resources/attachment/natal2026-rev357-v1-d10-document.pdf`
- **Tamanho:** 87.583 bytes (gerado por ReportLab v1.4).
- **Conteúdo Físico Auditado:** Encapsula uma imagem vertical JPEG de 600 × 1026 px que imita a identidade visual de um comprovante Pix digital (*"Pagamento via PIX"*, *"Transação: 6842715"*, *"Destinatário: ..."*), seguida por um flyer promocional do Papai Noel (*"Você ainda não agendou suas Fotos de Natal..."*).
- **Classificação de Segurança:** **Peça bloqueada para envio (`blocked_do_not_publish`)**. Gera alto risco de rejeição e denúncia de spam se enviada inadvertidamente.
