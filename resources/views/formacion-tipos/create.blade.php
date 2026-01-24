@extends('layouts.app')

@section('title', 'Nuevo Tipo de Formacion')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nuevo Tipo de Formacion</h1>
            <p class="text-muted mb-0">Registra un nuevo tipo de formacion o certificacion</p>
        </div>
        <a href="{{ route('formacion-tipos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('formacion-tipos.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Datos Basicos -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Datos de la Formacion</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nombre de la Formacion <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre') }}" placeholder="Ej: PRL Basico, Carnet Fitosanitario..." required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Descripcion</label>
                                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                          rows="3" placeholder="Descripcion detallada de la formacion...">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuracion -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Configuracion</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Duracion (horas)</label>
                                <input type="number" name="duracion_horas"
                                       class="form-control @error('duracion_horas') is-invalid @enderror"
                                       value="{{ old('duracion_horas') }}" min="1" max="999"
                                       placeholder="Ej: 60">
                                @error('duracion_horas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Duracion del curso en horas</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Periodicidad de renovacion (meses)</label>
                                <input type="number" name="periodicidad_meses"
                                       class="form-control @error('periodicidad_meses') is-invalid @enderror"
                                       value="{{ old('periodicidad_meses') }}" min="1" max="240"
                                       placeholder="Ej: 60 (5 anos)">
                                @error('periodicidad_meses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Dejar vacio si no caduca</small>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="obligatoria" value="0">
                                    <input class="form-check-input" type="checkbox" name="obligatoria" value="1"
                                           id="obligatoria" {{ old('obligatoria') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="obligatoria">
                                        <strong>Formacion Obligatoria</strong>
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Marcar si esta formacion es obligatoria para todos los trabajadores
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">
                <!-- Acciones -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-check2-square me-2"></i>Acciones</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Guardar Tipo
                            </button>
                            <a href="{{ route('formacion-tipos.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ayuda -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Ayuda</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">
                            <strong>Formacion Obligatoria:</strong> Formaciones que todos los trabajadores deben tener para poder trabajar (PRL, etc).
                        </p>
                        <p class="small text-muted mb-2">
                            <strong>Periodicidad:</strong> Tiempo en meses despues del cual la formacion expira y debe renovarse.
                        </p>
                        <p class="small text-muted mb-0">
                            <strong>Ejemplos:</strong>
                            <br>- PRL Basico: 60h, sin caducidad
                            <br>- Carnet Fitosanitario: 120 meses (10 anos)
                            <br>- Operador Motosierra: 36 meses (3 anos)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
