@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-1">Categorías</h1>
        <p class="text-muted mb-0">Explora nuestros cursos organizados por categorías</p>
    </div>

    <!-- Lista de Categorías -->
    <div class="row g-4">
        @forelse($categorias ?? [] as $categoria)
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 category-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($categoria->image)
                        <img src="{{ asset($categoria->image) }}"
                             alt="{{ $categoria->name }}"
                             class="rounded-3 me-3"
                             style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                        <div class="bg-primary bg-opacity-10 rounded-3 me-3 d-flex align-items-center justify-content-center"
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-folder text-primary display-5"></i>
                        </div>
                        @endif
                        <div>
                            <h4 class="mb-1">{{ $categoria->name }}</h4>
                            <span class="badge bg-primary-subtle text-primary">
                                {{ $categoria->courses_count ?? $categoria->courses->count() }} cursos
                            </span>
                        </div>
                    </div>

                    @if($categoria->description)
                    <div class="text-muted mb-3 description-content">{!! Str::limit(strip_tags($categoria->description), 100) !!}</div>
                    @endif

                    <!-- Progreso del usuario en la categoría -->
                    @auth
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Tu progreso</small>
                            <small class="fw-bold">{{ $categoria->user_progress['percentage'] ?? 0 }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success"
                                 style="width: {{ $categoria->user_progress['percentage'] ?? 0 }}%"></div>
                        </div>
                        <small class="text-muted">
                            {{ $categoria->user_progress['completed_videos'] ?? 0 }} de {{ $categoria->user_progress['total_videos'] ?? 0 }} videos
                        </small>
                    </div>
                    @endauth

                    <!-- Lista de cursos preview -->
                    @if($categoria->courses && $categoria->courses->count() > 0)
                    <div class="border-top pt-3">
                        <h6 class="text-muted mb-2 small">Cursos incluidos:</h6>
                        @foreach($categoria->courses->take(3) as $curso)
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-play-circle text-primary me-2"></i>
                            <small>{{ Str::limit($curso->title, 35) }}</small>
                            @auth
                            @if(auth()->user()->hasCourseCompleted($curso))
                            <i class="bi bi-check-circle-fill text-success ms-auto"></i>
                            @endif
                            @endauth
                        </div>
                        @endforeach
                        @if($categoria->courses->count() > 3)
                        <small class="text-muted">+{{ $categoria->courses->count() - 3 }} más...</small>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-primary w-100">
                        <i class="bi bi-eye me-2"></i>Ver Cursos
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-folder-x display-3 text-muted d-block mb-3"></i>
                    <h4>No hay categorías disponibles</h4>
                    <p class="text-muted">Pronto habrá contenido disponible para ti</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
.category-card {
    transition: all 0.3s ease;
}
.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}
</style>
@endpush
@endsection
