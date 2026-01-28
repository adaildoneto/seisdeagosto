<?php
/**
 * Limpar cache do Instagram Reels
 * Acesse: seu-site.com/wp-content/themes/seisdeagosto/blocks/instagram-reels/clear-cache.php
 */

// Subir dois níveis para encontrar wp-load.php
require_once('../../../../wp-load.php');

// Load the image downloader
require_once __DIR__ . '/instagram-image-downloader.php';

// Get cache stats before clearing
$stats_before = seisdeagosto_get_instagram_cache_stats();

// Clear old cached images (older than 7 days)
$deleted_old_images = seisdeagosto_clear_old_instagram_cache();

// Clear all cached images if requested
$clear_all = isset( $_GET['all'] ) && $_GET['all'] === 'true';
$deleted_all_images = 0;

if ( $clear_all ) {
    $upload_dir = wp_upload_dir();
    $instagram_dir = $upload_dir['basedir'] . '/instagram-cache';
    
    if ( file_exists( $instagram_dir ) ) {
        $files = glob( $instagram_dir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE );
        foreach ( $files as $file ) {
            if ( unlink( $file ) ) {
                $deleted_all_images++;
            }
        }
    }
}

// Get cache stats after clearing
$stats_after = seisdeagosto_get_instagram_cache_stats();

// Limpar todos os transients do Instagram
global $wpdb;
$deleted = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_ig_%' OR option_name LIKE '_transient_timeout_ig_%'" );

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Limpar Cache Instagram</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .success { background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; text-align: center; }
        h1 { margin: 0 0 10px 0; }
        a { color: #155724; }
    </style>
</head>
<body>
    <div class="success">
        <h1>✅ Cache Limpo!</h1>
        <p>Foram removidos <strong><?php echo $deleted; ?></strong> registros de cache (transients) do Instagram.</p>
        
        <?php if ( $clear_all ) : ?>
            <p>🗑️ <strong><?php echo $deleted_all_images; ?></strong> imagens foram deletadas do cache local.</p>
        <?php else : ?>
            <p>🗑️ <strong><?php echo $deleted_old_images; ?></strong> imagens antigas (7+ dias) foram deletadas.</p>
        <?php endif; ?>
        
        <hr style="margin: 20px 0; border: none; border-top: 1px solid #c3e6cb;">
        
        <p><strong>Cache de Imagens:</strong></p>
        <ul style="list-style: none; padding: 0;">
            <li>📊 Antes: <?php echo $stats_before['count']; ?> arquivos (<?php echo $stats_before['size_formatted']; ?>)</li>
            <li>📊 Depois: <?php echo $stats_after['count']; ?> arquivos (<?php echo $stats_after['size_formatted']; ?>)</li>
        </ul>
        
        <hr style="margin: 20px 0; border: none; border-top: 1px solid #c3e6cb;">
        
        <p>Os posts serão buscados novamente do Instagram na próxima visualização.</p>
        
        <p style="margin-top: 30px;">
            <a href="<?php echo home_url(); ?>" style="display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">← Voltar ao site</a>
            <?php if ( ! $clear_all ) : ?>
                <a href="?all=true" style="display: inline-block; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;">🗑️ Limpar TUDO</a>
            <?php endif; ?>
        </p>
    </div>
</body>
</html>
