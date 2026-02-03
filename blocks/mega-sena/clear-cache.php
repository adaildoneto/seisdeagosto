<?php
/**
 * Script para limpar cache das loterias
 * Acesse via navegador: /wp-content/themes/seisdeagosto/blocks/mega-sena/clear-cache.php
 */

// Evita acesso direto
if ( ! defined( 'ABSPATH' ) ) {
    // Tenta carregar o WordPress
    $wp_load_path = dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/wp-load.php';
    
    if ( file_exists( $wp_load_path ) ) {
        require_once $wp_load_path;
    } else {
        die( 'WordPress não encontrado. Execute este script via admin do WordPress.' );
    }
}

// Carrega a API
if ( ! function_exists( 'seisdeagosto_get_loteria_result' ) ) {
    require_once __DIR__ . '/loteria-api.php';
}

// Limpa todos os caches
seisdeagosto_clear_loteria_cache();

// Exibe mensagem
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cache Limpo</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f0f0f1;
        }
        .container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        h1 {
            color: #209869;
            margin: 0 0 1rem;
        }
        p {
            color: #666;
            margin: 0.5rem 0;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .btn {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.75rem 2rem;
            background: #209869;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #1a7a53;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">✅</div>
        <h1>Cache Limpo com Sucesso!</h1>
        <p>O cache de todas as loterias foi removido.</p>
        <p>A próxima requisição irá buscar dados atualizados da API.</p>
        <a href="<?php echo admin_url(); ?>" class="btn">Voltar ao WordPress</a>
    </div>
</body>
</html>
