<?php
/**
 * Instagram Proxy - Server-side fetch to bypass CORS
 * Usage: instagram-proxy.php?username=seisdeagosto
 */

// Load WordPress
require_once dirname(__FILE__) . '/../../../wp-load.php';

/**
 * Recursively search for user data in nested JSON structure
 */
function find_user_data_recursive($data, $username, $depth = 0) {
    // Prevent infinite recursion
    if ($depth > 10) return null;
    
    // If it's an array or object, search through it
    if (is_array($data)) {
        // Check if this level has user data we need
        if (isset($data['username']) && $data['username'] === $username) {
            // Found the user object!
            if (isset($data['edge_owner_to_timeline_media'])) {
                return $data;
            }
        }
        
        // Check for common Instagram data structures
        if (isset($data['user']) && is_array($data['user'])) {
            if (isset($data['user']['username']) && $data['user']['username'] === $username) {
                return $data['user'];
            }
        }
        
        // Recursively search all array elements
        foreach ($data as $value) {
            if (is_array($value) || is_object($value)) {
                $result = find_user_data_recursive($value, $username, $depth + 1);
                if ($result) return $result;
            }
        }
    }
    
    return null;
}

// Allow CORS from same domain
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: max-age=300'); // 5 minutes cache

// Get username
$username = isset($_GET['username']) ? sanitize_text_field($_GET['username']) : '';

if (empty($username)) {
    http_response_code(400);
    echo json_encode(['error' => 'Username parameter is required']);
    exit;
}

// Cache key
$cache_key = 'ig_proxy_' . md5($username);

// Allow manual cache clear
if (isset($_GET['nocache'])) {
    delete_transient($cache_key);
}

$cached = get_transient($cache_key);

if ($cached !== false) {
    echo $cached;
    exit;
}

// Method 1: Try Instagram JSON endpoint
$url = "https://www.instagram.com/{$username}/?__a=1&__d=dis";

$args = array(
    'timeout'     => 15,
    'redirection' => 5,
    'httpversion' => '1.1',
    'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'headers'     => array(
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
        'Accept-Language' => 'en-US,en;q=0.5',
        'Accept-Encoding' => 'gzip, deflate',
        'Connection' => 'keep-alive',
        'Upgrade-Insecure-Requests' => '1',
        'Sec-Fetch-Dest' => 'document',
        'Sec-Fetch-Mode' => 'navigate',
        'Sec-Fetch-Site' => 'none',
    ),
);

$response = wp_remote_get($url, $args);

// If JSON API works, return it
if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
    $body = wp_remote_retrieve_body($response);
    
    // Validate JSON
    $json = json_decode($body);
    if (json_last_error() === JSON_ERROR_NONE && isset($json->graphql)) {
        // Cache for 5 minutes
        set_transient($cache_key, $body, 300);
        echo $body;
        exit;
    }
}

// Method 2: Fallback - Parse HTML page
$url = "https://www.instagram.com/{$username}/";
$response = wp_remote_get($url, $args);

if (is_wp_error($response)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch Instagram profile', 'details' => $response->get_error_message()]);
    exit;
}

$html = wp_remote_retrieve_body($response);

// Debug mode - set ?debug=1 to see raw HTML
if (isset($_GET['debug'])) {
    echo json_encode([
        'debug' => true,
        'html_length' => strlen($html),
        'html_preview' => substr($html, 0, 1000),
        'has_sharedData' => strpos($html, '_sharedData') !== false,
        'has_graphql' => strpos($html, 'graphql') !== false,
        'has_json_ld' => strpos($html, 'application/ld+json') !== false
    ]);
    exit;
}

// Try to extract JSON from HTML
// Pattern 1: window._sharedData (old format)
if (preg_match('/<script type="text\/javascript">window\._sharedData = (.+?);<\/script>/', $html, $matches)) {
    $json_data = $matches[1];
    
    // Validate JSON
    $json = json_decode($json_data);
    if (json_last_error() === JSON_ERROR_NONE) {
        $output = json_encode($json);
        set_transient($cache_key, $output, 300);
        echo $output;
        exit;
    }
}

