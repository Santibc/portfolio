@props(['title' => 'Catálogo de Proyectos'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
    <title>{{ $title }} - {{ config('app.name', 'AGROMARKET') }}</title>

    {{-- Fuentes --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome 6 --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    {{-- CSS del Template --}}
    <link href="{{ asset('css/agromarket-global.css') }}" rel="stylesheet">
    <link href="{{ asset('css/agromarket-dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/categorias.css') }}" rel="stylesheet">
    <link href="{{ asset('css/agromarket-components.css') }}" rel="stylesheet">

    {{-- Estilos específicos para catálogo público --}}
    <style>
        :root {
            --header-green: #2D5A27;
            --header-green-dark: #1e3d1a;
        }

        .public-header {
            background: linear-gradient(135deg, #2D5A27 0%, #1e3d1a 100%);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        .public-header .header-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .public-header .nav-logo {
            color: #ffffff !important;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            flex-shrink: 0;
        }

        .public-header .nav-logo:hover {
            opacity: 0.9;
            color: #ffffff !important;
        }

        .public-header .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .public-header .nav-links a {
            color: rgba(255,255,255,0.9) !important;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .public-header .nav-links a:hover {
            color: #ffffff !important;
        }

        .public-header .auth-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .public-header .btn-login {
            color: #ffffff !important;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.5);
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }

        .public-header .btn-login:hover {
            background: rgba(255,255,255,0.1);
            border-color: #ffffff;
            color: #ffffff !important;
        }

        .public-header .btn-register {
            color: #2D5A27 !important;
            background: #ffffff;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
        }

        .public-header .btn-register:hover {
            background: #f0f0f0;
            transform: translateY(-1px);
            color: #2D5A27 !important;
        }

        .public-main {
            margin-top: 70px;
            min-height: calc(100vh - 70px);
            background: #f5f7f5;
        }

        .public-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        /* Mobile navigation overlay */
        .mobile-nav-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1001;
        }

        .mobile-nav-overlay.active {
            display: block;
        }

        .mobile-nav {
            position: fixed;
            top: 0;
            right: -300px;
            width: 280px;
            height: 100vh;
            background: #ffffff;
            z-index: 1002;
            transition: right 0.3s ease;
            box-shadow: -5px 0 20px rgba(0,0,0,0.15);
            overflow-y: auto;
        }

        .mobile-nav.active {
            right: 0;
        }

        .mobile-nav-header {
            background: linear-gradient(135deg, #2D5A27 0%, #1e3d1a 100%);
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-nav-header .logo {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mobile-nav-close {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.25rem;
        }

        .mobile-nav-links {
            padding: 1rem 0;
        }

        .mobile-nav-links a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            color: #333333;
            text-decoration: none;
            font-weight: 500;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }

        .mobile-nav-links a:hover {
            background: #f8f8f8;
        }

        .mobile-nav-links a i {
            color: #2D5A27;
            width: 20px;
            text-align: center;
        }

        .mobile-nav-auth {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            border-top: 1px solid #eee;
        }

        .mobile-nav-auth .btn-login-mobile {
            display: block;
            text-align: center;
            padding: 0.75rem;
            border: 2px solid #2D5A27;
            border-radius: 8px;
            color: #2D5A27;
            text-decoration: none;
            font-weight: 600;
        }

        .mobile-nav-auth .btn-register-mobile {
            display: block;
            text-align: center;
            padding: 0.75rem;
            background: #2D5A27;
            border-radius: 8px;
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .public-header .nav-links {
                display: none;
            }

            .public-header .auth-buttons {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .public-header {
                padding: 0.75rem 1rem;
            }
        }

        @media (max-width: 768px) {
            .public-container {
                padding: 1rem;
            }

            .public-main {
                margin-top: 60px;
            }

            .public-header .nav-logo {
                font-size: 1.25rem;
            }
        }

        /* User logged in state */
        .user-menu-public {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #ffffff;
        }

        .user-menu-public .user-avatar {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu-public .user-name {
            font-weight: 500;
            color: #ffffff;
        }

        .user-menu-public .btn-dashboard {
            color: #ffffff !important;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            margin-left: 1rem;
            transition: all 0.2s;
        }

        .user-menu-public .btn-dashboard:hover {
            background: rgba(255,255,255,0.25);
            color: #ffffff !important;
        }

        @media (max-width: 992px) {
            .user-menu-public {
                display: none;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    {{-- Header Público --}}
    <header class="public-header">
        <div class="header-container">
            <a href="{{ route('welcome') }}" class="nav-logo">
                <i class="fas fa-seedling"></i> AGROMARKET
            </a>

            <nav class="nav-links">
                <a href="{{ route('catalog.index') }}">
                    <i class="fas fa-store"></i> Catálogo
                </a>
                <a href="#">
                    <i class="fas fa-info-circle"></i> Cómo Funciona
                </a>
                <a href="#">
                    <i class="fas fa-question-circle"></i> FAQ
                </a>
            </nav>

            <div class="auth-buttons">
                @auth
                    <div class="user-menu-public">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <a href="{{ route('dashboard') }}" class="btn-dashboard">
                            <i class="fas fa-th-large"></i> Mi Dashboard
                        </a>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-login">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="btn-register">Registrarse</a>
                @endauth
            </div>

            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    {{-- Mobile Navigation --}}
    <div class="mobile-nav-overlay" id="mobileNavOverlay" onclick="closeMobileMenu()"></div>
    <nav class="mobile-nav" id="mobileNav">
        <div class="mobile-nav-header">
            <span class="logo">
                <i class="fas fa-seedling"></i> AGROMARKET
            </span>
            <button class="mobile-nav-close" onclick="closeMobileMenu()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mobile-nav-links">
            <a href="{{ route('catalog.index') }}">
                <i class="fas fa-store"></i> Catálogo de Proyectos
            </a>
            <a href="#">
                <i class="fas fa-info-circle"></i> Cómo Funciona
            </a>
            <a href="#">
                <i class="fas fa-question-circle"></i> Preguntas Frecuentes
            </a>
            @auth
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-th-large"></i> Mi Dashboard
                </a>
            @endauth
        </div>
        <div class="mobile-nav-auth">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-register-mobile">
                    <i class="fas fa-user"></i> {{ Auth::user()->name }}
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-login-mobile">Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="btn-register-mobile">Registrarse Gratis</a>
            @endauth
        </div>
    </nav>

    {{-- Contenido Principal --}}
    <main class="public-main">
        <div class="public-container">
            {{ $slot }}
        </div>
    </main>

    {{-- JavaScript --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- JavaScript del Template --}}
    <script src="{{ asset('js/agromarket-main.js') }}"></script>

    <script>
        function toggleMobileMenu() {
            const nav = document.getElementById('mobileNav');
            const overlay = document.getElementById('mobileNavOverlay');
            nav.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            const nav = document.getElementById('mobileNav');
            const overlay = document.getElementById('mobileNavOverlay');
            nav.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    </script>

    @stack('scripts')
</body>
</html>
