// Enfileira JS dos blocos personalizados no editor
function seisdeagosto_enqueue_block_editor_assets() {
    wp_enqueue_script(
        'seisdeagosto-custom-blocks',
        get_template_directory_uri() . '/assets/js/custom-blocks.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-api-fetch'),
        filemtime(get_template_directory() . '/assets/js/custom-blocks.js'),
        true
    );
}
add_action('enqueue_block_editor_assets', 'seisdeagosto_enqueue_block_editor_assets');

if ( ! function_exists( 'u_seisbarra8_setup' ) ) :

function u_seisbarra8_setup() {

    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    /* Pinegrow generated Load Text Domain Begin */
    // Load legacy and new text domains to prevent translation issues during rename
    load_theme_textdomain( 'u_seisbarra8', get_template_directory() . '/languages' );
    load_theme_textdomain( 'seideagosto', get_template_directory() . '/languages' );
    /* Pinegrow generated Load Text Domain End */

    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    /*
     * Let WordPress manage the document title.
     */
    add_theme_support( 'title-tag' );

    // Block theme capabilities (align with Twenty Twenty-Four)
    add_theme_support( 'block-templates' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'appearance-tools' );
    add_theme_support( 'custom-units' );
    add_theme_support( 'custom-line-height' );

    /*
     * Enable support for Post Thumbnails on posts and pages.
     */
    add_theme_support( 'post-thumbnails' );
    
        /**
         * SEO and Open Graph meta for single posts.
         */
        function u_seisbarra8_single_seo_opengraph() {
            if ( ! is_single() ) return;

            global $post;
            if ( ! $post ) return;

            $title       = wp_strip_all_tags( get_the_title( $post ) );
            $permalink   = get_permalink( $post );
            $site_name   = get_bloginfo( 'name' );
            $excerpt     = has_excerpt( $post ) ? get_the_excerpt( $post ) : '';
            if ( empty( $excerpt ) ) {
                // Fallback: trim content without shortcodes/HTML
                $content = wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post ) ) );
                $excerpt = wp_trim_words( $content, 32, '' );
            }
            $description = mb_substr( trim( $excerpt ), 0, 160 );

            // Featured image (full) if available
            $image_url = '';
            if ( has_post_thumbnail( $post ) ) {
                $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'full' );
                if ( is_array( $src ) && ! empty( $src[0] ) ) {
                    $image_url = $src[0];
                }
            }

            $published  = get_the_date( DATE_W3C, $post );
            $modified   = get_the_modified_date( DATE_W3C, $post );
            $author     = get_the_author_meta( 'display_name', $post->post_author );

            // Optional publisher logo from custom_logo
            $publisher_logo = '';
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            if ( $custom_logo_id ) {
                $logo_src = wp_get_attachment_image_src( $custom_logo_id, 'full' );
                if ( is_array( $logo_src ) && ! empty( $logo_src[0] ) ) {
                    $publisher_logo = $logo_src[0];
                }
            }

            // Canonical (avoid duplicate if WP already adds rel_canonical)
            if ( ! has_action( 'wp_head', 'rel_canonical' ) ) {
                echo '<link rel="canonical" href="' . esc_url( $permalink ) . '" />' . "\n";
            }

            // Basic SEO description
            if ( $description ) {
                echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
            }

            // Open Graph
            echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '" />' . "\n";
            echo '<meta property="og:type" content="article" />' . "\n";
            echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
            echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
            echo '<meta property="og:url" content="' . esc_url( $permalink ) . '" />' . "\n";
            echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
            if ( $image_url ) {
                echo '<meta property="og:image" content="' . esc_url( $image_url ) . '" />' . "\n";
            }

            // Twitter Card
            echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
            echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
            echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
            if ( $image_url ) {
                echo '<meta name="twitter:image" content="' . esc_url( $image_url ) . '" />' . "\n";
            }

            // Article-specific OG
            echo '<meta property="article:published_time" content="' . esc_attr( $published ) . '" />' . "\n";
            echo '<meta property="article:modified_time" content="' . esc_attr( $modified ) . '" />' . "\n";
            $cats = wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) );
            if ( ! empty( $cats ) ) {
                echo '<meta property="article:section" content="' . esc_attr( $cats[0] ) . '" />' . "\n";
            }
            $tags = wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) );
            if ( ! empty( $tags ) ) {
                foreach ( $tags as $t ) {
                    echo '<meta property="article:tag" content="' . esc_attr( $t ) . '" />' . "\n";
                }
            }

            // JSON-LD Article/NewsArticle
            $is_news = in_array( 'post', array( get_post_type( $post ) ), true );
            $schema  = array(
                '@context'        => 'https://schema.org',
                '@type'           => $is_news ? 'NewsArticle' : 'Article',
                'mainEntityOfPage'=> array(
                    '@type' => 'WebPage',
                    '@id'   => $permalink,
                ),
                'headline'        => $title,
                'description'     => $description,
                'datePublished'   => $published,
                'dateModified'    => $modified,
                'author'          => array(
                    '@type' => 'Person',
                    'name'  => $author,
                ),
                'publisher'       => array(
                    '@type' => 'Organization',
                    'name'  => $site_name,
                ),
            );
            if ( $publisher_logo ) {
                $schema['publisher']['logo'] = array(
                    '@type' => 'ImageObject',
                    'url'   => $publisher_logo,
                );
            }
            if ( $image_url ) {
                $schema['image'] = array( $image_url );
            }

            echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
        }
        add_action( 'wp_head', 'u_seisbarra8_single_seo_opengraph', 5 );

    // Add menus.
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'u_seisbarra8' ),
        'social'  => __( 'Social Links Menu', 'u_seisbarra8' ),
    ) );

/*
     * Register custom menu locations
     */
    /* Pinegrow generated Register Menus Begin */

    register_nav_menu(  'categorias', __( 'Categorias para a capa do site', 'u_seisbarra8' )  );

    /* Pinegrow generated Register Menus End */

/*
    * Set image sizes
     */
    /* Pinegrow generated Image Sizes Begin */

    add_image_size( 'destaque', 300, 180, true );
    add_image_size( 'destatquegrande', 730, 410, true );
    update_option( 'thumbnail_size_w', 250 );
    update_option( 'thumbnail_size_h', 120 );
    update_option( 'thumbnail_crop', 1 );

    /* Pinegrow generated Image Sizes End */

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ) );

    /*
     * Enable support for Post Formats.
     */
    add_theme_support( 'post-formats', array(
        'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
    ) );

    /*
     * Enable support for Page excerpts.
     */
     add_post_type_support( 'page', 'excerpt' );

    /*
     * Enable support for Editor Styles.
     */
    add_theme_support( 'editor-styles' );
    add_editor_style( 'css/theme.css' );
    add_editor_style( 'bootstrap/css/bootstrap.css' );
    add_editor_style( 'css/blocks-layout.css' );

    /*
     * AMP support (requires AMP plugin).
     */
    add_theme_support( 'amp', array(
        'paired' => true,
    ) );
}
endif; // u_seisbarra8_setup

add_action( 'after_setup_theme', 'u_seisbarra8_setup' );

/**
 * Process shortcodes inside wp:html blocks in FSE templates.
 * This ensures [u68_*] shortcodes render correctly in header/footer/sidebar.
 */
add_filter( 'render_block_core/html', function( $block_content, $block ) {
    // Only process if the content contains our shortcodes
    if ( strpos( $block_content, '[u68_' ) !== false || strpos( $block_content, '[' ) !== false ) {
        $block_content = do_shortcode( $block_content );
    }
    return $block_content;
}, 10, 2 );

/**
 * Also process shortcodes in template parts content.
 */
add_filter( 'render_block_core/template-part', function( $block_content, $block ) {
    // Process shortcodes in template part content
    if ( strpos( $block_content, '[u68_' ) !== false || strpos( $block_content, '[' ) !== false ) {
        $block_content = do_shortcode( $block_content );
    }
    return $block_content;
}, 10, 2 );

/**
 * Global filter to ensure shortcodes work in all block content.
 * This catches any shortcodes that might slip through specific block filters.
 */
add_filter( 'render_block', function( $block_content, $block ) {
    // Process shortcodes if content contains bracket notation
    if ( strpos( $block_content, '[u68_' ) !== false ) {
        $block_content = do_shortcode( $block_content );
    }
    return $block_content;
}, 10, 2 );

/**
 * Shortcode: Sidebar intro text (classic theme setting).
 */
function u68_sidebar_intro_shortcode() {
    $default = function_exists( 'u_correio68_get_sidebar_intro_default_text' )
        ? u_correio68_get_sidebar_intro_default_text()
        : '';
    $text = get_theme_mod( 'u_correio68_sidebar_intro_text', $default );
    if ( empty( $text ) ) {
        return '';
    }
    return wp_kses_post( nl2br( $text ) );
}
add_shortcode( 'u68_sidebar_intro', 'u68_sidebar_intro_shortcode' );

/**
 * Shortcode: Footer text (classic theme setting).
 */
function u68_footer_text_shortcode() {
    $default = function_exists( 'u_seisbarra8_get_footer_default_text' )
        ? u_seisbarra8_get_footer_default_text()
        : sprintf(
            'Orgulhosamente feito com <i class="fa fa-heart"></i> no Acre | <b>%s</b>',
            esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ?: home_url() )
        );
    $text = get_theme_mod( 'footer_text', $default );
    if ( empty( $text ) ) {
        return '';
    }
    return wp_kses_post( $text );
}
add_shortcode( 'u68_footer_text', 'u68_footer_text_shortcode' );

/**
 * Shortcode: Weather Widget with City Selector
 * Usage: [u68_weather_selector default_city="Rio Branco" default_lat="-9.975" default_lon="-67.824"]
 */
