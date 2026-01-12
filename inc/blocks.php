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
        'seideagosto-blocks',
        get_template_directory_uri() . '/assets/js/custom-blocks.js',
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-server-side-render', 'wp-hooks', 'wp-dom-ready' ),
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
        'seideagosto-blocks',
        'seideagostoBlocks',
        array(
            'categories' => $cat_options,
            'sidebars'   => $sidebar_options
        )
    );
    // Backward compatibility - also expose as old name
    wp_localize_script(
        'seideagosto-blocks',
        'uCorreio68Blocks',
        array(
            'categories' => $cat_options,
            'sidebars'   => $sidebar_options
        )
    );

    $typography_default = u_correio68_typography_attribute_schema();
    $typography_light   = u_correio68_typography_attribute_schema( '#FFFFFF' );

    // Register Destaques Home Block
    register_block_type( 'seideagosto/destaques-home', array(
        'editor_script' => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_destaques_home',
        'attributes' => array(
            'categoryId' => array(
                'type' => 'string',
                'default' => '0',
            ),
            'layoutType' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),
    ) );

    // Register Colunistas Grid Block
    register_block_type( 'seideagosto/colunistas-grid', array(
        'editor_script' => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_colunistas_grid',
    ) );

    // Register Colunista Item Block
    register_block_type( 'seideagosto/colunista-item', array(
        'editor_script' => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_colunista_item',
        'attributes' => array(
            'name' => array( 'type' => 'string', 'default' => '' ),
            'columnTitle' => array( 'type' => 'string', 'default' => '' ),
            'imageUrl' => array( 'type' => 'string', 'default' => '' ),
            'categoryId' => array( 'type' => 'string', 'default' => '0' ),
        ),
    ) );

    // Register News Grid Block
    register_block_type( 'seideagosto/news-grid', array(
        'editor_script' => 'seideagosto-blocks',
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
    register_block_type( 'seideagosto/category-highlight', array(
        'editor_script' => 'seideagosto-blocks',
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
    register_block_type( 'seideagosto/destaque-misto', array(
        'editor_script' => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_destaque_misto',
        'attributes' => array_merge(
            array(
                'categoryId' => array( 'type' => 'string', 'default' => '0' ),
            ),
            $typography_light
        ),
    ) );

    // Register Top Most Read (Top 5) Block
    register_block_type( 'seideagosto/top-most-read', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_top_most_read',
        'attributes'      => array(
            'title'      => array( 'type' => 'string', 'default' => 'Mais lidas' ),
            'count'      => array( 'type' => 'number', 'default' => 5 ),
            'metaKey'    => array( 'type' => 'string', 'default' => 'post_views_count' ),
            'categoryId' => array( 'type' => 'string', 'default' => '0' ),
            'period'     => array( 'type' => 'string', 'default' => 'year' ), // year, 90days, 30days, week
        ),
    ) );

    // Register Weather Block
    register_block_type( 'seideagosto/weather', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_weather',
        'attributes'      => array(
            'cityName'      => array( 'type' => 'string', 'default' => '' ),
            'latitude'      => array( 'type' => 'string', 'default' => '' ),
            'longitude'     => array( 'type' => 'string', 'default' => '' ),
            'units'         => array( 'type' => 'string', 'default' => 'c' ), // 'c' Celsius, 'f' Fahrenheit
            'showWind'      => array( 'type' => 'boolean', 'default' => true ),
            'showRain'      => array( 'type' => 'boolean', 'default' => true ),
            'forecastDays'  => array( 'type' => 'number', 'default' => 5 ), // 3, 5, or 7 days
            'showForecast'  => array( 'type' => 'boolean', 'default' => true ), // Show/hide forecast
        ),
    ) );

    // Register Currency Monitor Block
    register_block_type( 'seideagosto/currency-monitor', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_currency_monitor',
        'attributes'      => array(
            'provider'    => array( 'type' => 'string',  'default' => 'currencyfreaks' ),
            'base'        => array( 'type' => 'string', 'default' => 'BRL' ),
            'baseAmount'  => array( 'type' => 'number', 'default' => 100 ),
            'showBRL'     => array( 'type' => 'boolean', 'default' => true ),
            'showUSD'     => array( 'type' => 'boolean', 'default' => true ),
            'showEUR'     => array( 'type' => 'boolean', 'default' => true ),
            'showPEN'     => array( 'type' => 'boolean', 'default' => true ),
            'showARS'     => array( 'type' => 'boolean', 'default' => true ),
            'showBOB'     => array( 'type' => 'boolean', 'default' => true ),
            'showCLP'     => array( 'type' => 'boolean', 'default' => false ),
            'showCOP'     => array( 'type' => 'boolean', 'default' => false ),
            'showUYU'     => array( 'type' => 'boolean', 'default' => false ),
            'showPYG'     => array( 'type' => 'boolean', 'default' => false ),
            'showMXN'     => array( 'type' => 'boolean', 'default' => false ),
            'spread'      => array( 'type' => 'number',  'default' => 0 ), // percent
            'showUpdated' => array( 'type' => 'boolean', 'default' => true ),
            'slidesToShow'=> array( 'type' => 'number',  'default' => 2 ), // slides per view
            'autoplay'    => array( 'type' => 'boolean', 'default' => true ),
            'autoplaySpeed' => array( 'type' => 'number', 'default' => 3000 ),
            'showFlags'   => array( 'type' => 'boolean', 'default' => true ),
            'showNames'   => array( 'type' => 'boolean', 'default' => true ),
        ),
    ) );

    // Register Sidebar Area Block (render a selected widget area)
    register_block_type( 'seideagosto/sidebar-area', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_sidebar_area',
        'attributes'      => array(
            'sidebarId' => array( 'type' => 'string', 'default' => 'right-sidebar' ),
            'title'     => array( 'type' => 'string', 'default' => '' ),
        ),
    ) );

    // ============================================================================
    // BACKWARD COMPATIBILITY: Register old blocks ONLY for rendering frontend
    // (These will be auto-migrated in editor to seideagosto/ namespace)
    // ============================================================================
    register_block_type( 'correio68/destaques-home', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_destaques_home',
        'attributes'      => array( 'categoryId' => array( 'type' => 'string', 'default' => '0' ) ),
    ) );
    register_block_type( 'correio68/colunistas-grid', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_colunistas_grid',
        'attributes'      => array(),
    ) );
    register_block_type( 'correio68/colunista-item', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_colunista_item',
        'attributes'      => array( 'authorId' => array( 'type' => 'number', 'default' => 0 ) ),
    ) );
    register_block_type( 'correio68/news-grid', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_news_grid',
        'attributes'      => array(
            'categoryId' => array( 'type' => 'string', 'default' => '0' ),
            'numberOfPosts' => array( 'type' => 'number', 'default' => 6 ),
            'columns' => array( 'type' => 'number', 'default' => 3 ),
            'paginate' => array( 'type' => 'boolean', 'default' => false ),
        ) + u_correio68_typography_attribute_schema(),
    ) );
    register_block_type( 'correio68/category-highlight', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_category_highlight',
        'attributes'      => array(
            'categoryId' => array( 'type' => 'string', 'default' => '0' ),
            'title' => array( 'type' => 'string', 'default' => '' ),
            'bigCount' => array( 'type' => 'number', 'default' => 1 ),
            'listCount' => array( 'type' => 'number', 'default' => 3 ),
        ) + u_correio68_typography_attribute_schema(),
    ) );
    register_block_type( 'correio68/destaque-misto', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_destaque_misto',
        'attributes'      => array(
            'categoryId' => array( 'type' => 'string', 'default' => '0' ),
            'title' => array( 'type' => 'string', 'default' => '' ),
            'bigCount' => array( 'type' => 'number', 'default' => 1 ),
            'mediumCount' => array( 'type' => 'number', 'default' => 2 ),
            'smallCount' => array( 'type' => 'number', 'default' => 3 ),
        ) + u_correio68_typography_attribute_schema(),
    ) );
    register_block_type( 'correio68/top-most-read', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_top_most_read',
        'attributes'      => array(
            'categoryId' => array( 'type' => 'string', 'default' => '0' ),
            'period' => array( 'type' => 'string', 'default' => 'year' ),
        ),
    ) );
    register_block_type( 'correio68/weather', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_weather',
        'attributes'      => array(
            'cityName'      => array( 'type' => 'string', 'default' => '' ),
            'latitude'      => array( 'type' => 'string', 'default' => '' ),
            'longitude'     => array( 'type' => 'string', 'default' => '' ),
            'units'         => array( 'type' => 'string', 'default' => 'c' ),
            'showWind'      => array( 'type' => 'boolean', 'default' => true ),
            'showRain'      => array( 'type' => 'boolean', 'default' => true ),
            'forecastDays'  => array( 'type' => 'number', 'default' => 5 ),
            'showForecast'  => array( 'type' => 'boolean', 'default' => true ),
        ),
    ) );
    register_block_type( 'correio68/currency-monitor', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_currency_monitor',
        'attributes'      => array(
            'provider' => array( 'type' => 'string', 'default' => 'currencyfreaks' ),
            'base' => array( 'type' => 'string', 'default' => 'BRL' ),
            'baseAmount' => array( 'type' => 'number', 'default' => 100 ),
            'showBRL' => array( 'type' => 'boolean', 'default' => true ),
            'showUSD' => array( 'type' => 'boolean', 'default' => true ),
            'showEUR' => array( 'type' => 'boolean', 'default' => true ),
            'showPEN' => array( 'type' => 'boolean', 'default' => true ),
            'showARS' => array( 'type' => 'boolean', 'default' => true ),
            'showBOB' => array( 'type' => 'boolean', 'default' => true ),
            'showCLP' => array( 'type' => 'boolean', 'default' => false ),
            'showCOP' => array( 'type' => 'boolean', 'default' => false ),
            'showUYU' => array( 'type' => 'boolean', 'default' => false ),
            'showPYG' => array( 'type' => 'boolean', 'default' => false ),
            'showMXN' => array( 'type' => 'boolean', 'default' => false ),
            'spread' => array( 'type' => 'number', 'default' => 0 ),
            'showUpdated' => array( 'type' => 'boolean', 'default' => true ),
            'slidesToShow' => array( 'type' => 'number', 'default' => 2 ),
            'autoplay' => array( 'type' => 'boolean', 'default' => true ),
            'autoplaySpeed' => array( 'type' => 'number', 'default' => 3000 ),
            'showFlags' => array( 'type' => 'boolean', 'default' => true ),
            'showNames' => array( 'type' => 'boolean', 'default' => true ),
        ),
    ) );
    register_block_type( 'correio68/sidebar-area', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_sidebar_area',
        'attributes'      => array(
            'sidebarId' => array( 'type' => 'string', 'default' => 'right-sidebar' ),
            'title' => array( 'type' => 'string', 'default' => '' ),
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
 * Inject common design supports to our custom blocks so the Design tab controls appear.
 */
function u_correio68_register_block_supports( $args, $name ) {
    $is_ours = ( strpos( $name, 'u-correio68/' ) === 0 ) || ( strpos( $name, 'seideagosto/' ) === 0 );
    if ( ! $is_ours ) {
        return $args;
    }

    $supports = array(
        'color' => array( 'text' => true, 'background' => true, 'link' => true ),
        'spacing' => array( 'margin' => true, 'padding' => true, 'blockGap' => true ),
        'typography' => array(
            'fontSize' => true, 'lineHeight' => true,
            'fontStyle' => true, 'fontWeight' => true,
            'letterSpacing' => true, 'textTransform' => true,
        ),
        'border' => array( 'color' => true, 'style' => true, 'width' => true, 'radius' => true ),
    );

    if ( empty( $args['supports'] ) || ! is_array( $args['supports'] ) ) {
        $args['supports'] = array();
    }
    // Merge without overwriting existing explicit settings
    foreach ( $supports as $key => $val ) {
        if ( ! isset( $args['supports'][ $key ] ) ) {
            $args['supports'][ $key ] = $val;
        }
    }
    return $args;
}
add_filter( 'register_block_type_args', 'u_correio68_register_block_supports', 10, 2 );

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
    $layout_type = isset( $attributes['layoutType'] ) ? $attributes['layoutType'] : 'default';
    $posts_count = ( $layout_type === 'single' ) ? 1 : 3;
    
    $args_all = array(
        'post_type'      => 'post',
        'posts_per_page' => $posts_count,
        'order'          => 'DESC',
        'orderby'        => 'date',
        'ignore_sticky_posts' => true,
        'post__not_in'   => class_exists( 'PG_Helper' ) ? PG_Helper::getShownPosts() : array(),
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
        
        // Layout class based on type
        $wrapper_class = ( $layout_type === 'single' ) ? 'destaques-home-wrapper-single' : 'destaques-home-wrapper';
        $col_class = ( $layout_type === 'single' ) ? 'col-12' : 'col-md-8';
        ?>
        <div class="row <?php echo esc_attr($wrapper_class); ?> d-none d-md-flex">
                    <!-- Big Post -->
                    <div class="<?php echo esc_attr($col_class); ?>">
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
                    <?php if ( $layout_type !== 'single' ) : ?>
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
                <?php endif; ?>
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
    // Merge PG_Helper shown posts with excludeIds
    $shownPosts = class_exists( 'PG_Helper' ) ? PG_Helper::getShownPosts() : array();
    $excludeIds = array_values( array_unique( array_merge( $excludeIds, $shownPosts ) ) );
    
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
        // Exclude specified IDs and already shown posts
        $shownPosts = class_exists( 'PG_Helper' ) ? PG_Helper::getShownPosts() : array();
        $allExcludeIds = array_unique( array_merge( $excludeIds, $shownPosts ) );
        if ( ! empty( $allExcludeIds ) ) {
            $ids = array_values( array_diff( $ids, $allExcludeIds ) );
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
        // Merge PG_Helper shown posts with excludeIds
        $shownPosts = class_exists( 'PG_Helper' ) ? PG_Helper::getShownPosts() : array();
        if ( ! empty( $excludeIds ) ) {
            $args['post__not_in'] = array_unique( array_merge( $excludeIds, $shownPosts ) );
        } elseif ( ! empty( $shownPosts ) ) {
            $args['post__not_in'] = $shownPosts;
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
        'post__not_in'        => class_exists( 'PG_Helper' ) ? PG_Helper::getShownPosts() : array(),
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
                        ?>
                        <!-- Top row: Badge (left) and Date (right) -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <?php if ( !empty( $chamada ) ) : ?>
                                <span class="badge badge-light text-white badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important; font-size: 0.7rem; padding: 0.25rem 0.5rem;"> 
                                    <ion-icon class="<?php echo esc_attr($icones); ?>" style="font-size: 0.8rem;"></ion-icon> 
                                    <span><?php echo esc_html($chamada); ?></span>
                                </span>
                            <?php endif; ?>
                            <small class="text-muted" style="font-size: 0.8rem;"><i class="fa fa-clock-o"></i> <?php echo get_the_date('d/m/Y', $post->ID); ?></small>
                        </div>
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

    $postUrl = '';
    $postTitle = '';
    
    if ( $categoryId ) {
        $args = [
            'posts_per_page' => 1,
            'cat'            => $categoryId,
            'order'          => 'DESC',
            'post__not_in'   => class_exists( 'PG_Helper' ) ? PG_Helper::getShownPosts() : array(),
        ];
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost();
                $postUrl = get_permalink();
                $postTitle = get_the_title();
            }
            wp_reset_postdata();
        }
    }

    ob_start();
    ?>
    <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-3">
        <?php if ( $postUrl ) : ?>
            <a href="<?php echo esc_url( $postUrl ); ?>" class="colunista-card-link">
        <?php endif; ?>
        <div class="our-team colunista-card">
            <?php if ( $postTitle ) : ?>
                <div class="colunista-bubble">
                    <p><?php echo esc_html( $postTitle ); ?></p>
                </div>
            <?php endif; ?>
            
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
        </div>
        <?php if ( $postUrl ) : ?>
            </a>
        <?php endif; ?>
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
        'post__not_in'        => class_exists( 'PG_Helper' ) ? PG_Helper::getShownPosts() : array(),
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
                                        <h3 class="TituloGrande text-shadow text-white"><a href="<?php the_permalink(); ?>" class="text-white"><?php the_title(); ?></a></h3>
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
    $period     = isset( $attributes['period'] ) ? sanitize_text_field( $attributes['period'] ) : 'year';

    // Calculate date_query based on period
    $date_query = array();
    switch ( $period ) {
        case 'week':
            $date_query = array( 'after' => '1 week ago' );
            break;
        case '30days':
            $date_query = array( 'after' => '30 days ago' );
            break;
        case '90days':
            $date_query = array( 'after' => '90 days ago' );
            break;
        case 'year':
        default:
            $date_query = array( 'after' => '1 year ago' );
            break;
    }

    // Primary query: order by meta value (views)
    $args = array(
        'post_type'           => 'post',
        'posts_per_page'      => $count,
        'orderby'             => 'meta_value_num',
        'meta_key'            => $meta_key,
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
        'post_status'         => 'publish',
        'date_query'          => array( $date_query ),
        'post__not_in'        => class_exists( 'PG_Helper' ) ? PG_Helper::getShownPosts() : array(),
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
            'date_query'          => array( $date_query ),
            'post__not_in'        => class_exists( 'PG_Helper' ) ? PG_Helper::getShownPosts() : array(),
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
    $city         = isset( $attributes['cityName'] ) ? sanitize_text_field( $attributes['cityName'] ) : '';
    $lat          = isset( $attributes['latitude'] ) ? sanitize_text_field( $attributes['latitude'] ) : '';
    $lon          = isset( $attributes['longitude'] ) ? sanitize_text_field( $attributes['longitude'] ) : '';
    $units        = ( isset( $attributes['units'] ) && $attributes['units'] === 'f' ) ? 'f' : 'c';
    $showWind     = ! empty( $attributes['showWind'] );
    $showRain     = ! empty( $attributes['showRain'] );
    $showForecast = ! empty( $attributes['showForecast'] );
    $forecastDays = isset( $attributes['forecastDays'] ) ? intval( $attributes['forecastDays'] ) : 5;

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
    // Bump cache key version to include daily support
    $cache_key = 'u68_weather_v2_' . md5( $lat . '_' . $lon . '_' . $units );
    $data = get_transient( $cache_key );
    $needs_refresh = false;
    if ( $data !== false && ( ! is_array( $data ) || empty( $data['daily'] ) ) ) {
        // Refresh if previous cache lacks daily forecast
        $needs_refresh = true;
    }

    if ( false === $data || $needs_refresh ) {
        $params = array(
            'latitude'        => $lat,
            'longitude'       => $lon,
            'current_weather' => 'true',
            'hourly'          => 'precipitation',
            'daily'           => 'weathercode,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_mean,windspeed_10m_max',
            'timezone'        => 'auto',
            'forecast_days'   => max( $forecastDays, 1 ),
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
        // Normalize daily arrays
        $daily = array();
        if ( ! empty( $json['daily'] ) && is_array( $json['daily'] ) ) {
            $daily = $json['daily'];
            foreach ( array( 'time','weathercode','temperature_2m_max','temperature_2m_min','precipitation_sum','precipitation_probability_mean','windspeed_10m_max' ) as $k ) {
                if ( ! isset( $daily[$k] ) || ! is_array( $daily[$k] ) ) {
                    $daily[$k] = array();
                }
            }
        }
        $data = array(
            'temperature'   => isset( $json['current_weather']['temperature'] ) ? floatval( $json['current_weather']['temperature'] ) : null,
            'windspeed'     => isset( $json['current_weather']['windspeed'] ) ? floatval( $json['current_weather']['windspeed'] ) : null,
            'winddirection' => isset( $json['current_weather']['winddirection'] ) ? intval( $json['current_weather']['winddirection'] ) : null,
            'weathercode'   => isset( $json['current_weather']['weathercode'] ) ? intval( $json['current_weather']['weathercode'] ) : null,
            'time'          => isset( $json['current_weather']['time'] ) ? sanitize_text_field( $json['current_weather']['time'] ) : '',
            'daily'         => $daily,
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
    $icon_color_class_main = in_array( $icon, array('rain','storm'), true ) ? 'accent' : 'primary';

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
        <div class="current-wrap d-flex flex-column align-items-center text-center">
            <div class="weather-icon icon-<?php echo esc_attr( $icon ); ?>" style="position:relative;width:56px;height:56px;margin-bottom:12px;">
                <div class="icon-base" style="position:absolute;inset:0;"></div>
                <?php if ( in_array( $icon, array('rain','storm'), true ) ) : ?>
                    <div class="rain" style="position:absolute;inset:0;"></div>
                <?php endif; ?>
                <?php if ( $showWind ) : ?>
                    <div class="wind" style="position:absolute;inset:0;"></div>
                <?php endif; ?>
                <div class="fa-overlay" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                    <i class="fa <?php echo esc_attr( $fa ); ?> weather-fa-icon icon-color-<?php echo esc_attr( $icon_color_class_main ); ?>" aria-hidden="true"></i>
                </div>
            </div>
            <div class="current-info">
                <div class="current-temp mb-2">
                    <span class="temp-value"><?php echo esc_html( round( $temp ) ); ?></span>
                    <span class="temp-unit"><?php echo esc_html( $temp_unit ); ?></span>
                </div>
                <div class="current-meta-inline mb-2">
                    <?php if ( ! empty( $city ) ) : ?>
                        <div class="city"><i class="fa fa-map-marker icon-color-primary mr-1" aria-hidden="true"></i> <?php echo esc_html( $city ); ?></div>
                    <?php endif; ?>
                    <div class="condition"><i class="fa fa-info-circle icon-color-accent mr-1" aria-hidden="true"></i> <?php echo esc_html( $desc ); ?></div>
                </div>
                <div class="meta-bottom d-flex align-items-center justify-content-center" style="gap: 8px; flex-wrap: nowrap;">
                    <?php if ( $showWind && is_numeric( $windspeed ) ) : ?>
                        <span class="badge badge-pill" style="background-color: #007bff; color: white; padding: 6px 12px; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                            <i class="fa fa-flag" aria-hidden="true"></i>
                            <span><?php echo esc_html( round( $windspeed ) ); ?> <?php echo esc_html( $wind_unit ); ?></span>
                        </span>
                    <?php endif; ?>
                    <?php
                    // Show today precipitation probability or sum if available
                    $header_prpct = null; $header_prsum = null;
                    if ( ! empty( $data['daily'] ) && ! empty( $data['daily']['precipitation_probability_mean'] ) && isset( $data['daily']['precipitation_probability_mean'][0] ) ) {
                        $header_prpct = intval( $data['daily']['precipitation_probability_mean'][0] );
                    }
                    if ( ( $header_prpct === null ) && ! empty( $data['daily'] ) && ! empty( $data['daily']['precipitation_sum'] ) && isset( $data['daily']['precipitation_sum'][0] ) ) {
                        $header_prsum = floatval( $data['daily']['precipitation_sum'][0] );
                    }
                    if ( $showRain ) {
                        if ( is_int( $header_prpct ) ) {
                            echo '<span class="badge badge-pill" style="background-color: #17a2b8; color: white; padding: 6px 12px; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;"><i class="fa fa-tint" aria-hidden="true"></i> <span>' . esc_html( $header_prpct ) . '%</span></span>';
                        } elseif ( is_float( $header_prsum ) ) {
                            echo '<span class="badge badge-pill" style="background-color: #17a2b8; color: white; padding: 6px 12px; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;"><i class="fa fa-tint" aria-hidden="true"></i> <span>' . esc_html( round( $header_prsum, 1 ) ) . ' mm</span></span>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php
        // Forecast (next days) - Skip today (index 0), start from tomorrow (index 1)
        if ( $showForecast ) :
            $daily = isset( $data['daily'] ) ? $data['daily'] : array();
            if ( ! empty( $daily ) && ! empty( $daily['time'] ) ) :
                // Start from index 1 to skip today, add 1 to get the requested number of future days
                $total_days = min( $forecastDays + 1, count( $daily['time'] ) );
                echo '<div class="weather-forecast mt-3">';
                echo '<div class="weather-forecast-slider">';
                for ( $i = 1; $i < $total_days; $i++ ) {
                    $date = sanitize_text_field( $daily['time'][$i] );
                    $wcode = isset( $daily['weathercode'][$i] ) ? intval( $daily['weathercode'][$i] ) : 0;
                    $tmax  = isset( $daily['temperature_2m_max'][$i] ) ? floatval( $daily['temperature_2m_max'][$i] ) : null;
                    $tmin  = isset( $daily['temperature_2m_min'][$i] ) ? floatval( $daily['temperature_2m_min'][$i] ) : null;
                    $prsum = isset( $daily['precipitation_sum'][$i] ) ? floatval( $daily['precipitation_sum'][$i] ) : null;
                    $prpct = isset( $daily['precipitation_probability_mean'][$i] ) ? intval( $daily['precipitation_probability_mean'][$i] ) : null;
                    $wmax  = isset( $daily['windspeed_10m_max'][$i] ) ? floatval( $daily['windspeed_10m_max'][$i] ) : null;

                    // Convert units if needed
                    if ( $units === 'f' ) {
                        if ( is_numeric( $tmax ) ) $tmax = round( ( $tmax * 9/5 ) + 32 );
                        if ( is_numeric( $tmin ) ) $tmin = round( ( $tmin * 9/5 ) + 32 );
                    } else {
                        if ( is_numeric( $tmax ) ) $tmax = round( $tmax );
                        if ( is_numeric( $tmin ) ) $tmin = round( $tmin );
                    }

                    // Simple daily icon mapping
                    $d_fa = 'fa-sun-o';
                    if ( in_array( $wcode, array(1,2,3), true ) ) $d_fa = 'fa-cloud';
                    elseif ( in_array( $wcode, array(61,63,65,66,67,80,81,82), true ) ) $d_fa = 'fa-umbrella';
                    elseif ( in_array( $wcode, array(71,73,75,77), true ) ) $d_fa = 'fa-snowflake-o';
                    elseif ( in_array( $wcode, array(95,96,99), true ) ) $d_fa = 'fa-bolt';
                    $d_color = ( in_array( $wcode, array(61,63,65,66,67,80,81,82,95,96,99), true ) ) ? 'accent' : 'primary';

                    echo '<div class="forecast-day small p-3 border rounded">';
                    echo '<div class="d-flex align-items-center mb-1">';
                    echo '<i class="fa ' . esc_attr( $d_fa ) . ' mr-2 icon-color-' . esc_attr( $d_color ) . '" aria-hidden="true"></i>';
                    echo '<span class="day-name">' . esc_html( date_i18n( 'l', strtotime( $date ) ) ) . '</span>';
                    echo '<span class="day-date ml-2">' . esc_html( date_i18n( 'j/m', strtotime( $date ) ) ) . '</span>';
                    echo '</div>';
                    // Temps inline com badges
                    echo '<div class="mb-1 metrics temps d-flex align-items-center" style="gap: 6px; flex-wrap: wrap;">';
                    echo '<span class="badge badge-pill" style="background-color: #ffc107; color: #333; padding: 4px 10px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-thermometer-full" aria-hidden="true"></i> Máx: ' . ( is_numeric( $tmax ) ? esc_html( $tmax ) . '°' : '-' ) . '</span>';
                    echo '<span class="badge badge-pill" style="background-color: #ffc107; color: #333; padding: 4px 10px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-thermometer-empty" aria-hidden="true"></i> Mín: ' . ( is_numeric( $tmin ) ? esc_html( $tmin ) . '°' : '-' ) . '</span>';
                    echo '</div>';
                    // Rain & Wind badges inline
                    echo '<div class="mb-1 metrics d-flex align-items-center" style="gap: 6px; flex-wrap: wrap;">';
                    if ( is_numeric( $prpct ) ) echo '<span class="badge badge-pill" style="background-color: #17a2b8; color: white; padding: 4px 10px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-tint" aria-hidden="true"></i> ' . esc_html( $prpct ) . '%</span>';
                    elseif ( is_numeric( $prsum ) ) echo '<span class="badge badge-pill" style="background-color: #17a2b8; color: white; padding: 4px 10px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-tint" aria-hidden="true"></i> ' . esc_html( round( $prsum, 1 ) ) . ' mm</span>';
                    if ( is_numeric( $wmax ) ) echo '<span class="badge badge-pill" style="background-color: #007bff; color: white; padding: 4px 10px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-flag" aria-hidden="true"></i> ' . esc_html( round( $wmax ) ) . ' ' . esc_html( $wind_unit ) . '</span>';
                    echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
            endif;
        endif;
        ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render Callback: Currency Monitor (exchangerate.host)
 */
function u_correio68_render_currency_monitor( $attributes ) {
    // Enqueue Slick CSS/JS - use handle já registrado em functions.php
    if ( ! is_admin() ) {
        // Força o carregamento do Slick que já está registrado
        wp_enqueue_style( 'u_seisbarra8-slick' );
        wp_enqueue_style( 'u_seisbarra8-slicktheme' );
        wp_enqueue_script( 'u_seisbarra8-slick' );
    }

    $provider    = isset( $attributes['provider'] ) ? sanitize_text_field( $attributes['provider'] ) : 'exchangerate';
    $base        = isset( $attributes['base'] ) ? strtoupper( sanitize_text_field( $attributes['base'] ) ) : 'BRL';
    $base_amount = isset( $attributes['baseAmount'] ) ? floatval( $attributes['baseAmount'] ) : 100;
    $show_brl    = ! empty( $attributes['showBRL'] );
    $show_usd    = ! empty( $attributes['showUSD'] );
    $show_eur    = ! empty( $attributes['showEUR'] );
    $show_pen    = ! empty( $attributes['showPEN'] );
    $show_ars    = ! empty( $attributes['showARS'] );
    $show_bob    = ! empty( $attributes['showBOB'] );
    $show_clp    = ! empty( $attributes['showCLP'] );
    $show_cop    = ! empty( $attributes['showCOP'] );
    $show_uyu    = ! empty( $attributes['showUYU'] );
    $show_pyg    = ! empty( $attributes['showPYG'] );
    $show_mxn    = ! empty( $attributes['showMXN'] );
    $spread_pct  = isset( $attributes['spread'] ) ? floatval( $attributes['spread'] ) : 0;
    $show_updated= ! empty( $attributes['showUpdated'] );
    $slides_to_show = isset( $attributes['slidesToShow'] ) ? intval( $attributes['slidesToShow'] ) : 2;
    $autoplay    = isset( $attributes['autoplay'] ) ? ! empty( $attributes['autoplay'] ) : true;
    $autoplay_speed = isset( $attributes['autoplaySpeed'] ) ? intval( $attributes['autoplaySpeed'] ) : 3000;
    $show_flags  = isset( $attributes['showFlags'] ) ? ! empty( $attributes['showFlags'] ) : true;
    $show_names  = isset( $attributes['showNames'] ) ? ! empty( $attributes['showNames'] ) : true;

    // Currency metadata: name, icon (emoji flag), decimal places, symbol
    $currency_info = array(
        'BRL' => array( 'name' => 'Real Brasileiro', 'flag' => '🇧🇷', 'decimals' => 2, 'symbol' => 'R$' ),
        'USD' => array( 'name' => 'Dólar Americano', 'flag' => '🇺🇸', 'decimals' => 2, 'symbol' => '$' ),
        'EUR' => array( 'name' => 'Euro', 'flag' => '🇪🇺', 'decimals' => 2, 'symbol' => '€' ),
        'PEN' => array( 'name' => 'Sol Peruano', 'flag' => '🇵🇪', 'decimals' => 2, 'symbol' => 'S/' ),
        'ARS' => array( 'name' => 'Peso Argentino', 'flag' => '🇦🇷', 'decimals' => 2, 'symbol' => '$' ),
        'BOB' => array( 'name' => 'Boliviano', 'flag' => '🇧🇴', 'decimals' => 2, 'symbol' => 'Bs.' ),
        'CLP' => array( 'name' => 'Peso Chileno', 'flag' => '🇨🇱', 'decimals' => 0, 'symbol' => '$' ),
        'COP' => array( 'name' => 'Peso Colombiano', 'flag' => '🇨🇴', 'decimals' => 0, 'symbol' => '$' ),
        'UYU' => array( 'name' => 'Peso Uruguaio', 'flag' => '🇺🇾', 'decimals' => 2, 'symbol' => '$' ),
        'PYG' => array( 'name' => 'Guaraní', 'flag' => '🇵🇾', 'decimals' => 0, 'symbol' => '₲' ),
        'MXN' => array( 'name' => 'Peso Mexicano', 'flag' => '🇲🇽', 'decimals' => 2, 'symbol' => '$' ),
    );

    // Symbols list
    $symbols = array();
    if ( $show_usd ) $symbols[] = 'USD';
    if ( $show_eur ) $symbols[] = 'EUR';
    if ( $show_pen ) $symbols[] = 'PEN';
    if ( $show_ars ) $symbols[] = 'ARS';
    if ( $show_bob ) $symbols[] = 'BOB';
    if ( $show_clp ) $symbols[] = 'CLP';
    if ( $show_cop ) $symbols[] = 'COP';
    if ( $show_uyu ) $symbols[] = 'UYU';
    if ( $show_pyg ) $symbols[] = 'PYG';
    if ( $show_mxn ) $symbols[] = 'MXN';

    // Fetch rates
    $rates = array();
    $updated_at = '';
    if ( ! empty( $symbols ) ) {
        $cache_key      = 'u68_fx_' . md5( $provider . '|' . $base . '|' . implode(',', $symbols) );
        $cached         = get_transient( $cache_key );
        $lastgood_store = get_option( 'u68_fx_lastgood', array() );
        $lastgood       = isset( $lastgood_store[ $cache_key ] ) ? $lastgood_store[ $cache_key ] : array();
        $timeout        = 4; // keep requests snappy

        if ( false === $cached ) {
            // Try primary provider
            $providers_to_try = array( $provider );
            
            // Add fallback providers if primary fails
            if ( $provider !== 'erapi' ) {
                $providers_to_try[] = 'erapi'; // Fast and free fallback
            }
            if ( $provider !== 'exchangerate' ) {
                $providers_to_try[] = 'exchangerate'; // Second fallback
            }
            
            foreach ( $providers_to_try as $current_provider ) {
                if ( ! empty( $rates ) ) break; // Got data, stop trying
                
                if ( $current_provider === 'frankfurter' ) {
                    $api_url = add_query_arg( array(
                        'from' => $base,
                        'to'   => implode(',', $symbols),
                    ), 'https://api.frankfurter.app/latest' );
                    $res = wp_remote_get( $api_url, array( 'timeout' => $timeout ) );
                    if ( ! is_wp_error( $res ) ) {
                        $body = wp_remote_retrieve_body( $res );
                        $json = json_decode( $body, true );
                        if ( ! empty( $json['rates'] ) ) {
                            $rates = $json['rates'];
                            $updated_at = isset( $json['date'] ) ? sanitize_text_field( $json['date'] ) : '';
                        }
                    }
                } elseif ( $current_provider === 'currencyfreaks' ) {
                // currencyfreaks.com - API key from settings/constant/env
                $api_key = function_exists('u68_get_currencyfreaks_api_key') ? u68_get_currencyfreaks_api_key() : '';
                if ( ! empty( $api_key ) ) {
                    // Free plan has USD base; request BRL plus target symbols for conversion
                    $req_symbols = array_unique( array_merge( $symbols, array( 'BRL' ) ) );
                    $api_url = add_query_arg( array(
                        'apikey'  => $api_key,
                        'symbols' => implode( ',', $req_symbols ),
                    ), 'https://api.currencyfreaks.com/v2.0/rates/latest' );
                    $res = wp_remote_get( $api_url, array( 'timeout' => $timeout ) );
                    if ( ! is_wp_error( $res ) ) {
                        $body = wp_remote_retrieve_body( $res );
                        $json = json_decode( $body, true );
                        if ( ! empty( $json['rates'] ) ) {
                            foreach ( $req_symbols as $sym ) {
                                if ( isset( $json['rates'][ $sym ] ) ) {
                                    $rates[ $sym ] = floatval( $json['rates'][ $sym ] );
                                }
                            }
                            $updated_at = isset( $json['date'] ) ? sanitize_text_field( $json['date'] ) : '';
                        }
                    }
                }
            } elseif ( $current_provider === 'erapi' ) {
                // open.er-api.com (rápida e gratuita)
                $api_url = 'https://open.er-api.com/v6/latest/' . rawurlencode( $base );
                $res = wp_remote_get( $api_url, array( 'timeout' => $timeout ) );
                if ( ! is_wp_error( $res ) ) {
                    $body = wp_remote_retrieve_body( $res );
                    $json = json_decode( $body, true );
                    if ( isset( $json['result'] ) && $json['result'] === 'success' && ! empty( $json['rates'] ) ) {
                        foreach ( $symbols as $sym ) {
                            if ( isset( $json['rates'][ $sym ] ) ) {
                                $rates[ $sym ] = floatval( $json['rates'][ $sym ] );
                            }
                        }
                        $updated_at = isset( $json['time_last_update_utc'] ) ? sanitize_text_field( $json['time_last_update_utc'] ) : '';
                    }
                }
            } else {
                $api_url = add_query_arg( array(
                    'base'    => $base,
                    'symbols' => implode(',', $symbols),
                ), 'https://api.exchangerate.host/latest' );
                $res = wp_remote_get( $api_url, array( 'timeout' => $timeout ) );
                if ( ! is_wp_error( $res ) ) {
                    $body = wp_remote_retrieve_body( $res );
                    $json = json_decode( $body, true );
                    if ( ! empty( $json['rates'] ) ) {
                        $rates = $json['rates'];
                        $updated_at = isset( $json['date'] ) ? sanitize_text_field( $json['date'] ) : '';
                    }
                }
            }
            } // end provider loop

            if ( ! empty( $rates ) ) {
                set_transient( $cache_key, array( 'rates' => $rates, 'updated' => $updated_at ), 10 * MINUTE_IN_SECONDS );
                $lastgood_store[ $cache_key ] = array( 'rates' => $rates, 'updated' => $updated_at );
                update_option( 'u68_fx_lastgood', $lastgood_store, false );
            }

            // Fallback to last known good data if live call failed
            if ( empty( $rates ) && ! empty( $lastgood ) ) {
                $rates      = isset( $lastgood['rates'] ) ? $lastgood['rates'] : array();
                $updated_at = isset( $lastgood['updated'] ) ? $lastgood['updated'] : '';
            }
        } else {
            $rates      = isset( $cached['rates'] ) ? $cached['rates'] : array();
            $updated_at = isset( $cached['updated'] ) ? $cached['updated'] : '';
        }
    }

    ob_start();
    ?>
    <div class="currency-monitor cm-minimal" style="padding: 0;">
        <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 12px; padding: 0 8px;">
            <h5 style="margin: 0; font-weight: 700; letter-spacing: 0.2px; font-size: 1.1rem;">Quanto vale <?php echo esc_html( number_format( $base_amount, 0, ',', '.' ) ); ?> reais?</h5>
            <?php if ( $show_updated && $updated_at ) : ?>
                <small class="text-muted" style="opacity: 0.7; font-size: 0.75rem;">Atualizado: <?php echo esc_html( $updated_at ); ?></small>
            <?php endif; ?>
        </div>
        <?php if ( empty( $symbols ) ) : ?>
            <div class="alert alert-info" style="background:#d1ecf1;border:1px solid #bee5eb;color:#0c5460;padding:8px;border-radius:6px;margin:0 8px 12px 8px;font-size:0.85rem;">
                Nenhuma moeda selecionada. Configure o bloco para escolher as moedas a exibir.
            </div>
        <?php elseif ( empty( $rates ) ) : ?>
            <div class="alert alert-warning" style="background:#fff3cd;border:1px solid #ffeeba;color:#856404;padding:8px;border-radius:6px;margin:0 8px 12px 8px;font-size:0.85rem;">
                Taxas indisponíveis no momento. Tentando reconectar...
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $symbols ) && ! empty( $rates ) ) : ?>
        <div class="cm-slick" style="position:relative; margin:0 8px;">
            <?php 
            // Ordem para exibir (todas as moedas latino-americanas)
            $display_order = array('USD', 'EUR', 'MXN', 'COP', 'PEN', 'ARS', 'CLP', 'UYU', 'PYG', 'BOB');
            
            foreach ( $display_order as $code ) :
                // Verificar se deve exibir
                if ( 
                    ( $code === 'USD' && ! $show_usd ) ||
                    ( $code === 'EUR' && ! $show_eur ) ||
                    ( $code === 'PEN' && ! $show_pen ) ||
                    ( $code === 'ARS' && ! $show_ars ) ||
                    ( $code === 'BOB' && ! $show_bob ) ||
                    ( $code === 'CLP' && ! $show_clp ) ||
                    ( $code === 'COP' && ! $show_cop ) ||
                    ( $code === 'UYU' && ! $show_uyu ) ||
                    ( $code === 'PYG' && ! $show_pyg ) ||
                    ( $code === 'MXN' && ! $show_mxn )
                ) {
                    continue;
                }
                
                $rate = isset( $rates[$code] ) ? floatval( $rates[$code] ) : null;
                if ( ! $rate ) continue;
                
                // Compute amount from BRL to target currency
                if ( $provider === 'currencyfreaks' ) {
                    $brl_rate = isset( $rates['BRL'] ) ? floatval( $rates['BRL'] ) : null; // BRL per 1 USD
                    if ( $brl_rate && $brl_rate > 0 ) {
                        // 1 BRL = (rate / brl_rate) target currency
                        $amount = $base_amount * ( $rate / $brl_rate );
                    } else {
                        $amount = $base_amount * $rate; // fallback
                    }
                } else {
                    $amount = $base_amount * $rate;
                }
                if ( $spread_pct > 0 ) {
                    $amount = $amount * ( 1 + ( $spread_pct / 100 ) );
                }
                $info = isset( $currency_info[$code] ) ? $currency_info[$code] : array( 'name' => $code, 'flag' => '💱', 'decimals' => 2, 'symbol' => '' );
                $decimals = isset( $info['decimals'] ) ? $info['decimals'] : 2;
                $symbol = isset( $info['symbol'] ) ? $info['symbol'] : '';
                
                // Determine color based on currency
                $color_map = array(
                    'USD' => '#0066cc',  // Azul
                    'EUR' => '#0066cc',  // Azul
                    'PEN' => '#d4a500',  // Ouro
                    'ARS' => '#4169e1',  // Royal Blue
                    'BOB' => '#a52a2a',  // Brown
                );
                $color = isset( $color_map[$code] ) ? $color_map[$code] : '#6c757d';
            ?>
                <div class="cm-item" style="flex-shrink: 0; text-align: center; padding: 10px 8px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff;">
                    <?php if ( $show_flags ) : ?>
                        <div style="font-size: 1.75rem; line-height: 1; margin-bottom: 3px;"><?php echo isset( $info['flag'] ) ? $info['flag'] : '💱'; ?></div>
                    <?php endif; ?>
                    <?php if ( $show_names ) : ?>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #111; margin-bottom: 1px;"><?php echo esc_html( $code ); ?></div>
                        <div style="color: #888; font-size: 0.75rem; margin-bottom: 8px; line-height: 1.2;"><?php echo esc_html( $info['name'] ); ?></div>
                    <?php endif; ?>
                    <div class="badge" style="display:inline-block; background: <?php echo esc_attr( $color ); ?>; color: #fff; padding: 6px 10px; border-radius: 999px; font-weight: 700; font-size: 1rem; white-space: nowrap;">
                        <?php echo esc_html( $symbol . ' ' . number_format( $amount, $decimals, ',', '.' ) ); ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if ( $show_brl ) : ?>
                <div class="cm-item" style="flex-shrink: 0; text-align: center; padding: 10px 8px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff;">
                    <div style="font-size: 1.75rem; line-height: 1; margin-bottom: 3px;">🇧🇷</div>
                    <div style="font-weight: 700; font-size: 0.9rem; color: #111; margin-bottom: 1px;">BRL</div>
                    <div style="color: #888; font-size: 0.75rem; margin-bottom: 8px; line-height: 1.2;">Real Brasileiro</div>
                    <div class="badge" style="display:inline-block; background: #27ae60; color: #fff; padding: 6px 10px; border-radius: 999px; font-weight: 700; font-size: 1rem; white-space: nowrap;">R$ 100,00</div>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; // End if symbols and rates are available ?>
        
        <style>
        .cm-slick .slick-prev, .cm-slick .slick-next {
            z-index: 10;
            width: 28px;
            height: 28px;
            top: 50%;
            transform: translateY(-50%);
        }
        .cm-slick .slick-prev::before, .cm-slick .slick-next::before {
            font-size: 18px;
            color: #333;
        }
        .cm-slick .slick-prev { left: -35px; }
        .cm-slick .slick-next { right: -35px; }
        @media (max-width: 768px) {
            .cm-slick .slick-prev { left: 5px; }
            .cm-slick .slick-next { right: 5px; }
        }
        </style>
        <?php if ( ! empty( $symbols ) && ! empty( $rates ) ) : ?>
        <script>
        jQuery(document).ready(function($){
            var slidesToShowDefault = <?php echo intval( $slides_to_show ); ?>;
            var autoplayEnabled = <?php echo $autoplay ? 'true' : 'false'; ?>;
            var autoplaySpeedVal = <?php echo intval( $autoplay_speed ); ?>;
            
            function initCurrencySlick() {
                var $el = $('.cm-slick');
                
                if (!$el.length) {
                    console.log('Currency monitor: .cm-slick element not found');
                    return;
                }
                
                // Check if there are items to show
                var itemCount = $el.find('.cm-item').length;
                if (itemCount === 0) {
                    console.log('Currency monitor: No items to display');
                    return;
                }
                
                if (typeof $.fn.slick === 'undefined') {
                    console.log('Currency monitor: Slick not loaded yet');
                    return;
                }
                
                // Destroy if already initialized
                if ($el.hasClass('slick-initialized')) {
                    $el.slick('unslick');
                }
                
                console.log('Currency monitor: Initializing slick with ' + slidesToShowDefault + ' slides and ' + itemCount + ' items');
                
                // Initialize slick
                $el.slick({
                    slidesToShow: slidesToShowDefault,
                    slidesToScroll: 1,
                    arrows: false,
                    dots: true,
                    infinite: true,
                    speed: 300,
                    autoplay: autoplayEnabled,
                    autoplaySpeed: autoplaySpeedVal,
                    pauseOnHover: true,
                    pauseOnFocus: true,
                    responsive: [
                        { breakpoint: 1200, settings: { slidesToShow: Math.max(1, slidesToShowDefault - 1) } },
                        { breakpoint: 992,  settings: { slidesToShow: Math.max(1, Math.floor(slidesToShowDefault / 2)) } },
                        { breakpoint: 600,  settings: { slidesToShow: 1 } }
                    ]
                });
            }
            
            // Try multiple times to ensure slick is loaded
            var attempts = 0;
            var maxAttempts = 5;
            var interval = setInterval(function(){
                attempts++;
                if (typeof $.fn.slick !== 'undefined') {
                    clearInterval(interval);
                    initCurrencySlick();
                } else if (attempts >= maxAttempts) {
                    clearInterval(interval);
                    console.error('Currency monitor: Slick library failed to load after ' + maxAttempts + ' attempts');
                }
            }, 200);
        });
        </script>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
