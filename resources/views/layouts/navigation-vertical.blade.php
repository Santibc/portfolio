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
    @endphp

    @if($isAdmin)
        {{-- SECCIÓN ADMIN --}}
        <div class="nav-section-title">Administración</div>

        <div class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard Admin</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
            <a href="{{ route('admin.usuarios.index') }}" class="nav-link">
                <i class="bi bi-people"></i>
                <span>Usuarios</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}">
            <a href="{{ route('admin.categorias.index') }}" class="nav-link">
                <i class="bi bi-folder"></i>
                <span>Categorías</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('admin.cursos.*') ? 'active' : '' }}">
            <a href="{{ route('admin.cursos.index') }}" class="nav-link">
                <i class="bi bi-collection-play"></i>
                <span>Cursos</span>
            </a>
        </div>

        <div class="nav-item {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">
            <a href="{{ route('admin.reportes.index') }}" class="nav-link">
                <i class="bi bi-graph-up"></i>
                <span>Reportes</span>
            </a>
        </div>
    @endif

    {{-- SECCIÓN ESTUDIANTE (visible para todos) --}}
    <div class="nav-section-title">Aprendizaje</div>

    <div class="nav-item {{ request()->routeIs('categorias.*') && !request()->routeIs('admin.*') ? 'active' : '' }}">
        <a href="{{ route('categorias.index') }}" class="nav-link">
            <i class="bi bi-grid"></i>
            <span>Categorías</span>
        </a>
    </div>

    <div class="nav-item {{ request()->routeIs('cursos.*') && !request()->routeIs('admin.*') ? 'active' : '' }}">
        <a href="{{ route('cursos.index') }}" class="nav-link">
            <i class="bi bi-play-circle"></i>
            <span>Mis Cursos</span>
        </a>
    </div>

    <div class="nav-item {{ request()->routeIs('progreso.*') ? 'active' : '' }}">
        <a href="{{ route('progreso.index') }}" class="nav-link">
            <i class="bi bi-bar-chart-line"></i>
            <span>Mi Progreso</span>
        </a>
    </div>

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