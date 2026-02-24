@extends('layouts.app')

@section('title', 'Panel de Recepcion')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Panel de Recepcion" description="Gestion de ordenes, clientes y entregas">
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-file-earmark-plus" value="0" title="Ordenes Abiertas" color="primary" />
        <x-sinden.stat-card icon="bi bi-clock-history" value="0" title="Entregas Pendientes Hoy" color="warning" />
        <x-sinden.stat-card icon="bi bi-exclamation-triangle" value="0" title="Entregas Vencidas" color="danger" />
        <x-sinden.stat-card icon="bi bi-people" value="0" title="Clientes Activos" color="info" />
    </div>

    {{-- Contenido --}}
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Bienvenido al Panel de Recepcion</h5>
                    <p class="text-muted">Desde aqui podras gestionar las ordenes de trabajo, clientes y entregas.</p>
                    <p class="text-muted mb-0">Los modulos estaran disponibles a medida que se implementen.</p>
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
@endsection
