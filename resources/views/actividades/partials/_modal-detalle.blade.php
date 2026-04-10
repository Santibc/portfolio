{{-- Modal: Detalle de actividad --}}
<div class="modal fade" id="modalDetalleActividad" tabindex="-1" aria-labelledby="modalDetalleActividadTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title fw-semibold mb-1" id="modalDetalleActividadTitulo">Detalle de actividad</h5>
                    <div class="small text-muted" id="modalDetalleActividadSubtitulo"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modalDetalleActividadBody">
                {{-- Inyectado por JS --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
