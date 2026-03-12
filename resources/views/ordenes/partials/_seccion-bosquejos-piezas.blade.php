{{-- Seccion 3: Piezas --}}
<div class="card border-0 shadow-sm mb-4 wizard-section" data-section="2" id="seccionBosquejosPiezas">
    <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-puzzle me-2 text-primary"></i>2. Piezas
        </h6>
    </div>
    <div class="card-body px-4 pb-4 pt-2">

        {{-- Header con botones --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="mb-0 fw-semibold text-secondary">
                <i class="bi bi-puzzle me-1"></i> Piezas
            </h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalBosquejoMatriz">
                    <i class="bi bi-grid-3x3 me-1"></i> Importar Matriz
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="agregarFilaPieza()">
                    <i class="bi bi-plus-lg me-1"></i> Agregar Pieza
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0" id="tablaPiezas" style="display:none; min-width:900px;">
                <thead>
                    <tr class="table-light">
                        <th style="width:100px" class="text-center">Bosquejo</th>
                        <th style="width:40px" class="text-center">#</th>
                        <th style="width:120px">Identificador</th>
                        <th style="width:70px" class="text-center">Cantidad</th>
                        <th style="width:140px">Material</th>
                        <th style="width:90px">Calibre</th>
                        <th style="width:150px">Especificacion</th>
                        <th>Notas</th>
                        <th style="width:60px" class="text-center">Operario</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="tbodyPiezas">
                    {{-- Filas dinamicas via JS --}}
                </tbody>
            </table>
        </div>

        <div id="piezasVacio" class="text-center py-3 text-muted">
            <i class="bi bi-puzzle fs-3 d-block mb-1 opacity-50"></i>
            <small>No hay piezas. Si esta orden requiere trabajo sobre piezas, agregue al menos una.</small>
            <br><small class="text-info"><i class="bi bi-info-circle me-1"></i>Sin piezas = Venta directa (se marca como ejecutada al generar)</small>
        </div>

        {{-- Divs ocultos para compatibilidad con JS residual --}}
        <div id="bosquejosGrid" style="display:none;"></div>
        <div id="bosquejosVacio" style="display:none;"></div>

    </div>
</div>
