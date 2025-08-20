<div class="d-flex flex-column h-100">
    {{-- Logo --}}
    <div class="d-flex justify-content-center align-items-center py-3 border-bottom">
        <a href="/" class="text-decoration-none">
            <img style="width: 80%; margin-left: 5%;" src="{{ asset('images/logo.png') }}" class="logo-full" width="100" alt="Logo">
            <img src="{{ asset('images/logo.png') }}" class="logo-icon d-none" width="40" alt="Logo Icon">
        </a>
    </div>

    {{-- Navegación --}}
    <nav class="nav flex-column px-2 py-3 overflow-auto flex-grow-1">
        {{-- Inicio --}}
        <a href="/dashboard"
           class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('dashboard') ? 'active' : 'text-dark' }}"
           title="Inicio">
            <i class="bi bi-house-door-fill"></i>
            <span>Inicio</span>
        </a>
        @if (auth()->user()->getRoleNames()->first() == 'admin')
            <a href="/usuarios"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('usuarios*') ? 'active' : 'text-dark' }}"
               title="Usuarios">
                <i class="bi bi-people-fill"></i>
                <span>Usuarios</span>
            </a>
        @endif
        @if(auth()->user()->empresa)
            <div class="nav-item">
                <a href="#empresaSubmenu" 
                   class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is(['empresa*', 'productos*', 'clientes*', 'categorias*']) ? 'active' : 'text-dark' }}"
                   data-bs-toggle="collapse" 
                   aria-expanded="{{ request()->is(['empresa*', 'productos*', 'clientes*', 'categorias*']) ? 'true' : 'false' }}">
                    <i class="bi bi-building"></i>
                    <span>Mi Empresa</span>
                    <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
                </a>
                <div class="collapse {{ request()->is(['empresa*', 'productos*', 'clientes*', 'categorias*']) ? 'show' : '' }}" id="empresaSubmenu">
                    <div class="ps-3">
                        <a href="/empresa"
                           class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('empresa') ? 'active' : 'text-dark' }}">
                            <i class="bi bi-gear"></i>
                            <span>Configuración</span>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <a href="/empresa"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('empresa*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-building"></i>
                <span>Mi Empresa</span>
            </a>
        @endif

    </nav>

    {{-- Botón Salir --}}
    <div class="mt-auto p-3 border-top">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-start gap-2">
                <i class="bi bi-box-arrow-right"></i>
                <span class="logout-label">Salir</span>
            </button>
        </form>
    </div>
</div>

<style>
    /* Estilos para submenús */
    .nav-item .nav-link[data-bs-toggle="collapse"] {
        position: relative;
    }
    
    .submenu-icon {
        transition: transform 0.3s ease;
        font-size: 0.8rem;
    }
    
    .nav-link[aria-expanded="true"] .submenu-icon {
        transform: rotate(180deg);
    }
    
    .collapse .ps-3 {
        border-left: 2px solid #dee2e6;
        margin-left: 1rem;
    }
    
    .collapse .ps-3 .nav-link {
        font-size: 0.9rem;
        padding: 0.4rem 0.75rem;
    }
    
    /* Ocultar iconos de submenú cuando sidebar está colapsado */
    .sidebar.collapsed .submenu-icon {
        display: none;
    }
    
    /* Ajustar submenús cuando sidebar está colapsado */
    .sidebar.collapsed .collapse {
        position: absolute;
        left: 70px;
        top: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        min-width: 200px;
        z-index: 1050;
    }
    
    .sidebar.collapsed .collapse .ps-3 {
        border-left: none;
        margin-left: 0;
        padding: 0.5rem;
    }
    
    /* Asegurar que el menú es scrolleable */
    .nav.overflow-auto {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
        overflow-x: hidden;
    }
    
    /* Estilo para scrollbar */
    .nav.overflow-auto::-webkit-scrollbar {
        width: 6px;
    }
    
    .nav.overflow-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .nav.overflow-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    
    .nav.overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>