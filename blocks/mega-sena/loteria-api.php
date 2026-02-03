<?php
/**
 * API Handler para Loterias da Caixa
 * Consulta os resultados via API oficial da Caixa Econômica Federal
 * 
 * API Documentation: https://servicebus2.caixa.gov.br/portaldeloterias/api/home/
 * Endpoint: https://servicebus2.caixa.gov.br/portaldeloterias/api/{modalidade}/
 */

/**
 * Busca o resultado de uma loteria específica
 * 
 * @param string $modalidade Nome da modalidade (megasena, lotofacil, quina, etc)
 * @param int $concurso Número do concurso (opcional, se não informado pega o último)
 * @return array|false Dados do concurso ou false em caso de erro
 */
function seisdeagosto_get_loteria_result( $modalidade = 'megasena', $concurso = null ) {
    // Cache key
    $cache_key = 'loteria_' . $modalidade;
    if ( $concurso ) {
        $cache_key .= '_' . $concurso;
    }
    
    // Verifica cache (30 minutos)
    $cached_data = get_transient( $cache_key );
    if ( false !== $cached_data ) {
        return $cached_data;
    }
    
    // Monta a URL da API
    $url = 'https://servicebus2.caixa.gov.br/portaldeloterias/api/' . $modalidade;
    if ( $concurso ) {
        $url .= '/' . $concurso;
    }
    
    // Faz a requisição
    $response = wp_remote_get( $url, array(
        'timeout' => 15,
        'headers' => array(
            'Accept' => 'application/json',
        ),
    ) );
    
    // Verifica erros
    if ( is_wp_error( $response ) ) {
        error_log( '[Loteria API] Erro: ' . $response->get_error_message() );
        return array( 'error' => 'Erro ao conectar com a API: ' . $response->get_error_message() );
    }
    
    $status_code = wp_remote_retrieve_response_code( $response );
    if ( $status_code !== 200 ) {
        error_log( '[Loteria API] Status code: ' . $status_code );
        return array( 'error' => 'API retornou status ' . $status_code );
    }
    
    // Decodifica a resposta
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        error_log( '[Loteria API] Erro ao decodificar JSON: ' . json_last_error_msg() );
        return array( 'error' => 'Erro ao processar dados da API' );
    }
    
    // Salva no cache por 30 minutos
    set_transient( $cache_key, $data, 30 * MINUTE_IN_SECONDS );
    
    return $data;
}

/**
 * Busca todas as modalidades disponíveis
 * 
 * @return array Lista de modalidades com seus últimos resultados
 */
function seisdeagosto_get_all_loterias() {
    // Cache key
    $cache_key = 'todas_loterias';
    
    // Verifica cache (30 minutos)
    $cached_data = get_transient( $cache_key );
    if ( false !== $cached_data ) {
        return $cached_data;
    }
    
    // Lista de modalidades disponíveis
    $modalidades = array(
        'megasena' => 'Mega Sena',
        'lotofacil' => 'Lotofácil',
        'quina' => 'Quina',
        'lotomania' => 'Lotomania',
        'timemania' => 'Timemania',
        'duplasena' => 'Dupla Sena',
        'federal' => 'Federal',
        'loteca' => 'Loteca',
        'diadesorte' => 'Dia de Sorte',
        'supersete' => 'Super Sete',
        'maismilionaria' => '+Milionária',
    );
    
    $resultados = array();
    
    foreach ( $modalidades as $slug => $nome ) {
        $resultado = seisdeagosto_get_loteria_result( $slug );
        if ( $resultado && ! isset( $resultado['error'] ) ) {
            $resultados[] = array(
                'slug' => $slug,
                'nome' => $nome,
                'dados' => $resultado,
            );
        }
    }
    
    // Salva no cache por 30 minutos
    set_transient( $cache_key, $resultados, 30 * MINUTE_IN_SECONDS );
    
    return $resultados;
}

/**
 * Limpa o cache de uma modalidade específica
 * 
 * @param string $modalidade Nome da modalidade
 */
function seisdeagosto_clear_loteria_cache( $modalidade = null ) {
    if ( $modalidade ) {
        delete_transient( 'loteria_' . $modalidade );
    } else {
        // Limpa todos os caches
        delete_transient( 'todas_loterias' );
        $modalidades = array(
            'megasena', 'lotofacil', 'quina', 'lotomania', 'timemania',
            'duplasena', 'federal', 'loteca', 'diadesorte', 'supersete', 'maismilionaria'
        );
        foreach ( $modalidades as $mod ) {
            delete_transient( 'loteria_' . $mod );
        }
    }
}

/**
 * Formata valor monetário
 * 
 * @param float $valor Valor a ser formatado
 * @return string Valor formatado
 */
function seisdeagosto_format_currency( $valor ) {
    return 'R$ ' . number_format( $valor, 2, ',', '.' );
}

/**
 * Formata valor monetário de forma simplificada (milhões)
 * 
 * @param float $valor Valor a ser formatado
 * @return string Valor formatado
 */
function seisdeagosto_format_currency_short( $valor ) {
    if ( $valor >= 1000000 ) {
        $milhoes = $valor / 1000000;
        if ( $milhoes >= 100 ) {
            return 'R$ ' . number_format( $milhoes, 0, ',', '.' ) . ' milhões';
        }
        return 'R$ ' . number_format( $milhoes, 1, ',', '.' ) . ' milhões';
    } elseif ( $valor >= 1000 ) {
        $mil = $valor / 1000;
        return 'R$ ' . number_format( $mil, 1, ',', '.' ) . ' mil';
    }
    return 'R$ ' . number_format( $valor, 2, ',', '.' );
}

/**
 * Formata data no padrão brasileiro
 * 
 * @param string $data Data em qualquer formato
 * @return string Data formatada
 */
function seisdeagosto_format_date( $data ) {
    if ( empty( $data ) ) {
        return '';
    }
    
    // Remove barras escapadas (vem como "03\/02\/2026" da API)
    $data = str_replace( '\/', '/', $data );
    
    // Se já está no formato brasileiro dd/mm/yyyy, retorna como está
    if ( preg_match( '/^(\d{2})\/(\d{2})\/(\d{4})$/', $data, $matches ) ) {
        $dia = $matches[1];
        $mes = $matches[2];
        $ano = $matches[3];
        
        // Valida a data
        if ( checkdate( (int)$mes, (int)$dia, (int)$ano ) ) {
            return $data;
        }
    }
    
    // Se está no formato ISO YYYY-MM-DD, converte para brasileiro
    if ( preg_match( '/(\d{4})-(\d{2})-(\d{2})/', $data, $matches ) ) {
        $ano = $matches[1];
        $mes = $matches[2];
        $dia = $matches[3];
        
        // Valida a data antes de retornar
        if ( checkdate( (int)$mes, (int)$dia, (int)$ano ) ) {
            return $dia . '/' . $mes . '/' . $ano;
        }
    }
    
    // Fallback: tenta usar DateTime
    $data_clean = $data;
    if ( strpos( $data_clean, 'T' ) !== false ) {
        $data_clean = str_replace( 'T', ' ', $data_clean );
        $data_clean = preg_replace( '/\.\d+/', '', $data_clean ); // Remove .000
        $data_clean = preg_replace( '/[+-]\d{2}:\d{2}/', '', $data_clean ); // Remove +00:00
        $data_clean = str_replace( 'Z', '', $data_clean ); // Remove Z
        $data_clean = trim( $data_clean );
    }
    
    try {
        $date = new DateTime( $data_clean );
        return $date->format( 'd/m/Y' );
    } catch ( Exception $e ) {
        return $data;
    }
}
