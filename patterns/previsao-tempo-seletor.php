<?php
/**
 * Title: Previsão do Tempo com Seletor de Cidades
 * Slug: seisdeagosto/previsao-tempo-seletor
 * Categories: widgets, utility, seisdeagosto
 * Description: Widget de clima com seletor de cidades usando API de geocoding gratuita.
 * Viewport Width: 800
 */
?>
<!-- wp:group {"className":"weather-widget-container p-3 rounded","style":{"color":{"background":"#0a4579"}}} -->
<div class="wp-block-group weather-widget-container p-3 rounded has-background" style="background-color:#0a4579">

<!-- wp:heading {"level":4,"style":{"color":{"text":"#ffffff"}}} -->
<h4 class="wp-block-heading has-text-color" style="color:#ffffff"><i class="fa fa-cloud"></i> Previsão do Tempo</h4>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[u68_weather_selector default_city="Rio Branco" default_lat="-9.975" default_lon="-67.824" theme="dark" show_forecast="true" forecast_days="5"]
<!-- /wp:shortcode -->

</div>
<!-- /wp:group -->
