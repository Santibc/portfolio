<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi Cuenta') - Flores y Algo Mas</title>
    <link rel="icon" type="image/png" href="{{ asset('images/ico.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --cliente-primary: #1a1a1a;
            --cliente-primary-hover: #333333;
            --cliente-beige: #f5f5f5;
            --cliente-text: #333333;
            --cliente-text-light: #666666;
            --cliente-bg: #FAFAFA;
            --cliente-white: #FFFFFF;
            --cliente-border: #E5E5E5;
            --font-serif: 'Playfair Display', Georgia, serif;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--cliente-bg);
            color: var(--cliente-text);
        }

        /* ============ NAVBAR MINIMALISTA ============ */
        .navbar {
            background: var(--cliente-white);
            border-bottom: 1px solid var(--cliente-border);
            padding: 12px 0;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .navbar-brand img {
            max-height: 40px;
        }

        .navbar-brand .brand-text {
            font-family: var(--font-serif);
            font-size: 18px;
            font-weight: 600;
            color: var(--cliente-text);
        }

        .navbar-nav .nav-link {
            color: var(--cliente-text);
            font-size: 14px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .navbar-nav .nav-link:hover {
            background: var(--cliente-beige);
            color: var(--cliente-primary);
        }

        .navbar-nav .nav-link.active {
            background: var(--cliente-primary);
            color: white;
        }

        .navbar-nav .nav-link i {
            font-size: 16px;
        }

        .navbar-toggler {
            border: none;
            padding: 8px;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(51, 51, 51, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* User dropdown */
        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            background: var(--cliente-beige);
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .user-dropdown .dropdown-toggle:hover {
            background: var(--cliente-primary);
            color: white;
        }

        .user-dropdown .dropdown-toggle::after {
            display: none;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--cliente-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }

        .user-dropdown .dropdown-toggle:hover .user-avatar {
            background: white;
            color: var(--cliente-primary);
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--cliente-text);
        }

        .user-dropdown .dropdown-toggle:hover .user-name {
            color: white;
        }

        .dropdown-menu {
            border: 1px solid var(--cliente-border);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 14px;
        }

        .dropdown-item:hover {
            background: var(--cliente-beige);
            color: var(--cliente-primary);
        }

        /* ============ PAGE HEADER ============ */
        .page-header {
            background: var(--cliente-white);
            border-bottom: 1px solid var(--cliente-border);
            padding: 24px 0;
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-family: var(--font-serif);
            font-size: 28px;
            font-weight: 500;
            color: var(--cliente-text);
            margin: 0;
        }

        .page-header .breadcrumb {
            margin: 0;
            padding: 0;
            background: none;
        }

        .page-header .breadcrumb-item {
            font-size: 13px;
        }

        .page-header .breadcrumb-item a {
            color: var(--cliente-text-light);
            text-decoration: none;
        }

        .page-header .breadcrumb-item a:hover {
            color: var(--cliente-primary);
        }

        .page-header .breadcrumb-item.active {
            color: var(--cliente-primary);
        }

        /* ============ CONTENT CARDS ============ */
        .content-card {
            background: var(--cliente-white);
            border: 1px solid var(--cliente-border);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .content-card .card-title {
            font-family: var(--font-serif);
            font-size: 20px;
            font-weight: 500;
            color: var(--cliente-text);
            margin-bottom: 16px;
        }

        /* ============ BUTTONS ============ */
        .btn-primary {
            background-color: var(--cliente-primary);
            border-color: var(--cliente-primary);
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: var(--cliente-primary-hover);
            border-color: var(--cliente-primary-hover);
        }

        .btn-outline-primary {
            color: var(--cliente-primary);
            border-color: var(--cliente-primary);
            border-radius: 8px;
            font-weight: 500;
        }

        .btn-outline-primary:hover {
            background-color: var(--cliente-primary);
            border-color: var(--cliente-primary);
            color: white;
        }

        /* ============ BADGES DE ESTADO ============ */
        .badge-estado {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 12px;
        }

        .badge-pendiente {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-pagada {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-enviada {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-entregada {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-cancelada {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .stars {
            color: #ffc107;
        }

        .stars .bi-star {
            color: var(--cliente-border);
        }

        /* ============ ALERTS ============ */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 16px 20px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .alert-info {
            background-color: var(--cliente-beige);
            color: var(--cliente-text);
        }

        /* ============ FOOTER ============ */
        footer {
            background: var(--cliente-white);
            border-top: 1px solid var(--cliente-border);
            color: var(--cliente-text-light);
            padding: 24px 0;
            margin-top: 48px;
        }

        footer a {
            color: var(--cliente-primary);
            text-decoration: none;
        }

        footer a:hover {
            color: var(--cliente-primary-hover);
        }

        /* ============ SIDEBAR NAVEGACIÓN CLIENTE ============ */
        .cliente-sidebar {
            background: var(--cliente-white);
            border: 1px solid var(--cliente-border);
            border-radius: 12px;
            padding: 16px;
            position: sticky;
            top: 100px;
        }

        .cliente-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            color: var(--cliente-text);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 4px;
        }

        .cliente-sidebar .nav-link:hover {
            background: var(--cliente-beige);
            color: var(--cliente-primary);
        }

        .cliente-sidebar .nav-link.active {
            background: var(--cliente-primary);
            color: white;
        }

        .cliente-sidebar .nav-link i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 991px) {
            .user-name {
                display: none;
            }

            .cliente-sidebar {
                margin-bottom: 24px;
                position: static;
            }
        }

        @media (max-width: 767px) {
            .page-header h1 {
                font-size: 24px;
            }

            .content-card {
                padding: 16px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cliente.compras*') ? 'active' : '' }}" href="{{ route('cliente.compras') }}">
                            <i class="bi bi-bag me-1"></i> Mis Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cliente.direcciones*') ? 'active' : '' }}" href="{{ route('cliente.direcciones') }}">
                            <i class="bi bi-geo-alt me-1"></i> Direcciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cliente.perfil*') ? 'active' : '' }}" href="{{ route('cliente.perfil') }}">
                            <i class="bi bi-person me-1"></i> Mi Perfil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cliente.puntos*') ? 'active' : '' }}" href="{{ route('cliente.puntos') }}">
                            <i class="bi bi-star me-1"></i> Mis Puntos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}" target="_blank">
                            <i class="bi bi-shop me-1"></i> Ver Tienda
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown user-dropdown">
                        <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <span class="user-name">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('cliente.perfil') }}">
                                    <i class="bi bi-person me-2"></i> Mi Perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    @hasSection('header')
    <div class="page-header">
        <div class="container">
            @yield('header')
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} Flores y Algo Más. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="me-3">Términos y condiciones</a>
                    <a href="#">Política de privacidad</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>
