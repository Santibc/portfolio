<style>
    .item-readonly {
        background-color: #f0f0f0 !important;
        cursor: not-allowed;
        color: #555;
    }
</style>
{{-- Seccion 2: Items --}}
<div class="card border-0 shadow-sm mb-4 wizard-section" data-section="3" id="seccionItems">
    <div class="card-header bg-white border-0 px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-cart3 me-2 text-primary"></i>3. Items (Productos y servicios)
        </h6>
        <button type="button" class="btn btn-sm btn-primary" onclick="agregarFilaItem()">
            <i class="bi bi-plus-lg me-1"></i> Agregar Item
        </button>
    </div>
    <div class="card-body px-4 pb-4 pt-2">
        <div class="table-responsive" id="itemsTableWrapper" style="overflow:visible;">
            <table class="table table-bordered align-middle mb-0" id="tablaItems">
                <thead>
                    <tr class="table-light">
                        <th style="width:60px" class="text-center">#</th>
                        <th style="width:180px">Codigo</th>
                        <th>Descripcion</th>
                        <th style="width:100px" class="text-center">Cantidad</th>
                        <th style="width:140px" class="text-end">P. Unitario</th>
                        <th style="width:70px" class="text-center">IVA</th>
                        <th style="width:140px" class="text-end">Subtotal</th>
                        <th style="width:50px"></th>
                    </tr>
                </thead>
                <tbody id="tbodyItems">
                    {{-- Filas dinamicas via JS --}}
                </tbody>
            </table>
        </div>

        <div id="itemsVacio" class="text-center py-4 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
            <p class="mb-0">No hay items agregados. Haga clic en <strong>"Agregar Item"</strong> para comenzar.</p>
        </div>

        {{-- Panel de Totales --}}
        <div class="row mt-3 justify-content-end" id="panelTotales" style="display:none;">
            <div class="col-md-5 col-lg-4">
                <div class="bg-light rounded p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <strong id="totalSubtotal">$0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">IVA:</span>
                        <strong id="totalIva">$0</strong>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="fs-5 fw-bold">TOTAL:</span>
                        <span class="fs-5 fw-bold text-primary" id="totalGeneral">$0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
