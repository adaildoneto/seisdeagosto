<?php
/**
 * Render callback for Título com Ícone block
 */
function seisdeagosto_render_titulo_com_icone( $attributes ) {
    $titulo = isset( $attributes['titulo'] ) ? sanitize_text_field( $attributes['titulo'] ) : 'CTA';
    $icone = isset( $attributes['icone'] ) ? sanitize_text_field( $attributes['icone'] ) : 'fa-star';
    $mostrarIcone = isset( $attributes['mostrarIcone'] ) ? (bool) $attributes['mostrarIcone'] : true;
    $corIcone = isset( $attributes['corIcone'] ) ? sanitize_hex_color( $attributes['corIcone'] ) : '';
    $corLinha = isset( $attributes['corLinha'] ) ? sanitize_hex_color( $attributes['corLinha'] ) : '';
    $tamanhoIcone = isset( $attributes['tamanhoIcone'] ) ? intval( $attributes['tamanhoIcone'] ) : 24;
    $tamanhoTitulo = isset( $attributes['tamanhoTitulo'] ) ? intval( $attributes['tamanhoTitulo'] ) : 28;
    $espessuraLinha = isset( $attributes['espessuraLinha'] ) ? intval( $attributes['espessuraLinha'] ) : 3;
    $alinhamento = isset( $attributes['alinhamento'] ) ? sanitize_text_field( $attributes['alinhamento'] ) : 'left';

    // Fallbacks para cores do tema
    if ( empty( $corIcone ) ) {
        $corIcone = get_theme_mod( 'u_correio68_primary_color', '#0a4579' );
    }
    if ( empty( $corLinha ) ) {
        $corLinha = get_theme_mod( 'u_correio68_primary_color', '#0a4579' );
    }

    // Normalizar classes do ícone (Font Awesome 4/5/6)
    $icone = trim( $icone );
    if ( $icone === '' ) {
        $icone = 'fa-star';
    }
    $has_prefix = preg_match( '/(^|\s)(fa|fas|far|fal|fab|fad)\s/', $icone );
    if ( ! $has_prefix && strpos( $icone, 'fa-' ) === 0 ) {
        $icone = 'fa ' . $icone;
    }

    // Mapear alinhamento para align-items e text-align
    $align_class = 'align-items-' . ( $alinhamento === 'center' ? 'center' : ( $alinhamento === 'right' ? 'end' : 'start' ) );
    $text_align = $alinhamento === 'center' ? 'text-center' : ( $alinhamento === 'right' ? 'text-right' : 'text-left' );

    // Gerar ID único para o estilo inline
    $unique_id = 'titulo-icone-' . wp_rand( 1000, 9999 );

    ob_start();
    ?>
    <div class="titulo-com-icone-wrapper d-flex <?php echo esc_attr( $align_class ); ?> py-3" id="<?php echo esc_attr( $unique_id ); ?>" style="gap:12px;">
        <!-- Ícone à esquerda -->
        <?php if ( $mostrarIcone ) : ?>
            <div class="titulo-com-icone-icon" style="flex-shrink: 0;">
                <i class="<?php echo esc_attr( $icone ); ?>" style="font-size: <?php echo intval( $tamanhoIcone ); ?>px; color: <?php echo esc_attr( $corIcone ); ?>;"></i>
            </div>
        <?php endif; ?>

        <!-- Título com linha animada -->
        <div class="titulo-com-icone-content">
            <div class="titulo-com-icone-line-wrapper" style="position: relative; display: inline-block;">
                <h3 class="titulo-com-icone-titulo m-0" style="font-size: <?php echo intval( $tamanhoTitulo ); ?>px; font-weight: 700; margin: 0;">
                    <?php echo esc_html( $titulo ); ?>
                </h3>
                <!-- Linha animada -->
                <div class="titulo-com-icone-line" style="
                    position: absolute;
                    bottom: -8px;
                    left: 0;
                    height: <?php echo intval( $espessuraLinha ); ?>px;
                    background-color: <?php echo esc_attr( $corLinha ); ?>;
                    width: 80%;
                "></div>
            </div>
        </div>
    </div>

    <style>
        #<?php echo esc_attr( $unique_id ); ?> .titulo-com-icone-line {
            transform: scaleX(0);
            transform-origin: left center;
            opacity: 0.7;
            transition: transform 0.35s ease, opacity 0.35s ease;
        }
        #<?php echo esc_attr( $unique_id ); ?> .titulo-com-icone-line-wrapper:hover .titulo-com-icone-line,
        #<?php echo esc_attr( $unique_id ); ?> .titulo-com-icone-wrapper:hover .titulo-com-icone-line {
            transform: scaleX(1);
            opacity: 1;
        }
    </style>
    <?php
    return ob_get_clean();
}
