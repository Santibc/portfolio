@extends('layouts.app')

@section('title', 'Editar Maquinaria')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Maquinaria</h1>
            <p class="text-muted mb-0">
                {{ $maquinaria->tipo->nombre ?? '' }} - {{ $maquinaria->marca }} {{ $maquinaria->modelo }}
                @if($maquinaria->codigo_interno)
                    <code class="ms-2">{{ $maquinaria->codigo_interno }}</code>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('maquinaria.show', $maquinaria) }}" class="btn btn-outline-info">
                <i class="bi bi-eye me-2"></i>Ver Detalle
            </a>
            <a href="{{ route('maquinaria.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <form action="{{ route('maquinaria.update', $maquinaria) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Datos Básicos -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Datos Básicos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Maquinaria <span class="text-danger">*</span></label>
                                <select name="maquinaria_tipo_id" class="form-select @error('maquinaria_tipo_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('maquinaria_tipo_id', $maquinaria->maquinaria_tipo_id) == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('maquinaria_tipo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Código Interno</label>
                                <input type="text" name="codigo_interno" class="form-control @error('codigo_interno') is-invalid @enderror"
                                       value="{{ old('codigo_interno', $maquinaria->codigo_interno) }}" placeholder="MAQ-0001">
                                @error('codigo_interno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Marca</label>
                                <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror"
                                       value="{{ old('marca', $maquinaria->marca) }}" placeholder="Ej: STIHL, Husqvarna...">
                                @error('marca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Modelo</label>
                                <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror"
                                       value="{{ old('modelo', $maquinaria->modelo) }}" placeholder="Ej: MS 461">
                                @error('modelo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Identificación -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-upc me-2"></i>Identificación</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Número de Serie</label>
                                <input type="text" name="numero_serie" class="form-control"
                                       value="{{ old('numero_serie', $maquinaria->numero_serie) }}" placeholder="S/N">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Número de Bastidor</label>
                                <input type="text" name="numero_bastidor" class="form-control"
                                       value="{{ old('numero_bastidor', $maquinaria->numero_bastidor) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos Económicos -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-currency-euro me-2"></i>Datos Económicos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Fecha de Compra</label>
                                <input type="date" name="fecha_compra" class="form-control"
                                       value="{{ old('fecha_compra', $maquinaria->fecha_compra?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Coste de Adquisición</label>
                                <div class="input-group">
                                    <input type="number" name="coste_adquisicion" id="coste_adquisicion" class="form-control"
                                           value="{{ old('coste_adquisicion', $maquinaria->coste_adquisicion) }}" step="0.01" min="0"
                                           placeholder="0.00" onchange="calcularAmortizacion()">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Vida Útil</label>
                                <div class="input-group">
                                    <input type="number" name="vida_util_meses" id="vida_util_meses" class="form-control"
                                           value="{{ old('vida_util_meses', $maquinaria->vida_util_meses) }}" min="1"
                                           placeholder="60" onchange="calcularAmortizacion()">
                                    <span class="input-group-text">meses</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Amortización Diaria (calculada)</label>
                                <div class="input-group">
                                    <input type="text" id="amortizacion_dia_display" class="form-control bg-light"
                                           readonly value="{{ $maquinaria->amortizacion_dia ? number_format($maquinaria->amortizacion_dia, 4, ',', '.') : '' }}">
                                    <span class="input-group-text">€/día</span>
                                </div>
                                <small class="text-muted">= Coste adquisición / (Vida útil x 30 días)</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Coste por Hora</label>
                                <div class="input-group">
                                    <input type="number" name="coste_hora" class="form-control"
                                           value="{{ old('coste_hora', $maquinaria->coste_hora) }}" step="0.01" min="0"
                                           placeholder="0.00">
                                    <span class="input-group-text">€/hora</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">
                <!-- Estado -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-toggles me-2"></i>Estado</h6>
                    </div>
                    <div class="card-body">
                        <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                            <option value="operativa" {{ old('estado', $maquinaria->estado) == 'operativa' ? 'selected' : '' }}>
                                Operativa
                            </option>
                            <option value="en_reparacion" {{ old('estado', $maquinaria->estado) == 'en_reparacion' ? 'selected' : '' }}>
                                En Reparación
                            </option>
                            <option value="baja" {{ old('estado', $maquinaria->estado) == 'baja' ? 'selected' : '' }}>
                                Baja
                            </option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Asignación Actual (Solo lectura) -->
                @if($maquinaria->obraAsignada)
                <div class="card border-0 shadow-sm mb-4 border-start border-primary border-3">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Asignación Actual</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1">
                            <strong>Obra:</strong>
                            <a href="{{ route('obras.show', $maquinaria->obraAsignada) }}">
                                {{ $maquinaria->obraAsignada->codigo }}
                            </a>
                        </p>
                        <p class="mb-0 text-muted small">{{ $maquinaria->obraAsignada->nombre }}</p>
                        @if($maquinaria->trabajadorAsignado)
                            <hr class="my-2">
                            <p class="mb-0">
                                <strong>Operario:</strong> {{ $maquinaria->trabajadorAsignado->nombre_completo }}
                            </p>
                        @endif
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Gestiona las asignaciones desde la vista de detalle
                        </small>
                    </div>
                </div>
                @endif

                <!-- Documentación -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-file-earmark-check me-2"></i>Documentación</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="tiene_marcado_ce" id="tiene_marcado_ce" class="form-check-input"
                                   value="1" {{ old('tiene_marcado_ce', $maquinaria->tiene_marcado_ce) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tiene_marcado_ce">
                                <i class="bi bi-award me-1"></i> Tiene Marcado CE
                            </label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="tiene_manual" id="tiene_manual" class="form-check-input"
                                   value="1" {{ old('tiene_manual', $maquinaria->tiene_manual) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tiene_manual">
                                <i class="bi bi-book me-1"></i> Tiene Manual de Usuario
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notas -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>Notas</h6>
                    </div>
                    <div class="card-body">
                        <textarea name="notas" class="form-control" rows="4"
                                  placeholder="Observaciones adicionales...">{{ old('notas', $maquinaria->notas) }}</textarea>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('maquinaria.show', $maquinaria) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function calcularAmortizacion() {
    const coste = parseFloat(document.getElementById('coste_adquisicion').value) || 0;
    const vidaUtil = parseInt(document.getElementById('vida_util_meses').value) || 0;

    if (coste > 0 && vidaUtil > 0) {
        const amortizacion = coste / (vidaUtil * 30);
        document.getElementById('amortizacion_dia_display').value = amortizacion.toFixed(4).replace('.', ',');
    } else {
        document.getElementById('amortizacion_dia_display').value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    calcularAmortizacion();
});
</script>
@endpush
@endsection
