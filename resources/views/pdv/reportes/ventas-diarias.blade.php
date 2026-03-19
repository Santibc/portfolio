<x-app-layout>
    @section('title', 'Ventas Diarias')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-calendar-day me-2"></i>Ventas Diarias</h4>
            <a href="{{ route('pdv.reportes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Reportes</a>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form class="row g-2" method="GET">
                    <div class="col-md-3">
                        <input type="date" name="fecha" class="form-control" value="{{ request('fecha', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <select name="caja_id" class="form-select">
                            <option value="">Todas las cajas</option>
                            @foreach($cajas as $c)
                                <option value="{{ $c->id }}" {{ request('caja_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn w-100" style="background: var(--miracle-pink); color: white;">Consultar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center"><div class="card-body">
                    <div class="h4 fw-bold" style="color: var(--miracle-pink);">${{ number_format($datos['monto_total'] ?? 0, 2) }}</div>
                    <small class="text-muted">Total ({{ $datos['total_ventas'] ?? 0 }} ventas)</small>
                </div></div>
            </div>
            @foreach(($datos['por_metodo_pago'] ?? collect()) as $metodo => $info)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center"><div class="card-body">
                        <div class="h5 fw-bold">${{ number_format($info['total'], 2) }}</div>
                        <small class="text-muted">{{ ucfirst($metodo) }} ({{ $info['cantidad'] }})</small>
                    </div></div>
                </div>
            @endforeach
        </div>

        @if(!empty($datos['por_vendedor']))
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0">Por Vendedor/Cajero</h6></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light"><tr><th>Cajero</th><th class="text-center">Ventas</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @foreach($datos['por_vendedor'] as $info)
                                <tr><td>{{ $info['vendedor'] }}</td><td class="text-center">{{ $info['cantidad'] }}</td><td class="text-end fw-bold">${{ number_format($info['total'], 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
