<?php
/**
 * Debug Currency Monitor Block
 * 
 * Adicione este código no final do functions.php para debugar:
 * add_action('wp_footer', function() {
 *     if (current_user_can('manage_options')) {
 *         $args = array(
 *             'provider' => 'erapi',
 *             'showUSD' => true,
 *             'showEUR' => true,
 *             'showPEN' => true,
 *             'showARS' => true,
 *             'showBOB' => true,
 *             'slidesToShow' => 2
 *         );
 *         echo '<div style="margin:20px;padding:20px;background:#f0f0f0;border:2px solid #333;">';
 *         echo '<h3>Debug Currency Monitor</h3>';
 *         echo u_seisbarra8_currency_monitor_render_callback($args, '');
 *         echo '</div>';
 *     }
 * });
 */

// Testa diretamente a API
function debug_currency_api_test() {
    $symbols = array('USD', 'EUR', 'PEN', 'ARS', 'BOB');
    $base = 'BRL';
    
    echo "<h2>Teste de API de Câmbio</h2>";
    
    // Test erapi
    echo "<h3>1. ER-API (Recomendado - Grátis)</h3>";
    $api_url = 'https://open.er-api.com/v6/latest/' . $base;
    $res = wp_remote_get($api_url, array('timeout' => 5));
    if (!is_wp_error($res)) {
        $body = wp_remote_retrieve_body($res);
        $json = json_decode($body, true);
        echo "<pre>";
        print_r($json);
        echo "</pre>";
        if (isset($json['rates'])) {
            echo "<p style='color:green'>✓ ER-API funcionando corretamente!</p>";
        } else {
            echo "<p style='color:red'>✗ ER-API retornou dados incompletos</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Erro: " . $res->get_error_message() . "</p>";
    }
    
    // Test exchangerate.host
    echo "<h3>2. ExchangeRate.host (Backup)</h3>";
    $api_url = add_query_arg(array(
        'base' => $base,
        'symbols' => implode(',', $symbols),
    ), 'https://api.exchangerate.host/latest');
    $res = wp_remote_get($api_url, array('timeout' => 5));
    if (!is_wp_error($res)) {
        $body = wp_remote_retrieve_body($res);
        $json = json_decode($body, true);
        echo "<pre>";
        print_r($json);
        echo "</pre>";
        if (isset($json['rates'])) {
            echo "<p style='color:green'>✓ ExchangeRate.host funcionando!</p>";
        } else {
            echo "<p style='color:red'>✗ ExchangeRate.host retornou dados incompletos</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Erro: " . $res->get_error_message() . "</p>";
    }
    
    // Check block registration
    echo "<h3>3. Verificação de Registro do Bloco</h3>";
    $registry = WP_Block_Type_Registry::get_instance();
    $has_seideagosto = $registry->is_registered('seideagosto/currency-monitor');
    $has_correio68 = $registry->is_registered('correio68/currency-monitor');
    
    echo "<p>seideagosto/currency-monitor: " . ($has_seideagosto ? '<span style="color:green">✓ Registrado</span>' : '<span style="color:red">✗ Não registrado</span>') . "</p>";
    echo "<p>correio68/currency-monitor: " . ($has_correio68 ? '<span style="color:green">✓ Registrado</span>' : '<span style="color:red">✗ Não registrado</span>') . "</p>";
    
    if ($has_seideagosto) {
        $block = $registry->get_registered('seideagosto/currency-monitor');
        echo "<h4>Atributos do Bloco:</h4>";
        echo "<pre>";
        print_r($block->attributes);
        echo "</pre>";
    }
}

// Para usar: adicione isto no functions.php temporariamente
// add_action('wp_footer', 'debug_currency_api_test');
