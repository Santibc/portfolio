{{-- Seccion 6: Pagos / Abonos --}}
<div class="card border-0 shadow-sm mb-4 wizard-section" data-section="6" id="seccionPagos">
    <div class="card-header bg-white border-0 px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-cash-coin me-2 text-primary"></i>6. Pagos / Abonos
        </h6>
        <button type="button" class="btn btn-sm btn-primary" onclick="agregarFilaPago()">
            <i class="bi bi-plus-lg me-1"></i> Agregar Pago
        </button>
    </div>
    <div class="card-body px-4 pb-4 pt-2">
        <div id="pagosContainer">
            {{-- Filas dinamicas de pagos via JS --}}
        </div>

        <div id="pagosVacio" class="text-center py-3 text-muted">
            <i class="bi bi-cash-stack fs-3 d-block mb-1 opacity-50"></i>
            <small>No hay pagos registrados. Puede agregar abonos opcionalmente.</small>
        </div>

        {{-- Resumen de Saldo --}}
        <div class="row mt-3 justify-content-end" id="panelSaldo" style="display:none;">
            <div class="col-md-5 col-lg-4">
                <div class="bg-light rounded p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Orden:</span>
                        <strong id="pagoTotalOrden">$0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Abonado:</span>
                        <strong id="pagoTotalAbonado" class="text-success">$0</strong>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">SALDO:</span>
                        <span class="fw-bold text-danger" id="pagoSaldo">$0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
