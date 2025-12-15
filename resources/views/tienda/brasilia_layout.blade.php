<!DOCTYPE html>
<html lang="es" xmlns:og="http://opengraphprotocol.org/schema/">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $empresa->nombre . ' - Tienda Online')</title>
    <meta name="description" content="@yield('description', $empresa->descripcion ?? 'Tienda online')">

    <!-- Open Graph Meta Tags (WhatsApp, Facebook, etc.) -->
    <meta property="og:site_name" content="{{ $empresa->nombre }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', $empresa->nombre . ' - Tienda Online')">
    <meta property="og:description" content="@yield('og_description', $empresa->descripcion ?? 'Visita nuestra tienda online')">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($empresa->logo_url)
    <meta property="og:image" content="{{ $empresa->logo_url }}">
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', $empresa->nombre . ' - Tienda Online')">
    <meta name="twitter:description" content="@yield('og_description', $empresa->descripcion ?? 'Visita nuestra tienda online')">
    @if($empresa->logo_url)
    <meta name="twitter:image" content="{{ $empresa->logo_url }}">
    @endif

    <!-- Favicon -->
    <link href="{{ $empresa->logo_url ?? asset('images/ico.png') }}" rel="icon">

    <!-- Google Fonts - Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/brasilia-theme.css') }}">

    <style>
        /* Apply Instrument Sans font globally */
        body, .font-family-body {
            font-family: 'Instrument Sans', sans-serif !important;
        }
        h1, h2, h3, h4, h5, h6, .font-family-heading {
            font-family: 'Instrument Sans', sans-serif !important;
        }

        /* Responsive header offset - no padding on mobile */
        @media (max-width: 767.98px) {
            body.head-offset {
                padding-top: 0 !important;
            }
        }
        @media (min-width: 768px) and (max-width: 991.98px) {
            body.head-offset {
                padding-top: 160px !important;
            }
        }

        /* Fix header overflow on narrow mobile screens */
        @media (max-width: 767.98px) {
            .head-row {
                display: flex !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
                justify-content: space-between !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
                overflow: hidden !important;
                max-width: 100% !important;
            }
            .head-row .search-container {
                display: none !important;
            }
            .head-row .logo-container {
                flex: 1 1 auto !important;
                min-width: 0 !important;
                max-width: none !important;
                width: auto !important;
            }
            .head-row .utilities-container {
                flex: 0 0 auto !important;
                margin-left: auto !important;
            }
            .head-row .menu-container {
                flex: 0 0 auto !important;
                margin-right: 10px !important;
            }
            .header-utility {
                padding: 5px !important;
            }
            .utility-icon.icon-lg {
                width: 24px !important;
                height: 24px !important;
            }
        }

        /* Extra small screens (< 360px) */
        @media (max-width: 359px) {
            .head-row .logo-container {
                max-width: 80px !important;
            }
            .head-row .logo-container .logo-img {
                max-width: 80px !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="js-head-offset head-offset @yield('body-class')" style="padding-top: 206px;">

    <!-- SVG Sprite -->
    @include('tienda.partials.brasilia.svg-sprite')

    <!-- Header -->
    @include('tienda.partials.brasilia.header')

    <!-- Main Content -->
    <main @yield('main-class')>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('tienda.partials.brasilia.footer')

    <!-- Modals -->
    @include('tienda.partials.brasilia.modals')

    <!-- Search Modal (Mobile) - Rebuilt -->
    <div id="brasilia-search-overlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.6); z-index:999999;"></div>
    <div id="brasilia-search-modal" style="display:none; position:fixed; top:0; left:0; right:0; background:#fff; z-index:1000000; padding:20px; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
        <div style="max-width:600px; margin:0 auto;">
            <div style="display:flex; flex-direction:column; gap:12px;">
                <input type="text" id="brasilia-search-input" placeholder="Buscar productos..."
                       style="width:100%; padding:14px 16px; border:2px solid #ddd; border-radius:8px; font-size:16px; outline:none;">
                <div style="display:flex; gap:10px;">
                    <button id="brasilia-search-submit" style="flex:1; padding:14px 20px; background:#000; color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:14px;">
                        BUSCAR
                    </button>
                    <button id="brasilia-search-close" style="flex:1; padding:14px 20px; background:#f0f0f0; color:#333; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:14px;">
                        CERRAR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Brasilia Search Modal - Simple and Direct
        (function() {
            function init() {
                var modal = document.getElementById('brasilia-search-modal');
                var overlay = document.getElementById('brasilia-search-overlay');
                var input = document.getElementById('brasilia-search-input');
                var submitBtn = document.getElementById('brasilia-search-submit');
                var closeBtn = document.getElementById('brasilia-search-close');
                var openBtns = document.querySelectorAll('.js-search-button');

                if (!modal || !overlay || !openBtns.length) {
                    console.log('Brasilia search: missing elements');
                    return;
                }

                function openModal() {
                    modal.style.display = 'block';
                    overlay.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                    if (input) input.focus();
                }

                function closeModal() {
                    modal.style.display = 'none';
                    overlay.style.display = 'none';
                    document.body.style.overflow = '';
                }

                function doSearch() {
                    var query = input ? input.value.trim() : '';
                    if (query) {
                        window.location.href = "{{ route('tienda.categorias', $empresa->slug) }}?buscar=" + encodeURIComponent(query);
                    }
                }

                // Bind open buttons
                for (var i = 0; i < openBtns.length; i++) {
                    openBtns[i].addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openModal();
                    });
                }

                // Bind close
                if (closeBtn) closeBtn.addEventListener('click', closeModal);
                overlay.addEventListener('click', closeModal);

                // Bind search
                if (submitBtn) submitBtn.addEventListener('click', doSearch);
                if (input) {
                    input.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') doSearch();
                    });
                }

                // ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && modal.style.display === 'block') {
                        closeModal();
                    }
                });

                console.log('Brasilia search modal ready');
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/brasilia-theme.js') }}"></script>

    @stack('scripts')
</body>
</html>
