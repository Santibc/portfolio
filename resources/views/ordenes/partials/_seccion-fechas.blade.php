{{-- Seccion 7: Fechas y Notas --}}
<div class="card border-0 shadow-sm mb-4 wizard-section" data-section="7" id="seccionFechas">
    <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-calendar3 me-2 text-primary"></i>7. Fechas y Notas
        </h6>
    </div>
    <div class="card-body px-4 pb-4 pt-2">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-medium">Fecha de Creacion</label>
                <input type="text" class="form-control bg-light" value="{{ now()->timezone('America/Bogota')->format('d/m/Y H:i') }}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Fecha de Entrega <span class="text-danger">*</span></label>
                <input type="date" id="fecha_entrega" class="form-control" min="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Hora de Entrega <span class="text-danger">*</span></label>
                <input type="time" id="hora_entrega" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">Notas / Observaciones Generales</label>
                <textarea id="notas" class="form-control" rows="2" placeholder="Notas adicionales para esta orden..."></textarea>
            </div>
        </div>
    </div>
</div>
