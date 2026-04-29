<x-app-layout>
    @section('title', 'Resumen Sesion')

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-receipt me-2"></i>Resumen — {{ $sesion->caja->nombre }}
            </h4>
            <div>
                @if($sesion->estado === 'cerrada')
                    <a href="{{ route('pdv.sesiones.ticket-print', $sesion->id) }}" target="_blank" class="btn btn-outline-primary me-2">
                        <i class="bi bi-printer me-1"></i>Imprimir Ticket
                    </a>
                @endif
                <a href="{{ route('pdv.sesiones.historial') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Historial
                </a>
            </div>
        </div>

        {{-- Session Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"><strong>Caja:</strong> {{ $sesion->caja->nombre }}</div>
                    <div class="col-md-3"><strong>Cajero:</strong> {{ $sesion->usuario->name }}</div>
                    <div class="col-md-3"><strong>Apertura:</strong> {{ $sesion->abierta_en->format('d/m/Y h:i A') }}</div>
                    <div class="col-md-3">
                        <strong>Cierre:</strong>
                        {{ $sesion->cerrada_en ? $sesion->cerrada_en->format('d/m/Y h:i A') : 'En curso' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-3">
                        <div class="h5 fw-bold" style="color: var(--miracle-pink);">${{ number_format($resumen['ventas']['total'], 2) }}</div>
                        <small class="text-muted">Total Ventas ({{ $resumen['ventas']['cantidad'] }})</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-3">
                        <div class="h5 fw-bold text-success">${{ number_format($resumen['ventas']['efectivo'], 2) }}</div>
                        <small class="text-muted">Efectivo</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-3">
                        <div class="h5 fw-bold text-info">${{ number_format($resumen['ventas']['transferencia'], 2) }}</div>
                        <small class="text-muted">Transferencias</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-3">
                        <div class="h5 fw-bold text-warning">${{ number_format($resumen['vales']['total'], 2) }}</div>
                        <small class="text-muted">Vales ({{ $resumen['vales']['cantidad'] }})</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-3">
                        <div class="h5 fw-bold text-danger">${{ number_format($resumen['anulaciones']['total'], 2) }}</div>
                        <small class="text-muted">Anuladas ({{ $resumen['anulaciones']['cantidad'] }})</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-3">
                        @if($sesion->diferencia !== null)
                            <div class="h5 fw-bold {{ $sesion->diferencia_color }}">${{ number_format(abs($sesion->diferencia), 2) }}</div>
                            <small class="text-muted">{{ $sesion->diferencia_label }}</small>
                        @else
                            <div class="h5 fw-bold text-muted">-</div>
                            <small class="text-muted">Sin cuadre</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left column: Cuadre + Pagos --}}
            <div class="col-lg-6">
                {{-- Cuadre de Caja --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-calculator me-2"></i>Cuadre de Caja</h6></div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr><td class="ps-3">Base (apertura):</td><td class="text-end pe-3">${{ number_format($resumen['monto_apertura'], 2) }}</td></tr>
                                <tr><td class="ps-3">(+) Ventas en efectivo:</td><td class="text-end pe-3">${{ number_format($resumen['ventas']['efectivo'], 2) }}</td></tr>
                                <tr><td class="ps-3">(-) Vales emitidos:</td><td class="text-end pe-3 text-danger">-${{ number_format($resumen['vales']['total'], 2) }}</td></tr>
                                <tr class="table-active"><td class="ps-3 fw-bold">(=) Efectivo esperado:</td><td class="text-end pe-3 fw-bold">${{ number_format($resumen['monto_esperado_efectivo'], 2) }}</td></tr>
                                @if($sesion->estado === 'cerrada')
                                    <tr><td class="ps-3">Efectivo contado:</td><td class="text-end pe-3">${{ number_format($sesion->monto_contado, 2) }}</td></tr>
                                    <tr class="table-warning">
                                        <td class="ps-3 fw-bold">Diferencia:</td>
                                        <td class="text-end pe-3 fw-bold {{ $sesion->diferencia_color }}">
                                            ${{ number_format($sesion->diferencia, 2) }}
                                            ({{ $sesion->diferencia_label }})
                                        </td>
                                    </tr>
                                @else
                                    <tr class="table-info">
                                        <td class="ps-3 fw-bold" colspan="2">
                                            <i class="bi bi-info-circle me-1"></i>Sesion en curso - cuadre disponible al cerrar
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Desglose por metodo de pago --}}
                @if($resumen['por_metodo_pago']->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Desglose por Metodo de Pago</h6></div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th class="ps-3">Metodo</th><th class="text-center">Ventas</th><th class="text-end pe-3">Total</th></tr>
                            </thead>
                            <tbody>
                                @foreach($resumen['por_metodo_pago'] as $metodo => $datos)
                                <tr>
                                    <td class="ps-3">
                                        @switch($metodo)
                                            @case('efectivo') <i class="bi bi-cash text-success me-1"></i>Efectivo @break
                                            @case('transferencia') <i class="bi bi-bank text-info me-1"></i>Transferencia @break
                                            @default {{ ucfirst($metodo) }}
                                        @endswitch
                                    </td>
                                    <td class="text-center">{{ $datos['cantidad'] }}</td>
                                    <td class="text-end pe-3 fw-semibold">${{ number_format($datos['total'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if($sesion->observaciones_cierre)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-chat-text me-2"></i>Observaciones</h6></div>
                    <div class="card-body">{{ $sesion->observaciones_cierre }}</div>
                </div>
                @endif
            </div>

            {{-- Right column: Ventas + Vales --}}
            <div class="col-lg-6">
                {{-- Ventas de la sesion --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-bag-check me-2"></i>Ventas de la Sesion</h6>
                        <span class="badge bg-primary">{{ $resumen['ventas']['cantidad'] + $resumen['anulaciones']['cantidad'] }}</span>
                    </div>
                    <div class="card-body p-0">
                        @if($sesion->ventas->count() > 0)
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Venta</th>
                                        <th>Metodo</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center pe-3">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sesion->ventas->sortByDesc('created_at') as $venta)
                                    <tr>
                                        <td class="ps-3">
                                            <small class="text-muted">{{ $venta->codigo ?? '#' . $venta->id }}</small>
                                        </td>
                                        <td>{{ ucfirst($venta->metodo_pago ?? '-') }}</td>
                                        <td class="text-end fw-semibold">${{ number_format($venta->total, 2) }}</td>
                                        <td class="text-center pe-3">
                                            @if($venta->estado === 'completada')
                                                <span class="badge bg-success">Completada</span>
                                            @elseif($venta->estado === 'anulada')
                                                <span class="badge bg-danger">Anulada</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($venta->estado) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-bag-x" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">No hay ventas en esta sesion</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Vales de la sesion --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-ticket-perforated me-2"></i>Vales de la Sesion</h6>
                        <span class="badge bg-warning text-dark">{{ $resumen['vales']['cantidad'] }}</span>
                    </div>
                    <div class="card-body p-0">
                        @if($sesion->vales->count() > 0)
                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Codigo</th>
                                        <th>Motivo</th>
                                        <th class="text-end">Monto</th>
                                        <th class="text-center pe-3">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sesion->vales->sortByDesc('created_at') as $vale)
                                    <tr>
                                        <td class="ps-3"><small>{{ $vale->codigo ?? '#' . $vale->id }}</small></td>
                                        <td>{{ Str::limit($vale->motivo ?? '-', 30) }}</td>
                                        <td class="text-end fw-semibold">${{ number_format($vale->monto, 2) }}</td>
                                        <td class="text-center pe-3">
                                            @if($vale->estado === 'pendiente')
                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                            @elseif($vale->estado === 'redimido')
                                                <span class="badge bg-success">Redimido</span>
                                            @else
                                                <span class="badge bg-danger">Anulado</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-ticket" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">No hay vales en esta sesion</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
