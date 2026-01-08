
    <article class="mb-5">
        <div class="container">
            <?php if ( have_posts() ) : ?>
                <div class="row">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php PG_Helper::rememberShownPost(); ?>
                        <div <?php post_class( 'col-md-4 spaces' ); ?> id="post-<?php the_ID(); ?>">
                            <span class="badge badge-light bg-orange text-white badge-pill"><?php echo get_field( 'chamada' ); ?></span>
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
            <?php else : ?>
                <p><?php _e( 'Sorry, no posts matched your criteria.', 'u_correio68' ); ?></p>
            <?php endif; ?>
        </div>
    </article>
    <nav aria-label="Posts navigation">
        <?php posts_nav_link( null, __( '&#xAB; Newer Posts', 'u_correio68' ), __( 'Older Posts &#xBB;', 'u_correio68' ) ); ?>
    </nav>
