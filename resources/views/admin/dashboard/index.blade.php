@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body py-4">
                    <h2 class="mb-1">Panel de Administración</h2>
                    <p class="mb-0 opacity-75">Bienvenido, {{ auth()->user()->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-people me-2"></i>Gestión de Usuarios</h5>
                    <p class="text-muted mb-3">Administra usuarios del sistema</p>
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-primary">
                        Ver Usuarios
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-gear me-2"></i>Configuración</h5>
                    <p class="text-muted mb-3">Ajustes del sistema</p>
                    <p class="text-muted small">Próximamente...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, var(--manzer-primary) 0%, var(--manzer-secondary) 100%);
}
</style>
@endpush
@endsection
