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

/**
 * Apply tag and keyword filters to a query args array based on block attributes.
 * Supported attributes:
 * - keyword: string (search term)
 * - tags: string (CSV of tag slugs)
 * - tagSlugs: array of tag slugs
 * - tagIds: array of tag IDs
 */
function u_correio68_apply_tag_keyword_filters( array $args, $attributes ) {
    // Keyword (search)
    if ( isset( $attributes['keyword'] ) && is_string( $attributes['keyword'] ) && $attributes['keyword'] !== '' ) {
        $args['s'] = sanitize_text_field( $attributes['keyword'] );
    }

    // Prefer explicit tag IDs
    if ( isset( $attributes['tagIds'] ) && is_array( $attributes['tagIds'] ) ) {
        $ids = array();
        foreach ( $attributes['tagIds'] as $id ) {
            $id = absint( $id );
            if ( $id > 0 ) { $ids[] = $id; }
        }
        if ( ! empty( $ids ) ) {
            $args['tag__in'] = array_values( array_unique( $ids ) );
        }
        return $args;
    }

    // Next, explicit tag slugs
    if ( isset( $attributes['tagSlugs'] ) && is_array( $attributes['tagSlugs'] ) ) {
        $slugs = array();
        foreach ( $attributes['tagSlugs'] as $slug ) {
            $slug = sanitize_title( $slug );
            if ( $slug !== '' ) { $slugs[] = $slug; }
        }
        if ( ! empty( $slugs ) ) {
            $args['tag_slug__in'] = array_values( array_unique( $slugs ) );
        }
        return $args;
    }

    // Finally, CSV of slugs via `tags`
    if ( isset( $attributes['tags'] ) && is_string( $attributes['tags'] ) && $attributes['tags'] !== '' ) {
        $parts = array_map( 'trim', explode( ',', $attributes['tags'] ) );
        $slugs = array();
        foreach ( $parts as $p ) {
            $p = sanitize_title( $p );
            if ( $p !== '' ) { $slugs[] = $p; }
        }
        if ( ! empty( $slugs ) ) {
            $args['tag_slug__in'] = array_values( array_unique( $slugs ) );
        }
    }

    return $args;
}

