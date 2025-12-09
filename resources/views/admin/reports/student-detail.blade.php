@extends('layouts.app')

@section('title', 'Detalle - ' . $estudiante->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reportes.estudiantes') }}">Estudiantes</a></li>
            <li class="breadcrumb-item active">{{ $estudiante->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Info del Estudiante -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($estudiante->name, 0, 1)) }}
                    </div>
                    <h4 class="mb-1">{{ $estudiante->name }}</h4>
                    <p class="text-muted mb-3">{{ $estudiante->email }}</p>

                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-primary-subtle text-primary">Estudiante</span>
                        <span class="badge bg-secondary-subtle text-secondary">
                            Desde {{ $estudiante->created_at->format('d/m/Y') }}
                        </span>
                    </div>

                    <hr>

                    <!-- Progreso Circular -->
                    <div class="mb-4">
                        <div class="position-relative d-inline-block">
                            <svg width="150" height="150" viewBox="0 0 150 150">
                                <circle cx="75" cy="75" r="60" fill="none" stroke="#e9ecef" stroke-width="12"/>
                                <circle cx="75" cy="75" r="60" fill="none" stroke="#0d6efd" stroke-width="12"
                                        stroke-dasharray="{{ ($progress['percentage'] ?? 0) * 3.77 }} 377"
                                        stroke-linecap="round" transform="rotate(-90 75 75)"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <h2 class="mb-0">{{ $progress['percentage'] ?? 0 }}%</h2>
                                <small class="text-muted">Completado</small>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center g-3">
                        <div class="col-4">
                            <h4 class="mb-1 text-primary">{{ $progress['completed_videos'] ?? 0 }}</h4>
                            <small class="text-muted">Videos</small>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-1 text-success">{{ $progress['courses_completed'] ?? 0 }}</h4>
                            <small class="text-muted">Cursos</small>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-1 text-warning">{{ $estudiante->notes_count ?? 0 }}</h4>
                            <small class="text-muted">Notas</small>
                        </div>
                    </div>
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
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($actividadReciente ?? [] as $actividad)
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-check2 text-success small"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="d-block">{{ $actividad->video->title ?? 'Video' }}</small>
                                    <small class="text-muted">
                                        {{ $actividad->video->course->title ?? 'Curso' }}
                                    </small>
                                </div>
                                <small class="text-muted">
                                    {{ $actividad->completed_at ? $actividad->completed_at->diffForHumans() : '' }}
                                </small>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            Sin actividad registrada
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Progreso por Curso -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-collection-play me-2 text-primary"></i>
                        Progreso por Curso
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Curso</th>
                                    <th>Categoría</th>
                                    <th class="text-center">Videos</th>
                                    <th>Progreso</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cursos ?? [] as $curso)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            @if($curso->thumbnail)
                                            <img src="{{ asset('storage/' . $curso->thumbnail) }}"
                                                 alt="{{ $curso->title }}"
                                                 class="rounded me-3"
                                                 style="width: 48px; height: 32px; object-fit: cover;">
                                            @else
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                 style="width: 48px; height: 32px;">
                                                <i class="bi bi-collection-play text-muted small"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $curso->title }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ $curso->category->name ?? 'Sin categoría' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-primary">{{ $curso->user_progress['completed_videos'] ?? 0 }}</span>
                                        <span class="text-muted">/</span>
                                        <span>{{ $curso->videos_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px; max-width: 120px;">
                                                <div class="progress-bar {{ ($curso->user_progress['is_completed'] ?? false) ? 'bg-success' : 'bg-primary' }}"
                                                     style="width: {{ $curso->user_progress['percentage'] ?? 0 }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $curso->user_progress['percentage'] ?? 0 }}%</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($curso->user_progress['is_completed'] ?? false)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bi bi-check-circle me-1"></i>Completado
                                        </span>
                                        @elseif(($curso->user_progress['completed_videos'] ?? 0) > 0)
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="bi bi-play-circle me-1"></i>En progreso
                                        </span>
                                        @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="bi bi-circle me-1"></i>Sin iniciar
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No hay cursos disponibles
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Estadísticas adicionales -->
            <div class="row mt-4 g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-check display-4 text-primary mb-3"></i>
                            <h5 class="mb-1">{{ $progress['last_activity'] ?? 'Sin actividad' }}</h5>
                            <p class="text-muted mb-0">Última actividad</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-history display-4 text-info mb-3"></i>
                            <h5 class="mb-1">{{ $progress['avg_time_per_video'] ?? '0 min' }}</h5>
                            <p class="text-muted mb-0">Tiempo promedio por video</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-lightning display-4 text-warning mb-3"></i>
                            <h5 class="mb-1">{{ $progress['streak'] ?? 0 }} días</h5>
                            <p class="text-muted mb-0">Racha de actividad</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
