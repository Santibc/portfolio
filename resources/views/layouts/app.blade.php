<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
    <title>{{ config('app.name', 'Manzer') }}</title>

    {{-- Fuentes --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome 6 --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Tailwind CSS (para componentes Blade - con preflight deshabilitado) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            }
        }
    </script>

    {{-- Manzer CSS --}}
    <link href="{{ asset('css/gva-global.css') }}" rel="stylesheet">
    <link href="{{ asset('css/gva-dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/gva-components.css') }}" rel="stylesheet">
    <link href="{{ asset('css/manzer-components.css') }}" rel="stylesheet">

    {{-- DataTables CSS --}}
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">

    @stack('styles')
</head>
<body>
    {{-- Header --}}
    <header class="dashboard-header">
        <div class="header-container">
            <div class="header-left">
                <div class="nav-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Manzer Logo" style="height: 32px; margin-right: 8px;"> Manzer
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="header-right">
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
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        {{-- Soporte para @yield('content') y {{ $slot }} --}}
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    {{-- JavaScript --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Bootstrap 5 JS (para modales) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- DataTables --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Manzer Main JS --}}
    <script src="{{ asset('js/gva-main.js') }}"></script>

    @stack('scripts')
</body>
</html>
