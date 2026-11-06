# AdSense no tema SeisdeAgosto: especificação

Data: 2026-11-06 · Status: aguardando revisão

## 1. Objetivo

Basta colar a tag do Google AdSense nas configurações do tema para o site passar a
exibir anúncios, com o tema cooperando com o robô do AdSense (verificação, `ads.txt`,
rastreamento) e oferecendo posições fixas opcionais. **Requisito central: os anúncios
nunca deformam o conteúdo do site** (largura das colunas, cards, grids e texto).

### Decisões do usuário

- Anúncios automáticos **e** posições fixas opcionais (cada posição só aparece com um
  ID de bloco de anúncio; sem ID, fica a cargo dos Anúncios automáticos).
- Sem anúncios para **usuários logados** e nas **Publicações Legais** (`edital`).
- Anúncios não podem deformar o layout.

### Premissas (corrigíveis)

- Site brasileiro: o tema **não** cria banner de consentimento. Para visitantes da
  Europa/Reino Unido, usar a mensagem pronta do AdSense em "Privacidade e mensagens".
- Anúncios automáticos são ligados/desligados **no painel do AdSense** (por site).
  O script é o mesmo nos dois casos, então o tema não tem um botão para isso.
- Âncora (anúncio fixo) e vinheta são configuradas no painel do AdSense. Como o
  cabeçalho do tema é fixo no topo, a documentação recomenda **âncora embaixo**.
- WordPress instalado na raiz do domínio (necessário para o `/ads.txt`).

### Fora do escopo

- AMP (`amp-auto-ads`), relatórios/ganhos, conexão com a API do AdSense, banner de
  consentimento próprio, opção "desativar anúncios" por post.
- A área de widget "Banner abaixo do post", que não aparece em nenhum template de
  bloco, continua como está.

## 2. Arquitetura

O site renderiza pelos templates de bloco (`templates/*.html`). As posições fixas são
inseridas por filtros de renderização (`render_block_*`, `the_content`), então
continuam funcionando se os templates forem editados no Editor do Site.

| Arquivo | Responsabilidade |
|---|---|
| `inc/adsense/settings.php` | Seção do Personalizador, sanitização, leitura das opções |
| `inc/adsense/output.php` | Regras de exibição, script no `<head>`, meta de verificação, `ads.txt`, `robots.txt`, aviso no admin |
| `inc/adsense/unit.php` | Função única que gera o HTML de um anúncio |
| `inc/adsense/placements.php` | As 6 posições fixas |
| `blocks/anuncio/` | Bloco "Anúncio" (`block.json`, `edit.js`, `render.php`) |
| `css/adsense.css` | Contêineres, altura reservada, rótulo, acomodação dos automáticos |
| `functions.php` | `require` de `inc/adsense/*.php` |

Prefixo de funções: `seisdeagosto_adsense_`. Opções em `theme_mod` com prefixo
`seisdeagosto_adsense_`.

## 3. Configuração (Personalizador → "📢 AdSense")

| Campo | Chave | Sanitização |
|---|---|---|
| Tag ou ID do AdSense | `seisdeagosto_adsense_client` | Aceita a tag inteira ou só o ID; extrai `ca-pub-\d{10,20}`. Se não encontrar, salva vazio e mostra o erro na descrição do campo. |
| Ativar anúncios | `seisdeagosto_adsense_enabled` | booleano; padrão **ligado** (desliga tudo sem perder o ID) |
| ID: Topo | `seisdeagosto_adsense_slot_topo` | `^\d{6,20}$` ou vazio |
| ID: Dentro da matéria | `seisdeagosto_adsense_slot_materia` | idem |
| Parágrafo do anúncio na matéria | `seisdeagosto_adsense_materia_paragrafo` | inteiro de 2 a 10; padrão 3 |
| ID: Fim da matéria | `seisdeagosto_adsense_slot_fim` | idem |
| ID: Barra lateral | `seisdeagosto_adsense_slot_lateral` | idem |
| ID: Entre notícias | `seisdeagosto_adsense_slot_lista` | idem |
| ID: Capa | `seisdeagosto_adsense_slot_capa` | idem |

A descrição da seção explica, em uma frase cada:
- onde achar a tag e o ID dos blocos de anúncio;
- que os Anúncios automáticos são ligados no painel;
- a recomendação da âncora embaixo.

## 4. Carregamento e cooperação com o robô do AdSense

**Regra única de exibição:** `seisdeagosto_adsense_is_allowed()` retorna `false` quando:
- não há ID válido ou "Ativar anúncios" está desligado;
- `is_user_logged_in()`;
- `is_singular( 'edital' )` ou `is_post_type_archive( 'edital' )`;
- `is_404()`, ou `is_search()` sem resultados;
- `is_customize_preview()`, `is_feed()`, `is_admin()`, `wp_doing_ajax()`, requisição REST;
- requisição AMP (`u_seisbarra8_is_amp()`).

