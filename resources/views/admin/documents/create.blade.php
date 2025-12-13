@extends('layouts.app')

@section('title', 'Nuevo Documento - ' . $curso->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.index') }}">Cursos</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.show', $curso) }}">{{ $curso->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.documents.index', $curso) }}">Documentos</a></li>
            <li class="breadcrumb-item active">Nuevo Documento</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-upload me-2 text-primary"></i>
                        Subir Nuevo Documento
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cursos.documents.store', $curso) }}" method="POST" enctype="multipart/form-data" id="uploadDocumentForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Título del Documento <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" required maxlength="255"
                                   placeholder="Ej: Manual de Usuario">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Descripción</label>
                            <div id="quillEditor" style="height: 150px;"></div>
                            <input type="hidden" name="description" id="descriptionInput">
                            <div id="initialDescription" class="d-none">{!! old('description') !!}</div>
                            @error('description')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Archivo <span class="text-danger">*</span></label>
                            <div class="upload-zone border-2 border-dashed rounded-3 p-5 text-center" id="uploadZone">
                                <input type="file" name="file" id="fileInput"
                                       class="d-none @error('file') is-invalid @enderror"
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required>
                                <div id="uploadPlaceholder">
                                    <i class="bi bi-cloud-arrow-up display-3 text-muted"></i>
                                    <h5 class="mt-3">Arrastra un documento aquí</h5>
                                    <p class="text-muted mb-3">o haz clic para seleccionar</p>
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                                        Seleccionar Archivo
                                    </button>
                                    <p class="text-muted small mt-3 mb-0">
                                        Formatos: PDF, Word, Excel, PowerPoint | Máximo: 50MB
                                    </p>
                                </div>
                                <div id="uploadPreview" class="d-none">
                                    <i class="bi bi-file-earmark-text display-4 text-primary" id="fileIcon"></i>
                                    <h5 class="mt-3" id="fileName"></h5>
                                    <p class="text-muted" id="fileSize"></p>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearFile()">
                                        <i class="bi bi-x-lg me-1"></i>Cambiar archivo
                                    </button>
                                </div>
                            </div>
                            @error('file')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Barra de progreso (oculta por defecto) -->
                        <div class="mb-4 d-none" id="progressContainer">
                            <label class="form-label">Subiendo documento...</label>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                     role="progressbar" id="uploadProgress" style="width: 0%">0%</div>
                            </div>
                            <small class="text-muted mt-2 d-block" id="uploadStatus">Preparando...</small>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.cursos.documents.index', $curso) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-upload me-2"></i>Subir Documento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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

// Cargar contenido existente si hay old input (desde el div oculto)
const initialContent = document.getElementById('initialDescription').innerHTML;
if (initialContent) {
    quill.root.innerHTML = initialContent;
}

// Sincronizar Quill en tiempo real
quill.on('text-change', function() {
    const content = quill.root.innerHTML;
    document.getElementById('descriptionInput').value = content === '<p><br></p>' ? '' : content;
});

const uploadZone = document.getElementById('uploadZone');
const fileInput = document.getElementById('fileInput');
const uploadPlaceholder = document.getElementById('uploadPlaceholder');
const uploadPreview = document.getElementById('uploadPreview');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');
const fileIcon = document.getElementById('fileIcon');

// Drag and drop
uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
});

uploadZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
});

uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');

    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        showPreview(files[0]);
    }
});

uploadZone.addEventListener('click', (e) => {
    if (e.target === uploadZone || uploadPlaceholder.contains(e.target)) {
        fileInput.click();
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        showPreview(e.target.files[0]);
    }
});

function showPreview(file) {
    const maxSize = 50 * 1024 * 1024; // 50MB
    if (file.size > maxSize) {
        Swal.fire('Error', 'El archivo es demasiado grande. Máximo 50MB.', 'error');
        return;
    }

    const ext = file.name.split('.').pop().toLowerCase();
    const iconMap = {
        'pdf': 'bi-file-earmark-pdf text-danger',
        'doc': 'bi-file-earmark-word text-primary',
        'docx': 'bi-file-earmark-word text-primary',
        'xls': 'bi-file-earmark-excel text-success',
        'xlsx': 'bi-file-earmark-excel text-success',
        'ppt': 'bi-file-earmark-ppt text-warning',
        'pptx': 'bi-file-earmark-ppt text-warning'
    };

    fileIcon.className = 'bi display-4 ' + (iconMap[ext] || 'bi-file-earmark-text text-primary');
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    uploadPlaceholder.classList.add('d-none');
    uploadPreview.classList.remove('d-none');
}

function clearFile() {
    fileInput.value = '';
    uploadPlaceholder.classList.remove('d-none');
    uploadPreview.classList.add('d-none');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Submit con progreso
document.getElementById('uploadDocumentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Capturar contenido de Quill
    const descriptionContent = quill.root.innerHTML;
    document.getElementById('descriptionInput').value = descriptionContent === '<p><br></p>' ? '' : descriptionContent;

    const formData = new FormData(this);
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('uploadProgress');
    const uploadStatus = document.getElementById('uploadStatus');
    const submitBtn = document.getElementById('submitBtn');

    submitBtn.disabled = true;
    progressContainer.classList.remove('d-none');
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Subiendo...';

    const xhr = new XMLHttpRequest();

    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressBar.textContent = percent + '%';
            uploadStatus.textContent = `Subiendo... ${formatFileSize(e.loaded)} de ${formatFileSize(e.total)}`;
        }
    });

    xhr.addEventListener('load', function() {
        try {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                uploadStatus.textContent = 'Documento subido exitosamente. Redirigiendo...';
                progressBar.classList.remove('progress-bar-animated');
                progressBar.classList.add('bg-success');
                window.location.href = '{{ route("admin.cursos.documents.index", $curso) }}';
            } else {
                Swal.fire('Error', response.message || 'Error al subir el documento', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Subir Documento';
                progressContainer.classList.add('d-none');
            }
        } catch {
            Swal.fire('Error', 'Error al procesar la respuesta. Verifica los datos.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Subir Documento';
            progressContainer.classList.add('d-none');
        }
    });

    xhr.addEventListener('error', function() {
        Swal.fire('Error', 'Error de conexión', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Subir Documento';
        progressContainer.classList.add('d-none');
    });

    xhr.open('POST', this.action);
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.send(formData);
});
</script>
@endpush

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
.upload-zone {
    cursor: pointer;
    transition: all 0.3s ease;
    border-color: #dee2e6;
}
.upload-zone:hover {
    border-color: var(--gva-primary);
    background-color: rgba(var(--gva-primary-rgb), 0.05);
}
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
@endsection
