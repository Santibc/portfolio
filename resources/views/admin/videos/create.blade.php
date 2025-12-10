@extends('layouts.app')

@section('title', 'Nuevo Video - ' . $curso->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.index') }}">Cursos</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.show', $curso) }}">{{ $curso->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.videos.index', $curso) }}">Videos</a></li>
            <li class="breadcrumb-item active">Nuevo Video</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-upload me-2 text-primary"></i>
                        Subir Nuevo Video
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cursos.videos.store', $curso) }}" method="POST" enctype="multipart/form-data" id="uploadVideoForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Título del Video <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" required maxlength="200"
                                   placeholder="Ej: Introducción al tema">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" maxlength="1000"
                                      placeholder="Describe brevemente el contenido del video...">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Archivo de Video <span class="text-danger">*</span></label>
                            <div class="upload-zone border-2 border-dashed rounded-3 p-5 text-center" id="uploadZone">
                                <input type="file" name="video" id="videoInput"
                                       class="d-none @error('video') is-invalid @enderror"
                                       accept="video/mp4,video/webm,video/ogg,video/quicktime"
                                       required>
                                <div id="uploadPlaceholder">
                                    <i class="bi bi-cloud-arrow-up display-3 text-muted"></i>
                                    <h5 class="mt-3">Arrastra un video aquí</h5>
                                    <p class="text-muted mb-3">o haz clic para seleccionar</p>
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('videoInput').click()">
                                        Seleccionar Video
                                    </button>
                                    <p class="text-muted small mt-3 mb-0">
                                        Formatos: MP4, WebM, OGG, MOV | Máximo: 500MB
                                    </p>
                                </div>
                                <div id="uploadPreview" class="d-none">
                                    <i class="bi bi-file-earmark-play display-4 text-primary"></i>
                                    <h5 class="mt-3" id="fileName"></h5>
                                    <p class="text-muted" id="fileSize"></p>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearVideo()">
                                        <i class="bi bi-x-lg me-1"></i>Cambiar video
                                    </button>
                                </div>
                            </div>
                            @error('video')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Barra de progreso (oculta por defecto) -->
                        <div class="mb-4 d-none" id="progressContainer">
                            <label class="form-label">Subiendo video...</label>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                     role="progressbar" id="uploadProgress" style="width: 0%">0%</div>
                            </div>
                            <small class="text-muted mt-2 d-block" id="uploadStatus">Preparando...</small>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.cursos.videos.index', $curso) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-upload me-2"></i>Subir Video
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const uploadZone = document.getElementById('uploadZone');
const videoInput = document.getElementById('videoInput');
const uploadPlaceholder = document.getElementById('uploadPlaceholder');
const uploadPreview = document.getElementById('uploadPreview');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');

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
        videoInput.files = files;
        showPreview(files[0]);
    }
});

uploadZone.addEventListener('click', (e) => {
    if (e.target === uploadZone || uploadPlaceholder.contains(e.target)) {
        videoInput.click();
    }
});

videoInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        showPreview(e.target.files[0]);
    }
});

function showPreview(file) {
    const maxSize = 500 * 1024 * 1024; // 500MB
    if (file.size > maxSize) {
        Swal.fire('Error', 'El archivo es demasiado grande. Máximo 500MB.', 'error');
        return;
    }

    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    uploadPlaceholder.classList.add('d-none');
    uploadPreview.classList.remove('d-none');
}

function clearVideo() {
    videoInput.value = '';
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
document.getElementById('uploadVideoForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('uploadProgress');
    const uploadStatus = document.getElementById('uploadStatus');
    const submitBtn = document.getElementById('submitBtn');

    progressContainer.classList.remove('d-none');
    submitBtn.disabled = true;
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
                uploadStatus.textContent = 'Video subido exitosamente. Redirigiendo...';
                progressBar.classList.remove('progress-bar-animated');
                progressBar.classList.add('bg-success');
                window.location.href = '{{ route("admin.cursos.videos.index", $curso) }}';
            } else {
                Swal.fire('Error', response.message || 'Error al subir el video', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Subir Video';
                progressContainer.classList.add('d-none');
            }
        } catch {
            // Si no es JSON, probablemente es un error de validación HTML
            Swal.fire('Error', 'Error al subir el video. Verifica que el archivo sea válido.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Subir Video';
            progressContainer.classList.add('d-none');
        }
    });

    xhr.addEventListener('error', function() {
        Swal.fire('Error', 'Error de conexión al subir el video', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Subir Video';
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
</style>
@endpush
@endsection
