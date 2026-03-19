<x-app-layout>
    @section('title', 'Reporte Vales')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-ticket-perforated me-2"></i>Reporte de Vales</h4>
            <a href="{{ route('pdv.reportes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Reportes</a>
        </div>
        <div class="row g-3 mb-4">
            @foreach(($datos['por_estado'] ?? collect()) as $estado => $info)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center"><div class="card-body">
                        <div class="h5 fw-bold">${{ number_format($info['total'], 2) }}</div>
                        <small class="text-muted">{{ ucfirst($estado) }} ({{ $info['cantidad'] }})</small>
                    </div></div>
                </div>
            @endforeach
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Caja</th><th>Descripción</th><th class="text-end">Monto</th><th>Responsable</th><th>Estado</th><th>Fecha</th></tr></thead>
                    <tbody>
                        @forelse(($datos['vales'] ?? collect()) as $vale)
                            <tr><td>{{ $vale->caja->nombre ?? '-' }}</td><td>{{ $vale->descripcion }}</td><td class="text-end fw-bold">${{ number_format($vale->monto, 2) }}</td><td>{{ $vale->usuario->name ?? '-' }}</td><td>{!! $vale->estado_badge !!}</td><td>{{ $vale->created_at->format('d/m/Y') }}</td></tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Sin vales</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
