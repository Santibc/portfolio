{{-- Seccion 4: Firma del Cliente --}}
<div class="card border-0 shadow-sm mb-4 wizard-section" data-section="4" id="seccionFirma">
    <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-pen me-2 text-primary"></i>4. Firma del Cliente
        </h6>
    </div>
    <div class="card-body px-4 pb-4 pt-2">
        <div class="border rounded p-2 bg-light text-center">
            <canvas id="firmaCanvas"
                style="border: 1px dashed #ccc; background: white; cursor: crosshair; width: 100%; height: 180px; display: block;"></canvas>
        </div>
        <div class="mt-2 d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarFirma()">
                <i class="bi bi-eraser me-1"></i> Limpiar
            </button>
            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Dibuje la firma del cliente con el mouse o dedo (touch)</small>
        </div>
    </div>
</div>
