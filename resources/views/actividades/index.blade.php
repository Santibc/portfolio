@extends('layouts.app')

@section('title', 'Mis Actividades')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Mis Actividades" description="Registro de todas tus acciones en el sistema" />

    {{-- Stat Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-activity" :value="$stats['total']" title="Total Actividades" color="primary" />
        <x-sinden.stat-card icon="bi bi-calendar-check" :value="$stats['hoy']" title="Hoy" color="success" />
        <x-sinden.stat-card icon="bi bi-star" :value="$stats['accion_frecuente']" title="Accion Frecuente" color="info" />
        <x-sinden.stat-card icon="bi bi-clock" :value="$stats['ultima_actividad']" title="Ultima Actividad" color="secondary" />
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-3">
            <h6 class="mb-3 fw-semibold text-dark">
                <i class="bi bi-funnel me-2 text-primary"></i>Filtros
            </h6>
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filtroFechaDesde" class="form-label small text-muted mb-1">Fecha Desde</label>
                    <input type="date" class="form-control" id="filtroFechaDesde">
                </div>
                <div class="col-md-3">
                    <label for="filtroFechaHasta" class="form-label small text-muted mb-1">Fecha Hasta</label>
                    <input type="date" class="form-control" id="filtroFechaHasta">
                </div>
                <div class="col-md-3">
                    <label for="filtroAccion" class="form-label small text-muted mb-1">Tipo de Accion</label>
                    <select class="form-select" id="filtroAccion">
                        <option value="">Todas las acciones</option>
                        @foreach($tiposAccion as $clave => $etiqueta)
                            <option value="{{ $clave }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary flex-fill" id="btnFiltrar">
                            <i class="bi bi-search me-1"></i>Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnLimpiar">
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
                    <i class="bi bi-clock-history me-2 text-primary"></i>Registro de Actividades
                </h6>
                <span class="badge bg-light text-muted border" id="totalRegistros"></span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sinden-datatable" id="actividadesTable" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:150px">Fecha/Hora</th>
                            <th style="width:200px">Accion</th>
                            <th style="width:120px">Orden</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/actividades.js') }}"></script>
<script>
    $(function() {
        initActividadesTable({
            ajaxUrl: window.location.href,
            personal: true
        });
    });
</script>
@endpush
