<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Analytics Enabled
    |--------------------------------------------------------------------------
    |
    | Habilitar o deshabilitar tracking de analytics globalmente.
    | Útil para desactivar en desarrollo.
    |
    */
    'enabled' => env('ANALYTICS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Google Analytics 4 Measurement ID
    |--------------------------------------------------------------------------
    |
    | El ID de medición de GA4 (formato: G-XXXXXXXXXX)
    | Obtenerlo desde: Google Analytics > Admin > Data Streams > Web
    |
    */
    'ga4_measurement_id' => env('GA4_MEASUREMENT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Facebook Pixel ID
    |--------------------------------------------------------------------------
    |
    | El ID del Pixel de Facebook/Meta
    | Obtenerlo desde: Meta Business Suite > Events Manager > Data Sources
    |
    */
    'fb_pixel_id' => env('FB_PIXEL_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Google Tag Manager Container ID
    |--------------------------------------------------------------------------
    |
    | El ID del contenedor de GTM (formato: GTM-XXXXXXX)
    | Opcional: si usas GTM, puedes manejar GA4 y FB Pixel desde ahí
    |
    */
    'gtm_container_id' => env('GTM_CONTAINER_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | Habilitar modo debug para ver eventos en consola del navegador.
    |
    */
    'debug' => env('ANALYTICS_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Eventos habilitados
    |--------------------------------------------------------------------------
    |
    | Configurar qué eventos de e-commerce están activos.
    |
    */
    'events' => [
        'page_view' => true,
        'view_item' => true,
        'view_item_list' => true,
        'add_to_cart' => true,
        'remove_from_cart' => true,
        'begin_checkout' => true,
        'add_shipping_info' => true,
        'add_payment_info' => true,
        'purchase' => true,
        'search' => true,
    ],
];
