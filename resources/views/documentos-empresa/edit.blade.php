@extends('layouts.app')
@section('title', 'Editar Documento')

@section('content')
<div class="container-fluid py-4">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('documentos-empresa.index') }}">Documentos de Empresa</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('documentos-empresa.show', $documento) }}">{{ Str::limit($documento->nombre, 20) }}</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Editar Documento</h1>
        </div>
        <a href="{{ route('documentos-empresa.show', $documento) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    {{-- ERRORES --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FORMULARIO --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('documentos-empresa.update', $documento) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    {{-- INFORMACIÓN BÁSICA --}}
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-info-circle me-2"></i>Información del Documento
                        </h5>
                    </div>

                    <div class="col-md-8">
                        <label for="nombre" class="form-label">Nombre del Documento <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('nombre') is-invalid @enderror"
                               id="nombre"
                               name="nombre"
                               value="{{ old('nombre', $documento->nombre) }}"
                               placeholder="Ej: Certificado ISO 9001, Póliza RC 2026..."
                               required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select @error('categoria') is-invalid @enderror"
                                id="categoria"
                                name="categoria"
                                required>
                            <option value="">Seleccionar categoría...</option>
                            @foreach($categorias as $key => $nombre)
                                <option value="{{ $key }}" {{ old('categoria', $documento->categoria) == $key ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                  id="descripcion"
                                  name="descripcion"
                                  rows="3"
                                  placeholder="Descripción opcional del documento...">{{ old('descripcion', $documento->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ARCHIVO ACTUAL --}}
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-file-earmark me-2"></i>Archivo
                        </h5>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info mb-3">
                            <div class="d-flex align-items-center">
                                @php
                                    $iconoExtension = match(strtolower($documento->archivo_extension)) {
                                        'pdf' => 'bi bi-file-earmark-pdf text-danger',
                                        'doc', 'docx' => 'bi bi-file-earmark-word text-primary',
                                        'xls', 'xlsx' => 'bi bi-file-earmark-excel text-success',
                                        'jpg', 'jpeg', 'png' => 'bi bi-file-earmark-image text-info',
                                        default => 'bi bi-file-earmark text-secondary',
                                    };
                                @endphp
                                <i class="{{ $iconoExtension }} fs-3 me-3"></i>
                                <div>
                                    <strong>Archivo actual:</strong> {{ $documento->archivo_nombre_original }}
                                    <br>
                                    <small class="text-muted">
                                        {{ strtoupper($documento->archivo_extension) }} &bull;
                                        {{ $documento->archivo_tamaño_formateado }}
                                    </small>
                                </div>
                                @if($documento->archivoExiste())
                                <a href="{{ route('documentos-empresa.descargar', $documento) }}"
                                   class="btn btn-sm btn-outline-primary ms-auto">
                                    <i class="bi bi-download me-1"></i>Descargar
                                </a>
                                @endif
                            </div>
                        </div>

                        <label for="archivo" class="form-label">Reemplazar Archivo (opcional)</label>
                        <input type="file"
                               class="form-control @error('archivo') is-invalid @enderror"
                               id="archivo"
                               name="archivo">
                        <div class="form-text">
                            Solo sube un archivo si deseas reemplazar el actual.
                            Tamaño máximo: 10MB.
                        </div>
                        @error('archivo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- FECHAS --}}
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-calendar-event me-2"></i>Fechas
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <label for="fecha_documento" class="form-label">Fecha del Documento</label>
                        <input type="date"
                               class="form-control @error('fecha_documento') is-invalid @enderror"
                               id="fecha_documento"
                               name="fecha_documento"
                               value="{{ old('fecha_documento', $documento->fecha_documento?->format('Y-m-d')) }}">
                        <div class="form-text">Fecha de emisión o creación del documento.</div>
                        @error('fecha_documento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="fecha_caducidad" class="form-label">Fecha de Caducidad</label>
                        <input type="date"
                               class="form-control @error('fecha_caducidad') is-invalid @enderror"
                               id="fecha_caducidad"
                               name="fecha_caducidad"
                               value="{{ old('fecha_caducidad', $documento->fecha_caducidad?->format('Y-m-d')) }}">
                        <div class="form-text">Dejar vacío si el documento no caduca.</div>
                        @error('fecha_caducidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- NOTAS --}}
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-sticky me-2"></i>Notas Adicionales
                        </h5>
                    </div>

                    <div class="col-12">
                        <label for="notas" class="form-label">Notas</label>
                        <textarea class="form-control @error('notas') is-invalid @enderror"
                                  id="notas"
                                  name="notas"
                                  rows="3"
                                  placeholder="Notas internas sobre el documento...">{{ old('notas', $documento->notas) }}</textarea>
                        @error('notas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- BOTONES --}}
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('documentos-empresa.show', $documento) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
