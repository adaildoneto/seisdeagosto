<?php
/**
 * Custom Gutenberg Blocks for UbberCorreio68
 */

/**
 * Shared typography attribute schema for dynamic blocks.
 */
function u_correio68_typography_attribute_schema( $defaultColor = '#000000' ) {
    return array(
        'fontSize'   => array( 'type' => 'number', 'default' => 16 ),
        'fontFamily' => array( 'type' => 'string', 'default' => 'Arial, sans-serif' ),
        'fontWeight' => array( 'type' => 'string', 'default' => 'normal' ),
        'titleColor' => array( 'type' => 'string', 'default' => $defaultColor ),
    );
}

/**
 * Resolve typography values and return a ready-to-use style string.
 */
function u_correio68_resolve_typography( $attributes, $defaultColor = '#000000' ) {
    $defaults = array(
        'fontSize'   => 16,
        'fontFamily' => 'Arial, sans-serif',
        'fontWeight' => 'normal',
        'titleColor' => $defaultColor,
    );

    $fontSize   = isset( $attributes['fontSize'] ) ? intval( $attributes['fontSize'] ) : $defaults['fontSize'];
    $fontFamily = isset( $attributes['fontFamily'] ) ? sanitize_text_field( $attributes['fontFamily'] ) : $defaults['fontFamily'];
    $fontWeight = isset( $attributes['fontWeight'] ) ? sanitize_text_field( $attributes['fontWeight'] ) : $defaults['fontWeight'];
    $titleColor = isset( $attributes['titleColor'] ) ? sanitize_hex_color( $attributes['titleColor'] ) : $defaults['titleColor'];

    if ( empty( $titleColor ) ) {
        $titleColor = $defaults['titleColor'];
    }

    return array(
        'fontSize'   => $fontSize,
        'fontFamily' => $fontFamily,
        'fontWeight' => $fontWeight,
        'titleColor' => $titleColor,
        'style'      => sprintf(
            'font-size: %dpx; font-family: %s; font-weight: %s; color: %s;',
            $fontSize,
            esc_attr( $fontFamily ),
            esc_attr( $fontWeight ),
            esc_attr( $titleColor )
        ),
    );
}

/**
 * Apply category filter (by term ID) to a query args array.
 */
function u_correio68_apply_category_filter( array $args, $categoryId ) {
    $categoryId = absint( $categoryId );

    if ( $categoryId > 0 ) {
        // Use core "cat" param for simple filtering by term ID.
        $args['cat'] = $categoryId;
    }

    return $args;
}

