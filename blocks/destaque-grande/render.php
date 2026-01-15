<?php
function u_correio68_render_destaque_grande($attributes) {
    $number = isset($attributes['numberOfPosts']) ? intval($attributes['numberOfPosts']) : 1;
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => max(1, $number),
        'order'          => 'DESC',
        'orderby'        => 'date',
        'post_status'    => 'publish'
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
        while ($query->have_posts()) : $query->the_post();
            ?>
            <div <?php post_class('bg-dark card spaces text-white'); ?> id="post-<?php the_ID(); ?>">
                <?php 
                if ( has_post_thumbnail() ) :
                    if ( class_exists('PG_Image') ) {
                        echo PG_Image::getPostImage(get_the_ID(), 'destatquegrande', array(
                            'class' => 'imagem-destaque w-100'
                        ), null, null);
                    } else {
                        the_post_thumbnail('destatquegrande', array('class' => 'imagem-destaque w-100'));
                    }
                endif;
                ?>
                <div class="card-img-overlay gradiente space">
                    <div class="tituloD">
                        <?php 
                        $cor     = function_exists('get_field') ? get_field('cor') : '';
                        $icones  = function_exists('get_field') ? get_field('icones') : '';
                        $chamada = function_exists('get_field') ? get_field('chamada') : '';
                        if ( !empty($chamada) ) : ?>
                            <span class="badge badge-light text-white bg-orange badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important;">
                                <ion-icon class="<?php echo esc_attr($icones); ?>"></ion-icon>
                                <span><?php echo esc_html($chamada); ?></span>
                            </span>
                            <br>
                        <?php endif; ?>
                        <a href="<?php echo esc_url(get_permalink()); ?>">
                            <span class="TituloGrande text-white text-shadow"><?php the_title(); ?></span>
                        </a>
                    </div>
                </div>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
    else :
        echo '<p>' . esc_html__( 'Sorry, no posts matched your criteria.', 'u_correio68' ) . '</p>';
    endif;

    return ob_get_clean();
}
