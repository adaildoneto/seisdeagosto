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
 * Rede de segurança do jQuery no rodapé.
 *
 * Se algum plugin registrar no <head> um script que depende do jQuery, o
 * navegador tentaria executá-lo antes da biblioteca. Nesse caso, o jQuery
 * volta para o <head> em vez de quebrar a página.
 */
function u68_jquery_group_guard() {
	if ( is_admin() ) {
		return;
	}

	$scripts = wp_scripts();

	if ( ! $scripts instanceof WP_Scripts || ! $scripts->query( 'jquery', 'enqueued' ) ) {
		return;
	}

	foreach ( (array) $scripts->registered as $handle => $item ) {
		if ( $scripts->get_data( $handle, 'group' ) ) {
			continue;
		}
		if ( empty( $item->deps ) || ! in_array( 'jquery', (array) $item->deps, true ) ) {
			continue;
		}

		// Existe dependente no <head>: mantém a biblioteca onde está.
		foreach ( array( 'jquery', 'jquery-core', 'jquery-migrate' ) as $jquery_handle ) {
			$scripts->add_data( $jquery_handle, 'group', 0 );
		}

		return;
	}
}
add_action( 'wp_print_scripts', 'u68_jquery_group_guard', 1 );

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
 * @param string $content Conteúdo do post.
 * @return string
 */
function u68_youtube_facade( $content ) {
	if ( is_admin() || is_feed() || is_customize_preview() || empty( $content ) ) {
		return $content;
	}

	$pattern = '#<iframe\b[^>]*\bsrc=(["\'])(?:https?:)?//(?:www\.)?(?:youtube(?:-nocookie)?\.com/embed/|youtu\.be/)([A-Za-z0-9_-]{6,})[^"\']*\1[^>]*>\s*</iframe>#i';

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
		$content
	);
}
add_filter( 'the_content', 'u68_youtube_facade', 99 );

/**
 * Adiciona loading="lazy" a iframes que restarem no conteúdo.
 *
 * @param string $content Conteúdo.
 * @return string
 */
function u68_lazy_iframes( $content ) {
	if ( is_admin() || is_feed() || is_customize_preview() || empty( $content ) ) {
		return $content;
	}

	return preg_replace_callback(
		'#<iframe\b([^>]*)>#i',
		function ( $matches ) {
			if ( preg_match( '#\bloading=#i', $matches[1] ) ) {
				return $matches[0];
			}
			return '<iframe' . $matches[1] . ' loading="lazy">';
		},
		$content
	);
}
add_filter( 'the_content', 'u68_lazy_iframes', 98 );

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
		window.addEventListener('load', function () {
			setTimeout(function () { loadGroup('analytics'); }, 2000);
			setTimeout(loadAds, 6000);
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'u68_print_delayed_script_loader', 99 );

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
