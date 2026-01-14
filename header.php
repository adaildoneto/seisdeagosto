<!DOCTYPE html>
<!--  Converted from HTML to WordPress with Pinegrow Web Editor. https://pinegrow.com  -->
<html <?php language_attributes(); ?>>
    <head><meta charset="utf-8">
    
    <!-- Removed Google AdSense external script to avoid CDN/external dependency -->
        
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <meta name="author" content="Correio68">
        
       
        <meta content="Pinegrow Web Editor" name="generator">
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

                            <!-- Topbar: small info + socials -->
                            <nav class="navbar navbar-dark bg-primary topbar primeiromenu py-1" aria-label="Topbar">
                                <div class="container">
                                    <div class="row w-100">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center justify-content-between">
                                        <div class="topbar-left d-flex align-items-center">
                                            <?php if ( is_active_sidebar( 'temperatura' ) ) : ?>
                                                <?php dynamic_sidebar( 'temperatura' ); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="topbar-right social-icons d-flex align-items-center">
                                            <a href="#" aria-label="Facebook" class="text-white ml-2"><i class="fa fa-facebook"></i></a>
                                            <a href="#" aria-label="Twitter" class="text-white ml-2"><i class="fa fa-twitter"></i></a>
                                            <a href="#" aria-label="Instagram" class="text-white ml-2"><i class="fa fa-instagram"></i></a>
                                            <a href="#" aria-label="WhatsApp" class="text-white ml-2"><i class="fa fa-whatsapp"></i></a>
                                            <a href="#" aria-label="Telegram" class="text-white ml-2"><i class="fa fa-telegram"></i></a>
                                            <a href="#" aria-label="YouTube" class="text-white ml-2"><i class="fa fa-youtube"></i></a>
                                        </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </nav>

                            <!-- Main navbar: brand + sidebar toggle + search -->
                            <nav id="headnev" class="bg-primary headnev navbar navbar-dark navbar-expand-lg topbar" aria-label="Main Navigation">
                                <div class="container">
                                    <div class="row w-100">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center w-100">
                                        <button class="btn bg-primary text-white mr-3" id="sidebarCollapse" aria-label="Abrir menu lateral">
                                            <i class="fa fa-align-justify"></i>
                                        </button>

                                        <?php if ( ! has_custom_logo() ) : ?>
                                            <a rel="home" class="navbar-brand" href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
                                        <?php else : ?>
                                            <?php the_custom_logo(); ?>
                                        <?php endif; ?>

                                                                                <div class="ml-auto d-flex align-items-center">
                                                                                    <!-- Desktop search -->
                                                                                    <div class="d-none d-lg-block">
                                                                                        <?php get_search_form( true ); ?>
                                                                                    </div>
                                                                                    <!-- Mobile: search is shown below logo by default -->
                                                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </nav>
                                                        <!-- Mobile search wrapper -->
                                                        <div id="mobileSearchWrapper" class="mobile-search d-block d-lg-none px-3">
                                                                <div class="containerpy-1">
                                                                        <?php get_search_form( true ); ?>
                                                                </div>
                                                        </div>
                                                        <script>
                                                        (function(){
                                                            var btn = document.getElementById('sidebarCollapse');
                                                            var sidebar = document.getElementById('sidebar');
                                                            var headerNav = document.getElementById('headnev');
                                                            if (btn && sidebar) {
                                                                btn.setAttribute('aria-controls', 'sidebar');
                                                                btn.setAttribute('aria-expanded', sidebar.classList.contains('active') ? 'false' : 'true');
                                                                btn.addEventListener('click', function(){
                                                                    var isActive = sidebar.classList.toggle('active');
                                                                    // When active, sidebar is hidden (slides left). Not expanded.
                                                                    btn.setAttribute('aria-expanded', (!isActive).toString());
                                                                });

                                                                // Sync sidebar background with header background color
                                                                try {
                                                                    if (headerNav) {
                                                                        var headerBg = window.getComputedStyle(headerNav).backgroundColor;
                                                                        if (headerBg) {
                                                                            sidebar.style.backgroundColor = headerBg;
                                                                        }
                                                                    }
                                                                } catch (e) {
                                                                    // ignore
                                                                }

                                                                // Close sidebar when clicking outside
                                                                document.addEventListener('click', function(ev){
                                                                    if (!sidebar || sidebar.classList.contains('active')) return;
                                                                    var target = ev.target;
                                                                    var clickedInsideSidebar = sidebar.contains(target);
                                                                    var clickedToggle = btn.contains(target);
                                                                    if (!clickedInsideSidebar && !clickedToggle) {
                                                                        sidebar.classList.add('active');
                                                                        btn.setAttribute('aria-expanded', 'false');
                                                                    }
                                                                });
                                                            }
                                                            // Mobile search displays by default below logo; no toggle needed
                                                        })();
                                                        </script>

                            <!-- Categories bar: horizontally scrollable on mobile -->
                            <nav class="bg-primary navbar navbar-categorias" aria-label="Categorias">
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
                        </div>
                    </header>
                    