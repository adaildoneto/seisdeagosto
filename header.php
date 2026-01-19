<!DOCTYPE html>
<!--  Converted from HTML to WordPress with Pinegrow Web Editor. https://pinegrow.com  -->
<html <?php language_attributes(); ?>>
    <head><meta charset="utf-8">
    
    <!-- Removed Google AdSense external script to avoid CDN/external dependency -->
        
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <meta name="author" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
        
       
    
        <?php wp_head(); ?>
       

    </head>
    <body class="<?php echo implode(' ', get_body_class()); ?>">
        <?php if( function_exists( 'wp_body_open' ) ) wp_body_open(); ?>
        <div id="wrapper">
            <nav id="sidebar" class="active navbar-fixed-top fixed-top h-100">
                <?php 
                // Sidebar header with logo and customizable intro text
                $sidebar_intro = get_theme_mod( 'u_correio68_sidebar_intro_text', function_exists( 'u_correio68_get_sidebar_intro_default_text' ) ? u_correio68_get_sidebar_intro_default_text() : '' );
                ?>
                <div class="sidebar-header p-3 text-white">
                    <div class="mb-2">
                        <?php if ( function_exists('the_custom_logo') && has_custom_logo() ) { the_custom_logo(); } else { ?>
                            <a rel="home" class="text-white p-0 m-0 h5 d-inline-block" href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
                        <?php } ?>
                    </div>
                    <?php if ( ! empty( $sidebar_intro ) ) : ?>
                        <div class="sidebar-intro small mt-4 pt-4" role="note">
                            <i class="fa fa-newspaper-o mr-2" aria-hidden="true"></i>
                            <span><?php echo nl2br( esc_html( $sidebar_intro ) ); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ( is_active_sidebar( 'navbarlateral' ) ) : ?>
                    <?php dynamic_sidebar( 'navbarlateral' ); ?>
                <?php endif; ?>
                <?php wp_nav_menu( array(
                        'menu' => 'primary',
                        'menu_class' => 'list-unstyled',
                        'container' => ''
                ) ); ?>
            </nav>
            <div id="content">
                <div class="hfeed site" id="page">
                    <header class="site-header sticky-top shadow-sm" role="banner">
                        <div itemscope itemtype="http://schema.org/WebSite" id="wrapper-navbar">
                            <a class="skip-link visually-hidden-focusable" href="#content"><?php _e( 'Skip to content', 'u_correio68' ); ?></a>

                            <nav id="headnev" class="navbar navbar-expand-lg navbar-dark bg-primary headnev topbar" aria-label="<?php esc_attr_e( 'Navegação principal', 'u_correio68' ); ?>">
                                <div class="container">
                                    <div class="d-flex align-items-center w-100">
                                        <?php echo u68_header_brand_markup(); ?>

                                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primaryNavbar" aria-controls="primaryNavbar" aria-expanded="false" aria-label="<?php esc_attr_e( 'Alternar navegação', 'u_correio68' ); ?>">
                                            <span class="navbar-toggler-icon"></span>
                                        </button>

                                        <div class="collapse navbar-collapse" id="primaryNavbar">
                                            <?php if ( has_nav_menu( 'primary' ) ) : ?>
                                                <?php
                                                wp_nav_menu( array(
                                                    'theme_location' => 'primary',
                                                    'menu_class'     => 'navbar-nav me-auto mb-2 mb-lg-0 gap-lg-2',
                                                    'container'      => '',
                                                    'depth'          => 2,
                                                    'fallback_cb'    => false,
                                                ) );
                                                ?>
                                            <?php endif; ?>

                                            <div class="d-flex align-items-center ms-lg-3 ml-auto mt-3 mt-lg-0">
                                                <?php get_search_form( true ); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </nav>

                            <?php if ( has_nav_menu( 'categorias' ) ) : ?>
                                <nav id="categoriesNav" class="navbar navbar-expand navbar-categorias categories-nav-top bg-body-tertiary border-top" aria-label="<?php esc_attr_e( 'Categorias', 'u_correio68' ); ?>">
                                    <div class="container">
                                        <div class="categories-slider-wrapper">
                                        <?php
                                        wp_nav_menu( array(
                                            'theme_location' => 'categorias',
                                            'menu_class'     => 'navbar-nav gap-2 mb-0 w-100',
                                            'container'      => '',
                                            'depth'          => 2,
                                            'fallback_cb'    => false,
                                        ) );
                                        ?>
                                        </div>
                                    </div>
                                </nav>
                            <?php endif; ?>

                        </div>
                    </header>
                    