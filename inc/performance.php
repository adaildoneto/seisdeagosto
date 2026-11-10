<?php
/**
 * Otimizações de performance do front-end.
 *
 * - Fontes locais em WOFF2 (ver dev/tools/build-fonts.py).
 * - jQuery/jQuery Migrate no rodapé (deixa de bloquear a renderização).
 * - Fachada leve para vídeos do YouTube (o player só carrega no clique).
 * - Adiamento de scripts de terceiros (AdSense, GTM e banner WordBlaster)
 *   até a primeira interação, com fallback por tempo.
 * - Qualidade das imagens servidas pelo CDN do Jetpack.
 *
 * @package SeisdeAgosto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Move o jQuery para o rodapé.
 *
 * O tema carrega todos os seus scripts no rodapé, então o jQuery não precisa
 * mais bloquear a renderização no <head>.
 *
 * @param WP_Scripts $scripts Fila de scripts do WordPress.
 */
function u68_move_jquery_to_footer( $scripts ) {
	if ( is_admin() ) {
		return;
	}

	foreach ( array( 'jquery', 'jquery-core', 'jquery-migrate' ) as $handle ) {
		$scripts->add_data( $handle, 'group', 1 );
	}
}
add_action( 'wp_default_scripts', 'u68_move_jquery_to_footer' );

/**
 * Coloca a fonte móvel do sistema no próprio HTML crítico.
 *
 * O Jetpack Boost carrega a folha completa de forma assíncrona. Manter esta
 * regra apenas no main.css faria o navegador iniciar Lato/Open Sans antes de
 * descobrir a substituição móvel.
 */
function u68_print_mobile_system_fonts() {
	if ( is_admin() || ( function_exists( 'u_seisbarra8_is_amp' ) && u_seisbarra8_is_amp() ) ) {
		return;
	}
	?>
	<style id="u68-mobile-system-fonts">
	@media (max-width:767.98px){body,body *:not(.fa):not(.dashicons):not([class^="icon-"]):not([class*=" icon-"]),body h1,body h2,body h3,body h4,body h5,body h6,body .h1,body .h2,body .h3,body .h4,body .h5,body .h6{font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif!important}}
	</style>
	<?php
}
add_action( 'wp_head', 'u68_print_mobile_system_fonts', 99 );

/**
 * Coloca no rodapé os scripts que dependem do jQuery.
 *
 * Assim a biblioteca e seus dependentes continuam na mesma leva (e na ordem
 * certa), sem precisar reservar o <head> para o jQuery.
 */
function u68_move_jquery_dependents_to_footer() {
	if ( is_admin() ) {
		return;
	}

	$scripts = wp_scripts();

	if ( ! $scripts instanceof WP_Scripts ) {
		return;
	}

	$jquery_handles = array( 'jquery', 'jquery-core', 'jquery-migrate' );
	$footer         = $jquery_handles;

	// Fecha a lista com dependentes indiretos (ex.: script que depende do
	// Bootstrap, que depende do jQuery), para não quebrar a ordem de execução.
	do {
		$added = false;

		foreach ( (array) $scripts->queue as $handle ) {
			if ( in_array( $handle, $footer, true ) ) {
				continue;
			}

			$item = isset( $scripts->registered[ $handle ] ) ? $scripts->registered[ $handle ] : null;

			if ( ! $item || empty( $item->deps ) ) {
				continue;
			}

			if ( ! array_intersect( (array) $item->deps, $footer ) ) {
				continue;
			}

			$footer[] = $handle;
			$added    = true;
		}
	} while ( $added );

	foreach ( $footer as $handle ) {
		if ( ! $scripts->query( $handle, 'registered' ) ) {
			continue;
		}

		if ( $scripts->get_data( $handle, 'group' ) ) {
			continue;
		}

		// Garante o rodapé também para a própria biblioteca: o hook
		// `wp_default_scripts` pode já ter disparado antes do tema carregar
		// (plugin que chama wp_scripts() cedo) e o grupo nunca seria aplicado.
		$scripts->add_data( $handle, 'group', 1 );
	}
}
add_action( 'wp_enqueue_scripts', 'u68_move_jquery_dependents_to_footer', 999 );
add_action( 'wp_print_scripts', 'u68_move_jquery_dependents_to_footer', 1 );
add_action( 'wp_footer', 'u68_move_jquery_dependents_to_footer', 1 );

