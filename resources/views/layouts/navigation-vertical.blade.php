<div class="d-flex flex-column h-100 bg-gradient-to-b from-pink-500 to-purple-600 text-white" style="background: linear-gradient(to bottom, #FF00C1, #0B00F9);">
    {{-- Logo --}}
    <div class="d-flex justify-content-center align-items-center py-4">
        <a href="/" class="text-decoration-none">
            <img style="width: 140px;" src="{{ asset('images/logo1.png') }}" class="logo-full" alt="Logo">
            <img src="{{ asset('images/logo1.png') }}" class="logo-icon d-none" width="40" alt="Logo Icon">
        </a>
    </div>

    {{-- Navegación --}}
    <nav class="nav flex-column px-3 py-3 overflow-auto flex-grow-1">
        {{-- Inicio --}}
        <a href="/dashboard"
           class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is('dashboard') ? 'bg-pink-500' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"
           title="Inicio"
           onmouseover="this.style.transform='translateX(5px)'" 
           onmouseout="this.style.transform='translateX(0)'">
            <i class="bi bi-house-door-fill"></i>
            <span>Inicio</span>
        </a>
            <a href="/membresias"
               class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is('membresias*') ? 'bg-pink-500' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"
               title="Membresias"
               onmouseover="this.style.transform='translateX(5px)'; this.style.backgroundColor='rgba(255,255,255,0.2)'" 
               onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='{{ request()->is('membresias*') ? '' : 'transparent' }}'">
                <i class="bi bi-award"></i>
                <span>Membresias</span>
            </a>
        {{-- Menú Admin --}}
        @if (auth()->user()->getRoleNames()->first() == 'admin')
            <a href="/usuarios"
               class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is('usuarios*') ? 'bg-pink-500' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"
               title="Usuarios"
               onmouseover="this.style.transform='translateX(5px)'; this.style.backgroundColor='rgba(255,255,255,0.2)'" 
               onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='{{ request()->is('usuarios*') ? '' : 'transparent' }}'">
                <i class="bi bi-person-circle"></i>
                <span>Usuarios</span>
            </a>
            <a href="admin/dashboard"
               class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is('admin/dashboard') && !request()->is('admin/dashboard/membresias') ? 'bg-pink-500' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"
               title="Comisiones"
               onmouseover="this.style.transform='translateX(5px)'; this.style.backgroundColor='rgba(255,255,255,0.2)'" 
               onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='{{ request()->is('admin/dashboard') && !request()->is('admin/dashboard/membresias') ? '' : 'transparent' }}'">
                <i class="bi bi-currency-dollar"></i>
                <span>Comisiones</span>
            </a>
            <a href="{{ route('admin.dashboard.membresias') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is('admin/dashboard/membresias*') ? 'bg-pink-500' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"
               title="Membresías"
               onmouseover="this.style.transform='translateX(5px)'; this.style.backgroundColor='rgba(255,255,255,0.2)'" 
               onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='{{ request()->is('admin/dashboard/membresias*') ? '' : 'transparent' }}'">
                <i class="bi bi-award"></i>
                <span>Dashboard Membresías</span>
            </a>
            <a href="{{ route('admin.planes-membresia.index') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is('admin/planes-membresia*') ? 'bg-pink-500' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"
               title="Planes de Membresía"
               onmouseover="this.style.transform='translateX(5px)'; this.style.backgroundColor='rgba(255,255,255,0.2)'" 
               onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='{{ request()->is('admin/planes-membresia*') ? '' : 'transparent' }}'">
                <i class="bi bi-credit-card"></i>
                <span>Planes Membresía</span>
            </a>
        @endif

        {{-- Mi Empresa (con submenú) --}}
        @if(auth()->user()->empresa)
            <div class="nav-item">
                <a href="#empresaSubmenu" 
                   class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is(['empresa*', 'productos*', 'clientes*', 'categorias*']) ? 'bg-pink-500' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"
                   title="Mi empresa"
                   data-bs-toggle="collapse" 
                   aria-expanded="{{ request()->is(['empresa*', 'productos*', 'clientes*', 'categorias*']) ? 'true' : 'false' }}"
                   onmouseover="this.style.transform='translateX(5px)'; this.style.backgroundColor='rgba(255,255,255,0.2)'" 
                   onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='{{ request()->is(['empresa*', 'productos*', 'clientes*', 'categorias*']) ? '' : 'transparent' }}'">
                    <i class="bi bi-building"></i>
                    <span>Mi Empresa</span>
                    <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
                </a>
                <div class="collapse {{ request()->is(['empresa*', 'productos*', 'clientes*', 'categorias*']) ? 'show' : '' }}" id="empresaSubmenu">
                    <div class="ps-3">
                        <a href="/empresa"
                           class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is('empresa') ? 'bg-pink-500 shadow-lg' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease;"
                           title="Configuración"
                           onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'" 
                           onmouseout="this.style.backgroundColor='{{ request()->is('empresa') ? '' : 'transparent' }}'">
                            <i class="bi bi-gear"></i>
                            <span>Configuración</span>
                        </a>
                        <a href="/categorias"
                           class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is('categorias*') ? 'bg-pink-500 shadow-lg' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease;"
                           title="Categorías"
                           onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'" 
                           onmouseout="this.style.backgroundColor='{{ request()->is('categorias*') ? '' : 'transparent' }}'">
                            <i class="bi bi-folder"></i>
                            <span>Categorías</span>
                        </a>
                        <a href="/productos"
                           class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is('productos*') ? 'bg-pink-500 shadow-lg' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease;"
                           title="Productos"
                           onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'" 
                           onmouseout="this.style.backgroundColor='{{ request()->is('productos*') ? '' : 'transparent' }}'">
                            <i class="bi bi-box"></i>
                            <span>Productos</span>
                        </a>
{{--                         <a href="/clientes"
                           class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('clientes*') ? 'active' : 'text-dark' }}">
                            <i class="bi bi-person-badge"></i>
                            <span>Clientes</span>
                        </a> --}}
                        @if(auth()->user()->empresa->activo)
                            <a href="/empresa/preview" target="_blank"
                               class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is('empresa.preview') ? 'bg-pink-500 shadow-lg' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease;"
                               title="Ver mi tienda"
                               onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'" 
                               onmouseout="this.style.backgroundColor='{{ request()->is('empresa.preview') ? '' : 'transparent' }}'">
                                <i class="bi bi-eye"></i>
                                <span>Ver Mi Tienda</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <a href="/empresa"
               class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->is('empresa*') ? 'bg-pink-500' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"
               title="Mi empresa"
               onmouseover="this.style.transform='translateX(5px)'; this.style.backgroundColor='rgba(255,255,255,0.2)'" 
               onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='{{ request()->is('empresa*') ? '' : 'transparent' }}'">
                <i class="bi bi-building"></i>
                <span>Mi Empresa</span>
            </a>
        @endif

        {{-- Ventas --}}
        @if(auth()->user()->hasRole(['vendedor', 'admin']))
{{--             <a href="{{ route('catalogo') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('catalogo*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-shop"></i>
                <span>Catálogo</span>
            </a> --}}
{{--             <a href="{{ route('solicitudes') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('solicitudes*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Solicitudes</span>
            </a> --}}
{{--             <a href="{{ route('enlaces') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('enlaces*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-link-45deg"></i>
                <span>Enlaces</span>
            </a> --}}
            
            {{-- Gestión de Stock (con submenú) --}}
            @if(auth()->user()->empresa)
                <div class="nav-item">
                    <a href="#stockSubmenu" 
                       class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->routeIs('stock.*') ? 'bg-pink-500' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"
                       title="Gestión de stock"
                       data-bs-toggle="collapse" 
                       aria-expanded="{{ request()->routeIs('stock.*') ? 'true' : 'false' }}"
                       onmouseover="this.style.transform='translateX(5px)'; this.style.backgroundColor='rgba(255,255,255,0.2)'" 
                       onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='{{ request()->routeIs('stock.*') ? '' : 'transparent' }}'">
                        <i class="bi bi-archive"></i>
                        <span>Gestión de Stock</span>
                        <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('stock.*') ? 'show' : '' }}" id="stockSubmenu">
                        <div class="ps-3">
                            <a href="{{ route('stock.index') }}"
                               class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->routeIs('stock.index') ? 'bg-pink-500 shadow-lg' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease;"
                               title="Inventario"
                               onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'" 
                               onmouseout="this.style.backgroundColor='{{ request()->routeIs('stock.index') ? '' : 'transparent' }}'">
                                <i class="bi bi-clipboard-check"></i>
                                <span>Inventario</span>
                            </a>
                            <a href="{{ route('stock.dashboard') }}"
                               class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->routeIs('stock.dashboard*') ? 'bg-pink-500 shadow-lg' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease;"
                               title="Dashboard"
                               onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'" 
                               onmouseout="this.style.backgroundColor='{{ request()->routeIs('stock.dashboard*') ? '' : 'transparent' }}'">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('stock.reporte-movimiento') }}"
                               class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->routeIs('stock.reporte-movimiento*') ? 'bg-pink-500 shadow-lg' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease;"
                               title="Reportes"
                               onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'" 
                               onmouseout="this.style.backgroundColor='{{ request()->routeIs('stock.reporte-movimiento*') ? '' : 'transparent' }}'">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                                <span>Reportes</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                {{-- Compras --}}
                <a href="{{ route('compras') }}"
                   class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->routeIs('compras*') ? 'bg-pink-500' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"
                   title="Compras"
                   onmouseover="this.style.transform='translateX(5px)'; this.style.backgroundColor='rgba(255,255,255,0.2)'" 
                   onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='{{ request()->routeIs('compras*') ? '' : 'transparent' }}'">
                    <i class="bi bi-cart-plus"></i>
                    <span>Compras</span>
                </a>
            @else
                <a href="{{ route('stock.index') }}"
                   class="nav-link mb-2 d-flex align-items-center gap-2 text-white {{ request()->routeIs('stock.index') ? 'bg-pink-500' : '' }}" style="transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"
                   title="Gestión de stock"
                   onmouseover="this.style.transform='translateX(5px)'; this.style.backgroundColor='rgba(255,255,255,0.2)'" 
                   onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='{{ request()->routeIs('stock.index') ? '' : 'transparent' }}'">
                    <i class="bi bi-archive"></i>
                    <span>Gestión de Stock</span>
                </a>
            @endif
        @endif
    </nav>

    {{-- Botón Salir --}}
    <div class="mt-auto p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn w-100 d-flex align-items-center justify-content-start gap-2 text-white" style="border: 2px solid rgba(255,255,255,0.5); background: transparent; transition: transform 0.2s ease, background-color 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.375rem;" 
                    onmouseover="this.style.backgroundColor='white'; this.style.color='#FF00C1'; this.style.transform='translateX(5px)'" 
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='white'; this.style.transform='translateX(0)'">
                <i class="bi bi-box-arrow-right"></i>
                <span class="logout-label">Salir</span>
            </button>
        </form>
    </div>
