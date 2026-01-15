<nav class="sidebar-nav">
    {{-- Dashboard --}}
    <div class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}" class="nav-link">
            <i class="bi bi-house-door"></i>
            <span>Inicio</span>
        </a>
    </div>

    @php
        $user = Auth::user();
        $isAdmin = $user->hasRole('Administrador');
        $canVerTrabajadores = $user->can('ver_trabajadores');
        $canVerCuadrillas = $user->can('ver_cuadrillas');
        $canVerClientes = $user->can('ver_clientes');
        $canVerObras = $user->can('ver_obras');
        $canVerFichajes = $user->can('ver_fichajes');
        $canVerPartesDiarios = $user->can('ver_partes');
    @endphp

    {{-- SECCIÓN COMERCIAL - Solo si tiene permiso de clientes --}}
    @if($canVerClientes)
        <div class="nav-section-title">Comercial</div>

        @can('ver_clientes')
        <div class="nav-item {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
            <a href="{{ route('clientes.index') }}" class="nav-link">
                <i class="bi bi-building"></i>
                <span>Clientes</span>
            </a>
        </div>
        @endcan
    @endif

    {{-- SECCIÓN OPERACIONES - Solo si tiene permiso de obras o partes --}}
    @if($canVerObras || $canVerPartesDiarios)
        <div class="nav-section-title">Operaciones</div>

        @can('ver_obras')
        <div class="nav-item {{ request()->routeIs('obras.*') ? 'active' : '' }}">
            <a href="{{ route('obras.index') }}" class="nav-link">
                <i class="bi bi-geo-alt-fill"></i>
                <span>Obras</span>
            </a>
        </div>
        @endcan

        @can('ver_partes')
        <div class="nav-item {{ request()->routeIs('partes-diarios.*') ? 'active' : '' }}">
            <a href="{{ route('partes-diarios.index') }}" class="nav-link">
                <i class="bi bi-file-earmark-text"></i>
                <span>Partes Diarios</span>
            </a>
        </div>
        @endcan
    @endif

    {{-- SECCIÓN RECURSOS HUMANOS - Solo si tiene algún permiso --}}
    @if($canVerTrabajadores || $canVerCuadrillas || $canVerFichajes)
        <div class="nav-section-title">Recursos Humanos</div>

        @can('ver_trabajadores')
        <div class="nav-item {{ request()->routeIs('trabajadores.*') ? 'active' : '' }}">
            <a href="{{ route('trabajadores.index') }}" class="nav-link">
                <i class="bi bi-people-fill"></i>
                <span>Trabajadores</span>
            </a>
        </div>
        @endcan

        @can('ver_cuadrillas')
        <div class="nav-item {{ request()->routeIs('cuadrillas.*') ? 'active' : '' }}">
            <a href="{{ route('cuadrillas.index') }}" class="nav-link">
                <i class="bi bi-diagram-3"></i>
                <span>Cuadrillas</span>
            </a>
        </div>
        @endcan

        @can('ver_fichajes')
        <div class="nav-item {{ request()->routeIs('fichajes.*') ? 'active' : '' }}">
            <a href="{{ route('fichajes.index') }}" class="nav-link">
                <i class="bi bi-clock-history"></i>
                <span>Fichajes</span>
            </a>
        </div>
        @endcan

        @role('Administrador|Contabilidad')
        <div class="nav-item {{ request()->routeIs('trabajadores.bonos.*') ? 'active' : '' }}">
            <a href="{{ route('trabajadores.bonos.index') }}" class="nav-link">
                <i class="bi bi-gift"></i>
                <span>Bonos y Primas</span>
            </a>
        </div>
        @endrole
    @endif

    @if($isAdmin)
        {{-- SECCIÓN ADMIN --}}
        <div class="nav-section-title">Administración</div>

        {{-- Temporalmente oculto hasta crear la vista
        <div class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <i class="bi bi-speedometer2"></i>
                <span>Panel Admin</span>
            </a>
        </div>
        --}}

        <div class="nav-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
            <a href="{{ route('admin.usuarios.index') }}" class="nav-link">
                <i class="bi bi-people"></i>
                <span>Usuarios</span>
            </a>
        </div>
    @endif

    {{-- SECCIÓN CUENTA --}}
    <div class="nav-section-title">Cuenta</div>

    <div class="nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
        <a href="{{ route('profile.edit') }}" class="nav-link">
            <i class="bi bi-person-gear"></i>
            <span>Mi Perfil</span>
        </a>
    </div>
</nav>

<div class="sidebar-footer">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            <i class="bi bi-box-arrow-left"></i>
            <span>Cerrar Sesión</span>
        </button>
    </form>
</div>
