<x-app-layout>
    @section('title', 'Top Productos')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-trophy me-2"></i>Top Productos Vendidos</h4>
            <a href="{{ route('pdv.reportes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Reportes</a>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form class="row g-2" method="GET">
                    <div class="col-md-2"><input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde', now()->startOfMonth()->toDateString()) }}"></div>
                    <div class="col-md-2"><input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta', now()->toDateString()) }}"></div>
                    <div class="col-md-3">
                        <select name="caja_id" class="form-select"><option value="">Todas las cajas</option>
                            @foreach($cajas as $c)<option value="{{ $c->id }}" {{ request('caja_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-2"><button type="submit" class="btn w-100 text-white" style="background: var(--miracle-pink);">Consultar</button></div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>#</th><th>Producto</th><th>Referencia</th><th class="text-center">Uds. Vendidas</th><th class="text-end">Total Vendido</th></tr></thead>
                    <tbody>
                        @forelse($datos as $i => $prod)
                            <tr>
                                <td><span class="badge bg-{{ $i < 3 ? 'warning text-dark' : 'secondary' }}">{{ $i + 1 }}</span></td>
                                <td class="fw-semibold">{{ $prod['nombre'] }}</td>
                                <td class="text-muted">{{ $prod['referencia'] }}</td>
                                <td class="text-center">{{ $prod['cantidad_total'] }}</td>
                                <td class="text-end fw-bold">${{ number_format($prod['monto_total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Sin datos para el período seleccionado</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
