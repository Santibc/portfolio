<!DOCTYPE html>
<html lang="es" xmlns:og="http://opengraphprotocol.org/schema/">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $empresa->nombre . ' - Tienda Online')</title>
    <meta name="description" content="@yield('description', $empresa->descripcion ?? 'Tienda online de productos deportivos')">

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

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@400;700&family=Chivo:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <style>
        :root {
            /* Colores */
            --main-foreground: #000000;
            --main-background: #FFFFFF;
            --accent-color: #000000;
            --button-background: #000000;
            --button-foreground: #FFFFFF;
            --header-background: #FFFFFF;
            --header-foreground: #000000;
            --footer-background: #000000;
            --footer-foreground: #FFFFFF;

            /* Fuentes */
            --heading-font: "Big Shoulders Display", sans-serif;
            --body-font: "Chivo", sans-serif;

            /* Tamaños de fuente */
            --h1: 28px;
            --h1-huge: 100px;
            --h1-huge-md: 125px;
            --h2: 24px;
            --h2-huge: 70px;
            --h3: 20px;
            --h3-huge: 50px;
            --h4: 18px;
            --font-base: 14px;
            --font-large: 18px;
            --font-small: 12px;

            /* Espaciados */
            --gutter: 15px;
            --section-distance: 30px;
            --section-distance-huge: 60px;

            /* Bordes */
            --border-radius: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--body-font);
            font-size: var(--font-base);
            color: var(--main-foreground);
            background-color: var(--main-background);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--heading-font);
            font-weight: 700;
            text-transform: uppercase;
        }

        h1 { font-size: var(--h1); }
        h2 { font-size: var(--h2); }
        h3 { font-size: var(--h3); }
        h4 { font-size: var(--h4); }

        .h1-huge { font-size: var(--h1-huge); }
        .h2-huge { font-size: var(--h2-huge); }
        .h3-huge { font-size: var(--h3-huge); }

        a {
            text-decoration: none;
            color: inherit;
            transition: opacity 0.3s;
        }

        a:hover {
            opacity: 0.7;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: var(--button-background);
            color: var(--button-foreground);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-family: var(--heading-font);
            font-weight: 700;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-link {
            background: transparent;
            color: var(--main-foreground);
            text-decoration: underline;
            padding: 0;
        }

        /* Header */
        header {
            background-color: var(--header-background);
            color: var(--header-foreground);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header-main {
            padding: 20px 0;
        }

        .header-container {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 20px;
        }

        /* Left utilities */
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
            justify-content: flex-start;
        }

        /* Center logo */
        .logo {
            font-family: var(--heading-font);
            font-size: 28px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: center;
            grid-column: 2;
        }

        @media (min-width: 768px) {
            .logo {
                font-size: 36px;
            }
        }

        /* Right utilities */
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
            justify-content: flex-end;
        }

        /* Header buttons */
        .header-btn {
            background: none;
            border: none;
            color: var(--header-foreground);
            font-family: var(--heading-font);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 14px;
            cursor: pointer;
            padding: 8px 12px;
            position: relative;
            transition: opacity 0.3s ease;
        }

        .header-btn:hover {
            opacity: 0.7;
        }

        @media (min-width: 768px) {
            .header-btn {
                font-size: 16px;
            }
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--accent-color);
            color: var(--button-foreground);
            border-radius: 0;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            padding: 0 4px;
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            z-index: 2000;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Menu Modal */
        .menu-modal {
            position: fixed;
            top: 0;
            left: -400px;
            width: 100%;
            max-width: 400px;
            height: 100%;
            background-color: var(--main-background);
            z-index: 2001;
            transition: left 0.3s ease;
            overflow-y: auto;
        }

        .menu-modal.active {
            left: 0;
        }

        .modal-header {
            padding: 30px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-family: var(--heading-font);
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 30px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            padding: 30px;
        }

        .category-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .category-list li {
            margin-bottom: 20px;
        }

        .category-list a {
            font-family: var(--heading-font);
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--main-foreground);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .category-list a:hover {
            color: var(--accent-color);
        }

        /* Search Modal */
        .search-modal {
            position: fixed;
            top: -100%;
            left: 0;
            width: 100%;
            background-color: var(--main-background);
            z-index: 2001;
            transition: top 0.3s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .search-modal.active {
            top: 0;
        }

        .search-container {
            padding: 40px 30px;
        }

        .search-input-wrapper {
            display: flex;
            gap: 10px;
            max-width: 800px;
            margin: 0 auto;
        }

        .search-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid rgba(0,0,0,0.2);
            border-radius: var(--border-radius);
            font-size: 18px;
            font-family: var(--body-font);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        /* Footer */
        footer {
            background-color: var(--footer-background);
            color: var(--footer-foreground);
            padding: 60px 0 20px;
            margin-top: 60px;
        }

        .footer-main {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h4 {
            margin-bottom: 20px;
            font-size: var(--h4);
        }

        .footer-column ul {
            list-style: none;
            padding: 0;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column a {
            opacity: 0.8;
        }

        .footer-column a:hover {
            opacity: 1;
        }

        .footer-logo-section {
            margin: 60px 0 40px;
            text-align: center;
        }

        .footer-logo {
            font-family: var(--heading-font);
            font-size: 80px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: var(--footer-foreground);
            line-height: 1;
        }

        @media (min-width: 768px) {
            .footer-logo {
                font-size: 120px;
                letter-spacing: 8px;
            }
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            text-align: center;
            font-size: var(--font-small);
            opacity: 0.7;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 0;
            font-size: 20px;
        }

        .social-links a:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .cart-badge {
            display: inline-block !important;
        }

        .cart-badge[style*="display: none"] {
            display: none !important;
        }

        /* Newsletter */
        .newsletter-form {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .newsletter-form input {
            flex: 1;
            padding: 12px;
            border: 1px solid rgba(0,0,0,0.2);
            border-radius: var(--border-radius);
            font-family: var(--body-font);
        }

        .newsletter-form button {
            padding: 12px 24px;
        }

        /* Menu hamburguesa */
        .menu-toggle {
            display: block;
        }

        @media (min-width: 768px) {
            .menu-toggle {
                display: none;
            }
        }

        /* Icons SVG */
        .icon {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        /* Transition */
        .transition-soft {
            transition: all 0.3s ease;
        }

        /* Container */
        .container-fluid {
            padding-left: var(--gutter);
            padding-right: var(--gutter);
        }

        @media (min-width: 768px) {
            .container-fluid {
                padding-left: 30px;
                padding-right: 30px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- SVG Icons Sprite -->
    <svg xmlns="http://www.w3.org/2000/svg" class="hidden" style="display: none;">
        <symbol id="arrow-long" viewBox="0 0 512 512">
            <path d="M442.7,243.2l-54.95-54.95,18.1-18.1L491.7,256l-85.85,85.85-18.1-18.1L442.7,268.8H25.6V243.2Z"></path>
        </symbol>
    </svg>

    <!-- Header -->
    <header>
        <div class="header-main">
            <div class="container-fluid">
                <div class="header-container">
                    <!-- Left: Menu & Search buttons -->
                    <div class="header-left">
                        <button class="header-btn" id="openMenuBtn">MENU</button>
                        <button class="header-btn" id="openSearchBtn">BUSCAR</button>
                    </div>

                    <!-- Center: Logo -->
                    <div class="logo">
                        <a href="{{ route('tienda.empresa', $empresa->slug) }}">
                            @if($empresa->logo_url)
                                <img src="{{ $empresa->logo_url }}" alt="{{ $empresa->nombre }}" style="max-height: 60px; width: auto;">
                            @else
                                {{ strtoupper($empresa->nombre) }}
                            @endif
                        </a>
                    </div>

                    <!-- Right: Cart button -->
                    <div class="header-right">
                        <button class="header-btn" id="cartBtn">
                            CARRITO
                            <span class="cart-badge cart-count" @if(!isset($carrito) || $carrito->total_items == 0) style="display: none;" @endif>
                                {{ isset($carrito) ? $carrito->total_items : 0 }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Menu Modal -->
    <div class="modal-overlay" id="menuOverlay"></div>
    <div class="menu-modal" id="menuModal">
        <div class="modal-header">
            <h2 class="modal-title">CATEGORÍAS</h2>
            <button class="close-modal" id="closeMenuBtn">&times;</button>
        </div>
        <div class="modal-content">
            <ul class="category-list">
                <li><a href="{{ route('tienda.categorias', $empresa->slug) }}">TODOS LOS PRODUCTOS</a></li>
                @if(isset($categorias) && $categorias->count() > 0)
                    @foreach($categorias as $categoria)
                        <li><a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}">{{ strtoupper($categoria->nombre) }}</a></li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>

    <!-- Search Modal -->
    <div class="search-modal" id="searchModal">
        <div class="search-container">
            <div class="search-input-wrapper">
                <input type="text" class="search-input" placeholder="Buscar productos..." id="searchInput">
                <button class="btn" id="searchSubmitBtn">BUSCAR</button>
                <button class="btn" id="closeSearchBtn">CERRAR</button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container-fluid">
            <div class="footer-main">
                <!-- Column 1: Menu -->
                <div class="footer-column">
                    <h4>NAVEGACIÓN</h4>
                    <ul>
                        <li><a href="{{ route('tienda.empresa', $empresa->slug) }}">INICIO</a></li>
                        <li><a href="{{ route('tienda.categorias', $empresa->slug) }}">PRODUCTOS</a></li>
                        @if(isset($categorias) && $categorias->count() > 0)
                            @foreach($categorias->take(3) as $categoria)
                                <li><a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}">{{ strtoupper($categoria->nombre) }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Column 2: Horario -->
                <div class="footer-column">
                    <h4>HORARIO</h4>
                    @if($empresa->horario_atencion)
                        <ul style="list-style: none; padding: 0; opacity: 0.8; font-size: 14px; line-height: 1.8;">
                            @php
                                $dias = ['lunes' => 'Lun', 'martes' => 'Mar', 'miercoles' => 'Mié',
                                         'jueves' => 'Jue', 'viernes' => 'Vie', 'sabado' => 'Sáb', 'domingo' => 'Dom'];
                                foreach($dias as $key => $dia) {
                                    if(isset($empresa->horario_atencion[$key])) {
                                        if($empresa->horario_atencion[$key]['cerrado'] ?? false) {
                                            echo '<li>' . $dia . ': Cerrado</li>';
                                        } else {
                                            echo '<li>' . $dia . ': ' . ($empresa->horario_atencion[$key]['apertura'] ?? '09:00') . ' - ' .
                                                ($empresa->horario_atencion[$key]['cierre'] ?? '18:00') . '</li>';
                                        }
                                    }
                                }
                            @endphp
                        </ul>
                    @else
                        <p style="opacity: 0.8;">Lun - Vie: 9:00 - 18:00</p>
                    @endif
                </div>

                <!-- Column 3: Social -->
                <div class="footer-column">
                    <h4>REDES SOCIALES</h4>
                    <div class="social-links">
                        @if($empresa->facebook_url)
                            <a href="{{ $empresa->facebook_url }}" target="_blank" aria-label="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                        @endif
                        @if($empresa->instagram_url)
                            <a href="{{ $empresa->instagram_url }}" target="_blank" aria-label="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                        @endif
                        @if($empresa->twitter_url)
                            <a href="{{ $empresa->twitter_url }}" target="_blank" aria-label="Twitter">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                        @endif
                        @if($empresa->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $empresa->whatsapp) }}" target="_blank" aria-label="WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Column 4: Contact -->
                <div class="footer-column">
                    <h4>CONTACTO</h4>
                    <ul>
                        @if($empresa->telefono)
                            <li>Tel: {{ $empresa->telefono }}</li>
                        @endif
                        @if($empresa->email)
                            <li>Email: {{ $empresa->email }}</li>
                        @endif
                        @if($empresa->direccion)
                            <li>{{ $empresa->direccion }}</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Footer Logo -->
            <div class="footer-logo-section">
                <div class="footer-logo">{{ strtoupper($empresa->nombre) }}</div>
            </div>

            <!-- Métodos de Pago -->
            <div class="footer-payments text-center my-4">
                <img src="https://d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/visa@2x.png" alt="Visa" class="payment-logo" style="height: 30px; margin: 0 8px;">
                <img src="https://d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/mastercard@2x.png" alt="Mastercard" class="payment-logo" style="height: 30px; margin: 0 8px;">
                <img src="https://d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/amex@2x.png" alt="Amex" class="payment-logo" style="height: 30px; margin: 0 8px;">
                <img src="{{ asset('images/WompiLogo.png') }}" alt="Wompi" class="payment-logo" style="height: 30px; margin: 0 8px;">
                <img src="{{ asset('images/Logo-Puntos-CO.jpg') }}" alt="Puntos Colombia" class="payment-logo" style="height: 30px; margin: 0 8px;">
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ $empresa->nombre }}. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <!-- Header Modals Script -->
    <script>
        // Menu Modal
        const menuModal = document.getElementById('menuModal');
        const menuOverlay = document.getElementById('menuOverlay');
        const openMenuBtn = document.getElementById('openMenuBtn');
        const closeMenuBtn = document.getElementById('closeMenuBtn');

        function openMenu() {
            menuModal.classList.add('active');
            menuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            menuModal.classList.remove('active');
            menuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        openMenuBtn.addEventListener('click', openMenu);
        closeMenuBtn.addEventListener('click', closeMenu);
        menuOverlay.addEventListener('click', closeMenu);

        // Search Modal
        const searchModal = document.getElementById('searchModal');
        const openSearchBtn = document.getElementById('openSearchBtn');
        const closeSearchBtn = document.getElementById('closeSearchBtn');
        const searchInput = document.getElementById('searchInput');
        const searchSubmitBtn = document.getElementById('searchSubmitBtn');

        function openSearch() {
            searchModal.classList.add('active');
            menuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                searchInput.focus();
            }, 300);
        }

        function closeSearch() {
            searchModal.classList.remove('active');
            menuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        openSearchBtn.addEventListener('click', openSearch);
        closeSearchBtn.addEventListener('click', closeSearch);
        menuOverlay.addEventListener('click', () => {
            if (searchModal.classList.contains('active')) {
                closeSearch();
            }
        });

        searchSubmitBtn.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query) {
                window.location.href = "{{ route('tienda.categorias', $empresa->slug) }}?buscar=" + encodeURIComponent(query);
            }
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchSubmitBtn.click();
            }
        });

        // Cart Button
        const cartBtn = document.getElementById('cartBtn');
        cartBtn.addEventListener('click', function() {
            window.location.href = "{{ route('tienda.carrito', $empresa->slug) }}";
        });

        // Close modals on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (menuModal.classList.contains('active')) {
                    closeMenu();
                }
                if (searchModal.classList.contains('active')) {
                    closeSearch();
                }
            }
        });

        // Update cart badge function (can be called from other pages)
        window.updateCartBadge = function(count) {
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge) {
                if (count > 0) {
                    cartBadge.textContent = count;
                    cartBadge.style.display = 'flex';
                } else {
                    cartBadge.style.display = 'none';
                }
            }
        };
    </script>

    @stack('scripts')
</body>
</html>
