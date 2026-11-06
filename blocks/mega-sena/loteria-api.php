<?php
/**
 * API Handler para Loterias da Caixa
 * Consulta os resultados via API oficial da Caixa Econômica Federal
 *
 * API Documentation: https://servicebus2.caixa.gov.br/portaldeloterias/api/home/
 * Endpoint: https://servicebus2.caixa.gov.br/portaldeloterias/api/{modalidade}/
 */

/**
 * Busca o resultado de uma loteria específica.
 *
 * Estratégia para nunca deixar a página presa esperando a API da Caixa:
 * 1. Cache "fresco" (transient): 30 min para o último concurso, 30 dias para concursos passados.
 * 2. Último resultado válido guardado em option: se o cache expirou ou a API falhou, ele é
 *    devolvido na hora e a atualização é agendada no WP-Cron (segundo plano).
 * 3. Disjuntor: após uma falha de conexão, a API não é chamada de novo por 5 minutos.
 * 4. Orçamento por requisição: no máximo ~8 s de chamadas síncronas por carregamento de página.
 *
 * @param string $modalidade Nome da modalidade (megasena, lotofacil, quina, etc).
 * @param int    $concurso   Número do concurso (opcional; sem ele pega o último).
 * @return array Dados do concurso ou array( 'error' => mensagem ).
 */
function seisdeagosto_get_loteria_result( $modalidade = 'megasena', $concurso = null ) {
	// Aceita apenas modalidades conhecidas: o valor compõe a URL da API e a chave de cache.
	$modalidade = sanitize_key( (string) $modalidade );
	if ( ! array_key_exists( $modalidade, seisdeagosto_get_loteria_modalidades() ) ) {
		return array( 'error' => 'Modalidade de loteria inválida.' );
	}

	$concurso  = absint( $concurso );
	$cache_key = seisdeagosto_loteria_cache_key( $modalidade, $concurso );

	$fresh = get_transient( $cache_key );
	if ( false !== $fresh && ! isset( $fresh['error'] ) ) {
		return $fresh;
	}

	// Último resultado válido (só para "último concurso"; concursos passados não mudam).
	$last      = $concurso ? false : seisdeagosto_get_last_loteria( $modalidade );
	$stale     = $last ? $last['data'] : false;
	$stale_age = $last ? time() - $last['time'] : 0;

	// Falha recente já registrada para esta chave: não tenta de novo agora.
	if ( false !== $fresh ) {
		return $stale ? $stale : $fresh;
	}

	if ( ! wp_doing_cron() ) {
		$api_down     = (bool) get_transient( 'seisdeagosto_loteria_api_down' );
		$over_budget  = seisdeagosto_loteria_budget_spent() >= (float) apply_filters( 'seisdeagosto_loteria_request_budget', 8 );
		$stale_recent = $stale && $stale_age < 2 * HOUR_IN_SECONDS;

		// Há resultado para mostrar: devolve na hora e atualiza em segundo plano.
		// Se ele já tem mais de 2 h (WP-Cron pode não estar rodando), busca direto,
		// desde que a API esteja respondendo e o orçamento de tempo permita.
		if ( $stale && ( $stale_recent || $api_down || $over_budget ) ) {
			seisdeagosto_schedule_loteria_refresh( $modalidade, $concurso );
			return $stale;
		}

		if ( $api_down ) {
			seisdeagosto_schedule_loteria_refresh( $modalidade, $concurso );
			return array( 'error' => 'API da Caixa indisponível no momento. Tente novamente em alguns minutos.' );
		}

		if ( $over_budget ) {
			seisdeagosto_schedule_loteria_refresh( $modalidade, $concurso );
			return array( 'error' => 'Resultado sendo atualizado. Recarregue a página em instantes.' );
		}
	}

	return seisdeagosto_fetch_loteria( $modalidade, $concurso );
}

/**
 * Último resultado válido guardado para uma modalidade.
 *
 * @param string $modalidade Modalidade já validada.
 * @return array|false array( 'time' => timestamp, 'data' => dados ) ou false.
 */
function seisdeagosto_get_last_loteria( $modalidade ) {
	$last = get_option( 'seisdeagosto_loteria_last_' . $modalidade, false );
	if ( ! is_array( $last ) || empty( $last['data'] ) || ! is_array( $last['data'] ) ) {
		return false;
	}
	$last['time'] = isset( $last['time'] ) ? (int) $last['time'] : 0;
	return $last;
}

/**
 * Chave de cache de uma modalidade/concurso.
 *
 * @param string $modalidade Modalidade já validada.
 * @param int    $concurso   Número do concurso ou 0.
 * @return string
 */
