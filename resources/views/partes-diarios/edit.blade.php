@extends('layouts.app')

@section('title', 'Editar Parte Diario')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Parte Diario</h1>
            <p class="text-muted mb-0">
                {{ $partes_diario->obra->nombre }} - {{ $partes_diario->fecha->format('d/m/Y') }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('partes-diarios.show', $partes_diario) }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye me-2"></i>Ver
            </a>
            <a href="{{ route('partes-diarios.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <form action="{{ route('partes-diarios.update', $partes_diario) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <!-- Datos básicos -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Datos del Parte</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Obra</label>
                                <input type="text" class="form-control" readonly
                                       value="{{ $partes_diario->obra->nombre }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Fecha</label>
                                <input type="text" class="form-control" readonly
                                       value="{{ $partes_diario->fecha->format('d/m/Y') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jornada <span class="text-danger">*</span></label>
                                <select name="jornada" class="form-select @error('jornada') is-invalid @enderror" required>
                                    <option value="diurna" {{ old('jornada', $partes_diario->jornada) == 'diurna' ? 'selected' : '' }}>Diurna</option>
                                    <option value="nocturna" {{ old('jornada', $partes_diario->jornada) == 'nocturna' ? 'selected' : '' }}>Nocturna</option>
                                </select>
                                @error('jornada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                                <small class="text-muted fw-semibold">DATOS ADIF</small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Línea</label>
                                <input type="text" name="linea" class="form-control"
                                       value="{{ old('linea', $partes_diario->linea) }}" placeholder="Ej: 400">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Trayecto</label>
                                <input type="text" name="trayecto" class="form-control"
                                       value="{{ old('trayecto', $partes_diario->trayecto) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gerencia/Jefatura</label>
                                <input type="text" name="gerencia_jefatura" class="form-control"
                                       value="{{ old('gerencia_jefatura', $partes_diario->gerencia_jefatura) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Brigada</label>
                                <input type="text" name="brigada" class="form-control"
                                       value="{{ old('brigada', $partes_diario->brigada) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Producción -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Producción del Día</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <small class="text-muted fw-semibold">DESBROCE</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Desbroce Total (m²)</label>
                                <input type="number" name="desbroce_total_m2" class="form-control"
                                       step="0.01" min="0" value="{{ old('desbroce_total_m2', $partes_diario->desbroce_total_m2) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Desbroce P5 (m²)</label>
                                <input type="number" name="desbroce_p5_m2" class="form-control"
                                       step="0.01" min="0" value="{{ old('desbroce_p5_m2', $partes_diario->desbroce_p5_m2) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Desbroce P6 (m²)</label>
                                <input type="number" name="desbroce_p6_m2" class="form-control"
                                       step="0.01" min="0" value="{{ old('desbroce_p6_m2', $partes_diario->desbroce_p6_m2) }}">
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                                <small class="text-muted fw-semibold">OTROS TRABAJOS</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Limpieza P8 (m²)</label>
                                <input type="number" name="limpieza_p8_m2" class="form-control"
                                       step="0.01" min="0" value="{{ old('limpieza_p8_m2', $partes_diario->limpieza_p8_m2) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Herbicida P4 (m²)</label>
                                <input type="number" name="herbicida_p4_m2" class="form-control"
                                       step="0.01" min="0" value="{{ old('herbicida_p4_m2', $partes_diario->herbicida_p4_m2) }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Talas (uds)</label>
                                <input type="number" name="talas_unidades" class="form-control"
                                       min="0" value="{{ old('talas_unidades', $partes_diario->talas_unidades) }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Podas (uds)</label>
                                <input type="number" name="podas_unidades" class="form-control"
                                       min="0" value="{{ old('podas_unidades', $partes_diario->podas_unidades) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Observaciones e Incidencias</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="3"
                                          placeholder="Observaciones del día...">{{ old('observaciones', $partes_diario->observaciones) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Incidencias</label>
                                <textarea name="incidencias" class="form-control" rows="3"
                                          placeholder="Incidencias o problemas...">{{ old('incidencias', $partes_diario->incidencias) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Estado -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Estado:</span>
                            @switch($partes_diario->estado)
                                @case('borrador')
                                    <span class="badge bg-secondary">Borrador</span>
                                    @break
                                @case('completado')
                                    <span class="badge bg-warning text-dark">Pendiente validación</span>
                                    @break
                                @case('validado')
                                    <span class="badge bg-success">Validado</span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>

                <!-- Trabajadores -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Trabajadores</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Selecciona los trabajadores que participaron:</p>
                        @php
                            $trabajadoresAsignados = $partes_diario->trabajadores->pluck('trabajador_id')->toArray();
                        @endphp
                        <div class="trabajadores-list" style="max-height: 400px; overflow-y: auto;">
                            @foreach($trabajadores as $trabajador)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           name="trabajadores[]" value="{{ $trabajador->id }}"
                                           id="trab{{ $trabajador->id }}"
                                           {{ in_array($trabajador->id, old('trabajadores', $trabajadoresAsignados)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="trab{{ $trabajador->id }}">
                                        {{ $trabajador->nombre }} {{ $trabajador->apellidos }}
                                        @if($trabajador->categoria)
                                            <br><small class="text-muted">{{ $trabajador->categoria }}</small>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                    </button>
                    <a href="{{ route('partes-diarios.show', $partes_diario) }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
