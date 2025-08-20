<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- CSS personalizado y Bootstrap --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
   @stack('styles')
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            transition: all 0.3s ease;
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
            justify-content: center !important; /* Center the icon when text is hidden */
        }

        header {
            height: 64px;
            background-color: white;
            position: fixed;
            top: 0;
            right: 0; /* Keep it aligned to the right */
            transition: left 0.3s ease; /* Smooth transition only for left */
            padding-right: 1rem; /* Add some padding to the right edge */
        }

        main {
            padding-top: 80px;
            transition: margin-left 0.3s ease;
        }

        #toggleSidebar {
            border: none !important;
            background-color: transparent;
        }

        #toggleSidebar:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .nav-link {
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
        }

        .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .nav-link.active {
            background-color: rgba(0, 0, 0, 0.08) !important;
            color: #000 !important;
            font-weight: 600;
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
    <div class="sidebar position-fixed top-0 start-0 bg-white border-end vh-100 d-flex flex-column" style="z-index: 1030;">
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
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff"
                     class="rounded-circle flex-shrink-0" width="40" height="40" alt="Avatar">
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
<script>
        const sidebar = document.querySelector('.sidebar');
        const appHeader = document.getElementById('appHeader');
        const appMainContent = document.getElementById('appMainContent');
        let isManuallyToggled = false;

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

        // Función para manejar el responsive
        function handleResponsive() {
            const windowWidth = window.innerWidth;
            
            // Solo aplicar auto-colapso si el usuario no ha interactuado manualmente
            if (!isManuallyToggled) {
                if (windowWidth <= 768) {
                    sidebar.classList.add('collapsed');
                } else {
                    sidebar.classList.remove('collapsed');
                }
                setTimeout(updateLayout, 300);
            }
        }

        // Función para restaurar el estado del sidebar desde localStorage
        function restoreSidebarState() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed');
            const windowWidth = window.innerWidth;
            
            // Si hay un estado guardado, usarlo
            if (isCollapsed !== null) {
                isManuallyToggled = true;
                if (isCollapsed === 'true') {
                    sidebar.classList.add('collapsed');
                } else {
                    sidebar.classList.remove('collapsed');
                }
            } else {
                // Si no hay estado guardado, aplicar responsive
                handleResponsive();
            }
            
            setTimeout(updateLayout, 300);
        }

        // Restaurar el estado del sidebar al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            restoreSidebarState();
            
            // Inicializar tooltips para el sidebar colapsado
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Modificar el click del botón para guardar el estado
        document.getElementById('toggleSidebar').addEventListener('click', () => {
            isManuallyToggled = true;
            sidebar.classList.toggle('collapsed');
            saveSidebarState(); // Guardar el nuevo estado

            // Esperar a que la transición termine para actualizar el layout
            setTimeout(updateLayout, 300);
        });

        // Manejar el redimensionamiento de la ventana
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                handleResponsive();
                updateLayout();
            }, 250);
        });

        // Manejar clicks en submenús cuando el sidebar está colapsado
        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('collapsed')) {
                const submenuTrigger = e.target.closest('[data-bs-toggle="collapse"]');
                if (submenuTrigger) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Expandir temporalmente el sidebar
                    sidebar.classList.remove('collapsed');
                    setTimeout(() => {
                        updateLayout();
                        // Activar el collapse después de expandir
                        const collapse = new bootstrap.Collapse(document.querySelector(submenuTrigger.getAttribute('href')), {
                            toggle: true
                        });
                    }, 300);
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>