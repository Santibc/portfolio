<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tienda Online')</title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@400;700&family=Chivo:wght@400;700&display=swap" rel="stylesheet">

    <!-- CSS Variables -->
    <style>
        :root {
            /* Colors */
            --main-foreground: #000000;
            --main-background: #FFFFFF;
            --accent-color: #000000;
            --button-background: #000000;
            --button-foreground: #FFFFFF;
            --header-background: #FFFFFF;
            --header-foreground: #000000;
            --footer-background: #000000;
            --footer-foreground: #FFFFFF;

            /* Fonts */
            --heading-font: "Big Shoulders Display", sans-serif;
            --body-font: "Chivo", sans-serif;

            /* Font Sizes */
            --h1: 28px;
            --h2: 24px;
            --h3: 20px;
            --h4: 18px;
            --font-base: 14px;
            --font-large: 18px;
            --font-small: 12px;

            /* Spacing */
            --gutter: 15px;
            --section-distance: 30px;

            /* Border */
            --border-radius: 4px;
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

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--gutter);
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
            background: none;
            padding: 0;
            text-decoration: underline;
        }

        /* Header Styles */
        header {
            background-color: var(--header-background);
            color: var(--header-foreground);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-top {
            background-color: var(--main-foreground);
            color: var(--main-background);
            padding: 8px 0;
            text-align: center;
            font-size: var(--font-small);
        }

        .header-main {
            padding: 15px 0;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .logo {
            font-family: var(--heading-font);
            font-size: 32px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .nav-primary {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .nav-primary a {
            font-family: var(--heading-font);
            font-weight: 700;
            text-transform: uppercase;
            font-size: var(--font-base);
            transition: opacity 0.3s;
        }

        .nav-primary a:hover {
            opacity: 0.7;
        }

        .header-utilities {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-icon,
        .cart-icon,
        .menu-icon {
            cursor: pointer;
            padding: 8px;
            transition: opacity 0.3s;
        }

        .search-icon:hover,
        .cart-icon:hover,
        .menu-icon:hover {
            opacity: 0.7;
        }

        .cart-badge {
            background-color: var(--accent-color);
            color: var(--button-foreground);
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: 700;
            margin-left: 4px;
        }

        /* Footer Styles */
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
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column a {
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .footer-column a:hover {
            opacity: 1;
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
            border-radius: 50%;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        /* Mobile Menu */
        .menu-icon {
            display: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-primary {
                display: none;
            }

            .menu-icon {
                display: block;
            }

            .logo {
                font-size: 24px;
            }

            .footer-main {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }

        /* SVG Icons */
        .icon {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        .icon-sm {
            width: 16px;
            height: 16px;
        }
    </style>

    @stack('styles')
</head>
<body class="@yield('body-class')">

    <!-- Header -->
    <header>
        <div class="header-top">
            🚚 ENVÍO GRATIS en compras mayores a $50.000
        </div>

        <div class="header-main">
            <div class="container">
                <div class="header-container">
                    <!-- Logo -->
                    <div class="logo">
                        <a href="/">SPORT STORE</a>
                    </div>

                    <!-- Navigation -->
                    <nav class="nav-primary">
                        <a href="/productos">TODOS</a>
                        <a href="/categoria/calzado">CALZADO</a>
                        <a href="/categoria/indumentaria">INDUMENTARIA</a>
                        <a href="/categoria/accesorios">ACCESORIOS</a>
                        <a href="/ofertas">OFERTAS</a>
                    </nav>

                    <!-- Utilities -->
                    <div class="header-utilities">
                        <div class="search-icon">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                        </div>

                        <div class="cart-icon">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                            <span class="cart-badge">0</span>
                        </div>

                        <div class="menu-icon">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-main">
                <div class="footer-column">
                    <h4>SOBRE NOSOTROS</h4>
                    <p>Somos una tienda especializada en productos deportivos de alta calidad. Encuentra todo lo que necesitas para tu deporte favorito.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook">
                            <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="Instagram">
                            <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="Twitter">
                            <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="footer-column">
                    <h4>NAVEGACIÓN</h4>
                    <ul>
                        <li><a href="/">Inicio</a></li>
                        <li><a href="/productos">Productos</a></li>
                        <li><a href="/ofertas">Ofertas</a></li>
                        <li><a href="/nosotros">Sobre Nosotros</a></li>
                        <li><a href="/contacto">Contacto</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>AYUDA</h4>
                    <ul>
                        <li><a href="/preguntas-frecuentes">Preguntas Frecuentes</a></li>
                        <li><a href="/envios">Información de Envíos</a></li>
                        <li><a href="/devoluciones">Devoluciones</a></li>
                        <li><a href="/terminos">Términos y Condiciones</a></li>
                        <li><a href="/privacidad">Política de Privacidad</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>CONTACTO</h4>
                    <ul>
                        <li>📍 Av. Principal 1234, Buenos Aires</li>
                        <li>📞 +54 11 1234-5678</li>
                        <li>✉️ info@sportstore.com</li>
                        <li>🕐 Lun - Vie: 9:00 - 18:00</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Sport Store. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
