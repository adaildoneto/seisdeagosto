<?php
/**
 * Custom Widgets for UbberCorreio68
 */

class UbberCorreio68_Destaques_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'u_correio68_destaques_widget',
            __( 'UbberCorreio68: Destaques (Topo)', 'u_correio68' ),
            array( 'description' => __( 'Exibe 1 post grande e 2 pequenos ao lado.', 'u_correio68' ), )
        );
    }

    public function widget( $args, $instance ) {
        // Defaults
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'] );
        $category = ! empty( $instance['category'] ) ? $instance['category'] : 0;

        echo $args['before_widget'];
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        // Query for Big Highlight (1 post)
        $args_big = array(
            'post_type'      => 'post',
            'posts_per_page' => 1,
            'order'          => 'DESC',
            'orderby'        => 'date',
        );
        if ( $category ) {
            $args_big['cat'] = $category;
        }
        
        $query_big = new WP_Query( $args_big );

        // Query for Small Highlights (2 posts, offset 1)
        $args_small = array(
            'post_type'      => 'post',
            'posts_per_page' => 2,
            'offset'         => 1,
            'order'          => 'DESC',
            'orderby'        => 'date',
        );
        if ( $category ) {
            $args_small['cat'] = $category;
        }
        
        // Note: Offset breaks pagination and sometimes simple queries if not careful, 
        // but for a static widget it's usually fine. 
        // However, if category is set, offset might need adjustment or exclusion of the first post ID.
        
        // Better approach: Get 3 posts, use first for big, next 2 for small.
        $args_all = array(
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'order'          => 'DESC',
            'orderby'        => 'date',
        );
        if ( $category ) {
            $args_all['cat'] = $category;
        }
        
        $query_all = new WP_Query( $args_all );

        if ( $query_all->have_posts() ) :
            $posts = $query_all->posts;
            $post_big = isset($posts[0]) ? $posts[0] : null;
            $posts_small = array_slice($posts, 1, 2);
            ?>

            <div class="destaquebg no-gutters row">
                <div class="container">
                    <div class="row wrapper">
                        <!-- Big Post -->
                        <div class="col-md-8">
                            <?php if ( $post_big ) : 
                                $post = $post_big; 
                                setup_postdata( $post ); 
                                if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost();
                            ?>
                                <div <?php post_class( 'bg-dark card spaces text-white' ); ?> id="post-<?php the_ID(); ?>">
                                    <?php 
                                    if ( class_exists( 'PG_Image' ) ) {
                                        echo PG_Image::getPostImage( get_the_ID(), 'destatquegrande', array(
                                            'class' => 'imagem-destaque w-100'
                                        ), null, null );
                                    } else {
                                        the_post_thumbnail( 'destatquegrande', array( 'class' => 'imagem-destaque w-100' ) );
                                    }
                                    ?>
                                    <div class="card-img-overlay gradiente space">
                                        <div class="tituloD">
                                            <?php 
                                            $cor = get_field( 'cor' );
                                            $icones = get_field( 'icones' );
                                            $chamada = get_field( 'chamada' );
                                            if ( $chamada ) : ?>
                                                <span class="badge badge-light text-white bg-orange badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important;"> 
                                                    <ion-icon class="<?php echo esc_attr($icones); ?>"></ion-icon> 
                                                    <span><?php echo esc_html($chamada); ?></span>
                                                </span>
                                                <br>
                                            <?php endif; ?>
                                            <a href="<?php echo esc_url( get_permalink() ); ?>"><span class="TituloGrande text-white text-shadow"><?php the_title(); ?></span></a>
                                        </div>
                                    </div>
                                </div>
                            <?php wp_reset_postdata(); endif; ?>
                        </div>

                        <!-- Small Posts -->
                        <div class="col-md-4">
                            <div>
                                <?php foreach ( $posts_small as $post ) : 
                                    setup_postdata( $post ); 
                                    if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost();
                                ?>
                                    <div <?php post_class( 'bg-dark card spaces text-white' ); ?> id="post-<?php the_ID(); ?>">
                                        <?php 
                                        if ( class_exists( 'PG_Image' ) ) {
                                            echo PG_Image::getPostImage( get_the_ID(), 'destatquegrande', array(
                                                'class' => 'w-100',
                                                'sizes' => 'max-width(320px) 85vw, max-width(640px) 510px, max-width(768px) 70vw, max-width(1024px) 60vw, max-width(1280px) 730px, 730px'
                                            ), 'both', null );
                                        } else {
                                            the_post_thumbnail( 'destatquegrande', array( 'class' => 'w-100' ) );
                                        }
                                        ?>
                                        <div class="card-img-overlay grad gradiente space">
                                            <div class="tituloD">
                                                <?php 
                                                $cor = get_field( 'cor' );
                                                $icones = get_field( 'icones' );
                                                $chamada = get_field( 'chamada' );
                                                if ( $chamada ) : ?>
                                                    <span class="badge badge-light text-white bg-orange badge-pill" style="background-color:<?php echo esc_attr($cor); ?> !important;"> 
                                                        <ion-icon class="<?php echo esc_attr($icones); ?>"></ion-icon> 
                                                        <span><?php echo esc_html($chamada); ?></span>
                                                    </span>
                                                <?php endif; ?>
                                                <a href="<?php echo esc_url( get_permalink() ); ?>"><br><span class="TituloGrande2 text-shadow text-white"><?php the_title(); ?></span></a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; wp_reset_postdata(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        endif;

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Destaques', 'u_correio68' );
        $category = ! empty( $instance['category'] ) ? $instance['category'] : 0;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'u_correio68' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php _e( 'Category:', 'u_correio68' ); ?></label> 
            <?php wp_dropdown_categories( array(
                'show_option_all' => 'All Categories',
                'name'            => $this->get_field_name( 'category' ),
                'selected'        => $category,
                'class'           => 'widefat',
            ) ); ?>
        </p>
        <?php 
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['category'] = ( ! empty( $new_instance['category'] ) ) ? absint( $new_instance['category'] ) : 0;
        return $instance;
    }
}


