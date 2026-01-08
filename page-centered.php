<?php
/*
 * Template Name: centered page
 * Template Post Type: page
 */

get_header();
?>

<div class="container" id="content" tabindex="-1">
    <div class="row">
        <div class="col-12">
            <main id="main" class="site-main py-4">
                <?php
                    get_template_part( 'loop-templates/content', 'page' );

                ?>
            </main>
        </div>
    </div>
    
</div>

<?php get_footer(); ?>
