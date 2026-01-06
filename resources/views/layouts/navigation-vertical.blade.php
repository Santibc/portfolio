<div class="d-flex flex-column h-100">
    {{-- Logo --}}
    <div class="sidebar-header">
        <a href="{{ url('/') }}" class="sidebar-logo">
            <img src="{{ asset('images/logo.png') }}" class="sidebar-logo-img" alt="Logo">
            <img src="{{ asset('images/ico.png') }}" class="sidebar-logo-icon" alt="Logo">
        </a>
    </div>

    {{-- Navegación --}}
    <nav class="sidebar-nav">
        {{-- Inicio --}}
        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->is('inicio') ? 'active' : '' }}"
           title="Inicio">
            <i class="bi bi-house-door"></i>
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

                <div class="sidebar-divider"></div>
                <div class="sidebar-section-title">Ventas</div>

                <a href="{{ route('compras') }}"
                   class="sidebar-link {{ request()->routeIs('compras*') ? 'active' : '' }}"
                   title="Pedidos">
                    <i class="bi bi-cart-check"></i>
                    <span>Pedidos</span>
                </a>

                <a href="{{ route('gestion-clientes.index') }}"
                   class="sidebar-link {{ request()->routeIs('gestion-clientes.*') ? 'active' : '' }}"
                   title="Clientes">
                    <i class="bi bi-people"></i>
                    <span>Clientes</span>
                </a>

                <div class="sidebar-divider"></div>
                <div class="sidebar-section-title">Catálogo</div>

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

                <a href="{{ route('stock.index') }}"
                   class="sidebar-link {{ request()->routeIs('stock.*') ? 'active' : '' }}"
                   title="Inventario">
                    <i class="bi bi-archive"></i>
                    <span>Inventario</span>
                </a>

                <a href="{{ route('flores.index') }}"
                   class="sidebar-link {{ request()->routeIs('flores.*') ? 'active' : '' }}"
                   title="Flores">
                    <i class="bi bi-flower1"></i>
                    <span>Flores</span>
                </a>

                <a href="{{ route('adicionales.index') }}"
                   class="sidebar-link {{ request()->routeIs('adicionales.*') ? 'active' : '' }}"
                   title="Adicionales">
                    <i class="bi bi-gift"></i>
                    <span>Adicionales</span>
                </a>

                <a href="{{ route('descuentos.index') }}"
                   class="sidebar-link {{ request()->routeIs('descuentos*') ? 'active' : '' }}"
                   title="Descuentos">
                    <i class="bi bi-tag"></i>
                    <span>Descuentos</span>
                </a>

                <div class="sidebar-divider"></div>
                <div class="sidebar-section-title">Operaciones</div>

                {{-- Logística con submenú --}}
                <div class="sidebar-submenu-container">
                    <a href="#logisticaMenu"
                       class="sidebar-link {{ request()->routeIs('logistica.*') || request()->routeIs('repartidores.*') ? 'active' : '' }}"
                       data-bs-toggle="collapse"
                       aria-expanded="{{ request()->routeIs('logistica.*') || request()->routeIs('repartidores.*') ? 'true' : 'false' }}"
                       title="Logística">
                        <i class="bi bi-truck"></i>
                        <span>Logística</span>
                        <i class="bi bi-chevron-down sidebar-chevron"></i>
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

                <div class="sidebar-divider"></div>
                <div class="sidebar-section-title">Tienda</div>

                <a href="{{ route('carrusel.index') }}"
                   class="sidebar-link {{ request()->routeIs('carrusel.*') ? 'active' : '' }}"
                   title="Carrusel">
                    <i class="bi bi-images"></i>
                    <span>Carrusel</span>
                </a>

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
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-100 text-start border-0 bg-transparent" title="Salir">
                <i class="bi bi-box-arrow-right"></i>
                <span>Salir</span>
            </button>
        </form>
    </div>
</div>
