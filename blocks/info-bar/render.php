<?php
/**
 * Render callback for Info Bar block
 * Displays location, weather, date and time in a minimalist horizontal bar
 */
function seisdeagosto_render_info_bar( $attributes, $content, $block ) {
    // Extract attributes with defaults
    $city_name    = isset( $attributes['cityName'] ) ? sanitize_text_field( $attributes['cityName'] ) : 'Rio Branco';
    $latitude     = isset( $attributes['latitude'] ) ? sanitize_text_field( $attributes['latitude'] ) : '-9.9749';
    $longitude    = isset( $attributes['longitude'] ) ? sanitize_text_field( $attributes['longitude'] ) : '-67.8103';
    $font_family  = isset( $attributes['fontFamily'] ) ? sanitize_text_field( $attributes['fontFamily'] ) : "'Roboto Condensed', 'Arial Narrow', sans-serif";
    $font_size    = isset( $attributes['fontSize'] ) ? intval( $attributes['fontSize'] ) : 13;
    $show_location = isset( $attributes['showLocation'] ) ? filter_var( $attributes['showLocation'], FILTER_VALIDATE_BOOLEAN ) : true;
    $show_weather  = isset( $attributes['showWeather'] ) ? filter_var( $attributes['showWeather'], FILTER_VALIDATE_BOOLEAN ) : true;
    $show_date     = isset( $attributes['showDate'] ) ? filter_var( $attributes['showDate'], FILTER_VALIDATE_BOOLEAN ) : true;
    $show_time     = isset( $attributes['showTime'] ) ? filter_var( $attributes['showTime'], FILTER_VALIDATE_BOOLEAN ) : true;
    $bg_color      = isset( $attributes['backgroundColor'] ) ? sanitize_hex_color( $attributes['backgroundColor'] ) : '#1a1a1a';
    $text_color    = isset( $attributes['textColor'] ) ? sanitize_hex_color( $attributes['textColor'] ) : '#ffffff';
    
    // Get weather data
    $weather_data = seisdeagosto_get_weather_data( $latitude, $longitude );
    
    // Build wrapper classes
    $wrapper_attributes = get_block_wrapper_attributes( array(
        'class' => 'info-bar-wrapper',
        'style' => sprintf(
            'background-color: %s; color: %s; font-family: %s; font-size: %dpx;',
            esc_attr( $bg_color ),
            esc_attr( $text_color ),
            esc_attr( $font_family ),
            $font_size
        )
    ) );
    
    ob_start();
    ?>
    <div <?php echo $wrapper_attributes; ?>>
        <div class="info-bar-container">
            <?php if ( $show_location ) : ?>
                <div class="info-bar-item info-location">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                    <span><?php echo esc_html( $city_name ); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ( $show_weather && $weather_data ) : ?>
                <div class="info-bar-item info-weather">
                    <?php 
                    // Get weather icon class based on weather code
                    $icon_class = seisdeagosto_get_weather_icon_class( $weather_data['weather_code'] );
                    $fa_icon = seisdeagosto_get_weather_fa_icon( $weather_data['weather_code'] );
                    ?>
                    <div class="weather-icon-mini <?php echo esc_attr( $icon_class ); ?>">
                        <div class="icon-base"></div>
                        <i class="fa <?php echo esc_attr( $fa_icon ); ?> weather-fa-icon-mini" aria-hidden="true"></i>
                    </div>
                    <span><?php echo esc_html( $weather_data['temperature'] ); ?>°C</span>
                </div>
            <?php endif; ?>
            
            <?php if ( $show_date ) : ?>
                <div class="info-bar-item info-date">
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                    <span><?php echo date_i18n( 'l, j \d\e F \d\e Y' ); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ( $show_time ) : ?>
                <div class="info-bar-item info-time">
                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                    <span class="current-time" data-timezone="America/Rio_Branco"><?php echo current_time( 'H:i' ); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ( $show_time ) : ?>
    <script>
    (function() {
        function updateTime() {
            var timeElements = document.querySelectorAll('.info-bar-wrapper .current-time');
            timeElements.forEach(function(el) {
                var now = new Date();
                var hours = String(now.getHours()).padStart(2, '0');
                var minutes = String(now.getMinutes()).padStart(2, '0');
                el.textContent = hours + ':' + minutes;
            });
        }
        
        // Update time every minute
        setInterval(updateTime, 60000);
        updateTime();
    })();
    </script>
    <?php endif; ?>
    <?php
    
    return ob_get_clean();
}

/**
 * Get weather data from Open-Meteo API
 */
function seisdeagosto_get_weather_data( $latitude, $longitude ) {
    // Create cache key
    $cache_key = 'info_bar_weather_' . md5( $latitude . $longitude );
    
    // Try to get cached data (15 minutes cache)
    $cached_data = get_transient( $cache_key );
    if ( false !== $cached_data ) {
        return $cached_data;
    }
    
    // Fetch fresh data from API
    $api_url = add_query_arg( array(
        'latitude'  => $latitude,
        'longitude' => $longitude,
        'current'   => 'temperature_2m,weather_code',
        'timezone'  => 'auto',
    ), 'https://api.open-meteo.com/v1/forecast' );
    
    $response = wp_remote_get( $api_url, array( 'timeout' => 10 ) );
    
    if ( is_wp_error( $response ) ) {
        return null;
    }
    
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    
    if ( empty( $data['current'] ) ) {
        return null;
    }
    
    $weather_data = array(
        'temperature'   => round( $data['current']['temperature_2m'] ),
        'weather_code'  => intval( $data['current']['weather_code'] ),
    );
    
    // Cache for 15 minutes
    set_transient( $cache_key, $weather_data, 15 * MINUTE_IN_SECONDS );
    
    return $weather_data;
}

/**
 * Map weather code to icon class (reusing clima tempo classes)
 */
function seisdeagosto_get_weather_icon_class( $code ) {
    if ( $code === 0 ) {
        return 'icon-clear';
    } elseif ( $code >= 1 && $code <= 3 ) {
        return 'icon-cloudy';
    } elseif ( $code >= 45 && $code <= 48 ) {
        return 'icon-cloudy';
    } elseif ( ( $code >= 51 && $code <= 57 ) || ( $code >= 61 && $code <= 67 ) || ( $code >= 80 && $code <= 82 ) ) {
        return 'icon-rain';
    } elseif ( $code >= 71 && $code <= 77 ) {
        return 'icon-snow';
    } elseif ( $code >= 95 && $code <= 99 ) {
        return 'icon-storm';
    }
    return 'icon-clear';
}

/**
 * Map weather code to Font Awesome icon
 */
function seisdeagosto_get_weather_fa_icon( $code ) {
    if ( $code === 0 ) {
        return 'fa-sun-o';
    } elseif ( $code >= 1 && $code <= 3 ) {
        return 'fa-cloud';
    } elseif ( $code >= 45 && $code <= 48 ) {
        return 'fa-cloud';
    } elseif ( ( $code >= 51 && $code <= 57 ) || ( $code >= 61 && $code <= 67 ) || ( $code >= 80 && $code <= 82 ) ) {
        return 'fa-tint';
    } elseif ( $code >= 71 && $code <= 77 ) {
        return 'fa-snowflake-o';
    } elseif ( $code >= 95 && $code <= 99 ) {
        return 'fa-bolt';
    }
    return 'fa-sun-o';
}
