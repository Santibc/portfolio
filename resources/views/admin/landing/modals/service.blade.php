<!-- Modal Agregar Servicio -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.landing.services.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Clase del Icono <span class="text-danger">*</span></label>
                        <input type="text" name="icon_class" class="form-control" placeholder="bi bi-currency-dollar" required>
                        <div class="form-text">
                            <strong>¿Cómo encontrar iconos?</strong><br>
                            1. Visita <a href="https://icons.getbootstrap.com/" target="_blank" class="text-primary">Bootstrap Icons</a><br>
                            2. Busca el icono que necesitas<br>
                            3. Haz clic en el icono que te guste<br>
                            4. Copia el nombre de la clase (ejemplo: "bi-house")<br>
                            5. Agrega "bi " al inicio (ejemplo: "bi bi-house")<br><br>
                            
                            <strong>Ejemplos:</strong><br>
                            <span class="badge bg-secondary me-1">bi bi-currency-dollar</span>
                            <span class="badge bg-secondary me-1">bi bi-shield-check</span>
                            <span class="badge bg-secondary me-1">bi bi-building</span>
                            <span class="badge bg-secondary">bi bi-globe-americas</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Nombre del servicio" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Descripción del servicio" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Agregar Servicio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Servicio -->
<div class="modal fade" id="editServiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editServiceForm" action="{{ route('admin.landing.services.update', 0) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editServiceId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Clase del Icono <span class="text-danger">*</span></label>
                        <input type="text" id="editServiceIconClass" name="icon_class" class="form-control" required>
                        <div class="form-text">
                            <strong>¿Cómo encontrar iconos?</strong><br>
                            1. Visita <a href="https://icons.getbootstrap.com/" target="_blank" class="text-primary">Bootstrap Icons</a><br>
                            2. Busca el icono que necesitas<br>
                            3. Haz clic en el icono que te guste<br>
                            4. Copia el nombre de la clase (ejemplo: "bi-house")<br>
                            5. Agrega "bi " al inicio (ejemplo: "bi bi-house")<br><br>
                            
                            <strong>Ejemplos:</strong><br>
                            <span class="badge bg-secondary me-1">bi bi-currency-dollar</span>
                            <span class="badge bg-secondary me-1">bi bi-shield-check</span>
                            <span class="badge bg-secondary me-1">bi bi-building</span>
                            <span class="badge bg-secondary">bi bi-globe-americas</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" id="editServiceTitle" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea id="editServiceDescription" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Actualizar Servicio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>