<?php
/**
 * Render do bloco "Anúncio (AdSense)".
 *
 * @package SeisdeAgosto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renderiza o bloco: um anúncio com o slot e o formato escolhidos.
 *
 * @param array $attributes Atributos do bloco.
 * @return string
 */
function seisdeagosto_render_anuncio_block( $attributes ) {
	$slot    = isset( $attributes['slot'] ) ? $attributes['slot'] : '';
	$formato = isset( $attributes['formato'] ) ? $attributes['formato'] : 'auto';
	$posicao = isset( $attributes['posicao'] ) ? sanitize_key( $attributes['posicao'] ) : 'bloco';
	return seisdeagosto_adsense_unit( $posicao ? $posicao : 'bloco', $slot, $formato );
}
