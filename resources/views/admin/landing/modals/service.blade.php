<!-- Modal Agregar Servicio -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.landing.services.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#add-basics" type="button">Basic Info</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#add-content" type="button">Full Content</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#add-seo" type="button">SEO</button></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="add-basics">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Título <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. NDIS Cleaning" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Slug (URL)</label>
                                    <input type="text" name="slug" class="form-control" placeholder="ndis-cleaning (auto if empty)">
                                    <div class="form-text">Se usará en /services/{slug}. Si lo dejas vacío se genera del título.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Clase del Icono <span class="text-danger">*</span></label>
                                    <input type="text" name="icon_class" class="form-control" placeholder="bi bi-house" required>
                                    <div class="form-text">
                                        Usa <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>. Formato: "bi bi-nombre".
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subtítulo</label>
                                    <input type="text" name="subtitle" class="form-control" placeholder="One-liner shown under the title">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción corta (tarjeta / listado)</label>
                                    <textarea name="short_description" class="form-control" rows="2" maxlength="300" placeholder="Descripción breve para mostrar en la tarjeta de servicios y en /servicios"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción (fallback) <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="3" required placeholder="Descripción del servicio (se usa si no hay contenido largo)"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Imagen hero</label>
                                    <input type="file" name="hero_image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                                    <div class="form-text">Recomendado: 1200×600px. Se muestra en la cabecera del detalle.</div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_published" value="0">
                                        <input type="checkbox" name="is_published" value="1" class="form-check-input" id="add_is_published" checked>
                                        <label class="form-check-label" for="add_is_published">Publicado (visible en el sitio)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="add-content">
                            <label class="form-label">Contenido completo de la página (HTML permitido)</label>
                            <textarea name="content_html" class="form-control" rows="14" placeholder="&lt;h2&gt;What's included&lt;/h2&gt;&#10;&lt;ul&gt;&lt;li&gt;...&lt;/li&gt;&lt;/ul&gt;&#10;&lt;h2&gt;Why choose us&lt;/h2&gt;&#10;..."></textarea>
                            <div class="form-text">Puedes usar HTML: h2, h3, p, ul, ol, strong, a. Este contenido se muestra en /services/{slug}.</div>
                        </div>
                        <div class="tab-pane fade" id="add-seo">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Meta Title</label>
                                    <input type="text" name="meta_title" class="form-control" maxlength="150" placeholder="NDIS Cleaning Adelaide | Clean Me Adelaide">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Focus Keyword</label>
                                    <input type="text" name="focus_keyword" class="form-control" maxlength="100" placeholder="ndis cleaning adelaide">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Meta Description</label>
                                    <textarea name="meta_description" class="form-control" rows="3" placeholder="Reliable NDIS cleaning services in Adelaide..."></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Meta Keywords</label>
                                    <input type="text" name="meta_keywords" class="form-control" maxlength="500" placeholder="ndis cleaning, disability cleaning, adelaide">
                                </div>
                            </div>
                        </div>
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
    <div class="modal-dialog modal-xl">
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
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#edit-basics" type="button">Basic Info</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#edit-content" type="button">Full Content</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#edit-seo" type="button">SEO</button></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="edit-basics">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Título <span class="text-danger">*</span></label>
                                    <input type="text" id="editServiceTitle" name="title" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Slug (URL)</label>
                                    <input type="text" id="editServiceSlug" name="slug" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Clase del Icono <span class="text-danger">*</span></label>
                                    <input type="text" id="editServiceIconClass" name="icon_class" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subtítulo</label>
                                    <input type="text" id="editServiceSubtitle" name="subtitle" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción corta</label>
                                    <textarea id="editServiceShortDescription" name="short_description" class="form-control" rows="2" maxlength="300"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción (fallback) <span class="text-danger">*</span></label>
                                    <textarea id="editServiceDescription" name="description" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Imagen hero (dejar vacío para conservar)</label>
                                    <input type="file" name="hero_image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                                    <div class="form-text" id="editServiceHeroPreview"></div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_published" value="0">
                                        <input type="checkbox" name="is_published" value="1" class="form-check-input" id="edit_is_published">
                                        <label class="form-check-label" for="edit_is_published">Publicado</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="edit-content">
                            <label class="form-label">Contenido completo (HTML permitido)</label>
                            <textarea id="editServiceContentHtml" name="content_html" class="form-control" rows="14"></textarea>
                        </div>
                        <div class="tab-pane fade" id="edit-seo">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Meta Title</label>
                                    <input type="text" id="editServiceMetaTitle" name="meta_title" class="form-control" maxlength="150">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Focus Keyword</label>
                                    <input type="text" id="editServiceFocusKeyword" name="focus_keyword" class="form-control" maxlength="100">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Meta Description</label>
                                    <textarea id="editServiceMetaDescription" name="meta_description" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Meta Keywords</label>
                                    <input type="text" id="editServiceMetaKeywords" name="meta_keywords" class="form-control" maxlength="500">
                                </div>
                            </div>
                        </div>
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
