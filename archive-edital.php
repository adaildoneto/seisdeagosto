<?php
/*
 * Template para listagem dos Editais (Custom Post Type)
 */
get_header();
?>
<main id="primary" class="site-main">
    <h1>Editais</h1>
    <?php if ( have_posts() ) : ?>
        <div class="edital-list">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="entry-summary">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        <?php the_posts_navigation(); ?>
    <?php else : ?>
        <p>Nenhum edital encontrado.</p>
    <?php endif; ?>
</main>
<?php
get_sidebar();
get_footer();