O resultado passa pelo filtro `seisdeagosto_adsense_is_allowed`, para exceções.

**Script:** em `wp_head`, uma única vez:
`<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-…" crossorigin="anonymous"></script>`

- Ele **não** é impresso se `seisdeagosto_adsense_is_allowed()` for falso.
- Também não é impresso se outro plugin já carregar o AdSense. Detecção feita na
  implementação: módulo AdSense do Site Kit com snippet ativo, mais o filtro
  `seisdeagosto_adsense_skip_script`.
- Quando o script não é impresso, as posições fixas também não aparecem.

**Meta de verificação:** `<meta name="google-adsense-account" content="ca-pub-…">`
sempre que houver ID válido, inclusive para logados, porque serve só para o Google
verificar o site.

**`/ads.txt`:** quando houver ID válido, uma requisição a `/ads.txt` responde em
`text/plain`:
`google.com, pub-XXXXXXXXXXXXXXXX, DIRECT, f08c47fec0942fa0`
- Um arquivo `ads.txt` físico no servidor tem prioridade, porque o servidor web o
  entrega antes do WordPress.
- Se o WordPress não estiver na raiz do domínio, um aviso no admin explica que o
  `ads.txt` precisa ser criado manualmente na raiz.

**`robots.txt`:** pelo filtro `robots_txt`, acrescenta
`User-agent: Mediapartners-Google` / `Allow: /`. Isso vale quando o `robots.txt` é
virtual, gerado pelo WordPress.

## 5. Unidade de anúncio

`seisdeagosto_adsense_unit( $posicao, $slot, $formato )` retorna:

```html
<div class="sda-ad sda-ad--{posicao} sda-ad--{formato}">
  <span class="sda-ad__label">Publicidade</span>
  <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-…"
       data-ad-slot="…" data-ad-format="auto" data-full-width-responsive="false"></ins>
  <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</div>
```

| Formato | Atributos do `<ins>` |
|---|---|
| `auto` (responsivo) | `data-ad-format="auto"` |
| `horizontal` | `data-ad-format="horizontal"` |
| `vertical` | `data-ad-format="vertical"` |
| `retangulo` | `data-ad-format="rectangle"` |
| `materia` (in-article) | `style="display:block; text-align:center;" data-ad-layout="in-article" data-ad-format="fluid"` |

Em todos os formatos, `data-full-width-responsive="false"`, para o anúncio não sair
da coluna. A função retorna `''` se não houver slot ou se `is_allowed()` for falso.
Toda saída é escapada (`esc_attr`).

## 6. Posições fixas

Cada posição aparece só com slot preenchido e no máximo uma vez por página.

| # | Posição | Onde | Como | Formato |
|---|---|---|---|---|
| 1 | Topo | Logo após o fechamento do `</header>` do template, **fora** do cabeçalho fixo (para o anúncio não "grudar" no topo) | `render_block_core/html`: no primeiro bloco HTML que contém `</header>`, insere logo após essa tag | `horizontal` |
| 2 | Dentro da matéria | Após o N-ésimo parágrafo (padrão 3) do post | `the_content` com prioridade 8 (antes de `do_blocks`), só no post principal de `is_singular( 'post' )`, detalhes abaixo | `materia` |
| 3 | Fim da matéria | Logo após o conteúdo do post | `render_block_core/post-content` em `is_singular( 'post' )` | `auto` |
| 4 | Barra lateral | Topo da barra lateral direita, nos posts | `dynamic_sidebar_before` para `right-sidebar` em `is_singular( 'post' )` | `vertical` |
| 5 | Entre notícias | Antes da 5ª notícia das listas | `render_block_core/post-template` em arquivo, categoria, tag e busca: insere um `<li class="wp-block-post sda-ad-item">` antes do 5º `<li class="wp-block-post` | `auto` |
| 6 | Capa | Após o primeiro bloco de destaques da capa | `render_block` em `is_front_page()`, depois do primeiro `seisdeagosto/destaques-home` (e aliases) | `horizontal` |

**Regras do anúncio dentro da matéria (posição 2):**
- Posts em blocos: `parse_blocks()`, contando só blocos `core/paragraph` de primeiro
  nível. O anúncio entra depois do N-ésimo parágrafo, **se** o próximo bloco de
  primeiro nível também for um parágrafo; senão avança para o próximo ponto entre dois
  parágrafos.
- O anúncio nunca entra dentro de listas, citações, tabelas, galerias, colunas ou
  grupos, nem antes de imagem ou título.
- Só aparece em matérias com pelo menos N + 2 parágrafos de primeiro nível. Depois,
  `serialize_blocks()`.
