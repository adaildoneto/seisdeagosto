<?php
/**
 * Currency monitor debug helpers.
 *
 * Arquivo utilitario: nao ativa nada sozinho.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Exibe um diagnostico simples das APIs e do registro do bloco.
 */
function debug_currency_api_test() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $symbols = array( 'USD', 'EUR', 'PEN', 'ARS', 'BOB' );
    $base    = 'BRL';

    echo '<h2>Teste de API de Cambio</h2>';

    echo '<h3>1. ER-API</h3>';
    $api_url = 'https://open.er-api.com/v6/latest/' . $base;
    $res     = wp_remote_get( $api_url, array( 'timeout' => 5 ) );
    if ( ! is_wp_error( $res ) ) {
        $body = wp_remote_retrieve_body( $res );
        $json = json_decode( $body, true );
        echo '<pre>' . esc_html( print_r( $json, true ) ) . '</pre>';
        if ( isset( $json['rates'] ) ) {
            echo "<p style='color:green'>ER-API funcionando corretamente.</p>";
        } else {
            echo "<p style='color:red'>ER-API retornou dados incompletos.</p>";
        }
    } else {
        echo "<p style='color:red'>Erro: " . esc_html( $res->get_error_message() ) . '</p>';
    }

    echo '<h3>2. ExchangeRate.host</h3>';
    $api_url = add_query_arg(
        array(
            'base'    => $base,
            'symbols' => implode( ',', $symbols ),
        ),
        'https://api.exchangerate.host/latest'
    );
    $res = wp_remote_get( $api_url, array( 'timeout' => 5 ) );
    if ( ! is_wp_error( $res ) ) {
        $body = wp_remote_retrieve_body( $res );
        $json = json_decode( $body, true );
        echo '<pre>' . esc_html( print_r( $json, true ) ) . '</pre>';
        if ( isset( $json['rates'] ) ) {
            echo "<p style='color:green'>ExchangeRate.host funcionando.</p>";
        } else {
            echo "<p style='color:red'>ExchangeRate.host retornou dados incompletos.</p>";
        }
    } else {
        echo "<p style='color:red'>Erro: " . esc_html( $res->get_error_message() ) . '</p>';
    }

    echo '<h3>3. Verificacao de Registro do Bloco</h3>';
    $registry         = WP_Block_Type_Registry::get_instance();
    $has_seideagosto  = $registry->is_registered( 'seisdeagosto/currency-monitor' );
    $has_correio68    = $registry->is_registered( 'correio68/currency-monitor' );

    echo '<p>seisdeagosto/currency-monitor: ' . ( $has_seideagosto ? 'Registrado' : 'Nao registrado' ) . '</p>';
    echo '<p>correio68/currency-monitor: ' . ( $has_correio68 ? 'Registrado' : 'Nao registrado' ) . '</p>';

    if ( $has_seideagosto ) {
        $block = $registry->get_registered( 'seisdeagosto/currency-monitor' );
        echo '<h4>Atributos do Bloco:</h4>';
        echo '<pre>' . esc_html( print_r( $block->attributes, true ) ) . '</pre>';
    }
}
