<x-app-layout>
    @section('title', 'Novedades PdV')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Novedades de Inventario</h4>
            <a href="{{ route('pdv.reportes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Reportes</a>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <p class="text-muted">Las novedades se gestionan desde el módulo de inventario.</p>
                <a href="{{ route('novedades-stock') }}" class="btn text-white" style="background: var(--miracle-pink);">
                    <i class="bi bi-exclamation-triangle me-1"></i>Ir a Novedades
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
