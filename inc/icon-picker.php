<?php
/**
 * Font Awesome Icon Picker - AJAX Handler
 * Provides list of Font Awesome 4.7 icons available in theme
 */

/**
 * Register AJAX actions for icon picker
 */
add_action( 'wp_ajax_get_fontawesome_icons', 'seisdeagosto_get_fontawesome_icons' );
add_action( 'wp_ajax_nopriv_get_fontawesome_icons', 'seisdeagosto_get_fontawesome_icons' );

/**
 * Get all Font Awesome 4.7 icons
 * Returns JSON array of icon names and labels
 */
function seisdeagosto_get_fontawesome_icons() {
    // Font Awesome 4.7.0 icon list (most commonly used)
    $icons = array(
        // Web Application Icons
        array( 'name' => 'fa-home', 'label' => 'Home' ),
        array( 'name' => 'fa-file', 'label' => 'File' ),
        array( 'name' => 'fa-clock-o', 'label' => 'Clock' ),
        array( 'name' => 'fa-road', 'label' => 'Road' ),
        array( 'name' => 'fa-download', 'label' => 'Download' ),
        array( 'name' => 'fa-inbox', 'label' => 'Inbox' ),
        array( 'name' => 'fa-refresh', 'label' => 'Refresh' ),
        array( 'name' => 'fa-lock', 'label' => 'Lock' ),
        array( 'name' => 'fa-flag', 'label' => 'Flag' ),
        array( 'name' => 'fa-headphones', 'label' => 'Headphones' ),
        array( 'name' => 'fa-volume-off', 'label' => 'Volume Off' ),
        array( 'name' => 'fa-volume-down', 'label' => 'Volume Down' ),
        array( 'name' => 'fa-volume-up', 'label' => 'Volume Up' ),
        array( 'name' => 'fa-qrcode', 'label' => 'QR Code' ),
        array( 'name' => 'fa-barcode', 'label' => 'Barcode' ),
        array( 'name' => 'fa-tag', 'label' => 'Tag' ),
        array( 'name' => 'fa-tags', 'label' => 'Tags' ),
        array( 'name' => 'fa-book', 'label' => 'Book' ),
        array( 'name' => 'fa-bookmark', 'label' => 'Bookmark' ),
        array( 'name' => 'fa-print', 'label' => 'Print' ),
        array( 'name' => 'fa-camera', 'label' => 'Camera' ),
        array( 'name' => 'fa-video-camera', 'label' => 'Video Camera' ),
        array( 'name' => 'fa-image', 'label' => 'Image' ),
        array( 'name' => 'fa-pencil', 'label' => 'Pencil' ),
        array( 'name' => 'fa-map-marker', 'label' => 'Map Marker' ),
        array( 'name' => 'fa-edit', 'label' => 'Edit' ),
        array( 'name' => 'fa-share', 'label' => 'Share' ),
        array( 'name' => 'fa-check', 'label' => 'Check' ),
        array( 'name' => 'fa-times', 'label' => 'Times' ),
        array( 'name' => 'fa-search', 'label' => 'Search' ),
        array( 'name' => 'fa-search-plus', 'label' => 'Search Plus' ),
        array( 'name' => 'fa-search-minus', 'label' => 'Search Minus' ),
        array( 'name' => 'fa-power-off', 'label' => 'Power Off' ),
        array( 'name' => 'fa-signal', 'label' => 'Signal' ),
        array( 'name' => 'fa-cog', 'label' => 'Cog' ),
        array( 'name' => 'fa-trash', 'label' => 'Trash' ),
        array( 'name' => 'fa-trash-o', 'label' => 'Trash Outline' ),
        
        // Directional Icons
        array( 'name' => 'fa-chevron-left', 'label' => 'Chevron Left' ),
        array( 'name' => 'fa-chevron-right', 'label' => 'Chevron Right' ),
        array( 'name' => 'fa-chevron-up', 'label' => 'Chevron Up' ),
        array( 'name' => 'fa-chevron-down', 'label' => 'Chevron Down' ),
        array( 'name' => 'fa-arrow-left', 'label' => 'Arrow Left' ),
        array( 'name' => 'fa-arrow-right', 'label' => 'Arrow Right' ),
        array( 'name' => 'fa-arrow-up', 'label' => 'Arrow Up' ),
        array( 'name' => 'fa-arrow-down', 'label' => 'Arrow Down' ),
        array( 'name' => 'fa-angle-left', 'label' => 'Angle Left' ),
        array( 'name' => 'fa-angle-right', 'label' => 'Angle Right' ),
        array( 'name' => 'fa-angle-up', 'label' => 'Angle Up' ),
        array( 'name' => 'fa-angle-down', 'label' => 'Angle Down' ),
        
        // Social Icons
        array( 'name' => 'fa-facebook', 'label' => 'Facebook' ),
        array( 'name' => 'fa-twitter', 'label' => 'Twitter' ),
        array( 'name' => 'fa-instagram', 'label' => 'Instagram' ),
        array( 'name' => 'fa-youtube', 'label' => 'YouTube' ),
        array( 'name' => 'fa-linkedin', 'label' => 'LinkedIn' ),
        array( 'name' => 'fa-whatsapp', 'label' => 'WhatsApp' ),
        array( 'name' => 'fa-telegram', 'label' => 'Telegram' ),
        
        // Text Editor
        array( 'name' => 'fa-font', 'label' => 'Font' ),
        array( 'name' => 'fa-bold', 'label' => 'Bold' ),
        array( 'name' => 'fa-italic', 'label' => 'Italic' ),
        array( 'name' => 'fa-text-height', 'label' => 'Text Height' ),
        array( 'name' => 'fa-text-width', 'label' => 'Text Width' ),
        array( 'name' => 'fa-align-left', 'label' => 'Align Left' ),
        array( 'name' => 'fa-align-center', 'label' => 'Align Center' ),
        array( 'name' => 'fa-align-right', 'label' => 'Align Right' ),
        array( 'name' => 'fa-align-justify', 'label' => 'Align Justify' ),
        array( 'name' => 'fa-list', 'label' => 'List' ),
        array( 'name' => 'fa-list-ul', 'label' => 'List Unordered' ),
        array( 'name' => 'fa-list-ol', 'label' => 'List Ordered' ),
        
        // News/Media Icons
        array( 'name' => 'fa-newspaper-o', 'label' => 'Newspaper' ),
        array( 'name' => 'fa-microphone', 'label' => 'Microphone' ),
        array( 'name' => 'fa-commenting', 'label' => 'Commenting' ),
        array( 'name' => 'fa-comment', 'label' => 'Comment' ),
        array( 'name' => 'fa-comment-o', 'label' => 'Comment Outline' ),
        array( 'name' => 'fa-comments', 'label' => 'Comments' ),
        array( 'name' => 'fa-comments-o', 'label' => 'Comments Outline' ),
        array( 'name' => 'fa-bullhorn', 'label' => 'Bullhorn' ),
        array( 'name' => 'fa-rss', 'label' => 'RSS' ),
        
        // User Icons
        array( 'name' => 'fa-user', 'label' => 'User' ),
        array( 'name' => 'fa-user-o', 'label' => 'User Outline' ),
        array( 'name' => 'fa-users', 'label' => 'Users' ),
        array( 'name' => 'fa-user-circle', 'label' => 'User Circle' ),
        array( 'name' => 'fa-user-circle-o', 'label' => 'User Circle Outline' ),
        
        // Weather Icons
        array( 'name' => 'fa-sun-o', 'label' => 'Sun' ),
        array( 'name' => 'fa-cloud', 'label' => 'Cloud' ),
        array( 'name' => 'fa-umbrella', 'label' => 'Umbrella' ),
        array( 'name' => 'fa-tint', 'label' => 'Tint' ),
        
        // Star Icons
        array( 'name' => 'fa-star', 'label' => 'Star' ),
        array( 'name' => 'fa-star-o', 'label' => 'Star Outline' ),
        array( 'name' => 'fa-star-half', 'label' => 'Star Half' ),
        array( 'name' => 'fa-star-half-o', 'label' => 'Star Half Outline' ),
        
        // Hand Icons
        array( 'name' => 'fa-hand-pointer-o', 'label' => 'Hand Pointer' ),
        array( 'name' => 'fa-hand-peace-o', 'label' => 'Hand Peace' ),
        array( 'name' => 'fa-thumbs-up', 'label' => 'Thumbs Up' ),
        array( 'name' => 'fa-thumbs-down', 'label' => 'Thumbs Down' ),
        
        // Shopping Icons
        array( 'name' => 'fa-shopping-cart', 'label' => 'Shopping Cart' ),
        array( 'name' => 'fa-shopping-bag', 'label' => 'Shopping Bag' ),
        array( 'name' => 'fa-shopping-basket', 'label' => 'Shopping Basket' ),
        array( 'name' => 'fa-credit-card', 'label' => 'Credit Card' ),
        
        // Medical Icons
        array( 'name' => 'fa-heart', 'label' => 'Heart' ),
        array( 'name' => 'fa-heart-o', 'label' => 'Heart Outline' ),
        array( 'name' => 'fa-heartbeat', 'label' => 'Heartbeat' ),
        array( 'name' => 'fa-medkit', 'label' => 'Medkit' ),
        array( 'name' => 'fa-stethoscope', 'label' => 'Stethoscope' ),
        
        // Currency Icons
        array( 'name' => 'fa-usd', 'label' => 'Dollar' ),
        array( 'name' => 'fa-eur', 'label' => 'Euro' ),
        array( 'name' => 'fa-gbp', 'label' => 'Pound' ),
        array( 'name' => 'fa-money', 'label' => 'Money' ),
        
        // Transport Icons
        array( 'name' => 'fa-car', 'label' => 'Car' ),
        array( 'name' => 'fa-taxi', 'label' => 'Taxi' ),
        array( 'name' => 'fa-bus', 'label' => 'Bus' ),
        array( 'name' => 'fa-plane', 'label' => 'Plane' ),
        array( 'name' => 'fa-ship', 'label' => 'Ship' ),
        array( 'name' => 'fa-bicycle', 'label' => 'Bicycle' ),
        
        // File Type Icons
        array( 'name' => 'fa-file-text', 'label' => 'File Text' ),
        array( 'name' => 'fa-file-pdf-o', 'label' => 'File PDF' ),
        array( 'name' => 'fa-file-word-o', 'label' => 'File Word' ),
        array( 'name' => 'fa-file-excel-o', 'label' => 'File Excel' ),
        array( 'name' => 'fa-file-powerpoint-o', 'label' => 'File PowerPoint' ),
        array( 'name' => 'fa-file-image-o', 'label' => 'File Image' ),
        array( 'name' => 'fa-file-archive-o', 'label' => 'File Archive' ),
        array( 'name' => 'fa-file-audio-o', 'label' => 'File Audio' ),
        array( 'name' => 'fa-file-video-o', 'label' => 'File Video' ),
        array( 'name' => 'fa-file-code-o', 'label' => 'File Code' ),
        
        // Spinner Icons
        array( 'name' => 'fa-spinner', 'label' => 'Spinner' ),
        array( 'name' => 'fa-circle-o-notch', 'label' => 'Circle Notch' ),
        array( 'name' => 'fa-refresh', 'label' => 'Refresh' ),
        
        // Form Control Icons
        array( 'name' => 'fa-check-square', 'label' => 'Check Square' ),
        array( 'name' => 'fa-check-square-o', 'label' => 'Check Square Outline' ),
        array( 'name' => 'fa-check-circle', 'label' => 'Check Circle' ),
        array( 'name' => 'fa-check-circle-o', 'label' => 'Check Circle Outline' ),
        array( 'name' => 'fa-times-circle', 'label' => 'Times Circle' ),
        array( 'name' => 'fa-times-circle-o', 'label' => 'Times Circle Outline' ),
        array( 'name' => 'fa-plus', 'label' => 'Plus' ),
        array( 'name' => 'fa-minus', 'label' => 'Minus' ),
        array( 'name' => 'fa-plus-circle', 'label' => 'Plus Circle' ),
        array( 'name' => 'fa-minus-circle', 'label' => 'Minus Circle' ),
        
        // Charts
        array( 'name' => 'fa-bar-chart', 'label' => 'Bar Chart' ),
        array( 'name' => 'fa-line-chart', 'label' => 'Line Chart' ),
        array( 'name' => 'fa-pie-chart', 'label' => 'Pie Chart' ),
        array( 'name' => 'fa-area-chart', 'label' => 'Area Chart' ),
        
        // Other Popular Icons
        array( 'name' => 'fa-envelope', 'label' => 'Envelope' ),
        array( 'name' => 'fa-envelope-o', 'label' => 'Envelope Outline' ),
        array( 'name' => 'fa-phone', 'label' => 'Phone' ),
        array( 'name' => 'fa-calendar', 'label' => 'Calendar' ),
        array( 'name' => 'fa-calendar-o', 'label' => 'Calendar Outline' ),
        array( 'name' => 'fa-globe', 'label' => 'Globe' ),
        array( 'name' => 'fa-lightbulb-o', 'label' => 'Lightbulb' ),
        array( 'name' => 'fa-gift', 'label' => 'Gift' ),
        array( 'name' => 'fa-fire', 'label' => 'Fire' ),
        array( 'name' => 'fa-trophy', 'label' => 'Trophy' ),
        array( 'name' => 'fa-graduation-cap', 'label' => 'Graduation Cap' ),
        array( 'name' => 'fa-certificate', 'label' => 'Certificate' ),
        array( 'name' => 'fa-wrench', 'label' => 'Wrench' ),
        array( 'name' => 'fa-briefcase', 'label' => 'Briefcase' ),
        array( 'name' => 'fa-suitcase', 'label' => 'Suitcase' ),
        array( 'name' => 'fa-coffee', 'label' => 'Coffee' ),
        array( 'name' => 'fa-cutlery', 'label' => 'Cutlery' ),
        array( 'name' => 'fa-building', 'label' => 'Building' ),
        array( 'name' => 'fa-building-o', 'label' => 'Building Outline' ),
        array( 'name' => 'fa-leaf', 'label' => 'Leaf' ),
        array( 'name' => 'fa-paw', 'label' => 'Paw' ),
        array( 'name' => 'fa-tree', 'label' => 'Tree' ),
        array( 'name' => 'fa-music', 'label' => 'Music' ),
        array( 'name' => 'fa-film', 'label' => 'Film' ),
        array( 'name' => 'fa-key', 'label' => 'Key' ),
        array( 'name' => 'fa-anchor', 'label' => 'Anchor' ),
        array( 'name' => 'fa-bug', 'label' => 'Bug' ),
        array( 'name' => 'fa-puzzle-piece', 'label' => 'Puzzle Piece' ),
        array( 'name' => 'fa-shield', 'label' => 'Shield' ),
        array( 'name' => 'fa-rocket', 'label' => 'Rocket' ),
        array( 'name' => 'fa-bell', 'label' => 'Bell' ),
        array( 'name' => 'fa-bell-o', 'label' => 'Bell Outline' ),
        array( 'name' => 'fa-exclamation', 'label' => 'Exclamation' ),
        array( 'name' => 'fa-exclamation-circle', 'label' => 'Exclamation Circle' ),
        array( 'name' => 'fa-exclamation-triangle', 'label' => 'Exclamation Triangle' ),
        array( 'name' => 'fa-info', 'label' => 'Info' ),
        array( 'name' => 'fa-info-circle', 'label' => 'Info Circle' ),
        array( 'name' => 'fa-question', 'label' => 'Question' ),
        array( 'name' => 'fa-question-circle', 'label' => 'Question Circle' ),
    );
    
    // Allow filtering of icons
    $icons = apply_filters( 'seisdeagosto_fontawesome_icons', $icons );
    
    wp_send_json_success( array( 'icons' => $icons ) );
}

/**
 * Enqueue icon picker assets
 */
function seisdeagosto_enqueue_icon_picker() {
    // Only load in admin
    if ( ! is_admin() ) {
        return;
    }
    
    wp_enqueue_style(
        'seisdeagosto-icon-picker',
        get_template_directory_uri() . '/assets/css/icon-picker.css',
        array(),
        filemtime( get_template_directory() . '/assets/css/icon-picker.css' )
    );
    
    wp_enqueue_script(
        'seisdeagosto-icon-picker',
        get_template_directory_uri() . '/assets/js/icon-picker.js',
        array( 'jquery' ),
        filemtime( get_template_directory() . '/assets/js/icon-picker.js' ),
        true
    );
    
    wp_localize_script(
        'seisdeagosto-icon-picker',
        'seisdeagostoIconPicker',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'seisdeagosto_icon_picker' ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'seisdeagosto_enqueue_icon_picker' );