class UbberCorreio68_Colunista_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'u_correio68_colunista_widget',
            __( 'UbberCorreio68: Colunista', 'u_correio68' ),
            array( 'description' => __( 'Exibe um colunista com foto e último post.', 'u_correio68' ), )
        );
    }

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'] );
        $author_name = ! empty( $instance['author_name'] ) ? $instance['author_name'] : '';
        $image_url = ! empty( $instance['image_url'] ) ? $instance['image_url'] : '';
        $category = ! empty( $instance['category'] ) ? $instance['category'] : 0;

        echo $args['before_widget'];
        
        // Wrapper class for grid if needed, or just the widget content
        // Using 'our-team' structure from the hardcoded file
        ?>
        <div class="our-team">
            <div class="picture">
                <?php if ( $image_url ) : ?>
                    <img class="img-fluid" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>">
                <?php endif; ?>
            </div>
            <div class="team-content">
                <?php if ( $title ) : ?>
                    <h5 class="name"><?php echo esc_html( $title ); ?></h5>
                <?php endif; ?>
                <?php if ( $author_name ) : ?>
                    <span class="small"><?php echo esc_html( 'por ' . $author_name ); ?></span>
                <?php endif; ?>
            </div>

            <?php
            if ( $category ) {
                $query_args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => 1,
                    'cat'            => $category,
                    'order'          => 'DESC',
                );
                $query = new WP_Query( $query_args );

                if ( $query->have_posts() ) :
                    while ( $query->have_posts() ) : $query->the_post();
                        if ( class_exists( 'PG_Helper' ) ) PG_Helper::rememberShownPost();
                        ?>
                        <a href="<?php the_permalink(); ?>">
                            <p><?php the_title(); ?></p>
                        </a>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
            }
            ?>
        </div>
        <?php

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $author_name = ! empty( $instance['author_name'] ) ? $instance['author_name'] : '';
        $image_url = ! empty( $instance['image_url'] ) ? $instance['image_url'] : '';
        $category = ! empty( $instance['category'] ) ? $instance['category'] : 0;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Coluna (Título):', 'u_correio68' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'author_name' ) ); ?>"><?php _e( 'Nome do Autor:', 'u_correio68' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'author_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'author_name' ) ); ?>" type="text" value="<?php echo esc_attr( $author_name ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'image_url' ) ); ?>"><?php _e( 'URL da Foto:', 'u_correio68' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_url' ) ); ?>" type="text" value="<?php echo esc_attr( $image_url ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php _e( 'Categoria:', 'u_correio68' ); ?></label> 
            <?php wp_dropdown_categories( array(
                'show_option_all' => 'Select Category',
                'name'            => $this->get_field_name( 'category' ),
                'selected'        => $category,
                'class'           => 'widefat',
            ) ); ?>
        </p>
        <?php 
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['author_name'] = ( ! empty( $new_instance['author_name'] ) ) ? sanitize_text_field( $new_instance['author_name'] ) : '';
        $instance['image_url'] = ( ! empty( $new_instance['image_url'] ) ) ? esc_url_raw( $new_instance['image_url'] ) : '';
        $instance['category'] = ( ! empty( $new_instance['category'] ) ) ? absint( $new_instance['category'] ) : 0;
        return $instance;
    }
}

// Register the widget
function u_correio68_register_widgets() {
    register_widget( 'UbberCorreio68_Destaques_Widget' );
    register_widget( 'UbberCorreio68_Colunista_Widget' );
}
add_action( 'widgets_init', 'u_correio68_register_widgets' );