/**
 * Remove o script de emojis do WordPress.
 *
 * Navegadores modernos renderizam emojis nativamente; o script só adiciona
 * bytes e uma requisição.
 */
function u68_disable_wp_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'u68_disable_wp_emoji' );

/**
 * Reduz a qualidade das imagens do CDN do Jetpack (Photon).
 *
 * Fotos de notícias não precisam de qualidade máxima; 82 mantém boa nitidez
 * com arquivos bem menores.
 *
 * @param array $args Argumentos do CDN.
 * @return array
 */
function u68_image_cdn_quality( $args ) {
	if ( is_admin() || ! is_array( $args ) ) {
		return $args;
	}

	if ( ! isset( $args['quality'] ) ) {
		$args['quality'] = 82;
	}

	return $args;
}
add_filter( 'jetpack_photon_pre_args', 'u68_image_cdn_quality', 10, 1 );
add_filter( 'image_cdn_pre_args', 'u68_image_cdn_quality', 10, 1 );

/**
 * Ajusta a imagem do logo do site.
 *
 * O CSS do tema exibe o logo em ~65px no celular e ~200px no desktop, mas o
 * WordPress declara `sizes="100vw"` e o navegador baixa a versão de 521px
 * (128 KB). Com `sizes` correto o celular recebe a versão de 300px.
 *
 * @param array $attr       Atributos da imagem.
 * @param int   $attachment ID do anexo.
 * @param mixed $size       Tamanho solicitado.
 * @return array
 */
function u68_custom_logo_attributes( $attr, $attachment, $size ) {
	if ( empty( $attr['class'] ) || false === strpos( $attr['class'], 'custom-logo' ) ) {
		return $attr;
	}

	$attr['sizes']        = '(max-width: 480px) 70px, (max-width: 991px) 150px, 210px';
	$attr['fetchpriority'] = 'low';
	$attr['decoding']      = 'async';

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'u68_custom_logo_attributes', 10, 3 );

/**
 * Garante os atributos do logo no HTML final.
 *
 * O WordPress pode adicionar fetchpriority="high" depois do filtro de
 * atributos, e o filtro legado do tema troca a classe custom-logo. Esta
 * segunda passagem evita que o logo concorra com a imagem LCP.
 *
 * @param string $html HTML do logo.
 * @return string
 */
function u68_optimize_custom_logo_markup( $html ) {
	if ( ! is_string( $html ) || false === stripos( $html, '<img' ) ) {
		return $html;
	}

	$html = preg_replace( '/\s+fetchpriority=(?:"[^"]*"|\'[^\']*\')/i', '', $html );

	if ( preg_match( '/\ssizes=/i', $html ) ) {
		$html = preg_replace( '/\ssizes=(?:"[^"]*"|\'[^\']*\')/i', ' sizes="(max-width: 480px) 70px, (max-width: 991px) 150px, 210px"', $html, 1 );
	} else {
		$html = preg_replace( '/<img\b/i', '<img sizes="(max-width: 480px) 70px, (max-width: 991px) 150px, 210px"', $html, 1 );
	}

	return preg_replace( '/<img\b/i', '<img fetchpriority="low"', $html, 1 );
}
add_filter( 'get_custom_logo', 'u68_optimize_custom_logo_markup', 99 );

/**
 * Pré-conecta ao domínio das miniaturas do YouTube.
 *
 * Só um dns-prefetch: as miniaturas são lazy e ficam abaixo da dobra, então
 * abrir a conexão antes da hora desperdiçaria handshake (DNS/TCP/TLS).
 *
 * @param array  $urls     Recursos já listados.
 * @param string $relation Tipo de relação (preconnect/dns-prefetch).
 * @return array
 */
