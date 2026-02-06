<div class="d-flex flex-column h-100">
    {{-- Logo --}}
    <div class="d-flex justify-content-center align-items-center py-3 border-bottom" style="border-color: var(--miracle-lilac) !important; background-color: white;">
        <a href="/" class="text-decoration-none">
            <img style="width: 80%; margin-left: 5%;" src="{{ asset('images/logo.png') }}" class="logo-full" width="100" alt="Miracle Beauty Experts">
            <img src="{{ asset('images/ico.png') }}" class="logo-icon d-none" width="40" alt="Miracle">
        </a>
    </div>

    {{-- Navegación con scroll vertical --}}
    <nav class="nav flex-column flex-nowrap px-2 py-3 flex-grow-1" style="min-height: 0; overflow-y: auto; overflow-x: hidden;">
        <a href="/dashboard"
           class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('dashboard') ? 'active' : 'text-dark' }}">
            <i class="bi bi-house"></i>
            <span>Inicio</span>
        </a>

        {{-- Servicio Técnico (para admin y técnico) - OCULTO TEMPORALMENTE
        @if(auth()->user()->hasRole(['admin', 'tecnico']))
            <div class="nav-item mb-2">
                <a href="#" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('st.*') ? 'active' : 'text-dark' }}"
                   data-bs-toggle="collapse" data-bs-target="#submenuServicioTecnico"
                   aria-expanded="{{ request()->routeIs('st.*') ? 'true' : 'false' }}">
                    <i class="bi bi-tools"></i>
                    <span>Servicio</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse {{ request()->routeIs('st.*') ? 'show' : '' }}" id="submenuServicioTecnico">
                    <div class="nav flex-column ms-3">
                        <a href="{{ route('st.dashboard') }}"
                           class="nav-link py-2 d-flex align-items-center gap-2 {{ request()->routeIs('st.dashboard') ? 'active' : 'text-dark' }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('st.ordenes.index') }}"
                           class="nav-link py-2 d-flex align-items-center gap-2 {{ request()->routeIs('st.ordenes.*') ? 'active' : 'text-dark' }}">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Órdenes</span>
                        </a>
                        <a href="{{ route('st.tecnicos.index') }}"
                           class="nav-link py-2 d-flex align-items-center gap-2 {{ request()->routeIs('st.tecnicos.*') ? 'active' : 'text-dark' }}">
                            <i class="bi bi-person-gear"></i>
                            <span>Técnicos</span>
                        </a>
                        <a href="{{ route('st.clientes.index') }}"
                           class="nav-link py-2 d-flex align-items-center gap-2 {{ request()->routeIs('st.clientes.*') ? 'active' : 'text-dark' }}">
                            <i class="bi bi-people"></i>
                            <span>Clientes</span>
                        </a>
                        <a href="{{ route('st.equipos.index') }}"
                           class="nav-link py-2 d-flex align-items-center gap-2 {{ request()->routeIs('st.equipos.*') ? 'active' : 'text-dark' }}">
                            <i class="bi bi-camera-video"></i>
                            <span>Equipos</span>
                        </a>
                        <a href="{{ route('st.repuestos.index') }}"
                           class="nav-link py-2 d-flex align-items-center gap-2 {{ request()->routeIs('st.repuestos.*') ? 'active' : 'text-dark' }}">
                            <i class="bi bi-box-seam"></i>
                            <span>Repuestos</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif
        --}}

        @if (auth()->user()->getRoleNames()->first() == 'admin')
            <a href="{{ route('dashboard.metricas') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('dashboard.metricas') ? 'active' : 'text-dark' }}">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Métricas</span>
            </a>
        @endif

        {{-- Usuarios (para admin e inventarios) --}}
        @if(auth()->user()->hasRole(['admin', 'inventarios']))
            <a href="/usuarios"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('usuarios*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-people"></i>
                <span>Usuarios</span>
            </a>
        @endif

        {{-- Cotizaciones (para vendedor, admin y facturación) --}}
        @if(auth()->user()->hasRole(['vendedor', 'admin', 'facturacion']))
            <a href="{{ route('solicitudes') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('solicitudes*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-clipboard-data"></i>
                <span>Cotizaciones</span>
            </a>
        @endif

        {{-- Clientes (para vendedor, admin, facturación e inventarios) --}}
        @if(auth()->user()->hasRole(['vendedor', 'admin', 'facturacion', 'inventarios']))
            <a href="/clientes"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('clientes*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-person-badge"></i>
                <span>Clientes</span>
            </a>
        @endif

        {{-- Catálogo (para vendedor y admin) --}}
        @if(auth()->user()->hasRole(['vendedor', 'admin']))
            <a href="{{ route('catalogo') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('catalogo*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-cart"></i>
                <span>Catálogo</span>
            </a>
            <a href="{{ route('enlaces') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('enlaces*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-link-45deg"></i>
                <span>Links</span>
            </a>
        @endif

        @if (auth()->user()->getRoleNames()->first() == 'admin')
            <a href="/listas-precios"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('listas-precios*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-currency-dollar"></i>
                <span>Listas de Precios</span>
            </a>
        @endif

        {{-- Sección Inventario (para admin e inventarios) --}}
        @if(auth()->user()->hasRole(['admin', 'inventarios']))
            <div class="border-top my-2" style="border-color: var(--miracle-lilac) !important;"></div>
            <p class="nav-link mb-1 text-muted small fw-semibold text-uppercase">
                <i class="bi bi-boxes me-1"></i>
                <span>Inventario</span>
            </p>
            <a href="/categorias"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('categorias*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-tags"></i>
                <span>Categorías</span>
            </a>
            <a href="/productos"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('productos*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-basket3"></i>
                <span>Productos</span>
            </a>
            <a href="{{ route('stock.index') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('stock.index*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-box-seam"></i>
                <span>Gestión de Stock</span>
            </a>
            <a href="{{ route('traslados') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('traslados*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-arrow-left-right"></i>
                <span>Traslados</span>
            </a>
            <a href="/ubicaciones"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('ubicaciones*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-geo-alt"></i>
                <span>Ubicaciones</span>
            </a>
            <a href="{{ route('novedades-stock') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('novedades-stock*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-exclamation-triangle"></i>
                <span>Novedades</span>
            </a>
            <a href="{{ route('productos.importacion.historial') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('productos/historial-importaciones*') || request()->is('productos/importacion*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-upload"></i>
                <span>Importar Productos</span>
            </a>
        @endif

        {{-- Punto de Venta (para admin, inventarios, punto_venta) - OCULTO TEMPORALMENTE
        @if(auth()->user()->hasRole(['admin', 'inventarios', 'punto_venta']))
            <div class="border-top my-2" style="border-color: var(--miracle-lilac) !important;"></div>
            <p class="nav-link mb-1 text-muted small fw-semibold text-uppercase">
                <i class="bi bi-shop me-1"></i>
                <span>Punto de Venta</span>
            </p>
            <a href="{{ route('punto-venta.dashboard') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('punto-venta.dashboard') ? 'active' : 'text-dark' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard PdV</span>
            </a>
            <a href="{{ route('punto-venta.nueva-venta') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('punto-venta.nueva-venta') ? 'active' : 'text-dark' }}">
                <i class="bi bi-cart-plus"></i>
                <span>Nueva Venta</span>
            </a>
            <a href="{{ route('punto-venta.index') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('punto-venta.index') ? 'active' : 'text-dark' }}">
                <i class="bi bi-list-ul"></i>
                <span>Historial Ventas</span>
            </a>
            <a href="{{ route('punto-venta.reporte') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('punto-venta.reporte') ? 'active' : 'text-dark' }}">
                <i class="bi bi-bar-chart"></i>
                <span>Reportes PdV</span>
            </a>
        @endif
        --}}

        {{-- Portal Cliente (solo para rol cliente) --}}
        @if(auth()->user()->hasRole('cliente'))
            <div class="border-top my-2" style="border-color: var(--miracle-lilac) !important;"></div>
            <p class="nav-link mb-1 text-muted small fw-semibold text-uppercase">
                <i class="bi bi-person-circle me-1"></i>
                <span>Mi Portal</span>
            </p>
            <a href="{{ route('portal.dashboard') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('portal.dashboard') ? 'active' : 'text-dark' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('portal.historial') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('portal.historial') ? 'active' : 'text-dark' }}">
                <i class="bi bi-clock-history"></i>
                <span>Mis Pedidos</span>
            </a>
        @endif
    </nav>

    {{-- Botón Salir --}}
    <div class="mt-auto p-3 border-top" style="border-color: var(--miracle-lilac) !important;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-logout" title="Cerrar sesión">
                <i class="bi bi-box-arrow-right"></i>
                <span class="logout-label">Salir</span>
            </button>
        </form>
    </div>
</div>
