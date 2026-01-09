<?php
if ( ! function_exists( 'u_correio68_setup' ) ) :

function u_correio68_setup() {

    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    /* Pinegrow generated Load Text Domain Begin */
    // Load legacy and new text domains to prevent translation issues during rename
    load_theme_textdomain( 'u_correio68', get_template_directory() . '/languages' );
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
        function u_correio68_single_seo_opengraph() {
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
        add_action( 'wp_head', 'u_correio68_single_seo_opengraph', 5 );

    // Add menus.
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'u_correio68' ),
        'social'  => __( 'Social Links Menu', 'u_correio68' ),
    ) );

/*
     * Register custom menu locations
     */
    /* Pinegrow generated Register Menus Begin */

    register_nav_menu(  'categorias', __( 'Categorias para a capa do site', 'u_correio68' )  );

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
}
endif; // u_correio68_setup

add_action( 'after_setup_theme', 'u_correio68_setup' );


if ( ! function_exists( 'u_correio68_init' ) ) :

function u_correio68_init() {


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
endif; // u_correio68_setup

add_action( 'init', 'u_correio68_init' );


if ( ! function_exists( 'u_correio68_custom_image_sizes_names' ) ) :

function u_correio68_custom_image_sizes_names( $sizes ) {

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
add_action( 'image_size_names_choose', 'u_correio68_custom_image_sizes_names' );
endif;// u_correio68_custom_image_sizes_names



if ( ! function_exists( 'u_correio68_widgets_init' ) ) :

function u_correio68_widgets_init() {

    /*
     * Register widget areas.
     */
    /* Pinegrow generated Register Sidebars Begin */

    register_sidebar( array(
        'name' => __( 'navbarlateral', 'u_correio68' ),
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
        'name' => __( 'bannervertical', 'u_correio68' ),
        'id' => 'banneraleac-vertical',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'temperatura', 'u_correio68' ),
        'id' => 'temperatura',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Banner Colunistas', 'u_correio68' ),
        'id' => 'banner_colunistas',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'banner ALEAC', 'u_correio68' ),
        'id' => 'bannneraleac',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Grupo Whatsapp', 'u_correio68' ),
        'id' => 'whatsappcorreio68',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Colunistas 68', 'u_correio68' ),
        'id' => 'colunistas',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Na Rota do Boi', 'u_correio68' ),
        'id' => 'narotadoboi',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Right Sidebar', 'u_correio68' ),
        'id' => 'right-sidebar',
        'description' => 'Right Sidebar widget area',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'banner Footer', 'u_correio68' ),
        'id' => 'bannerfooter',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Banners do Cabral', 'u_correio68' ),
        'id' => 'cabralize',
        'description' => 'Area dos banners do Cabral',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Left Sidebar', 'u_correio68' ),
        'id' => 'left-sidebar',
        'description' => 'Left Sidebar widget area',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ) );

    /* Pinegrow generated Register Sidebars End */
}
add_action( 'widgets_init', 'u_correio68_widgets_init' );
endif;// u_correio68_widgets_init



if ( ! function_exists( 'u_correio68_customize_register' ) ) :

