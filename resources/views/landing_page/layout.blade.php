<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta --}}
    <title>{{ $seo->meta_title ?? ($layoutConfig->site_title ?? 'Manzer Agroforestal') }}</title>
    <meta name="description" content="{{ $seo->meta_description ?? 'Expertos en trabajos forestales y agroforestales en Lleida. Tala en altura, poda, desbroces y prevencion de incendios.' }}">
    <meta name="keywords" content="{{ $seo->meta_keywords ?? 'trabajos forestales, tala en altura, poda, desbroces, prevencion incendios, Lleida, agroforestal' }}">

    @if ($seo && $seo->canonical_url)
        <link rel="canonical" href="{{ $seo->canonical_url }}">
    @else
        <link rel="canonical" href="{{ url()->current() }}">
    @endif

    @if ($seo && $seo->robots)
        <meta name="robots" content="{{ $seo->robots }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $seo->og_title ?? $seo->meta_title ?? ($layoutConfig->site_title ?? 'Manzer Agroforestal') }}">
    <meta property="og:description" content="{{ $seo->og_description ?? $seo->meta_description ?? '' }}">
    <meta property="og:image" content="{{ $seo && $seo->og_image ? asset($seo->og_image) : asset('images/og-default.jpg') }}">
    <meta property="og:type" content="{{ $seo->og_type ?? 'website' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="es_ES">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Schema.org LocalBusiness --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "Manzer Agroforestal, S.L.",
        "description": "Empresa de servicios forestales y agroforestales en Lleida",
        "url": "{{ url('/') }}",
        "telephone": "+34698989666",
        "email": "contacto@manzeragroforestal.es",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "C/ Major, 54",
            "addressLocality": "Menarguens",
            "addressRegion": "Lleida",
            "postalCode": "25139",
            "addressCountry": "ES"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": 41.7442,
            "longitude": 0.7228
        },
        "sameAs": [
            "https://www.instagram.com/manzer_agroforestal"
        ]
    }
    </script>

    @stack('schema')

    {{-- Favicon --}}
    <link href="{{ asset('images/manzer-favicon.png') }}" rel="icon">
    <link href="{{ asset('images/manzer-favicon.png') }}" rel="apple-touch-icon">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Swiper --}}
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">

    {{-- LightGallery --}}
    <link href="https://cdn.jsdelivr.net/npm/lightgallery@2.7/css/lightgallery-bundle.min.css" rel="stylesheet">

    {{-- Splitting.js --}}
    <link href="https://cdn.jsdelivr.net/npm/splitting/dist/splitting.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/splitting/dist/splitting-cells.css" rel="stylesheet">

    {{-- Main Manzer Styles --}}
    <style>
        :root {
            --manzer-green: #39ff14;
            --manzer-green-muted: #2d8a2d;
            --manzer-forest: #1b4332;
            --manzer-forest-light: #2d6a4f;
            --manzer-dark: #0d1f0d;
            --manzer-darker: #080f08;
            --manzer-earth: #3d2b1f;
            --manzer-bark: #5c4033;
            --manzer-light: #f0f5f0;
            --manzer-cream: #faf8f2;
            --manzer-white: #ffffff;
            --manzer-gray: #6b7280;
            --manzer-transition: cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: auto;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            background: var(--manzer-cream);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: color 0.3s ease;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ===== PRELOADER ===== */
        #preloader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: var(--manzer-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.6s ease, visibility 0.6s ease;
        }
        #preloader::after {
            content: '';
            width: 50px;
            height: 50px;
            border: 3px solid rgba(57, 255, 20, 0.2);
            border-top-color: var(--manzer-green);
            border-radius: 50%;
            animation: preloaderSpin 0.8s linear infinite;
        }
        #preloader.loaded {
            opacity: 0;
            visibility: hidden;
        }
        @keyframes preloaderSpin {
            to { transform: rotate(360deg); }
        }

        /* ===== HEADER / NAVBAR ===== */
        .manzer-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 20px 0;
            transition: all 0.4s var(--manzer-transition);
        }
        .manzer-header.scrolled {
            background: rgba(13, 31, 13, 0.95);
            backdrop-filter: blur(20px);
            padding: 12px 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }
        .manzer-header .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .manzer-header .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .manzer-header .logo img {
            height: 50px;
            width: auto;
            transition: height 0.4s ease;
        }
        .manzer-header.scrolled .logo img {
            height: 40px;
        }
        .manzer-header .logo-text {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--manzer-white);
            letter-spacing: 1px;
        }
        .manzer-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .manzer-nav a {
            color: rgba(255, 255, 255, 0.85);
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 10px 20px;
            border-radius: 30px;
            transition: all 0.3s ease;
            position: relative;
        }
        .manzer-nav a:hover,
        .manzer-nav a.active {
            color: var(--manzer-green);
        }
        .manzer-nav a.nav-cta {
            background: var(--manzer-green);
            color: var(--manzer-dark);
            font-weight: 700;
        }
        .manzer-nav a.nav-cta:hover {
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(57, 255, 20, 0.3);
        }

        /* Mobile Menu */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--manzer-white);
            font-size: 1.8rem;
            cursor: pointer;
            padding: 5px;
            z-index: 1001;
        }
        .mobile-menu {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(13, 31, 13, 0.98);
            z-index: 999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
        }
        .mobile-menu.open {
            opacity: 1;
            visibility: visible;
        }
        .mobile-menu a {
            color: var(--manzer-white);
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 15px 30px;
            transition: color 0.3s ease;
        }
        .mobile-menu a:hover,
        .mobile-menu a.active {
            color: var(--manzer-green);
        }

        @media (max-width: 991px) {
            .manzer-nav { display: none; }
            .mobile-toggle { display: block; }
            .mobile-menu { display: flex; }
            .manzer-header .header-inner { padding: 0 20px; }
        }

        /* ===== SECTION STYLES ===== */
        .section {
            padding: 100px 0;
            position: relative;
        }
        .section-dark {
            background: var(--manzer-dark);
            color: var(--manzer-white);
        }
        .section-forest {
            background: var(--manzer-forest);
            color: var(--manzer-white);
        }
        .section-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            margin-bottom: 20px;
            line-height: 1.1;
        }
        .section-subtitle {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            font-size: 1.1rem;
            color: var(--manzer-gray);
            max-width: 600px;
        }
        .section-dark .section-subtitle {
            color: rgba(255, 255, 255, 0.6);
        }
        .section-label {
            display: inline-block;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--manzer-green);
            margin-bottom: 16px;
        }

        /* ===== BUTTONS ===== */
        .btn-manzer {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 36px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            transition: all 0.4s var(--manzer-transition);
            position: relative;
            overflow: hidden;
        }
        .btn-manzer-primary {
            background: var(--manzer-green);
            color: var(--manzer-dark);
        }
        .btn-manzer-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 40px rgba(57, 255, 20, 0.3);
        }
        .btn-manzer-outline {
            background: transparent;
            color: var(--manzer-white);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        .btn-manzer-outline:hover {
            border-color: var(--manzer-green);
            color: var(--manzer-green);
            transform: translateY(-3px);
        }
        .btn-manzer-dark {
            background: var(--manzer-dark);
            color: var(--manzer-white);
        }
        .btn-manzer-dark:hover {
            background: var(--manzer-forest);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        /* ===== FOOTER ===== */
        .manzer-footer {
            background: var(--manzer-darker);
            color: rgba(255, 255, 255, 0.7);
            padding: 80px 0 30px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 60px;
            margin-bottom: 60px;
        }
        .footer-brand img {
            height: 60px;
            margin-bottom: 20px;
        }
        .footer-brand p {
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 24px;
        }
        .footer-social {
            display: flex;
            gap: 12px;
        }
        .footer-social a {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .footer-social a:hover {
            background: var(--manzer-green);
            color: var(--manzer-dark);
            transform: translateY(-3px);
        }
        .footer-title {
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--manzer-white);
            margin-bottom: 24px;
        }
        .footer-links {
            list-style: none;
            padding: 0;
        }
        .footer-links li {
            margin-bottom: 12px;
        }
        .footer-links a {
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .footer-links a:hover {
            color: var(--manzer-green);
            padding-left: 5px;
        }
        .footer-contact-item {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }
        .footer-contact-item i {
            color: var(--manzer-green);
            font-size: 1.1rem;
            margin-top: 2px;
        }
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 30px;
            text-align: center;
            font-size: 0.85rem;
        }

        @media (max-width: 991px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 40px;
            }
        }
        @media (max-width: 575px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }

        /* ===== WHATSAPP FLOAT ===== */
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: #25d366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 6px 30px rgba(37, 211, 102, 0.4);
            z-index: 998;
            transition: all 0.3s ease;
            animation: whatsappPulse 2s infinite;
        }
        .whatsapp-float:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 40px rgba(37, 211, 102, 0.5);
            color: white;
        }
        @keyframes whatsappPulse {
            0%, 100% { box-shadow: 0 6px 30px rgba(37, 211, 102, 0.4); }
            50% { box-shadow: 0 6px 30px rgba(37, 211, 102, 0.7); }
        }

        /* ===== SCROLL TOP ===== */
        .scroll-top-btn {
            position: fixed;
            bottom: 100px;
            right: 34px;
            width: 44px;
            height: 44px;
            background: var(--manzer-forest);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--manzer-green);
            font-size: 1.2rem;
            z-index: 997;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
            cursor: pointer;
            border: none;
        }
        .scroll-top-btn.visible {
            opacity: 1;
            visibility: visible;
        }
        .scroll-top-btn:hover {
            background: var(--manzer-green);
            color: var(--manzer-dark);
            transform: translateY(-3px);
        }

        /* ===== REVEAL ANIMATIONS ===== */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
        }
        .reveal-left {
            opacity: 0;
            transform: translateX(-60px);
        }
        .reveal-right {
            opacity: 0;
            transform: translateX(60px);
        }
        .reveal-scale {
            opacity: 0;
            transform: scale(0.9);
        }

        /* ===== CUSTOM CURSOR ===== */
        @media (hover: hover) {
            .cursor-dot {
                position: fixed;
                width: 8px;
                height: 8px;
                background: var(--manzer-green);
                border-radius: 50%;
                pointer-events: none;
                z-index: 10000;
                mix-blend-mode: difference;
                transition: transform 0.15s ease;
            }
            .cursor-ring {
                position: fixed;
                width: 40px;
                height: 40px;
                border: 2px solid rgba(57, 255, 20, 0.3);
                border-radius: 50%;
                pointer-events: none;
                z-index: 10000;
                mix-blend-mode: difference;
                transition: transform 0.3s ease, width 0.3s ease, height 0.3s ease;
            }
            .cursor-ring.hover {
                width: 60px;
                height: 60px;
                border-color: rgba(57, 255, 20, 0.6);
            }
        }

        /* ===== UTILITIES ===== */
        .text-green { color: var(--manzer-green); }
        .bg-forest { background: var(--manzer-forest); }
        .overflow-hidden { overflow: hidden; }
    </style>

    @stack('styles')

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17792196133"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'AW-17792196133');
    </script>
