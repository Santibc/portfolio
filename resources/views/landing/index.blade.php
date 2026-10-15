@php
    $lugar = trim(($negocio->barrio ? $negocio->barrio.', ' : '').$negocio->ciudad);
    $titulo = $negocio->nombre.' · Panadería y pastelería en '.$lugar;
    $descripcion = 'Pan artesanal, pastelería y tortas por encargo en '.$lugar.'. Haz tu pedido por WhatsApp al '.$negocio->telefonoVisible().'.';
    $imagenSocial = asset('images/landing/og-1200.jpg');
    $fotoHero = $fotos->get('hero');
    // Anclas del menú (nav y footer): solo las secciones que tienen contenido
    $enlaces = array_filter([
        'historia' => 'Nosotros',
        'productos' => $productos->isNotEmpty() ? 'Productos' : null,
        'proceso' => $pasos->isNotEmpty() ? 'Proceso' : null,
        'galeria' => $galeria->isNotEmpty() ? 'Galería' : null,
        'visitanos' => 'Visítanos',
    ]);
@endphp
<!DOCTYPE html>
<html lang="es-CO">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo }}</title>
    <meta name="description" content="{{ $descripcion }}">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta name="theme-color" content="#fffdfa" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1a1610" media="(prefers-color-scheme: dark)">

    {{-- Redes sociales / WhatsApp --}}
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_CO">
    <meta property="og:site_name" content="{{ $negocio->nombre }}">
    <meta property="og:title" content="{{ $titulo }}">
    <meta property="og:description" content="{{ $descripcion }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ $imagenSocial }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">

    @if ($fotoHero?->hasImagen())
        <link rel="preload" as="image" href="{{ $fotoHero->imagen_url }}" fetchpriority="high"
              @if ($fotoHero->imagen_srcset) imagesrcset="{{ $fotoHero->imagen_srcset }}" imagesizes="(min-width: 1024px) 30rem, 90vw" @endif>
    @endif

    {{-- Tema (anti-FOUC, misma clave que el panel) + estado inicial de animaciones --}}
    <script>
        (function () {
            var html = document.documentElement;
            var guardado = null;
            try { guardado = localStorage.getItem('sopas-theme'); } catch (e) {}
            var preferencia = guardado || 'auto';
            var tema = preferencia === 'auto'
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : preferencia;
            html.setAttribute('data-theme', tema);
            html.setAttribute('data-bs-theme', tema);
            if (tema === 'dark') html.classList.add('dark');

            // Oculta lo que se va a animar solo si hay movimiento permitido; si el JS
            // no llega a cargar, a los 4 s se quita la clase y todo queda visible.
            if (window.matchMedia('(prefers-reduced-motion: no-preference)').matches) {
                html.classList.add('anim');
                setTimeout(function () { if (!window.__landingLista) html.classList.remove('anim'); }, 4000);
            }
        })();
    </script>

    @vite(['resources/css/landing.css', 'resources/js/landing.js'])

    <script type="application/ld+json">{!! json_encode($negocio->datosEstructurados(url('/'), $imagenSocial), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
</head>
<body class="grano overflow-x-clip">
    <a href="#contenido"
       class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[70] focus:rounded-full focus:bg-primary-500 focus:px-5 focus:py-3 focus:text-sm focus:font-semibold focus:text-white">
        Saltar al contenido
    </a>

    @include('landing.partials.nav')

    <main id="contenido">
        @include('landing.partials.hero')
        @include('landing.partials.marquesina')
        @include('landing.partials.historia')
        {{-- Las listas vacías (sin contenido en el panel) ocultan su sección --}}
        @includeWhen($productos->isNotEmpty(), 'landing.partials.productos')
        @includeWhen($pasos->isNotEmpty(), 'landing.partials.proceso')
        @include('landing.partials.encargos')
        @includeWhen($valores->isNotEmpty(), 'landing.partials.valores')
        @include('landing.partials.mision')
        @includeWhen($galeria->isNotEmpty(), 'landing.partials.galeria')
        @include('landing.partials.visitanos')
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.whatsapp')
</body>
</html>