- Posts clássicos (sem blocos): insere após o N-ésimo `</p>` seguido diretamente de `<p`.

## 7. Bloco "Anúncio"

- Nome `seisdeagosto/anuncio`, categoria "Seis de Agosto". Atributos `slot` (string)
  e `formato` (`auto`, `horizontal`, `vertical`, `retangulo`, `materia`).
- **Editor:** caixa cinza "Anúncio AdSense: ID …", com o tamanho aproximado do formato.
  Sem script do Google no editor.
- **Front:** `seisdeagosto_adsense_unit( 'bloco', $slot, $formato )`.
- Segue o padrão dos blocos existentes: `block.json` + `edit.js` com `wp.element`, sem
  build.

## 8. Layout sem deformação (`css/adsense.css`)

Carregado só quando `is_allowed()`.

- **Contêiner:** `.sda-ad { display:block; width:100%; max-width:100%; margin:1.5rem auto; text-align:center; clear:both; }`.
  O `ins.adsbygoogle` tem `max-width:100%`. Nada de `overflow:hidden`, que cortaria o
  anúncio e viola a política.
- **Altura reservada (CLS):** `--horizontal` 90 px (100 px no celular); `--auto`,
  `--retangulo` e `--materia` 250 px; `--vertical` 600 px no desktop e 250 px no
  celular.
- **Rótulo:** `.sda-ad__label` com fonte de 11 px, cor `#767676` (contraste AA) e
  maiúsculas.
- **Espaço vazio:**
  `.sda-ad:has(ins.adsbygoogle[data-ad-status="unfilled"]) { display:none; }` e
  `ins.adsbygoogle[data-ad-status="unfilled"] { display:none !important; }`, a forma
  documentada pelo próprio AdSense.
- **Item entre notícias:** `.sda-ad-item` ocupa a linha inteira do grid/flex da lista.
- **Acomodação dos Anúncios automáticos** (classe `.google-auto-placed`, que o Google
  adiciona):
  - em qualquer lugar: `max-width:100%; clear:both`;
  - dentro de `.row` e contêineres flex do tema: `flex:0 0 100%; max-width:100%; width:100%`;
  - dentro de grids (`.news-grid`, `.wp-block-post-template.is-layout-grid` e afins):
    `grid-column:1 / -1`.

  O anúncio ocupa a linha inteira em vez de espremer os cards. Ele **nunca é
  escondido**.

## 9. Testes

**PHP, isolado com stubs** (em `dev/tests/`, fora do .zip do tema):
- extração do ID a partir de: tag completa, só o ID, ID com espaços, lixo e
  `<script>` malicioso;
- validação do slot;
- todas as regras de `is_allowed()`;
- inserção na matéria: posição N, pulo antes de imagem ou título, mínimo de parágrafos,
  parágrafo dentro de lista ou grupo não conta, post clássico;
- HTML da unidade escapado.

**No WordPress Playground, com o AdSense simulado:** a URL do `adsbygoogle.js` é
interceptada e servida por um script de teste que preenche cada `<ins>` com um bloco
de tamanho fixo e injeta `.google-auto-placed` dentro dos grids do tema.

| Verificação | Como |
|---|---|
| Script, meta, `ads.txt`, `robots.txt` | Conteúdo exato; script ausente para logado, edital, 404, busca vazia e preview |
| Sem deformação | Em 1366, 820 e 390 px, e com anúncios de 300×250, 336×280, 728×90, 970×250 e 1200×300: **(1)** sem rolagem horizontal; **(2)** colunas de conteúdo, cards do grid e Destaque Misto com a **mesma largura** que tinham sem anúncio (tolerância de 1 px); **(3)** nenhum anúncio sobrepondo texto ou outro elemento |
| Altura reservada | O anúncio de 250 px não desloca o conteúdo abaixo, porque o espaço já estava reservado |
| Unfilled | `<ins>` marcado `unfilled` deixa o espaço recolhido |
| Posições | Cada uma aparece só com slot, uma vez, no lugar certo; topo fora do `<header>` fixo |
| Bloco "Anúncio" | Renderiza no front e mostra a caixa no editor |

## 10. Riscos

- **Mudanças no AdSense:** o Google pode mudar a classe `.google-auto-placed` ou o
  `data-ad-status`. O teste com anúncio simulado pega isso só se o simulador for
  atualizado. Documentar no README.
- **Template editado no Editor do Site:** se o `</header>` sair de um bloco HTML, a
  posição Topo deixa de aparecer. Ela falha em silêncio, sem quebrar a página.
- **Detecção de outro plugin:** cobre o Site Kit e o filtro. Outros plugins podem
  duplicar o script, e o README explica como desligar no tema.
