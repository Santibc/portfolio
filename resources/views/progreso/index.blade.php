@extends('layouts.app')

@section('title', 'Mi Progreso')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-1">Mi Progreso</h1>
        <p class="text-muted mb-0">Revisa tu avance en todos los cursos</p>
    </div>

    <!-- Stats Generales -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Progreso General</h6>
                            <h2 class="mb-0">{{ $overallProgress['percentage'] ?? 0 }}%</h2>
                        </div>
                        <i class="bi bi-graph-up fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Cursos Completados</h6>
                            <h2 class="mb-0">{{ $overallProgress['courses_completed'] ?? 0 }}</h2>
                        </div>
                        <i class="bi bi-trophy fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Videos Vistos</h6>
                            <h2 class="mb-0">{{ $overallProgress['completed_videos'] ?? 0 }}/{{ $overallProgress['total_videos'] ?? 0 }}</h2>
                        </div>
                        <i class="bi bi-play-circle fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">En Progreso</h6>
                            <h2 class="mb-0">{{ $cursosEnProgreso->count() ?? 0 }}</h2>
                        </div>
                        <i class="bi bi-hourglass-split fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cursos en Progreso -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-hourglass-split me-2 text-warning"></i>
                        Cursos en Progreso
                    </h5>
                </div>
                <div class="card-body">
                    @if($cursosEnProgreso && $cursosEnProgreso->count() > 0)
                    <div class="row g-3">
                        @foreach($cursosEnProgreso as $curso)
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="d-flex align-items-start mb-3">
                                    @if($curso->thumbnail)
                                    <img src="{{ asset('storage/' . $curso->thumbnail) }}"
                                         class="rounded me-3" style="width: 60px; height: 45px; object-fit: cover;">
                                    @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 45px;">
                                        <i class="bi bi-collection-play text-muted"></i>
                                    </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $curso->title }}</h6>
                                        <small class="text-muted">{{ $curso->category->name ?? '' }}</small>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Progreso</small>
                                        <small class="fw-bold">{{ auth()->user()->getCourseProgressPercentage($curso) }}%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             style="width: {{ auth()->user()->getCourseProgressPercentage($curso) }}%"></div>
                                    </div>
                                </div>
                                <a href="{{ route('cursos.show', $curso) }}" class="btn btn-sm btn-primary w-100">
                                    Continuar
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-collection display-4 d-block mb-3"></i>
                        <p>No tienes cursos en progreso</p>
                        <a href="{{ route('cursos.index') }}" class="btn btn-primary">Explorar Cursos</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Cursos Completados -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy me-2 text-success"></i>
                        Cursos Completados
                    </h5>
                </div>
                <div class="card-body">
                    @if($cursosCompletados && $cursosCompletados->count() > 0)
                    <div class="row g-3">
                        @foreach($cursosCompletados as $curso)
                        <div class="col-md-6">
                            <div class="border border-success rounded-3 p-3 h-100 bg-success bg-opacity-10">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $curso->title }}</h6>
                                        <small class="text-muted">{{ $curso->category->name ?? '' }}</small>
                                    </div>
                                    <a href="{{ route('cursos.show', $curso) }}" class="btn btn-sm btn-outline-success">
                                        Repasar
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-trophy display-4 d-block mb-3"></i>
                        <p>Aún no has completado ningún curso</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Progreso por Categoría -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-folder me-2 text-primary"></i>
                        Progreso por Categoría
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($progressByCategory ?? [] as $catProgress)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>{{ $catProgress['name'] }}</small>
                            <small class="fw-bold">{{ $catProgress['percentage'] }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $catProgress['percentage'] }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0">Sin datos de progreso</p>
                    @endforelse
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-activity me-2 text-info"></i>
                        Actividad Reciente
                    </h6>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivity ?? [] as $activity)
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-check2 text-success small"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="d-block fw-bold">{{ $activity->video->title ?? 'Video' }}</small>
                                    <small class="text-muted">{{ $activity->video->course->title ?? '' }}</small>
                                </div>
                                <small class="text-muted">
                                    {{ $activity->completed_at ? $activity->completed_at->diffForHumans() : '' }}
                                </small>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            Sin actividad reciente
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
