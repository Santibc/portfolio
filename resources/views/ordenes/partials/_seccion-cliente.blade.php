{{-- Seccion 1: Cliente --}}
<div class="card border-0 shadow-sm mb-4 wizard-section" data-section="1" id="seccionCliente">
    <div class="card-header bg-white border-0 px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-person me-2 text-primary"></i>1. Cliente
        </h6>
        <span class="badge bg-light text-muted border" id="clienteStatus">Sin seleccionar</span>
    </div>
    <div class="card-body px-4 pb-4 pt-2">
        <div class="row align-items-end">
            <div class="col-md-8">
                <label class="form-label fw-medium">Buscar cliente <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="clienteSearch" class="form-control border-start-0"
                            placeholder="Escriba nombre, celular o correo..." autocomplete="off">
                    </div>
                    <div id="clienteResults" class="list-group shadow-sm" style="display:none; position:absolute; z-index:1050; width:100%; max-height:250px; overflow-y:auto;"></div>
                </div>
                <input type="hidden" id="cliente_id" name="cliente_id">
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    @if(!empty($clientePredeterminado))
                    <button type="button" class="btn btn-outline-success flex-fill"
                        onclick="seleccionarCliente({{ $clientePredeterminado->id }}, '{{ addslashes($clientePredeterminado->nombre) }}', '{{ addslashes($clientePredeterminado->celular_1 ?? '') }}', '{{ addslashes($clientePredeterminado->correo ?? '') }}')">
                        <i class="bi bi-person-check me-1"></i> Mostrador
                    </button>
                    @endif
                    <button type="button" class="btn btn-outline-primary flex-fill" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                        <i class="bi bi-plus-lg me-1"></i> Crear Nuevo
                    </button>
                </div>
            </div>
        </div>

        {{-- Info del cliente seleccionado (contenido generado via JS) --}}
        <div id="clienteSeleccionado" class="mt-3" style="display:none;"></div>
    </div>
</div>
