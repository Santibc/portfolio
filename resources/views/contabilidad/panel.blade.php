@extends('layouts.app')

@section('title', 'Panel de Contabilidad')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Panel de Contabilidad" description="Gestion de pagos, abonos y saldos pendientes">
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-cash-coin" value="0" title="Ordenes con Saldo" color="danger" />
        <x-sinden.stat-card icon="bi bi-hourglass-split" value="0" title="Abonos por Aprobar" color="warning" />
        <x-sinden.stat-card icon="bi bi-currency-dollar" value="$0" title="Total Pendiente" color="info" />
        <x-sinden.stat-card icon="bi bi-check2-all" value="$0" title="Recaudado Hoy" color="success" />
    </div>

    {{-- Contenido --}}
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Bienvenido al Panel de Contabilidad</h5>
                    <p class="text-muted">Desde aqui podras gestionar pagos, aprobar abonos y ver el estado financiero de las ordenes.</p>
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
