<x-app-layout>
    <x-slot name="header">
        Reporte de Ventas PdV
        @if($ubicacionSeleccionada)
            <span class="badge bg-primary ms-2">{{ $ubicacionSeleccionada->nombre }}</span>
        @endif
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filtros --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-funnel"></i> Período del Reporte</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('punto-venta.reporte') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Ubicación</label>
                                <select name="ubicacion_id" class="form-select">
                                    <option value="">-- Todas --</option>
                                    @foreach($ubicaciones as $ubi)
                                        <option value="{{ $ubi->id }}" {{ $ubicacionId == $ubi->id ? 'selected' : '' }}>
                                            {{ $ubi->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Desde</label>
                                <input type="date" name="fecha_desde" class="form-control" value="{{ $fechaDesde }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hasta</label>
                                <input type="date" name="fecha_hasta" class="form-control" value="{{ $fechaHasta }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Generar Reporte
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Resumen General --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <h6 class="text-success"><i class="bi bi-cash-stack"></i> Total Ventas</h6>
                            <p class="display-4 mb-0 fw-bold text-success">{{ $resumen['total_ventas'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-primary h-100">
                        <div class="card-body text-center">
                            <h6 class="text-primary"><i class="bi bi-currency-dollar"></i> Monto Total</h6>
                            <p class="h3 mb-0 fw-bold text-primary">${{ number_format($resumen['monto_total'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info h-100">
                        <div class="card-body text-center">
                            <h6 class="text-info"><i class="bi bi-graph-up"></i> Promedio</h6>
                            <p class="h3 mb-0 fw-bold text-info">${{ number_format($resumen['monto_promedio'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning h-100">
                        <div class="card-body text-center">
                            <h6 class="text-warning"><i class="bi bi-box-seam"></i> Items Vendidos</h6>
                            <p class="display-4 mb-0 fw-bold text-warning">{{ $resumen['total_items'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                {{-- Por método de pago --}}
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-credit-card"></i> Por Método de Pago</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Método</th>
                                            <th class="text-center">Ventas</th>
                                            <th class="text-end">Monto</th>
                                            <th class="text-end">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $metodos = ['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'mixto' => 'Mixto'];
                                            $colores = ['efectivo' => 'success', 'tarjeta' => 'primary', 'transferencia' => 'info', 'mixto' => 'warning'];
                                        @endphp
                                        @foreach($metodos as $key => $label)
                                            @php
                                                $data = $porMetodoPago[$key] ?? ['cantidad' => 0, 'total' => 0];
                                                $porcentaje = $resumen['monto_total'] > 0 ? ($data['total'] / $resumen['monto_total']) * 100 : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <span class="badge bg-{{ $colores[$key] }}">{{ $label }}</span>
                                                </td>
                                                <td class="text-center">{{ $data['cantidad'] }}</td>
                                                <td class="text-end">${{ number_format($data['total'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($porcentaje, 1) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ventas anuladas --}}
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-x-circle"></i> Ventas Anuladas</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h6 class="text-muted">Cantidad</h6>
                                    <p class="display-5 mb-0 text-danger">{{ $totalAnuladas }}</p>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-muted">Monto</h6>
                                    <p class="h3 mb-0 text-danger">${{ number_format($montoAnulado, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            @if($totalAnuladas > 0)
                                <hr>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-info-circle"></i>
                                    Las ventas anuladas ya restauraron el stock correspondiente.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ventas por día --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-calendar3"></i> Ventas por Día</h5>
                    <span class="text-muted">{{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Día</th>
                                    <th class="text-center">N° Ventas</th>
                                    <th class="text-end">Monto Total</th>
                                    <th class="text-end">Promedio</th>
                                    <th style="width: 30%;">Distribución</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($porDia as $fecha => $data)
                                    @php
                                        $fechaCarbon = \Carbon\Carbon::parse($fecha);
                                        $promedio = $data['cantidad'] > 0 ? $data['total'] / $data['cantidad'] : 0;
                                        $porcentaje = $resumen['monto_total'] > 0 ? ($data['total'] / $resumen['monto_total']) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $fechaCarbon->format('d/m/Y') }}</td>
                                        <td>{{ $fechaCarbon->locale('es')->dayName }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $data['cantidad'] }}</span>
                                        </td>
                                        <td class="text-end fw-bold">${{ number_format($data['total'], 0, ',', '.') }}</td>
                                        <td class="text-end">${{ number_format($promedio, 0, ',', '.') }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" style="width: {{ $porcentaje }}%">
                                                    {{ number_format($porcentaje, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1"></i>
                                            <p>No hay ventas en este período</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('punto-venta.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Dashboard
                </a>
                <div>
                    <a href="{{ route('punto-venta.index') }}" class="btn btn-primary">
                        <i class="bi bi-list-ul"></i> Ver Detalle de Ventas
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
