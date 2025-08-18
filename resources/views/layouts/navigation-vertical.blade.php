<div class="d-flex flex-column h-100">
    {{-- Logo --}}
    <div class="d-flex justify-content-center align-items-center py-3 border-bottom">
        <a href="/" class="text-decoration-none">
            <img style="width: 80%; margin-left: 5%;" src="{{ asset('images/logo.png') }}" class="logo-full" width="100" alt="Logo">
            <img src="{{ asset('images/logo.png') }}" class="logo-icon d-none" width="40" alt="Logo Icon">
        </a>
    </div>

    {{-- Navegación --}}
    <nav class="nav flex-column px-2 py-3">
        <a href="/dashboard"
           class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('dashboard') ? 'active' : 'text-dark' }}">
            <i class="bi bi-house"></i>
            <span>Inicio</span>
        </a>

        @if (auth()->user()->getRoleNames()->first() == 'admin')
            <a href="/usuarios"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('usuarios*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-people"></i>
                <span>Usuarios</span>
            </a>
{{--             <a href="/clientes"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('clientes*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-person-badge"></i>
                <span>Clientes</span>
            </a>
            <a href="/categorias"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('categorias*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-tags"></i>
                <span>Categorías</span>
            </a>
            <a href="/productos"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('productos*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-basket3"></i>
                <span>Productos</span>
            </a> --}}
        @endif

        @if(auth()->user()->empresa)
            <a href="/productos"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('productos*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-basket3"></i>
                <span>Productos</span>
            </a>
            <a href="/clientes"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('clientes*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-person-badge"></i>
                <span>Clientes</span>
            </a>
            <a href="/categorias"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('categorias*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-tags"></i>
                <span>Categorías</span>
            </a>            
            {{--             <x-nav-link :href="route('compras')" :active="request()->routeIs('compras*')">
                <i class="bi bi-cart3"></i> {{ __('Ventas') }}
            </x-nav-link> --}}
            @endif
            <a href="/empresa"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('empresa*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-tags"></i>
                <span>Mi Empresa</span>
            </a>       
        @if(auth()->user()->empresa && auth()->user()->empresa->activo)
            <a href="/empresa/preview" target="_blank"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('empresa.preview') ? 'active' : 'text-dark' }}">
                <i class="bi bi-tags"></i>
                <span>Ver Mi Tienda</span>
            </a>    
        @endif









        {{-- Catálogo (para vendedor y admin) --}}
        @if(auth()->user()->hasRole(['vendedor', 'admin']))
            <a href="{{ route('catalogo') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('catalogo*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-cart"></i>
                <span>Catálogo</span>
            </a>
            <a href="{{ route('solicitudes') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('solicitudes*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-clipboard-data"></i>
                <span>Solicitudes</span>
            </a>
            <a href="{{ route('enlaces') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('enlaces*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-link-45deg"></i>
                <span>Links</span>
            </a>
            <a href="{{ route('stock.index') }}"
               class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->routeIs('stock.index*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-box-seam"></i>
                <span>Gestión de Stock</span>
            </a>
        @endif
    </nav>

    {{-- Botón Salir --}}
    <div class="mt-auto p-3 border-top">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-start gap-2">
                <i class="fas fa-sign-out-alt"></i>
                <span class="logout-label">Salir</span>
            </button>
        </form>
    </div>
</div>
