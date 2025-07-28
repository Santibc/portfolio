<div class="d-flex flex-column h-100">
    {{-- Logo --}}
    <div class="d-flex justify-content-center align-items-center py-3 border-bottom">
        <a href="/" class="text-decoration-none">
            <img style="width: 80%;margin-left: 5%;" src="{{ asset('images/logo.png') }}" class="logo-full" width="100" alt="Logo">
            <img src="{{ asset('images/logo.png') }}" class="logo-icon d-none" width="40" alt="Logo Icon">
        </a>
    </div>

    {{-- Navegación --}}
    <nav class="nav flex-column px-2 py-3">
<a href="/dashboard"
   class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('dashboard') ? 'active' : 'text-dark' }}">
    <i class="bi bi-speedometer2"></i>
    <span>Inicio</span>
</a>
@if (auth()->user()->getRoleNames()->first() == 'admin') 
<a href="/usuarios"
   class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('usuarios*') ? 'active' : 'text-dark' }}">
    <i class="bi bi-person-lines-fill"></i>
    <span>Usuarios</span>
</a>
<a href="/clientes"
   class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('clientes*') ? 'active' : 'text-dark' }}">
    <i class="bi bi-person-lines-fill"></i>
    <span>Clientes</span>
</a>
@endif
{{-- @if (auth()->user()->getRoleNames()->first() == 'admin') 
<a href="/clientes"
   class="nav-link mb-2 d-flex align-items-center gap-2 {{ request()->is('clientes*') ? 'active' : 'text-dark' }}">
    <i class="bi bi-person-lines-fill"></i>
    <span>clientes</span>
</a>
@endif --}}



        {{-- Agrega más enlaces aquí si lo deseas --}}
    </nav>

    {{-- Botón Salir --}}
    <div class="mt-auto p-3 border-top">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-start gap-2">
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span class="logout-label">Salir</span>
            </button>
        </form>
    </div>
</div>