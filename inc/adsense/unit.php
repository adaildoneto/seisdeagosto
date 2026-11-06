<?php
/**
 * AdSense: HTML de um anúncio (usado pelas posições fixas e pelo bloco "Anúncio").
 *
 * @package SeisdeAgosto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formatos aceitos e os atributos do <ins> de cada um.
 *
 * @return array formato => atributos.
 */
function seisdeagosto_adsense_formats() {
	return array(
		'auto'       => array( 'data-ad-format' => 'auto' ),
		'horizontal' => array( 'data-ad-format' => 'horizontal' ),
		'vertical'   => array( 'data-ad-format' => 'vertical' ),
		'retangulo'  => array( 'data-ad-format' => 'rectangle' ),
		'materia'    => array(
			'data-ad-layout' => 'in-article',
			'data-ad-format' => 'fluid',
		),
	);
}

/**
 * HTML de um anúncio do AdSense.
 *
 * Retorna '' se os anúncios não podem aparecer nesta requisição ou se o slot for inválido.
 *
 * @param string $position Nome da posição (vira a classe sda-ad--{posição}).
 * @param string $slot     ID do bloco de anúncio (data-ad-slot).
 * @param string $format   Formato: auto, horizontal, vertical, retangulo ou materia.
 * @return string
 */
function seisdeagosto_adsense_unit( $position, $slot, $format = 'auto' ) {
	$slot = seisdeagosto_adsense_sanitize_slot( $slot );
	if ( '' === $slot || ! seisdeagosto_adsense_is_allowed() ) {
		return '';
	}

	$formats = seisdeagosto_adsense_formats();
	$format  = isset( $formats[ $format ] ) ? $format : 'auto';

	$attrs = array_merge(
		array(
			'class'                      => 'adsbygoogle',
			'style'                      => 'materia' === $format ? 'display:block; text-align:center;' : 'display:block',
			'data-ad-client'             => seisdeagosto_adsense_get_client(),
			'data-ad-slot'               => $slot,
			// Nunca mais largo que a coluna: o padrão do Google ocupa a tela toda no celular.
			'data-full-width-responsive' => 'false',
		),
		$formats[ $format ]
	);
	if ( 'materia' === $format ) {
		unset( $attrs['data-full-width-responsive'] ); // Não se aplica ao formato "No artigo".
	}

	$ins = '<ins';
	foreach ( $attrs as $name => $value ) {
		$ins .= ' ' . $name . '="' . esc_attr( $value ) . '"';
	}
	$ins .= '></ins>';

	$position = sanitize_html_class( $position );

	return '<div class="sda-ad sda-ad--' . esc_attr( $position ) . ' sda-ad--' . esc_attr( $format ) . '" data-sda-position="' . esc_attr( $position ) . '">'
		. '<span class="sda-ad__label">' . esc_html__( 'Publicidade', 'u_correio68' ) . '</span>'
		. $ins
		. '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>'
		. '</div>';
}
