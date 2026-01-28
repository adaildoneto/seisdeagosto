<?php
/**
 * Regenerar miniaturas de imagens
 * Acesse: http://seisdeagosto.local/wp-content/themes/seisdeagosto/regenerate-thumbnails.php
 * 
 * Este script regenera as miniaturas das imagens recentes
 */

// Carregar WordPress
require_once('../../../wp-load.php');

// Verificar se é administrador
if (!current_user_can('manage_options')) {
    wp_die('Você não tem permissão para acessar esta página.');
}

set_time_limit(300); // 5 minutos

// Pegar posts recentes com imagens
$args = array(
    'post_type' => array('post', 'edital'),
    'posts_per_page' => 50,
    'meta_key' => '_thumbnail_id',
    'orderby' => 'date',
    'order' => 'DESC'
);

$query = new WP_Query($args);
$regenerated = 0;
$errors = 0;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Regenerar Miniaturas</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .header { background: #0073aa; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: white; padding: 20px; border-radius: 0 0 8px 8px; }
        .success { color: #46b450; }
        .error { color: #dc3232; }
        .post-item { padding: 10px; border-bottom: 1px solid #ddd; }
        .progress { background: #f0f0f0; height: 30px; border-radius: 5px; overflow: hidden; margin: 20px 0; }
        .progress-bar { background: #0073aa; height: 100%; text-align: center; color: white; line-height: 30px; transition: width 0.3s; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 20px 0; }
        .stat-box { background: #f9f9f9; padding: 15px; border-radius: 5px; text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; color: #0073aa; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🖼️ Regenerar Miniaturas de Imagens</h1>
        <p>Regenerando miniaturas dos últimos 50 posts com imagens destacadas</p>
    </div>
    
    <div class="content">
        <div class="stats">
            <div class="stat-box">
                <div class="stat-number"><?php echo $query->found_posts; ?></div>
                <div>Posts com Imagem</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="regenerated-count">0</div>
                <div>Regeneradas</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="error-count">0</div>
                <div>Erros</div>
            </div>
        </div>
        
        <div class="progress">
            <div class="progress-bar" id="progress-bar" style="width: 0%">0%</div>
        </div>
        
        <div id="log">
            <?php
            if ($query->have_posts()) :
                $total = $query->found_posts;
                $current = 0;
                
                while ($query->have_posts()) : $query->the_post();
                    $current++;
                    $thumbnail_id = get_post_thumbnail_id();
                    
                    if ($thumbnail_id) {
                        echo '<div class="post-item">';
                        echo '<strong>' . get_the_title() . '</strong><br>';
                        
                        // Regenerar miniaturas
                        $attachment_path = get_attached_file($thumbnail_id);
                        
                        if ($attachment_path && file_exists($attachment_path)) {
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            
                            $metadata = wp_generate_attachment_metadata($thumbnail_id, $attachment_path);
                            
                            if (!is_wp_error($metadata) && !empty($metadata)) {
                                wp_update_attachment_metadata($thumbnail_id, $metadata);
                                echo '<span class="success">✅ Miniaturas regeneradas com sucesso!</span>';
                                $regenerated++;
                            } else {
                                echo '<span class="error">❌ Erro ao gerar metadados</span>';
                                $errors++;
                            }
                        } else {
                            echo '<span class="error">❌ Arquivo de imagem não encontrado</span>';
                            $errors++;
                        }
                        
                        echo '</div>';
                        
                        // Atualizar progresso
                        $percentage = round(($current / $total) * 100);
                        echo '<script>
                            document.getElementById("progress-bar").style.width = "' . $percentage . '%";
                            document.getElementById("progress-bar").textContent = "' . $percentage . '%";
                            document.getElementById("regenerated-count").textContent = "' . $regenerated . '";
                            document.getElementById("error-count").textContent = "' . $errors . '";
                        </script>';
                        
                        // Flush para mostrar progresso em tempo real
                        if (ob_get_level() > 0) {
                            ob_flush();
                            flush();
                        }
                    }
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>Nenhum post com imagem destacada encontrado.</p>';
            endif;
            ?>
        </div>
        
        <hr style="margin: 30px 0;">
        
        <div style="background: #d4edda; padding: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
            <h3 style="margin: 0 0 10px 0; color: #155724;">✅ Processo Concluído!</h3>
            <p style="margin: 0; color: #155724;">
                <strong><?php echo $regenerated; ?></strong> imagens foram regeneradas com sucesso.<br>
                <?php if ($errors > 0) : ?>
                    <strong><?php echo $errors; ?></strong> erros encontrados.
                <?php endif; ?>
            </p>
        </div>
        
        <p style="margin-top: 30px; text-align: center;">
            <a href="<?php echo home_url(); ?>" style="display: inline-block; padding: 10px 20px; background: #0073aa; color: white; text-decoration: none; border-radius: 5px;">← Voltar ao site</a>
            <a href="?" style="display: inline-block; padding: 10px 20px; background: #f0f0f0; color: #333; text-decoration: none; border-radius: 5px; margin-left: 10px;">🔄 Executar Novamente</a>
        </p>
    </div>
</body>
</html>
