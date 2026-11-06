<?php
/**
 * Testes do módulo AdSense, executados dentro do WordPress (funções reais).
 *
 * Uso (WordPress de teste): um runner carrega o wp-load.php, define
 * SEISDEAGOSTO_RUN_TESTS e inclui este arquivo. Não faz nada se aberto direto.
 *
 * @package SeisdeAgosto
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'SEISDEAGOSTO_RUN_TESTS' ) ) {
	exit;
}

$GLOBALS['sda_fail'] = 0;
$GLOBALS['sda_pass'] = 0;

/**
 * Registra o resultado de uma verificação.
 *
 * @param bool   $ok  Passou.
 * @param string $msg Descrição.
 */
function sda_check( $ok, $msg ) {
	if ( $ok ) {
		++$GLOBALS['sda_pass'];
		echo 'PASS ', $msg, "\n";
	} else {
		++$GLOBALS['sda_fail'];
		echo 'FAIL ', $msg, "\n";
	}
}

/**
 * Parágrafo em blocos.
 *
 * @param string $t Texto.
 * @return string
 */
function sda_p( $t ) {
	return "<!-- wp:paragraph -->\n<p>" . $t . "</p>\n<!-- /wp:paragraph -->\n\n";
}

$sda_saved_mods = get_theme_mods();
$client         = 'ca-pub-1234567890123456';

// ---------------------------------------------------------------- ID e slot.
sda_check( seisdeagosto_adsense_extract_client( '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1234567890123456" crossorigin="anonymous"></script>' ) === $client, 'ID extraído da tag completa' );
sda_check( seisdeagosto_adsense_extract_client( '  ca-pub-1234567890123456 ' ) === $client, 'ID com espaços' );
sda_check( seisdeagosto_adsense_extract_client( 'pub-1234567890123456' ) === $client, 'ID sem o prefixo ca-' );
sda_check( seisdeagosto_adsense_extract_client( 'CA-PUB-1234567890123456' ) === $client, 'ID em maiúsculas' );
sda_check( '' === seisdeagosto_adsense_extract_client( 'ca-pub-123' ), 'ID curto demais é recusado' );
sda_check( '' === seisdeagosto_adsense_extract_client( '<script>alert(1)</script>' ), 'lixo/script sem ID é recusado' );
sda_check( '' === seisdeagosto_adsense_extract_client( array( 'x' ) ), 'valor não-texto é recusado' );
sda_check( '1234567890' === seisdeagosto_adsense_sanitize_slot( ' 1234567890 ' ), 'slot numérico aceito' );
sda_check( '' === seisdeagosto_adsense_sanitize_slot( '12345"><script>' ), 'slot com HTML recusado' );
sda_check( '' === seisdeagosto_adsense_sanitize_slot( '123' ), 'slot curto recusado' );
sda_check( 3 === seisdeagosto_adsense_sanitize_paragraph( 99 ) && 5 === seisdeagosto_adsense_sanitize_paragraph( '5' ), 'parágrafo limitado a 2..10' );

// ---------------------------------------------------------------- Regras de exibição.
remove_theme_mod( 'seisdeagosto_adsense_client' );
sda_check( ! seisdeagosto_adsense_is_allowed(), 'sem ID: anúncios desligados' );
set_theme_mod( 'seisdeagosto_adsense_client', $client );
set_theme_mod( 'seisdeagosto_adsense_enabled', true );
wp_set_current_user( 0 );
sda_check( seisdeagosto_adsense_is_allowed(), 'com ID e visitante anônimo: anúncios ligados' );
set_theme_mod( 'seisdeagosto_adsense_enabled', false );
sda_check( ! seisdeagosto_adsense_is_allowed(), '"Ativar anúncios" desligado' );
set_theme_mod( 'seisdeagosto_adsense_enabled', true );
$admins = get_users(
	array(
		'role'   => 'administrator',
		'number' => 1,
		'fields' => 'ID',
	)
);
wp_set_current_user( (int) $admins[0] );
sda_check( ! seisdeagosto_adsense_is_allowed(), 'usuário logado não vê anúncios' );
wp_set_current_user( 0 );
add_filter( 'seisdeagosto_adsense_is_allowed', '__return_false' );
sda_check( ! seisdeagosto_adsense_is_allowed(), 'filtro seisdeagosto_adsense_is_allowed funciona' );
remove_filter( 'seisdeagosto_adsense_is_allowed', '__return_false' );

