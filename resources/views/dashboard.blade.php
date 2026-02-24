@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body py-4">
                    <h2 class="mb-1">Bienvenido, {{ auth()->user()->name }}!</h2>
                    <p class="mb-0 opacity-75">Has iniciado sesion exitosamente</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Bienvenido al Sistema</h5>

                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="mb-3">Enlaces Rapidos</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="{{ route('profile.edit') }}" class="text-decoration-none">
                                <i class="bi bi-person-gear me-2"></i>Mi Perfil
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, var(--sinden-primary) 0%, var(--sinden-secondary) 100%);
}
</style>
@endpush
@endsection
