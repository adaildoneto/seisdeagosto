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
 * @return string Conteúdo com o bloco, ou o original se não houver lugar adequado.
 */
function seisdeagosto_adsense_insert_after_paragraph_blocks( $content, $n, $markup ) {
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
			return serialize_blocks( $blocks );
		}
	}
	return $content;
}

/**
 * Insere HTML depois do N-ésimo parágrafo de um post clássico (sem blocos), já com <p>.
 *
 * @param string $html HTML do post (depois do wpautop).
 * @param int    $n    Parágrafo após o qual inserir.
 * @param string $ad   HTML do anúncio.
 * @return string
 */
function seisdeagosto_adsense_insert_after_paragraph_html( $html, $n, $ad ) {
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
			$pos = $match[1] + strlen( '</p>' );
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
	$markup = '<!-- wp:seisdeagosto/anuncio ' . wp_json_encode(
		array(
			'slot'    => $slot,
			'formato' => 'materia',
			'posicao' => 'materia',
		)
	) . ' /-->';
	$result = seisdeagosto_adsense_insert_after_paragraph_blocks( $content, seisdeagosto_adsense_get_paragraph(), $markup );
	if ( $result !== $content ) {
		seisdeagosto_adsense_claim( 'materia' );
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
	return seisdeagosto_adsense_insert_after_paragraph_html( $content, seisdeagosto_adsense_get_paragraph(), $ad );
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
