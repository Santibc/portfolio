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

        {{-- Landing Page --}}
        <a href="{{ route('admin.landing.index') }}"
           class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('admin/landing*') ? 'active' : 'text-dark' }}"
           title="Landing Page">
            <i class="bi bi-globe"></i>
            <span>Landing Page</span>
        </a>

        {{-- Districts --}}
        <a href="{{ route('admin.districts.index') }}"
           class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('admin/districts*') ? 'active' : 'text-dark' }}"
           title="Districts">
            <i class="bi bi-geo-alt-fill"></i>
            <span>Districts</span>
        </a>

        {{-- Coupons --}}
        <a href="{{ route('admin.coupons.index') }}"
           class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('admin/coupons*') ? 'active' : 'text-dark' }}"
           title="Coupons">
            <i class="bi bi-ticket-perforated-fill"></i>
            <span>Coupons</span>
        </a>

        {{-- Cleaning Orders --}}
        <a href="{{ route('admin.cleaning-orders.index') }}"
           class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('admin/cleaning-orders*') ? 'active' : 'text-dark' }}"
           title="Cleaning Orders">
            <i class="bi bi-cart-check-fill"></i>
            <span>Cleaning Orders</span>
        </a>
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