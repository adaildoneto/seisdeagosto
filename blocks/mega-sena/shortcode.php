<?php
/**
 * Shortcode para Resultados das Loterias
 * Permite usar os resultados em qualquer lugar do WordPress
 * 
 * USO:
 * [loteria modalidade="megasena"]
 * [loteria modalidade="lotofacil" concurso="2500"]
 * [loteria modalidade="quina" mostrar_numeros="true" mostrar_premio="false"]
 */

// Evita acesso direto
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Carrega a API se ainda não foi carregada
if ( ! function_exists( 'seisdeagosto_get_loteria_result' ) ) {
    require_once __DIR__ . '/loteria-api.php';
}

/**
 * Shortcode principal [loteria]
 * 
 * Atributos:
 * - modalidade: megasena, lotofacil, quina, etc (padrão: megasena)
 * - concurso: número do concurso (opcional, pega o último se não informado)
 * - mostrar_numeros: true/false (padrão: true)
 * - mostrar_premio: true/false (padrão: true)
 * - mostrar_data: true/false (padrão: true)
 * - mostrar_concurso: true/false (padrão: true)
 * - cor_bola: cor hexadecimal (padrão: #209869)
 * - tamanho: mini, pequeno, normal, grande (padrão: normal)
 */
function seisdeagosto_loteria_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'modalidade' => 'megasena',
        'concurso' => null,
        'mostrar_numeros' => 'true',
        'mostrar_premio' => 'true',
        'mostrar_data' => 'true',
        'mostrar_concurso' => 'true',
        'cor_bola' => '#209869',
        'tamanho' => 'normal',
    ), $atts );

    // Converte strings boolean
    $mostrar_numeros = filter_var( $atts['mostrar_numeros'], FILTER_VALIDATE_BOOLEAN );
    $mostrar_premio = filter_var( $atts['mostrar_premio'], FILTER_VALIDATE_BOOLEAN );
    $mostrar_data = filter_var( $atts['mostrar_data'], FILTER_VALIDATE_BOOLEAN );
    $mostrar_concurso = filter_var( $atts['mostrar_concurso'], FILTER_VALIDATE_BOOLEAN );

    // Busca resultado
    $resultado = seisdeagosto_get_loteria_result( $atts['modalidade'], $atts['concurso'] );

    if ( ! $resultado || isset( $resultado['error'] ) ) {
        return '<div class="loteria-shortcode-error">Erro ao carregar resultado</div>';
    }

    // Define tamanhos
    $tamanhos = array(
        'mini' => '35px',
        'pequeno' => '45px',
        'normal' => '55px',
        'grande' => '70px',
    );
    $tamanho_bola = isset( $tamanhos[ $atts['tamanho'] ] ) ? $tamanhos[ $atts['tamanho'] ] : '55px';

    // Prepara dados
    $concurso = isset( $resultado['numero'] ) ? $resultado['numero'] : 'N/A';
    $data_sorteio = isset( $resultado['dataApuracao'] ) ? seisdeagosto_format_date( $resultado['dataApuracao'] ) : 'N/A';
    $numeros = isset( $resultado['listaDezenas'] ) ? $resultado['listaDezenas'] : array();

    // Prêmio
    $premio = 0;
    if ( isset( $resultado['listaRateioPremio'] ) && is_array( $resultado['listaRateioPremio'] ) ) {
        foreach ( $resultado['listaRateioPremio'] as $rateio ) {
            if ( isset( $rateio['faixa'] ) && $rateio['faixa'] == 1 ) {
                $premio = isset( $rateio['valorPremio'] ) ? $rateio['valorPremio'] : 0;
                break;
            }
        }
    }

    // Monta HTML
    ob_start();
    ?>
    <div class="loteria-shortcode" style="text-align: center; padding: 1rem;">
        
        <?php if ( $mostrar_concurso || $mostrar_data ) : ?>
        <div style="margin-bottom: 1rem; font-size: 0.9rem; color: #6c757d;">
            <?php if ( $mostrar_concurso ) : ?>
                <strong>Concurso:</strong> <?php echo esc_html( $concurso ); ?>
            <?php endif; ?>
            <?php if ( $mostrar_data ) : ?>
                <?php if ( $mostrar_concurso ) echo ' | '; ?>
                <strong>Data:</strong> <?php echo esc_html( $data_sorteio ); ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( $mostrar_numeros && ! empty( $numeros ) ) : ?>
        <div class="loteria-numeros" style="display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem; margin: 1rem 0;">
            <?php foreach ( $numeros as $numero ) : ?>
                <span class="loteria-ball-shortcode" style="
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: <?php echo $tamanho_bola; ?>;
                    height: <?php echo $tamanho_bola; ?>;
                    border-radius: 50%;
                    background-color: <?php echo esc_attr( $atts['cor_bola'] ); ?>;
                    color: #ffffff;
                    font-weight: bold;
                    font-size: calc(<?php echo $tamanho_bola; ?> * 0.4);
                    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                ">
                    <?php echo str_pad( $numero, 2, '0', STR_PAD_LEFT ); ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ( $mostrar_premio && $premio > 0 ) : ?>
        <div style="margin-top: 1rem; padding: 1rem; background: linear-gradient(135deg, <?php echo esc_attr( $atts['cor_bola'] ); ?> 0%, <?php echo esc_attr( adjustBrightness( $atts['cor_bola'], -20 ) ); ?> 100%); color: #fff; border-radius: 8px;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Prêmio</div>
            <div style="font-size: 1.5rem; font-weight: bold;"><?php echo seisdeagosto_format_currency( $premio ); ?></div>
        </div>
        <?php endif; ?>
        
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'loteria', 'seisdeagosto_loteria_shortcode' );