</head>

<body>
    {{-- Preloader --}}
    <div id="preloader"></div>

    {{-- Custom Cursor --}}
    <div class="cursor-dot" id="cursorDot"></div>
    <div class="cursor-ring" id="cursorRing"></div>

    {{-- Header --}}
    <header class="manzer-header" id="header">
        <div class="header-inner">
            <a href="{{ route('welcome') }}" class="logo">
                @if ($layoutConfig && $layoutConfig->footer_logo_path)
                    <img src="{{ asset($layoutConfig->footer_logo_path) }}" alt="Manzer Agroforestal">
                @else
                    <img src="{{ asset('images/manzer-logo.png') }}" alt="Manzer Agroforestal">
                @endif
                <span class="logo-text">MANZER</span>
            </a>

            <nav class="manzer-nav">
                <a href="{{ route('welcome') }}" @if(Route::currentRouteName() == 'welcome') class="active" @endif>Inicio</a>
                <a href="{{ route('servicios') }}" @if(str_contains(Route::currentRouteName() ?? '', 'servicio')) class="active" @endif>Servicios</a>
                <a href="{{ route('nosotros') }}" @if(Route::currentRouteName() == 'nosotros') class="active" @endif>Nosotros</a>
                <a href="{{ route('blog.index') }}" @if(str_contains(Route::currentRouteName() ?? '', 'blog')) class="active" @endif>Blog</a>
                <a href="{{ route('contacto') }}" class="nav-cta">Contacto</a>
            </nav>

            <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
                <i class="bi bi-list" id="mobileToggleIcon"></i>
            </button>
        </div>
    </header>

    {{-- Mobile Menu --}}
    <div class="mobile-menu" id="mobileMenu">
        <a href="{{ route('welcome') }}" @if(Route::currentRouteName() == 'welcome') class="active" @endif>Inicio</a>
        <a href="{{ route('servicios') }}" @if(str_contains(Route::currentRouteName() ?? '', 'servicio')) class="active" @endif>Servicios</a>
        <a href="{{ route('nosotros') }}" @if(Route::currentRouteName() == 'nosotros') class="active" @endif>Nosotros</a>
        <a href="{{ route('blog.index') }}" @if(str_contains(Route::currentRouteName() ?? '', 'blog')) class="active" @endif>Blog</a>
        <a href="{{ route('contacto') }}">Contacto</a>
    </div>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="manzer-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    @if ($layoutConfig && $layoutConfig->footer_logo_path)
                        <img src="{{ asset($layoutConfig->footer_logo_path) }}" alt="Manzer Agroforestal">
                    @else
                        <img src="{{ asset('images/manzer-logo.png') }}" alt="Manzer Agroforestal">
                    @endif
                    <p>{{ $layoutConfig->footer_description ?? 'Expertos en trabajos forestales y agroforestales. Seguridad, calidad y compromiso medioambiental en cada proyecto.' }}</p>
                    <div class="footer-social">
                        @if ($layoutConfig && $layoutConfig->instagram_url)
                            <a href="{{ $layoutConfig->instagram_url }}" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        @endif
                        @if ($layoutConfig && $layoutConfig->facebook_url)
                            <a href="{{ $layoutConfig->facebook_url }}" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        @endif
                        @if ($layoutConfig && $layoutConfig->whatsapp_url)
                            <a href="{{ $layoutConfig->whatsapp_url }}" target="_blank" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        @endif
                        @if ($layoutConfig && $layoutConfig->tiktok_url)
                            <a href="{{ $layoutConfig->tiktok_url }}" target="_blank" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                        @endif
                        @if ($layoutConfig && $layoutConfig->linkedin_url)
                            <a href="{{ $layoutConfig->linkedin_url }}" target="_blank" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        @endif
                    </div>
                </div>

                <div>
                    <h4 class="footer-title">Empresa</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('welcome') }}">Inicio</a></li>
                        <li><a href="{{ route('servicios') }}">Servicios</a></li>
                        <li><a href="{{ route('nosotros') }}">Nosotros</a></li>
                        <li><a href="{{ route('blog.index') }}">Blog</a></li>
                        <li><a href="{{ route('contacto') }}">Contacto</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-title">Servicios</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('servicios') }}">Tala en altura</a></li>
                        <li><a href="{{ route('servicios') }}">Poda en altura</a></li>
                        <li><a href="{{ route('servicios') }}">Desbroces</a></li>
                        <li><a href="{{ route('servicios') }}">Prevencion de incendios</a></li>
                        <li><a href="{{ route('servicios') }}">Retirada de arboles</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-title">Contacto</h4>
                    <div class="footer-contact-item">
                        <i class="bi bi-geo-alt"></i>
                        <span>{{ $layoutConfig->footer_address ?? 'C/ Major, 54' }}<br>{{ $layoutConfig->footer_city ?? '25139 Menarguens, Lleida' }}</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-telephone"></i>
                        <a href="tel:{{ $layoutConfig->footer_phone ?? '+34698989666' }}">{{ $layoutConfig->footer_phone ?? '+34 698 98 96 66' }}</a>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-envelope"></i>
                        <a href="mailto:{{ $layoutConfig->footer_email ?? 'contacto@manzeragroforestal.es' }}">{{ $layoutConfig->footer_email ?? 'contacto@manzeragroforestal.es' }}</a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ $layoutConfig->copyright_company ?? 'Manzer Agroforestal, S.L.' }} — Todos los derechos reservados</p>
            </div>
        </div>
    </footer>

    {{-- WhatsApp Float --}}
    <a href="https://wa.me/34698989666" target="_blank" class="whatsapp-float" aria-label="WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    {{-- Scroll to Top --}}
    <button class="scroll-top-btn" id="scrollTopBtn" aria-label="Volver arriba">
        <i class="bi bi-arrow-up"></i>
    </button>

    {{-- JS Libraries (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/splitting/dist/splitting.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7/lightgallery.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7/plugins/zoom/lg-zoom.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanilla-tilt@1.8.1/dist/vanilla-tilt.min.js"></script>

    {{-- Core Manzer JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Remove preloader
            setTimeout(() => {
                document.getElementById('preloader').classList.add('loaded');
            }, 500);

            // ===== LENIS SMOOTH SCROLL =====
            const lenis = new Lenis({
                duration: 1.2,
                easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                smoothWheel: true,
            });
            function raf(time) {
                lenis.raf(time);
                requestAnimationFrame(raf);
            }
            requestAnimationFrame(raf);

            // Connect Lenis to GSAP ScrollTrigger
            lenis.on('scroll', ScrollTrigger.update);
            gsap.ticker.add((time) => lenis.raf(time * 1000));
            gsap.ticker.lagSmoothing(0);

            // ===== HEADER SCROLL =====
            const header = document.getElementById('header');
            ScrollTrigger.create({
                start: 'top -80',
                onUpdate: (self) => {
                    if (self.scroll() > 80) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                }
            });

            // ===== MOBILE MENU =====
            const mobileToggle = document.getElementById('mobileToggle');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileIcon = document.getElementById('mobileToggleIcon');

            if (mobileToggle) {
                mobileToggle.addEventListener('click', () => {
                    mobileMenu.classList.toggle('open');
                    mobileIcon.classList.toggle('bi-list');
                    mobileIcon.classList.toggle('bi-x');
                });
                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        mobileMenu.classList.remove('open');
                        mobileIcon.classList.add('bi-list');
                        mobileIcon.classList.remove('bi-x');
                    });
                });
            }

            // ===== GSAP REVEAL ANIMATIONS =====
            gsap.registerPlugin(ScrollTrigger);

            gsap.utils.toArray('.reveal').forEach(el => {
                gsap.to(el, {
                    opacity: 1,
                    y: 0,
                    duration: 1,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 85%',
                        once: true
                    }
                });
            });

            gsap.utils.toArray('.reveal-left').forEach(el => {
                gsap.to(el, {
                    opacity: 1,
                    x: 0,
                    duration: 1,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 85%',
                        once: true
                    }
                });
            });

            gsap.utils.toArray('.reveal-right').forEach(el => {
                gsap.to(el, {
                    opacity: 1,
                    x: 0,
                    duration: 1,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 85%',
                        once: true
                    }
                });
            });

            gsap.utils.toArray('.reveal-scale').forEach(el => {
                gsap.to(el, {
                    opacity: 1,
                    scale: 1,
                    duration: 1,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 85%',
                        once: true
                    }
                });
            });

            // Stagger children reveal
            gsap.utils.toArray('.stagger-children').forEach(container => {
                const children = container.querySelectorAll('.stagger-item');
                gsap.fromTo(children,
                    { opacity: 0, y: 40 },
                    {
                        opacity: 1, y: 0,
                        duration: 0.8,
                        stagger: 0.15,
                        ease: 'power3.out',
                        scrollTrigger: {
                            trigger: container,
                            start: 'top 80%',
                            once: true
                        }
                    }
                );
            });

            // ===== SCROLL TO TOP =====
            const scrollTopBtn = document.getElementById('scrollTopBtn');
            ScrollTrigger.create({
                start: 'top -400',
                onUpdate: (self) => {
                    if (self.scroll() > 400) {
                        scrollTopBtn.classList.add('visible');
                    } else {
                        scrollTopBtn.classList.remove('visible');
                    }
                }
            });
            scrollTopBtn.addEventListener('click', () => {
                lenis.scrollTo(0, { duration: 1.5 });
            });

            // ===== CUSTOM CURSOR =====
            const cursorDot = document.getElementById('cursorDot');
            const cursorRing = document.getElementById('cursorRing');
            if (cursorDot && window.matchMedia('(hover: hover)').matches) {
                let mouseX = 0, mouseY = 0;
                let dotX = 0, dotY = 0;
                let ringX = 0, ringY = 0;

                document.addEventListener('mousemove', (e) => {
                    mouseX = e.clientX;
                    mouseY = e.clientY;
                });

                function animateCursor() {
                    dotX += (mouseX - dotX) * 0.3;
                    dotY += (mouseY - dotY) * 0.3;
                    ringX += (mouseX - ringX) * 0.15;
                    ringY += (mouseY - ringY) * 0.15;

                    cursorDot.style.transform = `translate(${dotX - 4}px, ${dotY - 4}px)`;
                    cursorRing.style.transform = `translate(${ringX - 20}px, ${ringY - 20}px)`;
                    requestAnimationFrame(animateCursor);
                }
                animateCursor();

                document.querySelectorAll('a, button, .tilt-card').forEach(el => {
                    el.addEventListener('mouseenter', () => cursorRing.classList.add('hover'));
                    el.addEventListener('mouseleave', () => cursorRing.classList.remove('hover'));
                });
            } else {
                if (cursorDot) cursorDot.style.display = 'none';
                if (cursorRing) cursorRing.style.display = 'none';
            }

            // ===== VANILLA TILT =====
            const tiltCards = document.querySelectorAll('.tilt-card');
            if (tiltCards.length > 0 && window.matchMedia('(hover: hover)').matches) {
                VanillaTilt.init(tiltCards, {
                    max: 8,
                    speed: 400,
                    glare: true,
                    'max-glare': 0.15,
                    perspective: 1000
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
