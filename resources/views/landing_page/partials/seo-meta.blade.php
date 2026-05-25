@php
    $defaultTitle = $layoutConfig->site_title ?? 'Clean Me';
    $defaultDescription = 'Professional cleaning services for residential and commercial spaces in Australia';
    $defaultKeywords = 'cleaning services, house cleaning, commercial cleaning, deep cleaning, Australia';

    $title = $seo->meta_title ?? $defaultTitle;
    $description = $seo->meta_description ?? $defaultDescription;
    $keywords = $seo->meta_keywords ?? $defaultKeywords;

    // Open Graph
    $ogTitle = $seo->og_title ?? $title;
    $ogDescription = $seo->og_description ?? $description;
    $ogType = $seo->og_type ?? 'website';
    $ogImagePath = $seo->og_image_path ?? ($layoutConfig->default_og_image_path ?? null);
    $ogImage = $ogImagePath ? asset($ogImagePath) : null;
    $currentUrl = url()->current();

    // Twitter
    $twitterCard = $seo->twitter_card ?? 'summary_large_image';
    $twitterTitle = $seo->twitter_title ?? $ogTitle;
    $twitterDescription = $seo->twitter_description ?? $ogDescription;
    $twitterImagePath = $seo->twitter_image_path ?? $ogImagePath;
    $twitterImage = $twitterImagePath ? asset($twitterImagePath) : null;

    // Logo URL absoluto (necesario para que Google muestre el logo en resultados de búsqueda)
    $logoUrl = asset('images/logo.png');

    // Schema markup
    $schemaType = $seo->schema_type ?? 'LocalBusiness';
    $schemaData = is_array($seo->schema_data ?? null) ? $seo->schema_data : null;
    if (!$schemaData && $schemaType === 'LocalBusiness' && $layoutConfig) {
        $schemaData = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $layoutConfig->site_title ?? config('app.name'),
            'description' => $description,
            'image' => $logoUrl,
            'logo' => $logoUrl,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $layoutConfig->footer_address ?? null,
                'addressLocality' => $layoutConfig->footer_city ?? null,
                'addressCountry' => 'AU',
            ],
            'telephone' => $layoutConfig->footer_phone ?? null,
            'email' => $layoutConfig->footer_email ?? null,
            'url' => url('/'),
        ];
    } elseif ($schemaData && !isset($schemaData['@context'])) {
        $schemaData = array_merge(['@context' => 'https://schema.org', '@type' => $schemaType], $schemaData);
        if (!isset($schemaData['logo'])) {
            $schemaData['logo'] = $logoUrl;
        }
        if (!isset($schemaData['image'])) {
            $schemaData['image'] = $logoUrl;
        }
    }

    // Schema Organization adicional (Google usa esto específicamente para mostrar el logo de marca)
    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $layoutConfig->site_title ?? config('app.name'),
        'url' => url('/'),
        'logo' => $logoUrl,
    ];
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">

@if(isset($seo) && $seo && $seo->canonical_url)
    <link rel="canonical" href="{{ $seo->canonical_url }}">
@else
    <link rel="canonical" href="{{ $currentUrl }}">
@endif

@if(isset($seo) && $seo && $seo->robots)
    <meta name="robots" content="{{ $seo->robots }}">
@endif

@if($layoutConfig && $layoutConfig->google_search_console_verification)
    <meta name="google-site-verification" content="{{ $layoutConfig->google_search_console_verification }}">
@endif

{{-- Open Graph --}}
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:url" content="{{ $currentUrl }}">
<meta property="og:site_name" content="{{ $layoutConfig->site_title ?? config('app.name') }}">
@if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $twitterTitle }}">
<meta name="twitter:description" content="{{ $twitterDescription }}">
@if($twitterImage)
    <meta name="twitter:image" content="{{ $twitterImage }}">
@endif

{{-- Schema markup JSON-LD --}}
@if($schemaData)
    <script type="application/ld+json">{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
@endif

{{-- Schema Organization para que Google muestre el logo en resultados de búsqueda --}}
<script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
