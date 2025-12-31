{{--
    Analytics Head Scripts Component
    Incluir en el <head> de los layouts principales

    Uso: <x-analytics.head-scripts />
--}}

@php
    $analyticsConfig = \App\Services\AnalyticsService::getConfig();
    $enabled = $analyticsConfig['enabled'] && app()->environment('production');
    $debug = config('analytics.debug', false);
@endphp

@if($enabled || $debug)

{{-- Google Tag Manager (si está configurado) --}}
@if($analyticsConfig['gtm_id'])
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $analyticsConfig['gtm_id'] }}');</script>
@endif

{{-- Google Analytics 4 --}}
@if($analyticsConfig['ga4_id'])
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $analyticsConfig['ga4_id'] }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $analyticsConfig['ga4_id'] }}', {
        'send_page_view': true,
        'debug_mode': {{ $debug ? 'true' : 'false' }}
    });

    // Helper global para enviar eventos
    window.trackGA4Event = function(eventName, params) {
        if (typeof gtag === 'function') {
            gtag('event', eventName, params);
            @if($debug)
            console.log('[GA4 Event]', eventName, params);
            @endif
        }
    };
</script>
@endif

{{-- Facebook Pixel --}}
@if($analyticsConfig['fb_pixel_id'])
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');

    fbq('init', '{{ $analyticsConfig['fb_pixel_id'] }}');
    fbq('track', 'PageView');

    // Helper global para enviar eventos
    window.trackFBEvent = function(eventName, params) {
        if (typeof fbq === 'function') {
            fbq('track', eventName, params);
            @if($debug)
            console.log('[FB Pixel Event]', eventName, params);
            @endif
        }
    };
</script>
<noscript>
    <img height="1" width="1" style="display:none"
         src="https://www.facebook.com/tr?id={{ $analyticsConfig['fb_pixel_id'] }}&ev=PageView&noscript=1"/>
</noscript>
@endif

{{-- Helper unificado para tracking --}}
<script>
    window.trackEvent = function(eventName, ga4Params, fbParams) {
        // GA4
        if (typeof window.trackGA4Event === 'function' && ga4Params) {
            window.trackGA4Event(eventName, ga4Params);
        }

        // Facebook Pixel (mapeo de nombres de eventos)
        if (typeof window.trackFBEvent === 'function' && fbParams) {
            var fbEventName = {
                'view_item': 'ViewContent',
                'add_to_cart': 'AddToCart',
                'begin_checkout': 'InitiateCheckout',
                'add_payment_info': 'AddPaymentInfo',
                'purchase': 'Purchase',
                'search': 'Search',
                'view_item_list': 'ViewCategory'
            }[eventName] || eventName;

            window.trackFBEvent(fbEventName, fbParams);
        }
    };
</script>

{{-- Scripts personalizados (Google Ads, TikTok Pixel, etc.) --}}
@if(!empty($analyticsConfig['custom_scripts']))
{!! $analyticsConfig['custom_scripts'] !!}
@endif

@endif
