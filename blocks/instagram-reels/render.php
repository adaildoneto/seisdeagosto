<?php
/**
 * Render callback for Instagram Reels Gallery block
 * Uses Instagram GraphQL Scraper (No Access Token Required)
 * Based on: https://github.com/ahmedrangel/instagram-media-scraper
 */

// Load the GraphQL scraper
require_once __DIR__ . '/instagram-graphql-scraper.php';

// Load the image downloader
require_once __DIR__ . '/instagram-image-downloader.php';

/**
 * Fetch Instagram media using Graph API (LEGACY - kept for backward compatibility)
 */
function seisdeagosto_fetch_instagram_media( $access_token, $limit = 6, $media_type = 'all', $username = '' ) {
    if ( empty( $access_token ) ) {
        return false;
    }
    
    // Check cache first (1 hour)
    $cache_key = 'ig_media_' . md5( $access_token . $limit . $media_type . $username );
    $cached_data = get_transient( $cache_key );
    
    if ( false !== $cached_data ) {
        return $cached_data;
    }
    
    // Instagram Graph API endpoint
    // Campos disponíveis conforme: https://developers.facebook.com/docs/instagram-platform/reference/instagram-media
    $fields = 'id,media_type,media_url,thumbnail_url,caption,permalink,timestamp,like_count,comments_count,media_product_type';
    
    // Se username específico for fornecido, usa Business Discovery API
    if ( ! empty( $username ) ) {
        // Remove @ se existir
        $username = ltrim( $username, '@' );
        
        // Passo 1: Obter Page ID através de /me/accounts
        $pages_url = 'https://graph.facebook.com/me/accounts?access_token=' . urlencode( $access_token );
        $pages_response = wp_remote_get( $pages_url, array( 'timeout' => 15 ) );
        
        if ( is_wp_error( $pages_response ) ) {
            error_log( '[Instagram API] Error getting pages: ' . $pages_response->get_error_message() );
            return false;
        }
        
        $pages_data = json_decode( wp_remote_retrieve_body( $pages_response ), true );
        if ( empty( $pages_data['data'][0]['id'] ) ) {
            error_log( '[Instagram API] No Facebook Page found. Business Discovery requires a Facebook Page connected to Instagram Business Account.' );
            set_transient( 'ig_last_error_' . md5( $access_token ), array(
                'message' => 'Nenhuma Página do Facebook encontrada. Vincule uma Página à sua conta Instagram Business.',
                'code' => 'NO_PAGE',
                'username' => $username
            ), 300 );
            return false;
        }
        
        $page_id = $pages_data['data'][0]['id'];
        
        // Passo 2: Obter IG User ID através do Page ID
        $ig_account_url = 'https://graph.facebook.com/' . $page_id . '?fields=instagram_business_account&access_token=' . urlencode( $access_token );
        $ig_account_response = wp_remote_get( $ig_account_url, array( 'timeout' => 15 ) );
        
        if ( is_wp_error( $ig_account_response ) ) {
            error_log( '[Instagram API] Error getting IG account: ' . $ig_account_response->get_error_message() );
            return false;
        }
        
        $ig_account_data = json_decode( wp_remote_retrieve_body( $ig_account_response ), true );
        if ( empty( $ig_account_data['instagram_business_account']['id'] ) ) {
            error_log( '[Instagram API] No Instagram Business Account linked to Page. Page ID: ' . $page_id );
            set_transient( 'ig_last_error_' . md5( $access_token ), array(
                'message' => 'Página do Facebook não está vinculada a uma conta Instagram Business. Vincule sua conta no Facebook.',
                'code' => 'NO_IG_BUSINESS',
                'username' => $username
            ), 300 );
            return false;
        }
        
        $ig_user_id = $ig_account_data['instagram_business_account']['id'];
        
        // Passo 3: Business Discovery API com IG_USER_ID correto
        $url = 'https://graph.facebook.com/' . $ig_user_id . '?fields=business_discovery.username(' . urlencode( $username ) . '){media.limit(' . intval( $limit * 2 ) . '){' . $fields . '}}&access_token=' . urlencode( $access_token );
    } else {
        // Usa endpoint normal para posts próprios
        $url = 'https://graph.instagram.com/me/media?access_token=' . urlencode( $access_token ) . '&fields=' . $fields . '&limit=' . intval( $limit * 2 );
    }
    
    $response = wp_remote_get( $url, array(
        'timeout' => 15,
        'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url()
    ) );
    
    if ( is_wp_error( $response ) ) {
        error_log( '[Instagram API] Error: ' . $response->get_error_message() );
        return false;
    }
    
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    
    // Log da resposta completa para debug
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( '[Instagram API] Response: ' . print_r( $data, true ) );
    }
    
    // Verificar se há erro na resposta
    if ( isset( $data['error'] ) ) {
        $error_msg = isset( $data['error']['message'] ) ? $data['error']['message'] : 'Unknown error';
        $error_code = isset( $data['error']['code'] ) ? $data['error']['code'] : 'N/A';
        error_log( '[Instagram API] Error (Code: ' . $error_code . '): ' . $error_msg );
        
        // Armazenar erro em transient para exibir ao usuário
        set_transient( 'ig_last_error_' . md5( $access_token ), array(
            'message' => $error_msg,
            'code' => $error_code,
            'username' => $username
        ), 300 ); // 5 minutos
        
        return false;
    }
    
    // Extrair dados dependendo do tipo de requisição
    $media_data = array();
    if ( ! empty( $username ) ) {
        // Business Discovery API retorna estrutura diferente
        if ( empty( $data['business_discovery']['media']['data'] ) ) {
            error_log( '[Instagram API] No media found for username: ' . $username );
            set_transient( 'ig_last_error_' . md5( $access_token ), array(
                'message' => 'Nenhum post encontrado para o perfil @' . $username,
                'code' => 'NO_MEDIA',
                'username' => $username
            ), 300 );
            return false;
        }
        $media_data = $data['business_discovery']['media']['data'];
    } else {
        // Endpoint normal
        if ( empty( $data['data'] ) ) {
            error_log( '[Instagram API] No media found for authenticated user' );
            return false;
        }
        $media_data = $data['data'];
    }
    
    // Filter by media type
    $media_items = array();
    foreach ( $media_data as $item ) {
        // Filter: only VIDEO (reels/videos) or IMAGE or ALL
        if ( $media_type === 'reels' && $item['media_type'] !== 'VIDEO' ) {
            continue;
        }
        if ( $media_type === 'images' && $item['media_type'] !== 'IMAGE' ) {
            continue;
        }
        
        // Limpar URLs removendo aspas extras
        $media_url = isset( $item['media_url'] ) ? trim( $item['media_url'], '"' ) : '';
        $thumbnail_url = isset( $item['thumbnail_url'] ) ? trim( $item['thumbnail_url'], '"' ) : $media_url;
        $permalink = isset( $item['permalink'] ) ? trim( $item['permalink'], '"' ) : '';
        
        $media_items[] = array(
            'id' => $item['id'],
            'type' => $item['media_type'],
            'url' => $media_url,
            'thumbnail' => $thumbnail_url,
            'caption' => isset( $item['caption'] ) ? $item['caption'] : '',
            'permalink' => $permalink,
            'timestamp' => isset( $item['timestamp'] ) ? $item['timestamp'] : '',
            'like_count' => isset( $item['like_count'] ) ? intval( $item['like_count'] ) : 0,
            'comments_count' => isset( $item['comments_count'] ) ? intval( $item['comments_count'] ) : 0,
            'media_product_type' => isset( $item['media_product_type'] ) ? $item['media_product_type'] : '',
        );
        
        if ( count( $media_items ) >= $limit ) {
            break;
        }
    }
    
    // Cache for 1 hour
    if ( ! empty( $media_items ) ) {
        set_transient( $cache_key, $media_items, HOUR_IN_SECONDS );
    }
    
    return $media_items;
}