function u68_resource_hints( $urls, $relation ) {
	if ( 'dns-prefetch' === $relation ) {
		$urls[] = 'https://i.ytimg.com';
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'u68_resource_hints', 10, 2 );

/**
 * Substitui iframes do YouTube por uma fachada que só carrega o player no clique.
 *
 * Roda no HTML final (buffer de saída) e não em `the_content`: assim também
 * alcança vídeos que vêm de blocos reutilizados, template parts e widgets,
 * que não passam pelo filtro de conteúdo do post.
 *
 * @param string $html HTML da página.
 * @return string
 */
function u68_youtube_facade( $html ) {
	if ( empty( $html ) ) {
		return $html;
	}

	$pattern = '#<iframe\b[^>]*\bsrc=(["\'])(?:https?:)?//(?:www\.)?(?:youtube(?:-nocookie)?\.com/(?:embed|shorts|live)/|youtu\.be/)([A-Za-z0-9_-]{6,})[^"\']*\1[^>]*>\s*</iframe>#i';

	return preg_replace_callback(
		$pattern,
		function ( $matches ) {
			$video_id = $matches[2];
			$title    = 'YouTube';

			if ( preg_match( '#\btitle=(["\'])(.*?)\1#i', $matches[0], $title_match ) ) {
				$title = $title_match[2];
			}

			$thumb = 'https://i.ytimg.com/vi/' . rawurlencode( $video_id ) . '/hqdefault.jpg';

			return '<button type="button" class="u68-video-facade" data-u68-yt="' . esc_attr( $video_id ) . '"'
				. ' data-u68-yt-title="' . esc_attr( $title ) . '"'
				. ' aria-label="' . esc_attr( sprintf( /* translators: %s: video title */ __( 'Reproduzir vídeo: %s', 'u_correio68' ), $title ) ) . '">'
				. '<img src="' . esc_url( $thumb ) . '" alt="" width="480" height="360" loading="lazy" decoding="async" fetchpriority="low">'
				. '<span class="u68-video-play" aria-hidden="true"></span>'
				. '</button>';
		},
		$html
	);
}

/**
 * Adiciona loading="lazy" a iframes que restarem no HTML final.
 *
 * @param string $html HTML da página.
 * @return string
 */
function u68_lazy_iframes( $html ) {
	if ( empty( $html ) ) {
		return $html;
	}

	return preg_replace_callback(
		'#<iframe\b([^>]*)>#i',
		function ( $matches ) {
			if ( preg_match( '#\bloading=#i', $matches[1] ) ) {
				return $matches[0];
			}
			return '<iframe' . $matches[1] . ' loading="lazy">';
		},
		$html
	);
}

/**
 * Adia imagens nativamente lazy até elas se aproximarem da área visível.
 *
 * O Chrome começa a baixar imagens loading="lazy" ainda muito longe da dobra.
 * Na home isso incluía miniaturas pesadas durante o carregamento do LCP.
 *
 * @param string $html HTML da página.
 * @return string
 */
function u68_defer_lazy_images( $html ) {
	if ( empty( $html ) ) {
		return $html;
	}

	return preg_replace_callback(
		'#<img\b[^>]*>#i',
		function ( $matches ) {
			$tag = $matches[0];

			if (
				! preg_match( '#\bloading=(["\'])lazy\1#i', $tag )
				|| false !== stripos( $tag, 'data-u68-src=' )
				|| false !== stripos( $tag, 'data-jp-lcp-optimized' )
				|| preg_match( '#\bfetchpriority=(["\'])high\1#i', $tag )
			) {
				return $tag;
			}

			$tag = preg_replace( '#\ssrcset=(["\'])(.*?)\1#i', ' data-u68-srcset=$1$2$1', $tag, 1 );
			$tag = preg_replace(
				'#\ssrc=(["\'])(.*?)\1#i',
				' data-u68-src=$1$2$1 src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="',
				$tag,
				1
			);

			return $tag;
		},
		$html
	);
}

/**
 * Inicia o buffer de saída para reescrever scripts de terceiros.
 */
function u68_start_output_buffer() {
	if ( is_admin() || is_feed() || is_robots() || wp_doing_ajax() ) {
		return;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}
	if ( is_customize_preview() ) {
		return;
	}
	if ( function_exists( 'u_seisbarra8_is_amp' ) && u_seisbarra8_is_amp() ) {
		return;
	}

	ob_start( 'u68_rewrite_third_party_markup' );
}
add_action( 'template_redirect', 'u68_start_output_buffer', 1 );

/**
 * Remove/adia marcações de terceiros no HTML final.
 *
 * @param string $html HTML da página.
 * @return string
 */
function u68_rewrite_third_party_markup( $html ) {
	if ( ! is_string( $html ) || '' === $html ) {
		return $html;
	}

	// Fachada de vídeo + lazy em iframes restantes (cobre blocos reutilizados
	// e template parts, que não passam por `the_content`).
	$html = u68_youtube_facade( $html );
	$html = u68_lazy_iframes( $html );
	$html = u68_defer_lazy_images( $html );

	// Banner Lottie do WordBlaster: remove a biblioteca externa (1,9 s de main
	// thread no mobile) e a inicialização inline; o tema assume o banner via
	// assets/js/lottie-banner.js, carregado só quando ele entra na viewport.
	$html = preg_replace(
		'#<script[^>]*\bsrc=["\']https?://wordblaster\.fun/[^"\']*lottie[^"\']*\.js["\'][^>]*>\s*</script>#i',
		'',
		$html
	);

	$html = preg_replace_callback(
		'#<script\b([^>]*)>([\s\S]*?)</script>#i',
		function ( $matches ) {
			if ( false !== stripos( $matches[1], 'src=' ) ) {
				return $matches[0];
			}
			if ( false === strpos( $matches[2], 'lottie.loadAnimation' ) ) {
				return $matches[0];
			}
			// Só remove animações de banners que o tema sabe reinicializar.
			if ( false === strpos( $matches[2], 'wb-banner-' ) ) {
				return $matches[0];
			}
			return '';
		},
		$html
	);

	$html = preg_replace_callback(
		'#<div([^>]*\bid=["\'](wb-banner-[^"\']+)["\'][^>]*)>#i',
		function ( $matches ) {
			if ( false !== stripos( $matches[1], 'data-lottie-src' ) ) {
				return $matches[0];
			}

			$id   = $matches[2];
			$base = 'https://wordblaster.fun/imprensa/arquivos/banners/';
			$src  = ( 'wb-banner-1280x200' === $id ) ? 'wb-banner-1280x200.json' : $base . $id . '.json';

			return '<div' . $matches[1] . ' data-lottie-src="' . esc_attr( $src ) . '">';
		},
		$html
	);

	// Adia o script do AdSense e o gtag/GTM.
	$html = preg_replace_callback(
		'#<script\b([^>]*\bsrc=["\']([^"\']+)["\'][^>]*)>\s*</script>#i',
		function ( $matches ) {
			$src = html_entity_decode( $matches[2], ENT_QUOTES, 'UTF-8' );

			if ( false !== strpos( $src, 'pagead2.googlesyndication.com/pagead/js/adsbygoogle.js' ) ) {
				$when = 'ads';
			} elseif ( false !== strpos( $src, 'googletagmanager.com/gtag/js' ) ) {
				$when = 'analytics';
			} else {
				return $matches[0];
			}

			$crossorigin = ( false !== stripos( $matches[1], 'crossorigin' ) ) ? ' crossorigin="anonymous"' : '';

			return '<script type="text/plain" data-u68-delay-src="' . esc_url( $src ) . '" data-u68-when="' . esc_attr( $when ) . '"' . $crossorigin . '></script>';
		},
		$html
	);

	return $html;
}

/**
 * Script que carrega os recursos adiados.
 */
function u68_print_delayed_script_loader() {
	if ( is_admin() || is_feed() || is_customize_preview() ) {
		return;
	}
	?>
	<script>
	(function () {
		var pending = Array.prototype.slice.call(document.querySelectorAll('script[type="text/plain"][data-u68-delay-src]'));
		if (!pending.length) { return; }
		var loaded = {};
		function loadGroup(group) {
			if (loaded[group]) { return; }
			loaded[group] = true;
			pending.forEach(function (placeholder) {
				if (placeholder.getAttribute('data-u68-when') !== group) { return; }
				var script = document.createElement('script');
				script.src = placeholder.getAttribute('data-u68-delay-src');
				script.async = true;
				if (placeholder.hasAttribute('crossorigin')) { script.crossOrigin = 'anonymous'; }
				document.head.appendChild(script);
			});
		}
		function loadAds() { loadGroup('ads'); }
		['scroll', 'pointerdown', 'touchstart', 'keydown', 'wheel'].forEach(function (eventName) {
			window.addEventListener(eventName, loadAds, { passive: true, once: true });
		});
		function loadAnalytics() { loadGroup('analytics'); }
		['scroll', 'pointerdown', 'touchstart', 'keydown', 'wheel'].forEach(function (eventName) {
			window.addEventListener(eventName, loadAnalytics, { passive: true, once: true });
		});
		window.addEventListener('load', function () {
			setTimeout(loadAnalytics, 15000);
			setTimeout(loadAds, 30000);
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'u68_print_delayed_script_loader', 99 );

/**
 * Restaura imagens lazy quando elas se aproximam da viewport.
 */
function u68_print_lazy_image_loader() {
	if ( is_admin() || is_feed() || is_customize_preview() ) {
		return;
	}
	?>
	<script>
	(function () {
		var images = Array.prototype.slice.call(document.querySelectorAll('img[data-u68-src]'));
		if (!images.length) { return; }
		function load(image) {
			if (!image.hasAttribute('data-u68-src')) { return; }
			if (image.hasAttribute('data-u68-srcset')) {
				image.srcset = image.getAttribute('data-u68-srcset');
				image.removeAttribute('data-u68-srcset');
			}
			image.src = image.getAttribute('data-u68-src');
			image.removeAttribute('data-u68-src');
		}
		if (!('IntersectionObserver' in window)) {
			images.forEach(load);
			return;
		}
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) { return; }
				observer.unobserve(entry.target);
				load(entry.target);
			});
		}, { rootMargin: '400px 0px' });
		images.forEach(function (image) { observer.observe(image); });
		setTimeout(function () { images.forEach(load); observer.disconnect(); }, 10000);
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'u68_print_lazy_image_loader', 97 );

/**
 * Fachada de vídeo: troca a imagem pelo iframe do YouTube no clique.
 */
function u68_print_video_facade_script() {
	if ( is_admin() || is_feed() || is_customize_preview() ) {
		return;
	}
	?>
	<script>
	(function () {
		function activate(facade) {
			var id = facade.getAttribute('data-u68-yt');
			if (!id) { return; }
			var iframe = document.createElement('iframe');
			iframe.src = 'https://www.youtube.com/embed/' + id + '?autoplay=1&feature=oembed';
			iframe.title = facade.getAttribute('data-u68-yt-title') || 'YouTube';
			iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
			iframe.setAttribute('allowfullscreen', '');
			iframe.className = 'u68-video-iframe';
			facade.parentNode.replaceChild(iframe, facade);
		}
		document.addEventListener('click', function (event) {
			var facade = event.target.closest('.u68-video-facade');
			if (!facade) { return; }
			event.preventDefault();
			activate(facade);
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'u68_print_video_facade_script', 98 );
