@extends('layouts.app')

@section('title', 'Editar Vehículo - ' . $vehiculo->matricula)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Vehículo</h1>
            <p class="text-muted mb-0">
                <code class="text-primary">{{ $vehiculo->matricula }}</code> - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
            </p>
        </div>
        <a href="{{ route('vehiculos.show', $vehiculo) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('vehiculos.update', $vehiculo) }}" method="POST">
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
                                <label class="form-label">Tipo de Vehículo <span class="text-danger">*</span></label>
                                <select name="vehiculo_tipo_id" class="form-select @error('vehiculo_tipo_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('vehiculo_tipo_id', $vehiculo->vehiculo_tipo_id) == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehiculo_tipo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Matrícula <span class="text-danger">*</span></label>
                                <input type="text" name="matricula" class="form-control @error('matricula') is-invalid @enderror"
                                       value="{{ old('matricula', $vehiculo->matricula) }}" placeholder="Ej: 1234 ABC" required style="text-transform: uppercase;">
                                @error('matricula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Marca</label>
                                <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror"
                                       value="{{ old('marca', $vehiculo->marca) }}" placeholder="Ej: Ford, Renault, Mercedes...">
                                @error('marca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Modelo</label>
                                <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror"
                                       value="{{ old('modelo', $vehiculo->modelo) }}" placeholder="Ej: Transit, Master...">
                                @error('modelo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Número de Bastidor</label>
                                <input type="text" name="numero_bastidor" class="form-control"
                                       value="{{ old('numero_bastidor', $vehiculo->numero_bastidor) }}" placeholder="VIN / Número de bastidor">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-calendar me-2"></i>Fechas</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Matriculación</label>
                                <input type="date" name="fecha_matriculacion" class="form-control"
                                       value="{{ old('fecha_matriculacion', $vehiculo->fecha_matriculacion?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha de Compra</label>
                                <input type="date" name="fecha_compra" class="form-control"
                                       value="{{ old('fecha_compra', $vehiculo->fecha_compra?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ITV -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-card-checklist me-2"></i>ITV</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Última ITV</label>
                                <input type="date" name="fecha_ultima_itv" class="form-control"
                                       value="{{ old('fecha_ultima_itv', $vehiculo->fecha_ultima_itv?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Próxima ITV</label>
                                <input type="date" name="fecha_proxima_itv" class="form-control @error('fecha_proxima_itv') is-invalid @enderror"
                                       value="{{ old('fecha_proxima_itv', $vehiculo->fecha_proxima_itv?->format('Y-m-d')) }}">
                                @error('fecha_proxima_itv')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seguro -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-shield me-2"></i>Seguro</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Compañía de Seguro</label>
                                <input type="text" name="compania_seguro" class="form-control"
                                       value="{{ old('compania_seguro', $vehiculo->compania_seguro) }}" placeholder="Ej: Mapfre, AXA, Allianz...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Número de Póliza</label>
                                <input type="text" name="numero_poliza" class="form-control"
                                       value="{{ old('numero_poliza', $vehiculo->numero_poliza) }}" placeholder="Número de póliza">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Vencimiento del Seguro</label>
                                <input type="date" name="fecha_vencimiento_seguro" class="form-control"
                                       value="{{ old('fecha_vencimiento_seguro', $vehiculo->fecha_vencimiento_seguro?->format('Y-m-d')) }}">
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
                        <h6 class="mb-0"><i class="bi bi-flag me-2"></i>Estado</h6>
                    </div>
                    <div class="card-body">
                        <select name="estado" class="form-select form-select-lg @error('estado') is-invalid @enderror" required>
                            <option value="operativo" {{ old('estado', $vehiculo->estado) == 'operativo' ? 'selected' : '' }}>Operativo</option>
                            <option value="en_taller" {{ old('estado', $vehiculo->estado) == 'en_taller' ? 'selected' : '' }}>En Taller</option>
                            <option value="baja" {{ old('estado', $vehiculo->estado) == 'baja' ? 'selected' : '' }}>Baja</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Datos Económicos -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-currency-euro me-2"></i>Datos Económicos</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Coste de Adquisición</label>
                            <div class="input-group">
                                <input type="number" name="coste_adquisicion" class="form-control"
                                       value="{{ old('coste_adquisicion', $vehiculo->coste_adquisicion) }}" step="0.01" min="0"
                                       placeholder="0.00">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Coste por Día</label>
                            <div class="input-group">
                                <input type="number" name="coste_dia" class="form-control"
                                       value="{{ old('coste_dia', $vehiculo->coste_dia) }}" step="0.01" min="0"
                                       placeholder="0.00">
                                <span class="input-group-text">€/día</span>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Kilometraje Actual</label>
                            <div class="input-group">
                                <input type="number" name="kilometraje_actual" class="form-control"
                                       value="{{ old('kilometraje_actual', $vehiculo->kilometraje_actual) }}" min="0"
                                       placeholder="0">
                                <span class="input-group-text">km</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asignación -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-person me-2"></i>Asignación</h6>
                    </div>
                    <div class="card-body">
                        <label class="form-label">Conductor Habitual</label>
                        <select name="conductor_habitual_id" class="form-select">
                            <option value="">Sin asignar</option>
                            @foreach($conductores as $conductor)
                                <option value="{{ $conductor->id }}" {{ old('conductor_habitual_id', $vehiculo->conductor_habitual_id) == $conductor->id ? 'selected' : '' }}>
                                    {{ $conductor->apellidos }}, {{ $conductor->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Notas -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>Notas</h6>
                    </div>
                    <div class="card-body">
                        <textarea name="notas" class="form-control" rows="4"
                                  placeholder="Observaciones adicionales...">{{ old('notas', $vehiculo->notas) }}</textarea>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('vehiculos.show', $vehiculo) }}" class="btn btn-outline-secondary">
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
// Convertir matrícula a mayúsculas
document.querySelector('input[name="matricula"]').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
@endpush
@endsection