function seisdeagosto_loteria_cache_key( $modalidade, $concurso = 0 ) {
	return 'loteria_' . $modalidade . ( $concurso ? '_' . $concurso : '' );
}

/**
 * Soma (e opcionalmente acumula) o tempo gasto em chamadas à API nesta requisição.
 *
 * @param float $add Segundos a somar.
 * @return float Total gasto até agora.
 */
function seisdeagosto_loteria_budget_spent( $add = 0.0 ) {
	static $spent = 0.0;
	$spent       += (float) $add;
	return $spent;
}

/**
 * Agenda a atualização de uma modalidade no WP-Cron (uma vez por modalidade/concurso).
 *
 * @param string $modalidade Modalidade já validada.
 * @param int    $concurso   Número do concurso ou 0.
 */
function seisdeagosto_schedule_loteria_refresh( $modalidade, $concurso = 0 ) {
	$args = array( $modalidade, (int) $concurso );
	if ( ! wp_next_scheduled( 'seisdeagosto_refresh_loteria', $args ) ) {
		wp_schedule_single_event( time(), 'seisdeagosto_refresh_loteria', $args );
	}
}

/**
 * Tarefa do WP-Cron: busca o resultado na API e atualiza os caches.
 *
 * @param string $modalidade Modalidade.
 * @param int    $concurso   Número do concurso ou 0.
 */
function seisdeagosto_cron_refresh_loteria( $modalidade, $concurso = 0 ) {
	$modalidade = sanitize_key( (string) $modalidade );
	if ( ! array_key_exists( $modalidade, seisdeagosto_get_loteria_modalidades() ) ) {
		return;
	}
	// Respeita o disjuntor também no cron, para não insistir com a API fora do ar.
	if ( get_transient( 'seisdeagosto_loteria_api_down' ) ) {
		return;
	}
	seisdeagosto_fetch_loteria( $modalidade, absint( $concurso ) );
	delete_transient( 'todas_loterias' );
}
add_action( 'seisdeagosto_refresh_loteria', 'seisdeagosto_cron_refresh_loteria', 10, 2 );

/**
 * Faz a requisição à API da Caixa e grava os caches.
 *
 * @param string $modalidade Modalidade já validada.
 * @param int    $concurso   Número do concurso ou 0.
 * @return array Dados do concurso ou array( 'error' => mensagem ).
 */
function seisdeagosto_fetch_loteria( $modalidade, $concurso = 0 ) {
	$cache_key = seisdeagosto_loteria_cache_key( $modalidade, $concurso );

	$url = 'https://servicebus2.caixa.gov.br/portaldeloterias/api/' . rawurlencode( $modalidade );
	if ( $concurso ) {
		$url .= '/' . $concurso;
	}

	$started  = microtime( true );
	$response = wp_remote_get(
		$url,
		array(
			'timeout' => (float) apply_filters( 'seisdeagosto_loteria_timeout', 5 ),
			'headers' => array(
				'Accept' => 'application/json',
			),
		)
	);
	seisdeagosto_loteria_budget_spent( microtime( true ) - $started );

	$error    = null;
	$api_down = false;

	if ( is_wp_error( $response ) ) {
		error_log( '[Loteria API] Erro: ' . $response->get_error_message() );
		$error    = 'Erro ao conectar com a API: ' . $response->get_error_message();
		$api_down = true;
	} else {
		$status_code = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 !== $status_code ) {
			error_log( '[Loteria API] Status code: ' . $status_code );
			$error    = 'API retornou status ' . $status_code;
			$api_down = $status_code >= 500 || 403 === $status_code || 429 === $status_code;
		} else {
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $data ) ) {
				error_log( '[Loteria API] Erro ao decodificar JSON: ' . json_last_error_msg() );
				$error = 'Erro ao processar dados da API';
			}
		}
	}

	if ( null !== $error ) {
		if ( $api_down ) {
			// Disjuntor: evita repetir chamadas lentas para as outras modalidades.
			set_transient( 'seisdeagosto_loteria_api_down', 1, 5 * MINUTE_IN_SECONDS );
		}
		$result = array( 'error' => $error );
		set_transient( $cache_key, $result, 5 * MINUTE_IN_SECONDS );

		$last = $concurso ? false : seisdeagosto_get_last_loteria( $modalidade );
		return $last ? $last['data'] : $result;
	}

	if ( $concurso ) {
		// Resultado de concurso passado não muda.
		set_transient( $cache_key, $data, 30 * DAY_IN_SECONDS );
	} else {
		set_transient( $cache_key, $data, 30 * MINUTE_IN_SECONDS );
		update_option(
			'seisdeagosto_loteria_last_' . $modalidade,
			array(
				'time' => time(),
				'data' => $data,
			),
			false
		);
	}

	return $data;
}

