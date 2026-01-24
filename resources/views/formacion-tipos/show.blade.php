@extends('layouts.app')

@section('title', $formacionTipo->nombre)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $formacionTipo->nombre }}</h1>
            <p class="text-muted mb-0">Detalle del tipo de formacion</p>
        </div>
        <div class="d-flex gap-2">
            @can('editar_formaciones')
            <a href="{{ route('formacion-tipos.edit', $formacionTipo) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            @endcan
            <a href="{{ route('formacion-tipos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <!-- Info Tipo -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Informacion de la Formacion</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nombre</label>
                            <p class="mb-0 fw-medium">{{ $formacionTipo->nombre }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tipo</label>
                            <p class="mb-0">
                                @if($formacionTipo->obligatoria)
                                    <span class="badge bg-danger">Obligatoria</span>
                                @else
                                    <span class="badge bg-success">Opcional</span>
                                @endif
                            </p>
                        </div>
                        @if($formacionTipo->descripcion)
                        <div class="col-12">
                            <label class="form-label text-muted small">Descripcion</label>
                            <p class="mb-0">{{ $formacionTipo->descripcion }}</p>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Duracion</label>
                            <p class="mb-0">
                                @if($formacionTipo->duracion_horas)
                                    <span class="badge bg-secondary">{{ $formacionTipo->duracion_horas }} horas</span>
                                @else
                                    <span class="text-muted">No especificada</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Periodicidad de renovacion</label>
                            <p class="mb-0">
                                @if($formacionTipo->periodicidad_meses)
                                    <span class="badge bg-info">Cada {{ $formacionTipo->periodicidad_meses }} meses</span>
                                    <small class="text-muted ms-1">({{ number_format($formacionTipo->periodicidad_meses / 12, 1) }} anos)</small>
                                @else
                                    <span class="text-muted">Sin caducidad</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trabajadores con esta formacion -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-people me-2"></i>Trabajadores con esta Formacion</h6>
                    <span class="badge bg-primary">{{ $formacionTipo->formaciones->count() }} registros</span>
                </div>
                <div class="card-body p-0">
                    @if($formacionTipo->formaciones->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Trabajador</th>
                                    <th>Fecha Realizacion</th>
                                    <th>Caducidad</th>
                                    <th>Centro Formacion</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($formacionTipo->formaciones->sortByDesc('fecha_realizacion') as $formacion)
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('trabajadores.show', $formacion->trabajador) }}" class="text-decoration-none">
                                            {{ $formacion->trabajador->nombre_completo ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ $formacion->fecha_realizacion->format('d/m/Y') }}</td>
                                    <td>
                                        @if($formacion->fecha_caducidad)
                                            {{ $formacion->fecha_caducidad->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($formacion->centro_formacion)
                                            {{ $formacion->centro_formacion }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($formacion->caducado)
                                            <span class="badge bg-danger">Caducado</span>
                                        @elseif($formacion->proximo_a_caducar)
                                            <span class="badge bg-warning text-dark">Prox. Caducar</span>
                                        @else
                                            <span class="badge bg-success">Vigente</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                        No hay trabajadores con esta formacion registrada
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Lateral -->
        <div class="col-lg-4">
            <!-- Estadisticas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Estadisticas</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Total Trabajadores</span>
                        <span class="badge bg-primary">{{ $stats['total_trabajadores'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Vigentes</span>
                        <span class="badge bg-success">{{ $stats['vigentes'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Proximas a Caducar</span>
                        <span class="badge bg-warning text-dark">{{ $stats['proximas_caducar'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Caducadas</span>
                        <span class="badge bg-danger">{{ $stats['caducadas'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Acciones</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('editar_formaciones')
                        <a href="{{ route('formacion-tipos.edit', $formacionTipo) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Editar Tipo
                        </a>
                        @endcan
                        @can('eliminar_formaciones')
                        @if($formacionTipo->formaciones->count() == 0)
                        <button type="button" class="btn btn-outline-danger" onclick="deleteTipo()">
                            <i class="bi bi-trash me-2"></i>Eliminar Tipo
                        </button>
                        @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Eliminar -->
<form id="deleteTipoForm" action="{{ route('formacion-tipos.destroy', $formacionTipo) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteTipo() {
    Swal.fire({
        title: '¿Eliminar tipo de formacion?',
        text: '¿Estas seguro de eliminar "{{ $formacionTipo->nombre }}"? Esta accion no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteTipoForm').submit();
        }
    });
}
</script>
@endpush
@endsection