function seisdeagosto_render_instagram_reels( $attributes, $content, $block ) {
    // Extract attributes
    $title = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : '';
    $description = isset( $attributes['description'] ) ? sanitize_text_field( $attributes['description'] ) : '';
    $profile_url = isset( $attributes['profileUrl'] ) ? esc_url( $attributes['profileUrl'] ) : '';
    $access_token = isset( $attributes['accessToken'] ) ? sanitize_text_field( $attributes['accessToken'] ) : '';
    $instagram_urls = isset( $attributes['instagramUrls'] ) ? $attributes['instagramUrls'] : '';
    $number_of_reels = isset( $attributes['numberOfReels'] ) ? intval( $attributes['numberOfReels'] ) : 6;
    $columns = isset( $attributes['columns'] ) ? intval( $attributes['columns'] ) : 3;
    $show_captions = isset( $attributes['showCaptions'] ) ? filter_var( $attributes['showCaptions'], FILTER_VALIDATE_BOOLEAN ) : true;
    $media_type = isset( $attributes['mediaType'] ) ? sanitize_text_field( $attributes['mediaType'] ) : 'all';
    $instagram_username = isset( $attributes['instagramUsername'] ) ? sanitize_text_field( $attributes['instagramUsername'] ) : '';
    
    // Fetch media from Instagram
    $media_items = array();
    
    // Priority 1: Use GraphQL scraper with URLs (NEW METHOD)
    if ( ! empty( $instagram_urls ) ) {
        // Parse URLs from textarea (one per line)
        $urls = array_filter( array_map( 'trim', explode( "\n", $instagram_urls ) ) );
        
        if ( ! empty( $urls ) ) {
            $media_items = seisdeagosto_get_instagram_multiple_posts( $urls );
            
            // Download images to local WordPress
            if ( ! empty( $media_items ) ) {
                $media_items = seisdeagosto_download_instagram_images( $media_items );
            }
            
            // Limit to requested number
            if ( count( $media_items ) > $number_of_reels ) {
                $media_items = array_slice( $media_items, 0, $number_of_reels );
            }
        }
    }
    // Priority 2: Fallback to old Graph API method (LEGACY)
    elseif ( ! empty( $access_token ) ) {
        $media_items = seisdeagosto_fetch_instagram_media( $access_token, $number_of_reels, $media_type, $instagram_username );
    }
    
    // Build wrapper classes
    $wrapper_attributes = get_block_wrapper_attributes( array(
        'class' => 'instagram-reels-gallery',
    ) );
    
    ob_start();
    ?>
    <div <?php echo $wrapper_attributes; ?>>
        
        <?php if ( $title || $description || $profile_url ) : ?>
            <div class="ig-reels-header">
                <?php if ( $title ) : ?>
                    <h2 class="ig-reels-title"><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>
                <?php if ( $description ) : ?>
                    <p class="ig-reels-description"><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>
                <?php if ( $profile_url ) : ?>
                    <div class="ig-profile-button-wrapper">
                        <a href="<?php echo esc_url( $profile_url ); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="ig-profile-button">
                            <i class="fa fa-instagram"></i> Seguir no Instagram
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ( empty( $instagram_urls ) && empty( $access_token ) ) : ?>
            <!-- Instruções para configurar -->
            <div class="ig-reels-instructions">
                <h3><i class="fa fa-info-circle"></i> Configure o Instagram Reels</h3>
                <p><strong>✨ Novo Método Simplificado (Recomendado):</strong></p>
                <ol>
                    <li>Copie as URLs dos posts/reels do Instagram que deseja exibir</li>
                    <li>Cole as URLs no campo "URLs dos Posts" (uma por linha)</li>
                    <li>Pronto! Não precisa de Access Token</li>
                </ol>
                <p><strong>⚙️ Método Alternativo (API Oficial):</strong></p>
                <ol>
                    <li>Acesse <a href="https://developers.facebook.com/" target="_blank">Facebook Developers</a></li>
                    <li>Crie um app tipo "Consumidor"</li>
                    <li>Adicione produto "Exibição Básica do Instagram"</li>
                    <li>Gere o User Token</li>
                    <li>Cole o token nas configurações do bloco</li>
                </ol>
                <p><a href="https://matteus.dev/contratar/incorporar-posts-do-instagram-no-site-2024/" target="_blank">Ver tutorial completo da API oficial</a></p>
            </div>
        <?php elseif ( empty( $media_items ) ) : ?>
            <!-- Erro ao buscar m�dia -->
            <div class="ig-reels-error">
                <p><strong>⚠️ Não foi possível carregar os posts do Instagram</strong></p>
                <?php 
                $last_error = get_transient( 'ig_last_error_' . md5( $access_token ) );
                if ( $last_error && ! empty( $instagram_username ) ) :
                ?>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;">
                        <p style="margin: 0 0 10px 0;"><strong>Erro:</strong> <?php echo esc_html( $last_error['message'] ); ?></p>
                        <?php if ( $last_error['code'] !== 'NO_MEDIA' ) : ?>
                            <p style="margin: 0; font-size: 13px;">Para buscar posts de <strong>@<?php echo esc_html( $instagram_username ); ?></strong>, você precisa:</p>
                            <ul style="font-size: 13px; margin: 5px 0;">
                                <li>✓ Conta Instagram <strong>Business</strong> (não funciona com conta pessoal)</li>
                                <li>✓ Página do Facebook vinculada</li>
                                <li>✓ Token com permissões: <code>instagram_basic</code>, <code>pages_show_list</code>, <code>pages_read_engagement</code></li>
                                <li>✓ O perfil buscado deve ser <strong>público</strong></li>
                            </ul>
                            <p style="font-size: 13px; margin: 10px 0 0 0;">
                                <strong>Dica:</strong> Deixe o campo "Perfil" em branco para exibir seus próprios posts (não requer conta Business).
                            </p>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <p>Verifique se o Access Token está correto e válido.</p>
                    <?php if ( ! empty( $instagram_username ) ) : ?>
                        <p><em>Tentando buscar posts de: <strong>@<?php echo esc_html( $instagram_username ); ?></strong></em></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <!-- Grid de Reels/Posts -->
            <div class="ig-reels-grid" data-columns="<?php echo esc_attr( $columns ); ?>">
                <?php foreach ( $media_items as $index => $item ) : ?>
                    <div class="ig-reel-item" data-reel-index="<?php echo esc_attr( $index ); ?>">
                        <div class="ig-reel-thumbnail">
                            <img src="<?php echo esc_url( $item['thumbnail'] ); ?>" 
                                 alt="<?php echo esc_attr( wp_trim_words( $item['caption'], 10 ) ); ?>"
                                 loading="lazy">
                            <div class="ig-reel-overlay">
                                <?php if ( $item['type'] === 'VIDEO' ) : ?>
                                    <i class="fa fa-play-circle"></i>
                                <?php else : ?>
                                    <i class="fa fa-camera"></i>
                                <?php endif; ?>
                                
                                <!-- Badge para Reels -->
                                <?php if ( ! empty( $item['media_product_type'] ) && $item['media_product_type'] === 'REELS' ) : ?>
                                    <span class="ig-reel-badge">Reel</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo esc_url( $item['permalink'] ); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="ig-reel-link"
                               aria-label="Ver no Instagram">
                            </a>
                        </div>
                        
                        <!-- Estatísticas (likes e comentários) -->
                        <?php if ( isset( $item['like_count'] ) || isset( $item['comments_count'] ) ) : ?>
                            <div class="ig-reel-stats">
                                <?php if ( $item['like_count'] > 0 ) : ?>
                                    <span class="ig-stat-item">
                                        <i class="fa fa-heart"></i> <?php echo number_format_i18n( $item['like_count'] ); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ( $item['comments_count'] > 0 ) : ?>
                                    <span class="ig-stat-item">
                                        <i class="fa fa-comment"></i> <?php echo number_format_i18n( $item['comments_count'] ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( $show_captions && ! empty( $item['caption'] ) ) : ?>
                            <div class="ig-reel-caption">
                                <?php echo esc_html( wp_trim_words( $item['caption'], 15 ) ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    
    // Enqueue frontend script for image error handling
    wp_enqueue_script(
        'seisdeagosto-instagram-reels-frontend',
        get_template_directory_uri() . '/blocks/instagram-reels/frontend.js',
        array(),
        filemtime( get_template_directory() . '/blocks/instagram-reels/frontend.js' ),
        true
    );
    
    return ob_get_clean();
}
