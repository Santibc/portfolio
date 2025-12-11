<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/ico.png') }}"/>
    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- CSS personalizado y Bootstrap --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts - Miracle Brand --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
   @stack('styles')
    <style>
        /* Variables de Marca Miracle Beauty Experts */
        :root {
            --miracle-pink: #FF84D5;
            --miracle-pink-light: #FFE4F3;
            --miracle-pink-hover: #ff6bc9;
            --miracle-lilac: #BCA9F5;
            --miracle-lilac-light: #E8E1FA;
            --miracle-gold: #D4AF37;
            --miracle-cream: #FFF1DD;
            --miracle-aqua: #B9DFDE;
            --miracle-dark: #382E65;
        }

        body {
            background-color: #faf8fc;
            font-family: 'Roboto', sans-serif;
            color: var(--miracle-dark);
        }

        h1, h2, h3, h4, h5, h6, .fw-semibold {
            font-family: 'Comfortaa', cursive;
        }

        .sidebar {
            width: 250px;
            transition: all 0.3s ease;
            background: linear-gradient(180deg, #ffffff 0%, var(--miracle-lilac-light) 100%) !important;
            border-right: 2px solid var(--miracle-lilac) !important;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar .nav-link span {
            transition: opacity 0.2s, width 0.2s;
        }

        .sidebar.collapsed .nav-link span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        /* Added for the logout text in sidebar */
        .sidebar.collapsed .logout-label {
            opacity: 0;
            width: 0;
            overflow: hidden;
            transition: opacity 0.2s, width 0.2s;
        }

        .sidebar.collapsed .btn-outline-danger {
            justify-content: center !important;
        }

        header {
            height: 64px;
            background-color: white;
            position: fixed;
            top: 0;
            right: 0;
            transition: left 0.3s ease;
            padding-right: 1rem;
            border-bottom: 2px solid var(--miracle-pink) !important;
        }

        main {
            padding-top: 80px;
            transition: margin-left 0.3s ease;
        }

        #toggleSidebar {
            border: none !important;
            background-color: transparent;
            color: var(--miracle-pink);
        }

        #toggleSidebar:hover {
            background-color: var(--miracle-cream);
            color: var(--miracle-pink-hover);
        }

        .nav-link {
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            color: var(--miracle-dark) !important;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: var(--miracle-cream) !important;
            color: var(--miracle-pink) !important;
        }

        .nav-link.active {
            background-color: var(--miracle-pink-light) !important;
            color: var(--miracle-pink) !important;
            font-weight: 600;
            border-left: 3px solid var(--miracle-pink);
        }

        .nav-link i {
            transition: color 0.2s ease;
        }

        .nav-link:hover i,
        .nav-link.active i {
            color: var(--miracle-pink) !important;
        }

        /* Botones con estilo Miracle */
        .btn-primary {
            background-color: var(--miracle-pink) !important;
            border-color: var(--miracle-pink) !important;
            color: white !important;
        }

        .btn-primary:hover {
            background-color: var(--miracle-pink-hover) !important;
            border-color: var(--miracle-pink-hover) !important;
        }

        .btn-outline-primary {
            color: var(--miracle-pink) !important;
            border-color: var(--miracle-pink) !important;
        }

        .btn-outline-primary:hover {
            background-color: var(--miracle-pink) !important;
            color: white !important;
        }

        .btn-info {
            background-color: var(--miracle-lilac) !important;
            border-color: var(--miracle-lilac) !important;
            color: white !important;
        }

        .btn-info:hover {
            background-color: #a896e8 !important;
            border-color: #a896e8 !important;
        }

        .btn-success {
            background-color: var(--miracle-aqua) !important;
            border-color: var(--miracle-aqua) !important;
            color: var(--miracle-dark) !important;
        }

        .btn-success:hover {
            background-color: #9ed1cf !important;
            border-color: #9ed1cf !important;
        }

        /* Badges Miracle */
        .bg-info {
            background-color: var(--miracle-lilac) !important;
        }

        .bg-primary {
            background-color: var(--miracle-pink) !important;
        }

        /* Spinner */
        .spinner-border {
            color: var(--miracle-pink) !important;
        }

        /* Cards con estilo suave */
        .card {
            border: 1px solid var(--miracle-lilac-light);
            box-shadow: 0 2px 8px rgba(188, 169, 245, 0.1);
        }

        .card-header {
            background-color: var(--miracle-lilac-light);
            border-bottom: 1px solid var(--miracle-lilac);
        }

        /* Tables */
        .table thead th {
            background-color: var(--miracle-lilac-light);
            color: var(--miracle-dark);
            border-color: var(--miracle-lilac);
        }

        /* Form controls focus */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--miracle-pink);
            box-shadow: 0 0 0 0.2rem rgba(255, 132, 213, 0.25);
        }

        /* Ensure user info in header expands as needed, remove max-width constraints if possible */
        .header-user-info {
            /* This div contains the name, email, and avatar */
            flex-grow: 1; /* Allow it to take available space */
            justify-content: flex-end; /* Push content to the right within this flex item */
            display: flex; /* Make it a flex container */
            align-items: center;
            gap: 0.5rem; /* Space between text and avatar */
        }

        .header-user-info .text-end {
            /* No max-width here, allow name/email to expand */
            overflow: hidden; /* Hide overflow if text is too long */
            white-space: nowrap;
            text-overflow: ellipsis;
            max-width: calc(100% - 50px); /* Allow text to take most of the space, reserving for avatar */
                                          /* Adjust 50px (avatar width + gap) as needed */
        }

        .header-user-info .text-end .fw-semibold,
        .header-user-info .text-end .text-muted {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }


        @media (max-width: 768px) {
            .header-user-info .text-end {
                max-width: calc(100% - 50px); /* Keep a max-width for smaller screens if necessary */
            }
        }
    </style>
