
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <?php PG_Helper::rememberShownPost(); ?>
            <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
            <?php if ( ! is_front_page() ) : ?>
            <header class="entry-header">
                <h1><?php the_title(); ?></h1>
                <div class="entry-meta"></div>
            </header>
            <?php endif; ?> 
                <div class="entry-content">
                    <?php the_content(); ?>
                    <?php wp_link_pages( array() ); ?>
                </div>
                <footer class="entry-footer">
                    <?php if ( has_tag() ) : ?>
                        <span class="tags-links"><?php the_tags( 'Tags: ', ', ' ); ?></span>
                    <?php endif; ?>
                    <?php edit_post_link( '<p class="text-muted">Edit Post</p>' ); ?>
                </footer>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p><?php _e( 'Sorry, no posts matched your criteria.', 'u_correio68' ); ?></p>
    <?php endif; ?>
    <?php if ( comments_open() || get_comments_number() || is_page() ) : ?>
        <?php comments_template( '/comments.php' ); ?>
    <?php endif; ?>
