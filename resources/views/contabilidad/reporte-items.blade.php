@extends('layouts.app')

@section('title', 'Reporte Ventas por Items')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Reporte Ventas por Items" description="Items vendidos en ordenes con estado de pago completado">
        <x-slot name="actions">
            <a href="{{ route('contabilidad.reporte-items.export') }}" class="btn btn-success" id="btnExportar" style="min-height:48px">
                <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
            </a>
        </x-slot>
    </x-sinden.page-header>

    {{-- Stat Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-gear" :value="'$' . number_format($totalServicios, 0, ',', '.')" title="Total Servicios" color="info" />
        <x-sinden.stat-card icon="bi bi-box" :value="'$' . number_format($totalMateriales, 0, ',', '.')" title="Total Materiales" color="warning" />
        <x-sinden.stat-card icon="bi bi-bag-check" :value="'$' . number_format($totalProductos, 0, ',', '.')" title="Total Prod. Terminados" color="success" />
        <x-sinden.stat-card icon="bi bi-cash-stack" :value="'$' . number_format($granTotal, 0, ',', '.')" title="Gran Total" color="primary" />
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body px-4 py-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3 col-6">
                    <label class="form-label small text-muted mb-1">Buscar (codigo / descripcion)</label>
                    <input type="text" class="form-control" id="filtroBusqueda" placeholder="Ej: CORTE-001 o corte laser" style="min-height:44px">
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small text-muted mb-1">Categoria</label>
                    <select class="form-select" id="filtroCategoria" style="min-height:44px">
                        <option value="todas">Todas</option>
                        <option value="servicio">Servicio</option>
                        <option value="material">Material</option>
                        <option value="producto_terminado">Producto Terminado</option>
                    </select>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small text-muted mb-1">Desde</label>
                    <input type="date" class="form-control" id="filtroFechaDesde" style="min-height:44px">
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small text-muted mb-1">Hasta</label>
                    <input type="date" class="form-control" id="filtroFechaHasta" style="min-height:44px">
                </div>
                <div class="col-md-3 col-12">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary flex-grow-1" id="btnFiltrarReporte" style="min-height:44px">
                            <i class="bi bi-search me-1"></i>Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnLimpiarReporte" style="min-height:44px">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-bar-chart-line me-2 text-primary"></i>Items Vendidos (Ordenes Pagadas)
                </h6>
                <span class="badge bg-light text-muted border" id="totalRegistros"></span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sinden-datatable" id="reporteItemsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Fecha</th>
                            <th>Codigo</th>
                            <th>Descripcion</th>
                            <th class="text-center">Categoria</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">P. Unitario</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">IVA</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="6" class="text-end">Totales (filtro aplicado):</td>
                            <td class="text-end" id="sumaPrecio">-</td>
                            <td class="text-end" id="sumaSubtotal">$0</td>
                            <td class="text-end" id="sumaIva">$0</td>
                            <td class="text-end" id="sumaTotal">$0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/contabilidad.js') }}"></script>
<script>
    $(function() {
        initReporteItemsTable({
            ajaxUrl: '{{ route("contabilidad.reporte-items") }}',
            exportUrl: '{{ route("contabilidad.reporte-items.export") }}'
        });
    });
</script>
@endpush
