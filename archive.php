<?php get_header(); ?>

<div class="wrapper" id="archive-wrapper">
    <div class="container " id="content" tabindex="-1">
        <div class="row">
            <?php if ( get_theme_mod( 'show_left_sidebar' ) ) : ?>
                <div class="col-md-4 widget-area" role="complementary" id="left-sidebar">
                    <?php if ( is_active_sidebar( 'left-sidebar' ) ) : ?>
                        <?php dynamic_sidebar( 'left-sidebar' ); ?>
                    <?php endif; ?> 
                </div>
            <?php endif; ?>
            <div class="col-md-7 offset-md-1 content-area" id="primary">
                <main>
                    <?php
                    $u68_tpl_page = 0;
                    if ( is_category() ) {
                        $u68_tpl_page = absint( get_theme_mod( 'template_page_category', 0 ) );
                    } elseif ( is_tag() ) {
                        $u68_tpl_page = absint( get_theme_mod( 'template_page_tag', 0 ) );
                    }
                    if ( $u68_tpl_page ) {
                        $u68_post = get_post( $u68_tpl_page );
                        if ( $u68_post && $u68_post->post_status === 'publish' ) {
                            echo '<div class="u68-archive-template">' . apply_filters( 'the_content', $u68_post->post_content ) . '</div>';
                        } else {
                            get_template_part( 'loop-templates/content-archive' );
                        }
                    } else {
                        get_template_part( 'loop-templates/content-archive' );
                    }
                    ?>
                </main>                                 
            </div>
            <div class="col-md-3 widget-area" role="complementary" id="right-sidebar">
                <?php if ( is_active_sidebar( 'right-sidebar' ) ) : ?>
                    <?php dynamic_sidebar( 'right-sidebar' ); ?>
                <?php endif; ?> 
            </div>
        </div>
    </div>
</div>                

<?php get_footer(); ?>