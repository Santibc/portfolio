<nav class="sidebar-nav">
    <div class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
    </div>

    {{-- MÓDULO 3: Gestión de Proyectos --}}
    @role('Agricultor')
    <div class="nav-item {{ request()->routeIs('farmer.projects.*') ? 'active' : '' }}">
        <a href="{{ route('farmer.projects.index') }}" class="nav-link">
            <i class="fas fa-seedling"></i>
            <span>Mis Proyectos</span>
        </a>
    </div>
    @endrole

    @role('Administrador')
    <div class="nav-section">
        <span>Proyectos</span>
    </div>
    <div class="nav-item {{ request()->routeIs('admin.projects.registration.*') ? 'active' : '' }}">
        <a href="{{ route('admin.projects.registration.index') }}" class="nav-link">
            <i class="fas fa-plus-circle"></i>
            <span>Registrar Proyecto</span>
        </a>
    </div>
    <div class="nav-item {{ request()->routeIs('admin.projects.review.*') ? 'active' : '' }}">
        <a href="{{ route('admin.projects.review.index') }}" class="nav-link">
            <i class="fas fa-clipboard-check"></i>
            <span>Aprobar Proyectos</span>
        </a>
    </div>
    @endrole

    {{-- TODO: Descomentar cuando se implementen los módulos --}}
    {{--
    <div class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-chart-pie"></i>
            <span>Mi Portafolio</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-search"></i>
            <span>Explorar Proyectos</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-wallet"></i>
            <span>Billetera</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-history"></i>
            <span>Historial</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-chart-line"></i>
            <span>Análisis</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-cog"></i>
            <span>Configuración</span>
        </a>
    </div>
    --}}
</nav>

<div class="sidebar-footer">
    <form method="POST" action="{{ route('logout') }}" id="logout-form-sidebar">
        @csrf
        <button type="button" class="logout-btn" onclick="Swal.fire({
            title: '¿Cerrar sesión?',
            text: '¿Estás seguro que deseas salir?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.closest('form').submit();
            }
        });">
            <i class="fas fa-sign-out-alt"></i>
            <span>Cerrar Sesión</span>
        </button>
    </form>
</div>
