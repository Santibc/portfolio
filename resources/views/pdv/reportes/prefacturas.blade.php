<x-app-layout>
    @section('title', 'Reporte Prefacturas')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Historial de Prefacturas</h4>
            <a href="{{ route('pdv.reportes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Reportes</a>
        </div>
        <div class="row g-3 mb-4">
            @foreach(($datos['por_estado'] ?? collect()) as $estado => $info)
                <div class="col-md-3"><div class="card border-0 shadow-sm text-center"><div class="card-body">
                    <div class="h5 fw-bold">{{ $info['cantidad'] }}</div>
                    <small class="text-muted">{{ ucfirst($estado) }} (${{ number_format($info['total'], 2) }})</small>
                </div></div></div>
            @endforeach
            <div class="col-md-3"><div class="card border-0 shadow-sm text-center"><div class="card-body">
                <div class="h5 fw-bold">{{ number_format($datos['tiempo_respuesta_promedio'] ?? 0, 0) }} min</div>
                <small class="text-muted">Tiempo respuesta promedio</small>
            </div></div></div>
        </div>
    </div>
</x-app-layout>
