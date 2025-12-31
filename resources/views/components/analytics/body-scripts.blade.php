{{--
    Analytics Body Scripts Component
    Incluir después de la etiqueta <body> de los layouts principales

    Uso: <x-analytics.body-scripts />
--}}

@php
    $analyticsConfig = \App\Services\AnalyticsService::getConfig();
    $enabled = $analyticsConfig['enabled'] && app()->environment('production');
    $debug = config('analytics.debug', false);
@endphp

@if(($enabled || $debug) && $analyticsConfig['gtm_id'])
{{-- Google Tag Manager (noscript) --}}
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id={{ $analyticsConfig['gtm_id'] }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
@endif
