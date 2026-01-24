<?php
/**
 * Debug Script for Título com Ícone Block
 * 
 * Add this to functions.php temporarily:
 * require_once get_template_directory() . '/debug-titulo-icone.php';
 */

// Add debug info to admin footer
add_action('admin_footer', 'debug_titulo_icone_editor');
function debug_titulo_icone_editor() {
    if (!current_user_can('manage_options')) return;
    
    ?>
    <script>
    console.log('=== DEBUG TÍTULO COM ÍCONE ===');
    
    // 1. Check WordPress APIs
    console.log('1. WordPress loaded:', typeof wp !== 'undefined' ? '✅' : '❌');
    if (typeof wp !== 'undefined') {
        console.log('   - wp.blocks:', typeof wp.blocks !== 'undefined' ? '✅' : '❌');
        console.log('   - wp.element:', typeof wp.element !== 'undefined' ? '✅' : '❌');
        console.log('   - wp.hooks:', typeof wp.hooks !== 'undefined' ? '✅' : '❌');
        console.log('   - wp.compose:', typeof wp.compose !== 'undefined' ? '✅' : '❌');
        console.log('   - wp.components:', typeof wp.components !== 'undefined' ? '✅' : '❌');
    }
    
    // 2. Check jQuery
    console.log('2. jQuery loaded:', typeof jQuery !== 'undefined' ? '✅ v' + jQuery.fn.jquery : '❌');
    
    // 3. Check Font Awesome
    const faTest = document.createElement('i');
    faTest.className = 'fa fa-star';
    faTest.style.cssText = 'position:absolute;left:-9999px';
    document.body.appendChild(faTest);
    const faStyles = window.getComputedStyle(faTest);
    const faLoaded = faStyles.fontFamily && faStyles.fontFamily.includes('FontAwesome');
    document.body.removeChild(faTest);
    console.log('3. Font Awesome loaded:', faLoaded ? '✅' : '❌');
    
    // 4. Check seideagostoBlocks variable
    console.log('4. seideagostoBlocks:', window.seideagostoBlocks ? '✅' : '❌');
    if (window.seideagostoBlocks) {
        console.log('   - ajaxUrl:', window.seideagostoBlocks.ajaxUrl);
        console.log('   - nonce:', window.seideagostoBlocks.nonce ? '✅' : '❌');
    }
    
    // 5. Check if block is registered
    if (typeof wp !== 'undefined' && wp.blocks) {
        const block = wp.blocks.getBlockType('seisdeagosto/titulo-com-icone');
        console.log('5. Block registered:', block ? '✅' : '❌');
        if (block) {
            console.log('   - Name:', block.name);
            console.log('   - Title:', block.title);
            console.log('   - Category:', block.category);
            console.log('   - Attributes:', Object.keys(block.attributes || {}).join(', '));
            console.log('   - Edit function:', typeof block.edit);
            console.log('   - Save function:', typeof block.save);
        }
    }
    
    // 6. Check if editor script loaded
    const editorScript = document.querySelector('script[src*="titulo-com-icone/editor.js"]');
    console.log('6. Editor script tag:', editorScript ? '✅' : '❌');
    if (editorScript) {
        console.log('   - src:', editorScript.src);
    }
    
    // 7. Test AJAX endpoint
    if (window.seideagostoBlocks && typeof jQuery !== 'undefined') {
        console.log('7. Testing AJAX endpoint...');
        jQuery.ajax({
            url: window.seideagostoBlocks.ajaxUrl,
            type: 'POST',
            data: { action: 'get_fontawesome_icons' },
            success: function(response) {
                if (response && response.success && response.data && response.data.icons) {
                    console.log('   ✅ AJAX working! Icons:', response.data.icons.length);
                    console.log('   First 3 icons:', response.data.icons.slice(0, 3));
                } else {
                    console.log('   ⚠️ Unexpected response:', response);
                }
            },
            error: function(xhr, status, error) {
                console.log('   ❌ AJAX failed:', status, error);
                console.log('   Response:', xhr.responseText);
            }
        });
    }
    
    // 8. Check for filters
    if (typeof wp !== 'undefined' && wp.hooks) {
        console.log('8. Checking hooks...');
        const filters = wp.hooks.filters || {};
        const editorFilters = filters['editor.BlockEdit'] || [];
        console.log('   - editor.BlockEdit filters:', editorFilters.length);
        if (editorFilters.length > 0) {
            editorFilters.forEach(function(filter) {
                console.log('     -', filter.namespace || 'unknown');
            });
        }
    }
    
    console.log('=== FIM DEBUG ===');
    </script>
    <?php
}