function u68_weather_selector_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'default_city' => '',
            'default_lat'  => '',
            'default_lon'  => '',
            'show_forecast' => 'true',
            'forecast_days' => '5',
            'units'         => 'c',
            'theme'         => 'dark', // dark or light
        ),
        $atts,
        'u68_weather_selector'
    );

    $widget_id = 'weather-selector-' . wp_unique_id();
    $theme_class = $atts['theme'] === 'dark' ? 'city-selector-dark' : '';
    
    // Build initial weather block if default city provided
    $initial_weather = '';
    if ( ! empty( $atts['default_lat'] ) && ! empty( $atts['default_lon'] ) ) {
        $weather_atts = array(
            'cityName'     => sanitize_text_field( $atts['default_city'] ),
            'latitude'     => sanitize_text_field( $atts['default_lat'] ),
            'longitude'    => sanitize_text_field( $atts['default_lon'] ),
            'units'        => $atts['units'],
            'showWind'     => true,
            'showRain'     => true,
            'showForecast' => $atts['show_forecast'] === 'true',
            'forecastDays' => intval( $atts['forecast_days'] ),
        );
        if ( function_exists( 'u_correio68_render_weather' ) ) {
            $initial_weather = u_correio68_render_weather( $weather_atts );
        }
    }

    ob_start();
    ?>
    <div id="<?php echo esc_attr( $widget_id ); ?>" class="weather-selector-widget" 
         data-units="<?php echo esc_attr( $atts['units'] ); ?>"
         data-show-forecast="<?php echo esc_attr( $atts['show_forecast'] ); ?>"
         data-forecast-days="<?php echo esc_attr( $atts['forecast_days'] ); ?>">
        
        <div class="weather-city-selector-wrap <?php echo esc_attr( $theme_class ); ?>"></div>
        
        <div class="weather-display-area">
            <?php if ( ! empty( $initial_weather ) ) : ?>
                <?php echo $initial_weather; ?>
            <?php else : ?>
                <div class="weather-placeholder text-center p-4">
                    <i class="fa fa-cloud fa-3x mb-3" style="opacity:0.5"></i>
                    <p class="mb-0">Selecione uma cidade para ver a previsão do tempo</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    jQuery(function($) {
        var $widget = $('#<?php echo esc_js( $widget_id ); ?>');
        var $selector = $widget.find('.weather-city-selector-wrap');
        var $display = $widget.find('.weather-display-area');
        var units = $widget.data('units') || 'c';
        var showForecast = $widget.data('show-forecast') !== false;
        var forecastDays = parseInt($widget.data('forecast-days')) || 5;
        
        // Initialize city selector
        $selector.citySelector({
            placeholder: 'Buscar cidade...',
            language: 'pt',
            onSelect: function(cityData) {
                // Show loading
                $display.html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2">Carregando clima...</p></div>');
                
                // Fetch weather via AJAX
                $.ajax({
                    url: '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>',
                    type: 'POST',
                    data: {
                        action: 'u68_get_weather',
                        city: cityData.name,
                        lat: cityData.latitude,
                        lon: cityData.longitude,
                        units: units,
                        show_forecast: showForecast ? 1 : 0,
                        forecast_days: forecastDays,
                        nonce: '<?php echo wp_create_nonce( 'u68_weather_nonce' ); ?>'
                    },
                    success: function(response) {
                        if (response.success && response.data.html) {
                            $display.html(response.data.html);
                            // Re-init slick if needed
                            if (typeof $.fn.slick !== 'undefined') {
                                $display.find('.weather-forecast-slider:not(.slick-initialized)').slick({
                                    dots: true,
                                    infinite: false,
                                    slidesToShow: 4,
                                    slidesToScroll: 1,
                                    arrows: false,
                                    accessibility: false,
                                    responsive: [
                                        { breakpoint: 992, settings: { slidesToShow: 3 } },
                                        { breakpoint: 768, settings: { slidesToShow: 2 } },
                                        { breakpoint: 480, settings: { slidesToShow: 1 } }
                                    ]
                                });
                            }
                        } else {
                            $display.html('<div class="alert alert-warning">Não foi possível obter os dados do clima.</div>');
                        }
                    },
                    error: function() {
                        $display.html('<div class="alert alert-danger">Erro ao carregar dados do clima.</div>');
                    }
                });
            }
        });
        
        <?php if ( ! empty( $atts['default_city'] ) && ! empty( $atts['default_lat'] ) && ! empty( $atts['default_lon'] ) ) : ?>
        // Set initial city
        var citySelector = $selector.data('citySelector');
        if (citySelector) {
            citySelector.setCity({
                name: '<?php echo esc_js( $atts['default_city'] ); ?>',
                latitude: <?php echo floatval( $atts['default_lat'] ); ?>,
                longitude: <?php echo floatval( $atts['default_lon'] ); ?>,
                country: 'Brasil',
                country_code: 'BR'
            });
        }
        <?php endif; ?>
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'u68_weather_selector', 'u68_weather_selector_shortcode' );

/**
 * AJAX handler for weather data
 */
function u68_ajax_get_weather() {
    check_ajax_referer( 'u68_weather_nonce', 'nonce' );
    
    $city = isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';
    $lat = isset( $_POST['lat'] ) ? sanitize_text_field( $_POST['lat'] ) : '';
    $lon = isset( $_POST['lon'] ) ? sanitize_text_field( $_POST['lon'] ) : '';
    $units = isset( $_POST['units'] ) ? sanitize_text_field( $_POST['units'] ) : 'c';
    $show_forecast = ! empty( $_POST['show_forecast'] );
    $forecast_days = isset( $_POST['forecast_days'] ) ? intval( $_POST['forecast_days'] ) : 5;
    
    if ( empty( $lat ) || empty( $lon ) ) {
        wp_send_json_error( array( 'message' => 'Coordenadas inválidas' ) );
    }
    
    $weather_atts = array(
        'cityName'     => $city,
        'latitude'     => $lat,
        'longitude'    => $lon,
        'units'        => $units,
        'showWind'     => true,
        'showRain'     => true,
        'showForecast' => $show_forecast,
        'forecastDays' => $forecast_days,
    );
    
    if ( function_exists( 'u_correio68_render_weather' ) ) {
        $html = u_correio68_render_weather( $weather_atts );
        wp_send_json_success( array( 'html' => $html ) );
    } else {
        wp_send_json_error( array( 'message' => 'Função de clima não disponível' ) );
    }
}
add_action( 'wp_ajax_u68_get_weather', 'u68_ajax_get_weather' );
add_action( 'wp_ajax_nopriv_u68_get_weather', 'u68_ajax_get_weather' );

/**
 * Shortcode: Render a widget area with optional condition.
 * Usage: [u68_widget_area id="left-sidebar" class="col-md-4 widget-area" role="complementary" condition="show_left_sidebar"]
 */
function u68_widget_area_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'id'        => '',
            'class'     => '',
            'role'      => '',
            'condition' => '',
        ),
        $atts,
        'u68_widget_area'
    );

    $sidebar_id = sanitize_text_field( $atts['id'] );
    if ( empty( $sidebar_id ) ) {
        return '';
    }

    if ( $atts['condition'] ) {
        $flag = (bool) get_theme_mod( sanitize_key( $atts['condition'] ), false );
        if ( ! $flag ) {
            return '';
        }
    }

    if ( ! is_active_sidebar( $sidebar_id ) ) {
        return '';
    }

    $classes = trim( sanitize_text_field( $atts['class'] ) );
    $role    = trim( sanitize_text_field( $atts['role'] ) );
    $role    = $role ? ' role="' . esc_attr( $role ) . '"' : '';

    ob_start();
    echo '<div class="' . esc_attr( $classes ) . '" id="' . esc_attr( $sidebar_id ) . '"' . $role . '>';
    dynamic_sidebar( $sidebar_id );
    echo '</div>';
    return ob_get_clean();
}
add_shortcode( 'u68_widget_area', 'u68_widget_area_shortcode' );

/**
 * Shortcode: Render menus using classic walker when available.
 * Usage: [u68_nav_menu location="categorias" class="navbar-nav ..." depth="2" slider="mobile"]
 */
function u68_nav_menu_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'location' => '',
            'menu'     => '',
            'class'    => '',
            'depth'    => 2,
            'dropdown' => 'true',
            'slider'   => '',  // 'mobile' para habilitar slider em dispositivos móveis
        ),
        $atts,
        'u68_nav_menu'
    );

    $location = sanitize_key( $atts['location'] );
    $menu     = sanitize_text_field( $atts['menu'] );
    $class    = sanitize_text_field( $atts['class'] );
    $depth    = absint( $atts['depth'] );
    $dropdown = ( $atts['dropdown'] === 'true' || $atts['dropdown'] === '1' );
    $slider   = sanitize_key( $atts['slider'] );

    if ( $location && ! has_nav_menu( $location ) ) {
        return '';
    }

    // Use Bootstrap navwalker if dropdown is enabled and walker class exists
    $walker = null;
    if ( $dropdown && class_exists( 'WP_Bootstrap4_Navwalker' ) ) {
        $walker = new WP_Bootstrap4_Navwalker();
    }

    // Se for menu de categorias, gera HTML customizado com <div>
    if ( $location === 'categorias' ) {
        $menu_items = wp_get_nav_menu_items( $menu ? $menu : get_nav_menu_locations()['categorias'] ?? '' );
        if ( ! $menu_items ) {
            $menu_obj = wp_get_nav_menu_object( get_nav_menu_locations()['categorias'] ?? '' );
            if ( $menu_obj ) {
                $menu_items = wp_get_nav_menu_items( $menu_obj->term_id );
            }
        }
        if ( $menu_items ) {
            // Organiza itens por parent
            $items_by_parent = array();
            foreach ( $menu_items as $item ) {
                $parent = $item->menu_item_parent ? $item->menu_item_parent : 0;
                if ( ! isset( $items_by_parent[ $parent ] ) ) $items_by_parent[ $parent ] = array();
                $items_by_parent[ $parent ][] = $item;
            }
            // Função recursiva para renderizar menu
            $render_menu = function( $parent = 0 ) use ( &$render_menu, $items_by_parent ) {
                $html = '';
                if ( ! empty( $items_by_parent[ $parent ] ) ) {
                    foreach ( $items_by_parent[ $parent ] as $item ) {
                        $has_children = ! empty( $items_by_parent[ $item->ID ] );
                        $dropdown_class = $has_children ? ' dropdown' : '';
                        $toggle_attr = $has_children ? ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' : '';
                        $html .= '<div class="nav-item' . $dropdown_class . '">';
                        $html .= '<a class="nav-link' . ( $has_children ? ' dropdown-toggle' : '' ) . '" href="' . esc_url( $item->url ) . '"' . $toggle_attr . '>' . esc_html( $item->title ) . '</a>';
                        if ( $has_children ) {
                            $html .= '<div class="dropdown-menu">' . $render_menu( $item->ID ) . '</div>';
                        }
                        $html .= '</div>';
                    }
                }
                return $html;
            };
            $menu_html = '<div class="' . esc_attr( $class ) . '">' . $render_menu( 0 ) . '</div>';
            if ( $slider === 'mobile' ) {
                $menu_html = '<div class="categories-slider-wrapper">' . $menu_html . '</div>';
            }
            return $menu_html;
        }
        return '';
    }
    // Menu padrão para outros locations
    $args = array(
        'menu'           => $menu,
        'theme_location' => $location,
        'menu_class'     => $class,
        'container'      => '',
        'depth'          => $depth,
        'echo'           => false,
        'fallback_cb'    => false,
    );
    if ( $walker ) {
        $args['walker'] = $walker;
    }
    $html = wp_nav_menu( $args );
    if ( $html && $slider === 'mobile' && $location === 'categorias' ) {
        $html = '<div class="categories-slider-wrapper">' . $html . '</div>';
    }
    return $html ? $html : '';
}
add_shortcode( 'u68_nav_menu', 'u68_nav_menu_shortcode' );

/**
 * Bootstrap 5 header brand markup.
 */
function u68_header_brand_markup() {
    ob_start();

    if ( has_custom_logo() ) {
        echo '<div class="navbar-brand mb-0 d-flex align-items-center gap-2">';
        the_custom_logo();
        echo '</div>';
    } else {
        echo '<a rel="home" class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
    }

    return trim( ob_get_clean() );
}

/**
 * Shortcode: header brand.
 * Usage: [u68_header_brand] or [u68_header_brand class="text-white p-0 m-0 h5 d-inline-block"]
 */
function u68_header_brand_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'class' => '',
        ),
        $atts,
        'u68_header_brand'
    );
    
    $custom_class = sanitize_text_field( $atts['class'] );
    
    ob_start();
    
    if ( has_custom_logo() ) {
        if ( $custom_class ) {
            echo '<div class="' . esc_attr( $custom_class ) . '">';
            the_custom_logo();
            echo '</div>';
        } else {
            echo '<div class="navbar-brand mb-0 d-flex align-items-center gap-2">';
            the_custom_logo();
            echo '</div>';
        }
    } else {
        $link_class = $custom_class ? $custom_class : 'navbar-brand d-flex align-items-center gap-2 fw-semibold';
        echo '<a rel="home" class="' . esc_attr( $link_class ) . '" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
    }
    
    return trim( ob_get_clean() );
}
add_shortcode( 'u68_header_brand', 'u68_header_brand_shortcode' );

/**
 * Shortcode: header search form with mobile toggle.
 */
