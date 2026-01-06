@extends('layouts.app')

@section('title', $imagen ? 'Editar Imagen' : 'Nueva Imagen')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('carrusel.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-1">{{ $imagen ? 'Editar Imagen' : 'Nueva Imagen' }}</h1>
                    <p class="text-muted mb-0">{{ $imagen ? 'Modifica los datos de la imagen del carrusel' : 'Agrega una nueva imagen al carrusel de tu tienda' }}</p>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ $imagen ? route('carrusel.update', $imagen->id) : route('carrusel.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="card">
                @csrf
                @if($imagen)
                    @method('PUT')
                @endif

                <div class="card-body">
                    {{-- Imagen --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Imagen {{ $imagen ? '' : '*' }}
                        </label>

                        @if($imagen && $imagen->imagen_url)
                            <div class="mb-3">
                                <img src="{{ $imagen->imagen_url }}" alt="Imagen actual"
                                     class="img-thumbnail" style="max-height: 200px;">
                                <p class="text-muted small mt-2 mb-0">Imagen actual. Sube una nueva para reemplazarla.</p>
                            </div>
                        @endif

                        <div class="drop-zone" id="dropZone">
                            <input type="file" name="imagen" id="imagenInput"
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                   class="drop-zone__input @error('imagen') is-invalid @enderror"
                                   {{ $imagen ? '' : 'required' }}>
                            <div class="drop-zone__prompt">
                                <i class="bi bi-cloud-arrow-up display-4 text-muted"></i>
                                <p class="mt-2 mb-1">Arrastra una imagen aquí o haz clic para seleccionar</p>
                                <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB</small>
                            </div>
                            <img src="" alt="Preview" class="drop-zone__preview" style="display: none;">
                        </div>
                        @error('imagen')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <p class="text-muted small mt-2 mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Recomendación: Usa imágenes de 1200x600px para mejor visualización
                        </p>
                    </div>

                    <div class="row">
                        {{-- Título --}}
                        <div class="col-md-6 mb-3">
                            <label for="titulo" class="form-label fw-semibold">Título (opcional)</label>
                            <input type="text" name="titulo" id="titulo"
                                   class="form-control @error('titulo') is-invalid @enderror"
                                   value="{{ old('titulo', $imagen?->titulo) }}"
                                   placeholder="Ej: Promoción de verano">
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Enlace --}}
                        <div class="col-md-6 mb-3">
                            <label for="link" class="form-label fw-semibold">Enlace (opcional)</label>
                            <input type="url" name="link" id="link"
                                   class="form-control @error('link') is-invalid @enderror"
                                   value="{{ old('link', $imagen?->link) }}"
                                   placeholder="https://...">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">URL a donde redirigir al hacer clic en la imagen</small>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold">Descripción (opcional)</label>
                        <textarea name="descripcion" id="descripcion" rows="2"
                                  class="form-control @error('descripcion') is-invalid @enderror"
                                  placeholder="Breve descripción que aparecerá sobre la imagen">{{ old('descripcion', $imagen?->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        {{-- Orden --}}
                        <div class="col-md-4 mb-3">
                            <label for="orden" class="form-label fw-semibold">Orden</label>
                            <input type="number" name="orden" id="orden"
                                   class="form-control @error('orden') is-invalid @enderror"
                                   value="{{ old('orden', $imagen?->orden ?? 0) }}"
                                   min="0">
                            @error('orden')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Menor número = aparece primero</small>
                        </div>

                        {{-- Fecha inicio --}}
                        <div class="col-md-4 mb-3">
                            <label for="fecha_inicio" class="form-label fw-semibold">Fecha inicio (opcional)</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio"
                                   class="form-control @error('fecha_inicio') is-invalid @enderror"
                                   value="{{ old('fecha_inicio', $imagen?->fecha_inicio?->format('Y-m-d')) }}">
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fecha fin --}}
                        <div class="col-md-4 mb-3">
                            <label for="fecha_fin" class="form-label fw-semibold">Fecha fin (opcional)</label>
                            <input type="date" name="fecha_fin" id="fecha_fin"
                                   class="form-control @error('fecha_fin') is-invalid @enderror"
                                   value="{{ old('fecha_fin', $imagen?->fecha_fin?->format('Y-m-d')) }}">
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1"
                                   {{ old('activo', $imagen?->activo ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activo">Imagen activa</label>
                        </div>
                        <small class="text-muted">Las imágenes inactivas no se mostrarán en el carrusel</small>
                    </div>
                </div>

                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('carrusel.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        {{ $imagen ? 'Actualizar imagen' : 'Agregar imagen' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .drop-zone {
        width: 100%;
        min-height: 200px;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
        background: #fafafa;
    }

    .drop-zone:hover {
        border-color: var(--bs-primary);
        background: #f0f7ff;
    }

    .drop-zone--over {
        border-color: var(--bs-primary);
        background: #e8f4ff;
    }

    .drop-zone__input {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    .drop-zone__prompt {
        padding: 20px;
    }

    .drop-zone__preview {
        width: 100%;
        height: 100%;
        object-fit: contain;
        max-height: 300px;
    }

    .drop-zone.has-preview .drop-zone__prompt {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const dropZone = document.getElementById('dropZone');
    const input = document.getElementById('imagenInput');
    const preview = dropZone.querySelector('.drop-zone__preview');

    // Click to select
    dropZone.addEventListener('click', () => input.click());

    // Drag events
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.add('drop-zone--over');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.remove('drop-zone--over');
        });
    });

    // Handle drop
    dropZone.addEventListener('drop', (e) => {
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            updatePreview(input.files[0]);
        }
    });

    // Handle file selection
    input.addEventListener('change', () => {
        if (input.files.length) {
            updatePreview(input.files[0]);
        }
    });

    function updatePreview(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                dropZone.classList.add('has-preview');
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>
@endpush