function u_correio68_register_custom_blocks() {
    // Register the block editor script
    $blocks_script_src      = get_template_directory_uri() . '/assets/js/custom-blocks.js';
    $blocks_script_deps     = array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-server-side-render', 'wp-hooks', 'wp-dom-ready' );
    $blocks_script_version  = filemtime( get_template_directory() . '/assets/js/custom-blocks.js' );

    wp_register_script(
        'seideagosto-blocks',
        $blocks_script_src,
        $blocks_script_deps,
        $blocks_script_version
    );

    // Legacy alias for existing block.json files referencing the old handle
    wp_register_script(
        'u-correio68-custom-blocks',
        false,
        array( 'seideagosto-blocks' ),
        $blocks_script_version
    );

    // Ensure Font Awesome is available inside the editor for icon-based blocks
    $fa_handle   = 'u_seisbarra8-fontawesome';
    $fa_vendor   = get_template_directory() . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css';
    $fa_fallback = get_template_directory_uri() . '/css/local-fa-fallback.css';
    if ( file_exists( $fa_vendor ) ) {
        wp_register_style( $fa_handle, get_template_directory_uri() . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css', array(), '4.7.0' );
    } else {
        wp_register_style( $fa_handle, $fa_fallback, array(), file_exists( get_template_directory() . '/css/local-fa-fallback.css' ) ? filemtime( get_template_directory() . '/css/local-fa-fallback.css' ) : null );
    }

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

    // Enqueue block editor assets
    add_action( 'enqueue_block_editor_assets', function() use ( $fa_handle ) {
        wp_enqueue_script( 'seideagosto-blocks' );
        if ( wp_style_is( $fa_handle, 'registered' ) ) {
            wp_enqueue_style( $fa_handle );
        }
    });

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
            'tags' => array( 'type' => 'string', 'default' => '' ),
            'keyword' => array( 'type' => 'string', 'default' => '' ),
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
                'tags'          => array( 'type' => 'string', 'default' => '' ),
                'keyword'       => array( 'type' => 'string', 'default' => '' ),
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
                'tags'       => array( 'type' => 'string', 'default' => '' ),
                'keyword'    => array( 'type' => 'string', 'default' => '' ),
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
                'showList' => array( 'type' => 'boolean', 'default' => true ),
                'showListThumbs' => array( 'type' => 'boolean', 'default' => true ),
                'showBadges' => array( 'type' => 'boolean', 'default' => true ),
                'tags'       => array( 'type' => 'string', 'default' => '' ),
                'keyword'    => array( 'type' => 'string', 'default' => '' ),
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
            'showList' => array( 'type' => 'boolean', 'default' => true ),
            'showListThumbs' => array( 'type' => 'boolean', 'default' => true ),
            'showBadges' => array( 'type' => 'boolean', 'default' => true ),
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
    register_block_type( 'seideagosto/image-slider', array(
        'editor_script'   => 'seideagosto-blocks',
        'render_callback' => 'u_correio68_render_image_slider',
        'attributes'      => array(
            'images' => array( 'type' => 'array', 'default' => array() ),
            'speed' => array( 'type' => 'number', 'default' => 3000 ),
            'autoplaySpeed' => array( 'type' => 'number', 'default' => 5000 ),
            'vertical' => array( 'type' => 'boolean', 'default' => false ),
            'rtl' => array( 'type' => 'boolean', 'default' => false ),
            'fade' => array( 'type' => 'boolean', 'default' => false ),
            'autoplay' => array( 'type' => 'boolean', 'default' => true ),
            'pauseOnHover' => array( 'type' => 'boolean', 'default' => true ),
            'slidesToShow' => array( 'type' => 'number', 'default' => 1 ),
            'slidesToScroll' => array( 'type' => 'number', 'default' => 1 ),
        ),
    ) );

    // Register metadata-based blocks from theme/blocks directory
    $blocks_dir = get_template_directory() . '/blocks';
    
    // Include render callbacks for metadata blocks
    if ( file_exists( $blocks_dir . '/titulo-com-icone/render.php' ) ) {
        require_once( $blocks_dir . '/titulo-com-icone/render.php' );
    }
    
    $metadata_blocks = array(
        'destaque-grande',
        'destaque-pequeno',
        'lista-noticias',
        'titulo-com-icone',
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
                    // Special handling for titulo-com-icone block
                    if ( $slug === 'titulo-com-icone' && function_exists( 'u_correio68_render_titulo_com_icone' ) ) {
                        register_block_type( $path, array(
                            'render_callback' => 'u_correio68_render_titulo_com_icone',
                        ) );
                    } else {
                        register_block_type( $path );
                    }
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
    // Apply tag and keyword filters if provided in attributes
    $args_all = u_correio68_apply_tag_keyword_filters( $args_all, $attributes );
    
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
        $args = u_correio68_apply_tag_keyword_filters( $args, $attributes );
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
                            
                            <a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block news-image-wrapper overflow-hidden mb-3 position-relative" style="height: 180px; border-radius: 4px; border: 1px solid #e9ecef;">
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
                                <?php 
                                $cor = function_exists('get_field') ? get_field( 'cor' ) : '';
                                $icones = function_exists('get_field') ? get_field( 'icones' ) : '';
                                $chamada = function_exists('get_field') ? get_field( 'chamada' ) : '';
                                if ( !empty($chamada) ) : ?>
                                    <span class="badge badge-light text-white badge-pill news-grid-badge" style="background-color:<?php echo esc_attr($cor); ?> !important; font-size: 0.7rem; padding: 0.25rem 0.5rem; position: absolute; left: 12px; top: 12px; z-index: 10;"> 
                                        <ion-icon class="<?php echo esc_attr($icones); ?>" style="font-size: 0.8rem;"></ion-icon> 
                                        <span><?php echo esc_html($chamada); ?></span>
                                    </span>
                                <?php endif; ?>
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
    $args = u_correio68_apply_tag_keyword_filters( $args, $attributes );

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
                <div class="primeiro mb-3 position-relative" id="post-<?php echo $post->ID; ?>" style="transition: opacity 0.2s ease;">
                    <div class="news-image-wrapper overflow-hidden mb-2 position-relative" style="height: 200px; border-radius: 4px; border: 1px solid #e9ecef;">
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
                        <?php 
                        $cor = function_exists('get_field') ? get_field( 'cor', $post->ID ) : '';
                        $icones = function_exists('get_field') ? get_field( 'icones', $post->ID ) : '';
                        $chamada = function_exists('get_field') ? get_field( 'chamada', $post->ID ) : '';
                        if ( !empty( $chamada ) ) : ?>
                            <span class="badge badge-light text-white badge-pill category-highlight-badge" style="background-color:<?php echo esc_attr($cor); ?> !important; font-size: 0.7rem; padding: 0.25rem 0.5rem; position: absolute; left: 12px; top: 12px; z-index: 10;"> 
                                <ion-icon class="<?php echo esc_attr($icones); ?>" style="font-size: 0.8rem;"></ion-icon> 
                                <span><?php echo esc_html($chamada); ?></span>
                            </span>
                        <?php endif; ?>
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
    $showList = isset( $attributes['showList'] ) ? filter_var( $attributes['showList'], FILTER_VALIDATE_BOOLEAN ) : true;
    $showListThumbs = isset( $attributes['showListThumbs'] ) ? filter_var( $attributes['showListThumbs'], FILTER_VALIDATE_BOOLEAN ) : true;
    $showBadges = isset( $attributes['showBadges'] ) ? filter_var( $attributes['showBadges'], FILTER_VALIDATE_BOOLEAN ) : true;
    
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
    $args = u_correio68_apply_tag_keyword_filters( $args, $attributes );

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
                                <div class="card-img-overlay gradiente space d-flex flex-column justify-content-end">
                                    <?php if ( $showBadges ) :
                                        $cor = function_exists('get_field') ? get_field( 'cor' ) : '';
                                        $icones = function_exists('get_field') ? get_field( 'icones' ) : '';
                                        $chamada = function_exists('get_field') ? get_field( 'chamada' ) : '';
                                        if ( !empty($chamada) ) : ?>
                                            <span class="badge badge-light text-white badge-pill dm-badge-overlay" style="background-color:<?php echo esc_attr($cor); ?> !important; font-size: 0.7rem; padding: 0.25rem 0.5rem;"> 
                                                <ion-icon class="<?php echo esc_attr($icones); ?>" style="font-size: 0.8rem;"></ion-icon> 
                                                <span><?php echo esc_html($chamada); ?></span>
                                            </span>
                                        <?php endif;
                                    endif; ?>
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
            <?php if ( $showList ): ?>
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
                                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="dm-card-link">
                                        <div class="media list-item">
                                            <?php if ( $showListThumbs ): ?>
                                            <div class="list-thumb">
                                              <?php 
                                              if ( class_exists('PG_Image') ) {
                                                  echo PG_Image::getPostImage(get_the_ID(), 'thumbnail', array('class' => 'img-fluid rounded w-100 h-100', 'style' => 'object-fit: cover;'), null, null);
                                              } else {
                                                  echo get_the_post_thumbnail(get_the_ID(), 'thumbnail', array('class' => 'img-fluid rounded w-100 h-100', 'style' => 'object-fit: cover;'));
                                              }
                                              ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="media-body list-content">
                                            <?php if ( $showBadges ) :
                                                $cor = function_exists('get_field') ? get_field( 'cor' ) : '';
                                                $icones = function_exists('get_field') ? get_field( 'icones' ) : '';
                                                $chamada = function_exists('get_field') ? get_field( 'chamada' ) : '';
                                                if ( !empty($chamada) ) : ?>
                                                    <div class="mb-1">
                                                        <span class="badge badge-light text-white badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important; font-size: 0.65rem; padding: 0.2rem 0.4rem;"> 
                                                            <ion-icon class="<?php echo esc_attr($icones); ?>" style="font-size: 0.7rem;"></ion-icon> 
                                                            <span><?php echo esc_html($chamada); ?></span>
                                                        </span>
                                                    </div>
                                                <?php endif;
                                            endif; ?>
                                            <h5 class="mt-0" style="<?php echo esc_attr( $titleStyle ); ?>"><a href="<?php the_permalink(); ?>" class="text-dark"><?php the_title(); ?></a></h5>
                                            <small class="text-muted"><?php echo get_the_date(); ?></small>
                                        </div>
                                        </div>
                                    </a>
                                </div>
                                <?php
                             endif;
                        endfor;
                        ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
    // PASSO 3 - WordPress Compatibility: Icons mapped to CSS classes (clear, cloudy, rain, storm, snow)
    if ( in_array( $code, array(0) ) ) { $desc = 'Céu limpo'; $icon = 'clear'; $fa = 'fa-sun-o'; }
    elseif ( in_array( $code, array(1,2) ) ) { $desc = 'Parcialmente nublado'; $icon = 'cloudy'; $fa = 'fa-cloud'; }
    elseif ( in_array( $code, array(3) ) ) { $desc = 'Nublado'; $icon = 'cloudy'; $fa = 'fa-cloud'; }
    elseif ( in_array( $code, array(45,48) ) ) { $desc = 'Neblina'; $icon = 'cloudy'; $fa = 'fa-cloud'; } // Mist mapped to cloudy
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
    
    // Enqueue Slick for forecast slider
    if ( ! is_admin() ) {
        wp_enqueue_style( 'u_seisbarra8-slick' );
        wp_enqueue_style( 'u_seisbarra8-slicktheme' );
        wp_enqueue_script( 'u_seisbarra8-slick' );
    }
    
    // Get current day name and format date
    $current_date = new DateTime( $data['time'] );
    $day_name = date_i18n( 'l', $current_date->getTimestamp() );
    $date_formatted = date_i18n( 'd M Y', $current_date->getTimestamp() );
    
    // Get humidity if available
    $humidity = isset( $data['relative_humidity_2m'] ) ? intval( $data['relative_humidity_2m'] ) : null;
    
    // Get precipitation
    $header_prpct = null; $header_prsum = null;
    if ( ! empty( $data['daily'] ) && ! empty( $data['daily']['precipitation_probability_mean'] ) && isset( $data['daily']['precipitation_probability_mean'][0] ) ) {
        $header_prpct = intval( $data['daily']['precipitation_probability_mean'][0] );
    }
    if ( ( $header_prpct === null ) && ! empty( $data['daily'] ) && ! empty( $data['daily']['precipitation_sum'] ) && isset( $data['daily']['precipitation_sum'][0] ) ) {
        $header_prsum = floatval( $data['daily']['precipitation_sum'][0] );
    }
    ?>
    <div class="weather-widget">
        <!-- TOP SIDE: Gradient Blue -->
        <div class="weather-side">
            <div class="weather-gradient"></div>
            
            <div class="date-container">
                <h2 class="date-dayname"><?php echo esc_html( $day_name ); ?></h2>
                <span class="date-day"><?php echo esc_html( $date_formatted ); ?></span>
            </div>
            
            <div class="weather-container">
                <div class="current-top">
                    <div class="weather-icon-large icon-<?php echo esc_attr( $icon ); ?>" style="position:relative;width:80px;height:80px;margin:0;">
                        <div class="icon-base" style="position:absolute;inset:0;"></div>
                        <?php if ( in_array( $icon, array('rain','storm'), true ) ) : ?>
                            <div class="rain" style="position:absolute;inset:0;"></div>
                        <?php endif; ?>
                        <div class="fa-overlay" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                            <i class="fa <?php echo esc_attr( $fa ); ?> weather-fa-icon-large" aria-hidden="true"></i>
                        </div>
                    </div>

                    <h1 class="weather-temp">
                        <span class="temp-value"><?php echo esc_html( round( $temp ) ); ?></span><span class="temp-unit">°</span>
                    </h1>
                </div>
                <h3 class="weather-desc"><?php echo esc_html( $desc ); ?></h3>
                <?php if ( ! empty( $city ) ) : ?>
                    <div class="weather-location">
                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                        <span class="city-name"><?php echo esc_html( $city ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- BOTTOM SIDE: Dark Info -->
        <div class="info-side">
            <div class="today-info-container">
                <div class="today-info">
                    <?php if ( $showRain ) : ?>
                    <div class="info-item">
                        <span class="info-title"><i class="fa fa-tint" aria-hidden="true"></i> Chuva</span>
                        <span class="info-value">
                            <?php 
                            if ( is_int( $header_prpct ) ) {
                                echo esc_html( $header_prpct ) . ' %';
                            } elseif ( is_float( $header_prsum ) ) {
                                echo esc_html( round( $header_prsum, 1 ) ) . ' mm';
                            } else {
                                echo '0 %';
                            }
                            ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ( ! is_null( $humidity ) ) : ?>
                    <div class="info-item">
                        <span class="info-title">Umidade</span>
                        <span class="info-value"><?php echo esc_html( $humidity ); ?> %</span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ( $showWind && is_numeric( $windspeed ) ) : ?>
                    <div class="info-item">
                        <span class="info-title"><i class="fa fa-flag" aria-hidden="true"></i> Vento</span>
                        <span class="info-value"><?php echo esc_html( round( $windspeed ) ); ?> <?php echo esc_html( $wind_unit ); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ( $showForecast ) : ?>
            <!-- Forecast Slider (Slick) -->
            <div class="week-container">
                <div class="weather-forecast-slider">
                    <?php 
                    $daily = isset( $data['daily'] ) ? $data['daily'] : array();
                    if ( ! empty( $daily ) && ! empty( $daily['time'] ) ) :
                        $total_days = min( $forecastDays + 1, count( $daily['time'] ) );
                        for ( $i = 0; $i < $total_days; $i++ ) {
                            $date = sanitize_text_field( $daily['time'][$i] );
                            $wcode = isset( $daily['weathercode'][$i] ) ? intval( $daily['weathercode'][$i] ) : 0;
                            $tmax  = isset( $daily['temperature_2m_max'][$i] ) ? floatval( $daily['temperature_2m_max'][$i] ) : null;
                            $tmin  = isset( $daily['temperature_2m_min'][$i] ) ? floatval( $daily['temperature_2m_min'][$i] ) : null;
                            
                            if ( $units === 'f' && is_numeric( $tmax ) ) {
                                $tmax = round( ( $tmax * 9/5 ) + 32, 1 );
                            }
                            if ( $units === 'f' && is_numeric( $tmin ) ) {
                                $tmin = round( ( $tmin * 9/5 ) + 32, 1 );
                            }
                            
                            // Map weather code to icon
                            $forecast_icon = 'fa-sun-o';
                            $forecast_icon_class = 'clear';
                            if ( in_array( $wcode, array(1,2,3,45,48), true ) ) { $forecast_icon = 'fa-cloud'; $forecast_icon_class = 'cloudy'; }
                            elseif ( in_array( $wcode, array(51,53,55,56,57,61,63,65,66,67,80,81,82), true ) ) { $forecast_icon = 'fa-umbrella'; $forecast_icon_class = 'rain'; }
                            elseif ( in_array( $wcode, array(71,73,75,77), true ) ) { $forecast_icon = 'fa-snowflake-o'; $forecast_icon_class = 'snow'; }
                            elseif ( in_array( $wcode, array(95,96,99), true ) ) { $forecast_icon = 'fa-bolt'; $forecast_icon_class = 'storm'; }
                            
                            $day_date = new DateTime( $date );
                            $day_short = date_i18n( 'D', $day_date->getTimestamp() );
                            $day_full = date_i18n( 'j \d\e M', $day_date->getTimestamp() );
                            $is_today = ( $i === 0 ) ? ' active' : '';
                            ?>
                            <div class="forecast-slide">
                                <div class="forecast-day-card<?php echo esc_attr( $is_today ); ?>">
                                    <div class="day-icon icon-<?php echo esc_attr( $forecast_icon_class ); ?>" style="position:relative;width:48px;height:48px;margin:0 auto 12px;">
                                        <i class="fa <?php echo esc_attr( $forecast_icon ); ?>" aria-hidden="true"></i>
                                    </div>
                                    <span class="day-name"><?php echo esc_html( $day_short ); ?></span>
                                    <span class="day-date"><?php echo esc_html( $day_full ); ?></span>
                                    <div class="temps">
                                        <span class="temp-max"><?php echo is_numeric( $tmax ) ? esc_html( round( $tmax ) ) . '°' : '-'; ?></span>
                                        <span class="temp-min"><?php echo is_numeric( $tmin ) ? esc_html( round( $tmin ) ) . '°' : '-'; ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    endif;
                    ?>
                </div>
            </div>
            
            <script>
            jQuery(document).ready(function($) {
                if ( $('.weather-forecast-slider').length ) {
                    $('.weather-forecast-slider').slick({
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 2000,
                        dots: true,
                        arrows: false,
                        infinite: true,
                        speed: 300
                    });
                }
            });
            </script>
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

/**
 * Render Image Slider Block (Slick.js)
 */
function u_correio68_render_image_slider( $attributes ) {
    // Enqueue Slick dependencies
    wp_enqueue_style( 'u_seisbarra8-slick' );
    wp_enqueue_style( 'u_seisbarra8-slicktheme' );
    wp_enqueue_script( 'u_seisbarra8-slick' );
    
    $images = isset( $attributes['images'] ) ? $attributes['images'] : array();
    
    if ( empty( $images ) ) {
        return '<div class="alert alert-info">Adicione imagens ao slider</div>';
    }
    
    // Configurações
    $speed = isset( $attributes['speed'] ) ? absint( $attributes['speed'] ) : 3000;
    $autoplaySpeed = isset( $attributes['autoplaySpeed'] ) ? absint( $attributes['autoplaySpeed'] ) : 5000;
    $vertical = isset( $attributes['vertical'] ) ? filter_var( $attributes['vertical'], FILTER_VALIDATE_BOOLEAN ) : false;
    $rtl = isset( $attributes['rtl'] ) ? filter_var( $attributes['rtl'], FILTER_VALIDATE_BOOLEAN ) : false;
    $fade = isset( $attributes['fade'] ) ? filter_var( $attributes['fade'], FILTER_VALIDATE_BOOLEAN ) : false;
    $autoplay = isset( $attributes['autoplay'] ) ? filter_var( $attributes['autoplay'], FILTER_VALIDATE_BOOLEAN ) : true;
    $pauseOnHover = isset( $attributes['pauseOnHover'] ) ? filter_var( $attributes['pauseOnHover'], FILTER_VALIDATE_BOOLEAN ) : true;
    $slidesToShow = isset( $attributes['slidesToShow'] ) ? absint( $attributes['slidesToShow'] ) : 1;
    $slidesToScroll = isset( $attributes['slidesToScroll'] ) ? absint( $attributes['slidesToScroll'] ) : 1;
    
    $slider_id = 'image-slider-' . uniqid();
    
    ob_start();
    ?>
    <div class="image-slider-wrapper" style="margin: 20px 0;">
        <div class="<?php echo esc_attr( $slider_id ); ?> image-slider-slick">
            <?php foreach ( $images as $image ) : 
                $image_id = isset( $image['id'] ) ? absint( $image['id'] ) : 0;
                $image_url = isset( $image['url'] ) ? esc_url( $image['url'] ) : '';
                $link_url = isset( $image['link'] ) ? esc_url( $image['link'] ) : '';
                $alt_text = isset( $image['alt'] ) ? esc_attr( $image['alt'] ) : '';
                
                if ( empty( $image_url ) && $image_id > 0 ) {
                    $image_url = wp_get_attachment_image_url( $image_id, 'full' );
                    $alt_text = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
                }
                
                if ( empty( $image_url ) ) continue;
            ?>
                <div class="slide-item">
                    <?php if ( ! empty( $link_url ) ) : ?>
                        <a href="<?php echo $link_url; ?>" target="_blank" rel="noopener">
                            <img src="<?php echo $image_url; ?>" alt="<?php echo $alt_text; ?>" class="img-fluid w-100" style="display: block; height: auto;">
                        </a>
                    <?php else : ?>
                        <img src="<?php echo $image_url; ?>" alt="<?php echo $alt_text; ?>" class="img-fluid w-100" style="display: block; height: auto;">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
    (function($){
        $(function(){
            var $slider = $('.<?php echo esc_js( $slider_id ); ?>');
            if (!$slider.length || typeof $.fn.slick !== 'function') {
                console.log('[ImageSlider] Element or Slick not available');
                return;
            }
            
            if ($slider.hasClass('slick-initialized')) {
                console.log('[ImageSlider] Already initialized');
                return;
            }
            
            $slider.slick({
                slidesToShow: <?php echo $slidesToShow; ?>,
                slidesToScroll: <?php echo $slidesToScroll; ?>,
                speed: <?php echo $speed; ?>,
                autoplay: <?php echo $autoplay ? 'true' : 'false'; ?>,
                autoplaySpeed: <?php echo $autoplaySpeed; ?>,
                arrows: false,
                dots: false,
                infinite: true,
                vertical: <?php echo $vertical ? 'true' : 'false'; ?>,
                rtl: <?php echo $rtl ? 'true' : 'false'; ?>,
                fade: <?php echo $fade ? 'true' : 'false'; ?>,
                pauseOnHover: <?php echo $pauseOnHover ? 'true' : 'false'; ?>,
                pauseOnFocus: true,
                adaptiveHeight: false,
                cssEase: 'ease-in-out',
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
            
            console.log('[ImageSlider] ✓ Initialized: <?php echo esc_js( $slider_id ); ?>');
        });
    })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}

