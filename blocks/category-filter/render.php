<?php
/**
 * Render callback for Category Filter block
 */
function seisdeagosto_render_category_filter( $attributes ) {
    $show_all = isset( $attributes['showAllOption'] ) ? (bool) $attributes['showAllOption'] : true;
    $all_text = isset( $attributes['allOptionText'] ) ? sanitize_text_field( $attributes['allOptionText'] ) : 'Todas as Categorias';
    $show_count = isset( $attributes['showCount'] ) ? (bool) $attributes['showCount'] : true;
    $show_empty = isset( $attributes['showEmpty'] ) ? (bool) $attributes['showEmpty'] : false;
    $orderby = isset( $attributes['orderBy'] ) ? sanitize_text_field( $attributes['orderBy'] ) : 'name';
    $order = isset( $attributes['order'] ) ? sanitize_text_field( $attributes['order'] ) : 'ASC';
    $hierarchical = isset( $attributes['hierarchical'] ) ? (bool) $attributes['hierarchical'] : true;
    $show_hierarchy = isset( $attributes['showHierarchy'] ) ? (bool) $attributes['showHierarchy'] : true;
    $display_style = isset( $attributes['displayStyle'] ) ? sanitize_text_field( $attributes['displayStyle'] ) : 'dropdown';
    $show_icons = isset( $attributes['showIcons'] ) ? (bool) $attributes['showIcons'] : true;
    
    // Obter categorias
    $args = array(
        'taxonomy' => 'category',
        'orderby' => $orderby,
        'order' => $order,
        'hide_empty' => !$show_empty,
        'hierarchical' => $hierarchical,
    );
    
    $categories = get_categories( $args );
    
    if ( empty( $categories ) ) {
        return '<p>' . __( 'Nenhuma categoria disponível.', 'seisdeagosto' ) . '</p>';
    }
    
    // Categoria atual (se estiver em uma página de categoria)
    $current_cat_id = 0;
    if ( is_category() ) {
        $current_cat_id = get_queried_object_id();
    } elseif ( is_single() ) {
        $post_cats = get_the_category();
        if ( ! empty( $post_cats ) ) {
            $current_cat_id = $post_cats[0]->term_id;
        }
    }
    
    // Enqueue frontend script
    wp_enqueue_script(
        'category-filter-frontend',
        get_template_directory_uri() . '/blocks/category-filter/view.js',
        array( 'jquery' ),
        filemtime( get_template_directory() . '/blocks/category-filter/view.js' ),
        true
    );
    
    // Enqueue frontend CSS
    wp_enqueue_style(
        'category-filter-frontend',
        get_template_directory_uri() . '/blocks/category-filter/style.css',
        array(),
        filemtime( get_template_directory() . '/blocks/category-filter/style.css' )
    );
    
    ob_start();
    ?>
    <div class="wp-block-seisdeagosto-category-filter" data-style="<?php echo esc_attr( $display_style ); ?>">
        <div class="category-filter-wrapper">
            <?php if ( $display_style === 'dropdown' ): ?>
                <select class="category-filter-dropdown" onchange="if(this.value) window.location.href=this.value;">
                    <?php if ( $show_all ): ?>
                        <option value="<?php echo esc_url( home_url( '/' ) ); ?>" <?php selected( $current_cat_id, 0 ); ?>>
                            <?php echo esc_html( $all_text ); ?>
                        </option>
                    <?php endif; ?>
                    <?php foreach ( $categories as $category ): ?>
                        <?php
                        $level_class = '';
                        if ( $show_hierarchy && $category->parent > 0 ) {
                            $level = 1;
                            $parent = $category->parent;
                            while ( $parent > 0 ) {
                                $parent_cat = get_category( $parent );
                                if ( $parent_cat && $parent_cat->parent > 0 ) {
                                    $level++;
                                    $parent = $parent_cat->parent;
                                } else {
                                    break;
                                }
                            }
                            $level_class = 'level-' . $level;
                        }
                        ?>
                        <option 
                            value="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" 
                            class="<?php echo esc_attr( $level_class ); ?>"
                            <?php selected( $current_cat_id, $category->term_id ); ?>>
                            <?php 
                            if ( $show_hierarchy && $category->parent > 0 ) {
                                echo str_repeat( '— ', substr_count( $level_class, 'level-' ) );
                            }
                            echo esc_html( $category->name ); 
                            if ( $show_count ) {
                                echo ' (' . $category->count . ')';
                            }
                            ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
            <?php elseif ( $display_style === 'list' ): ?>
                <ul class="category-filter-list">
                    <?php foreach ( $categories as $category ): ?>
                        <?php
                        $level_class = '';
                        if ( $show_hierarchy && $category->parent > 0 ) {
                            $level = 1;
                            $parent = $category->parent;
                            while ( $parent > 0 ) {
                                $parent_cat = get_category( $parent );
                                if ( $parent_cat && $parent_cat->parent > 0 ) {
                                    $level++;
                                    $parent = $parent_cat->parent;
                                } else {
                                    break;
                                }
                            }
                            $level_class = 'level-' . $level;
                        }
                        $is_active = ( $current_cat_id === $category->term_id );
                        ?>
                        <li class="category-filter-item <?php echo esc_attr( $level_class ); ?>">
                            <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" 
                               class="category-filter-link<?php echo $is_active ? ' active' : ''; ?>">
                                <?php if ( $show_icons ): ?>
                                    <i class="fa fa-folder category-filter-icon"></i>
                                <?php endif; ?>
                                <?php echo esc_html( $category->name ); ?>
                                <?php if ( $show_count ): ?>
                                    <span class="category-filter-count"><?php echo $category->count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
            <?php elseif ( $display_style === 'grid' ): ?>
                <div class="category-filter-grid">
                    <?php foreach ( $categories as $category ): ?>
                        <?php
                        $is_active = ( $current_cat_id === $category->term_id );
                        ?>
                        <div class="category-filter-item">
                            <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" 
                               class="category-filter-link<?php echo $is_active ? ' active' : ''; ?>">
                                <?php if ( $show_icons ): ?>
                                    <i class="fa fa-folder category-filter-icon"></i>
                                <?php endif; ?>
                                <?php echo esc_html( $category->name ); ?>
                                <?php if ( $show_count ): ?>
                                    <span class="category-filter-count"><?php echo $category->count; ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                
            <?php elseif ( $display_style === 'buttons' ): ?>
                <div class="category-filter-buttons">
                    <?php if ( $show_all ): ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" 
                           class="category-filter-button<?php echo $current_cat_id === 0 ? ' active' : ''; ?>">
                            <?php echo esc_html( $all_text ); ?>
                        </a>
                    <?php endif; ?>
                    <?php foreach ( $categories as $category ): ?>
                        <?php
                        $is_active = ( $current_cat_id === $category->term_id );
                        ?>
                        <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" 
                           class="category-filter-button<?php echo $is_active ? ' active' : ''; ?>">
                            <?php 
                            echo esc_html( $category->name ); 
                            if ( $show_count ) {
                                echo ' (' . $category->count . ')';
                            }
                            ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
