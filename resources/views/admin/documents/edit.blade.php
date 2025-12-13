@extends('layouts.app')

@section('title', 'Editar Documento - ' . $document->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.index') }}">Cursos</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.show', $curso) }}">{{ $curso->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.documents.index', $curso) }}">Documentos</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil me-2 text-primary"></i>
                        Editar Documento
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cursos.documents.update', [$curso, $document]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label">Título del Documento <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $document->title) }}" required maxlength="255">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Descripción</label>
                            <div id="quillEditor" style="height: 150px;"></div>
                            <input type="hidden" name="description" id="descriptionInput">
                            <div id="initialDescription" class="d-none">{!! old('description', $document->description) !!}</div>
                            @error('description')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Documento actual -->
                        <div class="mb-4">
                            <label class="form-label">Documento Actual</label>
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="bi {{ $document->file_icon }} fs-1 {{ $document->file_icon_color }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $document->file_name }}</h6>
                                        <small class="text-muted">
                                            {{ strtoupper($document->file_type) }} - {{ $document->formatted_file_size }}
                                        </small>
                                    </div>
                                    <a href="{{ route('admin.cursos.documents.download', [$curso, $document]) }}"
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-download me-1"></i>Descargar
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Reemplazar documento -->
                        <div class="mb-4">
                            <label class="form-label">Reemplazar Documento <small class="text-muted">(opcional)</small></label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                            <small class="text-muted">Selecciona un nuevo archivo solo si deseas reemplazar el documento actual. Máximo 50MB.</small>
                            @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info -->
                        <div class="mb-4">
                            <label class="form-label">Información</label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h4 class="text-info mb-1">{{ $document->order ?? 1 }}</h4>
                                        <small class="text-muted">Posición</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h4 class="text-primary mb-1">{{ $document->formatted_file_size }}</h4>
                                        <small class="text-muted">Tamaño</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h4 class="text-success mb-1">{{ $document->created_at->format('d/m/Y') }}</h4>
                                        <small class="text-muted">Creado</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.cursos.documents.index', $curso) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
#quillEditor {
    background: #fff;
    border-radius: 0 0 0.375rem 0.375rem;
}
.ql-toolbar.ql-snow {
    border-radius: 0.375rem 0.375rem 0 0;
    border-color: #dee2e6;
}
.ql-container.ql-snow {
    border-color: #dee2e6;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
// Inicializar Quill Editor
const quill = new Quill('#quillEditor', {
    theme: 'snow',
    placeholder: 'Describe brevemente el contenido del documento...',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link'],
            ['clean']
        ]
    }
});

// Cargar contenido existente desde el div oculto
const initialContent = document.getElementById('initialDescription').innerHTML.trim();
if (initialContent) {
    quill.root.innerHTML = initialContent;
}

// Función para sincronizar contenido de Quill al input hidden
function syncQuillContent() {
    const descriptionContent = quill.root.innerHTML;
    const cleanContent = descriptionContent === '<p><br></p>' ? '' : descriptionContent;
    document.getElementById('descriptionInput').value = cleanContent;
}

// Sincronizar en cada cambio del editor
quill.on('text-change', syncQuillContent);

// Sincronizar contenido inicial
syncQuillContent();

// También capturar antes del submit por seguridad
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
    syncQuillContent();
});
</script>
@endpush
@endsection
