@php
    $prefix = $prefix ?? 'default';
    $ubicaciones = $ubicaciones ?? collect();
@endphp

<div class="border rounded p-3 mb-3 bg-light" id="garProdContainer_{{ $prefix }}">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <strong><i class="bi bi-box-seam"></i> Productos de cambio <small class="text-muted fw-normal">(opcional)</small></strong>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-gar-prod-toggle="{{ $prefix }}">
            <i class="bi bi-chevron-down"></i> Mostrar
        </button>
    </div>
    <div id="garProdBody_{{ $prefix }}" style="display: none;">
        <p class="small text-muted mb-2">Selecciona una ubicación y agrega productos que se descontarán del stock al liberar la garantía.</p>
        <div class="row g-2 mb-2">
            <div class="col-md-12">
                <label class="form-label small fw-semibold mb-1">Ubicación</label>
                <select class="form-select form-select-sm" id="garProdUbicacion_{{ $prefix }}" data-gar-prod-prefix="{{ $prefix }}">
                    <option value="">Selecciona una ubicación...</option>
                    @foreach($ubicaciones as $u)
                        <option value="{{ $u->id }}">{{ $u->nombre }}@if(!empty($u->tipo)) ({{ ucfirst($u->tipo) }})@endif</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row g-2 align-items-end mb-2">
            <div class="col-md-5">
                <label class="form-label small fw-semibold mb-1">Producto</label>
                <select class="form-select form-select-sm" id="garProdProducto_{{ $prefix }}" disabled>
                    <option value="">Selecciona ubicación primero</option>
                </select>
            </div>
            <div class="col-md-3" id="garProdVarianteWrap_{{ $prefix }}" style="display: none;">
                <label class="form-label small fw-semibold mb-1">Variante</label>
                <select class="form-select form-select-sm" id="garProdVariante_{{ $prefix }}">
                    <option value="">—</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Cantidad</label>
                <input type="number" class="form-control form-control-sm" id="garProdCantidad_{{ $prefix }}" min="1" value="1">
                <small class="text-muted" id="garProdStockInfo_{{ $prefix }}"></small>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-primary w-100" id="garProdAgregar_{{ $prefix }}" disabled>
                    <i class="bi bi-plus-lg"></i> Agregar
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0" id="garProdTabla_{{ $prefix }}">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Variante</th>
                        <th class="text-center" style="width: 90px;">Cantidad</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-muted text-center" data-empty-row><td colspan="4" class="py-2">Sin productos agregados</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
