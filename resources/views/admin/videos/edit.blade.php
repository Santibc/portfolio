@extends('layouts.app')

@section('title', 'Editar Video - ' . $video->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.index') }}">Cursos</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.show', $curso) }}">{{ $curso->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.videos.index', $curso) }}">Videos</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil me-2 text-primary"></i>
                        Editar Video
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cursos.videos.update', [$curso, $video]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label">Título del Video <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $video->title) }}" required maxlength="200">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" maxlength="1000">{{ old('description', $video->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Video actual -->
                        <div class="mb-4">
                            <label class="form-label">Video Actual</label>
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                        <i class="bi bi-file-earmark-play text-primary fs-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ basename($video->video_path) }}</h6>
                                        <small class="text-muted">
                                            Duración: {{ $video->formatted_duration ?? 'No disponible' }}
                                        </small>
                                    </div>
                                    <a href="{{ asset('videos/' . $video->video_path) }}"
                                       target="_blank" class="btn btn-outline-primary">
                                        <i class="bi bi-play-fill me-1"></i>Ver
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Reemplazar video -->
                        <div class="mb-4">
                            <label class="form-label">Reemplazar Video <small class="text-muted">(opcional)</small></label>
                            <input type="file" name="video" class="form-control @error('video') is-invalid @enderror"
                                   accept="video/mp4,video/webm,video/ogg,video/quicktime">
                            <small class="text-muted">Selecciona un nuevo archivo solo si deseas reemplazar el video actual. Máximo 500MB.</small>
                            @error('video')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Estadísticas -->
                        <div class="mb-4">
                            <label class="form-label">Estadísticas</label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h4 class="text-primary mb-1">{{ $video->completions_count ?? 0 }}</h4>
                                        <small class="text-muted">Veces completado</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h4 class="text-info mb-1">{{ $video->order ?? 1 }}</h4>
                                        <small class="text-muted">Posición</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h4 class="text-success mb-1">{{ $video->duration_seconds ? gmdate('H:i:s', $video->duration_seconds) : '00:00' }}</h4>
                                        <small class="text-muted">Duración</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.cursos.videos.index', $curso) }}" class="btn btn-secondary">
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
@endsection