/**
 * Lista de modalidades aceitas (slug => nome).
 *
 * @return array
 */
function seisdeagosto_get_loteria_modalidades() {
	return array(
		'megasena'       => 'Mega Sena',
		'lotofacil'      => 'Lotofácil',
		'quina'          => 'Quina',
		'lotomania'      => 'Lotomania',
		'timemania'      => 'Timemania',
		'duplasena'      => 'Dupla Sena',
		'federal'        => 'Federal',
		'loteca'         => 'Loteca',
		'diadesorte'     => 'Dia de Sorte',
		'supersete'      => 'Super Sete',
		'maismilionaria' => '+Milionária',
	);
}

function seisdeagosto_get_all_loterias() {
	$cache_key   = 'todas_loterias';
	$cached_data = get_transient( $cache_key );
	if ( false !== $cached_data ) {
		return $cached_data;
	}

	$modalidades = seisdeagosto_get_loteria_modalidades();
	$resultados  = array();

	foreach ( $modalidades as $slug => $nome ) {
		$resultado = seisdeagosto_get_loteria_result( $slug );
		if ( $resultado && ! isset( $resultado['error'] ) ) {
			$resultados[] = array(
				'slug'  => $slug,
				'nome'  => $nome,
				'dados' => $resultado,
			);
		}
	}

	// Lista completa: 30 minutos. Incompleta (API falhando ou atualizando): só 2 minutos.
	$ttl = count( $resultados ) === count( $modalidades ) ? 30 * MINUTE_IN_SECONDS : 2 * MINUTE_IN_SECONDS;
	set_transient( $cache_key, $resultados, $ttl );

	return $resultados;
}

/**
 * Limpa o cache de uma modalidade específica
 *
 * @param string $modalidade Nome da modalidade
 */
function seisdeagosto_clear_loteria_cache( $modalidade = null ) {
	$modalidades = $modalidade ? array( sanitize_key( $modalidade ) ) : array_keys( seisdeagosto_get_loteria_modalidades() );
	foreach ( $modalidades as $mod ) {
		delete_transient( 'loteria_' . $mod );
		delete_option( 'seisdeagosto_loteria_last_' . $mod );
	}
	delete_transient( 'todas_loterias' );
	delete_transient( 'seisdeagosto_loteria_api_down' );
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
		if ( checkdate( (int) $mes, (int) $dia, (int) $ano ) ) {
			return $data;
		}
	}

	// Se está no formato ISO YYYY-MM-DD, converte para brasileiro
	if ( preg_match( '/(\d{4})-(\d{2})-(\d{2})/', $data, $matches ) ) {
		$ano = $matches[1];
		$mes = $matches[2];
		$dia = $matches[3];

		// Valida a data antes de retornar
		if ( checkdate( (int) $mes, (int) $dia, (int) $ano ) ) {
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

/**
 * Ação do admin para limpar o cache das loterias (substitui o antigo clear-cache.php).
 * URL: wp-admin/admin-post.php?action=seisdeagosto_clear_loteria_cache&_wpnonce=...
 */
function seisdeagosto_handle_clear_loteria_cache() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Acesso negado.', 'u_correio68' ), 403 );
	}
	check_admin_referer( 'seisdeagosto_clear_loteria_cache' );

	seisdeagosto_clear_loteria_cache();

	$redirect = wp_get_referer();
	wp_safe_redirect( $redirect ? $redirect : admin_url() );
	exit;
}
add_action( 'admin_post_seisdeagosto_clear_loteria_cache', 'seisdeagosto_handle_clear_loteria_cache' );

/**
 * Atalho na barra de administração para limpar o cache das loterias.
 *
 * @param WP_Admin_Bar $wp_admin_bar Barra de administração.
 */
function seisdeagosto_loteria_admin_bar( $wp_admin_bar ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$url = wp_nonce_url(
		admin_url( 'admin-post.php?action=seisdeagosto_clear_loteria_cache' ),
		'seisdeagosto_clear_loteria_cache'
	);
	$wp_admin_bar->add_node(
		array(
			'id'    => 'seisdeagosto-clear-loteria-cache',
			'title' => esc_html__( 'Limpar cache das loterias', 'u_correio68' ),
			'href'  => $url,
		)
	);
}
add_action( 'admin_bar_menu', 'seisdeagosto_loteria_admin_bar', 121 );
