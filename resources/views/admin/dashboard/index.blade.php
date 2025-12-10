@extends('layouts.app')

@section('title', 'Dashboard Administrativo')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard Administrativo</h1>
            <p class="text-muted mb-0">Resumen general de la plataforma GVA</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary">
                <i class="bi bi-calendar3 me-1"></i>
                {{ now()->format('d M, Y') }}
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-people-fill text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Estudiantes</h6>
                            <h3 class="mb-0">{{ $stats['total_estudiantes'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-collection-play-fill text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Cursos</h6>
                            <h3 class="mb-0">{{ $stats['total_cursos'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-play-btn-fill text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Videos</h6>
                            <h3 class="mb-0">{{ $stats['total_videos'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-check2-circle text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Completados</h6>
                            <h3 class="mb-0">{{ $stats['total_completaciones'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cursos Populares -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-trophy me-2 text-warning"></i>
                            Cursos Populares
                        </h5>
                        <a href="{{ route('admin.cursos.index') }}" class="btn btn-sm btn-outline-primary">
                            Ver todos
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Curso</th>
                                    <th>Categoría</th>
                                    <th>Videos</th>
                                    <th>Completados</th>
                                    <th class="text-end pe-4">Tasa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularCourses ?? [] as $curso)
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
                                                <i class="bi bi-collection-play text-muted"></i>
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
                                    <td>{{ $curso->videos_count ?? 0 }}</td>
                                    <td>{{ $curso->completions_count ?? 0 }}</td>
                                    <td class="text-end pe-4">
                                        <div class="progress" style="width: 80px; height: 6px;">
                                            <div class="progress-bar bg-success"
                                                 style="width: {{ $curso->completion_rate ?? 0 }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $curso->completion_rate ?? 0 }}%</small>
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
        </div>

        <!-- Actividad Reciente -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-activity me-2 text-info"></i>
                        Actividad Reciente
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivity ?? [] as $activity)
                        <div class="list-group-item border-0 px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                        <i class="bi bi-check2 text-success"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-0 small">
                                        <strong>{{ $activity->user->name ?? 'Usuario' }}</strong>
                                        completó
                                        <strong>{{ $activity->video->title ?? 'Video' }}</strong>
                                    </p>
                                    <small class="text-muted">
                                        {{ $activity->completed_at ? $activity->completed_at->diffForHumans() : '' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item border-0 text-center py-4 text-muted">
                            No hay actividad reciente
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila -->
    <div class="row g-4 mt-0">
        <!-- Categorías -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-folder me-2 text-primary"></i>
                            Categorías
                        </h5>
                        <a href="{{ route('admin.categorias.index') }}" class="btn btn-sm btn-outline-primary">
                            Gestionar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($categorias ?? [] as $categoria)
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    @if($categoria->image)
                                    <img src="{{ asset($categoria->image) }}"
                                         alt="{{ $categoria->name }}"
                                         class="rounded me-2"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                    <div class="bg-primary bg-opacity-10 rounded me-2 d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-folder text-primary"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $categoria->name }}</h6>
                                        <small class="text-muted">{{ $categoria->courses_count ?? 0 }} cursos</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge {{ $categoria->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ $categoria->is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-4 text-muted">
                            No hay categorías creadas
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Estudiantes Destacados -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-star me-2 text-warning"></i>
                            Estudiantes Destacados
                        </h5>
                        <a href="{{ route('admin.reportes.estudiantes') }}" class="btn btn-sm btn-outline-primary">
                            Ver todos
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Estudiante</th>
                                    <th>Completados</th>
                                    <th class="text-end pe-4">Progreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topStudents ?? [] as $student)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                 style="width: 36px; height: 36px;">
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $student->name }}</h6>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->video_completions_count ?? 0 }} videos</td>
                                    <td class="text-end pe-4">
                                        <div class="progress" style="width: 80px; height: 6px;">
                                            <div class="progress-bar bg-primary"
                                                 style="width: {{ $student->progress['percentage'] ?? 0 }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $student->progress['percentage'] ?? 0 }}%</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        No hay estudiantes registrados
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