function u68_header_search_shortcode() {
    $search_form = get_search_form( false );
    
    // Output desktop search + mobile toggle + mobile search wrapper
    $output = '';
    
    // Desktop: show search form directly
    $output .= '<div class="d-none d-lg-flex align-items-center">' . $search_form . '</div>';
    
    // Mobile: toggle button + expandable search
    $output .= '<button id="searchToggleMobile" class="btn btn-link text-white d-lg-none p-0 ms-2" type="button" aria-label="' . esc_attr__( 'Abrir busca', 'u_correio68' ) . '">';
    $output .= '<i class="fa fa-search fa-lg"></i>';
    $output .= '</button>';
    $output .= '<div id="mobileSearchWrapper" class="d-lg-none position-absolute start-0 end-0 bg-primary px-3 py-2" style="top: 100%; z-index: 1030;">';
    $output .= $search_form;
    $output .= '</div>';
    
    return $output;
}
add_shortcode( 'u68_header_search', 'u68_header_search_shortcode' );

/**
 * Add Bootstrap 5 classes to header menus.
 */
function u68_nav_item_classes( $classes, $item, $args, $depth ) {
    if ( isset( $args->theme_location ) && in_array( $args->theme_location, array( 'primary', 'categorias' ), true ) ) {
        if ( $depth === 0 ) {
            $classes[] = 'nav-item';

            if ( in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
                $classes[] = 'dropdown';
            }
        }
    }

    return $classes;
}
add_filter( 'nav_menu_css_class', 'u68_nav_item_classes', 10, 4 );

/**
 * Add link attributes for Bootstrap 5 menus.
 */
function u68_nav_link_attributes( $atts, $item, $args, $depth = 0 ) {
    if ( isset( $args->theme_location ) && in_array( $args->theme_location, array( 'primary', 'categorias' ), true ) ) {
        $base_class     = $depth > 0 ? 'dropdown-item' : 'nav-link';
        $existing_class = isset( $atts['class'] ) ? $atts['class'] . ' ' : '';
        $atts['class']  = trim( $existing_class . $base_class );

        if ( in_array( 'menu-item-has-children', (array) $item->classes, true ) && $depth === 0 ) {
            $atts['class']         .= ' dropdown-toggle';
            $atts['data-bs-toggle'] = 'dropdown';
            $atts['aria-expanded'] = 'false';
            $atts['role']          = 'button';
        }
    }

    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'u68_nav_link_attributes', 10, 4 );

/**
 * Submenu classes for Bootstrap 5 menus.
 */
function u68_nav_submenu_classes( $classes, $args, $depth ) {
    if ( isset( $args->theme_location ) && in_array( $args->theme_location, array( 'primary', 'categorias' ), true ) ) {
        $classes[] = 'dropdown-menu';
    }

    return $classes;
}
add_filter( 'nav_menu_submenu_css_class', 'u68_nav_submenu_classes', 10, 3 );

/**
 * Add body classes based on sidebar toggles.
 */
function u68_sidebar_body_classes( $classes ) {
    if ( ! get_theme_mod( 'show_left_sidebar', false ) ) {
        $classes[] = 'u68-no-left-sidebar';
    }
    if ( ! get_theme_mod( 'show_right_sidebar', false ) ) {
        $classes[] = 'u68-no-right-sidebar';
    }
    return $classes;
}
add_filter( 'body_class', 'u68_sidebar_body_classes' );

/**
 * AMP request helper (requires AMP plugin).
 */
function u_seisbarra8_is_amp() {
    return function_exists( 'amp_is_request' ) && amp_is_request();
}

/**
 * Disable MetaSlider output and assets on AMP.
 */
function u_seisbarra8_disable_metaslider_on_amp() {
    if ( ! u_seisbarra8_is_amp() || is_admin() ) {
        return;
    }

    // Block MetaSlider widgets.
    add_filter( 'widget_display_callback', function( $instance, $widget, $args ) {
        if ( isset( $widget->id_base ) && $widget->id_base === 'metaslider_widget' ) {
            return false;
        }
        if ( isset( $widget->id_base ) && stripos( $widget->id_base, 'metaslider' ) !== false ) {
            return false;
        }
        if ( isset( $widget->id ) && stripos( $widget->id, 'metaslider' ) !== false ) {
            return false;
        }
        return $instance;
    }, 10, 3 );

    // Remove MetaSlider shortcodes in content.
    add_filter( 'pre_do_shortcode_tag', function( $return, $tag, $attr, $m ) {
        if ( $tag === 'metaslider' ) {
            return '';
        }
        return $return;
    }, 10, 4 );

    // Dequeue MetaSlider scripts/styles if enqueued.
    add_action( 'wp_enqueue_scripts', function() {
        $handles = array(
            'metaslider-script',
            'metaslider-easing',
            'metaslider-flex-slider',
            'metaslider-nivo-slider',
            'metaslider-coin-slider',
            'metaslider-responsive-slider',
            'metaslider-public',
            'metaslider-pro-public',
        );
        foreach ( $handles as $handle ) {
            wp_dequeue_script( $handle );
            wp_dequeue_style( $handle );
        }
    }, 100 );
}
add_action( 'wp', 'u_seisbarra8_disable_metaslider_on_amp', 1 );

/**
 * Remove jQuery (and migrate) on AMP to avoid disallowed scripts enqueued by plugins.
 */
function u_seisbarra8_disable_jquery_on_amp() {
    if ( ! u_seisbarra8_is_amp() || is_admin() ) {
        return;
    }

    add_action( 'wp_enqueue_scripts', function() {
        wp_dequeue_script( 'jquery' );
        wp_dequeue_script( 'jquery-core' );
        wp_dequeue_script( 'jquery-migrate' );
        wp_deregister_script( 'jquery' );
        wp_deregister_script( 'jquery-core' );
        wp_deregister_script( 'jquery-migrate' );
    }, 100 );
}
add_action( 'wp', 'u_seisbarra8_disable_jquery_on_amp', 1 );

/**
 * Ensure jQuery (and jQuery Migrate when available) load early for plugin compatibility (e.g., MetaSlider).
 * Loads with very low priority so other enqueues can safely depend on it.
 */
function u_seisbarra8_ensure_jquery_first() {
    if ( u_seisbarra8_is_amp() ) {
        return;
    }
    // Enqueue core jQuery (do not replace with bundled files)
    wp_enqueue_script( 'jquery' );
    // Ensure jQuery loads in header group to satisfy plugins expecting early availability
    if ( wp_script_is( 'jquery', 'registered' ) ) {
        wp_script_add_data( 'jquery', 'group', 1 );
    }
    // Provide $ alias for legacy scripts that expect global $
    wp_add_inline_script( 'jquery', 'window.$ = window.$ || window.jQuery;', 'after' );
    // Enqueue jQuery Migrate if registered by WP (helps older plugins)
    if ( wp_script_is( 'jquery-migrate', 'registered' ) ) {
        wp_enqueue_script( 'jquery-migrate' );
        wp_script_add_data( 'jquery-migrate', 'group', 1 );
    }
}
add_action( 'wp_enqueue_scripts', 'u_seisbarra8_ensure_jquery_first', 0 );

/**
 * MetaSlider/JS validation helper: when logged-in admin opens ?debug=ms,
 * logs script queue and common MetaSlider markers in the browser console.
 */
function u_seisbarra8_debug_metaslider_footer() {
    if ( empty( $_GET['debug'] ) || $_GET['debug'] !== 'ms' ) return;
    $wp_scripts = wp_scripts();
    $queue      = is_array( $wp_scripts->queue ) ? $wp_scripts->queue : array();
    $handles    = array_values( $queue );
    $interesting = array();
    foreach ( $handles as $h ) {
        if ( preg_match( '/jquery|migrate|meta|slider|flex|nivo|slick/i', $h ) ) {
            $interesting[] = $h;
        }
    }
    $inline = "(function(){try{console.group('u_seisbarra8: MetaSlider Debug');";
    $inline .= "console.log('jQuery present:', !!window.jQuery, 'migrate:', !!(window.jQuery && window.jQuery.migrateWarnings));";
    $inline .= "console.log('Script queue (subset):', " . wp_json_encode( $interesting ) . ");";
    $inline .= "document.addEventListener('DOMContentLoaded', function(){var els={metaslider:document.querySelectorAll('.metaslider'),flex:document.querySelectorAll('.flexslider'),slides:document.querySelectorAll('.metaslider .slides, .flexslider .slides')};console.log('DOM markers:',{metaslider:els.metaslider.length,flex:els.flex.length,slides:els.slides.length});if(window.jQuery){console.log('jQuery.fn.flexslider exists:', !!jQuery.fn.flexslider);}});";
    $inline .= "console.groupEnd();}catch(e){console.error('MetaSlider Debug error:', e);}})();";
    // Attach after jQuery to reduce risk of syntax/ordering issues
    wp_add_inline_script( 'jquery', $inline, 'after' );
}
add_action( 'wp_enqueue_scripts', 'u_seisbarra8_debug_metaslider_footer', 11 );


if ( ! function_exists( 'u_seisbarra8_init' ) ) :

function u_seisbarra8_init() {


    // Use categories and tags with attachments
    register_taxonomy_for_object_type( 'category', 'attachment' );
    register_taxonomy_for_object_type( 'post_tag', 'attachment' );

    /*
     * Register custom post types. You can also move this code to a plugin.
     */
    /* Pinegrow generated Custom Post Types Begin */

    /* Pinegrow generated Custom Post Types End */

    /*
     * Register custom taxonomies. You can also move this code to a plugin.
     */
    /* Pinegrow generated Taxonomies Begin */

    /* Pinegrow generated Taxonomies End */

}
endif; // u_seisbarra8_setup

add_action( 'init', 'u_seisbarra8_init' );

// Register custom block category for theme blocks
add_filter( 'block_categories_all', function( $categories ) {
    foreach ( $categories as $category ) {
        if ( isset( $category['slug'] ) && $category['slug'] === 'seisdeagosto' ) {
            return $categories;
        }
    }

    $categories[] = array(
        'slug'  => 'seisdeagosto',
        'title' => __( 'Seis de Agosto', 'u_seisbarra8' ),
        'icon'  => null,
    );

    return $categories;
} );


if ( ! function_exists( 'u_seisbarra8_custom_image_sizes_names' ) ) :

function u_seisbarra8_custom_image_sizes_names( $sizes ) {

    /*
     * Add names of custom image sizes.
     */
    /* Pinegrow generated Image Sizes Names Begin */

    return array_merge( $sizes, array(
        'destaque' => __( 'imagens do Destaque Normal ' ),
        'destatquegrande' => __( 'Imagens do Destaque grande' )
    ) );

    /* Pinegrow generated Image Sizes Names End */
    return $sizes;
}
add_action( 'image_size_names_choose', 'u_seisbarra8_custom_image_sizes_names' );
endif;// u_seisbarra8_custom_image_sizes_names



if ( ! function_exists( 'u_seisbarra8_widgets_init' ) ) :

function u_seisbarra8_widgets_init() {

    /*
     * Register widget areas.
     */
    /* Pinegrow generated Register Sidebars Begin */

    register_sidebar( array(
        'name' => __( 'navbarlateral', 'u_seisbarra8' ),
        'id' => 'navbarlateral',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );
    
    register_sidebar( array(
        'name'          => 'Banner abaixo do post',
        'id'            => 'banner_post',
        'description'   => 'Widget exibido logo abaixo do conteúdo dos posts.',
        'before_widget' => '<div id="%1$s" class="widget banner-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );
    
    register_sidebar( array(
        'name' => __( 'bannervertical', 'u_seisbarra8' ),
        'id' => 'banneraleac-vertical',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'temperatura', 'u_seisbarra8' ),
        'id' => 'temperatura',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Banner Colunistas', 'u_seisbarra8' ),
        'id' => 'banner_colunistas',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'banner ALEAC', 'u_seisbarra8' ),
        'id' => 'bannneraleac',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Grupo Whatsapp', 'u_seisbarra8' ),
        'id' => 'whatsappcorreio68',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Colunistas 68', 'u_seisbarra8' ),
        'id' => 'colunistas',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Na Rota do Boi', 'u_seisbarra8' ),
        'id' => 'narotadoboi',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Right Sidebar', 'u_seisbarra8' ),
        'id' => 'right-sidebar',
        'description' => 'Right Sidebar widget area',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'banner Footer', 'u_seisbarra8' ),
        'id' => 'bannerfooter',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Banners do Cabral', 'u_seisbarra8' ),
        'id' => 'cabralize',
        'description' => 'Area dos banners do Cabral',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Left Sidebar', 'u_seisbarra8' ),
        'id' => 'left-sidebar',
        'description' => 'Left Sidebar widget area',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ) );

    /* Pinegrow generated Register Sidebars End */
}
add_action( 'widgets_init', 'u_seisbarra8_widgets_init' );
endif;// u_seisbarra8_widgets_init



