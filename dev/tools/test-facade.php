<?php
define( 'ABSPATH', __DIR__ );
function add_action() {}
function add_filter() {}
function __( $t ) { return $t; }
function esc_attr( $t ) { return htmlspecialchars( $t, ENT_QUOTES ); }
function esc_url( $t ) { return $t; }
function is_admin() { return false; }
require dirname( __DIR__, 2 ) . '/inc/performance.php';

$iframe = '<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube"><div class="wp-block-embed__wrapper">' . "\n" .
	'<iframe title="Live Bora Porongar!" width="730" height="411" src="https://www.youtube.com/embed/e3grHsrR8Yo?feature=oembed" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>' . "\n" .
	'</div></figure>';

$out = u68_youtube_facade( $iframe );
echo ( false === strpos( $out, '<iframe' ) ? "OK: iframe substituido\n" : "FALHOU: iframe continua\n" );
echo substr( $out, 0, 220 ) . "\n\n";

$shorts = '<iframe src="https://www.youtube.com/shorts/abc123XYZ" loading="lazy"></iframe>';
$out2   = u68_youtube_facade( $shorts );
echo ( false === strpos( $out2, '<iframe' ) ? "OK: shorts substituido\n" : "shorts NAO substituido\n" );

$no_cookie = '<iframe src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ" title="teste"></iframe>';
$out3      = u68_youtube_facade( $no_cookie );
echo ( false === strpos( $out3, '<iframe' ) ? "OK: nocookie substituido\n" : "nocookie NAO substituido\n" );

$logo = '<a class="navbar-brand custom-logo-link"><img width="521" class="img-fluid" fetchpriority="high" sizes="100vw" src="logo.png"></a>';
$logo = u68_optimize_custom_logo_markup( $logo );
echo ( false !== strpos( $logo, 'fetchpriority="low"' ) && false === strpos( $logo, 'fetchpriority="high"' ) && false === strpos( $logo, 'sizes="100vw"' ) ? "OK: logo otimizado\n" : "FALHOU: logo nao otimizado\n" );

$lazy = '<img src="foto.jpg" srcset="foto-300.jpg 300w, foto-600.jpg 600w" loading="lazy" alt="">';
$lazy = u68_defer_lazy_images( $lazy );
echo ( false !== strpos( $lazy, 'data-u68-src="foto.jpg"' ) && false !== strpos( $lazy, 'data-u68-srcset="foto-300.jpg 300w, foto-600.jpg 600w"' ) ? "OK: imagem lazy adiada\n" : "FALHOU: imagem lazy nao adiada\n" );
