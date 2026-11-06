<?php
/**
 * AdSense: posições fixas nos templates de bloco.
 *
 * Cada posição só aparece com o ID do bloco de anúncio preenchido no Personalizador
 * e no máximo uma vez por página. Sem ID, o lugar fica a cargo dos Anúncios automáticos.
 *
 * @package SeisdeAgosto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Marca uma posição como usada nesta requisição.
 *
 * @param string $position Posição.
 * @return bool true se ainda não tinha sido usada (pode inserir).
 */
function seisdeagosto_adsense_claim( $position ) {
	static $used = array();
	if ( isset( $used[ $position ] ) ) {
		return false;
	}
	$used[ $position ] = true;
	return true;
}

/**
 * Anúncio de uma posição fixa, se ela tiver ID e ainda não tiver sido usada.
 *
 * @param string $position Chave de seisdeagosto_adsense_positions().
 * @return string HTML ou ''.
 */
function seisdeagosto_adsense_position_html( $position ) {
	$positions = seisdeagosto_adsense_positions();
	$slot      = seisdeagosto_adsense_get_slot( $position );
	if ( '' === $slot || ! isset( $positions[ $position ] ) || ! seisdeagosto_adsense_is_allowed() ) {
		return '';
	}
	if ( ! seisdeagosto_adsense_claim( $position ) ) {
		return '';
	}
	return seisdeagosto_adsense_unit( $position, $slot, $positions[ $position ]['formato'] );
}

/**
 * O post sendo renderizado é o post principal desta página de post?
 *
 * @return bool
 */
function seisdeagosto_adsense_is_main_post() {
	return is_singular( 'post' ) && (int) get_the_ID() === (int) get_queried_object_id();
}

/*
-------------------------------------------------------------------------
 * 1. Topo: logo após o </header> do template, fora do cabeçalho fixo.
 * ---------------------------------------------------------------------- */

/**
 * Posição logo após o </header> que fecha um cabeçalho aberto FORA deste bloco.
 *
 * Os templates abrem <header class="site-header sticky-top"> num bloco HTML, colocam
 * o template part do cabeçalho (que tem o seu próprio <header>…</header>) e fecham
 * o </header> em outro bloco HTML. O anúncio precisa ir depois desse fechamento de
 * fora; depois do </header> interno ele ficaria dentro do cabeçalho fixo.
 *
 * @param string $html HTML de um bloco.
 * @return int|false Posição logo após o </header> de fora, ou false.
 */
