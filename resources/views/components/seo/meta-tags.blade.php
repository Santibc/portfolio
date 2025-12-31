{{--
    SEO Meta Tags Component

    Uso:
    @include('components.seo.meta-tags', [
        'title' => 'Título de la página',
        'description' => 'Descripción de la página',
        'keywords' => 'palabras, clave, seo',
        'image' => 'url/imagen.jpg',
        'type' => 'website|product|article',
        'producto' => $producto, // opcional, para páginas de producto
        'empresa' => $empresa
    ])
--}}

@php
    $pageTitle = isset($title) ? $title . ' | ' . $empresa->nombre : $empresa->nombre . ' - Tienda Online';
    $pageDescription = isset($description) ? $description : ($empresa->descripcion ?? 'Tienda online de ' . $empresa->nombre);
    $pageKeywords = isset($keywords) ? $keywords : 'flores, ramos, arreglos florales, ' . $empresa->nombre;
    $pageImage = isset($image) ? $image : ($empresa->logo_url ?? asset('images/default-og.jpg'));
    $pageType = isset($type) ? $type : 'website';
    $canonicalUrl = url()->current();
@endphp

{{-- Meta Tags Básicos --}}
<meta name="description" content="{{ Str::limit(strip_tags($pageDescription), 160) }}">
<meta name="keywords" content="{{ $pageKeywords }}">
<meta name="author" content="{{ $empresa->nombre }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ $canonicalUrl }}">

{{-- Open Graph Meta Tags --}}
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($pageDescription), 200) }}">
<meta property="og:type" content="{{ $pageType }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $pageImage }}">
<meta property="og:image:alt" content="{{ $pageTitle }}">
<meta property="og:site_name" content="{{ $empresa->nombre }}">
<meta property="og:locale" content="es_CO">

{{-- Twitter Card Meta Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ Str::limit(strip_tags($pageDescription), 200) }}">
<meta name="twitter:image" content="{{ $pageImage }}">

{{-- Product Specific Meta (para páginas de producto) --}}
@if(isset($producto))
    <meta property="og:type" content="product">
    <meta property="product:price:amount" content="{{ $producto->precio_actual->precio ?? 0 }}">
    <meta property="product:price:currency" content="COP">
    @if($producto->stockPrincipal && $producto->stockPrincipal->cantidad > 0)
        <meta property="product:availability" content="in stock">
    @else
        <meta property="product:availability" content="out of stock">
    @endif
    <meta property="product:category" content="{{ $producto->categoria->nombre ?? 'Flores' }}">
    <meta property="product:brand" content="{{ $empresa->nombre }}">
@endif

{{-- Geo Tags (para SEO local) --}}
@if($empresa->ciudad)
    <meta name="geo.region" content="CO-{{ $empresa->ciudad->departamento->codigo ?? '' }}">
    <meta name="geo.placename" content="{{ $empresa->ciudad->nombre ?? '' }}, Colombia">
@endif

{{-- Additional SEO Helpers --}}
<meta name="format-detection" content="telephone=no">
<meta name="theme-color" content="#e91e63">
<meta name="msapplication-TileColor" content="#e91e63">
