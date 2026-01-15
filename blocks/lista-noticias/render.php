<?php
function u_correio68_render_lista_noticias($attributes) {
    $number  = isset($attributes['numberOfPosts']) ? intval($attributes['numberOfPosts']) : 9;
    $offset  = isset($attributes['offset']) ? intval($attributes['offset']) : 3;
    $columns = isset($attributes['columns']) ? intval($attributes['columns']) : 3;
    
    // Validar e limitar colunas entre 2 e 6
    $columns = max(2, min(6, $columns));
    
    // Mapear número de colunas para classes Bootstrap
    $col_class = 'col-md-4'; // padrão para 3 colunas
    if ($columns === 2) {
        $col_class = 'col-md-6';
    } elseif ($columns === 4) {
        $col_class = 'col-md-3';
    } elseif ($columns === 6) {
        $col_class = 'col-lg-2 col-md-3';
    }
    
    $args = array(
        'post__not_in'        => get_option('sticky_posts'),
        'post_type'           => 'post',
        'posts_per_page'      => max(1, $number),
        'ignore_sticky_posts' => true,
        'order'               => 'DESC',
        'orderby'             => 'date',
        'offset'              => max(0, $offset),
        'post_status'         => 'publish'
    );

    if (!empty($attributes['category'])) {
        $args['category_name'] = sanitize_text_field($attributes['category']);
    }

    // Apply tag filter (CSV of slugs) and keyword search
    if (!empty($attributes['tags']) && is_string($attributes['tags'])) {
        $slugs = array_filter(array_map('sanitize_title', array_map('trim', explode(',', $attributes['tags']))));
        if (!empty($slugs)) {
            $args['tag_slug__in'] = $slugs;
        }
    }
    if (!empty($attributes['keyword'])) {
        $args['s'] = sanitize_text_field($attributes['keyword']);
    }

    $query = new WP_Query($args);
    ob_start();

    if ($query->have_posts()) :
        echo '<div class="row lista-noticias-grid" style="--columns:' . intval($columns) . ';">';
        while ($query->have_posts()) : $query->the_post();
            ?>
            <div <?php post_class($col_class . ' mb-4'); ?> id="post-<?php the_ID(); ?>">
                <article class="news-item" style="transition: opacity 0.2s ease;">
                    <?php 
                    $cor     = function_exists('get_field') ? get_field('cor') : '';
                    $icones  = function_exists('get_field') ? get_field('icones') : '';
                    $chamada = function_exists('get_field') ? get_field('chamada') : '';
                    if ( !empty($chamada) ) : ?>
                        <div class="mb-2">
                            <span class="badge badge-light text-white badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important; font-size: 0.7rem; padding: 0.25rem 0.5rem;"> 
                                <ion-icon class="<?php echo esc_attr($icones); ?>" style="font-size: 0.8rem;"></ion-icon> 
                                <span><?php echo esc_html($chamada); ?></span>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="news-image-wrapper overflow-hidden mb-3" style="height: 180px; border-radius: 4px; border: 1px solid #e9ecef;">
                        <?php 
                        if ( has_post_thumbnail() ) :
                            if ( class_exists('PG_Image') ) {
                                echo PG_Image::getPostImage(get_the_ID(), 'destaque', array(
                                    'class' => 'w-100 h-100',
                                    'style' => 'object-fit: cover;'
                                ), 'both', null);
                            } else {
                                the_post_thumbnail('destaque', array(
                                    'class' => 'w-100 h-100',
                                    'style' => 'object-fit: cover;'
                                ));
                            }
                        else:
                            echo '<div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light"><i class="fa fa-image fa-2x text-muted"></i></div>';
                        endif;
                        ?>
                    </div>
                    
                    <div class="news-content">
                        <a href="<?php echo esc_url(get_permalink()); ?>" class="text-decoration-none">
                            <h5 class="mb-2" style="line-height: 1.4; font-size: 1rem; color: #333; font-weight: 600;">
                                <?php the_title(); ?>
                            </h5>
                        </a>
                        
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <small class="text-muted" style="font-size: 0.8rem;">
                                <i class="fa fa-clock-o"></i> <?php echo get_the_date('d/m/Y'); ?>
                            </small>
                            <a href="<?php echo esc_url(get_permalink()); ?>" class="text-primary" style="font-size: 0.85rem; font-weight: 500;">
                                Ler mais →
                            </a>
                        </div>
                    </div>
                </article>
            </div>
            <?php
        endwhile;
        echo '</div>';
        wp_reset_postdata();
    else :
        echo '<div class="alert alert-info w-100" role="alert"><i class="fa fa-info-circle"></i> Nenhuma notícia encontrada.</div>';
    endif;

    return ob_get_clean();
}

