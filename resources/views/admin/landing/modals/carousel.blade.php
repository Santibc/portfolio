<!-- Modal Agregar Imagen Carrusel -->
<div class="modal fade" id="addCarouselModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Imagen al Carrusel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.landing.carousel.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Imagen <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Texto Alternativo</label>
                        <input type="text" name="alt_text" class="form-control" placeholder="Descripción de la imagen">
                        <small class="form-text text-muted">Opcional. Texto que se muestra si la imagen no carga.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Subir Imagen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>