// ---------------------------------------------------------------- Unidade.
$unit = seisdeagosto_adsense_unit( 'fim', '1234567890', 'auto' );
sda_check( false !== strpos( $unit, 'data-ad-client="ca-pub-1234567890123456"' ) && false !== strpos( $unit, 'data-ad-slot="1234567890"' ), 'unidade com client e slot' );
sda_check( false !== strpos( $unit, 'data-full-width-responsive="false"' ), 'unidade não ocupa a tela toda no celular' );
sda_check( false !== strpos( $unit, 'Publicidade' ) && false !== strpos( $unit, '.push({})' ), 'unidade com rótulo e push' );
$materia = seisdeagosto_adsense_unit( 'materia', '1234567890', 'materia' );
sda_check( false !== strpos( $materia, 'data-ad-layout="in-article"' ) && false !== strpos( $materia, 'data-ad-format="fluid"' ) && false === strpos( $materia, 'full-width' ), 'formato "No artigo" correto' );
sda_check( '' === seisdeagosto_adsense_unit( 'fim', 'abc', 'auto' ), 'slot inválido: sem unidade' );
sda_check( false !== strpos( seisdeagosto_adsense_unit( '"><x', '1234567890', 'inexistente' ), 'sda-ad--auto' ) && false === strpos( seisdeagosto_adsense_unit( '"><x', '1234567890', 'auto' ), '"><x' ), 'posição e formato saneados' );

// ---------------------------------------------------------------- Dentro da matéria (blocos).
$mark  = '<!-- wp:seisdeagosto/anuncio {"slot":"1"} /-->';
$six   = sda_p( 'P1' ) . sda_p( 'P2' ) . sda_p( 'P3' ) . sda_p( 'P4' ) . sda_p( 'P5' ) . sda_p( 'P6' );
$out   = seisdeagosto_adsense_insert_after_paragraph_blocks( $six, 3, $mark );
$order = array_values( array_filter( wp_list_pluck( parse_blocks( $out ), 'blockName' ) ) );
sda_check( array( 'core/paragraph', 'core/paragraph', 'core/paragraph', 'seisdeagosto/anuncio', 'core/paragraph', 'core/paragraph', 'core/paragraph' ) === $order, 'anúncio após o 3º parágrafo' );

$four = sda_p( 'P1' ) . sda_p( 'P2' ) . sda_p( 'P3' ) . sda_p( 'P4' );
sda_check( seisdeagosto_adsense_insert_after_paragraph_blocks( $four, 3, $mark ) === $four, 'matéria curta (4 parágrafos, N=3): sem anúncio' );

$img   = "<!-- wp:image -->\n<figure class=\"wp-block-image\"><img src=\"x.jpg\" alt=\"\"/></figure>\n<!-- /wp:image -->\n\n";
$head  = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Título</h2>\n<!-- /wp:heading -->\n\n";
$mixed = sda_p( 'P1' ) . sda_p( 'P2' ) . sda_p( 'P3' ) . $img . sda_p( 'P4' ) . $head . sda_p( 'P5' ) . sda_p( 'P6' ) . sda_p( 'P7' );
$out   = seisdeagosto_adsense_insert_after_paragraph_blocks( $mixed, 3, $mark );
$order = array_values( array_filter( wp_list_pluck( parse_blocks( $out ), 'blockName' ) ) );
$at    = array_search( 'seisdeagosto/anuncio', $order, true );
sda_check( false !== $at && 'core/paragraph' === $order[ $at - 1 ] && 'core/paragraph' === $order[ $at + 1 ], 'nunca antes de imagem ou título: entre dois parágrafos' );
sda_check( 7 === $at, 'avança até o primeiro ponto entre dois parágrafos após o 3º (depois do P5)' );

$group = "<!-- wp:group -->\n<div class=\"wp-block-group\">" . sda_p( 'G1' ) . sda_p( 'G2' ) . sda_p( 'G3' ) . sda_p( 'G4' ) . sda_p( 'G5' ) . "</div>\n<!-- /wp:group -->\n\n";
sda_check( seisdeagosto_adsense_insert_after_paragraph_blocks( $group . sda_p( 'P1' ), 3, $mark ) === $group . sda_p( 'P1' ), 'parágrafos dentro de grupo não contam (sem anúncio dentro do grupo)' );

$list  = "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>a</li></ul>\n<!-- /wp:list -->\n\n";
$out   = seisdeagosto_adsense_insert_after_paragraph_blocks( sda_p( 'P1' ) . sda_p( 'P2' ) . sda_p( 'P3' ) . $list . sda_p( 'P4' ) . sda_p( 'P5' ) . sda_p( 'P6' ), 3, $mark );
$order = array_values( array_filter( wp_list_pluck( parse_blocks( $out ), 'blockName' ) ) );
$at    = array_search( 'seisdeagosto/anuncio', $order, true );
sda_check( false !== $at && 'core/list' !== $order[ $at - 1 ] && 'core/list' !== $order[ $at + 1 ] && 5 === $at, 'nunca colado em lista (entra entre P4 e P5)' );

