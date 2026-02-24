<nav class="sidebar-nav">
    {{-- Dashboard (todos) --}}
    <div class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('recepcion.panel') || request()->routeIs('operario.panel') || request()->routeIs('contabilidad.panel') || request()->routeIs('admin.configuracion') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}" class="nav-link">
            <i class="bi bi-house-door"></i>
            <span>Inicio</span>
        </a>
    </div>

    @hasanyrole('Administrador|Recepcion')
        {{-- SECCION ORDENES --}}
        <div class="nav-section-title">Ordenes</div>

        <div class="nav-item {{ request()->routeIs('recepcion.ordenes.crear') ? 'active' : '' }}">
            <a href="{{ route('recepcion.ordenes.crear') }}" class="nav-link">
                <i class="bi bi-file-earmark-plus"></i>
                <span>Crear Orden</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('recepcion.ordenes.index') || request()->routeIs('recepcion.ordenes.show') || request()->routeIs('recepcion.ordenes.edit') ? 'active' : '' }}">
            <a href="{{ route('recepcion.ordenes.index') }}" class="nav-link">
                <i class="bi bi-search"></i>
                <span>Buscar Ordenes</span>
            </a>
        </div>

        <div class="nav-item disabled-nav">
            <a href="#" class="nav-link text-muted" title="Disponible en Fase 8">
                <i class="bi bi-box-seam"></i>
                <span>Entregas Pendientes</span>
            </a>
        </div>

        {{-- SECCION CATALOGOS --}}
        <div class="nav-section-title">Catalogos</div>

        <div class="nav-item {{ request()->routeIs('recepcion.clientes.*') ? 'active' : '' }}">
            <a href="{{ route('recepcion.clientes.index') }}" class="nav-link">
                <i class="bi bi-person-lines-fill"></i>
                <span>Clientes</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('recepcion.items.*') ? 'active' : '' }}">
            <a href="{{ route('recepcion.items.index') }}" class="nav-link">
                <i class="bi bi-tags"></i>
                <span>Items</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('recepcion.bosquejos-matriz.*') ? 'active' : '' }}">
            <a href="{{ route('recepcion.bosquejos-matriz.index') }}" class="nav-link">
                <i class="bi bi-image"></i>
                <span>Bosquejos Matriz</span>
            </a>
        </div>

        <div class="nav-item disabled-nav">
            <a href="#" class="nav-link text-muted" title="Disponible en Fase 12">
                <i class="bi bi-calculator"></i>
                <span>Consulta Precios</span>
            </a>
        </div>
    @endhasanyrole

    @can('ver_bosquejos_matriz')
        @unless(auth()->user()->hasAnyRole(['Administrador', 'Recepcion']))
        <div class="nav-section-title">Catalogos</div>
        <div class="nav-item {{ request()->routeIs('recepcion.bosquejos-matriz.*') ? 'active' : '' }}">
            <a href="{{ route('recepcion.bosquejos-matriz.index') }}" class="nav-link">
                <i class="bi bi-image"></i>
                <span>Bosquejos Matriz</span>
            </a>
        </div>
        @endunless
    @endcan

    @role('Operario')
        {{-- SECCION MI TRABAJO --}}
        <div class="nav-section-title">Mi Trabajo</div>

        <div class="nav-item {{ request()->routeIs('operario.ordenes-asignadas') || request()->routeIs('operario.ordenes.*') ? 'active' : '' }}">
            <a href="{{ route('operario.ordenes-asignadas') }}" class="nav-link">
                <i class="bi bi-list-check"></i>
                <span>Ordenes Asignadas</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('operario.buscar*') ? 'active' : '' }}">
            <a href="{{ route('operario.buscar') }}" class="nav-link">
                <i class="bi bi-search"></i>
                <span>Buscar Orden</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('operario.complementar') ? 'active' : '' }}">
            <a href="{{ route('operario.complementar') }}" class="nav-link">
                <i class="bi bi-plus-circle"></i>
                <span>Complementar Ordenes</span>
            </a>
        </div>
    @endrole

    @hasanyrole('Administrador|Contabilidad')
        {{-- SECCION FINANZAS --}}
        <div class="nav-section-title">Finanzas</div>

        <div class="nav-item disabled-nav">
            <a href="#" class="nav-link text-muted" title="Disponible en Fase 9">
                <i class="bi bi-cash-coin"></i>
                <span>Ordenes Pendientes</span>
            </a>
        </div>

        @role('Contabilidad')
        <div class="nav-item {{ request()->routeIs('contabilidad.items.*') ? 'active' : '' }}">
            <a href="{{ route('contabilidad.items.index') }}" class="nav-link">
                <i class="bi bi-tags"></i>
                <span>Items</span>
            </a>
        </div>
        @endrole
    @endhasanyrole

    @role('Administrador')
        {{-- SECCION ADMIN --}}
        <div class="nav-section-title">Administracion</div>

        <div class="nav-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
            <a href="{{ route('admin.usuarios.index') }}" class="nav-link">
                <i class="bi bi-people"></i>
                <span>Usuarios</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('admin.configuracion') ? 'active' : '' }}">
            <a href="{{ route('admin.configuracion') }}" class="nav-link">
                <i class="bi bi-gear"></i>
                <span>Configuracion</span>
            </a>
        </div>

        <div class="nav-item disabled-nav">
            <a href="#" class="nav-link text-muted" title="Disponible en Fase 12">
                <i class="bi bi-table"></i>
                <span>Tabla de Precios</span>
            </a>
        </div>
    @endrole

    {{-- SECCION SISTEMA (todos) --}}
    <div class="nav-section-title">Sistema</div>

    <div class="nav-item disabled-nav">
        <a href="#" class="nav-link text-muted" title="Disponible en Fase 16">
            <i class="bi bi-clock-history"></i>
            <span>Mis Actividades</span>
        </a>
    </div>

    @hasanyrole('Administrador|Recepcion')
    <div class="nav-item disabled-nav">
        <a href="#" class="nav-link text-muted" title="Disponible en Fase 16">
            <i class="bi bi-activity"></i>
            <span>Actividades Globales</span>
        </a>
    </div>
    @endhasanyrole

    {{-- SECCION CUENTA --}}
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
            <span>Cerrar Sesion</span>
        </button>
    </form>
</div>
