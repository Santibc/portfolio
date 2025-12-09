@extends('layouts.app')

@section('title', 'Cursos')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-1">Todos los Cursos</h1>
        <p class="text-muted mb-0">Explora nuestro catálogo completo de cursos</p>
    </div>

    <!-- Cursos agrupados por categoría -->
    @forelse($cursosPorCategoria ?? [] as $categoryId => $cursosCategoria)
    @php
        $categoria = $cursosCategoria->first()->category ?? null;
    @endphp
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <i class="bi bi-folder me-2 text-primary"></i>
                {{ $categoria->name ?? 'Sin categoría' }}
            </h4>
            @if($categoria)
            <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-sm btn-outline-primary">
                Ver todos
            </a>
            @endif
        </div>

        <div class="row g-4">
            @foreach($cursosCategoria as $index => $curso)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 course-card {{ !($curso->user_progress['can_access'] ?? true) ? 'course-locked' : '' }}">
                    <!-- Número de orden -->
                    <div class="position-absolute top-0 start-0 m-2 z-1">
                        <span class="badge bg-dark rounded-circle" style="width: 28px; height: 28px; line-height: 20px;">
                            {{ $index + 1 }}
                        </span>
                    </div>

                    <!-- Thumbnail -->
                    <div class="position-relative">
                        @if($curso->thumbnail)
                        <img src="{{ asset('storage/' . $curso->thumbnail) }}"
                             alt="{{ $curso->title }}"
                             class="card-img-top"
                             style="height: 140px; object-fit: cover;">
                        @else
                        <div class="bg-light d-flex align-items-center justify-content-center"
                             style="height: 140px;">
                            <i class="bi bi-collection-play display-5 text-muted"></i>
                        </div>
                        @endif

                        @auth
                        @if(!($curso->user_progress['can_access'] ?? true))
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                             style="background: rgba(0,0,0,0.7);">
                            <div class="text-center text-white">
                                <i class="bi bi-lock fs-2 mb-1"></i>
                                <p class="mb-0 small">Bloqueado</p>
                            </div>
                        </div>
                        @elseif($curso->user_progress['is_completed'] ?? false)
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i>
                            </span>
                        </div>
                        @endif
                        @endauth
                    </div>

                    <div class="card-body py-3">
                        <h6 class="card-title mb-1">{{ Str::limit($curso->title, 40) }}</h6>
                        <div class="d-flex gap-2 mb-2">
                            <small class="text-muted">
                                <i class="bi bi-play-btn me-1"></i>{{ $curso->videos_count ?? 0 }}
                            </small>
                        </div>

                        @auth
                        @if($curso->user_progress['can_access'] ?? true)
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar {{ ($curso->user_progress['is_completed'] ?? false) ? 'bg-success' : 'bg-primary' }}"
                                 style="width: {{ $curso->user_progress['percentage'] ?? 0 }}%"></div>
                        </div>
                        @endif
                        @endauth
                    </div>

                    <div class="card-footer bg-transparent border-0 pt-0">
                        @auth
                        @if($curso->user_progress['can_access'] ?? true)
                        <a href="{{ route('cursos.show', $curso) }}" class="btn btn-sm btn-primary w-100">
                            {{ ($curso->user_progress['percentage'] ?? 0) > 0 ? 'Continuar' : 'Comenzar' }}
                        </a>
                        @else
                        <button class="btn btn-sm btn-secondary w-100" disabled>Bloqueado</button>
                        @endif
                        @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary w-100">Iniciar sesión</a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-collection display-3 text-muted d-block mb-3"></i>
            <h4>No hay cursos disponibles</h4>
            <p class="text-muted">Pronto habrá contenido para ti</p>
        </div>
    </div>
    @endforelse
</div>

@push('styles')
<style>
.course-card { transition: all 0.3s ease; }
.course-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.1) !important; }
.course-locked:hover { transform: none; }
</style>
@endpush
@endsection
