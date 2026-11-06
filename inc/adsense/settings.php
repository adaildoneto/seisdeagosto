<?php
/**
 * AdSense: configurações no Personalizador e leitura das opções.
 *
 * Especificação: docs/superpowers/specs/2026-11-06-adsense-design.md
 *
 * @package SeisdeAgosto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Posições fixas oferecidas pelo tema.
 *
 * @return array posição => array( 'label' => rótulo, 'formato' => formato do anúncio ).
 */
function seisdeagosto_adsense_positions() {
	return array(
		'topo'    => array(
			'label'   => __( 'Topo (abaixo do cabeçalho)', 'u_correio68' ),
			'formato' => 'horizontal',
		),
		'materia' => array(
			'label'   => __( 'Dentro da matéria (entre parágrafos)', 'u_correio68' ),
			'formato' => 'materia',
		),
		'fim'     => array(
			'label'   => __( 'Fim da matéria', 'u_correio68' ),
			'formato' => 'auto',
		),
		'lateral' => array(
			'label'   => __( 'Barra lateral (posts)', 'u_correio68' ),
			'formato' => 'vertical',
		),
		'lista'   => array(
			'label'   => __( 'Entre notícias (categorias, tags, busca)', 'u_correio68' ),
			'formato' => 'auto',
		),
		'capa'    => array(
			'label'   => __( 'Capa (após os destaques)', 'u_correio68' ),
			'formato' => 'horizontal',
		),
	);
}

/**
 * Extrai o ID de editor (ca-pub-…) da tag do AdSense ou do próprio ID.
 *
 * Aceita a tag <script> completa, a URL do script, "ca-pub-…" ou "pub-…".
 *
 * @param mixed $raw Valor digitado.
 * @return string ID no formato ca-pub-XXXXXXXXXXXXXXXX, ou '' se não houver um válido.
 */
function seisdeagosto_adsense_extract_client( $raw ) {
	if ( ! is_string( $raw ) || '' === trim( $raw ) ) {
		return '';
	}
	if ( preg_match( '/(?<![a-z0-9-])(?:ca-)?pub-(\d{10,20})(?!\d)/i', $raw, $m ) ) {
		return 'ca-pub-' . $m[1];
	}
	return '';
}

/**
 * Valida o ID de um bloco de anúncio (data-ad-slot): só dígitos, 6 a 20.
 *
 * @param mixed $raw Valor digitado.
 * @return string ID ou ''.
 */
function seisdeagosto_adsense_sanitize_slot( $raw ) {
	$raw = is_scalar( $raw ) ? trim( (string) $raw ) : '';
	return preg_match( '/^\d{6,20}$/', $raw ) ? $raw : '';
}

/**
 * Parágrafo após o qual entra o anúncio da matéria (2 a 10).
 *
 * @param mixed $raw Valor digitado.
 * @return int
 */
function seisdeagosto_adsense_sanitize_paragraph( $raw ) {
	$n = absint( $raw );
	return ( $n >= 2 && $n <= 10 ) ? $n : 3;
}

/**
 * ID de editor configurado (ca-pub-…), ou ''.
 *
 * @return string
 */
function seisdeagosto_adsense_get_client() {
	return seisdeagosto_adsense_extract_client( (string) get_theme_mod( 'seisdeagosto_adsense_client', '' ) );
}

/**
 * "Ativar anúncios" ligado.
 *
 * @return bool
 */
function seisdeagosto_adsense_is_enabled() {
	return (bool) get_theme_mod( 'seisdeagosto_adsense_enabled', true );
}

/**
 * ID do bloco de anúncio de uma posição fixa, ou ''.
 *
 * @param string $position Chave de seisdeagosto_adsense_positions().
 * @return string
 */
function seisdeagosto_adsense_get_slot( $position ) {
	return seisdeagosto_adsense_sanitize_slot( get_theme_mod( 'seisdeagosto_adsense_slot_' . sanitize_key( $position ), '' ) );
}

/**
 * Parágrafo configurado para o anúncio da matéria.
 *
 * @return int
 */
function seisdeagosto_adsense_get_paragraph() {
	return seisdeagosto_adsense_sanitize_paragraph( get_theme_mod( 'seisdeagosto_adsense_materia_paragrafo', 3 ) );
}

/**
 * Parágrafo do 2º anúncio nas matérias longas (6 a 30).
 *
 * @param mixed $raw Valor digitado.
 * @return int
 */
function seisdeagosto_adsense_sanitize_paragraph2( $raw ) {
	$n = absint( $raw );
	return ( $n >= 6 && $n <= 30 ) ? $n : 10;
}

/**
 * 2º anúncio nas matérias longas ligado.
 *
 * @return bool
 */
function seisdeagosto_adsense_materia2_enabled() {
	return (bool) get_theme_mod( 'seisdeagosto_adsense_materia2', true );
}

/**
 * Parágrafo configurado para o 2º anúncio nas matérias longas.
 *
 * @return int
 */
function seisdeagosto_adsense_get_paragraph2() {
	return seisdeagosto_adsense_sanitize_paragraph2( get_theme_mod( 'seisdeagosto_adsense_materia2_paragrafo', 10 ) );
}

/**
 * Seção "📢 AdSense" no Personalizador.
 *
 * @param WP_Customize_Manager $wp_customize Personalizador.
 */