// Pattern 2: Newer format - script with type="application/json" (MOST COMMON NOW)
if (preg_match_all('/<script type="application\/json"[^>]*data-content-len="[^"]*"[^>]*>(.+?)<\/script>/s', $html, $matches)) {
    foreach ($matches[1] as $json_str) {
        $json = json_decode($json_str, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Look for user data in the complex structure
            if (isset($json['require'])) {
                // Navigate through the require structure to find user data
                foreach ($json['require'] as $req) {
                    if (isset($req[3]) && is_array($req[3])) {
                        foreach ($req[3] as $module) {
                            if (isset($module['__bbox']) && isset($module['__bbox']['result'])) {
                                $result = $module['__bbox']['result'];
                                if (isset($result['data']['user'])) {
                                    // Found user data! Convert to old format
                                    $user_data = [
                                        'graphql' => [
                                            'user' => $result['data']['user']
                                        ]
                                    ];
                                    $output = json_encode($user_data);
                                    set_transient($cache_key, $output, 300);
                                    echo $output;
                                    exit;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

// Pattern 3: Search for any large JSON blob containing user/graphql data
if (preg_match_all('/<script type="application\/json"[^>]*>(.+?)<\/script>/s', $html, $matches)) {
    foreach ($matches[1] as $json_str) {
        // Only process large JSON blobs (likely to contain user data)
        if (strlen($json_str) > 10000) {
            $json = json_decode($json_str, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Recursive search for user data
                $user_data = find_user_data_recursive($json, $username);
                if ($user_data) {
                    $output = json_encode(['graphql' => ['user' => $user_data]]);
                    set_transient($cache_key, $output, 300);
                    echo $output;
                    exit;
                }
            }
        }
    }
}

// Pattern 4: Extract from script tags with embedded data
if (preg_match('/<script[^>]*>\s*window\.__additionalDataLoaded[^{]*(\{.+?\});?\s*<\/script>/s', $html, $matches)) {
    $json_str = $matches[1];
    $json = json_decode($json_str);
    if (json_last_error() === JSON_ERROR_NONE) {
        $output = json_encode($json);
        set_transient($cache_key, $output, 300);
        echo $output;
        exit;
    }
}

// Pattern 5: New Instagram format with Relay data
if (preg_match('/window\.__INITIAL_STATE__\s*=\s*(\{.+?\});/s', $html, $matches)) {
    $json_str = $matches[1];
    $json = json_decode($json_str);
    if (json_last_error() === JSON_ERROR_NONE) {
        $output = json_encode($json);
        set_transient($cache_key, $output, 300);
        echo $output;
        exit;
    }
}

// Pattern 5: Try meta tags with JSON data
if (preg_match('/<meta property="og:description" content="([^"]+)"/', $html, $matches)) {
    // At least we can confirm the profile exists
    // Return minimal data structure
    $minimal_data = [
        'graphql' => [
            'user' => [
                'username' => $username,
                'edge_owner_to_timeline_media' => [
                    'edges' => []
                ]
            ]
        ],
        'note' => 'Could not extract posts. Profile exists but data structure not recognized.',
        'suggestion' => 'Use manual mode to add reels'
    ];
    
    $output = json_encode($minimal_data);
    echo $output;
    exit;
}

// If all methods fail, return error with helpful message
http_response_code(200); // Return 200 so JavaScript can handle gracefully
echo json_encode([
    'error' => 'Could not extract Instagram data',
    'suggestion' => 'O perfil pode ser privado ou o Instagram mudou a estrutura. Use o modo manual para adicionar reels.',
    'username' => $username,
    'debug_url' => get_template_directory_uri() . '/instagram-proxy.php?username=' . urlencode($username) . '&debug=1',
    'graphql' => [
        'user' => [
            'username' => $username,
            'edge_owner_to_timeline_media' => [
                'edges' => []
            ]
        ]
    ]
]);