/**
 * Shortcode para listar todas as loterias
 * [loterias_lista layout="grid" colunas="3"]
 */
function seisdeagosto_loterias_lista_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'layout' => 'grid',
        'colunas' => '3',
        'modalidades' => '', // CSV de modalidades ou vazio para todas
    ), $atts );

    $loterias = seisdeagosto_get_all_loterias();

    if ( empty( $loterias ) ) {
        return '<div>Carregando resultados...</div>';
    }

    // Filtra modalidades se especificado
    if ( ! empty( $atts['modalidades'] ) ) {
        $modalidades_filtro = array_map( 'trim', explode( ',', $atts['modalidades'] ) );
        $loterias = array_filter( $loterias, function( $loteria ) use ( $modalidades_filtro ) {
            return in_array( $loteria['slug'], $modalidades_filtro );
        } );
    }

    ob_start();
    ?>
    <div class="loterias-lista-shortcode" style="display: grid; grid-template-columns: repeat(<?php echo intval( $atts['colunas'] ); ?>, 1fr); gap: 1.5rem; margin: 2rem 0;">
        <?php foreach ( $loterias as $loteria ) : ?>
            <div style="background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h4 style="margin: 0 0 1rem; color: #209869;"><?php echo esc_html( $loteria['nome'] ); ?></h4>
                <?php
                echo seisdeagosto_loteria_shortcode( array(
                    'modalidade' => $loteria['slug'],
                    'tamanho' => 'pequeno',
                    'mostrar_concurso' => 'false',
                    'mostrar_data' => 'false',
                ) );
                ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'loterias_lista', 'seisdeagosto_loterias_lista_shortcode' );

/**
 * Função auxiliar para ajustar brilho de cor
 */
function adjustBrightness( $hex, $steps ) {
    $hex = str_replace( '#', '', $hex );
    
    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );
    
    $r = max( 0, min( 255, $r + $steps ) );
    $g = max( 0, min( 255, $g + $steps ) );
    $b = max( 0, min( 255, $b + $steps ) );
    
    return '#' . str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT )
               . str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT )
               . str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );
}

/**
 * Exemplo de Widget (WordPress Legacy Widget)
 */
class Seisdeagosto_Loteria_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'seisdeagosto_loteria_widget',
            'Resultado Loteria',
            array( 'description' => 'Exibe resultado de uma loteria da Caixa' )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        
        $modalidade = ! empty( $instance['modalidade'] ) ? $instance['modalidade'] : 'megasena';
        
        echo seisdeagosto_loteria_shortcode( array(
            'modalidade' => $modalidade,
            'tamanho' => 'pequeno',
        ) );
        
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'Resultado Mega Sena';
        $modalidade = ! empty( $instance['modalidade'] ) ? $instance['modalidade'] : 'megasena';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">Título:</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" 
                   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" 
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'modalidade' ); ?>">Modalidade:</label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'modalidade' ); ?>" 
                    name="<?php echo $this->get_field_name( 'modalidade' ); ?>">
                <option value="megasena" <?php selected( $modalidade, 'megasena' ); ?>>Mega Sena</option>
                <option value="lotofacil" <?php selected( $modalidade, 'lotofacil' ); ?>>Lotofácil</option>
                <option value="quina" <?php selected( $modalidade, 'quina' ); ?>>Quina</option>
                <option value="lotomania" <?php selected( $modalidade, 'lotomania' ); ?>>Lotomania</option>
                <option value="timemania" <?php selected( $modalidade, 'timemania' ); ?>>Timemania</option>
                <option value="duplasena" <?php selected( $modalidade, 'duplasena' ); ?>>Dupla Sena</option>
            </select>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['modalidade'] = ( ! empty( $new_instance['modalidade'] ) ) ? strip_tags( $new_instance['modalidade'] ) : 'megasena';
        return $instance;
    }
}

// Registra o widget
function seisdeagosto_register_loteria_widget() {
    register_widget( 'Seisdeagosto_Loteria_Widget' );
}
add_action( 'widgets_init', 'seisdeagosto_register_loteria_widget' );