function seisdeagosto_adsense_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'seisdeagosto_adsense',
		array(
			'title'       => __( '📢 AdSense', 'u_correio68' ),
			'priority'    => 160,
			'description' => __( 'Cole a tag do Google AdSense (AdSense → Sites → "Obter código") ou só o ID ca-pub-…. O tema carrega o script, gera o ads.txt e libera o robô do AdSense no robots.txt. Os Anúncios automáticos são ligados no painel do AdSense; lá, prefira a âncora embaixo, porque o cabeçalho do tema é fixo no topo. Usuários logados e Publicações Legais não veem anúncios.', 'u_correio68' ),
		)
	);

	$wp_customize->add_setting(
		'seisdeagosto_adsense_client',
		array(
			'default'           => '',
			'sanitize_callback' => 'seisdeagosto_adsense_extract_client',
			'validate_callback' => function ( $validity, $value ) {
				if ( is_string( $value ) && '' !== trim( $value ) && '' === seisdeagosto_adsense_extract_client( $value ) ) {
					$validity->add( 'invalid_client', __( 'Não encontrei um ID de editor (ca-pub- seguido de números) nesse texto.', 'u_correio68' ) );
				}
				return $validity;
			},
		)
	);
	$wp_customize->add_control(
		'seisdeagosto_adsense_client',
		array(
			'label'       => __( 'Tag ou ID do AdSense', 'u_correio68' ),
			'description' => __( 'Ex.: ca-pub-1234567890123456 ou a tag <script> inteira.', 'u_correio68' ),
			'section'     => 'seisdeagosto_adsense',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'seisdeagosto_adsense_enabled',
		array(
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'seisdeagosto_adsense_enabled',
		array(
			'label'       => __( 'Ativar anúncios', 'u_correio68' ),
			'description' => __( 'Desligue para tirar todos os anúncios sem perder as configurações.', 'u_correio68' ),
			'section'     => 'seisdeagosto_adsense',
			'type'        => 'checkbox',
		)
	);

	foreach ( seisdeagosto_adsense_positions() as $key => $position ) {
		$wp_customize->add_setting(
			'seisdeagosto_adsense_slot_' . $key,
			array(
				'default'           => '',
				'sanitize_callback' => 'seisdeagosto_adsense_sanitize_slot',
			)
		);
		$wp_customize->add_control(
			'seisdeagosto_adsense_slot_' . $key,
			array(
				/* translators: %s: nome da posição do anúncio. */
				'label'       => sprintf( __( 'ID do bloco de anúncio: %s', 'u_correio68' ), $position['label'] ),
				'description' => 'materia' === $key
					? __( 'Opcional. Crie um bloco "No artigo" no AdSense e cole só o número (data-ad-slot). Vazio: a posição fica a cargo dos Anúncios automáticos.', 'u_correio68' )
					: __( 'Opcional. Crie um bloco "De display" no AdSense e cole só o número (data-ad-slot). Vazio: a posição fica a cargo dos Anúncios automáticos.', 'u_correio68' ),
				'section'     => 'seisdeagosto_adsense',
				'type'        => 'text',
				'input_attrs' => array(
					'inputmode'   => 'numeric',
					'placeholder' => '1234567890',
				),
			)
		);

		if ( 'materia' === $key ) {
			$wp_customize->add_setting(
				'seisdeagosto_adsense_materia_paragrafo',
				array(
					'default'           => 3,
					'sanitize_callback' => 'seisdeagosto_adsense_sanitize_paragraph',
				)
			);
			$wp_customize->add_control(
				'seisdeagosto_adsense_materia_paragrafo',
				array(
					'label'       => __( 'Anúncio da matéria após o parágrafo nº', 'u_correio68' ),
					'section'     => 'seisdeagosto_adsense',
					'type'        => 'number',
					'input_attrs' => array(
						'min' => 2,
						'max' => 10,
					),
				)
			);

			$wp_customize->add_setting(
				'seisdeagosto_adsense_materia2',
				array(
					'default'           => true,
					'sanitize_callback' => 'rest_sanitize_boolean',
				)
			);
			$wp_customize->add_control(
				'seisdeagosto_adsense_materia2',
				array(
					'label'       => __( '2º anúncio nas matérias longas', 'u_correio68' ),
					'description' => __( 'Em matérias com 14 parágrafos ou mais, repete o bloco "No artigo" mais adiante no texto, sempre a pelo menos 4 parágrafos do primeiro.', 'u_correio68' ),
					'section'     => 'seisdeagosto_adsense',
					'type'        => 'checkbox',
				)
			);
			$wp_customize->add_setting(
				'seisdeagosto_adsense_materia2_paragrafo',
				array(
					'default'           => 10,
					'sanitize_callback' => 'seisdeagosto_adsense_sanitize_paragraph2',
				)
			);
			$wp_customize->add_control(
				'seisdeagosto_adsense_materia2_paragrafo',
				array(
					'label'       => __( '2º anúncio após o parágrafo nº', 'u_correio68' ),
					'section'     => 'seisdeagosto_adsense',
					'type'        => 'number',
					'input_attrs' => array(
						'min' => 6,
						'max' => 30,
					),
				)
			);
		}
	}
}
add_action( 'customize_register', 'seisdeagosto_adsense_customize_register' );
