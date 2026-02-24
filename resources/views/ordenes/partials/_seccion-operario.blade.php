{{-- Seccion 5: Asignar Operario --}}
<div class="card border-0 shadow-sm mb-4 wizard-section" data-section="5" id="seccionOperario">
    <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-person-gear me-2 text-primary"></i>5. Asignar Operario
        </h6>
    </div>
    <div class="card-body px-4 pb-4 pt-2">
        {{-- Mensaje cuando no hay piezas --}}
        <div id="operarioInfo" class="alert alert-info mb-0">
            <i class="bi bi-info-circle me-2"></i>
            La asignacion de operario solo es necesaria cuando la orden tiene piezas.
            Sin piezas, la orden se trata como venta directa.
        </div>

        {{-- Selector cuando hay piezas --}}
        <div id="operarioSelector" style="display:none;">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Seleccionar Operario <span class="text-danger">*</span></label>
                    <select id="operario_id" class="form-select">
                        <option value="">-- Seleccione un operario --</option>
                        @foreach($operarios as $op)
                            <option value="{{ $op->id }}">{{ $op->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted mt-1 d-block">
                        <i class="bi bi-exclamation-triangle me-1"></i>Obligatorio para generar la orden con piezas.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
