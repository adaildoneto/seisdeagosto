<?php
/*
 * Template para exibir um único Edital (Custom Post Type)
 */
get_header();
?>
<main id="primary" class="site-main">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
                <h1><?php the_title(); ?></h1>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>
            </article>
        <?php endwhile;
    else : ?>
        <p>Nenhum edital encontrado.</p>
    <?php endif; ?>
</main>
<?php
get_sidebar();
get_footer();
