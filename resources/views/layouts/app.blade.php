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
    {{-- DataTables Buttons (faltante en el bundle: arregla el dropdown de "Filas"/"Columnas") --}}
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.min.css" rel="stylesheet">
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

            /* Sidebar responsive en móvil */
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050 !important;
            }

            .sidebar.show-mobile {
                transform: translateX(0);
            }

            /* Overlay para cerrar sidebar en móvil */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }

            /* Header y main ocupan todo el ancho en móvil */
            header#appHeader {
                left: 0 !important;
            }

            main#appMainContent {
                margin-left: 0 !important;
            }
        }

        /* Botón de salir - estilos mejorados para todas las resoluciones */
        .btn-logout {
            background-color: var(--miracle-pink-light);
            color: var(--miracle-pink);
            border: 1px solid var(--miracle-pink);
            transition: all 0.2s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.9rem;
            min-height: 42px;
        }

        .btn-logout:hover {
            background-color: var(--miracle-pink);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(255, 132, 213, 0.3);
        }

        .btn-logout:active {
            transform: translateY(0);
        }

        .btn-logout i {
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .btn-logout .logout-label {
            transition: opacity 0.2s ease, max-width 0.2s ease;
            overflow: hidden;
            white-space: nowrap;
        }

        /* Sidebar colapsado - solo mostrar icono */
        .sidebar.collapsed .btn-logout {
            justify-content: center;
            padding: 0.625rem;
        }

        .sidebar.collapsed .btn-logout .logout-label {
            opacity: 0;
            max-width: 0;
            display: none;
        }

        /* Responsive móvil */
        @media (max-width: 768px) {
            .btn-logout {
                padding: 0.75rem 1rem;
                font-size: 1rem;
            }

            /* Asegurar que el sidebar en móvil tenga scroll */
            .sidebar {
                max-height: 100vh;
                overflow: hidden;
            }

            .sidebar > div {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .sidebar nav {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
                -webkit-overflow-scrolling: touch;
            }

            /* El botón de salir siempre visible al fondo */
            .sidebar .mt-auto {
                flex-shrink: 0;
                background: white;
                position: sticky;
                bottom: 0;
            }
        }

        /* Responsive tablet */
        @media (min-width: 769px) and (max-width: 1024px) {
            .btn-logout {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
            }
        }

        /* DataTables Buttons - integración con theme Miracle */
        div.dt-button-collection {
            background-color: #fff;
            border: 1px solid var(--miracle-lilac);
            border-radius: 0.5rem;
            box-shadow: 0 4px 16px rgba(56, 46, 101, 0.12);
            padding: 0.25rem;
            min-width: 8rem;
            z-index: 2050 !important;
        }
        div.dt-button-collection button.dt-button {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.4rem 0.75rem;
            margin: 0;
            background: transparent;
            border: none;
            border-radius: 0.35rem;
            color: var(--miracle-dark);
            text-decoration: none !important;
            font-size: 0.875rem;
            cursor: pointer;
        }
        div.dt-button-collection button.dt-button:hover {
            background-color: var(--miracle-lilac-light);
            color: var(--miracle-dark);
        }
        div.dt-button-collection button.dt-button.dt-button-active,
        div.dt-button-collection button.dt-button.active {
            background-color: var(--miracle-pink-light);
            color: var(--miracle-pink);
            font-weight: 600;
            text-decoration: none !important;
        }
        div.dt-button-background {
            background: rgba(56, 46, 101, 0.15);
        }
        /* Botones principales del toolbar */
        div.dt-buttons {
            display: inline-flex;
            gap: 0.4rem;
            flex-wrap: wrap;
            margin-bottom: 0.5rem;
        }
        div.dt-buttons .dt-button {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.85rem;
            margin: 0;
            background-color: #fff;
            border: 1px solid var(--miracle-lilac);
            border-radius: 0.5rem;
            color: var(--miracle-dark);
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none !important;
            box-shadow: 0 1px 2px rgba(56, 46, 101, 0.05);
            transition: all 0.15s ease;
            cursor: pointer;
        }
        div.dt-buttons .dt-button:hover,
        div.dt-buttons .dt-button:focus {
            background-color: var(--miracle-lilac-light);
            border-color: var(--miracle-lilac);
            color: var(--miracle-dark);
            box-shadow: 0 2px 6px rgba(188, 169, 245, 0.25);
            outline: none;
        }
        div.dt-buttons .dt-button.btn-outline-success,
        div.dt-buttons .buttons-excel {
            border-color: var(--miracle-aqua);
            color: #2d7a78;
        }
        div.dt-buttons .dt-button.btn-outline-success:hover,
        div.dt-buttons .buttons-excel:hover {
            background-color: var(--miracle-aqua);
            color: #fff;
        }
        div.dt-buttons .dt-button i,
        div.dt-buttons .dt-button .bi {
            font-size: 0.95rem;
        }
        /* Caret del dropdown (Filas, Columnas) */
        div.dt-buttons .dt-button.buttons-collection::after,
        div.dt-buttons .dt-button.buttons-page-length::after,
        div.dt-buttons .dt-button.buttons-colvis::after {
            content: '';
            display: inline-block;
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid currentColor;
            margin-left: 0.35rem;
            opacity: 0.7;
        }
        /* Buscador de DataTables */
        div.dt-container .dt-search input,
        div.dt-container input.dt-input[type="search"] {
            border: 1px solid var(--miracle-lilac);
            border-radius: 0.5rem;
            padding: 0.4rem 0.75rem;
            font-size: 0.875rem;
        }
        div.dt-container .dt-search input:focus,
        div.dt-container input.dt-input[type="search"]:focus {
            border-color: var(--miracle-pink);
            outline: none;
            box-shadow: 0 0 0 0.15rem rgba(255, 132, 213, 0.2);
        }
    </style>
</head>
<body>

    {{-- Overlay para móvil --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar fijo --}}
    <div class="sidebar position-fixed top-0 start-0 vh-100 d-flex flex-column" id="mainSidebar" style="z-index: 1030;">
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
                @if(Auth::user() && Auth::user()->hasRole('cajero_principal'))
                    <a href="{{ route('pdv.caja.abrir-cajon') }}" target="_blank"
                       class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 me-2"
                       title="Abrir cajón monedero">
                        <i class="bi bi-cash-stack"></i>
                        <span class="d-none d-md-inline">Abrir Caja</span>
                    </a>
                @endif
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
        const sidebar = document.getElementById('mainSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const appHeader = document.getElementById('appHeader');
        const appMainContent = document.getElementById('appMainContent');

        // Función para detectar si es móvil
        function isMobile() {
            return window.innerWidth <= 768;
        }

        function updateLayout() {
            if (isMobile()) {
                // En móvil, header y main ocupan todo el ancho
                appHeader.style.left = '0';
                appHeader.style.right = '0';
                appMainContent.style.marginLeft = '0';
            } else {
                // En desktop, ajustar según el ancho del sidebar
                const sidebarWidth = sidebar.offsetWidth;
                appHeader.style.left = `${sidebarWidth}px`;
                appHeader.style.right = '0';
                appMainContent.style.marginLeft = `${sidebarWidth}px`;
            }
        }

        // Función para guardar el estado del sidebar en localStorage (solo desktop)
        function saveSidebarState() {
            if (!isMobile() && sidebar.classList.contains('collapsed')) {
                localStorage.setItem('sidebarCollapsed', 'true');
            } else if (!isMobile()) {
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        }

        // Función para restaurar el estado del sidebar desde localStorage
        function restoreSidebarState() {
            if (isMobile()) {
                // En móvil, asegurarse de que el sidebar está oculto
                sidebar.classList.remove('show-mobile');
                sidebar.classList.remove('collapsed');
                sidebarOverlay.classList.remove('show');
                updateLayout();
            } else {
                // En desktop, restaurar el estado guardado
                const isCollapsed = localStorage.getItem('sidebarCollapsed');
                if (isCollapsed === 'true') {
                    sidebar.classList.add('collapsed');
                    setTimeout(updateLayout, 300);
                } else {
                    sidebar.classList.remove('collapsed');
                    updateLayout();
                }
            }
        }

        // Función para cerrar sidebar en móvil
        function closeMobileSidebar() {
            sidebar.classList.remove('show-mobile');
            sidebarOverlay.classList.remove('show');
        }

        // Función para abrir sidebar en móvil
        function openMobileSidebar() {
            sidebar.classList.add('show-mobile');
            sidebarOverlay.classList.add('show');
        }

        // Restaurar el estado del sidebar al cargar la página
        document.addEventListener('DOMContentLoaded', restoreSidebarState);

        // Click en el botón toggle
        document.getElementById('toggleSidebar').addEventListener('click', () => {
            if (isMobile()) {
                // En móvil, mostrar/ocultar con overlay
                if (sidebar.classList.contains('show-mobile')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            } else {
                // En desktop, colapsar/expandir
                sidebar.classList.toggle('collapsed');
                saveSidebarState();
                setTimeout(updateLayout, 300);
            }
        });

        // Click en overlay cierra el sidebar en móvil
        sidebarOverlay.addEventListener('click', closeMobileSidebar);

        // Actualizar en el redimensionamiento de la ventana
        window.addEventListener('resize', () => {
            // Cerrar sidebar móvil si cambiamos a desktop
            if (!isMobile() && sidebar.classList.contains('show-mobile')) {
                closeMobileSidebar();
            }
            restoreSidebarState();
        });
    </script>

    @stack('scripts')
</body>
</html>