</head>
<body>

    {{-- Sidebar fijo --}}
    <div class="sidebar position-fixed top-0 start-0 vh-100 d-flex flex-column" style="z-index: 1030;">
        @include('layouts.navigation-vertical')
    </div>

    {{-- Contenedor principal --}}
    <div>
        {{-- Header fijo --}}
        <header id="appHeader" class="position-fixed top-0 border-bottom d-flex justify-content-between align-items-center px-3" style="z-index: 1020;">
            <button id="toggleSidebar" class="btn btn-sm" title="Menú">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center gap-2 header-user-info">
                <div class="text-end">
                    <div class="fw-semibold">{{ Auth::user()->name }}</div>
                    <div class="text-muted small">{{ Auth::user()->email }}</div>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=FF84D5&color=fff"
                     class="rounded-circle flex-shrink-0" width="40" height="40" alt="Avatar" style="border: 2px solid var(--miracle-lilac);">
            </div>
        </header>

        {{-- Contenido --}}
        <main id="appMainContent" >
            {{ $slot }}
        </main>
    </div>

    {{-- JS --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const sidebar = document.querySelector('.sidebar');
        const appHeader = document.getElementById('appHeader');
        const appMainContent = document.getElementById('appMainContent');

        function updateLayout() {
            const sidebarWidth = sidebar.offsetWidth; // Obtiene el ancho actual (70px o 250px)
            appHeader.style.left = `${sidebarWidth}px`;
            appHeader.style.right = `0`;
            appMainContent.style.marginLeft = `${sidebarWidth}px`;
        }

        // Función para guardar el estado del sidebar en localStorage
        function saveSidebarState() {
            if (sidebar.classList.contains('collapsed')) {
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        }

        // Función para restaurar el estado del sidebar desde localStorage
        function restoreSidebarState() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed');
            if (isCollapsed === 'true') {
                sidebar.classList.add('collapsed');
                // IMPORTANTE: Esperar a que la transición CSS termine antes de actualizar el layout
                // 300ms debe coincidir con la duración de la transición en el CSS para .sidebar
                setTimeout(updateLayout, 300);
            } else {
                sidebar.classList.remove('collapsed');
                // Si no está colapsado, puedes actualizar el layout inmediatamente o con un pequeño delay si también tiene transición de apertura
                updateLayout();
            }
        }

        // Restaurar el estado del sidebar al cargar la página
        document.addEventListener('DOMContentLoaded', restoreSidebarState);

        // Modificar el click del botón para guardar el estado
        document.getElementById('toggleSidebar').addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            saveSidebarState(); // Guardar el nuevo estado

            // Esperar a que la transición termine para actualizar el layout
            setTimeout(updateLayout, 300);
        });

        // También actualizar en el redimensionamiento de la ventana
        window.addEventListener('resize', updateLayout);
    </script>

    @stack('scripts')
</body>
</html>