function u_correio68_register_custom_blocks() {
    // Register the block editor script
    wp_register_script(
        'u-correio68-custom-blocks',
        get_template_directory_uri() . '/assets/js/custom-blocks.js',
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-server-side-render' ),
        filemtime( get_template_directory() . '/assets/js/custom-blocks.js' )
    );

    // Get categories for the dropdown
    $categories = get_categories( array( 'hide_empty' => false ) );
    $cat_options = array( array( 'value' => 0, 'label' => 'Todas as Categorias' ) );
    foreach ( $categories as $cat ) {
        $cat_options[] = array(
            'value' => $cat->term_id,
            'label' => $cat->name
        );
    }

    // Get registered sidebars for sidebar selector block
    $sidebar_options = array();
    global $wp_registered_sidebars;
    if ( is_array( $wp_registered_sidebars ) ) {
        foreach ( $wp_registered_sidebars as $id => $sb ) {
            $sidebar_options[] = array(
                'value' => $id,
                'label' => isset( $sb['name'] ) ? $sb['name'] . " (" . $id . ")" : $id
            );
        }
    }

    // Pass data to JS
    wp_localize_script(
        'u-correio68-custom-blocks',
        'uCorreio68Blocks',
        array(
            'categories' => $cat_options,
            'sidebars'   => $sidebar_options
        )
    );
    // Also expose under new namespace for forward compatibility
    wp_localize_script(
        'u-correio68-custom-blocks',
        'seideagostoBlocks',
        array(
            'categories' => $cat_options,
            'sidebars'   => $sidebar_options
        )
    );

    $typography_default = u_correio68_typography_attribute_schema();
    $typography_light   = u_correio68_typography_attribute_schema( '#FFFFFF' );

    // Register Destaques Home Block
    register_block_type( 'u-correio68/destaques-home', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_destaques_home',
        'attributes' => array(
            'categoryId' => array(
                'type' => 'string',
                'default' => '0',
            ),
        ),
    ) );
    // Forward-compatible registration under new namespace
    register_block_type( 'seideagosto/destaques-home', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_destaques_home',
        'attributes' => array(
            'categoryId' => array(
                'type' => 'string',
                'default' => '0',
            ),
        ),
    ) );

    // Register Colunistas Grid Block
    register_block_type( 'u-correio68/colunistas-grid', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_colunistas_grid',
    ) );
    // Forward-compatible registration under new namespace
    register_block_type( 'seideagosto/colunistas-grid', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_colunistas_grid',
    ) );

    // Register Colunista Item Block
    register_block_type( 'u-correio68/colunista-item', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_colunista_item',
        'attributes' => array(
            'name' => array( 'type' => 'string', 'default' => '' ),
            'columnTitle' => array( 'type' => 'string', 'default' => '' ),
            'imageUrl' => array( 'type' => 'string', 'default' => '' ),
            'categoryId' => array( 'type' => 'string', 'default' => '0' ),
        ),
    ) );
    // Forward-compatible registration under new namespace
    register_block_type( 'seideagosto/colunista-item', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_colunista_item',
        'attributes' => array(
            'name' => array( 'type' => 'string', 'default' => '' ),
            'columnTitle' => array( 'type' => 'string', 'default' => '' ),
            'imageUrl' => array( 'type' => 'string', 'default' => '' ),
            'categoryId' => array( 'type' => 'string', 'default' => '0' ),
        ),
    ) );

    // Register News Grid Block
    register_block_type( 'u-correio68/news-grid', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_news_grid',
        'attributes' => array_merge(
            array(
                'categoryId'    => array( 'type' => 'string', 'default' => '0' ),
                'numberOfPosts' => array( 'type' => 'number', 'default' => 9 ),
                'offset'        => array( 'type' => 'number', 'default' => 0 ),
                'columns'       => array( 'type' => 'number', 'default' => 3 ),
                'paginate'      => array( 'type' => 'boolean', 'default' => false ),
            ),
            $typography_default
        ),
    ) );
    // Forward-compatible registration under new namespace (editor will expose once JS registers)
    register_block_type( 'seideagosto/news-grid', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_news_grid',
        'attributes' => array_merge(
            array(
                'categoryId'    => array( 'type' => 'string', 'default' => '0' ),
                'numberOfPosts' => array( 'type' => 'number', 'default' => 9 ),
                'offset'        => array( 'type' => 'number', 'default' => 0 ),
                'columns'       => array( 'type' => 'number', 'default' => 3 ),
                'paginate'      => array( 'type' => 'boolean', 'default' => false ),
            ),
            $typography_default
        ),
    ) );

    // Register Category Highlight Block (1 Big + 3 List)
    register_block_type( 'u-correio68/category-highlight', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_category_highlight',
        'attributes' => array_merge(
            array(
                'categoryId' => array( 'type' => 'string', 'default' => '0' ),
                'title'      => array( 'type' => 'string', 'default' => '' ),
                'bigCount'   => array( 'type' => 'number', 'default' => 1 ),
                'listCount'  => array( 'type' => 'number', 'default' => 3 ),
            ),
            $typography_default
        ),
    ) );
    // Forward-compatible registration under new namespace
    register_block_type( 'seideagosto/category-highlight', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_category_highlight',
        'attributes' => array_merge(
            array(
                'categoryId' => array( 'type' => 'string', 'default' => '0' ),
                'title'      => array( 'type' => 'string', 'default' => '' ),
                'bigCount'   => array( 'type' => 'number', 'default' => 1 ),
                'listCount'  => array( 'type' => 'number', 'default' => 3 ),
            ),
            $typography_default
        ),
    ) );

    // Register Destaque Misto (2 Big + List + 1 Column)
    register_block_type( 'u-correio68/destaque-misto', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_destaque_misto',
        'attributes' => array_merge(
            array(
                'categoryId' => array( 'type' => 'string', 'default' => '0' ),
            ),
            $typography_light
        ),
    ) );
    // Forward-compatible registration under new namespace
    register_block_type( 'seideagosto/destaque-misto', array(
        'editor_script' => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_destaque_misto',
        'attributes' => array_merge(
            array(
                'categoryId' => array( 'type' => 'string', 'default' => '0' ),
            ),
            $typography_light
        ),
    ) );

    // Register Top Most Read (Top 5) Block
    register_block_type( 'u-correio68/top-most-read', array(
        'editor_script'   => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_top_most_read',
        'attributes'      => array(
            'title'      => array( 'type' => 'string', 'default' => 'Mais lidas' ),
            'count'      => array( 'type' => 'number', 'default' => 5 ),
            'metaKey'    => array( 'type' => 'string', 'default' => 'post_views_count' ),
            'categoryId' => array( 'type' => 'string', 'default' => '0' ),
        ),
    ) );
    // Forward-compatible registration under new namespace
    register_block_type( 'seideagosto/top-most-read', array(
        'editor_script'   => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_top_most_read',
        'attributes'      => array(
            'title'      => array( 'type' => 'string', 'default' => 'Mais lidas' ),
            'count'      => array( 'type' => 'number', 'default' => 5 ),
            'metaKey'    => array( 'type' => 'string', 'default' => 'post_views_count' ),
            'categoryId' => array( 'type' => 'string', 'default' => '0' ),
        ),
    ) );

    // Register Weather Block
    register_block_type( 'u-correio68/weather', array(
        'editor_script'   => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_weather',
        'attributes'      => array(
            'cityName'   => array( 'type' => 'string', 'default' => '' ),
            'latitude'   => array( 'type' => 'string', 'default' => '' ),
            'longitude'  => array( 'type' => 'string', 'default' => '' ),
            'units'      => array( 'type' => 'string', 'default' => 'c' ), // 'c' Celsius, 'f' Fahrenheit
            'showWind'   => array( 'type' => 'boolean', 'default' => true ),
            'showRain'   => array( 'type' => 'boolean', 'default' => true ),
        ),
    ) );
    // Forward-compatible registration under new namespace
    register_block_type( 'seideagosto/weather', array(
        'editor_script'   => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_weather',
        'attributes'      => array(
            'cityName'   => array( 'type' => 'string', 'default' => '' ),
            'latitude'   => array( 'type' => 'string', 'default' => '' ),
            'longitude'  => array( 'type' => 'string', 'default' => '' ),
            'units'      => array( 'type' => 'string', 'default' => 'c' ), // 'c' Celsius, 'f' Fahrenheit
            'showWind'   => array( 'type' => 'boolean', 'default' => true ),
            'showRain'   => array( 'type' => 'boolean', 'default' => true ),
        ),
    ) );

    // Register Currency Monitor Block
    register_block_type( 'u-correio68/currency-monitor', array(
        'editor_script'   => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_currency_monitor',
        'attributes'      => array(
            'provider'    => array( 'type' => 'string',  'default' => 'exchangerate' ),
            'base'        => array( 'type' => 'string', 'default' => 'BRL' ),
            'showBRL'     => array( 'type' => 'boolean', 'default' => true ),
            'showUSD'     => array( 'type' => 'boolean', 'default' => true ),
            'showBOB'     => array( 'type' => 'boolean', 'default' => true ),
            'showPEN'     => array( 'type' => 'boolean', 'default' => true ),
            'spread'      => array( 'type' => 'number',  'default' => 0.5 ), // percent
            'showUpdated' => array( 'type' => 'boolean', 'default' => true ),
        ),
    ) );
    // Forward-compatible registration under new namespace
    register_block_type( 'seideagosto/currency-monitor', array(
        'editor_script'   => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_currency_monitor',
        'attributes'      => array(
            'provider'    => array( 'type' => 'string',  'default' => 'exchangerate' ),
            'base'        => array( 'type' => 'string', 'default' => 'BRL' ),
            'showBRL'     => array( 'type' => 'boolean', 'default' => true ),
            'showUSD'     => array( 'type' => 'boolean', 'default' => true ),
            'showBOB'     => array( 'type' => 'boolean', 'default' => true ),
            'showPEN'     => array( 'type' => 'boolean', 'default' => true ),
            'spread'      => array( 'type' => 'number',  'default' => 0.5 ), // percent
            'showUpdated' => array( 'type' => 'boolean', 'default' => true ),
        ),
    ) );

    // Register Sidebar Area Block (render a selected widget area)
    register_block_type( 'u-correio68/sidebar-area', array(
        'editor_script'   => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_sidebar_area',
        'attributes'      => array(
            'sidebarId' => array( 'type' => 'string', 'default' => 'right-sidebar' ),
            'title'     => array( 'type' => 'string', 'default' => '' ),
        ),
    ) );
    // Forward-compatible registration under new namespace
    register_block_type( 'seideagosto/sidebar-area', array(
        'editor_script'   => 'u-correio68-custom-blocks',
        'render_callback' => 'u_correio68_render_sidebar_area',
        'attributes'      => array(
            'sidebarId' => array( 'type' => 'string', 'default' => 'right-sidebar' ),
            'title'     => array( 'type' => 'string', 'default' => '' ),
        ),
    ) );

    // Register metadata-based blocks from theme/blocks directory
    $blocks_dir = get_template_directory() . '/blocks';
    $metadata_blocks = array(
        'destaque-grande',
        'destaque-pequeno',
        'lista-noticias',
    );
    foreach ( $metadata_blocks as $slug ) {
        $path = $blocks_dir . '/' . $slug;
        if ( file_exists( $path . '/block.json' ) ) {
            $block_config = json_decode( file_get_contents( $path . '/block.json' ), true );
            if ( $block_config ) {
                $block_config['render_callback'] = isset( $block_config['renderCallback'] ) ? $block_config['renderCallback'] : '';
                if ( ! empty( $block_config['render_callback'] ) && function_exists( $block_config['render_callback'] ) ) {
                    register_block_type( $path, array(
                        'render_callback' => $block_config['render_callback'],
                    ) );
                } else {
                    register_block_type( $path );
                }
            }
        }
    }
}
add_action( 'init', 'u_correio68_register_custom_blocks' );

