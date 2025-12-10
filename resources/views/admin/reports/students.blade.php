@extends('layouts.app')

@section('title', 'Reporte de Estudiantes')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Reporte de Estudiantes</h1>
            <p class="text-muted mb-0">Progreso individual de cada estudiante</p>
        </div>
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver a Reportes
        </a>
    </div>

    <!-- Tabla de Estudiantes -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="studentsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Estudiante</th>
                            <th>Email</th>
                            <th class="text-center">Videos Completados</th>
                            <th class="text-center">Cursos Completados</th>
                            <th>Progreso General</th>
                            <th class="text-center pe-4">Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estudiantesConProgreso ?? [] as $estudiante)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($estudiante->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $estudiante->name }}</h6>
                                        <small class="text-muted">
                                            Desde {{ $estudiante->created_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $estudiante->email }}</td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info">
                                    {{ $estudiante->video_completions_count ?? 0 }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success">
                                    {{ $estudiante->progress['courses_completed'] ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 8px; max-width: 150px;">
                                        <div class="progress-bar bg-primary"
                                             style="width: {{ $estudiante->progress['percentage'] ?? 0 }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $estudiante->progress['percentage'] ?? 0 }}%</small>
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <span class="badge bg-warning-subtle text-warning">
                                    {{ $estudiante->notes_count ?? 0 }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-4 d-block mb-3"></i>
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

@push('scripts')
<script>
$(document).ready(function() {
    $('#studentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[2, 'desc']],
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print']
    });
});
</script>
@endpush
@endsection
