# seideagosto

Tema WordPress personalizado criado por Adaildo Neto (adaildo.neto@gmail.com).

## Visão Geral
- Blocos Gutenberg dinâmicos focados em notícias e destaques.
- Construtor de páginas de Arquivo/Busca com conteúdo em blocos (seleção via Customizer).
- Estilos baseados em variáveis de tema e painel de tipografia unificada.

## Blocos Gutenberg
- **Destaques Home (1 Grande + 2 Pequenos)**: hero com um post grande e dois menores; aceita filtro por categoria.
- **Grid de Notícias**: grade de cards com imagem e título; opções de categoria, quantidade, offset, colunas e tipografia. Suporta **paginação opcional**.
- **Destaque Categoria (1 Grande + 3 Lista)**: primeiro post com imagem + lista textual; com tipografia configurável.
- **Destaque Misto (2 Grandes + Lista + 1 Coluna)**: dois destaques principais e lista de itens; respeita a categoria selecionada.
- **Grid de Colunistas / Item de Colunista**: grupo de colunistas; cada item mostra nome, título e o último post da categoria definida.
- **Top Mais Lidas (Top N)**: lista ordenada por views (meta) com fallback por comentários.
- **Clima (Open‑Meteo)**: exibe clima atual por cidade ou coordenadas; cache curta para performance.
- **Monitor de Câmbio**: taxas BRL vs USD/BOB/PEN com spread; mostra compra e venda.
- **Blocos metadata (theme/blocks)**: destaque-grande, destaque-pequeno, lista-noticias.

## Customizer (inc/customizer.php)
- **Cores**: primária, badge, fundo de destaques, fundo colunistas, header, footer.
- **Tipografia**: tamanhos e peso dos títulos (`.TituloGrande`, `.TituloGrande2`, `.title-post`).
- **Layout**: altura imagem destacada, espaçamento entre cards.
- **Toolbar de leitura (single)**: botões A-/A/A+ seguem variáveis de cor do tema.
- **Modelos de Arquivo**: escolha páginas (Page) para renderizar o conteúdo de Categorias, Tags e Busca com blocos.
- **Live Preview**: atualiza estilos em tempo real (js/customizer-preview.js).

## Arquivos/Busca com blocos
- Crie uma Página com os blocos desejados (ex: Grid de Notícias com categoria “Todas”).
- No Customizer, selecione essa Página para “Categorias”, “Tags” e/ou “Busca”.
- As páginas de arquivo/busca vão renderizar o conteúdo dessa Página, e o Grid de Notícias seguirá o contexto automaticamente.
- Se o Grid tiver paginação habilitada, os links usam a paginação do arquivo/busca.

## Áreas de Widgets
1. **navbarlateral** (`navbarlateral`)
2. **Banner abaixo do post** (`banner_post`)
3. **bannervertical** (`banneraleac-vertical`)
4. **temperatura** (`temperatura`)
5. **Banner Colunistas** (`banner_colunistas`)
6. **banner ALEAC** (`bannneraleac`)
7. **Grupo Whatsapp** (`whatsappcorreio68`)
8. **Colunistas 68** (`colunistas`)
9. **Na Rota do Boi** (`narotadoboi`)
10. **Right Sidebar** (`right-sidebar`)
11. **banner Footer** (`bannerfooter`)
12. **Banners do Cabral** (`cabralize`)
13. **Left Sidebar** (`left-sidebar`)

## Tamanhos de Imagem
- `destaque`: 300x180 (crop)
- `destatquegrande`: 730x410 (crop)

## Notas de Desenvolvimento
- Text Domain usado no tema: `u_correio68`.
- Baseado em Bootstrap e arquivos do Starter Theme (ST2/UnderStrap).

## Autor
- Nome: Adaildo Neto
- Email: adaildo.neto@gmail.com
