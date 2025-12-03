<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
    <title>{{ config('app.name', 'AGROMARKET') }}</title>

    {{-- Fuentes --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome 6 --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    {{-- CSS del Template (NO incluir Bootstrap para evitar conflictos) --}}
    <link href="{{ asset('css/agromarket-global.css') }}" rel="stylesheet">
    <link href="{{ asset('css/agromarket-dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/agromarket-auth.css') }}" rel="stylesheet">
    <link href="{{ asset('css/categorias.css') }}" rel="stylesheet">
    <link href="{{ asset('css/agromarket-components.css') }}" rel="stylesheet">

    {{-- Leaflet para mapas interactivos --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    @stack('styles')
</head>
<body>
    {{-- Header --}}
    <header class="dashboard-header">
        <div class="header-container">
            <div class="header-left">
                <div class="nav-logo">
                    <i class="fas fa-seedling"></i> AGROMARKET
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            {{-- Buscador deshabilitado --}}
            {{--
            <div class="header-center">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Buscar proyectos..." id="searchInput">
                </div>
            </div>
            --}}

            <div class="header-right">
                {{-- Notificaciones - Comentado temporalmente --}}
                {{--
                <button class="notification-btn" onclick="toggleNotifications()">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationCount">0</span>
                </button>
                --}}

                <div class="user-menu">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <span class="user-role">{{ Auth::user()->roles->first()->name ?? 'Usuario' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        @include('layouts.navigation-vertical')
    </aside>

    {{-- Contenido Principal --}}
    <main class="main-content" id="mainContent">
        {{ $slot }}
    </main>

    {{-- JavaScript --}}
    {{-- jQuery (si lo necesitas) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Chart.js para gráficos interactivos (REQUERIDO ANTES de los scripts del template) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

    {{-- Leaflet para mapas interactivos --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    {{-- SweetAlert2 para alertas modernas --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- JavaScript del Template --}}
    <script src="{{ asset('js/agromarket-main.js') }}"></script>
    <script src="{{ asset('js/agromarket-dashboard.js') }}"></script>
    <script src="{{ asset('js/agromarket-charts.js') }}"></script>
    <script src="{{ asset('js/agromarket-interactive-map.js') }}"></script>

    @stack('scripts')
</body>
</html>
