<?php
/**
 * Instagram GraphQL Scraper
 * Based on: https://github.com/ahmedrangel/instagram-media-scraper
 * Method 2: GraphQL (No Cookie Needed)
 * 
 * This implementation uses Instagram's GraphQL API to fetch post/reel data
 * without requiring cookies or access tokens.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Extract Instagram post ID from URL
 * 
 * @param string $url Instagram post/reel URL
 * @return string|null Post ID (shortcode) or null if invalid
 */
function seisdeagosto_get_instagram_id_from_url( $url ) {
    // Regex pattern to match Instagram URLs
    // Matches: instagram.com/{username}/p/{id}, instagram.com/reel/{id}, instagram.com/reels/{id}
    $pattern = '/instagram\.com\/(?:[A-Za-z0-9_.]+\/)?(p|reels|reel|stories)\/([A-Za-z0-9-_]+)/';
    
    if ( preg_match( $pattern, $url, $matches ) ) {
        return isset( $matches[2] ) ? $matches[2] : null;
    }
    
    return null;
}

/**
 * Fetch Instagram post data using GraphQL API
 * 
 * @param string $url Instagram post/reel URL
 * @return array|false Post data or false on error
 */
function seisdeagosto_get_instagram_graphql_data( $url ) {
    // Extract post ID
    $ig_id = seisdeagosto_get_instagram_id_from_url( $url );
    
    if ( ! $ig_id ) {
        error_log( '[Instagram GraphQL] Invalid URL: ' . $url );
        return false;
    }
    
    // Check cache first (1 hour)
    $cache_key = 'ig_graphql_' . md5( $ig_id );
    $cached_data = get_transient( $cache_key );
    
    if ( false !== $cached_data ) {
        return $cached_data;
    }
    
    // User-Agent (recommended to use a current browser User-Agent)
    $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36';
    
    // X-IG-App-ID (Instagram app ID, commonly used value)
    $x_ig_app_id = '936619743392459';
    
    // Build GraphQL API URL
    $graphql_url = 'https://www.instagram.com/api/graphql';
    
    // Add query parameters
    $params = array(
        'variables' => json_encode( array( 'shortcode' => $ig_id ) ),
        'doc_id' => '10015901848480474',
        'lsd' => 'AVqbxe3J_YA'
    );
    
    $graphql_url = add_query_arg( $params, $graphql_url );
    
    // Make POST request to Instagram GraphQL API
    $response = wp_remote_post( $graphql_url, array(
        'timeout' => 15,
        'headers' => array(
            'User-Agent' => $user_agent,
            'Content-Type' => 'application/x-www-form-urlencoded',
            'X-IG-App-ID' => $x_ig_app_id,
            'X-FB-LSD' => 'AVqbxe3J_YA',
            'X-ASBD-ID' => '129477',
            'Sec-Fetch-Site' => 'same-origin'
        )
    ) );
    
    if ( is_wp_error( $response ) ) {
        error_log( '[Instagram GraphQL] Error: ' . $response->get_error_message() );
        return false;
    }
    
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    
    // Debug logging
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( '[Instagram GraphQL] Response for ' . $ig_id . ': ' . print_r( $data, true ) );
    }
    
    // Check for errors
    if ( ! isset( $data['data']['xdt_shortcode_media'] ) ) {
        error_log( '[Instagram GraphQL] No media data found for: ' . $ig_id );
        return false;
    }
    
    $items = $data['data']['xdt_shortcode_media'];
    
    // Extract and normalize data
    $media_data = array(
        '__typename' => isset( $items['__typename'] ) ? $items['__typename'] : '',
        'shortcode' => isset( $items['shortcode'] ) ? $items['shortcode'] : $ig_id,
        'dimensions' => isset( $items['dimensions'] ) ? $items['dimensions'] : array(),
        'display_url' => isset( $items['display_url'] ) ? $items['display_url'] : '',
        'display_resources' => isset( $items['display_resources'] ) ? $items['display_resources'] : array(),
        'has_audio' => isset( $items['has_audio'] ) ? $items['has_audio'] : false,
        'video_url' => isset( $items['video_url'] ) ? $items['video_url'] : '',
        'video_view_count' => isset( $items['video_view_count'] ) ? intval( $items['video_view_count'] ) : 0,
        'video_play_count' => isset( $items['video_play_count'] ) ? intval( $items['video_play_count'] ) : 0,
        'is_video' => isset( $items['is_video'] ) ? $items['is_video'] : false,
        'caption' => isset( $items['edge_media_to_caption']['edges'][0]['node']['text'] ) ? 
                     $items['edge_media_to_caption']['edges'][0]['node']['text'] : '',
        'is_paid_partnership' => isset( $items['is_paid_partnership'] ) ? $items['is_paid_partnership'] : false,
        'location' => isset( $items['location'] ) ? $items['location'] : null,
        'owner' => isset( $items['owner'] ) ? $items['owner'] : array(),
        'product_type' => isset( $items['product_type'] ) ? $items['product_type'] : '',
        'video_duration' => isset( $items['video_duration'] ) ? floatval( $items['video_duration'] ) : 0,
        'thumbnail_src' => isset( $items['thumbnail_src'] ) ? $items['thumbnail_src'] : '',
        'clips_music_attribution_info' => isset( $items['clips_music_attribution_info'] ) ? 
                                          $items['clips_music_attribution_info'] : array(),
        'sidecar' => isset( $items['edge_sidecar_to_children']['edges'] ) ? 
                    $items['edge_sidecar_to_children']['edges'] : array(),
        'permalink' => 'https://www.instagram.com/p/' . $ig_id . '/',
        'timestamp' => isset( $items['taken_at_timestamp'] ) ? $items['taken_at_timestamp'] : time(),
        'like_count' => isset( $items['edge_media_preview_like']['count'] ) ? 
                       intval( $items['edge_media_preview_like']['count'] ) : 0,
        'comment_count' => isset( $items['edge_media_to_parent_comment']['count'] ) ? 
                          intval( $items['edge_media_to_parent_comment']['count'] ) : 0,
    );
    
    // Cache for 1 hour
    set_transient( $cache_key, $media_data, HOUR_IN_SECONDS );
    
    return $media_data;
}

/**
 * Fetch multiple Instagram posts from a list of URLs
 * 
 * @param array $urls Array of Instagram post/reel URLs
 * @return array Array of post data
 */
function seisdeagosto_get_instagram_multiple_posts( $urls ) {
    if ( ! is_array( $urls ) || empty( $urls ) ) {
        return array();
    }
    
    $media_items = array();
    
    foreach ( $urls as $url ) {
        $url = trim( $url );
        if ( empty( $url ) ) {
            continue;
        }
        
        $media_data = seisdeagosto_get_instagram_graphql_data( $url );
        
        if ( $media_data ) {
            // Get best quality thumbnail
            $thumbnail_url = $media_data['display_url'];
            if ( $media_data['is_video'] && ! empty( $media_data['thumbnail_src'] ) ) {
                $thumbnail_url = $media_data['thumbnail_src'];
            } elseif ( ! empty( $media_data['display_resources'] ) && is_array( $media_data['display_resources'] ) ) {
                // Use highest quality from display_resources
                $highest_res = end( $media_data['display_resources'] );
                if ( isset( $highest_res['src'] ) ) {
                    $thumbnail_url = $highest_res['src'];
                }
            }
            
            // Normalize data structure for compatibility with existing render code
            $media_items[] = array(
                'id' => $media_data['shortcode'],
                'type' => $media_data['is_video'] ? 'VIDEO' : 'IMAGE',
                'url' => $media_data['is_video'] ? $media_data['video_url'] : $media_data['display_url'],
                'thumbnail' => $thumbnail_url,
                'caption' => $media_data['caption'],
                'permalink' => $media_data['permalink'],
                'timestamp' => date( 'c', $media_data['timestamp'] ),
                'like_count' => $media_data['like_count'],
                'comments_count' => $media_data['comment_count'],
                'media_product_type' => $media_data['product_type'] === 'clips' ? 'REELS' : strtoupper( $media_data['product_type'] ),
                'owner' => isset( $media_data['owner']['username'] ) ? $media_data['owner']['username'] : '',
                'video_view_count' => $media_data['video_view_count'],
                'video_play_count' => $media_data['video_play_count'],
                'dimensions' => $media_data['dimensions'],
                'raw_data' => $media_data, // Keep raw data for advanced usage
            );
        }
    }
    
    return $media_items;
}

/**
 * Clear Instagram GraphQL cache for a specific post
 * 
 * @param string $url Instagram post/reel URL
 * @return bool True if cache was deleted, false otherwise
 */
function seisdeagosto_clear_instagram_cache( $url ) {
    $ig_id = seisdeagosto_get_instagram_id_from_url( $url );
    
    if ( ! $ig_id ) {
        return false;
    }
    
    $cache_key = 'ig_graphql_' . md5( $ig_id );
    return delete_transient( $cache_key );
}
