@extends('layouts.app')

@section('title', 'Gestión de Categorías')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de Categorías</h1>
            <p class="text-muted mb-0">Organiza los cursos por categorías</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="bi bi-plus-lg me-2"></i>Nueva Categoría
        </button>
    </div>

    <!-- Lista de Categorías -->
    <div class="row g-4" id="categoriesContainer">
        @forelse($categorias ?? [] as $categoria)
        <div class="col-xl-4 col-md-6" data-category-id="{{ $categoria->id }}">
            <div class="card border-0 shadow-sm h-100 category-card">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <div class="drag-handle me-2 text-muted" style="cursor: grab;">
                                <i class="bi bi-grip-vertical"></i>
                            </div>
                            <span class="badge {{ $categoria->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $categoria->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button class="dropdown-item" onclick="editCategory('{{ $categoria->slug }}', {{ $categoria->id }})">
                                        <i class="bi bi-pencil me-2"></i>Editar
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item" onclick="toggleCategory('{{ $categoria->slug }}')">
                                        <i class="bi bi-toggle-on me-2"></i>
                                        {{ $categoria->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger" onclick="deleteCategory('{{ $categoria->slug }}', '{{ $categoria->name }}')">
                                        <i class="bi bi-trash me-2"></i>Eliminar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body text-center">
                    @if($categoria->image)
                    <img src="{{ asset($categoria->image) }}"
                         alt="{{ $categoria->name }}"
                         class="rounded-3 mb-3"
                         style="width: 100%; height: 120px; object-fit: cover;">
                    @else
                    <div class="bg-light rounded-3 mb-3 d-flex align-items-center justify-content-center"
                         style="height: 120px;">
                        <i class="bi bi-folder2-open display-4 text-muted"></i>
                    </div>
                    @endif
                    <h5 class="card-title">{{ $categoria->name }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($categoria->description, 80) }}</p>
                </div>
                <div class="card-footer bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="bi bi-collection-play me-1"></i>
                            {{ $categoria->courses_count ?? 0 }} cursos
                        </div>
                        <a href="{{ route('admin.cursos.index', ['categoria' => $categoria->id]) }}"
                           class="btn btn-sm btn-outline-primary">
                            Ver cursos
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-folder-x display-1 text-muted"></i>
                    <h5 class="mt-3">No hay categorías</h5>
                    <p class="text-muted">Crea tu primera categoría para organizar los cursos</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                        <i class="bi bi-plus-lg me-2"></i>Nueva Categoría
                    </button>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Crear Categoría -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.categorias.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <div id="createQuillEditor" style="height: 100px;"></div>
                        <input type="hidden" name="description" id="createDescriptionInput">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="createIsActive" checked>
                            <label class="form-check-label" for="createIsActive">Categoría activa</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Categoría -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="editCategoryForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="modal-title">Editar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editCategoryName" class="form-control" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <div id="editQuillEditor" style="height: 100px;"></div>
                        <input type="hidden" name="description" id="editDescriptionInput">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen actual</label>
                        <div id="currentCategoryImage" class="mb-2"></div>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Selecciona una nueva imagen para reemplazar la actual</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editCategoryIsActive">
                            <label class="form-check-label" for="editCategoryIsActive">Categoría activa</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form ocultos para acciones -->
<form id="toggleCategoryForm" method="POST" class="d-none">
    @csrf
    @method('PATCH')
</form>

<form id="deleteCategoryForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
.ql-editor {
    min-height: 60px;
}
.ql-toolbar.ql-snow {
    border-radius: 0.375rem 0.375rem 0 0;
    border-color: #dee2e6;
}
.ql-container.ql-snow {
    border-radius: 0 0 0.375rem 0.375rem;
    border-color: #dee2e6;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
// Configuración común de Quill
const quillConfig = {
    theme: 'snow',
    placeholder: 'Describe la categoría...',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link'],
            ['clean']
        ]
    }
};

// Inicializar Quill para el modal de crear
const createQuill = new Quill('#createQuillEditor', quillConfig);

// Inicializar Quill para el modal de editar
const editQuill = new Quill('#editQuillEditor', quillConfig);

// Funciones para sincronizar contenido de Quill
function syncCreateQuill() {
    const content = createQuill.root.innerHTML;
    document.getElementById('createDescriptionInput').value = content === '<p><br></p>' ? '' : content;
}

function syncEditQuill() {
    const content = editQuill.root.innerHTML;
    document.getElementById('editDescriptionInput').value = content === '<p><br></p>' ? '' : content;
}

// Sincronizar en cada cambio del editor
createQuill.on('text-change', syncCreateQuill);
editQuill.on('text-change', syncEditQuill);

// Capturar contenido de Quill antes de enviar formulario de crear
document.querySelector('#createCategoryModal form').addEventListener('submit', function(e) {
    syncCreateQuill();
});

// Capturar contenido de Quill antes de enviar formulario de editar
document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
    syncEditQuill();
});

// Limpiar editor de crear cuando se cierra el modal
document.getElementById('createCategoryModal').addEventListener('hidden.bs.modal', function() {
    createQuill.root.innerHTML = '';
    document.getElementById('createDescriptionInput').value = '';
});

// Sortable para reordenar categorías
const container = document.getElementById('categoriesContainer');
if (container) {
    new Sortable(container, {
        animation: 150,
        handle: '.drag-handle',
        onEnd: function(evt) {
            const order = [];
            container.querySelectorAll('[data-category-id]').forEach((el, index) => {
                order.push({
                    id: el.dataset.categoryId,
                    order: index + 1
                });
            });

            fetch('{{ route("admin.categorias.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      // Mostrar notificación sutil
                  }
              });
        }
    });
}

function editCategory(categorySlug, categoryId) {
    // Usar la ruta show que devuelve JSON (usa slug por el getRouteKeyName del modelo)
    fetch(`/admin/categorias/${categorySlug}`, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar los datos');
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('editCategoryName').value = data.name;
            editQuill.root.innerHTML = data.description || '';
            document.getElementById('editCategoryIsActive').checked = data.is_active;

            const imageContainer = document.getElementById('currentCategoryImage');
            if (data.image) {
                imageContainer.innerHTML = `<img src="${data.image_url}" class="rounded" style="max-height: 100px;">`;
            } else {
                imageContainer.innerHTML = '<span class="text-muted">Sin imagen</span>';
            }

            // Usar el slug para la acción del form (consistente con el route model binding)
            document.getElementById('editCategoryForm').action = `/admin/categorias/${categorySlug}`;
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo cargar la categoría', 'error');
        });
}

function toggleCategory(categorySlug) {
    const form = document.getElementById('toggleCategoryForm');
    form.action = `/admin/categorias/${categorySlug}/toggle`;
    form.submit();
}

function deleteCategory(categorySlug, categoryName) {
    Swal.fire({
        title: '¿Eliminar categoría?',
        text: `¿Estás seguro de eliminar "${categoryName}"? Los cursos asociados quedarán sin categoría.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteCategoryForm');
            form.action = `/admin/categorias/${categorySlug}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
