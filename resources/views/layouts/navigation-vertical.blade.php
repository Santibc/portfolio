<div class="d-flex flex-column h-100 text-white">
    {{-- Logo --}}
    <div class="d-flex justify-content-center align-items-center py-3 border-bottom border-white-25" style="min-height: 80px;">
        <a href="{{ url('/') }}" class="text-decoration-none d-flex justify-content-center">
            <img src="{{ asset('images/logo.png') }}" class="sidebar-logo-full" style="max-width: 140px; height: auto;" alt="Logo">
            <img src="{{ asset('images/ico.png') }}" class="sidebar-logo-icon" style="width: 36px; display: none;" alt="Logo">
        </a>
    </div>

    {{-- Navegación --}}
    <nav class="flex-grow-1 overflow-auto py-2 px-2">
        {{-- Inicio --}}
        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}"
           title="Inicio">
            <i class="bi bi-house-door-fill"></i>
            <span>Inicio</span>
        </a>

        {{-- Panel Cliente --}}
        @if(auth()->user()->hasRole('cliente'))
            <a href="{{ route('cliente.compras') }}"
               class="sidebar-link {{ request()->routeIs('cliente.*') ? 'active' : '' }}"
               title="Mis Compras">
                <i class="bi bi-bag-check"></i>
                <span>Mis Compras</span>
            </a>
        @endif

        {{-- Opciones para vendedores/admin --}}
        @unless(auth()->user()->hasRole('cliente'))
            @if(auth()->user()->empresa)
                <a href="{{ route('dashboard-analitico') }}"
                   class="sidebar-link {{ request()->routeIs('dashboard-analitico*') ? 'active' : '' }}"
                   title="Dashboard">
                    <i class="bi bi-graph-up"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('compras') }}"
                   class="sidebar-link {{ request()->routeIs('compras*') ? 'active' : '' }}"
                   title="Pedidos">
                    <i class="bi bi-cart-check"></i>
                    <span>Pedidos</span>
                </a>

                <a href="{{ route('productos') }}"
                   class="sidebar-link {{ request()->is('productos*') ? 'active' : '' }}"
                   title="Productos">
                    <i class="bi bi-box-seam"></i>
                    <span>Productos</span>
                </a>

                <a href="{{ route('categorias') }}"
                   class="sidebar-link {{ request()->is('categorias*') ? 'active' : '' }}"
                   title="Categorías">
                    <i class="bi bi-folder"></i>
                    <span>Categorías</span>
                </a>

                <a href="{{ route('gestion-clientes.index') }}"
                   class="sidebar-link {{ request()->routeIs('gestion-clientes.*') ? 'active' : '' }}"
                   title="Clientes">
                    <i class="bi bi-people"></i>
                    <span>Clientes</span>
                </a>

                <a href="{{ route('stock.index') }}"
                   class="sidebar-link {{ request()->routeIs('stock.*') ? 'active' : '' }}"
                   title="Inventario">
                    <i class="bi bi-archive"></i>
                    <span>Inventario</span>
                </a>

                <a href="{{ route('descuentos.index') }}"
                   class="sidebar-link {{ request()->routeIs('descuentos*') ? 'active' : '' }}"
                   title="Descuentos">
                    <i class="bi bi-tag"></i>
                    <span>Descuentos</span>
                </a>

                {{-- Logística con submenú --}}
                <div class="sidebar-submenu-container">
                    <a href="#logisticaMenu"
                       class="sidebar-link {{ request()->routeIs('logistica.*') || request()->routeIs('repartidores.*') ? 'active' : '' }}"
                       data-bs-toggle="collapse"
                       title="Logística">
                        <i class="bi bi-truck"></i>
                        <span>Logística</span>
                        <i class="bi bi-chevron-down ms-auto sidebar-chevron"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('logistica.*') || request()->routeIs('repartidores.*') ? 'show' : '' }}" id="logisticaMenu">
                        <div class="sidebar-submenu">
                            <a href="{{ route('logistica.zonas.index') }}" class="sidebar-sublink {{ request()->routeIs('logistica.zonas.*') ? 'active' : '' }}" title="Zonas">
                                <i class="bi bi-geo-alt"></i>
                                <span>Zonas</span>
                            </a>
                            <a href="{{ route('logistica.tarifas.index') }}" class="sidebar-sublink {{ request()->routeIs('logistica.tarifas.*') ? 'active' : '' }}" title="Tarifas">
                                <i class="bi bi-cash-coin"></i>
                                <span>Tarifas</span>
                            </a>
                            <a href="{{ route('logistica.horarios.index') }}" class="sidebar-sublink {{ request()->routeIs('logistica.horarios.*') ? 'active' : '' }}" title="Horarios">
                                <i class="bi bi-clock"></i>
                                <span>Horarios</span>
                            </a>
                            <a href="{{ route('logistica.capacidad.index') }}" class="sidebar-sublink {{ request()->routeIs('logistica.capacidad.*') ? 'active' : '' }}" title="Capacidad">
                                <i class="bi bi-calendar-event"></i>
                                <span>Capacidad</span>
                            </a>
                            <a href="{{ route('repartidores.index') }}" class="sidebar-sublink {{ request()->routeIs('repartidores.*') ? 'active' : '' }}" title="Repartidores">
                                <i class="bi bi-person-badge"></i>
                                <span>Repartidores</span>
                            </a>
                        </div>
                    </div>
                </div>

                <hr class="my-2 border-white border-opacity-25">

                <a href="{{ route('empresa.index') }}"
                   class="sidebar-link {{ request()->is('empresa') ? 'active' : '' }}"
                   title="Configuración">
                    <i class="bi bi-gear"></i>
                    <span>Configuración</span>
                </a>

                @if(auth()->user()->empresa->activo)
                    <a href="{{ route('home') }}" target="_blank"
                       class="sidebar-link"
                       title="Ver Tienda">
                        <i class="bi bi-shop"></i>
                        <span>Ver Tienda</span>
                    </a>
                @endif
            @else
                <a href="{{ route('empresa.index') }}"
                   class="sidebar-link {{ request()->is('empresa*') ? 'active' : '' }}"
                   title="Mi Empresa">
                    <i class="bi bi-building"></i>
                    <span>Mi Empresa</span>
                </a>
            @endif
        @endunless
    </nav>

    {{-- Botón Salir --}}
    <div class="p-2 border-top border-white-25">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-100 text-start border-0 bg-transparent" title="Salir">
                <i class="bi bi-box-arrow-right"></i>
                <span>Salir</span>
            </button>
        </form>
    </div>
