<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Debug - Data Mega Sena</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f0f0f0; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; }
        pre { background: #f8f8f8; padding: 10px; overflow-x: auto; }
        h2 { color: #209869; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>🔍 Debug - Data Mega Sena</h1>
    
    <?php
    // Limpa cache
    if (isset($_GET['limpar'])) {
        delete_transient('loteria_megasena');
        echo '<div class="box success">✅ Cache limpo!</div>';
    }
    
    // Carrega WordPress
    require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-load.php';
    
    // Carrega API
    require_once __DIR__ . '/loteria-api.php';
    
    // Busca dados
    echo '<div class="box">';
    echo '<h2>📡 Requisição à API da Caixa</h2>';
    
    $url = 'https://servicebus2.caixa.gov.br/portaldeloterias/api/megasena';
    echo '<p><strong>URL:</strong> ' . $url . '</p>';
    
    $response = wp_remote_get($url, array('timeout' => 15));
    
    if (is_wp_error($response)) {
        echo '<p class="error">❌ Erro: ' . $response->get_error_message() . '</p>';
    } else {
        $status = wp_remote_retrieve_response_code($response);
        echo '<p><strong>Status HTTP:</strong> ' . $status . '</p>';
        
        if ($status === 200) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            echo '<h3>📅 Dados de Data:</h3>';
            echo '<ul>';
            echo '<li><strong>dataApuracao (RAW):</strong> ' . ($data['dataApuracao'] ?? 'N/A') . '</li>';
            
            if (isset($data['dataApuracao'])) {
                // Teste 1: strtotime
                $timestamp = strtotime($data['dataApuracao']);
                echo '<li><strong>strtotime:</strong> ' . $timestamp . ' → ' . date('d/m/Y', $timestamp) . '</li>';
                
                // Teste 2: DateTime
                try {
                    $date = new DateTime($data['dataApuracao']);
                    echo '<li><strong>DateTime:</strong> ' . $date->format('d/m/Y') . ' (' . $date->format('Y-m-d H:i:s') . ')</li>';
                } catch (Exception $e) {
                    echo '<li class="error"><strong>DateTime ERRO:</strong> ' . $e->getMessage() . '</li>';
                }
                
                // Teste 3: Função helper
                echo '<li><strong>seisdeagosto_format_date:</strong> ' . seisdeagosto_format_date($data['dataApuracao']) . '</li>';
            }
            echo '</ul>';
            
            echo '<h3>🎲 Concurso:</h3>';
            echo '<p><strong>Número:</strong> ' . ($data['numero'] ?? 'N/A') . '</p>';
            
            echo '<h3>🔢 Números Sorteados:</h3>';
            if (isset($data['listaDezenas'])) {
                echo '<p>' . implode(' - ', $data['listaDezenas']) . '</p>';
            }
            
            echo '<h3>📋 JSON Completo:</h3>';
            echo '<pre>' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
        } else {
            echo '<p class="error">❌ Status não é 200</p>';
        }
    }
    echo '</div>';
    
    echo '<div class="box">';
    echo '<h2>🗑️ Limpar Cache</h2>';
    echo '<a href="?limpar=1" style="display:inline-block; padding:10px 20px; background:#209869; color:white; text-decoration:none; border-radius:5px;">Limpar Cache e Recarregar</a>';
    echo '</div>';
    ?>
    
</body>
</html>
