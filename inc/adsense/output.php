<?php
/**
 * AdSense: regras de exibição, script, verificação, ads.txt e robots.txt.
 *
 * @package SeisdeAgosto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Anúncios podem aparecer nesta requisição?
 *
 * Falso sem ID válido, com "Ativar anúncios" desligado, para usuários logados,
 * em Publicações Legais, 404, busca sem resultados, preview, feed, admin, AJAX,
 * REST e AMP.
 *
 * @return bool
 */
function seisdeagosto_adsense_is_allowed() {
	$allowed = '' !== seisdeagosto_adsense_get_client() && seisdeagosto_adsense_is_enabled();

	if ( $allowed ) {
		if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_feed() ) {
			$allowed = false;
		} elseif ( is_user_logged_in() || is_customize_preview() ) {
			$allowed = false;
		} elseif ( is_404() || is_singular( 'edital' ) || is_post_type_archive( 'edital' ) ) {
			$allowed = false;
		} elseif ( is_search() && empty( $GLOBALS['wp_query']->post_count ) ) {
			// Não usar have_posts(): dentro do loop, no último post, ele dispara loop_end e rebobina.
			$allowed = false;
		} elseif ( function_exists( 'u_seisbarra8_is_amp' ) && u_seisbarra8_is_amp() ) {
			$allowed = false;
		}
	}

	/**
	 * Permite exceções às regras de exibição dos anúncios.
	 *
	 * @param bool $allowed Se os anúncios podem aparecer.
	 */
	return (bool) apply_filters( 'seisdeagosto_adsense_is_allowed', $allowed );
}

/**
 * Outro plugin (Site Kit by Google) já insere o script do AdSense?
 *
 * @return bool
 */
function seisdeagosto_adsense_other_loader() {
	$other   = false;
	$modules = get_option( 'googlesitekit_active_modules' );
	if ( is_array( $modules ) && in_array( 'adsense', $modules, true ) ) {
		$settings = get_option( 'googlesitekit_adsense_settings' );
		$other    = is_array( $settings ) && ! empty( $settings['useSnippet'] );
	}

	/**
	 * Força o tema a não imprimir o script do AdSense (outro plugin já o carrega).
	 *
	 * @param bool $other Se outro plugin carrega o script.
	 */
	return (bool) apply_filters( 'seisdeagosto_adsense_skip_script', $other );
}

/**
 * Meta de verificação e script do AdSense no <head>.
 */
function seisdeagosto_adsense_head() {
	$client = seisdeagosto_adsense_get_client();
	if ( '' === $client ) {
		return;
	}

	// Verificação do site pelo Google: sempre que houver ID (inclusive para logados).
	echo '<meta name="google-adsense-account" content="' . esc_attr( $client ) . '">' . "\n";

	if ( ! seisdeagosto_adsense_is_allowed() || seisdeagosto_adsense_other_loader() ) {
		return;
	}

	$src = add_query_arg( 'client', $client, 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js' );
	// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- O AdSense exige o script exatamente neste formato (async + crossorigin).
	echo '<script async src="' . esc_url( $src ) . '" crossorigin="anonymous"></script>' . "\n";
}
add_action( 'wp_head', 'seisdeagosto_adsense_head', 1 );

/**
 * CSS dos anúncios, só quando eles podem aparecer.
 */
function seisdeagosto_adsense_enqueue_style() {
	if ( ! seisdeagosto_adsense_is_allowed() ) {
		return;
	}
	wp_enqueue_style(
		'seisdeagosto-adsense',
		get_template_directory_uri() . '/css/adsense.css',
		array(),
		function_exists( 'u_seisbarra8_asset_version' ) ? u_seisbarra8_asset_version( 'css/adsense.css' ) : null
	);
}
add_action( 'wp_enqueue_scripts', 'seisdeagosto_adsense_enqueue_style', 20 );

/**
 * O WordPress está na raiz do domínio (necessário para servir /ads.txt)?
 *
 * @return bool
 */
function seisdeagosto_adsense_site_at_root() {
	$path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	return ! $path || '/' === $path;
}

/**
 * Conteúdo do ads.txt para o ID configurado.
 *
 * @param string $client ID ca-pub-….
 * @return string
 */
function seisdeagosto_adsense_ads_txt( $client ) {
	return 'google.com, ' . substr( $client, 3 ) . ', DIRECT, f08c47fec0942fa0' . "\n";
}

/**
 * Responde /ads.txt (um arquivo ads.txt físico no servidor tem prioridade).
 */
function seisdeagosto_adsense_serve_ads_txt() {
	if ( empty( $_SERVER['REQUEST_URI'] ) || ! seisdeagosto_adsense_site_at_root() ) {
		return;
	}
	$path = wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Só comparado com uma string fixa.
	if ( '/ads.txt' !== $path ) {
		return;
	}
	$client = seisdeagosto_adsense_get_client();
	if ( '' === $client ) {
		return; // Sem ID: segue o fluxo normal (404).
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	echo esc_html( seisdeagosto_adsense_ads_txt( $client ) );
	exit;
}
add_action( 'init', 'seisdeagosto_adsense_serve_ads_txt', 1 );

/**
 * Libera o robô do AdSense (Mediapartners-Google) no robots.txt virtual.
 *
 * @param string $output    Conteúdo do robots.txt.
 * @param bool   $is_public Site visível para buscadores.
 * @return string
 */
function seisdeagosto_adsense_robots_txt( $output, $is_public ) {
	if ( '' === seisdeagosto_adsense_get_client() || false !== stripos( $output, 'Mediapartners-Google' ) ) {
		return $output;
	}
	return rtrim( $output ) . "\n\n# Google AdSense (tema SeisdeAgosto)\nUser-agent: Mediapartners-Google\nAllow: /\n";
}
add_filter( 'robots_txt', 'seisdeagosto_adsense_robots_txt', 20, 2 );

/**
 * Aviso no admin quando o /ads.txt não pode ser servido pelo tema.
 */
function seisdeagosto_adsense_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) || '' === seisdeagosto_adsense_get_client() || seisdeagosto_adsense_site_at_root() ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>AdSense:</strong> ' . esc_html__( 'o WordPress não está na raiz do domínio, então o tema não consegue servir o /ads.txt. Crie o arquivo ads.txt na raiz do domínio com a linha abaixo:', 'u_correio68' ) . '</p><p><code>' . esc_html( trim( seisdeagosto_adsense_ads_txt( seisdeagosto_adsense_get_client() ) ) ) . '</code></p></div>';
}
add_action( 'admin_notices', 'seisdeagosto_adsense_admin_notice' );
