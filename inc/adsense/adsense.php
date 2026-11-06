<?php
/**
 * Google AdSense no tema SeisdeAgosto.
 *
 * Basta colar a tag ou o ID do AdSense em Personalizar → 📢 AdSense.
 * Especificação: docs/superpowers/specs/2026-11-06-adsense-design.md
 *
 * @package SeisdeAgosto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$seisdeagosto_adsense_dir = get_template_directory() . '/inc/adsense/';
require_once $seisdeagosto_adsense_dir . 'settings.php';
require_once $seisdeagosto_adsense_dir . 'output.php';
require_once $seisdeagosto_adsense_dir . 'unit.php';
require_once $seisdeagosto_adsense_dir . 'placements.php';
require_once $seisdeagosto_adsense_dir . 'block.php';
unset( $seisdeagosto_adsense_dir );