// Add debug info to frontend
add_action('wp_footer', 'debug_titulo_icone_frontend');
function debug_titulo_icone_frontend() {
    if (!current_user_can('manage_options')) return;
    
    ?>
    <script>
    console.log('=== DEBUG FRONTEND - TÍTULO COM ÍCONE ===');
    
    // Check Font Awesome
    const faTest = document.createElement('i');
    faTest.className = 'fa fa-star';
    faTest.style.cssText = 'position:absolute;left:-9999px;font-size:24px;';
    document.body.appendChild(faTest);
    const faStyles = window.getComputedStyle(faTest);
    const faLoaded = faStyles.fontFamily && faStyles.fontFamily.includes('FontAwesome');
    console.log('1. Font Awesome loaded:', faLoaded ? '✅' : '❌');
    console.log('   - Font family:', faStyles.fontFamily);
    document.body.removeChild(faTest);
    
    // Check for titulo-com-icone blocks in page
    const tituloBlocks = document.querySelectorAll('.titulo-com-icone-wrapper');
    console.log('2. Titulo-com-icone blocks found:', tituloBlocks.length);
    
    tituloBlocks.forEach(function(block, index) {
        console.log('   Block', index + 1 + ':');
        const icon = block.querySelector('.titulo-com-icone-icon i');
        const title = block.querySelector('.titulo-com-icone-titulo');
        const line = block.querySelector('.titulo-com-icone-line');
        
        if (icon) {
            console.log('     - Icon class:', icon.className);
            console.log('     - Icon color:', window.getComputedStyle(icon).color);
        } else {
            console.log('     - ❌ No icon found');
        }
        
        if (title) {
            console.log('     - Title:', title.textContent);
            console.log('     - Font size:', window.getComputedStyle(title).fontSize);
        } else {
            console.log('     - ❌ No title found');
        }
        
        if (line) {
            console.log('     - Line color:', window.getComputedStyle(line).backgroundColor);
        } else {
            console.log('     - ❌ No line found');
        }
    });
    
    // Check loaded stylesheets
    const stylesheets = document.querySelectorAll('link[rel="stylesheet"]');
    const faStylesheet = Array.from(stylesheets).find(function(link) {
        return link.href.includes('font-awesome');
    });
    console.log('3. Font Awesome stylesheet:', faStylesheet ? '✅' : '❌');
    if (faStylesheet) {
        console.log('   - URL:', faStylesheet.href);
    }
    
    console.log('=== FIM DEBUG FRONTEND ===');
    </script>
    <?php
}

// Show registered blocks in admin
add_action('admin_notices', 'debug_show_registered_blocks');
function debug_show_registered_blocks() {
    if (!current_user_can('manage_options')) return;
    
    $screen = get_current_screen();
    if (!$screen || $screen->base !== 'post') return;
    
    $registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
    $titulo_block = null;
    
    foreach ($registered_blocks as $name => $block) {
        if ($name === 'seisdeagosto/titulo-com-icone') {
            $titulo_block = $block;
            break;
        }
    }
    
    if (!$titulo_block) {
        echo '<div class="notice notice-error"><p><strong>❌ Bloco "seisdeagosto/titulo-com-icone" NÃO está registrado!</strong></p></div>';
        return;
    }
    
    echo '<div class="notice notice-success is-dismissible">';
    echo '<p><strong>✅ Bloco "Título com Ícone" está registrado</strong></p>';
    echo '<ul style="list-style-type: disc; margin-left: 20px;">';
    echo '<li>Nome: ' . esc_html($titulo_block->name) . '</li>';
    echo '<li>Título: ' . esc_html($titulo_block->title) . '</li>';
    echo '<li>Categoria: ' . esc_html($titulo_block->category) . '</li>';
    echo '<li>Render callback: ' . (isset($titulo_block->render_callback) && is_callable($titulo_block->render_callback) ? '✅ Definido' : '❌ Não definido') . '</li>';
    
    if (isset($titulo_block->render_callback)) {
        if (is_string($titulo_block->render_callback)) {
            echo '<li>Função: ' . esc_html($titulo_block->render_callback) . '</li>';
            echo '<li>Função existe: ' . (function_exists($titulo_block->render_callback) ? '✅' : '❌') . '</li>';
        }
    }
    
    echo '<li>Atributos: ' . count($titulo_block->attributes) . '</li>';
    echo '</ul>';
    echo '</div>';
}
