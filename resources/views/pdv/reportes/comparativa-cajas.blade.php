<x-app-layout>
    @section('title', 'Comparativa Cajas')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-bar-chart-steps me-2"></i>Comparativa entre Cajas</h4>
            <a href="{{ route('pdv.reportes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Reportes</a>
        </div>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form class="row g-2" method="GET">
                    <div class="col-md-3"><input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde', now()->startOfMonth()->toDateString()) }}"></div>
                    <div class="col-md-3"><input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta', now()->toDateString()) }}"></div>
                    <div class="col-md-2"><button type="submit" class="btn w-100 text-white" style="background: var(--miracle-pink);">Comparar</button></div>
                </form>
            </div>
        </div>
        @if($datos)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Caja</th><th class="text-center">Ventas</th><th class="text-end">Total</th><th class="text-end">Promedio</th><th class="text-end">Efectivo</th><th class="text-end">Transfer.</th></tr></thead>
                        <tbody>
                            @foreach($datos as $row)
                                <tr><td class="fw-semibold">{{ $row['caja_nombre'] }}</td><td class="text-center">{{ $row['cantidad_ventas'] }}</td><td class="text-end fw-bold">${{ number_format($row['total_ventas'], 2) }}</td><td class="text-end">${{ number_format($row['promedio'], 2) }}</td><td class="text-end">${{ number_format($row['total_efectivo'], 2) }}</td><td class="text-end">${{ number_format($row['total_transferencia'], 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm"><div class="card-body text-center text-muted py-5">Seleccione un rango de fechas para comparar</div></div>
        @endif
    </div>
</x-app-layout>
