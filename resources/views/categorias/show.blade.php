@extends('layouts.app')

@section('title', $categoria->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}">Categorías</a></li>
            <li class="breadcrumb-item active">{{ $categoria->name }}</li>
        </ol>
    </nav>

    <!-- Header de la categoría -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    @if($categoria->image)
                    <img src="{{ asset($categoria->image) }}"
                         alt="{{ $categoria->name }}"
                         class="rounded-3"
                         style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                    <div class="bg-primary bg-opacity-10 rounded-3 mx-auto d-flex align-items-center justify-content-center"
                         style="width: 120px; height: 120px;">
                        <i class="bi bi-folder text-primary display-4"></i>
                    </div>
                    @endif
                </div>
                <div class="col-md-7">
                    <h1 class="h2 mb-2">{{ $categoria->name }}</h1>
                    @if($categoria->description)
                    <div class="text-muted mb-3 description-content">{!! $categoria->description !!}</div>
                    @endif
                    <div class="d-flex gap-3">
                        <span class="badge bg-primary-subtle text-primary fs-6">
                            <i class="bi bi-collection-play me-1"></i>
                            {{ $categoria->courses->count() }} cursos
                        </span>
                        <span class="badge bg-info-subtle text-info fs-6">
                            <i class="bi bi-play-btn me-1"></i>
                            {{ $categoriaProgress['total_videos'] ?? 0 }} videos
                        </span>
                    </div>
                </div>
                <div class="col-md-3">
                    @auth
                    <div class="text-center">
                        <div class="position-relative d-inline-block mb-2">
                            <svg width="100" height="100" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#e9ecef" stroke-width="8"/>
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#198754" stroke-width="8"
                                        stroke-dasharray="{{ ($categoriaProgress['percentage'] ?? 0) * 2.51 }} 251"
                                        stroke-linecap="round" transform="rotate(-90 50 50)"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <h4 class="mb-0">{{ $categoriaProgress['percentage'] ?? 0 }}%</h4>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">
                            {{ $categoriaProgress['completed_videos'] ?? 0 }}/{{ $categoriaProgress['total_videos'] ?? 0 }} videos
                        </p>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Cursos -->
    <h4 class="mb-3">
        <i class="bi bi-collection-play me-2 text-primary"></i>
        Cursos de esta categoría
    </h4>

    <div class="row g-4">
        @forelse($categoria->courses as $index => $curso)
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 course-card {{ !($curso->user_progress['can_access'] ?? true) ? 'course-locked' : '' }}">
                <!-- Número de orden -->
                <div class="position-absolute top-0 start-0 m-3 z-1">
                    <span class="badge bg-dark rounded-circle" style="width: 30px; height: 30px; line-height: 22px;">
                        {{ $index + 1 }}
                    </span>
                </div>

                <!-- Thumbnail -->
                <div class="position-relative">
                    @if($curso->thumbnail)
                    <img src="{{ asset('storage/' . $curso->thumbnail) }}"
                         alt="{{ $curso->title }}"
                         class="card-img-top"
                         style="height: 160px; object-fit: cover;">
                    @else
                    <div class="bg-light d-flex align-items-center justify-content-center"
                         style="height: 160px;">
                        <i class="bi bi-collection-play display-4 text-muted"></i>
                    </div>
                    @endif

                    <!-- Overlay si está bloqueado -->
                    @auth
                    @if(!($curso->user_progress['can_access'] ?? true))
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                         style="background: rgba(0,0,0,0.7);">
                        <div class="text-center text-white">
                            <i class="bi bi-lock display-4 mb-2"></i>
                            <p class="mb-0 small">Completa el curso anterior</p>
                        </div>
                    </div>
                    @elseif($curso->user_progress['is_completed'] ?? false)
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle me-1"></i>Completado
                        </span>
                    </div>
                    @endif
                    @endauth
                </div>

                <div class="card-body">
                    <h5 class="card-title">{{ $curso->title }}</h5>
                    <p class="card-text text-muted small">{!! Str::limit(strip_tags($curso->description), 80) !!}</p>

                    <div class="d-flex gap-2 mb-3">
                        <span class="badge bg-info-subtle text-info">
                            <i class="bi bi-play-btn me-1"></i>
                            {{ $curso->videos_count ?? $curso->videos->count() }} videos
                        </span>
                    </div>

                    <!-- Barra de progreso -->
                    @auth
                    @if($curso->user_progress['can_access'] ?? true)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Progreso</small>
                            <small class="fw-bold">{{ $curso->user_progress['percentage'] ?? 0 }}%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ ($curso->user_progress['is_completed'] ?? false) ? 'bg-success' : 'bg-primary' }}"
                                 style="width: {{ $curso->user_progress['percentage'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    @endif
                    @endauth
                </div>

                <div class="card-footer bg-transparent border-0">
                    @auth
                    @if($curso->user_progress['can_access'] ?? true)
                        @if($curso->user_progress['is_completed'] ?? false)
                        <a href="{{ route('cursos.show', $curso) }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-arrow-repeat me-2"></i>Repasar
                        </a>
                        @elseif(($curso->user_progress['percentage'] ?? 0) > 0)
                        <a href="{{ route('cursos.show', $curso) }}" class="btn btn-primary w-100">
                            <i class="bi bi-play-fill me-2"></i>Continuar
                        </a>
                        @else
                        <a href="{{ route('cursos.show', $curso) }}" class="btn btn-success w-100">
                            <i class="bi bi-play-fill me-2"></i>Comenzar
                        </a>
                        @endif
                    @else
                    <button class="btn btn-secondary w-100" disabled>
                        <i class="bi bi-lock me-2"></i>Bloqueado
                    </button>
                    @endif
                    @else
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Inicia sesión
                    </a>
                    @endauth
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-collection display-3 text-muted d-block mb-3"></i>
                    <h4>No hay cursos en esta categoría</h4>
                    <p class="text-muted">Pronto habrá contenido disponible</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
.course-card {
    transition: all 0.3s ease;
}
.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}
.course-locked {
    opacity: 0.8;
}
.course-locked:hover {
    transform: none;
}
/* Estilos para contenido de descripción (Quill) */
.description-content p {
    margin-bottom: 0.5rem;
}
.description-content p:last-child {
    margin-bottom: 0;
}
.description-content ul,
.description-content ol {
    margin-bottom: 0.5rem;
    padding-left: 1.5rem;
}
.description-content a {
    color: var(--gva-primary, #0d6efd);
}
</style>
@endpush
@endsection