$empty = sda_p( 'P1' ) . sda_p( '' ) . sda_p( 'P2' ) . sda_p( 'P3' ) . sda_p( 'P4' );
sda_check( seisdeagosto_adsense_insert_after_paragraph_blocks( $empty, 3, $mark ) === $empty, 'parágrafo vazio não conta' );

// ---------------------------------------------------------------- Dentro da matéria (clássico).
$html = '<p>A</p><p>B</p><p>C</p><p>D</p><p>E</p>';
sda_check( '<p>A</p><p>B</p><p>C</p>[AD]<p>D</p><p>E</p>' === seisdeagosto_adsense_insert_after_paragraph_html( $html, 3, '[AD]' ), 'post clássico: após o 3º parágrafo' );
$html2 = '<p>A</p><p>B</p><p>C</p><figure>x</figure><p>D</p><p>E</p><p>F</p>';
sda_check( '<p>A</p><p>B</p><p>C</p><figure>x</figure><p>D</p>[AD]<p>E</p><p>F</p>' === seisdeagosto_adsense_insert_after_paragraph_html( $html2, 3, '[AD]' ), 'post clássico: pula a figura' );
sda_check( '<p>A</p><p>B</p>' === seisdeagosto_adsense_insert_after_paragraph_html( '<p>A</p><p>B</p>', 3, '[AD]' ), 'post clássico curto: sem anúncio' );

// ---------------------------------------------------------------- Entre notícias.
$items = '<ul class="wp-block-post-template">';
for ( $i = 1; $i <= 6; $i++ ) {
	$items .= '<li class="wp-block-post post-' . $i . ' post type-post">N' . $i . '</li>';
}
$items .= '</ul>';
$out    = seisdeagosto_adsense_insert_in_post_list( $items, 5, '[AD]' );
sda_check( false !== strpos( $out, 'N4</li><li class="wp-block-post sda-ad-item">[AD]</li><li class="wp-block-post post-5' ), 'anúncio antes da 5ª notícia' );
sda_check( seisdeagosto_adsense_insert_in_post_list( '<ul><li class="wp-block-post">1</li></ul>', 5, '[AD]' ) === '<ul><li class="wp-block-post">1</li></ul>', 'lista curta: sem anúncio' );

// ---------------------------------------------------------------- Topo: </header> de fora.
sda_check( false === seisdeagosto_adsense_outer_header_close( '<header class="x"><div>marca</div></header>' ), 'Topo: </header> com abertura no mesmo bloco (cabeçalho interno) é ignorado' );
sda_check( 9 === seisdeagosto_adsense_outer_header_close( '</header><div class="wrapper">' ), 'Topo: </header> que fecha o cabeçalho de fora é usado' );
$mix = '<header class="a"><p>x</p></header></header>';
sda_check( strlen( $mix ) === seisdeagosto_adsense_outer_header_close( $mix ), 'Topo: pula o par interno e usa o fechamento de fora' );
sda_check( false === seisdeagosto_adsense_outer_header_close( '<div>sem cabeçalho</div>' ), 'Topo: bloco sem </header>' );

// ---------------------------------------------------------------- ads.txt e robots.txt.
sda_check( "google.com, pub-1234567890123456, DIRECT, f08c47fec0942fa0\n" === seisdeagosto_adsense_ads_txt( $client ), 'linha do ads.txt' );
$robots = seisdeagosto_adsense_robots_txt( "User-agent: *\nDisallow: /wp-admin/\n", true );
sda_check( false !== strpos( $robots, "User-agent: Mediapartners-Google\nAllow: /" ), 'robots.txt libera o Mediapartners-Google' );
sda_check( seisdeagosto_adsense_robots_txt( "User-agent: Mediapartners-Google\nAllow: /\n", true ) === "User-agent: Mediapartners-Google\nAllow: /\n", 'robots.txt não duplica a regra' );

// ---------------------------------------------------------------- Restaura e resume.
foreach ( array_keys( get_theme_mods() ) as $mod ) {
	if ( ! array_key_exists( $mod, (array) $sda_saved_mods ) ) {
		remove_theme_mod( $mod );
	}
}
foreach ( (array) $sda_saved_mods as $mod => $value ) {
	set_theme_mod( $mod, $value );
}

echo "\n", $GLOBALS['sda_pass'], ' passaram, ', $GLOBALS['sda_fail'], " falharam\n";
