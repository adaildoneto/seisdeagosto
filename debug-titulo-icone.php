<?php
/**
 * Debug helpers for the "Titulo com Icone" block.
 *
 * Arquivo utilitario: chame debug_titulo_icone_enable() manualmente quando precisar.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function debug_titulo_icone_editor() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <script>
    console.log('=== DEBUG TITULO COM ICONE ===');
    console.log('WordPress loaded:', typeof wp !== 'undefined');
    if (typeof wp !== 'undefined') {
        console.log('wp.blocks:', typeof wp.blocks !== 'undefined');
        console.log('wp.element:', typeof wp.element !== 'undefined');
        console.log('wp.hooks:', typeof wp.hooks !== 'undefined');
        console.log('wp.compose:', typeof wp.compose !== 'undefined');
        console.log('wp.components:', typeof wp.components !== 'undefined');
    }

    console.log('jQuery loaded:', typeof jQuery !== 'undefined');
    console.log('seideagostoBlocks:', !!window.seideagostoBlocks);

    if (typeof wp !== 'undefined' && wp.blocks) {
        const block = wp.blocks.getBlockType('seisdeagosto/titulo-com-icone');
        console.log('Block registered:', !!block);
        if (block) {
            console.log('Block metadata:', {
                name: block.name,
                title: block.title,
                category: block.category,
                attributes: Object.keys(block.attributes || {})
            });
        }
    }
    </script>
    <?php
}

function debug_titulo_icone_frontend() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <script>
    console.log('=== DEBUG FRONTEND TITULO COM ICONE ===');
    const tituloBlocks = document.querySelectorAll('.titulo-com-icone-wrapper');
    console.log('Blocks found:', tituloBlocks.length);
    </script>
    <?php
}

function debug_show_registered_blocks() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || 'post' !== $screen->base ) {
        return;
    }

    $registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
    $titulo_block      = null;

    foreach ( $registered_blocks as $name => $block ) {
        if ( 'seisdeagosto/titulo-com-icone' === $name ) {
            $titulo_block = $block;
            break;
        }
    }

    if ( ! $titulo_block ) {
        echo '<div class="notice notice-error"><p><strong>Bloco "seisdeagosto/titulo-com-icone" nao esta registrado.</strong></p></div>';
        return;
    }

    echo '<div class="notice notice-success is-dismissible">';
    echo '<p><strong>Bloco "Titulo com Icone" esta registrado.</strong></p>';
    echo '<ul style="list-style-type: disc; margin-left: 20px;">';
    echo '<li>Nome: ' . esc_html( $titulo_block->name ) . '</li>';
    echo '<li>Titulo: ' . esc_html( $titulo_block->title ) . '</li>';
    echo '<li>Categoria: ' . esc_html( $titulo_block->category ) . '</li>';
    echo '<li>Render callback: ' . ( isset( $titulo_block->render_callback ) && is_callable( $titulo_block->render_callback ) ? 'Definido' : 'Nao definido' ) . '</li>';
    echo '<li>Atributos: ' . esc_html( (string) count( $titulo_block->attributes ) ) . '</li>';
    echo '</ul>';
    echo '</div>';
}

function debug_titulo_icone_enable() {
    if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
        return;
    }

    add_action( 'admin_footer', 'debug_titulo_icone_editor' );
    add_action( 'wp_footer', 'debug_titulo_icone_frontend' );
    add_action( 'admin_notices', 'debug_show_registered_blocks' );
}
