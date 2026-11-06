<?php
/**
 * AdSense: registro do bloco "Anúncio (AdSense)".
 *
 * @package SeisdeAgosto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/blocks/anuncio/render.php';

/**
 * Registra o script do editor e o bloco.
 */
function seisdeagosto_adsense_register_block() {
	wp_register_script(
		'seisdeagosto-anuncio-editor',
		get_template_directory_uri() . '/blocks/anuncio/edit.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		function_exists( 'u_seisbarra8_asset_version' ) ? u_seisbarra8_asset_version( 'blocks/anuncio/edit.js' ) : null,
		true
	);
	register_block_type(
		get_template_directory() . '/blocks/anuncio',
		array( 'render_callback' => 'seisdeagosto_render_anuncio_block' )
	);
}
add_action( 'init', 'seisdeagosto_adsense_register_block' );
