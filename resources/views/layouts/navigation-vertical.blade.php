<nav class="sidebar-nav">
    {{-- Dashboard --}}
    <div class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}" class="nav-link">
            <i class="bi bi-house-door"></i>
            <span>Inicio</span>
        </a>
    </div>

    {{-- Dashboard Encargado - Solo para rol Encargado --}}
    @role('Encargado')
    <div class="nav-item {{ request()->routeIs('encargado.dashboard') ? 'active' : '' }}">
        <a href="{{ route('encargado.dashboard') }}" class="nav-link">
            <i class="bi bi-speedometer2"></i>
            <span>Mi Panel</span>
        </a>
    </div>
    @endrole

    {{-- Portal del Trabajador - Solo para rol Trabajador --}}
    @role('Trabajador')
    <div class="nav-item {{ request()->routeIs('trabajador.dashboard*') ? 'active' : '' }}">
        <a href="{{ route('trabajador.dashboard') }}" class="nav-link">
            <i class="bi bi-person-badge"></i>
            <span>Mi Portal</span>
        </a>
    </div>
    @endrole

    {{-- Dashboard Admin - Solo para rol Administrador --}}
    @role('Administrador')
    <div class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="bi bi-graph-up"></i>
            <span>Dashboard Admin</span>
        </a>
    </div>
    @endrole

    @php
        $user = Auth::user();
        $isAdmin = $user->hasRole('Administrador');
        $canVerTrabajadores = $user->can('ver_trabajadores');
        $canVerCuadrillas = $user->can('ver_cuadrillas');
        $canVerClientes = $user->can('ver_clientes');
        $canVerObras = $user->can('ver_obras');
        $canVerFichajes = $user->can('ver_fichajes');
        $canVerPartesDiarios = $user->can('ver_partes');
        $canVerMaquinaria = $user->can('ver_maquinaria');
        $canVerVehiculos = $user->can('ver_vehiculos');
        $canVerSubcontratas = $user->can('ver_subcontratas');
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

    {{-- SECCIÓN ORGANIZACIÓN - Todos los usuarios con permiso --}}
    @can('ver_tableros')
        <div class="nav-section-title">Organizacion</div>

        <div class="nav-item {{ request()->routeIs('tableros.*') ? 'active' : '' }}">
            <a href="{{ route('tableros.index') }}" class="nav-link">
                <i class="bi bi-kanban"></i>
                <span>Tableros</span>
            </a>
        </div>
    @endcan

    {{-- SECCIÓN OPERACIONES - Solo si tiene permiso de obras, partes, maquinaria, vehiculos o subcontratas --}}
    @if($canVerObras || $canVerPartesDiarios || $canVerMaquinaria || $canVerVehiculos || $canVerSubcontratas)
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

        @can('ver_maquinaria')
        <div class="nav-item {{ request()->routeIs('maquinaria.*') ? 'active' : '' }}">
            <a href="{{ route('maquinaria.index') }}" class="nav-link">
                <i class="bi bi-tools"></i>
                <span>Maquinaria</span>
            </a>
        </div>
        @endcan

        @can('ver_vehiculos')
        <div class="nav-item {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}">
            <a href="{{ route('vehiculos.index') }}" class="nav-link">
                <i class="bi bi-truck"></i>
                <span>Vehiculos</span>
            </a>
        </div>
        @endcan

        @can('ver_subcontratas')
        <div class="nav-item {{ request()->routeIs('subcontratas.*') ? 'active' : '' }}">
            <a href="{{ route('subcontratas.index') }}" class="nav-link">
                <i class="bi bi-briefcase-fill"></i>
                <span>Subcontratas</span>
            </a>
        </div>
        @endcan
    @endif

    {{-- SECCIÓN RECURSOS HUMANOS - Solo si tiene algún permiso --}}
    @if($canVerTrabajadores || $canVerCuadrillas || $canVerFichajes)
        <div class="nav-section-title">Recursos Humanos</div>

        @can('ver_trabajadores')
        <div class="nav-item {{ request()->routeIs('trabajadores.*') && !request()->routeIs('trabajadores.bonos.*') ? 'active' : '' }}">
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

    {{-- SECCIÓN PREVENCIÓN - Admin, RRHH, Encargado, Contabilidad --}}
    @role('Administrador|RRHH|Encargado|Contabilidad')
        <div class="nav-section-title">Prevención</div>

        <div class="nav-item {{ request()->routeIs('epi-inventario.*') ? 'active' : '' }}">
            <a href="{{ route('epi-inventario.index') }}" class="nav-link">
                <i class="bi bi-shield-check"></i>
                <span>Inventario EPIs</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('epi-entregas.*') ? 'active' : '' }}">
            <a href="{{ route('epi-entregas.index') }}" class="nav-link">
                <i class="bi bi-arrow-left-right"></i>
                <span>Entregas EPIs</span>
            </a>
        </div>

        @role('Administrador|RRHH|Encargado')
        <div class="nav-item {{ request()->routeIs('epi-catalogo.*') ? 'active' : '' }}">
            <a href="{{ route('epi-catalogo.index') }}" class="nav-link">
                <i class="bi bi-list-check"></i>
                <span>Catálogo EPIs</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('formacion-tipos.*') ? 'active' : '' }}">
            <a href="{{ route('formacion-tipos.index') }}" class="nav-link">
                <i class="bi bi-mortarboard"></i>
                <span>Tipos Formación</span>
            </a>
        </div>
        @endrole
    @endrole

    {{-- SECCIÓN ALERTAS - Todos los usuarios autenticados --}}
    <div class="nav-section-title">Alertas</div>

    <div class="nav-item {{ request()->routeIs('alertas.*') && !request()->routeIs('alertas.configuracion.*') ? 'active' : '' }}">
        <a href="{{ route('alertas.index') }}" class="nav-link d-flex justify-content-between align-items-center">
            <span>
                <i class="bi bi-bell"></i>
                <span>Centro de Alertas</span>
            </span>
            @if(isset($alertasNoLeidas) && $alertasNoLeidas > 0)
                <span class="badge bg-danger rounded-pill">{{ $alertasNoLeidas > 99 ? '99+' : $alertasNoLeidas }}</span>
            @endif
        </a>
    </div>

    @role('Administrador|RRHH')
    <div class="nav-item {{ request()->routeIs('caducidades-generales.*') ? 'active' : '' }}">
        <a href="{{ route('caducidades-generales.index') }}" class="nav-link">
            <i class="bi bi-calendar-x"></i>
            <span>Caducidades Empresa</span>
        </a>
    </div>

    <div class="nav-item {{ request()->routeIs('alertas.configuracion.*') ? 'active' : '' }}">
        <a href="{{ route('alertas.configuracion.index') }}" class="nav-link">
            <i class="bi bi-sliders"></i>
            <span>Config. Alertas</span>
        </a>
    </div>

    <div class="nav-item {{ request()->routeIs('cumpleanos.configuracion.*') ? 'active' : '' }}">
        <a href="{{ route('cumpleanos.configuracion.index') }}" class="nav-link">
            <i class="bi bi-envelope-heart"></i>
            <span>Emails Cumpleaños</span>
        </a>
    </div>
    @endrole

    {{-- SECCIÓN CONTABILIDAD - Solo Administrador o Contabilidad --}}
    @role('Administrador|Contabilidad')
        <div class="nav-section-title">Contabilidad</div>

        <div class="nav-item {{ request()->routeIs('ingresos.*') ? 'active' : '' }}">
            <a href="{{ route('ingresos.index') }}" class="nav-link">
                <i class="bi bi-arrow-down-circle"></i>
                <span>Ingresos</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('gastos.*') ? 'active' : '' }}">
            <a href="{{ route('gastos.index') }}" class="nav-link">
                <i class="bi bi-arrow-up-circle"></i>
                <span>Gastos</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('contratos.*') ? 'active' : '' }}">
            <a href="{{ route('contratos.index') }}" class="nav-link">
                <i class="bi bi-file-earmark-text"></i>
                <span>Contratos</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('facturas.*') ? 'active' : '' }}">
            <a href="{{ route('facturas.index') }}" class="nav-link">
                <i class="bi bi-receipt"></i>
                <span>Facturas</span>
            </a>
        </div>

        @role('Administrador')
        <div class="nav-item {{ request()->routeIs('gasto-categorias.*') ? 'active' : '' }}">
            <a href="{{ route('gasto-categorias.index') }}" class="nav-link">
                <i class="bi bi-tags"></i>
                <span>Categorías Gastos</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('contrato-tipos.*') ? 'active' : '' }}">
            <a href="{{ route('contrato-tipos.index') }}" class="nav-link">
                <i class="bi bi-collection"></i>
                <span>Tipos Contrato</span>
            </a>
        </div>
        @endrole
    @endrole

    {{-- GASTOS - Encargado --}}
    @role('Encargado')
        <div class="nav-section-title">Finanzas</div>

        <div class="nav-item {{ request()->routeIs('gastos.*') ? 'active' : '' }}">
            <a href="{{ route('gastos.index') }}" class="nav-link">
                <i class="bi bi-arrow-up-circle"></i>
                <span>Gastos</span>
            </a>
        </div>
    @endrole

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

        <div class="nav-item {{ request()->routeIs('documentos-empresa.*') ? 'active' : '' }}">
            <a href="{{ route('documentos-empresa.index') }}" class="nav-link">
                <i class="bi bi-folder2"></i>
                <span>Docs. Empresa</span>
            </a>
        </div>

        @can('ver_auditoria')
        <div class="nav-item {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
            <a href="{{ route('auditoria.index') }}" class="nav-link">
                <i class="bi bi-journal-text"></i>
                <span>Auditoría</span>
            </a>
        </div>
        @endcan
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
