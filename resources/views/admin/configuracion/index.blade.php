@extends('layouts.app')

@section('title', 'Configuracion del Sistema')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Configuracion del Sistema" description="Parametros generales, empresa y timeouts">
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-people" value="{{ \App\Models\User::count() }}" title="Usuarios" color="primary" />
        <x-sinden.stat-card icon="bi bi-shield-check" value="4" title="Roles" color="info" />
        <x-sinden.stat-card icon="bi bi-key" value="29" title="Permisos" color="warning" />
        <x-sinden.stat-card icon="bi bi-gear" value="{{ \App\Models\ConfiguracionSistema::count() }}" title="Configuraciones" color="success" />
    </div>

    {{-- Contenido --}}
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Panel de Administracion</h5>
                    <p class="text-muted">Desde aqui podras configurar los parametros del sistema, gestionar usuarios y la tabla de precios.</p>
                    <p class="text-muted mb-0">El formulario completo de configuracion estara disponible en la Fase 13.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="mb-3">Enlaces Rapidos</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="{{ route('admin.usuarios.index') }}" class="text-decoration-none">
                                <i class="bi bi-people me-2"></i>Gestion de Usuarios
                            </a>
                        </li>
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
@endsection
