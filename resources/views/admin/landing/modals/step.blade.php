<!-- Modal Agregar Paso -->
<div class="modal fade" id="addStepModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Paso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.landing.steps.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Nombre del paso" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Descripción del paso" required></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Nota:</strong> El número del paso se asignará automáticamente según el orden.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Agregar Paso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Paso -->
<div class="modal fade" id="editStepModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Paso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStepForm" action="{{ route('admin.landing.steps.update', 0) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editStepId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" id="editStepTitle" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea id="editStepDescription" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Nota:</strong> El número del paso se mantendrá igual. Para cambiar el orden, elimine y vuelva a crear los pasos.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Actualizar Paso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>