/**
 * Render callback for the Sidebar Area block.
 */
function u_correio68_render_sidebar_area( $attributes ) {
    $sidebar_id = isset( $attributes['sidebarId'] ) ? sanitize_text_field( $attributes['sidebarId'] ) : '';
    $title      = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : '';

    global $wp_registered_sidebars;
    $exists = isset( $wp_registered_sidebars[ $sidebar_id ] );

    ob_start();
    echo '<div class="widget-area block-sidebar" data-sidebar="' . esc_attr( $sidebar_id ) . '">';
    if ( ! empty( $title ) ) {
        echo '<h3 class="widget-title">' . esc_html( $title ) . '</h3>';
    }
    if ( $exists ) {
        dynamic_sidebar( $sidebar_id );
    } else {
        // Helpful notice in editor/front if invalid or missing
        echo '<p class="text-muted">' . esc_html__( 'Selecione uma sidebar válida nas configurações do bloco.', 'u_correio68' ) . '</p>';
    }
    echo '</div>';
    return ob_get_clean();
}

/**
 * Schedule a daily refresh of common FX rates (USD, BOB, PEN vs BRL).
 */
function u_correio68_schedule_fx_cron() {
    if ( ! wp_next_scheduled( 'u_correio68_fx_daily_event' ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'u_correio68_fx_daily_event' );
    }
}
add_action( 'init', 'u_correio68_schedule_fx_cron' );

/**
 * Cron handler: prefetch rates and populate transients.
 */
function u_correio68_fx_cron_refresh() {
    $base     = 'BRL';
    $symbols  = array( 'USD', 'BOB', 'PEN' );
    $providers= array( 'exchangerate', 'frankfurter' );

    foreach ( $providers as $provider ) {
        $cache_key = 'u68_fx_' . md5( $provider . '|' . $base . '|' . implode(',', $symbols) );
        // Prefetch using same logic as render
        $rates = array();
        $updated_at = '';
        if ( $provider === 'frankfurter' ) {
            $api_url = add_query_arg( array(
                'from' => $base,
                'to'   => implode(',', $symbols),
            ), 'https://api.frankfurter.app/latest' );
        } else {
            $api_url = add_query_arg( array(
                'base'    => $base,
                'symbols' => implode(',', $symbols),
            ), 'https://api.exchangerate.host/latest' );
        }
        $res = wp_remote_get( $api_url, array( 'timeout' => 10 ) );
        if ( ! is_wp_error( $res ) ) {
            $body = wp_remote_retrieve_body( $res );
            $json = json_decode( $body, true );
            if ( ! empty( $json['rates'] ) ) {
                $rates = $json['rates'];
                $updated_at = isset( $json['date'] ) ? sanitize_text_field( $json['date'] ) : current_time( 'Y-m-d' );
                set_transient( $cache_key, array( 'rates' => $rates, 'updated' => $updated_at ), DAY_IN_SECONDS );
            }
        }
    }
}
add_action( 'u_correio68_fx_daily_event', 'u_correio68_fx_cron_refresh' );

/**
 * Render Callback: Destaques Home
 */
function u_correio68_render_destaques_home( $attributes ) {
    $category_id = isset( $attributes['categoryId'] ) ? intval( $attributes['categoryId'] ) : 0;
    
    $args_all = array(
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'order'          => 'DESC',
        'orderby'        => 'date',
        'ignore_sticky_posts' => true,
    );
    if ( $category_id ) {
        $args_all['cat'] = $category_id;
    }
    
    $query_all = new WP_Query( $args_all );
    
    ob_start();
    if ( $query_all->have_posts() ) :
        $posts = $query_all->posts;
        $post_big = isset($posts[0]) ? $posts[0] : null;
        $posts_small = array_slice($posts, 1, 2);
        ?>
        <div class="row destaques-home-wrapper d-none d-md-flex">
                    <!-- Big Post -->
                    <div class="col-md-8">
                        <?php if ( $post_big ) : 
                            $post = $post_big; 
                            setup_postdata( $post ); 
                            if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost();
                        ?>
                            <div class="card spaces text-white" id="post-<?php echo $post->ID; ?>">
                                <?php 
                                if ( class_exists( 'PG_Image' ) ) {
                                    echo PG_Image::getPostImage( $post->ID, 'destatquegrande', array(
                                        'class' => 'imagem-destaque w-100'
                                    ), null, null );
                                } else {
                                    echo get_the_post_thumbnail( $post->ID, 'destatquegrande', array( 'class' => 'imagem-destaque w-100' ) );
                                }
                                ?>
                                <div class="card-img-overlay gradiente space">
                                    <div class="tituloD">
                                        <?php 
                                        $cor = get_field( 'cor', $post->ID );
                                        $icones = get_field( 'icones', $post->ID );
                                        $chamada = get_field( 'chamada', $post->ID );
                                        if ( $chamada ) : ?>
                                            <span class="badge badge-light text-white bg-orange badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important;"> 
                                                <ion-icon class="<?php echo esc_attr($icones); ?>"></ion-icon> 
                                                <span><?php echo esc_html($chamada); ?></span>
                                            </span>
                                            <br>
                                        <?php endif; ?>
                                        <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"><span class="TituloGrande text-white text-shadow"><?php echo get_the_title( $post->ID ); ?></span></a>
                                    </div>
                                </div>
                            </div>
                        <?php wp_reset_postdata(); endif; ?>
                    </div>

                    <!-- Small Posts -->
                    <div class="col-md-4">
                        <div>
                            <?php foreach ( $posts_small as $post ) : 
                                setup_postdata( $post ); 
                                if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost();
                            ?>
                                <div class="card spaces text-white" id="post-<?php echo $post->ID; ?>">
                                    <?php 
                                    if ( class_exists( 'PG_Image' ) ) {
                                        echo PG_Image::getPostImage( $post->ID, 'destatquegrande', array(
                                            'class' => 'w-100',
                                            'sizes' => 'max-width(320px) 85vw, max-width(640px) 510px, max-width(768px) 70vw, max-width(1024px) 60vw, max-width(1280px) 730px, 730px'
                                        ), 'both', null );
                                    } else {
                                        echo get_the_post_thumbnail( $post->ID, 'destatquegrande', array( 'class' => 'w-100' ) );
                                    }
                                    ?>
                                    <div class="card-img-overlay grad gradiente space">
                                        <div class="tituloD">
                                            <?php 
                                            $cor = get_field( 'cor', $post->ID );
                                            $icones = get_field( 'icones', $post->ID );
                                            $chamada = get_field( 'chamada', $post->ID );
                                            if ( $chamada ) : ?>
                                                <span class="badge badge-light text-white bg-orange badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important;"> 
                                                    <ion-icon class="<?php echo esc_attr($icones); ?>"></ion-icon> 
                                                    <span><?php echo esc_html($chamada); ?></span>
                                                </span>
                                            <?php endif; ?>
                                            <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"><br><span class="TituloGrande2 text-shadow text-white"><?php echo get_the_title( $post->ID ); ?></span></a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; wp_reset_postdata(); ?>
                        </div>
                </div>
        </div>

        <!-- Mobile Slider Version -->
        <div class="destaques-home-mobile-slider d-md-none">
            <?php 
            // Build a unified list of up to 3 posts (1 big + 2 small) for mobile slider
            $mobile_posts = array();
            if ( $post_big ) { $mobile_posts[] = $post_big; }
            foreach ( $posts_small as $p ) { $mobile_posts[] = $p; }
            foreach ( $mobile_posts as $post ) : setup_postdata( $post ); if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost(); ?>
                <div class="px-2">
                    <div class="position-relative overflow-hidden" style="height: 360px; border-radius: 6px; border: 1px solid #e9ecef;">
                        <?php 
                        if ( has_post_thumbnail( $post->ID ) ) {
                            if ( class_exists( 'PG_Image' ) ) {
                                echo PG_Image::getPostImage( $post->ID, 'destatquegrande', array(
                                    'class' => 'w-100 h-100',
                                    'style' => 'object-fit: cover;'
                                ), 'both', null );
                            } else {
                                echo get_the_post_thumbnail( $post->ID, 'destatquegrande', array(
                                    'class' => 'w-100 h-100',
                                    'style' => 'object-fit: cover;'
                                ) );
                            }
                        } else {
                            echo '<div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light"><i class="fa fa-image fa-2x text-muted"></i></div>';
                        }
                        ?>
                        <div class="card-img-overlay gradiente space d-flex flex-column justify-content-end">
                            <?php 
                            $cor = function_exists('get_field') ? get_field( 'cor', $post->ID ) : '';
                            $icones = function_exists('get_field') ? get_field( 'icones', $post->ID ) : '';
                            $chamada = function_exists('get_field') ? get_field( 'chamada', $post->ID ) : '';
                            if ( !empty($chamada) ) : ?>
                                <span class="badge badge-light text-white bg-orange badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important;"> 
                                    <ion-icon class="<?php echo esc_attr($icones); ?>"></ion-icon> 
                                    <span><?php echo esc_html($chamada); ?></span>
                                </span>
                                <br>
                            <?php endif; ?>
                            <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
                                <span class="TituloGrande text-white text-shadow"><?php echo get_the_title( $post->ID ); ?></span>
                            </a>
                        </div>
                        <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="stretched-link" aria-label="Abrir destaque"></a>
                    </div>
                </div>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>

        <script>
        (function($){
            $(function(){
                var $slider = $('.destaques-home-mobile-slider');
                if ($slider.length && typeof $slider.slick === 'function' && !$slider.hasClass('slick-initialized')) {
                    $slider.slick({
                        dots: true,
                        arrows: false,
                        slidesToShow: 1,
                        adaptiveHeight: true
                    });
                }
            });
        })(jQuery);
        </script>
    <?php else: ?>
        <div class="alert alert-info">Nenhum post encontrado para os destaques.</div>
    <?php endif;
    return ob_get_clean();
}

