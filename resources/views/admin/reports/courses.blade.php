@extends('layouts.app')

@section('title', 'Reporte de Cursos')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Reporte de Cursos</h1>
            <p class="text-muted mb-0">Análisis del progreso de cada curso</p>
        </div>
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver a Reportes
        </a>
    </div>

    <!-- Lista de Cursos -->
    <div class="row g-4">
        @forelse($cursosConProgreso ?? [] as $curso)
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        @if($curso->thumbnail)
                        <img src="{{ asset('storage/' . $curso->thumbnail) }}"
                             alt="{{ $curso->title }}"
                             class="rounded me-3"
                             style="width: 80px; height: 50px; object-fit: cover;">
                        @else
                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                             style="width: 80px; height: 50px;">
                            <i class="bi bi-collection-play text-muted"></i>
                        </div>
                        @endif
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $curso->title }}</h5>
                            <div class="d-flex gap-2">
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $curso->category->name ?? 'Sin categoría' }}
                                </span>
                                <span class="badge bg-info-subtle text-info">
                                    {{ $curso->videos_count ?? 0 }} videos
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Barra de progreso general -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Tasa de completación</small>
                            <small class="fw-bold">{{ $curso->stats['tasa_completacion'] ?? 0 }}%</small>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $curso->stats['tasa_completacion'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row text-center g-2">
                        <div class="col-3">
                            <div class="border rounded py-2">
                                <h5 class="mb-0 text-muted">{{ $curso->stats['total_estudiantes'] ?? 0 }}</h5>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded py-2 bg-success bg-opacity-10">
                                <h5 class="mb-0 text-success">{{ $curso->stats['completados'] ?? 0 }}</h5>
                                <small class="text-muted">Completados</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded py-2 bg-info bg-opacity-10">
                                <h5 class="mb-0 text-info">{{ $curso->stats['en_progreso'] ?? 0 }}</h5>
                                <small class="text-muted">En progreso</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded py-2">
                                <h5 class="mb-0 text-secondary">{{ $curso->stats['sin_iniciar'] ?? 0 }}</h5>
                                <small class="text-muted">Sin iniciar</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-chat-left-text me-1"></i>
                            {{ $curso->notes_count ?? 0 }} notas
                        </small>
                        <a href="{{ route('admin.cursos.show', $curso) }}" class="btn btn-sm btn-outline-primary">
                            Ver detalles
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-collection-play display-3 text-muted d-block mb-3"></i>
                    <h5>No hay cursos publicados</h5>
                    <p class="text-muted">Publica cursos para ver las estadísticas</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
