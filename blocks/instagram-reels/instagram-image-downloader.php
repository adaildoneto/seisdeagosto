<?php
/**
 * Instagram Image Downloader
 * Downloads Instagram images to WordPress uploads directory to bypass CORS
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Download Instagram image and save to WordPress uploads
 * 
 * @param string $image_url Original Instagram image URL
 * @param string $post_id Instagram post ID/shortcode
 * @return string|false Local image URL or false on error
 */
function seisdeagosto_download_instagram_image( $image_url, $post_id ) {
    if ( empty( $image_url ) || empty( $post_id ) ) {
        return false;
    }
    
    // Get WordPress upload directory
    $upload_dir = wp_upload_dir();
    $instagram_dir = $upload_dir['basedir'] . '/instagram-cache';
    $instagram_url = $upload_dir['baseurl'] . '/instagram-cache';
    
    // Create directory if doesn't exist
    if ( ! file_exists( $instagram_dir ) ) {
        wp_mkdir_p( $instagram_dir );
        
        // Add .htaccess to allow image serving
        $htaccess_content = "<IfModule mod_headers.c>\n";
        $htaccess_content .= "    Header set Access-Control-Allow-Origin \"*\"\n";
        $htaccess_content .= "    Header set Cache-Control \"max-age=86400, public\"\n";
        $htaccess_content .= "</IfModule>\n";
        file_put_contents( $instagram_dir . '/.htaccess', $htaccess_content );
    }
    
    // Generate filename based on post ID and original URL
    $extension = 'jpg'; // Instagram images are usually JPG
    $filename = sanitize_file_name( $post_id . '-' . md5( $image_url ) . '.' . $extension );
    $file_path = $instagram_dir . '/' . $filename;
    $file_url = $instagram_url . '/' . $filename;
    
    // Check if file already exists
    if ( file_exists( $file_path ) ) {
        // Check if file is older than 7 days
        $file_time = filemtime( $file_path );
        $current_time = time();
        $age_days = ( $current_time - $file_time ) / DAY_IN_SECONDS;
        
        if ( $age_days < 7 ) {
            // File is recent, use it
            return $file_url;
        }
        // File is old, re-download
    }
    
    // Download image from Instagram
    $response = wp_remote_get( $image_url, array(
        'timeout' => 30,
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
        'headers' => array(
            'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
            'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
        )
    ) );
    
    if ( is_wp_error( $response ) ) {
        error_log( '[Instagram Download] Error downloading image: ' . $response->get_error_message() );
        return false;
    }
    
    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code !== 200 ) {
        error_log( '[Instagram Download] HTTP error ' . $response_code . ' for: ' . $image_url );
        return false;
    }
    
    $image_data = wp_remote_retrieve_body( $response );
    
    if ( empty( $image_data ) ) {
        error_log( '[Instagram Download] Empty image data for: ' . $image_url );
        return false;
    }
    
    // Save image to file
    $saved = file_put_contents( $file_path, $image_data );
    
    if ( $saved === false ) {
        error_log( '[Instagram Download] Failed to save image to: ' . $file_path );
        return false;
    }
    
    // Optimize image if possible
    if ( function_exists( 'wp_get_image_editor' ) ) {
        $image_editor = wp_get_image_editor( $file_path );
        if ( ! is_wp_error( $image_editor ) ) {
            $image_editor->set_quality( 85 );
            $image_editor->save( $file_path );
        }
    }
    
    // Log success
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( '[Instagram Download] Successfully saved: ' . $filename . ' (' . size_format( $saved ) . ')' );
    }
    
    return $file_url;
}

/**
 * Download multiple Instagram images
 * 
 * @param array $media_items Array of media items with 'thumbnail' and 'id' keys
 * @return array Updated media items with local URLs
 */
function seisdeagosto_download_instagram_images( $media_items ) {
    if ( empty( $media_items ) || ! is_array( $media_items ) ) {
        return $media_items;
    }
    
    foreach ( $media_items as $index => $item ) {
        if ( empty( $item['thumbnail'] ) || empty( $item['id'] ) ) {
            continue;
        }
        
        // Download and get local URL
        $local_url = seisdeagosto_download_instagram_image( $item['thumbnail'], $item['id'] );
        
        if ( $local_url ) {
            // Update thumbnail to local URL
            $media_items[ $index ]['thumbnail'] = $local_url;
            $media_items[ $index ]['thumbnail_original'] = $item['thumbnail']; // Keep original for reference
            
            // Also update main URL if it's an image (not video)
            if ( $item['type'] === 'IMAGE' && ! empty( $item['url'] ) ) {
                $local_image_url = seisdeagosto_download_instagram_image( $item['url'], $item['id'] . '-full' );
                if ( $local_image_url ) {
                    $media_items[ $index ]['url'] = $local_image_url;
                    $media_items[ $index ]['url_original'] = $item['url'];
                }
            }
        }
    }
    
    return $media_items;
}

/**
 * Clear old Instagram cache files (older than 7 days)
 * 
 * @return int Number of files deleted
 */
function seisdeagosto_clear_old_instagram_cache() {
    $upload_dir = wp_upload_dir();
    $instagram_dir = $upload_dir['basedir'] . '/instagram-cache';
    
    if ( ! file_exists( $instagram_dir ) ) {
        return 0;
    }
    
    $files = glob( $instagram_dir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE );
    $deleted = 0;
    $current_time = time();
    
    foreach ( $files as $file ) {
        $file_time = filemtime( $file );
        $age_days = ( $current_time - $file_time ) / DAY_IN_SECONDS;
        
        if ( $age_days > 7 ) {
            if ( unlink( $file ) ) {
                $deleted++;
            }
        }
    }
    
    if ( $deleted > 0 ) {
        error_log( '[Instagram Cache] Deleted ' . $deleted . ' old cached images' );
    }
    
    return $deleted;
}

/**
 * Get cache directory stats
 * 
 * @return array Statistics about cache directory
 */
function seisdeagosto_get_instagram_cache_stats() {
    $upload_dir = wp_upload_dir();
    $instagram_dir = $upload_dir['basedir'] . '/instagram-cache';
    
    if ( ! file_exists( $instagram_dir ) ) {
        return array(
            'exists' => false,
            'count' => 0,
            'size' => 0,
            'size_formatted' => '0 B'
        );
    }
    
    $files = glob( $instagram_dir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE );
    $total_size = 0;
    
    foreach ( $files as $file ) {
        $total_size += filesize( $file );
    }
    
    return array(
        'exists' => true,
        'count' => count( $files ),
        'size' => $total_size,
        'size_formatted' => size_format( $total_size ),
        'path' => $instagram_dir
    );
}

// Schedule automatic cleanup (once daily)
if ( ! wp_next_scheduled( 'seisdeagosto_cleanup_instagram_cache' ) ) {
    wp_schedule_event( time(), 'daily', 'seisdeagosto_cleanup_instagram_cache' );
}

add_action( 'seisdeagosto_cleanup_instagram_cache', 'seisdeagosto_clear_old_instagram_cache' );
