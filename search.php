<?php get_header(); ?>

<div class="wrapper" id="search-wrapper">
    <div class="container" id="content" tabindex="-1">
        <div class="row">
            <?php if ( get_theme_mod( 'show_left_sidebar' ) ) : ?>
                <div class="col-md-4 widget-area" role="complementary" id="left-sidebar">
                    <?php if ( is_active_sidebar( 'left-sidebar' ) ) : ?>
                        <?php dynamic_sidebar( 'left-sidebar' ); ?>
                    <?php endif; ?> 
                </div>
            <?php endif; ?>
            <div class="content-area col-md-8 content-area" id="primary">
                <main class="site-main" id="main">
                    <?php
                    $u68_search_tpl = absint( get_theme_mod( 'template_page_search', 0 ) );
                    if ( $u68_search_tpl ) {
                        $u68_post = get_post( $u68_search_tpl );
                        if ( $u68_post && $u68_post->post_status === 'publish' ) {
                            echo '<div class="u68-search-template">' . apply_filters( 'the_content', $u68_post->post_content ) . '</div>';
                        } else {
                            // Fallback to legacy layout if selected page is invalid
                            ?>
                            <h1><?php _e( 'Resultado da pesquisa:', 'u_correio68' ); ?> <span><?php echo esc_html( get_search_query( false ) ); ?></span></h1>
                            <p><?php echo $wp_query->found_posts.' results found.'; ?></p>
                            <?php if ( have_posts() ) : ?>
                                <article <?php post_class( 'mb-5' ); ?> id="post-<?php the_ID(); ?>">
                                    <div class="row">
                                        <?php while ( have_posts() ) : the_post(); ?>
                                            <?php PG_Helper::rememberShownPost(); ?>
                                            <div <?php post_class( 'col-md-4 spaces' ); ?> id="post-<?php the_ID(); ?>">
                                                <?php u_correio68_the_badge(); ?>
                                                <div class="card"> 
                                                    <?php echo PG_Image::getPostImage( null, 'destaque', array(
                                                            'class' => 'card-img-top img-d img-fluid'
                                                    ), 'both', null ) ?> 
                                                    <div class="card-body"> 
                                                        <a href="<?php echo esc_url( get_permalink() ); ?>"><p class="card-title"><?php the_title(); ?></p></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </article>
                            <?php else : ?>
                                <p class="lead text-muted"><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'u_correio68' ); ?></p>
                            <?php endif; ?>
                            <?php if ( !have_posts() ) : ?>
                                <?php get_search_form( true ); ?>
                            <?php endif; ?>
                            <?php
                        }
                    } else {
                        ?>
                        <h1><?php _e( 'Resultado da pesquisa:', 'u_correio68' ); ?> <span><?php echo esc_html( get_search_query( false ) ); ?></span></h1>
                        <p><?php echo $wp_query->found_posts.' results found.'; ?></p>
                        <?php if ( have_posts() ) : ?>
                            <article <?php post_class( 'mb-5' ); ?> id="post-<?php the_ID(); ?>">
                                <div class="row">
                                    <?php while ( have_posts() ) : the_post(); ?>
                                        <?php PG_Helper::rememberShownPost(); ?>
                                        <div <?php post_class( 'col-md-4 spaces' ); ?> id="post-<?php the_ID(); ?>">
                                            <?php u_correio68_the_badge(); ?>
                                            <div class="card"> 
                                                <?php echo PG_Image::getPostImage( null, 'destaque', array(
                                                        'class' => 'card-img-top img-d img-fluid'
                                                ), 'both', null ) ?> 
                                                <div class="card-body"> 
                                                    <a href="<?php echo esc_url( get_permalink() ); ?>"><p class="card-title"><?php the_title(); ?></p></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </article>
                        <?php else : ?>
                            <p class="lead text-muted"><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'u_correio68' ); ?></p>
                        <?php endif; ?>
                        <?php if ( !have_posts() ) : ?>
                            <?php get_search_form( true ); ?>
                        <?php endif; ?>
                        <?php
                    }
                    ?>
                </main>
                <nav aria-label="Posts navigation">
                    <?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
                </nav>                                 
            </div>
            <?php if ( get_theme_mod( 'show_right_sidebar' ) ) : ?>
                <div class="col-md-4 widget-area" role="complementary" id="right-sidebar">
                    <?php if ( is_active_sidebar( 'right-sidebar' ) ) : ?>
                        <?php dynamic_sidebar( 'right-sidebar' ); ?>
                    <?php endif; ?> 
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>                

<?php get_footer(); ?>