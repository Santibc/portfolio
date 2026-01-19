<x-app-layout>
    <x-slot name="header">
        Punto de Venta - Dashboard
        @if($ubicacion)
            <span class="badge bg-primary ms-2">{{ $ubicacion->nombre }}</span>
        @endif
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alertas --}}
            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            @if(session('warning'))
                <x-alert type="warning" :message="session('warning')" />
            @endif

            {{-- Selector de ubicación y botón nueva venta --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <form action="{{ route('punto-venta.cambiar-ubicacion') }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <select name="ubicacion_id" class="form-select" onchange="this.form.submit()">
                            @foreach($ubicaciones as $ubi)
                                <option value="{{ $ubi->id }}" {{ $ubicacionId == $ubi->id ? 'selected' : '' }}>
                                    {{ $ubi->nombre }} ({{ ucfirst($ubi->tipo) }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('punto-venta.nueva-venta') }}" class="btn btn-success btn-lg">
                        <i class="bi bi-cart-plus"></i> Nueva Venta
                    </a>
                </div>
            </div>

            {{-- Métricas del día --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary h-100">
                        <div class="card-body text-center">
                            <h6 class="card-title text-primary">
                                <i class="bi bi-cash-stack"></i> Ventas Hoy
                            </h6>
                            <p class="display-5 mb-0 fw-bold text-primary">
                                {{ $metricasDia['total_ventas'] }}
                            </p>
                            <small class="text-muted">transacciones</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <h6 class="card-title text-success">
                                <i class="bi bi-currency-dollar"></i> Total Hoy
                            </h6>
                            <p class="display-5 mb-0 fw-bold text-success">
                                ${{ number_format($metricasDia['monto_total'], 0, ',', '.') }}
                            </p>
                            <small class="text-muted">en ventas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info h-100">
                        <div class="card-body text-center">
                            <h6 class="card-title text-info">
                                <i class="bi bi-graph-up"></i> Promedio
                            </h6>
                            <p class="display-5 mb-0 fw-bold text-info">
                                ${{ number_format($metricasDia['monto_promedio'], 0, ',', '.') }}
                            </p>
                            <small class="text-muted">por venta</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning h-100">
                        <div class="card-body text-center">
                            <h6 class="card-title text-warning">
                                <i class="bi bi-box-seam"></i> Items Vendidos
                            </h6>
                            <p class="display-5 mb-0 fw-bold text-warning">
                                {{ $metricasDia['total_items'] }}
                            </p>
                            <small class="text-muted">productos</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resumen por método de pago --}}
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-credit-card"></i> Ventas por Método de Pago (Hoy)</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center border-end">
                                    <i class="bi bi-cash text-success fs-2"></i>
                                    <h6 class="mt-2">Efectivo</h6>
                                    <p class="h4 mb-0">${{ number_format($metricasDia['efectivo'] ?? 0, 0, ',', '.') }}</p>
                                    <small class="text-muted">
                                        {{ $metricasDia['por_metodo_pago']['efectivo']['cantidad'] ?? 0 }} ventas
                                    </small>
                                </div>
                                <div class="col-md-3 text-center border-end">
                                    <i class="bi bi-credit-card-2-front text-primary fs-2"></i>
                                    <h6 class="mt-2">Tarjeta</h6>
                                    <p class="h4 mb-0">${{ number_format($metricasDia['tarjeta'] ?? 0, 0, ',', '.') }}</p>
                                    <small class="text-muted">
                                        {{ $metricasDia['por_metodo_pago']['tarjeta']['cantidad'] ?? 0 }} ventas
                                    </small>
                                </div>
                                <div class="col-md-3 text-center border-end">
                                    <i class="bi bi-bank text-info fs-2"></i>
                                    <h6 class="mt-2">Transferencia</h6>
                                    <p class="h4 mb-0">${{ number_format($metricasDia['transferencia'] ?? 0, 0, ',', '.') }}</p>
                                    <small class="text-muted">
                                        {{ $metricasDia['por_metodo_pago']['transferencia']['cantidad'] ?? 0 }} ventas
                                    </small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <i class="bi bi-wallet2 text-warning fs-2"></i>
                                    <h6 class="mt-2">Mixto</h6>
                                    <p class="h4 mb-0">${{ number_format($metricasDia['por_metodo_pago']['mixto']['total'] ?? 0, 0, ',', '.') }}</p>
                                    <small class="text-muted">
                                        {{ $metricasDia['por_metodo_pago']['mixto']['cantidad'] ?? 0 }} ventas
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-calendar-month"></i> Resumen del Mes</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total ventas:</span>
                                <strong>{{ $metricasMes['total_ventas'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Monto total:</span>
                                <strong class="text-success">${{ number_format($metricasMes['monto_total'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Promedio diario:</span>
                                <strong>${{ number_format($metricasMes['monto_promedio'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Días con ventas:</span>
                                <strong>{{ $metricasMes['dias_con_ventas'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Productos más vendidos --}}
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-trophy"></i> Top 5 Productos del Mes</h5>
                        </div>
                        <div class="card-body p-0">
                            @if(count($productosTop) > 0)
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($productosTop as $index => $producto)
                                            <tr>
                                                <td>
                                                    @if($index == 0)
                                                        <i class="bi bi-trophy-fill text-warning"></i>
                                                    @else
                                                        {{ $index + 1 }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $producto['referencia'] }}</strong><br>
                                                    <small class="text-muted">{{ Str::limit($producto['nombre'], 30) }}</small>
                                                </td>
                                                <td class="text-center">{{ $producto['cantidad_total'] }}</td>
                                                <td class="text-end">${{ number_format($producto['monto_total'], 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p>No hay ventas este mes</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Últimas ventas del día --}}
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Últimas Ventas de Hoy</h5>
                            <a href="{{ route('punto-venta.index') }}" class="btn btn-sm btn-outline-primary">
                                Ver todas
                            </a>
                        </div>
                        <div class="card-body p-0">
                            @if($ultimasVentas->count() > 0)
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>N° Venta</th>
                                            <th>Hora</th>
                                            <th class="text-center">Items</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ultimasVentas as $venta)
                                            <tr class="{{ $venta->estado === 'anulada' ? 'table-danger' : '' }}">
                                                <td>
                                                    <a href="#" onclick="verDetalle({{ $venta->id }}); return false;">
                                                        {{ $venta->numero_venta }}
                                                    </a>
                                                    @if($venta->estado === 'anulada')
                                                        <span class="badge bg-danger">Anulada</span>
                                                    @endif
                                                </td>
                                                <td>{{ $venta->created_at->format('H:i') }}</td>
                                                <td class="text-center">{{ $venta->items->sum('cantidad') }}</td>
                                                <td class="text-end">${{ number_format($venta->total, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-cart-x fs-1"></i>
                                    <p>No hay ventas hoy</p>
                                    <a href="{{ route('punto-venta.nueva-venta') }}" class="btn btn-success">
                                        <i class="bi bi-cart-plus"></i> Iniciar primera venta
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Accesos rápidos --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-around flex-wrap gap-2">
                                <a href="{{ route('punto-venta.nueva-venta') }}" class="btn btn-lg btn-success">
                                    <i class="bi bi-cart-plus"></i> Nueva Venta
                                </a>
                                <a href="{{ route('punto-venta.index') }}" class="btn btn-lg btn-primary">
                                    <i class="bi bi-list-ul"></i> Historial de Ventas
                                </a>
                                <a href="{{ route('punto-venta.reporte') }}" class="btn btn-lg btn-info">
                                    <i class="bi bi-bar-chart"></i> Reportes
                                </a>
                                <a href="{{ route('stock.index') }}" class="btn btn-lg btn-warning">
                                    <i class="bi bi-box-seam"></i> Inventario
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal detalle venta --}}
    <div class="modal fade" id="modalDetalleVenta" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoDetalleVenta">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function verDetalle(ventaId) {
            const modal = new bootstrap.Modal(document.getElementById('modalDetalleVenta'));
            document.getElementById('contenidoDetalleVenta').innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>';
            modal.show();

            fetch(`{{ url('punto-venta') }}/${ventaId}/detalle`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('contenidoDetalleVenta').innerHTML = data.html;
                })
                .catch(error => {
                    document.getElementById('contenidoDetalleVenta').innerHTML = '<div class="alert alert-danger">Error al cargar los datos</div>';
                });
        }
    </script>
    @endpush
</x-app-layout>
