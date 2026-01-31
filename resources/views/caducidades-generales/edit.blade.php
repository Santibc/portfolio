@extends('layouts.app')

@section('title', 'Editar Caducidad')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Caducidad</h1>
            <p class="text-muted mb-0">{{ $caducidadGeneral->nombre }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('caducidades-generales.show', $caducidadGeneral) }}" class="btn btn-outline-info">
                <i class="bi bi-eye me-2"></i>Ver
            </a>
            <a href="{{ route('caducidades-generales.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('caducidades-generales.update', $caducidadGeneral) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Tipo -->
                            <div class="col-md-6">
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach($tipos as $key => $label)
                                        <option value="{{ $key }}" {{ old('tipo', $caducidadGeneral->tipo) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre', $caducidadGeneral->nombre) }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                          rows="3">{{ old('descripcion', $caducidadGeneral->descripcion) }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha Emisión -->
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Emisión</label>
                                <input type="date" name="fecha_emision" class="form-control @error('fecha_emision') is-invalid @enderror"
                                       value="{{ old('fecha_emision', $caducidadGeneral->fecha_emision?->format('Y-m-d')) }}">
                                @error('fecha_emision')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha Caducidad -->
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Caducidad <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_caducidad" class="form-control @error('fecha_caducidad') is-invalid @enderror"
                                       value="{{ old('fecha_caducidad', $caducidadGeneral->fecha_caducidad->format('Y-m-d')) }}" required>
                                @error('fecha_caducidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Documento actual -->
                            @if($caducidadGeneral->documento_path)
                            <div class="col-12">
                                <label class="form-label">Documento Actual</label>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ asset($caducidadGeneral->documento_path) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>Ver documento
                                    </a>
                                    <span class="text-muted small">{{ basename($caducidadGeneral->documento_path) }}</span>
                                </div>
                            </div>
                            @endif

                            <!-- Nuevo Documento -->
                            <div class="col-md-8">
                                <label class="form-label">{{ $caducidadGeneral->documento_path ? 'Reemplazar Documento' : 'Documento' }}</label>
                                <input type="file" name="documento" class="form-control @error('documento') is-invalid @enderror">
                                <small class="text-muted">Máximo 10MB.</small>
                                @error('documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Alerta Activa -->
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="alerta_activa"
                                           id="alerta_activa" value="1" {{ old('alerta_activa', $caducidadGeneral->alerta_activa) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="alerta_activa">
                                        Generar alertas
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-danger" id="btnEliminar">
                                <i class="bi bi-trash me-2"></i>Eliminar
                            </button>
                            <div class="d-flex gap-2">
                                <a href="{{ route('caducidades-generales.index') }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Estado Actual</h6>
                </div>
                <div class="card-body">
                    @php
                        $hoy = now();
                        $caducada = $caducidadGeneral->fecha_caducidad <= $hoy;
                        $proxima = !$caducada && $caducidadGeneral->fecha_caducidad <= $hoy->copy()->addDays(30);
                    @endphp

                    @if($caducada)
                        <div class="alert alert-danger mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Caducada</strong> desde {{ $caducidadGeneral->fecha_caducidad->format('d/m/Y') }}
                        </div>
                    @elseif($proxima)
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <strong>Próxima a caducar</strong> - {{ $caducidadGeneral->fecha_caducidad->diffInDays(now()) }} días
                        </div>
                    @else
                        <div class="alert alert-success mb-3">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Vigente</strong> - {{ $caducidadGeneral->fecha_caducidad->diffInDays(now()) }} días restantes
                        </div>
                    @endif

                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Creado</td>
                            <td>{{ $caducidadGeneral->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Modificado</td>
                            <td>{{ $caducidadGeneral->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Eliminar -->
<form id="deleteForm" action="{{ route('caducidades-generales.destroy', $caducidadGeneral) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
document.getElementById('btnEliminar').addEventListener('click', function() {
    Swal.fire({
        title: '¿Eliminar caducidad?',
        text: '¿Estás seguro de eliminar "{{ $caducidadGeneral->nombre }}"? Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
});
</script>
@endpush
@endsection
