<?php
/**
 * Render callback for Mega Sena Block
 * Exibe resultados da Mega Sena via API da Caixa
 */

// Carrega o API handler (apenas uma vez)
if ( ! function_exists( 'seisdeagosto_get_loteria_result' ) ) {
    require_once __DIR__ . '/loteria-api.php';
}

/**
 * Renderiza o bloco Mega Sena
 */
function seisdeagosto_render_mega_sena_block( $attributes ) {
    // Enqueue FontAwesome
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        array(),
        '6.4.0'
    );

    // Enqueue Bootstrap JS (necessário para accordion e dropdown)
    wp_enqueue_script(
        'bootstrap-bundle',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
        array(),
        '5.3.0',
        true
    );

    // Enqueue frontend script
    wp_enqueue_script(
        'mega-sena-frontend',
        get_template_directory_uri() . '/blocks/mega-sena/frontend.js',
        array('bootstrap-bundle'),
        filemtime( __DIR__ . '/frontend.js' ),
        true
    );
    
    $title = isset( $attributes['title'] ) ? esc_html( $attributes['title'] ) : 'Resultado Mega Sena';
    $show_concurso = isset( $attributes['showConcurso'] ) ? $attributes['showConcurso'] : true;
    $show_data = isset( $attributes['showData'] ) ? $attributes['showData'] : true;
    $show_premio = isset( $attributes['showPremio'] ) ? $attributes['showPremio'] : true;
    $show_proximo = isset( $attributes['showProximoConcurso'] ) ? $attributes['showProximoConcurso'] : true;
    $show_anterior = isset( $attributes['showConcursoAnterior'] ) ? $attributes['showConcursoAnterior'] : false;
    $show_menu = isset( $attributes['showMenuJogos'] ) ? $attributes['showMenuJogos'] : false;
    $jogo = isset( $attributes['jogoSelecionado'] ) ? $attributes['jogoSelecionado'] : 'megasena';
    $bg_color = isset( $attributes['backgroundColor'] ) ? esc_attr( $attributes['backgroundColor'] ) : '#ffffff';
    $text_color = isset( $attributes['textColor'] ) ? esc_attr( $attributes['textColor'] ) : '#333333';
    $ball_color = isset( $attributes['ballColor'] ) ? esc_attr( $attributes['ballColor'] ) : '#209869';

    // Busca o resultado da API
    $resultado = seisdeagosto_get_loteria_result( $jogo );

    if ( ! $resultado || isset( $resultado['error'] ) ) {
        return sprintf(
            '<div class="wp-block-seisdeagosto-mega-sena mega-sena-error" style="background-color: %s; color: %s;">
                <i class="fas fa-exclamation-triangle"></i>
                <p><strong>Erro ao carregar resultado</strong></p>
                <p>%s</p>
            </div>',
            esc_attr( $bg_color ),
            esc_attr( $text_color ),
            isset( $resultado['error'] ) ? esc_html( $resultado['error'] ) : 'Não foi possível carregar os dados da API.'
        );
    }

    // Prepara os dados
    $concurso = isset( $resultado['numero'] ) ? $resultado['numero'] : 'N/A';
    
    // Formata a data corretamente
    $data_sorteio = 'N/A';
    if ( isset( $resultado['dataApuracao'] ) && ! empty( $resultado['dataApuracao'] ) ) {
        $data_original = $resultado['dataApuracao'];
        
        // Usa a função helper que já está otimizada
        $data_sorteio = seisdeagosto_format_date( $data_original );
    }
    
    // Números sorteados
    $numeros = isset( $resultado['listaDezenas'] ) ? $resultado['listaDezenas'] : array();
    
    // Prêmio (sena)
    $premio = 0;
    if ( isset( $resultado['listaRateioPremio'] ) && is_array( $resultado['listaRateioPremio'] ) ) {
        foreach ( $resultado['listaRateioPremio'] as $rateio ) {
            if ( isset( $rateio['descricaoFaixa'] ) && strpos( strtolower( $rateio['descricaoFaixa'] ), 'sena' ) !== false ) {
                $premio = isset( $rateio['valorPremio'] ) ? $rateio['valorPremio'] : 0;
                break;
            }
        }
    }
    $premio_formatado = seisdeagosto_format_currency_short( $premio );
    
    // Próximo concurso
    $proximo_concurso = isset( $resultado['numeroConcursoProximo'] ) ? $resultado['numeroConcursoProximo'] : '';
    
    // Formata a data do próximo concurso
    $data_proximo = '';
    if ( isset( $resultado['dataProximoConcurso'] ) && ! empty( $resultado['dataProximoConcurso'] ) ) {
        $data_proximo = seisdeagosto_format_date( $resultado['dataProximoConcurso'] );
    }
    
    $valor_estimado = isset( $resultado['valorEstimadoProximoConcurso'] ) ? $resultado['valorEstimadoProximoConcurso'] : 0;
    $valor_estimado_formatado = seisdeagosto_format_currency_short( $valor_estimado );
    
    // Busca o concurso anterior se necessário
    $resultado_anterior = null;
    if ( $show_anterior && isset( $resultado['numero'] ) && $resultado['numero'] > 1 ) {
        $concurso_anterior_num = $resultado['numero'] - 1;
        $resultado_anterior = seisdeagosto_get_loteria_result( $jogo, $concurso_anterior_num );
    }

    // Lista de jogos disponíveis
    $jogos_disponiveis = array(
        'megasena' => 'Mega-Sena',
        'lotofacil' => 'Lotofácil',
        'quina' => 'Quina',
        'lotomania' => 'Lotomania',
        'timemania' => 'Timemania',
        'duplasena' => 'Dupla Sena',
        'diadesorte' => 'Dia de Sorte',
        'supersete' => 'Super Sete',
        'federal' => 'Federal',
        'loteca' => 'Loteca',
        'maismilionaria' => '+Milionária'
    );

    // Monta o HTML
    ob_start();
    $block_id = 'mega-sena-' . uniqid();
    ?>
    <div class="wp-block-seisdeagosto-mega-sena" style="background-color: <?php echo $bg_color; ?>; color: <?php echo $text_color; ?>;">
        
        <?php if ( $show_menu ) : ?>
        <div class="mega-sena-menu-jogos">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownJogos<?php echo $block_id; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-dice"></i>
                    <?php echo isset( $jogos_disponiveis[$jogo] ) ? $jogos_disponiveis[$jogo] : 'Selecione o Jogo'; ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownJogos<?php echo $block_id; ?>">
                    <?php foreach ( $jogos_disponiveis as $slug => $nome ) : ?>
                        <li><a class="dropdown-item <?php echo $slug === $jogo ? 'active' : ''; ?>" href="?jogo=<?php echo $slug; ?>"><?php echo $nome; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mega-sena-header">
            <h2>
                <i class="fas fa-trophy"></i>
                <?php echo $title; ?>
            </h2>
        </div>

        <?php if ( $show_concurso || $show_data ) : ?>
        <div class="mega-sena-info">
            <?php if ( $show_concurso ) : ?>
            <div class="mega-sena-info-item">
                <div class="label">
                    <i class="fas fa-hashtag"></i>
                    Concurso
                </div>
                <div class="value"><?php echo esc_html( $concurso ); ?></div>
            </div>
            <?php endif; ?>

            <?php if ( $show_data ) : ?>
            <div class="mega-sena-info-item">
                <div class="label">
                    <i class="fas fa-calendar-alt"></i>
                    Data do Sorteio
                </div>
                <div class="value"><?php echo esc_html( $data_sorteio ); ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="mega-sena-numbers">
            <?php foreach ( $numeros as $numero ) : ?>
                <div class="mega-sena-ball" style="background-color: <?php echo $ball_color; ?>;">
                    <?php echo str_pad( $numero, 2, '0', STR_PAD_LEFT ); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ( $show_premio && $premio > 0 ) : ?>
        <div class="mega-sena-premio">
            <div class="label">
                <i class="fas fa-money-bill-wave"></i>
                Prêmio Total
            </div>
            <div class="value"><?php echo esc_html( $premio_formatado ); ?></div>
        </div>
        <?php endif; ?>

        <?php if ( $show_proximo && $proximo_concurso ) : ?>
        <div class="mega-sena-proximo">
            <div class="label">
                <i class="fas fa-clock"></i>
                Próximo Concurso: <?php echo esc_html( $proximo_concurso ); ?> - <?php echo esc_html( $data_proximo ); ?>
            </div>
            <div class="value">
                Estimativa: <?php echo esc_html( $valor_estimado_formatado ); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ( $show_anterior && $resultado_anterior && ! isset( $resultado_anterior['error'] ) ) : ?>
        <div class="mega-sena-accordion accordion mt-3" id="accordionAnterior<?php echo $block_id; ?>">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingAnterior<?php echo $block_id; ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAnterior<?php echo $block_id; ?>" aria-expanded="false" aria-controls="collapseAnterior<?php echo $block_id; ?>">
                        <i class="fas fa-history me-2"></i>
                        Concurso Anterior <?php echo isset( $resultado_anterior['numero'] ) ? '(' . $resultado_anterior['numero'] . ')' : ''; ?>
                    </button>
                </h2>
                <div id="collapseAnterior<?php echo $block_id; ?>" class="accordion-collapse collapse" aria-labelledby="headingAnterior<?php echo $block_id; ?>" data-bs-parent="#accordionAnterior<?php echo $block_id; ?>">
                    <div class="accordion-body">
                        <?php 
                        $data_anterior = isset( $resultado_anterior['dataApuracao'] ) ? seisdeagosto_format_date( $resultado_anterior['dataApuracao'] ) : 'N/A';
                        $numeros_anterior = isset( $resultado_anterior['listaDezenas'] ) ? $resultado_anterior['listaDezenas'] : array();
                        ?>
                        
                        <div class="mega-sena-info mb-3">
                            <div class="mega-sena-info-item">
                                <div class="label">
                                    <i class="fas fa-calendar-alt"></i>
                                    Data do Sorteio
                                </div>
                                <div class="value"><?php echo esc_html( $data_anterior ); ?></div>
                            </div>
                        </div>
                        
                        <div class="mega-sena-numbers">
                            <?php foreach ( $numeros_anterior as $numero ) : ?>
                                <div class="mega-sena-ball" style="background-color: <?php echo $ball_color; ?>;">
                                    <?php echo str_pad( $numero, 2, '0', STR_PAD_LEFT ); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php 
                        $premio_anterior = 0;
                        if ( isset( $resultado_anterior['listaRateioPremio'] ) && is_array( $resultado_anterior['listaRateioPremio'] ) ) {
                            foreach ( $resultado_anterior['listaRateioPremio'] as $rateio ) {
                                if ( isset( $rateio['descricaoFaixa'] ) && strpos( strtolower( $rateio['descricaoFaixa'] ), 'sena' ) !== false ) {
                                    $premio_anterior = isset( $rateio['valorPremio'] ) ? $rateio['valorPremio'] : 0;
                                    break;
                                }
                            }
                        }
                        if ( $premio_anterior > 0 ) :
                        ?>
                        <div class="mega-sena-premio mt-3">
                            <div class="label">
                                <i class="fas fa-money-bill-wave"></i>
                                Prêmio Total
                            </div>
                            <div class="value"><?php echo esc_html( seisdeagosto_format_currency_short( $premio_anterior ) ); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
    <?php
    return ob_get_clean();
}
