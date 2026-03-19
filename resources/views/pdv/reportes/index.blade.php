<x-app-layout>
    @section('title', 'Reportes PdV')

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2"></i>Reportes Punto de Venta</h4>
            <a href="{{ route('pdv.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Dashboard
            </a>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('pdv.reportes.ventas-diarias') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-calendar-day display-4 mb-2" style="color: var(--miracle-pink);"></i>
                        <h6 class="fw-bold">Ventas Diarias</h6>
                        <small class="text-muted">Desglose por caja, vendedor y forma de pago</small>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('pdv.sesiones.historial') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-cash-coin display-4 mb-2" style="color: var(--miracle-lilac);"></i>
                        <h6 class="fw-bold">Cierre de Turno</h6>
                        <small class="text-muted">Efectivo esperado, contado y diferencia</small>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('pdv.reportes.top-productos') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-trophy display-4 mb-2 text-warning"></i>
                        <h6 class="fw-bold">Top Productos</h6>
                        <small class="text-muted">Productos más vendidos por período</small>
                    </div>
                </a>
            </div>
            @if(auth()->user()->hasRole('admin'))
            <div class="col-md-4">
                <a href="{{ route('pdv.reportes.comparativa-cajas') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-bar-chart-steps display-4 mb-2 text-info"></i>
                        <h6 class="fw-bold">Comparativa Cajas</h6>
                        <small class="text-muted">Comparar rendimiento entre cajas</small>
                    </div>
                </a>
            </div>
            @endif
            <div class="col-md-4">
                <a href="{{ route('pdv.reportes.vales') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-ticket-perforated display-4 mb-2 text-danger"></i>
                        <h6 class="fw-bold">Reporte Vales</h6>
                        <small class="text-muted">Estado, valor, responsable y fecha</small>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('pdv.reportes.prefacturas') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-receipt display-4 mb-2 text-success"></i>
                        <h6 class="fw-bold">Historial Prefacturas</h6>
                        <small class="text-muted">Aceptadas, anuladas y tiempos de atención</small>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
