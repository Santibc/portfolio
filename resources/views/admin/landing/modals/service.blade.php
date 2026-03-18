<!-- Modal Agregar Servicio -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.landing.services.store') }}" method="POST" enctype="multipart/form-data">
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
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" placeholder="nombre-del-servicio">
                        <small class="form-text text-muted">URL amigable del servicio. Si se deja vacio se genera automaticamente.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción Corta <span class="text-danger">*</span></label>
                        <textarea name="short_description" class="form-control" rows="2" placeholder="Breve descripcion del servicio" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción Larga</label>
                        <textarea name="long_description" class="form-control" rows="5" placeholder="Descripcion detallada del servicio"></textarea>
                        <small class="form-text text-muted">Este campo soporta texto enriquecido (rich text) en futuras versiones.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Descripción del servicio" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen del Servicio</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Texto Alt de la Imagen</label>
                        <input type="text" name="featured_image_alt" class="form-control" placeholder="Texto alternativo para la imagen">
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
            <form id="editServiceForm" action="{{ route('admin.landing.services.update', 0) }}" method="POST" enctype="multipart/form-data">
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
                        <label class="form-label">Slug</label>
                        <input type="text" id="editServiceSlug" name="slug" class="form-control">
                        <small class="form-text text-muted">URL amigable del servicio. Si se deja vacio se genera automaticamente.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción Corta <span class="text-danger">*</span></label>
                        <textarea id="editServiceShortDescription" name="short_description" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción Larga</label>
                        <textarea id="editServiceLongDescription" name="long_description" class="form-control" rows="5"></textarea>
                        <small class="form-text text-muted">Este campo soporta texto enriquecido (rich text) en futuras versiones.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea id="editServiceDescription" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen del Servicio</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Dejar vacio para mantener la imagen actual.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Texto Alt de la Imagen</label>
                        <input type="text" id="editServiceFeaturedImageAlt" name="featured_image_alt" class="form-control" placeholder="Texto alternativo para la imagen">
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