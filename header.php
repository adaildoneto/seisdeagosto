<!DOCTYPE html>
<!--  Converted from HTML to WordPress with Pinegrow Web Editor. https://pinegrow.com  -->
<html <?php language_attributes(); ?>>
    <head><meta charset="utf-8">
    
    <!-- Removed Google AdSense external script to avoid CDN/external dependency -->
        
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <meta name="author" content="6barra8">
        
       
    
        <?php wp_head(); ?>
       

    </head>
    <body class="<?php echo implode(' ', get_body_class()); ?>">
        <?php if( function_exists( 'wp_body_open' ) ) wp_body_open(); ?>
        <div id="wrapper">
            <nav id="sidebar" class="active navbar-fixed-top fixed-top h-100">
                <?php 
                // Sidebar header with logo and customizable intro text
                $sidebar_intro = get_theme_mod( 'u_correio68_sidebar_intro_text', 'O 6barra8 é um jornal em homenagem a seis de agosto, data da revolução acreana. Temos orgulho de ser acreano e a revolução virá através da informação.' );
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
                            <a class="skip-link sr-only sr-only-focusable" href="#content"><?php _e( 'Skip to content', 'u_correio68' ); ?></a>

                            <!-- Main navbar: brand + search -->
                            <nav id="headnev" class="bg-primary headnev navbar navbar-dark navbar-expand-lg topbar" aria-label="Main Navigation">
                                <div class="container">
                                    <div class="row w-100">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center w-100">

                                        <?php if ( ! has_custom_logo() ) : ?>
                                            <a rel="home" class="navbar-brand" href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
                                        <?php else : ?>
                                            <?php the_custom_logo(); ?>
                                        <?php endif; ?>

                                                                                <!-- Desktop search -->
                                                                                <div class="ml-auto d-none d-lg-block">
                                                                                    <?php get_search_form( true ); ?>
                                                                                </div>
                                                                                
                                                                                <!-- Mobile search toggle button -->
                                                                                <button class="btn btn-search-toggle d-lg-none ml-auto" id="searchToggleMobile" aria-label="Abrir busca">
                                                                                    <i class="fa fa-search"></i>
                                                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </nav>

                            <!-- Mobile expandable search -->
                            <div id="mobileSearchWrapper" class="mobile-search-expanded d-lg-none">
                                <div class="container py-2">
                                    <?php get_search_form( true ); ?>
                                </div>
                            </div>

                            <!-- Categories bar: hidden on scroll -->
                            <nav id="categoriesNav" class="bg-primary navbar navbar-categorias categories-nav-top" aria-label="Categorias">
                                <div class="container">
                                    <div class="row w-100">
                                        <div class="col-12">
                                            <div class="d-flex flex-nowrap overflow-auto w-100">
                                        <?php if ( has_nav_menu( 'categorias' ) ) : ?>
                                            <?php
                                            $menu_args = array(
                                                'menu'           => 'categorias',
                                                'menu_class'     => 'navbar-nav flex-row flex-nowrap align-items-center justify-content-center w-100',
                                                'container'      => '',
                                                'depth'          => 2,
                                                'theme_location' => 'categorias',
                                                'fallback_cb'    => false,
                                            );
                                            if ( class_exists( 'wp_bootstrap4_navwalker' ) ) {
                                                $menu_args['walker'] = new wp_bootstrap4_navwalker();
                                            }
                                            wp_nav_menu( $menu_args );
                                            ?>
                                        <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </nav>

                            <?php if ( ! ( function_exists( 'u_seisbarra8_is_amp' ) && u_seisbarra8_is_amp() ) ) : ?>
                            <script>
                            (function() {
                                // Scroll handler for categories and header with debounce
                                var categoriesNav = document.getElementById('categoriesNav');
                                var header = document.querySelector('.site-header');
                                var contentWrapper = document.getElementById('content');
                                var searchToggle = document.getElementById('searchToggleMobile');
                                var mobileSearchWrapper = document.getElementById('mobileSearchWrapper');
                                var scrollThreshold = 50;
                                var ticking = false;
                                var lastScrollTop = 0;
                                
                                function updateScrollState() {
                                    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                                    
                                    if (scrollTop > scrollThreshold) {
                                        header.classList.add('scrolled', 'fixed-header');
                                        categoriesNav.classList.add('hidden');
                                        if (contentWrapper) {
                                            contentWrapper.classList.add('header-fixed');
                                        }
                                        // Close mobile search when scrolling
                                        if (mobileSearchWrapper && searchToggle) {
                                            mobileSearchWrapper.classList.remove('active');
                                            searchToggle.classList.remove('active');
                                        }
                                    } else if (scrollTop <= scrollThreshold) {
                                        header.classList.remove('scrolled', 'fixed-header');
                                        categoriesNav.classList.remove('hidden');
                                        if (contentWrapper) {
                                            contentWrapper.classList.remove('header-fixed');
                                        }
                                    }
                                    ticking = false;
                                }
                                
                                window.addEventListener('scroll', function() {
                                    lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;
                                    if (!ticking) {
                                        window.requestAnimationFrame(updateScrollState);
                                        ticking = true;
                                    }
                                }, { passive: true });
                                
                                // Mobile search toggle
                                if (searchToggle) {
                                    searchToggle.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        mobileSearchWrapper.classList.toggle('active');
                                        searchToggle.classList.toggle('active');
                                    });
                                    
                                    // Close search when clicking outside
                                    document.addEventListener('click', function(e) {
                                        if (!searchToggle.contains(e.target) && !mobileSearchWrapper.contains(e.target)) {
                                            mobileSearchWrapper.classList.remove('active');
                                            searchToggle.classList.remove('active');
                                        }
                                    });
                                    
                                    // Close search when clicking on search input (auto-focus)
                                    var searchInput = mobileSearchWrapper.querySelector('input[type="search"]');
                                    if (searchInput) {
                                        searchInput.addEventListener('blur', function() {
                                            setTimeout(function() {
                                                if (!document.activeElement.closest('.navbar-search')) {
                                                    mobileSearchWrapper.classList.remove('active');
                                                    searchToggle.classList.remove('active');
                                                }
                                            }, 200);
                                        });
                                    }
                                }
                            })();
                            </script>
                            <?php endif; ?>
                        </div>
                    </header>
                    