function seisdeagosto_adsense_outer_header_close( $html ) {
	if ( ! preg_match_all( '#<(/?)header\b[^>]*>#i', $html, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
		return false;
	}
	$depth = 0;
	foreach ( $m as $tag ) {
		if ( '' === $tag[1][0] ) {
			++$depth; // <header …> aberto neste bloco.
		} elseif ( $depth > 0 ) {
			--$depth; // Fecha um <header> aberto neste mesmo bloco: não é o de fora.
		} else {
			return $tag[0][1] + strlen( $tag[0][0] );
		}
	}
	return false;
}

/**
 * Insere o anúncio do topo logo após o cabeçalho do template (fora do cabeçalho fixo).
 *
 * @param string $content Bloco renderizado.
 * @return string
 */
function seisdeagosto_adsense_place_topo( $content ) {
	if ( false === stripos( $content, '</header>' ) || '' === seisdeagosto_adsense_get_slot( 'topo' ) ) {
		return $content;
	}
	$pos = seisdeagosto_adsense_outer_header_close( $content );
	if ( false === $pos ) {
		return $content;
	}
	$ad = seisdeagosto_adsense_position_html( 'topo' );
	if ( '' === $ad ) {
		return $content;
	}
	return substr( $content, 0, $pos ) . '<div class="container sda-ad-topo-wrap">' . $ad . '</div>' . substr( $content, $pos );
}
add_filter( 'render_block_core/html', 'seisdeagosto_adsense_place_topo', 20 );

/*
-------------------------------------------------------------------------
 * 2. Dentro da matéria: entre parágrafos de primeiro nível.
 * ---------------------------------------------------------------------- */

/**
 * Insere um bloco depois do N-ésimo parágrafo de primeiro nível de um conteúdo em blocos.
 *
 * Só entre dois parágrafos (nunca antes de imagem, título, lista etc.; nunca dentro
 * de grupos, colunas, citações). Exige pelo menos N + 2 parágrafos de primeiro nível.
 *
 * @param string $content Conteúdo do post (marcação de blocos).
 * @param int    $n       Parágrafo após o qual inserir.
 * @param string $markup  Marcação a inserir (um bloco serializado).
 * @param int    $after   (Saída) Número do parágrafo após o qual inseriu; 0 se não inseriu.
 * @return string Conteúdo com o bloco, ou o original se não houver lugar adequado.
 */
function seisdeagosto_adsense_insert_after_paragraph_blocks( $content, $n, $markup, &$after = 0 ) {
	$after  = 0;
	$blocks = parse_blocks( $content );
	$tops   = array();
	foreach ( $blocks as $i => $block ) {
		if ( ! empty( $block['blockName'] ) ) {
			$tops[] = $i;
		}
	}

	$is_paragraph = function ( $block ) {
		return 'core/paragraph' === $block['blockName'] && '' !== trim( wp_strip_all_tags( $block['innerHTML'] ) );
	};

	$total = 0;
	foreach ( $tops as $i ) {
		if ( $is_paragraph( $blocks[ $i ] ) ) {
			++$total;
		}
	}
	if ( $total < $n + 2 ) {
		return $content;
	}

	$count      = 0;
	$count_tops = count( $tops );
	for ( $k = 0; $k < $count_tops - 1; $k++ ) {
		$current = $blocks[ $tops[ $k ] ];
		$next    = $blocks[ $tops[ $k + 1 ] ];
		if ( ! $is_paragraph( $current ) ) {
			continue;
		}
		++$count;
		if ( $count >= $n && $is_paragraph( $next ) ) {
			$insert = parse_blocks( $markup );
			array_splice( $blocks, $tops[ $k ] + 1, 0, $insert );
			$after = $count;
			return serialize_blocks( $blocks );
		}
	}
	return $content;
}

/**
 * Número de parágrafos (não vazios) de primeiro nível de um conteúdo em blocos.
 *
 * @param string $content Conteúdo do post.
 * @return int
 */
function seisdeagosto_adsense_count_paragraph_blocks( $content ) {
	$total = 0;
	foreach ( parse_blocks( $content ) as $block ) {
		if ( 'core/paragraph' === $block['blockName'] && '' !== trim( wp_strip_all_tags( $block['innerHTML'] ) ) ) {
			++$total;
		}
	}
	return $total;
}

/**
 * Parágrafo do 2º anúncio, ou 0 se a matéria não for longa o bastante ou se ele estiver desligado.
 *
 * Regras: matéria com pelo menos 14 parágrafos (filtro seisdeagosto_adsense_materia2_min),
 * e o 2º anúncio sempre a pelo menos 4 parágrafos do primeiro.
 *
 * @param int $total Parágrafos da matéria.
 * @param int $first Parágrafo após o qual entrou o 1º anúncio.
 * @return int
 */
function seisdeagosto_adsense_second_paragraph( $total, $first ) {
	if ( ! $first || ! seisdeagosto_adsense_materia2_enabled() ) {
		return 0;
	}
	$min = max( 8, (int) apply_filters( 'seisdeagosto_adsense_materia2_min', 14 ) );
	if ( $total < $min ) {
		return 0;
	}
	return max( seisdeagosto_adsense_get_paragraph2(), $first + 4 );
}

/**
 * Insere HTML depois do N-ésimo parágrafo de um post clássico (sem blocos), já com <p>.
 *
 * @param string $html  HTML do post (depois do wpautop).
 * @param int    $n     Parágrafo após o qual inserir.
 * @param string $ad    HTML do anúncio.
 * @param int    $after (Saída) Número do parágrafo após o qual inseriu; 0 se não inseriu.
 * @return string
 */
function seisdeagosto_adsense_insert_after_paragraph_html( $html, $n, $ad, &$after = 0 ) {
	$after = 0;
	// Pontos "</p> seguido diretamente de <p": entre dois parágrafos.
	if ( ! preg_match_all( '#</p>(?=\s*<p[\s>])#i', $html, $m, PREG_OFFSET_CAPTURE ) ) {
		return $html;
	}
	$total = substr_count( strtolower( $html ), '</p>' );
	if ( $total < $n + 2 ) {
		return $html;
	}
	foreach ( $m[0] as $match ) {
		$before = substr( $html, 0, $match[1] );
		$index  = substr_count( strtolower( $before ), '</p>' ) + 1; // Número deste </p>.
		if ( $index >= $n ) {
			$pos   = $match[1] + strlen( '</p>' );
			$after = $index;
			return substr( $html, 0, $pos ) . $ad . substr( $html, $pos );
		}
	}
	return $html;
}

/**
 * Posts em blocos: insere o bloco "Anúncio" antes do do_blocks (prioridade 8).
 *
 * @param string $content Conteúdo do post.
 * @return string
 */
function seisdeagosto_adsense_place_materia_blocks( $content ) {
	if ( ! has_blocks( $content ) || ! seisdeagosto_adsense_is_main_post() ) {
		return $content;
	}
	$slot = seisdeagosto_adsense_get_slot( 'materia' );
	if ( '' === $slot || ! seisdeagosto_adsense_is_allowed() ) {
		return $content;
	}
	$markup = function ( $posicao ) use ( $slot ) {
		return '<!-- wp:seisdeagosto/anuncio ' . wp_json_encode(
			array(
				'slot'    => $slot,
				'formato' => 'materia',
				'posicao' => $posicao,
			)
		) . ' /-->';
	};
	$first  = 0;
	$result = seisdeagosto_adsense_insert_after_paragraph_blocks( $content, seisdeagosto_adsense_get_paragraph(), $markup( 'materia' ), $first );
	if ( ! $first ) {
		return $content;
	}
	seisdeagosto_adsense_claim( 'materia' );

	// 2º anúncio nas matérias longas (o mesmo bloco de anúncio pode se repetir na página).
	$n2 = seisdeagosto_adsense_second_paragraph( seisdeagosto_adsense_count_paragraph_blocks( $content ), $first );
	if ( $n2 ) {
		$second = 0;
		$with2  = seisdeagosto_adsense_insert_after_paragraph_blocks( $result, $n2, $markup( 'materia-2' ), $second );
		if ( $second ) {
			seisdeagosto_adsense_claim( 'materia-2' );
			$result = $with2;
		}
	}
	return $result;
}
add_filter( 'the_content', 'seisdeagosto_adsense_place_materia_blocks', 8 );

/**
 * Posts clássicos: insere o anúncio depois do wpautop (prioridade 20).
 *
 * @param string $content Conteúdo do post.
 * @return string
 */
function seisdeagosto_adsense_place_materia_classic( $content ) {
	if ( false !== strpos( $content, 'sda-ad--materia' ) || ! seisdeagosto_adsense_is_main_post() ) {
		return $content;
	}
	$post = get_post();
	if ( ! $post || has_blocks( $post->post_content ) ) {
		return $content;
	}
	$ad = seisdeagosto_adsense_position_html( 'materia' );
	if ( '' === $ad ) {
		return $content;
	}
	$first  = 0;
	$result = seisdeagosto_adsense_insert_after_paragraph_html( $content, seisdeagosto_adsense_get_paragraph(), $ad, $first );
	$n2     = seisdeagosto_adsense_second_paragraph( substr_count( strtolower( $content ), '</p>' ), $first );
	if ( $n2 && seisdeagosto_adsense_claim( 'materia-2' ) ) {
		$ad2    = seisdeagosto_adsense_unit( 'materia-2', seisdeagosto_adsense_get_slot( 'materia' ), 'materia' );
		$result = seisdeagosto_adsense_insert_after_paragraph_html( $result, $n2, $ad2 );
	}
	return $result;
}
add_filter( 'the_content', 'seisdeagosto_adsense_place_materia_classic', 20 );

/*
-------------------------------------------------------------------------
 * 3. Fim da matéria.
 * ---------------------------------------------------------------------- */

/**
 * Anúncio logo após o conteúdo do post principal.
 *
 * @param string   $content  Bloco renderizado.
 * @param array    $block    Bloco.
 * @param WP_Block $instance Instância (contexto postId).
 * @return string
 */
function seisdeagosto_adsense_place_fim( $content, $block, $instance = null ) {
	if ( ! is_singular( 'post' ) ) {
		return $content;
	}
	$post_id = ( $instance instanceof WP_Block && isset( $instance->context['postId'] ) ) ? (int) $instance->context['postId'] : (int) get_the_ID();
	if ( $post_id !== (int) get_queried_object_id() ) {
		return $content;
	}
	return $content . seisdeagosto_adsense_position_html( 'fim' );
}
add_filter( 'render_block_core/post-content', 'seisdeagosto_adsense_place_fim', 20, 3 );

/*
-------------------------------------------------------------------------
 * 4. Barra lateral (topo da barra lateral direita, nos posts).
 * ---------------------------------------------------------------------- */

/**
 * Anúncio no topo da barra lateral direita.
 *
 * @param int|string $index ID da área de widgets.
 */
function seisdeagosto_adsense_place_lateral( $index ) {
	if ( 'right-sidebar' !== $index || ! is_singular( 'post' ) ) {
		return;
	}
	echo seisdeagosto_adsense_position_html( 'lateral' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML montado e escapado em seisdeagosto_adsense_unit().
}
add_action( 'dynamic_sidebar_before', 'seisdeagosto_adsense_place_lateral' );

/*
-------------------------------------------------------------------------
 * 5. Entre notícias (listas de arquivo, categoria, tag, busca).
 * ---------------------------------------------------------------------- */

/**
 * Insere um item de anúncio antes da N-ésima notícia de uma lista (core/post-template).
 *
 * @param string $html Lista renderizada.
 * @param int    $n    Posição do item antes do qual inserir (1 = primeiro).
 * @param string $ad   HTML do anúncio.
 * @return string
 */
function seisdeagosto_adsense_insert_in_post_list( $html, $n, $ad ) {
	if ( ! preg_match_all( '#<li\s[^>]*class="[^"]*\bwp-block-post\b#i', $html, $m, PREG_OFFSET_CAPTURE ) || count( $m[0] ) < $n ) {
		return $html;
	}
	$pos = $m[0][ $n - 1 ][1];
	return substr( $html, 0, $pos ) . '<li class="wp-block-post sda-ad-item">' . $ad . '</li>' . substr( $html, $pos );
}

/**
 * Anúncio antes da 5ª notícia das listas.
 *
 * @param string $content Bloco renderizado.
 * @return string
 */
function seisdeagosto_adsense_place_lista( $content ) {
	if ( ! ( is_archive() || is_search() || is_home() ) || is_front_page() || '' === seisdeagosto_adsense_get_slot( 'lista' ) ) {
		return $content;
	}
	if ( ! preg_match_all( '#<li\s[^>]*class="[^"]*\bwp-block-post\b#i', $content, $m ) || count( $m[0] ) < 5 ) {
		return $content;
	}
	$ad = seisdeagosto_adsense_position_html( 'lista' );
	return '' === $ad ? $content : seisdeagosto_adsense_insert_in_post_list( $content, 5, $ad );
}
add_filter( 'render_block_core/post-template', 'seisdeagosto_adsense_place_lista', 20 );

/**
 * Posições de início dos itens diretos da primeira .news-grid de um HTML.
 *
 * @param string $html HTML do bloco Grade de Notícias.
 * @return int[] Deslocamentos (em bytes) do "<div" de cada item direto da grade.
 */
function seisdeagosto_adsense_news_grid_items( $html ) {
	if ( ! preg_match( '#<div\s[^>]*class="[^"]*\bnews-grid\b[^"]*"[^>]*>#i', $html, $g, PREG_OFFSET_CAPTURE ) ) {
		return array();
	}
	$start = $g[0][1] + strlen( $g[0][0] );
	if ( ! preg_match_all( '#<(/?)div\b[^>]*>#i', $html, $tags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE, $start ) ) {
		return array();
	}
	$items = array();
	$depth = 0;
	foreach ( $tags as $tag ) {
		if ( '' === $tag[1][0] ) {
			if ( 0 === $depth ) {
				$items[] = $tag[0][1]; // Item direto da grade.
			}
			++$depth;
		} elseif ( 0 === $depth ) {
			break; // Fechou a própria .news-grid.
		} else {
			--$depth;
		}
	}
	return $items;
}

/**
 * Insere um item de anúncio de largura inteira antes do N-ésimo item da Grade de Notícias.
 *
 * @param string $html HTML do bloco.
 * @param int    $n    Item antes do qual inserir (1 = primeiro).
 * @param string $ad   HTML do anúncio.
 * @return string
 */
function seisdeagosto_adsense_insert_in_news_grid( $html, $n, $ad ) {
	$items = seisdeagosto_adsense_news_grid_items( $html );
	if ( count( $items ) < $n ) {
		return $html;
	}
	$pos = $items[ $n - 1 ];
	return substr( $html, 0, $pos ) . '<div class="col-12 sda-ad-item">' . $ad . '</div>' . substr( $html, $pos );
}

/**
 * "Entre notícias" no bloco Grade de Notícias (categorias, tags, busca e capa):
 * anúncio de largura inteira depois da 6ª notícia (2 linhas de 3), na primeira
 * grade da página que tenha pelo menos 7 notícias.
 *
 * @param string $content Bloco renderizado.
 * @param array  $block   Bloco.
 * @return string
 */
function seisdeagosto_adsense_place_lista_grid( $content, $block ) {
	$names = array( 'seisdeagosto/news-grid', 'seideagosto/news-grid', 'u-correio68/news-grid', 'correio68/news-grid' );
	if ( empty( $block['blockName'] ) || ! in_array( $block['blockName'], $names, true ) ) {
		return $content;
	}
	if ( ! ( is_archive() || is_search() || is_home() || is_front_page() ) || '' === seisdeagosto_adsense_get_slot( 'lista' ) ) {
		return $content;
	}
	/**
	 * Notícia antes da qual entra o anúncio na Grade de Notícias (padrão: 7, ou seja, após a 6ª).
	 *
	 * @param int $before Posição do item.
	 */
	$before = max( 2, (int) apply_filters( 'seisdeagosto_adsense_grid_position', 7 ) );
	if ( count( seisdeagosto_adsense_news_grid_items( $content ) ) < $before ) {
		return $content;
	}
	$ad = seisdeagosto_adsense_position_html( 'lista' );
	return '' === $ad ? $content : seisdeagosto_adsense_insert_in_news_grid( $content, $before, $ad );
}
add_filter( 'render_block', 'seisdeagosto_adsense_place_lista_grid', 20, 2 );

/*
-------------------------------------------------------------------------
 * 6. Capa: após o primeiro bloco de destaques.
 * ---------------------------------------------------------------------- */

/**
 * Anúncio depois do primeiro bloco "Destaques Home" da capa.
 *
 * @param string $content Bloco renderizado.
 * @param array  $block   Bloco.
 * @return string
 */
function seisdeagosto_adsense_place_capa( $content, $block ) {
	$names = array( 'seisdeagosto/destaques-home', 'seideagosto/destaques-home', 'u-correio68/destaques-home' );
	if ( empty( $block['blockName'] ) || ! in_array( $block['blockName'], $names, true ) || ! is_front_page() ) {
		return $content;
	}
	return $content . seisdeagosto_adsense_position_html( 'capa' );
}
add_filter( 'render_block', 'seisdeagosto_adsense_place_capa', 20, 2 );