/**
 * Render Callback: News Grid
 */
function u_correio68_render_news_grid( $attributes ) {
    $categoryId    = isset( $attributes['categoryId'] ) ? intval( $attributes['categoryId'] ) : 0;
    $numberOfPosts = isset( $attributes['numberOfPosts'] ) ? intval( $attributes['numberOfPosts'] ) : 9;
    $offset        = isset( $attributes['offset'] ) ? intval( $attributes['offset'] ) : 0;
    $columns       = isset( $attributes['columns'] ) ? intval( $attributes['columns'] ) : 3;
    $show_pagination = ! empty( $attributes['paginate'] );
    // Support excluding specific post IDs
    $excludeIdsAttr = array();
    if ( isset( $attributes['excludeIds'] ) ) {
        $excludeIdsAttr = is_array( $attributes['excludeIds'] ) ? $attributes['excludeIds'] : array( $attributes['excludeIds'] );
    }
    if ( isset( $attributes['excludeId'] ) ) {
        $excludeIdsAttr[] = $attributes['excludeId'];
    }
    $excludeIds = array();
    foreach ( (array) $excludeIdsAttr as $eid ) {
        $eid = intval( $eid );
        if ( $eid > 0 ) { $excludeIds[] = $eid; }
    }
    if ( ! empty( $excludeIds ) ) {
        $excludeIds = array_values( array_unique( $excludeIds ) );
    }
    
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

    $typography = u_correio68_resolve_typography( $attributes );
    $titleStyle = $typography['style'];

    // Decide data source: current archive/search query vs fresh query
    $use_current_archive = ( $categoryId === 0 ) && ( is_archive() || is_search() );

    if ( $use_current_archive && isset( $GLOBALS['wp_query'] ) && $GLOBALS['wp_query'] instanceof WP_Query ) {
        // Build a safe duplicate query using IDs from the main query to avoid advancing the global loop
        $main = $GLOBALS['wp_query'];
        $ids = array();
        if ( ! empty( $main->posts ) && is_array( $main->posts ) ) {
            foreach ( $main->posts as $p ) {
                if ( isset( $p->ID ) ) { $ids[] = intval( $p->ID ); }
            }
        }
        // Exclude specified IDs
        if ( ! empty( $excludeIds ) ) {
            $ids = array_values( array_diff( $ids, $excludeIds ) );
        }
        if ( empty( $ids ) ) {
            $query = new WP_Query( array( 'post__in' => array(0) ) );
        } else {
            $query = new WP_Query( array(
                'post_type'           => 'post',
                'post__in'            => $ids,
                'orderby'             => 'post__in',
                'posts_per_page'      => count( $ids ),
                'ignore_sticky_posts' => true,
                'post_status'         => 'publish',
                'no_found_rows'       => ! $show_pagination, // pagination uses main query's max_num_pages
            ) );
        }
    } else {
        // Build a fresh query; if paginating, rely on paged and ignore offset for correct counts
        $current_page = max( 1, intval( get_query_var( 'paged' ) ) );
        $args = array(
            'post_type'           => 'post',
            'posts_per_page'      => max(1, $numberOfPosts),
            'order'               => 'DESC',
            'orderby'             => 'date',
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'no_found_rows'       => ! $show_pagination,
        );
        if ( $show_pagination ) {
            $args['paged'] = $current_page;
        } else {
            $args['offset'] = max( 0, $offset );
        }
        if ( ! empty( $excludeIds ) ) {
            $args['post__not_in'] = $excludeIds;
        }
        $args = u_correio68_apply_category_filter( $args, $categoryId );
        $query = new WP_Query( $args );
    }
    
    ob_start();
    if ( $query->have_posts() ) : ?>
        <div class="row news-grid" style="--columns:<?php echo intval($columns); ?>;">
                <?php while ( $query->have_posts() ) : $query->the_post(); 
                    if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost();
                ?>
                    <div class="<?php echo esc_attr($col_class); ?> mb-4" id="post-<?php the_ID(); ?>">
                        <article class="news-item" style="transition: opacity 0.2s ease;">
                            <?php 
                            $cor = function_exists('get_field') ? get_field( 'cor' ) : '';
                            $icones = function_exists('get_field') ? get_field( 'icones' ) : '';
                            $chamada = function_exists('get_field') ? get_field( 'chamada' ) : '';
                            if ( !empty($chamada) ) : ?>
                                <div class="mb-2">
                                    <span class="badge badge-light text-white badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important; font-size: 0.7rem; padding: 0.25rem 0.5rem;"> 
                                        <ion-icon class="<?php echo esc_attr($icones); ?>" style="font-size: 0.8rem;"></ion-icon> 
                                        <span><?php echo esc_html($chamada); ?></span>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block news-image-wrapper overflow-hidden mb-3" style="height: 180px; border-radius: 4px; border: 1px solid #e9ecef;">
                                <?php 
                                if ( has_post_thumbnail() ) :
                                    if ( class_exists( 'PG_Image' ) ) {
                                        echo PG_Image::getPostImage( get_the_ID(), 'destaque', array(
                                            'class' => 'w-100 h-100',
                                            'style' => 'object-fit: cover;'
                                        ), 'both', null );
                                    } else {
                                        echo get_the_post_thumbnail( get_the_ID(), 'destaque', array(
                                            'class' => 'w-100 h-100',
                                            'style' => 'object-fit: cover;'
                                        ) );
                                    }
                                else:
                                    echo '<div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light"><i class="fa fa-image fa-2x text-muted"></i></div>';
                                endif;
                                ?>
                            </a>
                            
                            <div class="news-content">
                                <a href="<?php echo esc_url( get_permalink() ); ?>" class="text-decoration-none">
                                    <h5 class="mb-2" style="<?php echo esc_attr( $titleStyle ); ?> line-height: 1.35; font-size: 1.1rem; color: #333; font-weight: 700;">
                                        <?php the_title(); ?>
                                    </h5>
                                </a>
                                
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <small class="text-muted" style="font-size: 0.8rem;">
                                        <i class="fa fa-clock-o"></i> <?php echo get_the_date('d/m/Y'); ?>
                                    </small>
                                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="text-primary" style="font-size: 0.85rem; font-weight: 500;">
                                        Ler mais →
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <?php if ( $show_pagination ) : ?>
            <?php
            // Determine pagination context and totals
            $big = 999999999;
            $current = max( 1, intval( get_query_var( 'paged' ) ) );
            if ( $use_current_archive && isset( $GLOBALS['wp_query'] ) && $GLOBALS['wp_query'] instanceof WP_Query ) {
                $total = max( 1, intval( $GLOBALS['wp_query']->max_num_pages ) );
            } else {
                $total = max( 1, intval( $query->max_num_pages ) );
            }

            if ( $total > 1 ) {
                $links = paginate_links( array(
                    'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format'    => '?paged=%#%',
                    'current'   => $current,
                    'total'     => $total,
                    'prev_text' => '« Anterior',
                    'next_text' => 'Próxima »',
                    'type'      => 'array',
                ) );
                if ( is_array( $links ) ) {
                    echo '<nav class="u68-pagination" aria-label="Paginação"><ul class="pagination justify-content-center">';
                    foreach ( $links as $l ) {
                        $is_current = strpos( $l, 'current' ) !== false;
                        $class = 'page-item' . ( $is_current ? ' active' : '' );
                        $l = str_replace( 'page-numbers', 'page-link page-numbers', $l );
                        echo '<li class="' . esc_attr( $class ) . '">' . $l . '</li>';
                    }
                    echo '</ul></nav>';
                }
            }
            ?>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info w-100" role="alert"><i class="fa fa-info-circle"></i> Nenhuma notícia encontrada.</div>
    <?php endif;
    return ob_get_clean();
}