</div>

<style>
/* ========== ESTILOS DEL SIDEBAR ========== */
.sidebar-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    margin-bottom: 4px;
    border-radius: 8px;
    color: white !important;
    text-decoration: none;
    font-size: 14px;
    transition: background 0.2s ease;
}

.sidebar-link:hover {
    background: rgba(255,255,255,0.15);
    color: white !important;
}

.sidebar-link.active {
    background: rgba(255,255,255,0.25);
}

.sidebar-link i:first-child {
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.sidebar-chevron {
    font-size: 12px;
    transition: transform 0.2s;
}

.sidebar-link[aria-expanded="true"] .sidebar-chevron {
    transform: rotate(180deg);
}

/* Submenú */
.sidebar-submenu {
    padding-left: 20px;
    border-left: 2px solid rgba(255,255,255,0.2);
    margin-left: 22px;
    margin-bottom: 8px;
}

.sidebar-sublink {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 6px;
    color: rgba(255,255,255,0.85) !important;
    text-decoration: none;
    font-size: 13px;
    transition: background 0.2s ease;
}

.sidebar-sublink:hover {
    background: rgba(255,255,255,0.1);
    color: white !important;
}

.sidebar-sublink.active {
    background: rgba(255,255,255,0.2);
    color: white !important;
}

.sidebar-sublink i {
    width: 16px;
    text-align: center;
    font-size: 14px;
}

/* ========== SIDEBAR COLAPSADO ========== */
.sidebar.collapsed .sidebar-logo-full {
    display: none !important;
}

.sidebar.collapsed .sidebar-logo-icon {
    display: block !important;
}

.sidebar.collapsed .sidebar-link span,
.sidebar.collapsed .sidebar-sublink span,
.sidebar.collapsed .sidebar-chevron {
    display: none;
}

.sidebar.collapsed .sidebar-link {
    justify-content: center;
    padding: 10px;
}

.sidebar.collapsed .sidebar-link i:first-child {
    font-size: 18px;
}

/* Submenú en colapsado */
.sidebar.collapsed .sidebar-submenu {
    padding-left: 0;
    border-left: none;
    margin-left: 0;
    background: rgba(255,255,255,0.1);
    border-radius: 6px;
    padding: 4px;
}

.sidebar.collapsed .sidebar-sublink {
    justify-content: center;
    padding: 8px;
}

.sidebar.collapsed .sidebar-sublink i {
    font-size: 14px;
}

/* Scrollbar */
nav.overflow-auto::-webkit-scrollbar {
    width: 4px;
}

nav.overflow-auto::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.3);
    border-radius: 2px;
}
</style>
