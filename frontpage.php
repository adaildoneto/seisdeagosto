<?php
/*
 Template Name: Home
 Template Post Type: post, page
*/
get_header(); ?>

<div class="main-container-wrapper">
    <div class="no-wrapper">
        <?php
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
        ?>
    </div>
</div>

<?php get_footer(); ?>
