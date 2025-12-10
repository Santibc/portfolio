@extends('layouts.app')

@section('title', 'Mi Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header de bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1">¡Bienvenido, {{ auth()->user()->name }}!</h2>
                            <p class="mb-0 opacity-75">Continúa tu aprendizaje donde lo dejaste</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-inline-block rounded-3 p-3" style="background: rgba(255,255,255,0.2);">
                                <h3 class="mb-0" style="color: #fff;">{{ $overallProgress['percentage'] ?? 0 }}%</h3>
                                <small style="color: rgba(255,255,255,0.9);">Progreso general</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-collection-play text-primary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Cursos Disponibles</h6>
                            <h3 class="mb-0">{{ $overallProgress['total_courses'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-trophy text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Cursos Completados</h6>
                            <h3 class="mb-0">{{ $overallProgress['courses_completed'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-play-circle text-info fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Videos Vistos</h6>
                            <h3 class="mb-0">{{ $overallProgress['completed_videos'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-hourglass-split text-warning fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">En Progreso</h6>
                            <h3 class="mb-0">{{ $cursosEnProgreso->count() ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Continuar Viendo -->
        <div class="col-xl-8">
            @if($nextVideo)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-play-circle me-2 text-primary"></i>
                        Continuar viendo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            @if($nextVideo->course->thumbnail)
                            <img src="{{ asset('storage/' . $nextVideo->course->thumbnail) }}"
                                 alt="{{ $nextVideo->course->title }}"
                                 class="w-100 rounded-3"
                                 style="height: 140px; object-fit: cover;">
                            @else
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                 style="height: 140px;">
                                <i class="bi bi-collection-play display-4 text-muted"></i>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-primary-subtle text-primary mb-2">
                                {{ $nextVideo->course->category->name ?? 'Categoría' }}
                            </span>
                            <h4 class="mb-1">{{ $nextVideo->title }}</h4>
                            <p class="text-muted mb-3">{{ $nextVideo->course->title }}</p>
                            <a href="{{ route('cursos.video', [$nextVideo->course, $nextVideo]) }}"
                               class="btn btn-primary">
                                <i class="bi bi-play-fill me-2"></i>Continuar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Cursos en Progreso -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-hourglass-split me-2 text-warning"></i>
                            Mis Cursos en Progreso
                        </h5>
                        <a href="{{ route('cursos.index') }}" class="btn btn-sm btn-outline-primary">
                            Ver todos
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($cursosEnProgreso && $cursosEnProgreso->count() > 0)
                    <div class="row g-3">
                        @foreach($cursosEnProgreso->take(4) as $curso)
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 course-card-hover">
                                <div class="d-flex align-items-start">
                                    @if($curso->thumbnail)
                                    <img src="{{ asset('storage/' . $curso->thumbnail) }}"
                                         alt="{{ $curso->title }}"
                                         class="rounded me-3"
                                         style="width: 64px; height: 48px; object-fit: cover;">
                                    @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                         style="width: 64px; height: 48px;">
                                        <i class="bi bi-collection-play text-muted"></i>
                                    </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ Str::limit($curso->title, 30) }}</h6>
                                        <small class="text-muted d-block mb-2">
                                            {{ $curso->category->name ?? '' }}
                                        </small>
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary"
                                                 style="width: {{ auth()->user()->getCourseProgressPercentage($curso) }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ auth()->user()->getCourseProgressPercentage($curso) }}% completado</small>
                                    </div>
                                </div>
                                <a href="{{ route('cursos.show', $curso) }}" class="stretched-link"></a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-collection display-4 text-muted d-block mb-3"></i>
                        <p class="text-muted mb-3">No tienes cursos en progreso</p>
                        <a href="{{ route('cursos.index') }}" class="btn btn-primary">
                            Explorar Cursos
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Próximo Curso -->
            @if($nextCourse)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-unlock me-2 text-success"></i>
                        Próximo Curso Disponible
                    </h6>
                </div>
                <div class="card-body">
                    @if($nextCourse->thumbnail)
                    <img src="{{ asset('storage/' . $nextCourse->thumbnail) }}"
                         alt="{{ $nextCourse->title }}"
                         class="w-100 rounded-3 mb-3"
                         style="height: 120px; object-fit: cover;">
                    @endif
                    <h6>{{ $nextCourse->title }}</h6>
                    <p class="text-muted small mb-3">{{ Str::limit($nextCourse->description, 80) }}</p>
                    <a href="{{ route('cursos.show', $nextCourse) }}" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-play-fill me-1"></i>Comenzar
                    </a>
                </div>
            </div>
            @endif

            <!-- Actividad Reciente -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-activity me-2 text-info"></i>
                        Mi Actividad Reciente
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivity ?? [] as $activity)
                        <div class="list-group-item border-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-check2 text-success small"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="d-block">{{ $activity->video->title ?? 'Video' }}</small>
                                    <small class="text-muted">
                                        {{ $activity->completed_at ? $activity->completed_at->diffForHumans() : '' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item border-0 text-center text-muted py-4">
                            <i class="bi bi-clock-history d-block mb-2"></i>
                            Sin actividad reciente
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('progreso.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        Ver todo mi progreso
                    </a>
                </div>
            </div>

            <!-- Cursos Completados -->
            @if($cursosCompletados && $cursosCompletados->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-trophy me-2 text-warning"></i>
                        Completados ({{ $cursosCompletados->count() }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($cursosCompletados->take(3) as $curso)
                        <a href="{{ route('cursos.show', $curso) }}" class="list-group-item list-group-item-action border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-3"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 small">{{ $curso->title }}</h6>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Categorías -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-folder me-2 text-primary"></i>
                            Explorar por Categorías
                        </h5>
                        <a href="{{ route('categorias.index') }}" class="btn btn-sm btn-outline-primary">
                            Ver todas
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($categorias ?? [] as $categoria)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="{{ route('categorias.show', $categoria) }}" class="text-decoration-none">
                                <div class="border rounded-3 p-3 text-center h-100 category-card-hover">
                                    @if($categoria->image)
                                    <img src="{{ asset($categoria->image) }}"
                                         alt="{{ $categoria->name }}"
                                         class="rounded-circle mb-2"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                    <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 60px;">
                                        <i class="bi bi-folder text-primary fs-4"></i>
                                    </div>
                                    @endif
                                    <h6 class="mb-1 text-dark">{{ $categoria->name }}</h6>
                                    <small class="text-muted">{{ $categoria->courses->count() }} cursos</small>
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="col-12 text-center py-4 text-muted">
                            No hay categorías disponibles
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, var(--gva-primary) 0%, var(--gva-secondary) 100%);
}
.course-card-hover {
    transition: all 0.3s ease;
    position: relative;
}
.course-card-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.category-card-hover {
    transition: all 0.3s ease;
}
.category-card-hover:hover {
    border-color: var(--gva-primary) !important;
    transform: translateY(-2px);
}
</style>
@endpush
@endsection
