@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="container-fluid py-4">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1">Reportes y Estadísticas</h1>
      <p class="text-muted mb-0">Análisis del progreso de la plataforma</p>
    </div>
    <div class="btn-group">
      <a href="{{ route('admin.reportes.estudiantes') }}" class="btn btn-outline-primary">
        <i class="bi bi-people me-2"></i>Por Estudiantes
      </a>
      <a href="{{ route('admin.reportes.cursos') }}" class="btn btn-outline-primary">
        <i class="bi bi-collection-play me-2"></i>Por Cursos
      </a>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
      <div class="card border-0 shadow-sm bg-primary text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-white-50 mb-1">Total Estudiantes</h6>
              <h2 class="mb-0">{{ $stats['total_students'] ?? 0 }}</h2>
            </div>
            <i class="bi bi-people fs-1 text-white-50"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6">
      <div class="card border-0 shadow-sm bg-success text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-white-50 mb-1">Cursos Completados</h6>
              <h2 class="mb-0">{{ $stats['courses_completed'] ?? 0 }}</h2>
            </div>
            <i class="bi bi-trophy fs-1 text-white-50"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6">
      <div class="card border-0 shadow-sm bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-white-50 mb-1">Videos Vistos</h6>
              <h2 class="mb-0">{{ $stats['total_completions'] ?? 0 }}</h2>
            </div>
            <i class="bi bi-play-circle fs-1 text-white-50"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Progreso por Categoría -->
    <div class="col-xl-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-transparent border-0 py-3">
          <h5 class="mb-0">
            <i class="bi bi-bar-chart me-2 text-primary"></i>
            Progreso por Categoría
          </h5>
        </div>
        <div class="card-body">
          @forelse($categorias ?? [] as $categoria)
          <div class="mb-4">
            <div class="d-flex justify-content-between mb-2">
              <div>
                <h6 class="mb-0">{{ $categoria->name }}</h6>
                <small class="text-muted">{{ $categoria->courses->count() }} cursos</small>
              </div>
              <span class="badge bg-primary-subtle text-primary align-self-start">
                {{ $categoria->courses->sum('videos_count') }} videos
              </span>
            </div>

            @foreach($categoria->courses as $curso)
            <div class="d-flex align-items-center mb-2 ps-3">
              <small class="text-muted me-3" style="min-width: 150px;">{{ Str::limit($curso->title, 25) }}</small>
              <div class="flex-grow-1">
                <div class="progress" style="height: 8px;">
                  <div class="progress-bar bg-success" style="width: {{ $curso->completion_rate ?? 0 }}%"></div>
                </div>
              </div>
              <small class="text-muted ms-3" style="min-width: 40px;">{{ $curso->completion_rate ?? 0 }}%</small>
            </div>
            @endforeach
          </div>
          @empty
          <div class="text-center py-4 text-muted">
            No hay categorías disponibles
          </div>
          @endforelse
        </div>
      </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="col-xl-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-transparent border-0 py-3">
          <h5 class="mb-0">
            <i class="bi bi-activity me-2 text-success"></i>
            Actividad de Hoy
          </h5>
        </div>
        <div class="card-body">
          <div class="row g-3 text-center mb-4">
            <div class="col-4">
              <div class="border rounded p-3">
                <h4 class="text-primary mb-0">{{ $stats['today_completions'] ?? 0 }}</h4>
                <small class="text-muted">Videos vistos</small>
              </div>
            </div>
            <div class="col-4">
              <div class="border rounded p-3">
                <h4 class="text-success mb-0">{{ $stats['today_notes'] ?? 0 }}</h4>
                <small class="text-muted">Notas</small>
              </div>
            </div>
            <div class="col-4">
              <div class="border rounded p-3">
                <h4 class="text-info mb-0">{{ $stats['active_students_today'] ?? 0 }}</h4>
                <small class="text-muted">Activos</small>
              </div>
            </div>
          </div>

          <h6 class="text-muted mb-3">Últimas acciones</h6>
          <div class="list-group list-group-flush">
            @forelse($stats['recent_activity'] ?? [] as $activity)
            <div class="list-group-item px-0">
              <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                  <i class="bi bi-check2 text-success small"></i>
                </div>
                <div class="flex-grow-1">
                  <small class="d-block">
                    <strong>{{ $activity->user->name ?? 'Usuario' }}</strong>
                  </small>
                  <small class="text-muted">
                    completó {{ $activity->video->title ?? 'video' }}
                  </small>
                </div>
                <small class="text-muted">
                  {{ $activity->completed_at ? $activity->completed_at->diffForHumans() : '' }}
                </small>
              </div>
            </div>
            @empty
            <div class="text-center py-3 text-muted">
              <small>Sin actividad reciente</small>
            </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tasa de Completación General -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="mb-3">
            <i class="bi bi-graph-up-arrow me-2 text-info"></i>
            Resumen General
          </h5>
          <div class="row g-4">
            <div class="col-md-3">
              <div class="text-center">
                <div class="position-relative d-inline-block mb-3">
                  <svg width="120" height="120" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="10" />
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#198754" stroke-width="10"
                      stroke-dasharray="{{ ($stats['overall_completion_rate'] ?? 0) * 3.14 }} 314"
                      stroke-linecap="round" transform="rotate(-90 60 60)" />
                  </svg>
                  <div class="position-absolute top-50 start-50 translate-middle">
                    <h4 class="mb-0">{{ $stats['overall_completion_rate'] ?? 0 }}%</h4>
                  </div>
                </div>
                <p class="text-muted mb-0">Tasa de Completación</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="text-center p-3 bg-light rounded">
                <h3 class="text-primary mb-1">{{ $stats['avg_videos_per_student'] ?? 0 }}</h3>
                <p class="text-muted mb-0">Videos promedio por estudiante</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="text-center p-3 bg-light rounded">
                <h3 class="text-success mb-1">{{ $stats['most_active_day'] ?? 'N/A' }}</h3>
                <p class="text-muted mb-0">Día más activo</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="text-center p-3 bg-light rounded">
                <h3 class="text-info mb-1">{{ $stats['avg_completion_time'] ?? 'N/A' }}</h3>
                <p class="text-muted mb-0">Tiempo promedio de curso</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection