@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categorias.show', $course->category) }}">{{ $course->category->name ?? 'Categoría' }}</a></li>
            <li class="breadcrumb-item active">{{ $course->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Info del Curso -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    @if($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}"
                         alt="{{ $course->title }}"
                         class="w-100 rounded-3 mb-3"
                         style="height: 180px; object-fit: cover;">
                    @else
                    <div class="bg-light rounded-3 mb-3 d-flex align-items-center justify-content-center"
                         style="height: 180px;">
                        <i class="bi bi-collection-play display-3 text-muted"></i>
                    </div>
                    @endif

                    <h3 class="mb-2">{{ $course->title }}</h3>

                    <div class="d-flex gap-2 mb-3">
                        <span class="badge bg-primary-subtle text-primary">
                            {{ $course->category->name ?? 'Sin categoría' }}
                        </span>
                        <span class="badge bg-info-subtle text-info">
                            {{ $course->videos->count() }} videos
                        </span>
                    </div>

                    @if($course->description)
                    <div class="text-muted description-content">{!! $course->description !!}</div>
                    @endif

                    <!-- Progreso -->
                    @if($courseProgress)
                    <hr>
                    <div class="text-center mb-3">
                        <div class="position-relative d-inline-block">
                            <svg width="120" height="120" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="10"/>
                                <circle cx="60" cy="60" r="50" fill="none"
                                        stroke="{{ $courseProgress['is_completed'] ? '#198754' : '#0d6efd' }}"
                                        stroke-width="10"
                                        stroke-dasharray="{{ $courseProgress['percentage'] * 3.14 }} 314"
                                        stroke-linecap="round" transform="rotate(-90 60 60)"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <h3 class="mb-0">{{ $courseProgress['percentage'] }}%</h3>
                            </div>
                        </div>
                        <p class="text-muted mt-2 mb-0">
                            {{ $courseProgress['completed_videos'] }} de {{ $courseProgress['total_videos'] }} videos
                        </p>
                    </div>

                    @if($courseProgress['is_completed'])
                    <div class="alert alert-success text-center mb-0">
                        <i class="bi bi-trophy me-2"></i>
                        <strong>¡Curso completado!</strong>
                    </div>
                    @endif
                    @endif
                </div>
            </div>

            <!-- Info de notas -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <i class="bi bi-sticky text-warning display-6 d-block mb-2"></i>
                    <p class="text-muted mb-0">Las notas se toman directamente en cada video</p>
                    <small class="text-muted">Selecciona un video para comenzar</small>
                </div>
            </div>
        </div>

        <!-- Lista de Videos -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-play-btn me-2 text-primary"></i>
                        Videos del Curso
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($course->videos as $index => $video)
                        <a href="{{ route('cursos.video', [$course, $video]) }}"
                           class="list-group-item list-group-item-action py-3 {{ $video->is_completed ? 'bg-success bg-opacity-10' : '' }}">
                            <div class="d-flex align-items-center">
                                <span class="badge {{ $video->is_completed ? 'bg-success' : 'bg-secondary' }} me-3 rounded-circle"
                                      style="width: 32px; height: 32px; line-height: 24px;">
                                    @if($video->is_completed)
                                    <i class="bi bi-check"></i>
                                    @else
                                    {{ $index + 1 }}
                                    @endif
                                </span>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 {{ $video->is_completed ? 'text-success' : '' }}">
                                        {{ $video->title }}
                                    </h6>
                                    @if($video->description)
                                    <p class="mb-0 small text-muted">{!! Str::limit(strip_tags($video->description), 80) !!}</p>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $video->formatted_duration ?? '0:00' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
