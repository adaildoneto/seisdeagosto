<?php
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
}
endif; // u_seisbarra8_setup

add_action( 'after_setup_theme', 'u_seisbarra8_setup' );


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

    $wp_customize->add_setting( 'footer_text', array(
        'default'           => 'Orgulhosamente feito com <i class="fa fa-heart"></i> no Acre | <b>6barra8.com</b>',
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
          

    // Ensure core jQuery is loaded
    wp_enqueue_script( 'jquery');
    // Alias $ to jQuery for legacy scripts expecting global $
    wp_add_inline_script( 'jquery', 'window.$ = window.$ || window.jQuery;', 'after' );
    // Add jQuery Migrate for compatibility with plugins/themes using deprecated APIs (e.g., $.load, $.bind)
    if ( wp_script_is( 'jquery-migrate', 'registered' ) ) {
        wp_enqueue_script( 'jquery-migrate' );
    }

    wp_enqueue_script( 'u_seisbarra8-carousel_init', get_template_directory_uri() . '/assets/js/carousel_init.js', array('jquery', 'u_seisbarra8-bootstrap'), null, true );

    wp_enqueue_script( 'u_seisbarra8-popper', get_template_directory_uri() . '/assets/js/popper.js', array(), null, true );

    wp_enqueue_script( 'u_seisbarra8-menustick', get_template_directory_uri() . '/assets/js/menustick.js', array('jquery'), null, true );

    wp_enqueue_script( 'u_seisbarra8-bootstrap', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', array('jquery', 'u_seisbarra8-popper'), null, true );

    wp_enqueue_script( 'u_seisbarra8-outline', get_template_directory_uri() . '/assets/js/outline.js', null, null, true );

    wp_deregister_script( 'u_seisbarra8-plugins' );
    wp_enqueue_script( 'u_seisbarra8-plugins', get_template_directory_uri() . '/components/pg.blocks.wp/js/plugins.js', array('jquery'), null, true);

    wp_deregister_script( 'u_seisbarra8-bskitscripts' );
    wp_enqueue_script( 'u_seisbarra8-bskitscripts', get_template_directory_uri() . '/components/pg.blocks.wp/js/bskit-scripts.js', array('jquery'), null, true);

    // Remove external Google Maps API to avoid CDN/external dependency
    wp_deregister_script( 'u_seisbarra8-script' );

    wp_deregister_script( 'u_seisbarra8-slick' );
    wp_enqueue_script( 'u_seisbarra8-slick', get_template_directory_uri() . '/slick/slick.min.js', array('jquery'), null, true );
    wp_enqueue_script( 'u_seisbarra8-colunistas-slick', get_template_directory_uri() . '/assets/js/colunistas-slick.js', array('jquery', 'u_seisbarra8-slick'), null, true );
    // Weather forecast slider init (uses slick)
    wp_enqueue_script( 'u_seisbarra8-weather-forecast', get_template_directory_uri() . '/assets/js/weather-forecast.js', array('jquery', 'u_seisbarra8-slick'), null, true );
    // Post-load i18n for weekday names (pt-BR)
    wp_enqueue_script( 'u_seisbarra8-weather-i18n', get_template_directory_uri() . '/assets/js/weather-i18n.js', array('jquery', 'u_seisbarra8-weather-forecast'), null, true );

    /* Pinegrow generated Enqueue Scripts End */

        /* Pinegrow generated Enqueue Styles Begin */

    wp_deregister_style( 'u_seisbarra8-bootstrap' );
    wp_enqueue_style( 'u_seisbarra8-bootstrap', get_template_directory_uri() . '/bootstrap/css/bootstrap.min.css', false, null, 'all');

    wp_deregister_style( 'u_seisbarra8-theme' );
    wp_enqueue_style( 'u_seisbarra8-theme', get_template_directory_uri() . '/css/theme.css', false, null, 'all');
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

    wp_deregister_style( 'u_seisbarra8-woocommerce' );
    wp_enqueue_style( 'u_seisbarra8-woocommerce', get_template_directory_uri() . '/css/woocommerce.css', false, null, 'all');

    wp_deregister_style( 'u_seisbarra8-blocks' );
    wp_enqueue_style( 'u_seisbarra8-blocks', get_template_directory_uri() . '/components/pg.blocks.wp/css/blocks.css', false, null, 'all');

    wp_deregister_style( 'u_seisbarra8-plugins' );
    wp_enqueue_style( 'u_seisbarra8-plugins', get_template_directory_uri() . '/components/pg.blocks.wp/css/plugins.css', false, null, 'all');

    wp_deregister_style( 'u_seisbarra8-stylelibrary' );
    wp_enqueue_style( 'u_seisbarra8-stylelibrary', get_template_directory_uri() . '/components/pg.blocks.wp/css/style-library-1.css', false, null, 'all');

    // Remove Google Fonts to avoid CDN/external dependency (use local fonts instead)
    wp_deregister_style( 'u_seisbarra8-style' );
    wp_deregister_style( 'u_seisbarra8-style-1' );

    // Prefer local Font Awesome 4.7 if present; otherwise fallback to hiding icons to avoid broken glyphs
    wp_deregister_style( 'u_seisbarra8-fontawesome' );
    $fa_vendor_css = get_template_directory() . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css';
    if ( file_exists( $fa_vendor_css ) ) {
        wp_enqueue_style( 'u_seisbarra8-fontawesome', get_template_directory_uri() . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css', false, '4.7.0', 'all');
    } else {
        wp_enqueue_style( 'u_seisbarra8-fontawesome', get_template_directory_uri() . '/css/local-fa-fallback.css', false, null, 'all');
    }

    wp_deregister_style( 'u_seisbarra8-slick' );
    wp_enqueue_style( 'u_seisbarra8-slick', get_template_directory_uri() . '/slick/slick.css', false, null, 'all');

    wp_deregister_style( 'u_seisbarra8-slicktheme' );
    wp_enqueue_style( 'u_seisbarra8-slicktheme', get_template_directory_uri() . '/slick/slick-theme.css', false, null, 'all');

    // Enqueue blocks layout CSS
    wp_enqueue_style( 'seideagosto-blocks-layout', get_template_directory_uri() . '/css/blocks-layout.css', false, null, 'all');

    // Check if local fonts exist, otherwise fallback to Google Fonts CDN
    $stylesheet_dir = get_stylesheet_directory();
    $stylesheet_uri = get_stylesheet_directory_uri();
    $font_dir = $stylesheet_dir . '/assets/fonts';
    
    $open_sans_file = $font_dir . '/open-sans/OpenSans-Variable.ttf';
    $lato_file = $font_dir . '/lato/Lato-Regular.ttf';
    
    if ( file_exists( $open_sans_file ) && file_exists( $lato_file ) ) {
        // Use local self-hosted fonts with absolute URLs
        wp_register_style( 'u_seisbarra8-fonts', false, array(), filemtime( $lato_file ) );
        wp_enqueue_style( 'u_seisbarra8-fonts' );
        
        // Cache buster using file modification time
        $font_mtime = '?v=' . filemtime( $lato_file );
        $font_uri = $stylesheet_uri . '/assets/fonts';
        
        $u68_font_faces = "@font-face { font-family: 'Open Sans'; src: url('" . esc_url( $font_uri . '/open-sans/OpenSans-Variable.ttf' . $font_mtime ) . "') format('truetype'); font-weight: 300 700; font-style: normal; font-display: swap; }
@font-face { font-family: 'Open Sans'; src: url('" . esc_url( $font_uri . '/open-sans/OpenSans-Italic-Variable.ttf' . $font_mtime ) . "') format('truetype'); font-weight: 300 700; font-style: italic; font-display: swap; }
@font-face { font-family: 'Lato'; src: url('" . esc_url( $font_uri . '/lato/Lato-Light.ttf' . $font_mtime ) . "') format('truetype'); font-weight: 300; font-style: normal; font-display: swap; }
@font-face { font-family: 'Lato'; src: url('" . esc_url( $font_uri . '/lato/Lato-LightItalic.ttf' . $font_mtime ) . "') format('truetype'); font-weight: 300; font-style: italic; font-display: swap; }
@font-face { font-family: 'Lato'; src: url('" . esc_url( $font_uri . '/lato/Lato-Regular.ttf' . $font_mtime ) . "') format('truetype'); font-weight: 400; font-style: normal; font-display: swap; }
@font-face { font-family: 'Lato'; src: url('" . esc_url( $font_uri . '/lato/Lato-Italic.ttf' . $font_mtime ) . "') format('truetype'); font-weight: 400; font-style: italic; font-display: swap; }
@font-face { font-family: 'Lato'; src: url('" . esc_url( $font_uri . '/lato/Lato-Bold.ttf' . $font_mtime ) . "') format('truetype'); font-weight: 700; font-style: normal; font-display: swap; }
@font-face { font-family: 'Lato'; src: url('" . esc_url( $font_uri . '/lato/Lato-BoldItalic.ttf' . $font_mtime ) . "') format('truetype'); font-weight: 700; font-style: italic; font-display: swap; }";
        
        wp_add_inline_style( 'u_seisbarra8-fonts', $u68_font_faces );
    } else {
        // Fallback to Google Fonts CDN
        wp_enqueue_style( 'u_seisbarra8-google-fonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap', false, null, 'all');
    }

    wp_deregister_style( 'u_seisbarra8-style-2' );
    wp_enqueue_style( 'u_seisbarra8-style-2', get_bloginfo('stylesheet_url'), false, null, 'all');

    /* Pinegrow generated Enqueue Styles End */

    }
    add_action( 'wp_enqueue_scripts', 'u_seisbarra8_enqueue_scripts' );
endif;

function pgwp_sanitize_placeholder($input) { return $input; }
/*
 * Resource files included by Pinegrow.*/
 
 function add_jquery() {
     
    }    

    add_action('init', 'add_jquery');
 
/* Pinegrow generated Include Resources Begin */
require_once "inc/custom.php";
require_once "inc/wp_pg_helpers.php";
require_once "inc/bootstrap/wp_bootstrap4_navwalker.php";
require_once "inc/blocks.php";
require_once "inc/widgets.php";
require_once "inc/customizer.php";

    /* Pinegrow generated Include Resources End */
    
    add_filter( '1', '__return_false' );
    
    // Fallback shims for Advanced Custom Fields to avoid fatals when plugin is missing
    if ( ! function_exists( 'get_field' ) ) {
        function get_field( $selector, $post_id = false, $format_value = true ) {
            return '';
        }
    }
    if ( ! function_exists( 'the_field' ) ) {
        function the_field( $selector, $post_id = false ) {
            echo get_field( $selector, $post_id );
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

