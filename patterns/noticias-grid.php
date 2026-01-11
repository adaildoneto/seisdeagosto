<?php
/**
 * Title: Lista de Notícias
 * Slug: seisdeagosto/noticias-grid
 * Categories: posts, query, seisdeagosto
 * Description: Últimas notícias em grade com imagem, título e data.
 * Viewport Width: 1200
 */
?>
<!-- wp:group {"align":"wide","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide"><!-- wp:heading {"level":2} -->
<h2>Últimas notícias</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":1,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"grid","columns":3},"align":"wide"} -->
<div class="wp-block-query alignwide"><!-- wp:post-template -->
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:post-featured-image {"isLink":true,"sizeSlug":"large"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-date {"format":"j M, Y"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Nenhum conteúdo encontrado.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