function u_correio68_customize_register( $wp_customize ) {
    
    $pgwp_sanitize = function_exists('pgwp_sanitize_placeholder') ? 'pgwp_sanitize_placeholder' : null;

    // ====================
    // PAINEL: Configurações do Layout
    // ====================
    $wp_customize->add_panel( 'u_correio68_layout_panel', array(
        'title'       => __( '📐 Layout e Estrutura', 'u_correio68' ),
        'description' => __( 'Configure a estrutura e layout do site', 'u_correio68' ),
        'priority'    => 25,
    ) );

    // Seção: Sidebars
    $wp_customize->add_section( 'u_correio68_sidebars', array(
        'title'       => __( 'Barras Laterais', 'u_correio68' ),
        'description' => __( 'Ative ou desative as barras laterais do site', 'u_correio68' ),
        'panel'       => 'u_correio68_layout_panel',
        'priority'    => 10,
    ));

    $wp_customize->add_setting( 'show_right_sidebar', array(
        'default'           => false,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control( 'show_right_sidebar', array(
        'label'       => __( 'Exibir Barra Lateral Direita', 'u_correio68' ),
        'description' => __( 'Marque para ativar a barra lateral direita', 'u_correio68' ),
        'type'        => 'checkbox',
        'section'     => 'u_correio68_sidebars',
    ));

    $wp_customize->add_setting( 'show_left_sidebar', array(
        'default'           => false,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control( 'show_left_sidebar', array(
        'label'       => __( 'Exibir Barra Lateral Esquerda', 'u_correio68' ),
        'description' => __( 'Marque para ativar a barra lateral esquerda', 'u_correio68' ),
        'type'        => 'checkbox',
        'section'     => 'u_correio68_sidebars',
    ));

    // ====================
    // PAINEL: Conteúdo do Site
    // ====================
    $wp_customize->add_panel( 'u_correio68_content_panel', array(
        'title'       => __( '📝 Conteúdo', 'u_correio68' ),
        'description' => __( 'Personalize textos e conteúdos do site', 'u_correio68' ),
        'priority'    => 35,
    ) );

    // Seção: Rodapé
    $wp_customize->add_section( 'u_correio68_footer', array(
        'title'       => __( 'Rodapé', 'u_correio68' ),
        'description' => __( 'Personalize o texto do rodapé', 'u_correio68' ),
        'panel'       => 'u_correio68_content_panel',
        'priority'    => 10,
    ));

    $wp_customize->add_setting( 'footer_text', array(
        'default'           => 'Orgulhosamente feito com <i class="fa fa-heart"></i> no Acre | <b>Correio68.com</b>',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control( 'footer_text', array(
        'label'       => __( 'Texto do Rodapé', 'u_correio68' ),
        'description' => __( 'HTML permitido: <i>, <b>, <a>, <span>', 'u_correio68' ),
        'type'        => 'textarea',
        'section'     => 'u_correio68_footer',
    ));

    /* Pinegrow generated Customizer Controls End */

}
add_action( 'customize_register', 'u_correio68_customize_register' );
endif;// u_correio68_customize_register


if ( ! function_exists( 'u_correio68_enqueue_scripts' ) ) :
    function u_correio68_enqueue_scripts() {

        /* Pinegrow generated Enqueue Scripts Begin */
          

    wp_enqueue_script( 'jquery');

    wp_enqueue_script( 'u_correio68-carousel_init', get_template_directory_uri() . '/assets/js/carousel_init.js', array('jquery', 'u_correio68-bootstrap'), null, true );

    wp_enqueue_script( 'u_correio68-popper', get_template_directory_uri() . '/assets/js/popper.js', array(), null, true );

    wp_enqueue_script( 'u_correio68-menustick', get_template_directory_uri() . '/assets/js/menustick.js', array('jquery'), null, true );

    wp_enqueue_script( 'u_correio68-bootstrap', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', array('jquery', 'u_correio68-popper'), null, true );

    wp_enqueue_script( 'u_correio68-outline', get_template_directory_uri() . '/assets/js/outline.js', null, null, true );

    wp_deregister_script( 'u_correio68-plugins' );
    wp_enqueue_script( 'u_correio68-plugins', get_template_directory_uri() . '/components/pg.blocks.wp/js/plugins.js', false, null, true);

    wp_deregister_script( 'u_correio68-bskitscripts' );
    wp_enqueue_script( 'u_correio68-bskitscripts', get_template_directory_uri() . '/components/pg.blocks.wp/js/bskit-scripts.js', false, null, true);

    // Remove external Google Maps API to avoid CDN/external dependency
    wp_deregister_script( 'u_correio68-script' );

    wp_deregister_script( 'u_correio68-slick' );
    wp_enqueue_script( 'u_correio68-slick', get_template_directory_uri() . '/slick/slick.min.js', array('jquery'), null, true );
    wp_enqueue_script( 'u_correio68-colunistas-slick', get_template_directory_uri() . '/assets/js/colunistas-slick.js', array('jquery', 'u_correio68-slick'), null, true );

    /* Pinegrow generated Enqueue Scripts End */

        /* Pinegrow generated Enqueue Styles Begin */

    wp_deregister_style( 'u_correio68-bootstrap' );
    wp_enqueue_style( 'u_correio68-bootstrap', get_template_directory_uri() . '/bootstrap/css/bootstrap.min.css', false, null, 'all');

    wp_deregister_style( 'u_correio68-theme' );
    wp_enqueue_style( 'u_correio68-theme', get_template_directory_uri() . '/css/theme.css', false, null, 'all');

    wp_deregister_style( 'u_correio68-woocommerce' );
    wp_enqueue_style( 'u_correio68-woocommerce', get_template_directory_uri() . '/css/woocommerce.css', false, null, 'all');

    wp_deregister_style( 'u_correio68-blocks' );
    wp_enqueue_style( 'u_correio68-blocks', get_template_directory_uri() . '/components/pg.blocks.wp/css/blocks.css', false, null, 'all');

    wp_deregister_style( 'u_correio68-plugins' );
    wp_enqueue_style( 'u_correio68-plugins', get_template_directory_uri() . '/components/pg.blocks.wp/css/plugins.css', false, null, 'all');

    wp_deregister_style( 'u_correio68-stylelibrary' );
    wp_enqueue_style( 'u_correio68-stylelibrary', get_template_directory_uri() . '/components/pg.blocks.wp/css/style-library-1.css', false, null, 'all');

    // Remove Google Fonts to avoid CDN/external dependency (use local fonts instead)
    wp_deregister_style( 'u_correio68-style' );
    wp_deregister_style( 'u_correio68-style-1' );
    // Enqueue local self-hosted fonts (Open Sans, Lato)
    wp_enqueue_style( 'u_correio68-local-fonts', get_template_directory_uri() . '/css/fonts-local.css', false, null, 'all');

    // Prefer local Font Awesome 4.7 if present; otherwise fallback to hiding icons to avoid broken glyphs
    wp_deregister_style( 'u_correio68-fontawesome' );
    $fa_vendor_css = get_template_directory() . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css';
    if ( file_exists( $fa_vendor_css ) ) {
        wp_enqueue_style( 'u_correio68-fontawesome', get_template_directory_uri() . '/assets/vendor/font-awesome-4.7/css/font-awesome.min.css', false, '4.7.0', 'all');
    } else {
        wp_enqueue_style( 'u_correio68-fontawesome', get_template_directory_uri() . '/css/local-fa-fallback.css', false, null, 'all');
    }

    wp_deregister_style( 'u_correio68-slick' );
    wp_enqueue_style( 'u_correio68-slick', get_template_directory_uri() . '/slick/slick.css', false, null, 'all');

    wp_deregister_style( 'u_correio68-slicktheme' );
    wp_enqueue_style( 'u_correio68-slicktheme', get_template_directory_uri() . '/slick/slick-theme.css', false, null, 'all');

    wp_deregister_style( 'u_correio68-style-2' );
    wp_enqueue_style( 'u_correio68-style-2', get_bloginfo('stylesheet_url'), false, null, 'all');

    /* Pinegrow generated Enqueue Styles End */

    }
    add_action( 'wp_enqueue_scripts', 'u_correio68_enqueue_scripts' );
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

    