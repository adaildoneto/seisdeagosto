<?php
function u_correio68_render_destaque_pequeno($attributes) {
    $number = isset($attributes['numberOfPosts']) ? intval($attributes['numberOfPosts']) : 2;
    $offset = isset($attributes['offset']) ? intval($attributes['offset']) : 1;
    $postType = isset($attributes['postType']) && in_array($attributes['postType'], ['post', 'edital']) ? $attributes['postType'] : 'post';
    $args = array(
        'post_type'      => $postType,
        'posts_per_page' => max(1, $number),
        'offset'         => max(0, $offset),
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
                            'class' => 'w-100',
                            'sizes' => 'max-width(320px) 85vw, max-width(640px) 510px, max-width(768px) 70vw, max-width(1024px) 60vw, max-width(1280px) 730px, 730px'
                        ), 'both', null);
                    } else {
                        the_post_thumbnail('destatquegrande', array('class' => 'w-100'));
                    }
                endif;
                ?>
                <div class="card-img-overlay grad gradiente space">
                    <div class="tituloD">
                        <?php 
                        $cor     = function_exists('get_field') ? get_field('cor') : '';
                        $icones  = function_exists('get_field') ? get_field('icones') : '';
                        $chamada = function_exists('get_field') ? get_field('chamada') : '';
                        if ( !empty($chamada) ) : ?>
                            <span class="badge badge-light text-white bg-orange badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important;"> 
                                <i class="<?php echo esc_attr($icones); ?>"></i> 
                                <span><?php echo esc_html($chamada); ?></span>
                            </span>
                        <?php endif; ?>
                        <a href="<?php echo esc_url(get_permalink()); ?>">
                            <br><span class="TituloGrande2 text-shadow text-white"><?php the_title(); ?></span>
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
