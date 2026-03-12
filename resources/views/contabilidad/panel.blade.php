@extends('layouts.app')

@section('title', 'Panel de Contabilidad')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Panel de Contabilidad" description="Resumen financiero y gestion de pagos">
    </x-sinden.page-header>

    {{-- Stat Cards clickeables --}}
    <div class="summary-cards">
        <a href="{{ route('contabilidad.ordenes-pendientes') }}" class="text-decoration-none">
            <x-sinden.stat-card icon="bi bi-receipt-cutoff" :value="$ordenesConSaldo" title="Ordenes con Saldo" color="danger" />
        </a>
        <a href="{{ route('contabilidad.pagos-pendientes') }}" class="text-decoration-none">
            <x-sinden.stat-card icon="bi bi-hourglass-split" :value="$abonosPorAprobar" title="Abonos por Aprobar" color="warning" />
        </a>
        <x-sinden.stat-card icon="bi bi-currency-dollar" :value="'$' . number_format($totalPendiente, 0, ',', '.')" title="Total Pendiente" color="info" />
        <x-sinden.stat-card icon="bi bi-cash-stack" :value="'$' . number_format($recaudadoHoy, 0, ',', '.')" title="Recaudado Hoy" color="success" />
    </div>

    {{-- Contenido principal --}}
    <div class="row mt-4">
        {{-- Ultimos pagos aprobados --}}
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bi bi-clock-history me-2 text-primary"></i>Ultimos Pagos Aprobados
                        </h6>
                        <span class="text-muted small">Reciente</span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    @if($ultimosPagos->count() > 0)
                        @foreach($ultimosPagos as $pago)
                            <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fw-bold text-success" style="font-size:1rem; min-width:100px">
                                        ${{ number_format($pago->monto, 0, ',', '.') }}
                                    </span>
                                    @php
                                        $metodoCfg = [
                                            'efectivo' => ['bg-success', 'bi-cash'],
                                            'nequi' => ['bg-purple', 'bi-phone'],
                                            'transferencia' => ['bg-info', 'bi-bank'],
                                            'tarjeta' => ['bg-warning', 'bi-credit-card'],
                                            'otro' => ['bg-secondary', 'bi-three-dots'],
                                        ];
                                        $mc = $metodoCfg[$pago->metodo_pago] ?? ['bg-secondary', 'bi-three-dots'];
                                    @endphp
                                    <span class="badge {{ $mc[0] }} bg-opacity-10 text-dark border small">
                                        <i class="bi {{ $mc[1] }} me-1"></i>{{ ucfirst($pago->metodo_pago) }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('contabilidad.ordenes.show', $pago->orden_id) }}" class="text-decoration-none fw-semibold small">
                                        {{ $pago->orden->numero_orden ?? '-' }}
                                    </a>
                                    <div class="text-muted small">{{ $pago->orden->cliente->nombre ?? '-' }}</div>
                                </div>
                                <div class="text-muted small text-end" style="min-width:60px">
                                    {{ $pago->updated_at->format('H:i') }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size:2rem"></i>
                            <p class="text-muted mt-2 mb-0">No hay pagos aprobados recientes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recaudo por metodo + acciones --}}
        <div class="col-lg-4 mb-4">
            {{-- Recaudo por metodo --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-pie-chart me-2 text-primary"></i>Recaudo por Metodo (Hoy)
                    </h6>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    @php
                        $metodos = [
                            'efectivo' => ['Efectivo', 'bi-cash', 'text-success'],
                            'nequi' => ['Nequi', 'bi-phone', 'text-purple'],
                            'transferencia' => ['Transferencia', 'bi-bank', 'text-info'],
                            'tarjeta' => ['Tarjeta', 'bi-credit-card', 'text-warning'],
                            'otro' => ['Otro', 'bi-three-dots', 'text-secondary'],
                        ];
                        $totalMetodos = array_sum($porMetodoPago);
                    @endphp

                    @forelse($porMetodoPago as $metodo => $total)
                        @php $cfg = $metodos[$metodo] ?? ['Otro', 'bi-three-dots', 'text-secondary']; @endphp
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <i class="bi {{ $cfg[1] }} {{ $cfg[2] }} me-2"></i>
                                <span>{{ $cfg[0] }}</span>
                            </div>
                            <span class="fw-semibold">${{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0 text-center py-2">Sin recaudo hoy</p>
                    @endforelse

                    @if($totalMetodos > 0)
                        <div class="border-top mt-2 pt-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold text-success">${{ number_format($totalMetodos, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($recaudadoSemana > 0)
                        <div class="mt-3 pt-2 border-top">
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Recaudo esta semana</span>
                                <span class="fw-semibold">${{ number_format($recaudadoSemana, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Garantias cobrables --}}
            @if($garantiasCobrables['count'] > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-shield-check me-2 text-danger"></i>Garantias Cobrables
                    </h6>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span>Garantias activas</span>
                        <span class="fw-bold">{{ $garantiasCobrables['count'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span>Monto cobrable</span>
                        <span class="fw-bold text-danger">${{ number_format($garantiasCobrables['monto'], 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('recepcion.garantias.index') }}" class="btn btn-outline-danger btn-sm w-100 mt-2">
                        <i class="bi bi-arrow-right me-1"></i>Ver Garantias
                    </a>
                </div>
            </div>
            @endif

            {{-- Acciones rapidas --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-lightning me-2 text-primary"></i>Acciones Rapidas
                    </h6>
                </div>
                <div class="card-body px-4 pb-4 pt-3 d-grid gap-2">
                    <a href="{{ route('contabilidad.ordenes-pendientes') }}" class="btn btn-outline-danger btn-lg d-flex align-items-center justify-content-center gap-2" style="min-height:48px">
                        <i class="bi bi-receipt-cutoff"></i> Ordenes con Saldo
                    </a>
                    <a href="{{ route('contabilidad.pagos-pendientes') }}" class="btn btn-outline-warning btn-lg d-flex align-items-center justify-content-center gap-2" style="min-height:48px">
                        <i class="bi bi-hourglass-split"></i> Pagos por Aprobar
                    </a>
                    <a href="{{ route('contabilidad.items.index') }}" class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-center gap-2" style="min-height:48px">
                        <i class="bi bi-tags"></i> Catalogo de Items (Productos y servicios)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .summary-cards a:hover .summary-card {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .summary-cards a .summary-card {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .bg-purple {
        background-color: rgba(128, 0, 255, 0.15) !important;
    }
    .text-purple {
        color: #7c3aed !important;
    }
</style>
@endsection
