<nav class="sidebar-nav">
    <div class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
    </div>

    {{-- MÓDULO 6: Catálogo Público de Proyectos --}}
    <div class="nav-item {{ request()->routeIs('catalog.*') ? 'active' : '' }}">
        <a href="{{ route('catalog.index') }}" class="nav-link">
            <i class="fas fa-store"></i>
            <span>Catálogo</span>
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

    <div class="nav-section">
        <span>Verificación</span>
    </div>
    <div class="nav-item {{ request()->routeIs('admin.kyc.*') ? 'active' : '' }}">
        <a href="{{ route('admin.kyc.index') }}" class="nav-link">
            <i class="fas fa-user-check"></i>
            <span>Revisar KYC</span>
            @php
                $pendingKycCount = \App\Models\User::where('kyc_status', 'en_revision')->count();
            @endphp
            @if($pendingKycCount > 0)
                <span class="badge badge-warning">{{ $pendingKycCount }}</span>
            @endif
        </a>
    </div>

    <div class="nav-section">
        <span>Finanzas</span>
    </div>
    <div class="nav-item {{ request()->routeIs('admin.dividends.*') ? 'active' : '' }}">
        <a href="{{ route('admin.dividends.index') }}" class="nav-link">
            <i class="fas fa-coins"></i>
            <span>Gestión Dividendos</span>
            @php
                $pendingDividends = \App\Models\Dividendo::where('estado', 'programado')
                    ->whereDate('fecha_programada', '<=', now())
                    ->count();
            @endphp
            @if($pendingDividends > 0)
                <span class="badge badge-warning">{{ $pendingDividends }}</span>
            @endif
        </a>
    </div>
    <div class="nav-item {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
        <a href="{{ route('admin.withdrawals.index') }}" class="nav-link">
            <i class="fas fa-money-bill-wave"></i>
            <span>Gestión Retiros</span>
            @php
                $pendingRetiros = \App\Models\Retiro::whereIn('estado', ['pendiente', 'aprobado'])->count();
            @endphp
            @if($pendingRetiros > 0)
                <span class="badge badge-warning">{{ $pendingRetiros }}</span>
            @endif
        </a>
    </div>
    @endrole

    {{-- MÓDULO 5: KYC para Inversionistas --}}
    @role('Inversionista')
    <div class="nav-item {{ request()->routeIs('inversionista.kyc.*') ? 'active' : '' }}">
        <a href="{{ route('inversionista.kyc.index') }}" class="nav-link">
            <i class="fas fa-id-card"></i>
            <span>Verificación KYC</span>
            @if(auth()->user()->kyc_status === 'rechazado')
                <span class="badge badge-danger">!</span>
            @elseif(auth()->user()->kyc_status === 'pendiente')
                <span class="badge badge-warning">!</span>
            @elseif(auth()->user()->kyc_status === 'aprobado')
                <i class="fas fa-check-circle text-success" style="font-size: 0.8em;"></i>
            @endif
        </a>
    </div>

    {{-- MÓDULO 7: Billetera (solo si KYC aprobado o en revisión) --}}
    @if(in_array(auth()->user()->kyc_status, ['en_revision', 'aprobado']))
    <div class="nav-item {{ request()->routeIs('inversionista.wallet.*') ? 'active' : '' }}">
        <a href="{{ route('inversionista.wallet.index') }}" class="nav-link">
            <i class="fas fa-wallet"></i>
            <span>Mi Billetera</span>
        </a>
    </div>

    {{-- MÓDULO 8: Inversiones --}}
    <div class="nav-item {{ request()->routeIs('inversionista.investments.*') ? 'active' : '' }}">
        <a href="{{ route('inversionista.investments.index') }}" class="nav-link">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Mis Inversiones</span>
        </a>
    </div>

    {{-- MÓDULO 9: Dividendos --}}
    <div class="nav-item {{ request()->routeIs('inversionista.dividends.*') ? 'active' : '' }}">
        <a href="{{ route('inversionista.dividends.index') }}" class="nav-link">
            <i class="fas fa-coins"></i>
            <span>Mis Dividendos</span>
        </a>
    </div>

    {{-- MÓDULO 11: Retiros --}}
    <div class="nav-item {{ request()->routeIs('inversionista.withdrawals.*') ? 'active' : '' }}">
        <a href="{{ route('inversionista.withdrawals.index') }}" class="nav-link">
            <i class="fas fa-money-bill-wave"></i>
            <span>Mis Retiros</span>
            @php
                $pendingWithdrawals = \App\Models\Retiro::where('usuario_id', auth()->id())
                    ->whereIn('estado', ['pendiente', 'en_revision', 'aprobado'])
                    ->count();
            @endphp
            @if($pendingWithdrawals > 0)
                <span class="badge badge-info">{{ $pendingWithdrawals }}</span>
            @endif
        </a>
    </div>
    @endif
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
