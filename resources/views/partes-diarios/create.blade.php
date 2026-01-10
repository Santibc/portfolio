@extends('layouts.app')

@section('title', 'Nuevo Parte Diario')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nuevo Parte Diario</h1>
            <p class="text-muted mb-0">Registrar trabajo realizado en una obra</p>
        </div>
        <a href="{{ route('partes-diarios.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('partes-diarios.store') }}" method="POST">
        @csrf
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
                                <label class="form-label">Obra <span class="text-danger">*</span></label>
                                <select name="obra_id" class="form-select @error('obra_id') is-invalid @enderror" required id="obraSelect">
                                    <option value="">Seleccionar obra...</option>
                                    @foreach($obras as $obra)
                                        <option value="{{ $obra->id }}"
                                                data-linea="{{ $obra->linea }}"
                                                data-trayecto="{{ $obra->trayecto }}"
                                                {{ (old('obra_id', $obraSeleccionada?->id) == $obra->id) ? 'selected' : '' }}>
                                            {{ $obra->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('obra_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                                       value="{{ old('fecha', date('Y-m-d')) }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jornada <span class="text-danger">*</span></label>
                                <select name="jornada" class="form-select @error('jornada') is-invalid @enderror" required>
                                    <option value="diurna" {{ old('jornada') == 'diurna' ? 'selected' : '' }}>Diurna</option>
                                    <option value="nocturna" {{ old('jornada') == 'nocturna' ? 'selected' : '' }}>Nocturna</option>
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
                                <input type="text" name="linea" class="form-control" id="lineaInput"
                                       value="{{ old('linea', $obraSeleccionada?->linea) }}" placeholder="Ej: 400">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Trayecto</label>
                                <input type="text" name="trayecto" class="form-control" id="trayectoInput"
                                       value="{{ old('trayecto', $obraSeleccionada?->trayecto) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gerencia/Jefatura</label>
                                <input type="text" name="gerencia_jefatura" class="form-control"
                                       value="{{ old('gerencia_jefatura') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Brigada</label>
                                <input type="text" name="brigada" class="form-control"
                                       value="{{ old('brigada', 'MANZER') }}">
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
                                       step="0.01" min="0" value="{{ old('desbroce_total_m2', 0) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Desbroce P5 (m²)</label>
                                <input type="number" name="desbroce_p5_m2" class="form-control"
                                       step="0.01" min="0" value="{{ old('desbroce_p5_m2', 0) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Desbroce P6 (m²)</label>
                                <input type="number" name="desbroce_p6_m2" class="form-control"
                                       step="0.01" min="0" value="{{ old('desbroce_p6_m2', 0) }}">
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                                <small class="text-muted fw-semibold">OTROS TRABAJOS</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Limpieza P8 (m²)</label>
                                <input type="number" name="limpieza_p8_m2" class="form-control"
                                       step="0.01" min="0" value="{{ old('limpieza_p8_m2', 0) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Herbicida P4 (m²)</label>
                                <input type="number" name="herbicida_p4_m2" class="form-control"
                                       step="0.01" min="0" value="{{ old('herbicida_p4_m2', 0) }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Talas (uds)</label>
                                <input type="number" name="talas_unidades" class="form-control"
                                       min="0" value="{{ old('talas_unidades', 0) }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Podas (uds)</label>
                                <input type="number" name="podas_unidades" class="form-control"
                                       min="0" value="{{ old('podas_unidades', 0) }}">
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
                                          placeholder="Observaciones del día...">{{ old('observaciones') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Incidencias</label>
                                <textarea name="incidencias" class="form-control" rows="3"
                                          placeholder="Incidencias o problemas...">{{ old('incidencias') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Trabajadores -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Trabajadores</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Selecciona los trabajadores que participaron:</p>
                        <div class="trabajadores-list" style="max-height: 400px; overflow-y: auto;">
                            @foreach($trabajadores as $trabajador)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           name="trabajadores[]" value="{{ $trabajador->id }}"
                                           id="trab{{ $trabajador->id }}"
                                           {{ in_array($trabajador->id, old('trabajadores', [])) ? 'checked' : '' }}>
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

                <!-- Info -->
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Información
                        </h6>
                        <p class="card-text small text-muted mb-0">
                            El parte se creará como <strong>borrador</strong>. Podrás editarlo y añadir más
                            detalles antes de marcarlo como completado para su validación.
                        </p>
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Crear Parte
                    </button>
                    <a href="{{ route('partes-diarios.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Auto-rellenar datos ADIF al seleccionar obra
    document.getElementById('obraSelect').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        document.getElementById('lineaInput').value = option.dataset.linea || '';
        document.getElementById('trayectoInput').value = option.dataset.trayecto || '';
    });
</script>
@endpush
@endsection