/**
 * Render Callback: Category Highlight (1 Big + 3 List)
 */
function u_correio68_render_category_highlight( $attributes ) {
    $categoryId = isset( $attributes['categoryId'] ) ? intval( $attributes['categoryId'] ) : 0;
    $title      = isset( $attributes['title'] ) ? $attributes['title'] : '';
    $bigCount   = isset( $attributes['bigCount'] ) ? max(0, intval( $attributes['bigCount'] )) : 1;
    $listCount  = isset( $attributes['listCount'] ) ? max(0, intval( $attributes['listCount'] )) : 3;

    $typography = u_correio68_resolve_typography( $attributes );
    $titleStyle = $typography['style'];

    // Query total posts based on counts
    $total = max(1, $bigCount + $listCount);
    $args = array(
        'post_type'           => 'post',
        'posts_per_page'      => $total,
        'order'               => 'DESC',
        'orderby'             => 'date',
        'ignore_sticky_posts' => true,
    );

    $args = u_correio68_apply_category_filter( $args, $categoryId );

    $query = new WP_Query( $args );
    
    ob_start();
    if ( $query->have_posts() ) : 
        $posts = $query->posts;
        $big_posts  = array_slice( $posts, 0, $bigCount );
        $posts_list = array_slice( $posts, $bigCount, $listCount );
        ?>
        <div>
            <?php if ( $title ) : ?>
                <h3><?php echo esc_html( $title ); ?></h3>
            <?php endif; ?>

            <!-- Big Posts -->
            <?php foreach ( $big_posts as $post ) : setup_postdata( $post ); if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost(); ?>
                <div class="primeiro mb-3" id="post-<?php echo $post->ID; ?>" style="transition: opacity 0.2s ease;">
                    <div class="news-image-wrapper overflow-hidden mb-2" style="height: 200px; border-radius: 4px; border: 1px solid #e9ecef;">
                        <?php 
                        if ( class_exists( 'PG_Image' ) ) {
                            echo PG_Image::getPostImage( $post->ID, 'destaque', array(
                                'class' => 'w-100 h-100',
                                'style' => 'object-fit: cover;'
                            ), 'both', null );
                        } else {
                            echo get_the_post_thumbnail( $post->ID, 'destaque', array(
                                'class' => 'w-100 h-100',
                                'style' => 'object-fit: cover;'
                            ) );
                        }
                        ?>
                    </div>
                    <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="text-decoration-none">
                        <h5 class="mb-3" style="<?php echo esc_attr( $titleStyle ); ?> line-height: 1.35; font-size: 1.25rem; color: #333; font-weight: 700; "><?php echo get_the_title( $post->ID ); ?></h5>
                    </a>
                </div>
            <?php endforeach; wp_reset_postdata(); ?>

            <!-- List Posts (Text Only) -->
            <div>
                <?php foreach ( $posts_list as $post ) : 
                    setup_postdata( $post );
                    if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost();
                ?>
                    <div class="mb-2 pb-2 border-bottom" id="post-<?php echo $post->ID; ?>" style="transition: opacity 0.2s ease;">
                        <?php 
                        $cor = function_exists('get_field') ? get_field( 'cor', $post->ID ) : '';
                        $icones = function_exists('get_field') ? get_field( 'icones', $post->ID ) : '';
                        $chamada = function_exists('get_field') ? get_field( 'chamada', $post->ID ) : '';
                        if ( !empty($chamada) ) : ?>
                            <div class="mb-2">
                                <span class="badge badge-light text-white badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important; font-size: 0.7rem; padding: 0.25rem 0.5rem;"> 
                                    <ion-icon class="<?php echo esc_attr($icones); ?>" style="font-size: 0.8rem;"></ion-icon> 
                                    <span><?php echo esc_html($chamada); ?></span>
                                </span>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 mr-3" style="width: 90px; height: 90px; overflow: hidden; border-radius: 4px;">
                                <?php 
                                if ( has_post_thumbnail( $post->ID ) ) :
                                    if ( class_exists( 'PG_Image' ) ) {
                                        echo PG_Image::getPostImage( $post->ID, 'thumbnail', array(
                                            'class' => 'w-100 h-100',
                                            'style' => 'object-fit: cover;'
                                        ), null, null );
                                    } else {
                                        echo get_the_post_thumbnail( $post->ID, 'thumbnail', array(
                                            'class' => 'w-100 h-100',
                                            'style' => 'object-fit: cover;'
                                        ) );
                                    }
                                else:
                                    echo '<div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light"><i class="fa fa-image text-muted"></i></div>';
                                endif;
                                ?>
                            </div>
                            <div class="flex-grow-1">
                                <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="text-decoration-none">
                                    <h6 class="mb-1" style="line-height: 1.4; font-size: 0.95rem; color: #333; font-weight: 600; <?php echo esc_attr( $titleStyle ); ?>"><?php echo get_the_title( $post->ID ); ?></h6>
                                </a>
                                <small class="text-muted" style="font-size: 0.8rem;"><i class="fa fa-clock-o"></i> <?php echo get_the_date('d/m/Y', $post->ID); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
        </div>
    <?php else: ?>
        <div><p>Nenhum post encontrado.</p></div>
    <?php endif;
    return ob_get_clean();
}

