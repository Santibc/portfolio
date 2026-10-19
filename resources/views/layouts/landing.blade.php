<!DOCTYPE html>
<html lang="es-CO">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $seo['titulo'] }}</title>
    <meta name="description" content="{{ $seo['descripcion'] }}">
    <link rel="canonical" href="{{ url('/') }}">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_CO">
    <meta property="og:site_name" content="{{ $negocio['nombre'] }}">
    <meta property="og:title" content="{{ $seo['titulo'] }}">
    <meta property="og:description" content="{{ $seo['descripcion'] }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset($seo['imagen']) }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">

    @include('layouts.partials.brand-head')

    {{-- Tema (anti-FOUC) y modo 3D, antes de pintar: el 3D se descarta aquí si el usuario pide menos movimiento --}}
    <script>
        (function () {
            var html = document.documentElement;
            var stored = 'auto';
            try { stored = localStorage.getItem('dorilokos-theme') || 'auto'; } catch (e) {}
            var resolved = stored === 'auto'
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : stored;
            html.setAttribute('data-theme', resolved);
            html.setAttribute('data-bs-theme', resolved);
            if (resolved === 'dark') html.classList.add('dark');
            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) html.classList.add('con-3d');

            // Precarga la placa de foil del inicio que corresponde a este tema y a este ancho.
            var placas = {
                dark: ['{{ Vite::asset('resources/images/landing/foil-oscuro-movil.webp') }}', '{{ Vite::asset('resources/images/landing/foil-oscuro-escritorio.webp') }}'],
                light: ['{{ Vite::asset('resources/images/landing/foil-claro-movil.webp') }}', '{{ Vite::asset('resources/images/landing/foil-claro-escritorio.webp') }}']
            };
            var precarga = document.createElement('link');
            precarga.rel = 'preload';
            precarga.as = 'image';
            precarga.href = placas[resolved][window.matchMedia('(min-width: 768px)').matches ? 1 : 0];
            document.head.appendChild(precarga);
        })();
    </script>

    {{-- La fuente del titular (referenciada en landing.css, por eso está en el manifiesto) --}}
    <link rel="preload" as="font" type="font/woff2" href="{{ Vite::asset('resources/fonts/lilita-one-latin-400-normal.woff2') }}" crossorigin>
    <link rel="preload" as="image" href="{{ asset('images/landing/paquete-dorilokos.webp') }}" fetchpriority="high">
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])

    <script type="application/ld+json">{!! json_encode($datosEstructurados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
</head>
<body>
    <a href="#contenido" class="salto">Saltar al contenido</a>

    @yield('contenido')
</body>
</html>