</div>

<style>
    /* Fuente personalizada */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    .sidebar {
        font-family: 'Poppins', sans-serif;
    }
    
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
        border-left: 2px solid rgba(255,255,255,0.3);
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
    
    /* Ajustar submenús cuando sidebar está colapsado - mantener distribución vertical y mostrar siempre */
    .sidebar.collapsed .collapse {
        position: static;
        background: rgba(255,255,255,0.1);
        border: none;
        border-radius: 0.375rem;
        box-shadow: none;
        min-width: auto;
        z-index: auto;
        margin: 0.25rem 0;
        display: block !important;
        visibility: visible !important;
        height: auto !important;
    }
    
    .sidebar.collapsed .collapse .ps-3 {
        border-left: none;
        margin-left: 0;
        padding: 0;
        padding-left: 0 !important;
    }
    
    /* Asegurar que el menú es scrolleable - forzar distribución vertical SIEMPRE */
    .nav.overflow-auto {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
        overflow-x: hidden !important;
        display: flex !important;
        flex-direction: column !important;
        flex-wrap: nowrap !important;
        width: 100%;
    }
    
    /* Estilo para scrollbar personalizado */
    .nav.overflow-auto::-webkit-scrollbar {
        width: 6px;
    }
    
    .nav.overflow-auto::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.1);
        border-radius: 3px;
    }
    
    .nav.overflow-auto::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.3);
        border-radius: 3px;
    }
    
    .nav.overflow-auto::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.5);
    }
    
    /* Animaciones adicionales */
    .nav-link {
        transition: all 0.3s ease;
        width: 100%;
        flex-shrink: 0;
    }
    
    .nav-link:hover {
        transform: translateX(5px) !important;
    }
    
    /* Evitar desplazamiento horizontal cuando sidebar está colapsado */
    .sidebar.collapsed .nav-link:hover {
        transform: none !important;
    }
    
    /* Mantener elementos en distribución vertical cuando colapsado */
    .sidebar.collapsed .nav {
        align-items: stretch;
    }
    
    .sidebar.collapsed .nav-item,
    .sidebar.collapsed .nav-link {
        width: 100%;
        flex-shrink: 0;
    }
    
    /* Asegurar que los submenús también mantengan distribución vertical sin sangría */
    .sidebar.collapsed .collapse .ps-3 {
        display: flex;
        flex-direction: column;
        width: 100%;
        padding-left: 0 !important;
        margin-left: 0 !important;
        border-left: none !important;
    }
    
    .sidebar.collapsed .collapse .ps-3 .nav-link {
        width: 100%;
        margin-bottom: 0.25rem;
    }
    
    /* Evitar overflow horizontal en el contenedor principal */
    .sidebar.collapsed {
        overflow-x: hidden;
    }
    
    /* Asegurar que el contenido del sidebar no se desborde horizontalmente */
    .sidebar .nav,
    .sidebar .nav-item,
    .sidebar .collapse {
        max-width: 100%;
        overflow-x: hidden;
    }
    
    /* Forzar distribución vertical cuando colapsado - no permitir desbordamiento horizontal */
    .sidebar.collapsed .nav {
        flex-wrap: nowrap !important;
        flex-direction: column !important;
        align-items: center !important;
        width: 100% !important;
    }
    
    .sidebar.collapsed .nav-item,
    .sidebar.collapsed .nav-link,
    .sidebar.collapsed .collapse {
        flex-shrink: 0 !important;
        width: 100% !important;
        max-width: 50px !important;
        box-sizing: border-box !important;
    }
    
    .sidebar.collapsed .collapse .ps-3 .nav-link {
        padding: 0.08rem !important;
        margin: 0.01rem auto !important;
        max-width: 45px !important;
        min-height: 23px !important;
        font-size: 0.8rem !important;
        justify-content: center !important;
    }
    
    /* Forzar scroll vertical únicamente */
    .sidebar.collapsed {
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }
    
    .sidebar.collapsed .nav.overflow-auto {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        flex-wrap: nowrap !important;
    }
    
    /* Aplicar los mismos estilos para sidebar expandido */
    .sidebar .nav {
        flex-wrap: nowrap !important;
        flex-direction: column !important;
        width: 100% !important;
        overflow-x: hidden !important;
    }
    
    .sidebar .nav-item,
    .sidebar .nav-link,
    .sidebar .collapse {
        flex-shrink: 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
    }
    
    .sidebar .collapse .ps-3 {
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
        overflow-x: hidden !important;
    }
    
    .sidebar .collapse .ps-3 .nav-link {
        width: 100% !important;
        margin-bottom: 0.25rem !important;
        overflow-x: hidden !important;
    }
    
    /* Cuando colapsado, eliminar cualquier margen/padding lateral de submenús */
    .sidebar.collapsed .collapse {
        margin: 0.05rem 0 !important;
        padding: 0 !important;
    }
</style>