<?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <?php PG_Helper::rememberShownPost(); ?>
            <article <?php post_class(); ?> itemscope itemtype="https://schema.org/NewsArticle" id="post-<?php the_ID(); ?>">
                <header class="entry-header"><meta charset="utf-8">
                    <?php u_correio68_the_badge( array( 'class' => 'badge badge-light text-white bg-orange badge-pill' ) ); ?>
                    <p class="title-post" itemprop="headline"><?php the_title(); ?></p>
                    <div class="entry-meta">
                        <p>
                            <span><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>" itemprop="datePublished"><?php echo esc_html( get_the_date() ); ?></time></span>
                            <?php _e( 'por', 'u_correio68' ); ?>
                            <span itemprop="author" itemscope itemtype="https://schema.org/Person"><span itemprop="name"><?php the_author(); ?></span></span>
                            <meta itemprop="dateModified" content="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>" />
                        </p>
                    </div>
                </header>
                <hr>
                <div class="d-flex justify-content-end align-items-center mb-2" id="reader-toolbar" aria-label="Controles de leitura">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Tamanho da fonte">
                        <button type="button" class="btn btn-outline-secondary" id="font-decrease" aria-label="Diminuir fonte">A-</button>
                        <button type="button" class="btn btn-outline-secondary" id="font-reset" aria-label="Tamanho padrão">A</button>
                        <button type="button" class="btn btn-outline-secondary" id="font-increase" aria-label="Aumentar fonte">A+</button>
                    </div>
                </div>
                <div class="col-12"> 
                    <a href="#"> <?php if ( get_field('foto_destacada') ) : ?><?php echo PG_Image::getPostImage( null, 'large', array(
                                    'class' => 'w-100 img-thumbnail',
                                    'sizes' => 'max-width(320px) 73vw, max-width(640px) 470px, max-width(768px) 65vw, max-width(1024px) 48vw, max-width(1280px) 595px, 595px'
                            ), 'both', null ) ?><?php endif; ?><?php if ( get_field('foto_destacada') ) : ?><?php endif; ?> </a> 
                </div>
                <div class="entry-content">
                    <?php the_content(); ?>
                    <?php wp_link_pages( array() ); ?>
                </div>
                
                <?php if ( is_single() && is_active_sidebar( 'banner_post' ) ) : ?>
                   <div class="banner-post-widget-area">
                   <?php dynamic_sidebar( 'banner_post' ); ?>
                    </div>
               <?php endif; ?>

                
                <footer class="entry-footer">
                    <?php if ( get_field('fonte') ) : ?>
                        <span class="fonte"><B class="small"><?php _e( 'FONTE:', 'u_correio68' ); ?></B> <a href="<?php echo esc_url( get_field( 'fonte_url' ) ); ?>" class="small"><span><?php echo get_field( 'fonte' ); ?></span></a> </span>
                    <?php endif; ?>
                    <?php if ( has_tag() ) : ?>
                        <span class="tags-links"><?php the_tags( 'Tags: ', ', ' ); ?></span>
                    <?php endif; ?>
                    <?php edit_post_link( '<p class="text-muted">Editar Post</p>' ); ?>
                </footer>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p><?php _e( 'Sorry, no posts matched your criteria.', 'u_correio68' ); ?></p>
    <?php endif; ?>
    <div class="container navigation post-navigation pt-3 pb-3">
        <h2 class="sr-only"><?php _e( 'Post navigation', 'u_correio68' ); ?></h2>
        <div class="row">
            <div class="col-6">
                <span class="nav-previous"><?php previous_post_link( '%link', __( '&laquo; %title', 'u_correio68' ) ); ?></span>
            </div>
            <div class="col-6">
                <span class="nav-next"><?php next_post_link( '%link', __( '%title &raquo;', 'u_correio68' ) ); ?></span> 
            </div>
        </div>
    </div>
    
    <!-- News Grid block on single: 9 posts, 3 columns -->
    <div class="container pt-4 pb-4 more-news-section">
        <h3 class="section-title mb-3">Mais notícias</h3>
        <?php
            // Render using the dynamic block so it matches the editor block exactly
            $cat_id = 0;
            $yoast_primary = intval( get_post_meta( get_the_ID(), '_yoast_wpseo_primary_category', true ) );
            if ( $yoast_primary && term_exists( $yoast_primary, 'category' ) ) {
                $cat_id = $yoast_primary;
            } else {
                $cats_for_post = get_the_category( get_the_ID() );
                if ( ! empty( $cats_for_post ) && isset( $cats_for_post[0]->term_id ) ) {
                    $cat_id = intval( $cats_for_post[0]->term_id );
                }
            }
            $attrs = array(
                'categoryId'    => $cat_id,
                'numberOfPosts' => 9,
                'offset'        => 0,
                'columns'       => 3,
                'paginate'      => false,
                'excludeIds'    => array( get_the_ID() ),
            );
            $block_markup = '<!-- wp:u-correio68/news-grid ' . wp_json_encode( $attrs ) . ' /-->';
            echo do_blocks( $block_markup );
        ?>
    </div>
    <div class="row wrapper">
        <div class="col-md-3">
            <?php echo get_avatar( get_the_author_meta( 'ID' ) ); ?> 
        </div>
        <div class="col-md-9">
            <h3><?php echo get_the_author_meta( 'display_name', false ) ?></h3>
            <?php the_author_meta( 'user_description' ); ?>
        </div>
    </div>
    
    <?php if ( comments_open() || get_comments_number() || is_single() ) : ?>
        <?php comments_template( '/comments.php' ); ?>
    <?php endif; ?>