if ( ! function_exists( 'u_seisbarra8_customize_register' ) ) :

function u_seisbarra8_customize_register( $wp_customize ) {
    
    $pgwp_sanitize = function_exists('pgwp_sanitize_placeholder') ? 'pgwp_sanitize_placeholder' : null;

    // ====================
    // PAINEL: Configurações do Layout
    // ====================
    $wp_customize->add_panel( 'u_seisbarra8_layout_panel', array(
        'title'       => __( '📐 Layout e Estrutura', 'u_seisbarra8' ),
        'description' => __( 'Configure a estrutura e layout do site', 'u_seisbarra8' ),
        'priority'    => 25,
    ) );

    // Seção: Sidebars
    $wp_customize->add_section( 'u_seisbarra8_sidebars', array(
        'title'       => __( 'Barras Laterais', 'u_seisbarra8' ),
        'description' => __( 'Ative ou desative as barras laterais do site', 'u_seisbarra8' ),
        'panel'       => 'u_seisbarra8_layout_panel',
        'priority'    => 10,
    ));

    $wp_customize->add_setting( 'show_right_sidebar', array(
        'default'           => false,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control( 'show_right_sidebar', array(
        'label'       => __( 'Exibir Barra Lateral Direita', 'u_seisbarra8' ),
        'description' => __( 'Marque para ativar a barra lateral direita', 'u_seisbarra8' ),
        'type'        => 'checkbox',
        'section'     => 'u_seisbarra8_sidebars',
    ));

    $wp_customize->add_setting( 'show_left_sidebar', array(
        'default'           => false,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control( 'show_left_sidebar', array(
        'label'       => __( 'Exibir Barra Lateral Esquerda', 'u_seisbarra8' ),
        'description' => __( 'Marque para ativar a barra lateral esquerda', 'u_seisbarra8' ),
        'type'        => 'checkbox',
        'section'     => 'u_seisbarra8_sidebars',
    ));

    // ====================
    // PAINEL: Conteúdo do Site
    // ====================
    $wp_customize->add_panel( 'u_seisbarra8_content_panel', array(
        'title'       => __( '📝 Conteúdo', 'u_seisbarra8' ),
        'description' => __( 'Personalize textos e conteúdos do site', 'u_seisbarra8' ),
        'priority'    => 35,
    ) );

    // Seção: Rodapé
    $wp_customize->add_section( 'u_seisbarra8_footer', array(
        'title'       => __( 'Rodapé', 'u_seisbarra8' ),
        'description' => __( 'Personalize o texto do rodapé', 'u_seisbarra8' ),
        'panel'       => 'u_seisbarra8_content_panel',
        'priority'    => 10,
    ));

    if ( ! function_exists( 'u_seisbarra8_get_footer_default_text' ) ) {
        function u_seisbarra8_get_footer_default_text() {
            $site_url  = home_url();
            $site_host = wp_parse_url( $site_url, PHP_URL_HOST );
            $site_display = $site_host ? $site_host : $site_url;
            return sprintf(
                'Orgulhosamente feito com <i class="fa fa-heart"></i> no Acre | <b>%s</b>',
                esc_html( $site_display )
            );
        }
    }

    $wp_customize->add_setting( 'footer_text', array(
        'default'           => u_seisbarra8_get_footer_default_text(),
        'type'              => 'theme_mod',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control( 'footer_text', array(
        'label'       => __( 'Texto do Rodapé', 'u_seisbarra8' ),
        'description' => __( 'HTML permitido: <i>, <b>, <a>, <span>', 'u_seisbarra8' ),
        'type'        => 'textarea',
        'section'     => 'u_seisbarra8_footer',
    ));

    /* Pinegrow generated Customizer Controls End */

}
add_action( 'customize_register', 'u_seisbarra8_customize_register' );
endif;// u_seisbarra8_customize_register


if ( ! function_exists( 'u_seisbarra8_enqueue_scripts' ) ) :
    function u_seisbarra8_enqueue_scripts() {

        /* Pinegrow generated Enqueue Scripts Begin */
          

    // Skip scripts on AMP requests
    $is_amp = u_seisbarra8_is_amp();

    // jQuery is enqueued early by u_seisbarra8_ensure_jquery_first().

    if ( ! $is_amp ) {
        wp_enqueue_script( 'u_seisbarra8-popper', get_template_directory_uri() . '/assets/js/popper.js', array(), null, true );

        wp_enqueue_script( 'u_seisbarra8-menustick', get_template_directory_uri() . '/assets/js/menustick.js', array('jquery'), null, true );

        wp_enqueue_script( 'u_seisbarra8-bootstrap', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', array('jquery', 'u_seisbarra8-popper'), null, true );

        wp_enqueue_script( 'u_seisbarra8-outline', get_template_directory_uri() . '/assets/js/outline.js', null, null, true );

        if ( ! wp_script_is( 'u_seisbarra8-plugins', 'enqueued' ) ) {
            wp_enqueue_script( 'u_seisbarra8-plugins', get_template_directory_uri() . '/components/pg.blocks.wp/js/plugins.js', array('jquery'), null, true);
        }

        if ( ! wp_script_is( 'u_seisbarra8-bskitscripts', 'enqueued' ) ) {
            wp_enqueue_script( 'u_seisbarra8-bskitscripts', get_template_directory_uri() . '/components/pg.blocks.wp/js/bskit-scripts.js', array('jquery'), null, true);
        }

        // Remove external Google Maps API to avoid CDN/external dependency
        wp_deregister_script( 'u_seisbarra8-script' );

        if ( ! wp_script_is( 'u_seisbarra8-slick', 'enqueued' ) ) {
            wp_enqueue_script( 'u_seisbarra8-slick', get_template_directory_uri() . '/slick/slick.min.js', array('jquery'), null, true );
        }
        
        // Carousel init - must load after slick
        wp_enqueue_script( 'u_seisbarra8-carousel_init', get_template_directory_uri() . '/assets/js/carousel_init.js', array('jquery', 'u_seisbarra8-bootstrap', 'u_seisbarra8-slick'), null, true );
        
        if ( apply_filters( 'u_seisbarra8_enable_colunistas_slick', false ) ) {
            wp_enqueue_script( 'u_seisbarra8-colunistas-slick', get_template_directory_uri() . '/assets/js/colunistas-slick.js', array('jquery', 'u_seisbarra8-slick'), null, true );
        }
        // Weather forecast slider init (uses slick)
        wp_enqueue_script( 'u_seisbarra8-weather-forecast', get_template_directory_uri() . '/assets/js/weather-forecast.js', array('jquery', 'u_seisbarra8-slick'), null, true );
        // Post-load i18n for weekday names (pt-BR)
        wp_enqueue_script( 'u_seisbarra8-weather-i18n', get_template_directory_uri() . '/assets/js/weather-i18n.js', array('jquery', 'u_seisbarra8-weather-forecast'), null, true );
        
        // City Selector for Weather Widget
        wp_enqueue_style( 'u_seisbarra8-city-selector', get_template_directory_uri() . '/assets/css/city-selector.css', array(), '1.0.0' );
        wp_enqueue_script( 'u_seisbarra8-city-selector', get_template_directory_uri() . '/assets/js/city-selector.js', array('jquery'), '1.0.0', true );
    }

    /* Pinegrow generated Enqueue Scripts End */

        /* Pinegrow generated Enqueue Styles Begin */

    // Estilos são carregados pelo enqueue_block_assets; apenas adicionar inline CSS aqui
    if ( ! wp_style_is( 'u_seisbarra8-bootstrap', 'enqueued' ) ) {
        wp_enqueue_style( 'u_seisbarra8-bootstrap', get_template_directory_uri() . '/bootstrap/css/bootstrap.min.css', false, null, 'all');
    }

    if ( ! wp_style_is( 'u_seisbarra8-theme', 'enqueued' ) ) {
        wp_enqueue_style( 'u_seisbarra8-theme', get_template_directory_uri() . '/css/theme.css', array('u_seisbarra8-bootstrap'), null, 'all');
    }
    // Inline CSS to align icon colors and spacing with theme variables
    wp_add_inline_style( 'u_seisbarra8-theme', '
    .weather-forecast .fa.icon-color-primary { color: var(--u68-primary-color, #007bff); }
    .weather-forecast .fa.icon-color-accent { color: var(--u68-badge-color, #ff5722); }
    .weather-forecast .forecast-day { padding: 8px; margin: 0; }
    .weather-forecast .metrics { display: flex; align-items: center; gap: 6px; }
    .weather-forecast .metrics .fa { margin-right: 4px; }
    .weather-forecast .day-name, .weather-forecast .day-date { font-weight: 700; font-size: 1.05rem; }
    .weather-forecast-slider .slick-slide { outline: none; }
    .weather-forecast-slider .slick-dots li button:before { color: var(--u68-primary-color, #007bff); }
    .weather-forecast .fa.icon-color-primary { color: var(--u68-primary-color, #007bff); }
    .weather-forecast .fa.icon-color-accent { color: var(--u68-badge-color, #ff5722); }
    .weather-forecast .daily-card { padding: 8px; margin: 0; }
    .weather-forecast .metrics { display: flex; align-items: center; gap: 6px; }
    .weather-forecast .metrics .fa { margin-right: 4px; }
    .weather-forecast .day-name { font-weight: 600; }
    .weather-forecast-slider .slick-slide { outline: none; }
    .weather-forecast-slider .slick-dots li button:before { color: var(--u68-primary-color, #007bff); }
    ' );
    // Inline CSS for current-day header alignment and emphasis
    wp_add_inline_style( 'u_seisbarra8-theme', '
    .weather-block .current-wrap { display:flex; flex-direction:column; align-items:center; text-align:center; }
    .weather-block .current-info { width:100%; }
    .weather-block .current-info .city { font-weight:700; color: var(--u68-primary-color, #007bff); font-size:0.95rem; display:block; }
    .weather-block .current-info .condition { font-weight:600; color: var(--u68-badge-color, #ff5722); font-size:0.9rem; display:block; }
    .weather-block .current-meta-inline { display:flex; flex-direction:column; gap:4px; align-items:center; }
    .weather-block .current-meta-inline > div { display:flex; align-items:center; justify-content:center; }
    .weather-block .current-meta-inline i { font-size:0.9rem; margin-right:6px; }
    .weather-block .meta-bottom { gap:6px; margin-top:8px; display:flex; flex-direction:column; align-items:center; }
    .weather-block .meta-bottom > div { display:flex; align-items:center; justify-content:center; }
    .weather-block .meta-bottom > div i { margin-right:6px; }
    .weather-block .wind-info, .weather-block .rain-info { font-size:0.95rem; color:#2c3e50; font-weight:500; }
    .weather-block .current-temp { font-weight:800; font-size:2rem; line-height:1; color: var(--u68-primary-color, #007bff); }
    .weather-block .current-temp .temp-unit { font-size:0.9rem; opacity:0.85; margin-left:2px; }
    .weather-block .weather-fa-icon { font-size:28px; }
        .weather-block .fa.icon-color-primary { color: var(--u68-primary-color, #007bff); }
        .weather-block .fa.icon-color-accent { color: var(--u68-badge-color, #ff5722); }
        .weather-block .current-info { min-width:0; }
    ' );

    if ( ! wp_style_is( 'u_seisbarra8-woocommerce', 'enqueued' ) ) {
        wp_enqueue_style( 'u_seisbarra8-woocommerce', get_template_directory_uri() . '/css/woocommerce.css', false, null, 'all');
    }

    if ( ! wp_style_is( 'u_seisbarra8-blocks', 'enqueued' ) ) {
        wp_enqueue_style( 'u_seisbarra8-blocks', get_template_directory_uri() . '/components/pg.blocks.wp/css/blocks.css', false, null, 'all');
    }

    if ( ! wp_style_is( 'u_seisbarra8-plugins', 'enqueued' ) ) {
        wp_enqueue_style( 'u_seisbarra8-plugins', get_template_directory_uri() . '/components/pg.blocks.wp/css/plugins.css', false, null, 'all');
    }

    if ( ! wp_style_is( 'u_seisbarra8-stylelibrary', 'enqueued' ) ) {
        wp_enqueue_style( 'u_seisbarra8-stylelibrary', get_template_directory_uri() . '/components/pg.blocks.wp/css/style-library-1.css', false, null, 'all');
    }

    // Remove Google Fonts to avoid CDN/external dependency (use local fonts instead)
    wp_deregister_style( 'u_seisbarra8-style' );
    wp_deregister_style( 'u_seisbarra8-style-1' );

    // Load Font Awesome 4.7 + override @font-face with modern formats only
    if ( ! wp_style_is( 'u_seisbarra8-fontawesome', 'enqueued' ) ) {
        $fa_vendor_css = get_template_directory() . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css';
        if ( file_exists( $fa_vendor_css ) ) {
            wp_enqueue_style( 'u_seisbarra8-fontawesome', get_template_directory_uri() . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css', false, '4.7.0', 'all');
            // Override @font-face with modern formats (removes .eot/.svg preload warnings)
            wp_enqueue_style( 'u_seisbarra8-fontawesome-modern', get_template_directory_uri() . '/css/local-fa-fallback.css', array('u_seisbarra8-fontawesome'), '1.1', 'all');
        }
    }

    if ( ! wp_style_is( 'u_seisbarra8-slick', 'enqueued' ) ) {
        wp_enqueue_style( 'u_seisbarra8-slick', get_template_directory_uri() . '/slick/slick.css', false, null, 'all');
    }

    if ( ! wp_style_is( 'u_seisbarra8-slicktheme', 'enqueued' ) ) {
        wp_enqueue_style( 'u_seisbarra8-slicktheme', get_template_directory_uri() . '/slick/slick-theme.css', false, null, 'all');
    }

    // Enqueue blocks layout CSS
    wp_enqueue_style( 'seideagosto-blocks-layout', get_template_directory_uri() . '/css/blocks-layout.css', false, null, 'all');

    // Check if local fonts exist, otherwise fallback to Google Fonts CDN
    $stylesheet_dir = get_stylesheet_directory();
    $stylesheet_uri = get_stylesheet_directory_uri();
    $font_dir = $stylesheet_dir . '/assets/fonts';

    $open_sans_file = $font_dir . '/open-sans/OpenSans-Variable.ttf';
    $lato_file = $font_dir . '/lato/Lato-Regular.ttf';

    $enable_google_fonts = (bool) get_theme_mod( 'u_correio68_enable_google_fonts', false );
    $google_families = get_theme_mod( 'u_correio68_google_fonts_family', 'Open+Sans:wght@300;400;600;700|Lato:wght@300;400;700' );
    if ( function_exists( 'u_correio68_sanitize_google_fonts_family' ) ) {
        $google_families = u_correio68_sanitize_google_fonts_family( $google_families );
    }
    $preset_key = get_theme_mod( 'u_correio68_google_fonts_preset', 'custom' );
    if ( function_exists( 'u_correio68_get_google_font_presets' ) ) {
        $presets = u_correio68_get_google_font_presets();
        if ( isset( $presets[ $preset_key ] ) && $preset_key !== 'custom' ) {
            $google_families = $presets[ $preset_key ]['families'];
            $enable_google_fonts = true;
        }
    }
    if ( $enable_google_fonts && ! empty( $google_families ) ) {
        $google_families_query = str_replace( '+', ' ', $google_families );
        $google_url = add_query_arg(
            array(
                'family'  => $google_families_query,
                'display' => 'swap',
            ),
            'https://fonts.googleapis.com/css2'
        );
        wp_enqueue_style( 'u_seisbarra8-google-fonts', esc_url( $google_url ), false, null, 'all' );
    } elseif ( file_exists( $open_sans_file ) && file_exists( $lato_file ) ) {
        // Use local self-hosted fonts with absolute URLs
        $font_uri = $stylesheet_uri . '/assets/fonts';

        $font_files = array(
            array(
                'family' => 'Open Sans',
                'file'   => $font_dir . '/open-sans/OpenSans-Variable.ttf',
                'uri'    => '/open-sans/OpenSans-Variable.ttf',
                'weight' => '300 700',
                'style'  => 'normal',
            ),
            array(
                'family' => 'Open Sans',
                'file'   => $font_dir . '/open-sans/OpenSans-Italic-Variable.ttf',
                'uri'    => '/open-sans/OpenSans-Italic-Variable.ttf',
                'weight' => '300 700',
                'style'  => 'italic',
            ),
            array(
                'family' => 'Lato',
                'file'   => $font_dir . '/lato/Lato-Light.ttf',
                'uri'    => '/lato/Lato-Light.ttf',
                'weight' => '300',
                'style'  => 'normal',
            ),
            array(
                'family' => 'Lato',
                'file'   => $font_dir . '/lato/Lato-LightItalic.ttf',
                'uri'    => '/lato/Lato-LightItalic.ttf',
                'weight' => '300',
                'style'  => 'italic',
            ),
            array(
                'family' => 'Lato',
                'file'   => $font_dir . '/lato/Lato-Regular.ttf',
                'uri'    => '/lato/Lato-Regular.ttf',
                'weight' => '400',
                'style'  => 'normal',
            ),
            array(
                'family' => 'Lato',
                'file'   => $font_dir . '/lato/Lato-Italic.ttf',
                'uri'    => '/lato/Lato-Italic.ttf',
                'weight' => '400',
                'style'  => 'italic',
            ),
            array(
                'family' => 'Lato',
                'file'   => $font_dir . '/lato/Lato-Bold.ttf',
                'uri'    => '/lato/Lato-Bold.ttf',
                'weight' => '700',
                'style'  => 'normal',
            ),
            array(
                'family' => 'Lato',
                'file'   => $font_dir . '/lato/Lato-BoldItalic.ttf',
                'uri'    => '/lato/Lato-BoldItalic.ttf',
                'weight' => '700',
                'style'  => 'italic',
            ),
        );

        $u68_font_faces = array();
        $font_mtime = 0;
        foreach ( $font_files as $font_file ) {
            if ( ! file_exists( $font_file['file'] ) ) {
                continue;
            }
            $file_mtime = filemtime( $font_file['file'] );
            $font_mtime = max( $font_mtime, $file_mtime );
            $u68_font_faces[] = "@font-face { font-family: '" . esc_attr( $font_file['family'] ) . "'; src: url('" . esc_url( $font_uri . $font_file['uri'] . '?v=' . $file_mtime ) . "') format('truetype'); font-weight: " . esc_attr( $font_file['weight'] ) . "; font-style: " . esc_attr( $font_file['style'] ) . "; font-display: swap; }";
        }

        if ( ! empty( $u68_font_faces ) ) {
            wp_register_style( 'u_seisbarra8-fonts', false, array(), $font_mtime ? $font_mtime : null );
            wp_enqueue_style( 'u_seisbarra8-fonts' );
            wp_add_inline_style( 'u_seisbarra8-fonts', implode( "\n", $u68_font_faces ) );
        } else {
            // Fallback to Google Fonts CDN when local fonts are missing
            wp_enqueue_style( 'u_seisbarra8-google-fonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap', false, null, 'all');
        }
    } else {
        // Fallback to Google Fonts CDN
        wp_enqueue_style( 'u_seisbarra8-google-fonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap', false, null, 'all');
    }

    wp_deregister_style( 'u_seisbarra8-style-2' );
    wp_enqueue_style( 'u_seisbarra8-style-2', get_bloginfo('stylesheet_url'), false, null, 'all');

    /* Pinegrow generated Enqueue Styles End */

    }

    // Garante o carregamento do main.js customizado para o menu categorias
    add_action( 'wp_enqueue_scripts', function() {
        wp_enqueue_script(
            'u_seisbarra8-main',
            get_template_directory_uri() . '/assets/js/main.js',
            array(),
            filemtime( get_template_directory() . '/assets/js/main.js' ),
            true
        );
    }, 99 );
    add_action( 'wp_enqueue_scripts', 'u_seisbarra8_enqueue_scripts' );
endif;

/**
 * Ensure Font Awesome is always enqueued (frontend + admin/editor) with modern font formats.
 */
function u_seisbarra8_ensure_fontawesome() {
    $handle = 'u_seisbarra8-fontawesome';
    $fa_vendor_css = get_template_directory() . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css';
    if ( file_exists( $fa_vendor_css ) ) {
        $src = get_template_directory_uri() . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css';
        $ver = '4.7.0';
        
        if ( ! wp_style_is( $handle, 'registered' ) ) {
            wp_register_style( $handle, $src, array(), $ver );
        }
        wp_enqueue_style( $handle );
        
        // Override @font-face with modern formats (removes .eot/.svg preload warnings)
        wp_enqueue_style( 'u_seisbarra8-fontawesome-modern', get_template_directory_uri() . '/css/local-fa-fallback.css', array( $handle ), '1.1' );
    }
}
add_action( 'wp_enqueue_scripts', 'u_seisbarra8_ensure_fontawesome', 1 );
add_action( 'admin_enqueue_scripts', 'u_seisbarra8_ensure_fontawesome', 1 );

// Load key styles AND scripts (Bootstrap, theme, icons, layout) in both frontend and Site Editor to keep header/footer styled and functional.
function u_seisbarra8_enqueue_block_assets() {
    $theme_uri = get_template_directory_uri();

    // CSS: Bootstrap + main + theme + icons + blocks + slick
    wp_enqueue_style( 'u_seisbarra8-bootstrap', $theme_uri . '/bootstrap/css/bootstrap.min.css', array(), null );
    wp_enqueue_style( 'u_seisbarra8-main', $theme_uri . '/css/main.css', array( 'u_seisbarra8-bootstrap' ), null );
    wp_enqueue_style( 'u_seisbarra8-theme', $theme_uri . '/css/theme.css', array( 'u_seisbarra8-bootstrap', 'u_seisbarra8-main' ), null );
    wp_enqueue_style( 'u_seisbarra8-fontawesome', $theme_uri . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css', array(), '4.7.0' );
    // Override @font-face with modern formats (removes .eot/.svg preload warnings)
    wp_enqueue_style( 'u_seisbarra8-fontawesome-modern', $theme_uri . '/css/local-fa-fallback.css', array( 'u_seisbarra8-fontawesome' ), '1.1' );
    wp_enqueue_style( 'u_seisbarra8-blocks', $theme_uri . '/components/pg.blocks.wp/css/blocks.css', array( 'u_seisbarra8-theme' ), null );
    wp_enqueue_style( 'u_seisbarra8-plugins', $theme_uri . '/components/pg.blocks.wp/css/plugins.css', array( 'u_seisbarra8-theme' ), null );
    wp_enqueue_style( 'u_seisbarra8-stylelibrary', $theme_uri . '/components/pg.blocks.wp/css/style-library-1.css', array( 'u_seisbarra8-theme' ), null );
    wp_enqueue_style( 'u_seisbarra8-slick', $theme_uri . '/slick/slick.css', array( 'u_seisbarra8-theme' ), null );
    wp_enqueue_style( 'u_seisbarra8-slicktheme', $theme_uri . '/slick/slick-theme.css', array( 'u_seisbarra8-slick' ), null );
    wp_enqueue_style( 'seideagosto-blocks-layout', $theme_uri . '/css/blocks-layout.css', array( 'u_seisbarra8-theme' ), null );

    // JS: Bootstrap and dependencies needed for navbar toggler, dropdowns, etc.
    // Skip on AMP requests
    if ( function_exists( 'u_seisbarra8_is_amp' ) && u_seisbarra8_is_amp() ) {
        return;
    }

    // Ensure jQuery is available
    wp_enqueue_script( 'jquery' );

    // Popper.js (Bootstrap 5 dependency)
    if ( ! wp_script_is( 'u_seisbarra8-popper', 'registered' ) ) {
        wp_register_script( 'u_seisbarra8-popper', $theme_uri . '/assets/js/popper.js', array(), null, true );
    }
    wp_enqueue_script( 'u_seisbarra8-popper' );

    // Bootstrap JS
    if ( ! wp_script_is( 'u_seisbarra8-bootstrap', 'registered' ) ) {
        wp_register_script( 'u_seisbarra8-bootstrap', $theme_uri . '/bootstrap/js/bootstrap.min.js', array( 'jquery', 'u_seisbarra8-popper' ), null, true );
    }
    wp_enqueue_script( 'u_seisbarra8-bootstrap' );

    // Slick carousel JS
    if ( ! wp_script_is( 'u_seisbarra8-slick', 'registered' ) ) {
        wp_register_script( 'u_seisbarra8-slick', $theme_uri . '/slick/slick.min.js', array( 'jquery' ), null, true );
    }
    wp_enqueue_script( 'u_seisbarra8-slick' );

    // Header JS (scroll behavior, mobile search toggle, categories)
    wp_enqueue_script( 'u_seisbarra8-header', $theme_uri . '/assets/js/header.js', array(), '1.0.0', true );
}
add_action( 'enqueue_block_assets', 'u_seisbarra8_enqueue_block_assets' );

// Also hook to wp_enqueue_scripts at priority 1 to ensure block theme loads assets on frontend
add_action( 'wp_enqueue_scripts', 'u_seisbarra8_enqueue_block_assets', 1 );

// Fallback guarantee: enqueue theme.css once for both frontend and editor if it was dropped by another hook.
function u68_force_theme_css() {
    $handle   = 'u_seisbarra8-theme';
    $css_path = get_theme_file_path( 'css/theme.css' );
    $css_uri  = get_theme_file_uri( 'css/theme.css' );

    if ( ! wp_style_is( $handle, 'enqueued' ) && file_exists( $css_path ) ) {
        $ver = filemtime( $css_path );
        wp_enqueue_style( $handle, $css_uri, array(), $ver );
    }
}
add_action( 'enqueue_block_assets', 'u68_force_theme_css', 1 );
add_action( 'wp_enqueue_scripts', 'u68_force_theme_css', 1 );

// Admin-only asset health checks to quickly debug missing header/footer or CSS loads.
function u68_get_asset_statuses() {
    global $wp_styles, $wp_scripts;

    $style_handles = array(
        'u_seisbarra8-bootstrap',
        'u_seisbarra8-main',
        'u_seisbarra8-theme',
        'u_seisbarra8-blocks',
        'u_seisbarra8-plugins',
        'u_seisbarra8-stylelibrary',
        'seideagosto-blocks-layout',
    );

    $script_handles = array(
        'u_seisbarra8-bootstrap',
        'u_seisbarra8-popper',
        'u_seisbarra8-slick',
        'u_seisbarra8-plugins',
        'u_seisbarra8-bskitscripts',
        'u_seisbarra8-carousel_init',
        'u_seisbarra8-outline',
        'u_seisbarra8-weather-forecast',
        'u_seisbarra8-weather-i18n',
    );

    $styles = array();
    foreach ( $style_handles as $handle ) {
        $styles[ $handle ] = array(
            'registered' => wp_style_is( $handle, 'registered' ),
            'enqueued'   => wp_style_is( $handle, 'enqueued' ),
            'src'        => isset( $wp_styles->registered[ $handle ] ) ? $wp_styles->registered[ $handle ]->src : '',
        );
    }

    $scripts = array();
    foreach ( $script_handles as $handle ) {
        $scripts[ $handle ] = array(
            'registered' => wp_script_is( $handle, 'registered' ),
            'enqueued'   => wp_script_is( $handle, 'enqueued' ),
            'src'        => isset( $wp_scripts->registered[ $handle ] ) ? $wp_scripts->registered[ $handle ]->src : '',
        );
    }

    return array(
        'styles'  => $styles,
        'scripts' => $scripts,
    );
}

function u68_adminbar_asset_status( $wp_admin_bar ) {
    if ( ! is_admin_bar_showing() || ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $status = u68_get_asset_statuses();
    $missing = array();

    foreach ( $status['styles'] as $handle => $data ) {
        if ( ! $data['enqueued'] ) {
            $missing[] = $handle;
        }
    }
    foreach ( $status['scripts'] as $handle => $data ) {
        if ( ! $data['enqueued'] ) {
            $missing[] = $handle;
        }
    }

    $title = empty( $missing ) ? 'Tema OK: assets carregados' : 'Tema alerta: faltam ' . count( $missing );
    $meta  = empty( $missing ) ? 'Todos os CSS/JS principais estão enfileirados.' : 'Faltando: ' . implode( ', ', $missing );

    $wp_admin_bar->add_node( array(
        'id'    => 'u68-asset-health',
        'title' => esc_html( $title ),
        'meta'  => array( 'title' => esc_html( $meta ) ),
        'href'  => '#',
    ) );
}
add_action( 'admin_bar_menu', 'u68_adminbar_asset_status', 120 );

// Console report to quickly inspect header/footer and CSS presence (admins only, front and editor).
function u68_asset_console_probe() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $status = u68_get_asset_statuses();
    $data   = array(
        'styles'        => $status['styles'],
        'scripts'       => $status['scripts'],
        'selectors'     => array(
            'header' => 'header.site-header, .site-header',
            'footer' => 'footer.site-footer, .site-footer',
        ),
        'checks'        => array(
            'themeCssSubstring' => '/css/theme.css',
        ),
    );

    $json = wp_json_encode( $data );

    $probe_js = <<<JS
(() => {
    const data = {$json};
    const findNode = (sel) => document.querySelector(sel);

    // Check for theme.css (may be combined/minified by cache plugins)
    const themeCss = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).find(link => link.href && link.href.includes(data.checks.themeCssSubstring));
    const boostCache = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).find(link => link.href && link.href.includes('boost-cache'));
    const headerEl = findNode(data.selectors.header);
    const footerEl = findNode(data.selectors.footer);

    const summary = {
        headerFound: !!headerEl,
        footerFound: !!footerEl,
        themeCssFound: !!themeCss || !!boostCache,
        cssOptimized: !!boostCache,
        styles: data.styles,
        scripts: data.scripts,
    };

    console.info('[u68] Asset/DOM check', summary);

    if (!headerEl) console.warn('[u68] Header não encontrado no DOM. Verifique template-part header.');
    if (!footerEl) console.warn('[u68] Footer não encontrado no DOM. Verifique template-part footer.');
    // Only warn about missing theme.css if no cache optimization is detected
    if (!themeCss && !boostCache) console.warn('[u68] theme.css não encontrado nos <link rel="stylesheet">.');
})();
JS;

    wp_print_inline_script_tag( $probe_js );
}
add_action( 'wp_footer', 'u68_asset_console_probe', 20 );
add_action( 'admin_footer', 'u68_asset_console_probe', 20 );

// Safety net: guarantee core header/footer assets are enqueued on the frontend even in block templates.
function u_seisbarra8_force_front_assets() {
    // Styles
    foreach ( array(
        'u_seisbarra8-bootstrap' => get_template_directory_uri() . '/bootstrap/css/bootstrap.min.css',
        'u_seisbarra8-main' => get_template_directory_uri() . '/css/main.css',
        'u_seisbarra8-theme' => get_template_directory_uri() . '/css/theme.css',
        'u_seisbarra8-fontawesome' => get_template_directory_uri() . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css',
        'u_seisbarra8-blocks' => get_template_directory_uri() . '/components/pg.blocks.wp/css/blocks.css',
        'u_seisbarra8-plugins' => get_template_directory_uri() . '/components/pg.blocks.wp/css/plugins.css',
        'u_seisbarra8-stylelibrary' => get_template_directory_uri() . '/components/pg.blocks.wp/css/style-library-1.css',
        'u_seisbarra8-slick' => get_template_directory_uri() . '/slick/slick.css',
        'u_seisbarra8-slicktheme' => get_template_directory_uri() . '/slick/slick-theme.css',
        'seideagosto-blocks-layout' => get_template_directory_uri() . '/css/blocks-layout.css',
    ) as $handle => $src ) {
        if ( ! wp_style_is( $handle, 'enqueued' ) ) {
            wp_enqueue_style( $handle, $src, array(), null );
        }
    }

    // Scripts (ensure navbar/search behavior works)
    $scripts = array(
        'u_seisbarra8-popper' => array( 'src' => get_template_directory_uri() . '/assets/js/popper.js', 'deps' => array(), 'in_footer' => true ),
        'u_seisbarra8-bootstrap' => array( 'src' => get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', 'deps' => array( 'jquery', 'u_seisbarra8-popper' ), 'in_footer' => true ),
        'u_seisbarra8-menustick' => array( 'src' => get_template_directory_uri() . '/assets/js/menustick.js', 'deps' => array( 'jquery' ), 'in_footer' => true ),
        'u_seisbarra8-slick' => array( 'src' => get_template_directory_uri() . '/slick/slick.min.js', 'deps' => array( 'jquery' ), 'in_footer' => true ),
        'u_seisbarra8-carousel_init' => array( 'src' => get_template_directory_uri() . '/assets/js/carousel_init.js', 'deps' => array( 'jquery', 'u_seisbarra8-bootstrap' ), 'in_footer' => true ),
        'u_seisbarra8-outline' => array( 'src' => get_template_directory_uri() . '/assets/js/outline.js', 'deps' => array(), 'in_footer' => true ),
        'u_seisbarra8-plugins' => array( 'src' => get_template_directory_uri() . '/components/pg.blocks.wp/js/plugins.js', 'deps' => array( 'jquery' ), 'in_footer' => true ),
        'u_seisbarra8-bskitscripts' => array( 'src' => get_template_directory_uri() . '/components/pg.blocks.wp/js/bskit-scripts.js', 'deps' => array( 'jquery' ), 'in_footer' => true ),
        'u_seisbarra8-weather-forecast' => array( 'src' => get_template_directory_uri() . '/assets/js/weather-forecast.js', 'deps' => array( 'jquery', 'u_seisbarra8-slick' ), 'in_footer' => true ),
        'u_seisbarra8-weather-i18n' => array( 'src' => get_template_directory_uri() . '/assets/js/weather-i18n.js', 'deps' => array( 'jquery', 'u_seisbarra8-weather-forecast' ), 'in_footer' => true ),
    );
    foreach ( $scripts as $handle => $data ) {
        if ( ! wp_script_is( $handle, 'enqueued' ) ) {
            wp_enqueue_script( $handle, $data['src'], $data['deps'], null, $data['in_footer'] );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'u_seisbarra8_force_front_assets', 5 );

function pgwp_sanitize_placeholder($input) { return $input; }
/*
 * Resource files included by Pinegrow.*/
 
/* Pinegrow generated Include Resources Begin */
require_once "inc/custom.php";
require_once "inc/wp_pg_helpers.php";
require_once "inc/bootstrap/wp_bootstrap4_navwalker.php";
require_once "inc/blocks.php";
require_once "inc/widgets.php";
require_once "inc/customizer.php";

    /* Pinegrow generated Include Resources End */
    
    // Removed invalid filter hook name.
    
    // Fallback shims for Advanced Custom Fields only if plugin is not active
    add_action( 'plugins_loaded', function() {
        if ( function_exists( 'get_field' ) ) {
            return; // ACF available, no shim needed
        }
        function get_field( $selector, $post_id = false, $format_value = true ) {
            $value = '';
            if ( $format_value ) {
                // Permite que filtros como acf/format_value* apliquem fallback de tema
                $name = is_string( $selector ) ? $selector : '';
                $field = array( 'name' => $name );
                $value = apply_filters( "acf/format_value/name={$name}", $value, $post_id, $field );
            }
            return $value;
        }
        function the_field( $selector, $post_id = false ) {
            echo get_field( $selector, $post_id );
        }
    } );

    /**
     * Helper: obtém dados da badge (texto, cor, ícone) com fallbacks do tema.
     */
    function u_correio68_get_badge_data( $post_id = null ) {
        $post_id = $post_id ? intval( $post_id ) : get_the_ID();

        $text      = get_field( 'chamada', $post_id );
        $color     = get_field( 'cor', $post_id );
        $icon_raw  = get_field( 'icones', $post_id );
        $text_color = get_theme_mod( 'u_correio68_badge_text_color', '#ffffff' );

        // Fallbacks adicionais
        if ( empty( $color ) ) {
            $color = get_theme_mod( 'u_correio68_primary_color', '#0a4579' );
        }
        if ( empty( $icon_raw ) ) {
            $icon_raw = (string) get_theme_mod( 'u_correio68_badge_icon_class', '' );
            $icon_raw = trim( $icon_raw );
        }
        if ( empty( $icon_raw ) ) {
            $icon_raw = '';
        }
        if ( function_exists( 'u_correio68_normalize_fa_icon_class' ) ) {
            $icon_raw = u_correio68_normalize_fa_icon_class( $icon_raw );
        }

        return array(
            'text'  => wp_strip_all_tags( (string) $text ),
            'color' => sanitize_hex_color( $color ) ?: $color,
            'text_color' => sanitize_hex_color( $text_color ) ?: $text_color,
            'icon'  => sanitize_text_field( $icon_raw ),
        );
    }

    /**
     * Helper: renderiza badge com texto, cor e ícone.
     */
    function u_correio68_the_badge( $args = array() ) {
        $defaults = array(
            'post_id'   => null,
            'class'     => 'badge badge-light text-white bg-orange badge-pill',
            'show_icon' => true,
        );
        $args = wp_parse_args( $args, $defaults );

        $badge = u_correio68_get_badge_data( $args['post_id'] );
        if ( empty( $badge['text'] ) && empty( $badge['icon'] ) ) {
            return;
        }

        $style_parts = array();
        if ( $badge['color'] ) {
            $style_parts[] = 'background-color:' . esc_attr( $badge['color'] ) . ' !important;';
        }
        if ( $badge['text_color'] ) {
            $style_parts[] = 'color:' . esc_attr( $badge['text_color'] ) . ' !important;';
        }
        $color_style = ! empty( $style_parts ) ? 'style="' . implode( ' ', $style_parts ) . '"' : '';

        // Decide se o ícone é Font Awesome ou Ionicon
        $icon_html = '';
        if ( $args['show_icon'] && ! empty( $badge['icon'] ) ) {
            $icon_html = '<i class="' . esc_attr( $badge['icon'] ) . '"></i> ';
        }

        echo '<span class="' . esc_attr( $args['class'] ) . '" ' . $color_style . '>' . $icon_html . '<span>' . esc_html( $badge['text'] ) . '</span></span>';
    }

    /**
     * Render a selected template page using its page template layout (without header/footer).
     */
    if ( ! function_exists( 'u_correio68_render_template_page' ) ) {
        function u_correio68_render_template_page( $page_id ) {
            $page_id = absint( $page_id );
            if ( ! $page_id ) {
                return false;
            }

            $page = get_post( $page_id );
            if ( ! $page || $page->post_status !== 'publish' ) {
                return false;
            }

            $template_slug = get_page_template_slug( $page_id );
            $old_post = $GLOBALS['post'] ?? null;
            $GLOBALS['post'] = $page;
            setup_postdata( $page );

            $content = apply_filters( 'the_content', $page->post_content );
            $title   = get_the_title( $page_id );

            ob_start();
            if ( $template_slug === 'page-centered.php' ) {
                echo '<div class="container" id="content" tabindex="-1"><div class="row"><div class="col-12"><main id="main" class="site-main py-4">';
                if ( ! empty( $title ) ) {
                    echo '<h1>' . esc_html( $title ) . '</h1>';
                }
                echo $content;
                echo '</main></div></div></div>';
            } else {
                // Default page layout (page.php)
                echo '<div id="page-wrapper" class="wrapper">';
                echo '<div class="container" id="content" tabindex="-1">';
                echo '<div class="row">';
                if ( get_theme_mod( 'show_left_sidebar' ) ) {
                    echo '<div class="col-md-4 widget-area" role="complementary" id="left-sidebar">';
                    if ( is_active_sidebar( 'left-sidebar' ) ) {
                        dynamic_sidebar( 'left-sidebar' );
                    }
                    echo '</div>';
                }
                $content_col = get_theme_mod( 'show_right_sidebar' ) || get_theme_mod( 'show_left_sidebar' ) ? 'col-md-8' : 'col-12';
                echo '<div class="content-area ' . esc_attr( $content_col ) . '" id="primary">';
                echo '<main class="site-main" id="main">';
                if ( ! empty( $title ) ) {
                    echo '<h1>' . esc_html( $title ) . '</h1>';
                }
                echo $content;
                echo '</main></div>';
                if ( get_theme_mod( 'show_right_sidebar' ) ) {
                    echo '<div class="col-md-4 widget-area" role="complementary" id="right-sidebar">';
                    if ( is_active_sidebar( 'right-sidebar' ) ) {
                        dynamic_sidebar( 'right-sidebar' );
                    }
                    echo '</div>';
                }
                echo '</div></div></div>';
            }
            $output = ob_get_clean();

            wp_reset_postdata();
            if ( $old_post ) {
                $GLOBALS['post'] = $old_post;
            }

            return $output;
        }
    }

    // Register custom Block Pattern category for this theme
    add_action( 'init', function() {
        if ( function_exists( 'register_block_pattern_category' ) ) {
            register_block_pattern_category( 'seisdeagosto', array(
                'label' => __( 'Seis de Agosto', 'u_seisbarra8' ),
            ) );
        }
    } );

    // Create synced patterns (Reusable Blocks) from existing /patterns files
    add_action( 'init', function() {
        // Ensure the reusable blocks post type exists
        if ( ! post_type_exists( 'wp_block' ) ) return;

        $defs = array(
            array(
                'file'  => 'patterns/destaques-home.php',
                'title' => __( 'Destaques da Capa (Sincronizado)', 'u_seisbarra8' ),
                'slug'  => 'seisdeagosto-destaques-home'
            ),
            array(
                'file'  => 'patterns/categoria-destaque.php',
                'title' => __( 'Categoria em Destaque (Sincronizado)', 'u_seisbarra8' ),
                'slug'  => 'seisdeagosto-categoria-destaque'
            ),
            array(
                'file'  => 'patterns/destaque-misto.php',
                'title' => __( 'Destaque Misto (Sincronizado)', 'u_seisbarra8' ),
                'slug'  => 'seisdeagosto-destaque-misto'
            ),
            array(
                'file'  => 'patterns/mais-lidas.php',
                'title' => __( 'Top Mais Lidas (Sincronizado)', 'u_seisbarra8' ),
                'slug'  => 'seisdeagosto-mais-lidas'
            ),
            array(
                'file'  => 'patterns/noticias-grid-bloco.php',
                'title' => __( 'Notícias (Bloco do Tema) (Sincronizado)', 'u_seisbarra8' ),
                'slug'  => 'seisdeagosto-noticias-grid-bloco'
            ),
            array(
                'file'  => 'patterns/colunistas-destaque.php',
                'title' => __( 'Colunistas em Destaque (Sincronizado)', 'u_seisbarra8' ),
                'slug'  => 'seisdeagosto-colunistas-destaque'
            ),
            array(
                'file'  => 'patterns/previsao-tempo.php',
                'title' => __( 'Previsão do Tempo (Sincronizado)', 'u_seisbarra8' ),
                'slug'  => 'seisdeagosto-previsao-tempo'
            ),
        );

        foreach ( $defs as $def ) {
            // Skip if already exists
            $existing = get_page_by_path( $def['slug'], OBJECT, 'wp_block' );
            if ( $existing ) continue;

            $path = get_theme_file_path( $def['file'] );
            if ( ! $path || ! file_exists( $path ) ) continue;

            $raw = file_get_contents( $path );
            if ( false === $raw ) continue;

            // Strip PHP header (pattern metadata) to keep only block markup
            $content = preg_replace( '/^<\?php.*?\?>\s*/s', '', $raw );
            if ( empty( $content ) ) continue;

            wp_insert_post( array(
                'post_type'    => 'wp_block',
                'post_status'  => 'publish',
                'post_title'   => $def['title'],
                'post_name'    => $def['slug'],
                'post_content' => $content,
            ) );
        }
    }, 20 );

    
    // Utility: Flush weather cache (transients) via URL flag for admins
    function u_seisbarra8_flush_weather_cache() {
        global $wpdb;
        $prefix_like = $wpdb->esc_like( 'u68_weather_v2_' ) . '%';
        // Delete cached values
        $transients = $wpdb->get_col( $wpdb->prepare(
            "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
            '_transient_' . $prefix_like
        ) );
        if ( ! empty( $transients ) ) {
            foreach ( $transients as $option_name ) {
                $t_key = str_replace( '_transient_', '', $option_name );
                delete_transient( $t_key );
            }
        }
        // Remove timeouts for completeness
        $timeouts = $wpdb->get_col( $wpdb->prepare(
            "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
            '_transient_timeout_' . $prefix_like
        ) );
        if ( ! empty( $timeouts ) ) {
            foreach ( $timeouts as $option_name ) {
                delete_option( $option_name );
            }
        }
    }
    add_action( 'init', function() {
        if ( is_user_logged_in() && current_user_can( 'manage_options' ) && isset( $_GET['flush_weather'] ) ) {
            u_seisbarra8_flush_weather_cache();
        }
    } );
    
function u68_get_currencyfreaks_api_key() {
    $opt = get_option( 'u68_currencyfreaks_api_key', '' );
    if ( is_string( $opt ) ) {
        $opt = trim( $opt );
    }
    if ( ! empty( $opt ) ) {
        return $opt;
    }
    $key = '';
    if ( defined( 'CURRENCYFREAKS_API_KEY' ) ) {
        $key = CURRENCYFREAKS_API_KEY;
    } elseif ( getenv( 'CURRENCYFREAKS_API_KEY' ) ) {
        $key = getenv( 'CURRENCYFREAKS_API_KEY' );
    }
    $key = is_string( $key ) ? trim( $key ) : '';
    return $key;
}

function u68_currencyfreaks_admin_notice() {
    if ( ! is_admin() ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;
    $key = u68_get_currencyfreaks_api_key();
    if ( $key ) return;
    $settings_url = esc_url( admin_url( 'options-general.php?page=u68-currencyfreaks' ) );
    echo '<div class="notice notice-warning"><p><strong>Bloco Câmbio:</strong> faltando API key do CurrencyFreaks. Configure em <a href="' . $settings_url . '">Configurações → CurrencyFreaks</a>, ou defina <code>CURRENCYFREAKS_API_KEY</code> no wp-config.php/variável de ambiente.</p></div>';
}
add_action( 'admin_notices', 'u68_currencyfreaks_admin_notice' );

// Settings page to store CurrencyFreaks API key
function u68_currencyfreaks_register_settings() {
    register_setting( 'u68_currencyfreaks', 'u68_currencyfreaks_api_key', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    ) );

    add_settings_section( 'u68_currencyfreaks_main', 'Configuração da API', function() {
        echo '<p>Informe sua API key do CurrencyFreaks para habilitar as cotações em tempo real. A chave não é armazenada no código-fonte do tema.</p>';
    }, 'u68-currencyfreaks' );

    add_settings_field( 'u68_currencyfreaks_api_key_field', 'API Key', function() {
        $val = esc_attr( get_option( 'u68_currencyfreaks_api_key', '' ) );
        echo '<input type="text" name="u68_currencyfreaks_api_key" value="' . $val . '" class="regular-text" />';
    }, 'u68-currencyfreaks', 'u68_currencyfreaks_main' );
}
add_action( 'admin_init', 'u68_currencyfreaks_register_settings' );

function u68_currencyfreaks_settings_page_render() {
    echo '<div class="wrap">';
    echo '<h1>CurrencyFreaks</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields( 'u68_currencyfreaks' );
    do_settings_sections( 'u68-currencyfreaks' );
    submit_button();
    echo '</form>';
    echo '</div>';
}

function u68_currencyfreaks_settings_menu() {
    add_options_page( 'CurrencyFreaks', 'CurrencyFreaks', 'manage_options', 'u68-currencyfreaks', 'u68_currencyfreaks_settings_page_render' );
}
add_action( 'admin_menu', 'u68_currencyfreaks_settings_menu' );

// Log REST API errors to help diagnose save issues
function u68_rest_error_logger( $response, $server, $request ) {
    try {
        $is_error = false;
        $messages = array();
        $code = '';

        if ( is_wp_error( $response ) ) {
            $is_error = true;
            $messages = $response->get_error_messages();
            $code = $response->get_error_code();
        } elseif ( $response instanceof WP_REST_Response ) {
            $data = $response->get_data();
            if ( is_wp_error( $data ) ) {
                $is_error = true;
                $messages = $data->get_error_messages();
                $code = $data->get_error_code();
            }
        }

        if ( $is_error ) {
            $route = method_exists( $request, 'get_route' ) ? $request->get_route() : 'unknown';
            $method = method_exists( $request, 'get_method' ) ? $request->get_method() : 'UNKNOWN';
            error_log( '[u68] REST error on ' . $method . ' ' . $route . ' code=' . $code . ' messages=' . implode( '; ', (array) $messages ) );
        }
    } catch ( Exception $e ) {
        // Avoid breaking responses due to logging
    }
    return $response;
}
add_filter( 'rest_post_dispatch', 'u68_rest_error_logger', 10, 3 );

// DEBUG TEMPORÁRIO - Remover após teste
add_action('wp_enqueue_scripts', function() {
    if ( empty( $_GET['debug'] ) || $_GET['debug'] !== 'ms' ) return;
    $registry = WP_Block_Type_Registry::get_instance();
    $registered = $registry->is_registered('seideagosto/currency-monitor') ? 'SIM' : 'NAO';
    $api_msg = '';
    $api_url = 'https://open.er-api.com/v6/latest/BRL';
    $res = wp_remote_get($api_url, array('timeout' => 5));
    if (!is_wp_error($res)) {
        $body = wp_remote_retrieve_body($res);
        $json = json_decode($body, true);
        if (isset($json['rates']['USD'])) {
            $api_msg = 'API funcionando - USD: ' . esc_js( $json['rates']['USD'] );
        } else {
            $api_msg = 'API retornou dados incompletos';
        }
    } else {
        $api_msg = 'Erro ao acessar API: ' . esc_js( $res->get_error_message() );
    }
    $inline = "console.log('[Currency Debug] Verificando bloco de cambio...');console.log('[Currency Debug] Bloco registrado: " . esc_js( $registered ) . "');console.log('[Currency Debug] " . $api_msg . "');";
    wp_add_inline_script('jquery', $inline, 'after');
}, 12);

/**
 * Shortcode: header search form only for mobile (toggle).
 */
function u68_header_search_mobile_shortcode() {
    $search_form = get_search_form( false );
    $output = '';
    // Mobile: toggle button + expandable search
    $output .= '<button id="searchToggleMobile" class="btn btn-link text-white d-lg-none p-0 ms-2" type="button" aria-label="' . esc_attr__( 'Abrir busca', 'u_correio68' ) . '">';
    $output .= '<i class="fa fa-search fa-lg"></i>';
    $output .= '</button>';
    $output .= '<div id="mobileSearchWrapper" class="d-lg-none position-absolute start-0 end-0 bg-primary px-3 py-2" style="top: 100%; z-index: 1030;">';
    $output .= $search_form;
    $output .= '</div>';
    return $output;
}
add_shortcode( 'u68_header_search_mobile', 'u68_header_search_mobile_shortcode' );

function cptui_register_my_cpts() {

	/**
	 * Post Type: editais.
	 */

	$labels = [
		"name" => esc_html__( "editais", "u_correio68" ),
		"singular_name" => esc_html__( "Edital", "u_correio68" ),
		"menu_name" => esc_html__( "Publicações Legais", "u_correio68" ),
		"all_items" => esc_html__( "Todos os editais", "u_correio68" ),
		"add_new" => esc_html__( "Adicionar novo", "u_correio68" ),
		"add_new_item" => esc_html__( "Adicionar novo Edital", "u_correio68" ),
		"edit_item" => esc_html__( "Editar Edital", "u_correio68" ),
		"new_item" => esc_html__( "Novo Edital", "u_correio68" ),
		"view_item" => esc_html__( "Ver Edital", "u_correio68" ),
		"view_items" => esc_html__( "Ver editais", "u_correio68" ),
		"search_items" => esc_html__( "Pesquisar editais", "u_correio68" ),
		"not_found" => esc_html__( "Nenhum editais encontrado", "u_correio68" ),
		"not_found_in_trash" => esc_html__( "Nenhum editais encontrado na lixeira", "u_correio68" ),
		"parent" => esc_html__( "Edital ascendente:", "u_correio68" ),
		"featured_image" => esc_html__( "Imagem em destaque para este Edital", "u_correio68" ),
		"set_featured_image" => esc_html__( "Definir imagem em destaque para este Edital", "u_correio68" ),
		"remove_featured_image" => esc_html__( "Remover imagem em destaque para este Edital", "u_correio68" ),
		"use_featured_image" => esc_html__( "Usar como imagem em destaque para este Edital", "u_correio68" ),
		"archives" => esc_html__( "Arquivos de Edital", "u_correio68" ),
		"insert_into_item" => esc_html__( "Inserir no Edital", "u_correio68" ),
		"uploaded_to_this_item" => esc_html__( "Enviar para esse Edital", "u_correio68" ),
		"filter_items_list" => esc_html__( "Filtrar lista de editais", "u_correio68" ),
		"items_list_navigation" => esc_html__( "Navegação na lista de editais", "u_correio68" ),
		"items_list" => esc_html__( "Lista de editais", "u_correio68" ),
		"attributes" => esc_html__( "Atributos de editais", "u_correio68" ),
		"name_admin_bar" => esc_html__( "Edital", "u_correio68" ),
		"item_published" => esc_html__( "Edital publicado", "u_correio68" ),
		"item_published_privately" => esc_html__( "Edital publicado de forma privada.", "u_correio68" ),
		"item_reverted_to_draft" => esc_html__( "Edital revertido para rascunho.", "u_correio68" ),
		"item_scheduled" => esc_html__( "Edital agendado.", "u_correio68" ),
		"item_updated" => esc_html__( "Edital atualizado.", "u_correio68" ),
		"parent_item_colon" => esc_html__( "Edital ascendente:", "u_correio68" ),
	];

	$args = [
		"label" => esc_html__( "editais", "u_correio68" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "edital", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "edital", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );

function cptui_register_my_cpts_edital() {

	/**
	 * Post Type: editais.
	 */

	$labels = [
		"name" => esc_html__( "editais", "u_correio68" ),
		"singular_name" => esc_html__( "Edital", "u_correio68" ),
		"menu_name" => esc_html__( "Publicações Legais", "u_correio68" ),
		"all_items" => esc_html__( "Todos os editais", "u_correio68" ),
		"add_new" => esc_html__( "Adicionar novo", "u_correio68" ),
		"add_new_item" => esc_html__( "Adicionar novo Edital", "u_correio68" ),
		"edit_item" => esc_html__( "Editar Edital", "u_correio68" ),
		"new_item" => esc_html__( "Novo Edital", "u_correio68" ),
		"view_item" => esc_html__( "Ver Edital", "u_correio68" ),
		"view_items" => esc_html__( "Ver editais", "u_correio68" ),
		"search_items" => esc_html__( "Pesquisar editais", "u_correio68" ),
		"not_found" => esc_html__( "Nenhum editais encontrado", "u_correio68" ),
		"not_found_in_trash" => esc_html__( "Nenhum editais encontrado na lixeira", "u_correio68" ),
		"parent" => esc_html__( "Edital ascendente:", "u_correio68" ),
		"featured_image" => esc_html__( "Imagem em destaque para este Edital", "u_correio68" ),
		"set_featured_image" => esc_html__( "Definir imagem em destaque para este Edital", "u_correio68" ),
		"remove_featured_image" => esc_html__( "Remover imagem em destaque para este Edital", "u_correio68" ),
		"use_featured_image" => esc_html__( "Usar como imagem em destaque para este Edital", "u_correio68" ),
		"archives" => esc_html__( "Arquivos de Edital", "u_correio68" ),
		"insert_into_item" => esc_html__( "Inserir no Edital", "u_correio68" ),
		"uploaded_to_this_item" => esc_html__( "Enviar para esse Edital", "u_correio68" ),
		"filter_items_list" => esc_html__( "Filtrar lista de editais", "u_correio68" ),
		"items_list_navigation" => esc_html__( "Navegação na lista de editais", "u_correio68" ),
		"items_list" => esc_html__( "Lista de editais", "u_correio68" ),
		"attributes" => esc_html__( "Atributos de editais", "u_correio68" ),
		"name_admin_bar" => esc_html__( "Edital", "u_correio68" ),
		"item_published" => esc_html__( "Edital publicado", "u_correio68" ),
		"item_published_privately" => esc_html__( "Edital publicado de forma privada.", "u_correio68" ),
		"item_reverted_to_draft" => esc_html__( "Edital revertido para rascunho.", "u_correio68" ),
		"item_scheduled" => esc_html__( "Edital agendado.", "u_correio68" ),
		"item_updated" => esc_html__( "Edital atualizado.", "u_correio68" ),
		"parent_item_colon" => esc_html__( "Edital ascendente:", "u_correio68" ),
	];

	$args = [
		"label" => esc_html__( "editais", "u_correio68" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "edital", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "edital", $args );
}

add_action( 'init', 'cptui_register_my_cpts_edital' );