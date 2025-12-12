@extends('layouts.app')

@section('title', 'Gestión de Cursos')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de Cursos</h1>
            <p class="text-muted mb-0">Administra los cursos de la plataforma</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Curso
        </button>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.cursos.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Título del curso..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" class="form-select">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias ?? [] as $categoria)
                        <option value="{{ $categoria->id }}" {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publicados</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Borradores</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Cursos -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 40px;">
                                <i class="bi bi-grip-vertical text-muted"></i>
                            </th>
                            <th>Curso</th>
                            <th>Categoría</th>
                            <th>Videos</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="coursesTableBody">
                        @forelse($cursos ?? [] as $curso)
                        <tr data-course-id="{{ $curso->id }}">
                            <td class="ps-4">
                                <span class="drag-handle" style="cursor: grab;">
                                    <i class="bi bi-grip-vertical text-muted"></i>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($curso->thumbnail)
                                    <img src="{{ asset('storage/' . $curso->thumbnail) }}"
                                         alt="{{ $curso->title }}"
                                         class="rounded me-3"
                                         style="width: 64px; height: 40px; object-fit: cover;">
                                    @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                         style="width: 64px; height: 40px;">
                                        <i class="bi bi-collection-play text-muted"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $curso->title }}</h6>
                                        <small class="text-muted">{{ Str::limit($curso->description, 50) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $curso->category->name ?? 'Sin categoría' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info">
                                    {{ $curso->videos_count ?? 0 }} videos
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $curso->is_published ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                                    {{ $curso->is_published ? 'Publicado' : 'Borrador' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('admin.cursos.show', $curso) }}"
                                       class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="editCourse('{{ $curso->slug }}')" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="{{ route('admin.cursos.videos.index', $curso) }}"
                                       class="btn btn-sm btn-outline-info" title="Gestionar videos">
                                        <i class="bi bi-play-btn"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm {{ $curso->is_published ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            onclick="toggleCourse('{{ $curso->slug }}')"
                                            title="{{ $curso->is_published ? 'Despublicar' : 'Publicar' }}">
                                        <i class="bi {{ $curso->is_published ? 'bi-eye-slash' : 'bi-check-lg' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteCourse('{{ $curso->slug }}', '{{ $curso->title }}')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-collection-play display-4 text-muted d-block mb-3"></i>
                                <h5>No hay cursos</h5>
                                <p class="text-muted">Crea tu primer curso para comenzar</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                                    <i class="bi bi-plus-lg me-2"></i>Nuevo Curso
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @if(
        isset($cursos) && 
        is_object($cursos) && 
        method_exists($cursos, 'hasPages') && 
        $cursos->hasPages()
    )
        <div class="card-footer bg-transparent border-0">
            {{ $cursos->links() }}
        </div>
    @endif
    </div>
</div>

<!-- Modal Crear Curso -->
<div class="modal fade" id="createCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.cursos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title">Nuevo Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required maxlength="200">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                @foreach($categorias ?? [] as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <div id="createQuillEditor" style="height: 120px;"></div>
                            <input type="hidden" name="description" id="createDescriptionInput">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thumbnail</label>
                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                            <small class="text-muted">Recomendado: 640x360 px</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_published" id="createIsPublished">
                                <label class="form-check-label" for="createIsPublished">Publicar inmediatamente</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Curso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Curso -->
<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST" id="editCourseForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="modal-title">Editar Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="editCourseTitle" class="form-control" required maxlength="200">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select name="category_id" id="editCourseCategoryId" class="form-select" required>
                                @foreach($categorias ?? [] as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <div id="editQuillEditor" style="height: 120px;"></div>
                            <input type="hidden" name="description" id="editDescriptionInput">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thumbnail actual</label>
                            <div id="currentCourseThumbnail" class="mb-2"></div>
                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_published" id="editCourseIsPublished">
                                <label class="form-check-label" for="editCourseIsPublished">Curso publicado</label>
                            </div>
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

<!-- Forms ocultos -->
<form id="toggleCourseForm" method="POST" class="d-none">
    @csrf
    @method('PATCH')
</form>

<form id="deleteCourseForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
.ql-editor {
    min-height: 80px;
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
    placeholder: 'Describe el contenido del curso...',
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
document.querySelector('#createCourseModal form').addEventListener('submit', function(e) {
    syncCreateQuill();
});

// Capturar contenido de Quill antes de enviar formulario de editar
document.getElementById('editCourseForm').addEventListener('submit', function(e) {
    syncEditQuill();
});

// Limpiar editor de crear cuando se cierra el modal
document.getElementById('createCourseModal').addEventListener('hidden.bs.modal', function() {
    createQuill.root.innerHTML = '';
    document.getElementById('createDescriptionInput').value = '';
});

// Sortable para reordenar cursos
const tableBody = document.getElementById('coursesTableBody');
if (tableBody && tableBody.children.length > 1) {
    new Sortable(tableBody, {
        animation: 150,
        handle: '.drag-handle',
        onEnd: function(evt) {
            const order = [];
            tableBody.querySelectorAll('[data-course-id]').forEach((el, index) => {
                order.push({
                    id: el.dataset.courseId,
                    order: index + 1
                });
            });

            fetch('{{ route("admin.cursos.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            });
        }
    });
}

function editCourse(courseSlug) {
    // Usar la ruta show que devuelve JSON (usa slug por el getRouteKeyName del modelo)
    fetch(`/admin/cursos/${courseSlug}`, {
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
            document.getElementById('editCourseTitle').value = data.title;
            editQuill.root.innerHTML = data.description || '';
            document.getElementById('editCourseCategoryId').value = data.category_id;
            document.getElementById('editCourseIsPublished').checked = data.is_published;

            const thumbContainer = document.getElementById('currentCourseThumbnail');
            if (data.thumbnail) {
                thumbContainer.innerHTML = `<img src="${data.thumbnail_url}" class="rounded" style="max-height: 80px;">`;
            } else {
                thumbContainer.innerHTML = '<span class="text-muted">Sin thumbnail</span>';
            }

            // Usar el slug para la acción del form
            document.getElementById('editCourseForm').action = `/admin/cursos/${courseSlug}`;
            new bootstrap.Modal(document.getElementById('editCourseModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo cargar el curso', 'error');
        });
}

function toggleCourse(courseSlug) {
    const form = document.getElementById('toggleCourseForm');
    form.action = `/admin/cursos/${courseSlug}/toggle`;
    form.submit();
}

function deleteCourse(courseSlug, courseTitle) {
    Swal.fire({
        title: '¿Eliminar curso?',
        text: `¿Estás seguro de eliminar "${courseTitle}"? Se eliminarán también todos los videos asociados.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteCourseForm');
            form.action = `/admin/cursos/${courseSlug}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
