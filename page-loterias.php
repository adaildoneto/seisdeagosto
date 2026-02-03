<?php
/**
 * Template Name: Resultados das Loterias
 * Description: Página para exibir todos os resultados dos jogos da Caixa
 */

get_header();

// Carrega a API
require_once get_template_directory() . '/blocks/mega-sena/loteria-api.php';

// Busca todas as loterias
$loterias = seisdeagosto_get_all_loterias();
?>

<style>
/* Estilos específicos da página */
.loterias-page {
    padding: 2rem 0;
}

.loterias-header {
    text-align: center;
    padding: 3rem 1rem;
    background: linear-gradient(135deg, #209869 0%, #1a7a53 100%);
    color: #ffffff;
    margin-bottom: 3rem;
    border-radius: 10px;
}

.loterias-header h1 {
    margin: 0;
    font-size: 2.5rem;
    font-weight: 700;
}

.loterias-header p {
    margin: 1rem 0 0;
    font-size: 1.125rem;
    opacity: 0.9;
}

.loteria-card {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 2rem;
    margin-bottom: 2rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.loteria-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.loteria-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
}

.loteria-title h2 {
    margin: 0;
    font-size: 1.75rem;
    color: #209869;
    font-weight: 700;
}

.loteria-title .icon {
    font-size: 2rem;
    color: #209869;
}

.loteria-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.loteria-info-item {
    text-align: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
}

.loteria-info-item .label {
    font-size: 0.875rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.loteria-info-item .value {
    font-size: 1.125rem;
    font-weight: 700;
    color: #209869;
}

.loteria-numbers {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin: 1.5rem 0;
}

.loteria-ball {
    min-width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 700;
    color: #ffffff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    padding: 0 0.5rem;
}

.loteria-ball.megasena { background-color: #209869; }
.loteria-ball.lotofacil { background-color: #930089; }
.loteria-ball.quina { background-color: #260085; }
.loteria-ball.lotomania { background-color: #F78100; }
.loteria-ball.timemania { background-color: #00FF48; color: #333; }
.loteria-ball.duplasena { background-color: #A61324; }
.loteria-ball.federal { background-color: #103099; }
.loteria-ball.loteca { background-color: #E53237; }
.loteria-ball.diadesorte { background-color: #CB852B; }
.loteria-ball.supersete { background-color: #A8CF45; color: #333; }
.loteria-ball.maismilionaria { background-color: #171C61; }

.loteria-premio {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #209869 0%, #1a7a53 100%);
    border-radius: 8px;
    color: #ffffff;
    margin-top: 1.5rem;
}

.loteria-premio .label {
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.loteria-premio .value {
    font-size: 1.75rem;
    font-weight: 700;
}

.loteria-rateio {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #dee2e6;
}

.loteria-rateio h4 {
    font-size: 1rem;
    color: #6c757d;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.rateio-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 0.5rem;
}

.rateio-item .faixa {
    font-weight: 600;
    color: #333;
}

.rateio-item .ganhadores {
    color: #6c757d;
    font-size: 0.875rem;
}

.rateio-item .valor {
    font-weight: 700;
    color: #209869;
}

.loading-state {
    text-align: center;
    padding: 5rem 1rem;
}

.loading-state i {
    font-size: 3rem;
    color: #209869;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .loterias-header h1 {
        font-size: 2rem;
    }
    
    .loteria-ball {
        min-width: 45px;
        height: 45px;
        font-size: 1.125rem;
    }
    
    .loteria-title h2 {
        font-size: 1.5rem;
    }
}
</style>

<div class="loterias-page">
    <div class="container">
        
        <div class="loterias-header">
            <h1>
                <i class="fas fa-trophy"></i>
                Resultados das Loterias Caixa
            </h1>
            <p>Confira os últimos resultados de todas as loterias da Caixa Econômica Federal</p>
            <p style="font-size: 0.875rem; margin-top: 1rem;">
                <i class="fas fa-sync-alt"></i>
                Atualizado automaticamente a cada 30 minutos
            </p>
        </div>

        <?php if ( empty( $loterias ) ) : ?>
            <div class="loading-state">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Carregando resultados...</p>
            </div>
        <?php else : ?>
            
            <div class="row">
                <?php foreach ( $loterias as $loteria ) : 
                    $dados = $loteria['dados'];
                    $slug = $loteria['slug'];
                    $nome = $loteria['nome'];
                    
                    $concurso = isset( $dados['numero'] ) ? $dados['numero'] : 'N/A';
                    $data_sorteio = isset( $dados['dataApuracao'] ) ? seisdeagosto_format_date( $dados['dataApuracao'] ) : 'N/A';
                    $numeros = isset( $dados['listaDezenas'] ) ? $dados['listaDezenas'] : array();
                    
                    // Prêmio principal
                    $premio_principal = 0;
                    $acumulado = false;
                    if ( isset( $dados['listaRateioPremio'] ) && is_array( $dados['listaRateioPremio'] ) ) {
                        foreach ( $dados['listaRateioPremio'] as $rateio ) {
                            if ( isset( $rateio['faixa'] ) && $rateio['faixa'] == 1 ) {
                                $premio_principal = isset( $rateio['valorPremio'] ) ? $rateio['valorPremio'] : 0;
                                $acumulado = isset( $rateio['numeroDeGanhadores'] ) && $rateio['numeroDeGanhadores'] == 0;
                                break;
                            }
                        }
                    }
                    
                    // Valor acumulado
                    $valor_acumulado = isset( $dados['valorAcumuladoConcursoEspecial'] ) ? $dados['valorAcumuladoConcursoEspecial'] : 
                                      ( isset( $dados['valorAcumuladoProximoConcurso'] ) ? $dados['valorAcumuladoProximoConcurso'] : 0 );
                ?>
                
                <div class="col-lg-6">
                    <div class="loteria-card">
                        
                        <div class="loteria-title">
                            <span class="icon">
                                <i class="fas fa-clover"></i>
                            </span>
                            <h2><?php echo esc_html( $nome ); ?></h2>
                        </div>

                        <div class="loteria-info">
                            <div class="loteria-info-item">
                                <div class="label">
                                    <i class="fas fa-hashtag"></i>
                                    Concurso
                                </div>
                                <div class="value"><?php echo esc_html( $concurso ); ?></div>
                            </div>
                            <div class="loteria-info-item">
                                <div class="label">
                                    <i class="fas fa-calendar-alt"></i>
                                    Data
                                </div>
                                <div class="value"><?php echo esc_html( $data_sorteio ); ?></div>
                            </div>
                        </div>

                        <?php if ( ! empty( $numeros ) ) : ?>
                        <div class="loteria-numbers">
                            <?php foreach ( $numeros as $numero ) : ?>
                                <div class="loteria-ball <?php echo esc_attr( $slug ); ?>">
                                    <?php echo str_pad( $numero, 2, '0', STR_PAD_LEFT ); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ( $acumulado && $valor_acumulado > 0 ) : ?>
                        <div class="loteria-premio">
                            <div class="label">
                                <i class="fas fa-exclamation-circle"></i>
                                Acumulado
                            </div>
                            <div class="value"><?php echo seisdeagosto_format_currency( $valor_acumulado ); ?></div>
                        </div>
                        <?php elseif ( $premio_principal > 0 ) : ?>
                        <div class="loteria-premio">
                            <div class="label">
                                <i class="fas fa-money-bill-wave"></i>
                                Prêmio Principal
                            </div>
                            <div class="value"><?php echo seisdeagosto_format_currency( $premio_principal ); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ( isset( $dados['listaRateioPremio'] ) && is_array( $dados['listaRateioPremio'] ) && count( $dados['listaRateioPremio'] ) > 0 ) : ?>
                        <div class="loteria-rateio">
                            <h4><i class="fas fa-award"></i> Premiação por Faixa</h4>
                            <?php foreach ( $dados['listaRateioPremio'] as $rateio ) : 
                                $faixa = isset( $rateio['descricaoFaixa'] ) ? $rateio['descricaoFaixa'] : '';
                                $ganhadores = isset( $rateio['numeroDeGanhadores'] ) ? $rateio['numeroDeGanhadores'] : 0;
                                $valor = isset( $rateio['valorPremio'] ) ? $rateio['valorPremio'] : 0;
                            ?>
                                <div class="rateio-item">
                                    <div>
                                        <div class="faixa"><?php echo esc_html( $faixa ); ?></div>
                                        <div class="ganhadores">
                                            <?php echo esc_html( $ganhadores ); ?> 
                                            <?php echo $ganhadores == 1 ? 'ganhador' : 'ganhadores'; ?>
                                        </div>
                                    </div>
                                    <div class="valor">
                                        <?php echo seisdeagosto_format_currency( $valor ); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
                
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <p class="text-muted">
                <i class="fas fa-info-circle"></i>
                Dados fornecidos pela API oficial da Caixa Econômica Federal
            </p>
        </div>
        
    </div>
</div>

<?php
get_footer();