/**
 * Render Callback: Colunistas Grid
 */
function u_correio68_render_colunistas_grid( $attributes, $content ) {
    return '<div class="row colunistas-grid">' . $content . '</div>';
}

/**
 * Render Callback: Colunista Item
 */
function u_correio68_render_colunista_item( $attributes ) {
    $name = isset($attributes['name']) ? $attributes['name'] : '';
    $columnTitle = isset($attributes['columnTitle']) ? $attributes['columnTitle'] : '';
    $imageUrl = isset($attributes['imageUrl']) ? $attributes['imageUrl'] : '';
    $categoryId = isset($attributes['categoryId']) ? intval($attributes['categoryId']) : 0;

    ob_start();
    ?>
    <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-3">
        <div class="our-team colunista-card">
            <div class="picture">
                <?php if ( $imageUrl ) : ?>
                    <img class="img-fluid" src="<?php echo esc_url( $imageUrl ); ?>" alt="<?php echo esc_attr( $columnTitle ); ?>">
                <?php else: ?>
                    <div style="background:#ccc; height:200px; display:flex; align-items:center; justify-content:center;">Foto</div>
                <?php endif; ?>
            </div>
            <div class="team-content">
                <h5 class="name"><?php echo esc_html( $columnTitle ); ?></h5>
                <span class="small"><?php echo esc_html( 'por ' . $name ); ?></span>
            </div>

            <?php
            if ( $categoryId ) {
                $args = [
                    'posts_per_page' => 1,
                    'cat'            => $categoryId,
                    'order'          => 'DESC'
                ];
                $query = new WP_Query($args);

                if ($query->have_posts()) :
                    while ($query->have_posts()) : $query->the_post();
                        if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost();
                        ?>
                        <a href="<?php the_permalink(); ?>">
                            <p><?php the_title(); ?></p>
                        </a>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
            }
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render Callback: Destaque Misto
 */
function u_correio68_render_destaque_misto( $attributes ) {
    global $post;
    $categoryId = isset( $attributes['categoryId'] ) ? intval( $attributes['categoryId'] ) : 0;

    $typography = u_correio68_resolve_typography( $attributes, '#FFFFFF' );
    $titleStyle = $typography['style'];
    
    // Total posts: 2 (Large) + 6 (List) = 8.
    $args = array(
        'post_type'           => 'post',
        'posts_per_page'      => 8, 
        'ignore_sticky_posts' => true,
        'order'               => 'DESC',
        'orderby'             => 'date',
        'post_status'         => 'publish',
    );

    $args = u_correio68_apply_category_filter( $args, $categoryId );

    $query = new WP_Query( $args );
    
    ob_start();
    if( $query->have_posts() ) :
        $posts = $query->posts;
        ?>
        <div class="destaque-misto-wrapper my-4">
            <!-- Row 1: 2 Large Highlights -->
            <div class="row mb-4">
                <?php 
                // First 2 posts
                for($i = 0; $i < 2; $i++): 
                    if(isset($posts[$i])): 
                        $post = $posts[$i];
                        setup_postdata($post);
                        ?>
                        <div class="col-md-6 mb-3">
                             <div <?php post_class('card spaces text-white h-100'); ?> id="post-<?php the_ID(); ?>">
                                <?php 
                                if ( class_exists('PG_Image') ) {
                                    echo PG_Image::getPostImage(get_the_ID(), 'destatquegrande', array('class' => 'imagem-destaque-misto w-100 h-100', 'style' => 'object-fit: cover;'), null, null);
                                } else {
                                    echo get_the_post_thumbnail(get_the_ID(), 'destatquegrande', array('class' => 'imagem-destaque-misto w-100 h-100', 'style' => 'object-fit: cover;'));
                                }
                                ?>
                                <div class="card-img-overlay gradiente space d-flex flex-column justify-content-end" style="background: none !important;">
                                    <div class="tituloD">
                                        <h3 class="mt-2 mb-3" style="<?php echo esc_attr( $titleStyle ); ?> line-height: 1.35; font-size: 1.35rem; font-weight: 700;"><a href="<?php the_permalink(); ?>" class="text-white"><?php the_title(); ?></a></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                    endif;
                endfor; 
                ?>
            </div>

            <!-- Row 2: List (Full Width) -->
            <div class="row">
                <!-- List (Full Width) -->
                <div class="col-12">
                    <div class="row">
                        <?php 
                        // Next 6 posts (index 2 to 7)
                        for($i = 2; $i < 8; $i++):
                             if(isset($posts[$i])):
                                $post = $posts[$i];
                                setup_postdata($post);
                                ?>
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="media list-item">
                                        <div class="list-thumb">
                                              <?php 
                                              if ( class_exists('PG_Image') ) {
                                                  echo PG_Image::getPostImage(get_the_ID(), 'thumbnail', array('class' => 'img-fluid rounded w-100 h-100', 'style' => 'object-fit: cover;'), null, null);
                                              } else {
                                                  echo get_the_post_thumbnail(get_the_ID(), 'thumbnail', array('class' => 'img-fluid rounded w-100 h-100', 'style' => 'object-fit: cover;'));
                                              }
                                              ?>
                                        </div>
                                        <div class="media-body list-content">
                                            <h5 class="mt-0" style="<?php echo esc_attr( $titleStyle ); ?>"><a href="<?php the_permalink(); ?>" class="text-dark"><?php the_title(); ?></a></h5>
                                            <small class="text-muted"><?php echo get_the_date(); ?></small>
                                        </div>
                                    </div>
                                </div>
                                <?php
                             endif;
                        endfor;
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        wp_reset_postdata();
    else:
        echo '<div class="alert alert-warning">Nenhum post encontrado.</div>';
    endif;
    return ob_get_clean();
}

/**
 * Render Callback: Top Most Read (Top N)
 */
function u_correio68_render_top_most_read( $attributes ) {
    $title      = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : 'Mais lidas';
    $count      = isset( $attributes['count'] ) ? max(1, intval( $attributes['count'] )) : 5;
    $meta_key   = isset( $attributes['metaKey'] ) ? sanitize_key( $attributes['metaKey'] ) : 'post_views_count';
    $categoryId = isset( $attributes['categoryId'] ) ? intval( $attributes['categoryId'] ) : 0;

    // Primary query: order by meta value (views)
    $args = array(
        'post_type'           => 'post',
        'posts_per_page'      => $count,
        'orderby'             => 'meta_value_num',
        'meta_key'            => $meta_key,
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
        'post_status'         => 'publish',
    );
    $args = u_correio68_apply_category_filter( $args, $categoryId );

    $query = new WP_Query( $args );

    // Fallback: if no results (meta missing), try comment_count
    if ( ! $query->have_posts() ) {
        $args_fallback = array(
            'post_type'           => 'post',
            'posts_per_page'      => $count,
            'orderby'             => 'comment_count',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
        );
        $args_fallback = u_correio68_apply_category_filter( $args_fallback, $categoryId );
        $query = new WP_Query( $args_fallback );
    }

    ob_start();
    if ( $title ) {
        echo '<h3 class="top-most-read-title">' . esc_html( $title ) . '</h3>';
    }
    if ( $query->have_posts() ) :
        echo '<ol class="top-most-read">';
        $rank = 1;
        while ( $query->have_posts() ) : $query->the_post();
            if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost();
            echo '<li class="top-item" id="post-' . get_the_ID() . '">';
            echo '<span class="rank" aria-hidden="true">' . intval($rank) . '</span>';
            echo '<a href="' . esc_url( get_permalink() ) . '" class="top-link" title="' . esc_attr( get_the_title() ) . '">' . get_the_title() . '</a>';
            echo '</li>';
            $rank++;
        endwhile;
        echo '</ol>';
        wp_reset_postdata();
    else:
        echo '<p class="text-muted">Nenhuma matéria encontrada.</p>';
    endif;
    return ob_get_clean();
}

/**
 * Render Callback: Weather Block (Open-Meteo)
 */
function u_correio68_render_weather( $attributes ) {
    $city      = isset( $attributes['cityName'] ) ? sanitize_text_field( $attributes['cityName'] ) : '';
    $lat       = isset( $attributes['latitude'] ) ? sanitize_text_field( $attributes['latitude'] ) : '';
    $lon       = isset( $attributes['longitude'] ) ? sanitize_text_field( $attributes['longitude'] ) : '';
    $units     = ( isset( $attributes['units'] ) && $attributes['units'] === 'f' ) ? 'f' : 'c';
    $showWind  = ! empty( $attributes['showWind'] );
    $showRain  = ! empty( $attributes['showRain'] );

    // Resolve coordinates via geocoding if city provided
    if ( empty( $lat ) || empty( $lon ) ) {
        if ( ! empty( $city ) ) {
            $geo_url = add_query_arg( array(
                'name' => $city,
                'count' => 1
            ), 'https://geocoding-api.open-meteo.com/v1/search' );
            $geo_res = wp_remote_get( $geo_url, array( 'timeout' => 8 ) );
            if ( ! is_wp_error( $geo_res ) ) {
                $geo_body = wp_remote_retrieve_body( $geo_res );
                $geo_json = json_decode( $geo_body, true );
                if ( ! empty( $geo_json['results'][0] ) ) {
                    $lat = (string) $geo_json['results'][0]['latitude'];
                    $lon = (string) $geo_json['results'][0]['longitude'];
                    $city = ! empty( $geo_json['results'][0]['name'] ) ? $geo_json['results'][0]['name'] : $city;
                }
            }
        }
    }

    if ( empty( $lat ) || empty( $lon ) ) {
        return '<div class="weather-block"><p class="text-muted">Defina uma cidade ou coordenadas.</p></div>';
    }

    // Cache current weather to avoid frequent external calls
    $cache_key = 'u68_weather_' . md5( $lat . '_' . $lon . '_' . $units );
    $data = get_transient( $cache_key );

    if ( false === $data ) {
        $params = array(
            'latitude'        => $lat,
            'longitude'       => $lon,
            'current_weather' => 'true',
            'hourly'          => 'precipitation',
        );
        $api_url = add_query_arg( $params, 'https://api.open-meteo.com/v1/forecast' );
        $res = wp_remote_get( $api_url, array( 'timeout' => 8 ) );
        if ( is_wp_error( $res ) ) {
            return '<div class="weather-block"><p class="text-muted">Erro ao obter clima.</p></div>';
        }
        $body = wp_remote_retrieve_body( $res );
        $json = json_decode( $body, true );
        if ( empty( $json['current_weather'] ) ) {
            return '<div class="weather-block"><p class="text-muted">Dados de clima indisponíveis.</p></div>';
        }
        $data = array(
            'temperature'   => isset( $json['current_weather']['temperature'] ) ? floatval( $json['current_weather']['temperature'] ) : null,
            'windspeed'     => isset( $json['current_weather']['windspeed'] ) ? floatval( $json['current_weather']['windspeed'] ) : null,
            'winddirection' => isset( $json['current_weather']['winddirection'] ) ? intval( $json['current_weather']['winddirection'] ) : null,
            'weathercode'   => isset( $json['current_weather']['weathercode'] ) ? intval( $json['current_weather']['weathercode'] ) : null,
            'time'          => isset( $json['current_weather']['time'] ) ? sanitize_text_field( $json['current_weather']['time'] ) : '',
        );
        set_transient( $cache_key, $data, 10 * MINUTE_IN_SECONDS );
    }

    // Map weathercode to simple descriptor
    $code = intval( $data['weathercode'] );
    $desc = 'Clima';
    $icon = 'clear';
    $fa   = 'fa-sun-o';
    // Open-Meteo weather codes mapping (simplified)
    if ( in_array( $code, array(0) ) ) { $desc = 'Céu limpo'; $icon = 'clear'; $fa = 'fa-sun-o'; }
    elseif ( in_array( $code, array(1,2) ) ) { $desc = 'Parcialmente nublado'; $icon = 'cloudy'; $fa = 'fa-cloud'; }
    elseif ( in_array( $code, array(3) ) ) { $desc = 'Nublado'; $icon = 'cloudy'; $fa = 'fa-cloud'; }
    elseif ( in_array( $code, array(45,48) ) ) { $desc = 'Neblina'; $icon = 'mist'; $fa = 'fa-cloud'; }
    elseif ( in_array( $code, array(51,53,55,56,57) ) ) { $desc = 'Garoa'; $icon = 'rain'; $fa = 'fa-tint'; }
    elseif ( in_array( $code, array(61,63,65,66,67) ) ) { $desc = 'Chuva'; $icon = 'rain'; $fa = 'fa-umbrella'; }
    elseif ( in_array( $code, array(71,73,75,77) ) ) { $desc = 'Neve'; $icon = 'snow'; $fa = 'fa-snowflake-o'; }
    elseif ( in_array( $code, array(80,81,82) ) ) { $desc = 'Aguaceiros'; $icon = 'rain'; $fa = 'fa-umbrella'; }
    elseif ( in_array( $code, array(95,96,99) ) ) { $desc = 'Trovoadas'; $icon = 'storm'; $fa = 'fa-bolt'; }

    // Units conversion
    $temp = $data['temperature'];
    if ( $units === 'f' && is_numeric( $temp ) ) {
        $temp = round( ( $temp * 9/5 ) + 32, 1 );
    }
    $temp_unit = $units === 'f' ? '°F' : '°C';

    $windspeed = $data['windspeed'];
    $wind_unit = 'km/h';

    ob_start();
    ?>
    <div class="weather-block minimal card spaces p-3 weather-eyecandy">
        <div class="d-flex align-items-center">
            <i class="fa <?php echo esc_attr( $fa ); ?> weather-fa-icon" aria-hidden="true"></i>
            <div class="ml-3">
                <div class="h4 mb-1" style="font-weight:700;">&nbsp;<?php echo esc_html( round( $temp ) ) . ' ' . esc_html( $temp_unit ); ?></div>
                <div class="small text-muted"><?php echo esc_html( $desc ); ?><?php echo $city ? ' • ' . esc_html( $city ) : ''; ?></div>
            </div>
        </div>
        <div class="mt-2 d-flex gap-3">
            <?php if ( $showWind && is_numeric( $windspeed ) ) : ?>
                <div class="small text-muted">Vento: <?php echo esc_html( round( $windspeed ) ) . ' ' . esc_html( $wind_unit ); ?></div>
            <?php endif; ?>
            <?php if ( $showRain && in_array( $icon, array('rain','storm') ) ) : ?>
                <div class="small text-muted">Chuva: ativo</div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render Callback: Currency Monitor (exchangerate.host)
 */
function u_correio68_render_currency_monitor( $attributes ) {
    $provider    = isset( $attributes['provider'] ) ? sanitize_text_field( $attributes['provider'] ) : 'exchangerate';
    $base        = isset( $attributes['base'] ) ? strtoupper( sanitize_text_field( $attributes['base'] ) ) : 'BRL';
    $show_brl    = ! empty( $attributes['showBRL'] );
    $show_usd    = ! empty( $attributes['showUSD'] );
    $show_bob    = ! empty( $attributes['showBOB'] );
    $show_pen    = ! empty( $attributes['showPEN'] );
    $spread_pct  = isset( $attributes['spread'] ) ? floatval( $attributes['spread'] ) : 0.5;
    $show_updated= ! empty( $attributes['showUpdated'] );

    // Symbols list
    $symbols = array();
    if ( $show_usd ) $symbols[] = 'USD';
    if ( $show_bob ) $symbols[] = 'BOB';
    if ( $show_pen ) $symbols[] = 'PEN';
    // BRL will be displayed as 1.0000 if requested

    $names = array(
        'BRL' => 'Real Brasileiro',
        'USD' => 'Dólar Americano',
        'BOB' => 'Boliviano',
        'PEN' => 'Sol Peruano',
    );

    // Fetch rates
    $rates = array();
    $updated_at = '';
    if ( ! empty( $symbols ) ) {
        $cache_key = 'u68_fx_' . md5( $provider . '|' . $base . '|' . implode(',', $symbols) );
        $cached = get_transient( $cache_key );
        if ( false === $cached ) {
            if ( $provider === 'frankfurter' ) {
                $api_url = add_query_arg( array(
                    'from' => $base,
                    'to'   => implode(',', $symbols),
                ), 'https://api.frankfurter.app/latest' );
                $res = wp_remote_get( $api_url, array( 'timeout' => 8 ) );
                if ( ! is_wp_error( $res ) ) {
                    $body = wp_remote_retrieve_body( $res );
                    $json = json_decode( $body, true );
                    if ( ! empty( $json['rates'] ) ) {
                        $rates = $json['rates'];
                        $updated_at = isset( $json['date'] ) ? sanitize_text_field( $json['date'] ) : '';
                        set_transient( $cache_key, array('rates'=>$rates,'updated'=>$updated_at), 10 * MINUTE_IN_SECONDS );
                    }
                }
            } else {
                $api_url = add_query_arg( array(
                    'base'    => $base,
                    'symbols' => implode(',', $symbols),
                ), 'https://api.exchangerate.host/latest' );
                $res = wp_remote_get( $api_url, array( 'timeout' => 8 ) );
                if ( ! is_wp_error( $res ) ) {
                    $body = wp_remote_retrieve_body( $res );
                    $json = json_decode( $body, true );
                    if ( ! empty( $json['rates'] ) ) {
                        $rates = $json['rates'];
                        $updated_at = isset( $json['date'] ) ? sanitize_text_field( $json['date'] ) : '';
                        set_transient( $cache_key, array('rates'=>$rates,'updated'=>$updated_at), 10 * MINUTE_IN_SECONDS );
                    }
                }
            }
        } else {
            $rates = isset($cached['rates']) ? $cached['rates'] : array();
            $updated_at = isset($cached['updated']) ? $cached['updated'] : '';
        }
    }

    // Compute buy/sell using spread percentage around mid-market
    $spread = max(0.0, $spread_pct) / 100.0;

    ob_start();
    ?>
    <div class="currency-monitor card spaces p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>Mercado de Câmbio</strong>
            <?php if ( $show_updated && $updated_at ) : ?>
                <small class="text-muted">Atualizado: <?php echo esc_html( $updated_at ); ?></small>
            <?php endif; ?>
        </div>
        <div class="small text-muted mb-2">Base: BRL • Spread: <?php echo esc_html( number_format( $spread_pct, 1, ',', '.' ) ); ?>%</div>
        <div class="cm-rows">
            <?php if ( $show_brl ) : ?>
                <div class="cm-row">
                    <div class="cm-name">Real Brasileiro (BRL)</div>
                    <div class="cm-values">
                        <span class="cm-buy">Compra: 1.0000</span>
                        <span class="cm-sell">Venda: 1.0000</span>
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ( $symbols as $code ) : 
                $mid = isset( $rates[$code] ) ? floatval( $rates[$code] ) : null;
                if ( ! $mid ) continue;
                $buy  = $mid * (1.0 - $spread);
                $sell = $mid * (1.0 + $spread);
            ?>
                <div class="cm-row">
                    <div class="cm-name"><?php echo esc_html( ( isset($names[$code]) ? $names[$code] : $code ) . ' (' . $code . ')' ); ?></div>
                    <div class="cm-values">
                        <span class="cm-buy">Compra: <?php echo esc_html( number_format( $buy, 4, ',', '.' ) ); ?></span>
                        <span class="cm-sell">Venda: <?php echo esc_html( number_format( $sell, 4, ',', '.' ) ); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
