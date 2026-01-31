@extends('layouts.app')

@section('title', 'Editar Obra')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Obra</h1>
            <p class="text-muted mb-0"><code>{{ $obra->codigo }}</code> - {{ $obra->nombre }}</p>
        </div>
        <a href="{{ route('obras.show', $obra) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('obras.update', $obra) }}" method="POST">
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
                            <div class="col-md-4">
                                <label class="form-label">Código <span class="text-danger">*</span></label>
                                <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror"
                                       value="{{ old('codigo', $obra->codigo) }}" required>
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre', $obra->nombre) }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_id', $obra->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nombre_comercial }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tipo de Obra</label>
                                <select name="obra_tipo_id" class="form-select">
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('obra_tipo_id', $obra->obra_tipo_id) == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $obra->descripcion) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Ubicación</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $obra->direccion) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Localidad</label>
                                <input type="text" name="localidad" class="form-control" value="{{ old('localidad', $obra->localidad) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Provincia</label>
                                <input type="text" name="provincia" class="form-control" value="{{ old('provincia', $obra->provincia) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Código Postal</label>
                                <input type="text" name="codigo_postal" class="form-control" value="{{ old('codigo_postal', $obra->codigo_postal) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Latitud</label>
                                <input type="number" name="coordenadas_lat" class="form-control" step="0.00000001"
                                       value="{{ old('coordenadas_lat', $obra->coordenadas_lat) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Longitud</label>
                                <input type="number" name="coordenadas_lng" class="form-control" step="0.00000001"
                                       value="{{ old('coordenadas_lng', $obra->coordenadas_lng) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos ADIF -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-train-front me-2"></i>Datos ADIF (Ferroviarios)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Línea</label>
                                <input type="text" name="linea" class="form-control" value="{{ old('linea', $obra->linea) }}">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Trayecto</label>
                                <input type="text" name="trayecto" class="form-control" value="{{ old('trayecto', $obra->trayecto) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">PK Inicio</label>
                                <input type="text" name="pk_inicio" class="form-control" value="{{ old('pk_inicio', $obra->pk_inicio) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">PK Fin</label>
                                <input type="text" name="pk_fin" class="form-control" value="{{ old('pk_fin', $obra->pk_fin) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gerencia/Jefatura</label>
                                <input type="text" name="gerencia_jefatura" class="form-control" value="{{ old('gerencia_jefatura', $obra->gerencia_jefatura) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Distrito</label>
                                <input type="text" name="distrito" class="form-control" value="{{ old('distrito', $obra->distrito) }}">
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
                        <select name="estado" class="form-select form-select-lg">
                            @foreach(['presentada' => 'Presentada', 'aprobada' => 'Aprobada', 'en_curso' => 'En Curso', 'pausada' => 'Pausada', 'finalizada' => 'Finalizada', 'cancelada' => 'Cancelada'] as $value => $label)
                                <option value="{{ $value }}" {{ old('estado', $obra->estado) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-calendar me-2"></i>Fechas</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Inicio Previsto</label>
                                <input type="date" name="fecha_inicio_prevista" class="form-control"
                                       value="{{ old('fecha_inicio_prevista', $obra->fecha_inicio_prevista?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-6">
                                <label class="form-label">Fin Previsto</label>
                                <input type="date" name="fecha_fin_prevista" class="form-control"
                                       value="{{ old('fecha_fin_prevista', $obra->fecha_fin_prevista?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-6">
                                <label class="form-label">Inicio Real</label>
                                <input type="date" name="fecha_inicio_real" class="form-control"
                                       value="{{ old('fecha_inicio_real', $obra->fecha_inicio_real?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-6">
                                <label class="form-label">Fin Real</label>
                                <input type="date" name="fecha_fin_real" class="form-control"
                                       value="{{ old('fecha_fin_real', $obra->fecha_fin_real?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rango Facturación -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Rango de Facturación</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Inicio Facturación</label>
                                <input type="date" name="fecha_facturacion_inicio" class="form-control"
                                       value="{{ old('fecha_facturacion_inicio', $obra->fecha_facturacion_inicio?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-6">
                                <label class="form-label">Fin Facturación</label>
                                <input type="date" name="fecha_facturacion_fin" class="form-control"
                                       value="{{ old('fecha_facturacion_fin', $obra->fecha_facturacion_fin?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Economía -->
                @can('ver_rentabilidad_obras')
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-currency-euro me-2"></i>Economía</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Presupuesto</label>
                                <div class="input-group">
                                    <input type="number" name="presupuesto" class="form-control" step="0.01" min="0"
                                           value="{{ old('presupuesto', $obra->presupuesto) }}">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Coste Estimado</label>
                                <div class="input-group">
                                    <input type="number" name="coste_estimado" class="form-control" step="0.01" min="0"
                                           value="{{ old('coste_estimado', $obra->coste_estimado) }}">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Centro de Coste</label>
                                <input type="text" name="centro_coste" class="form-control" value="{{ old('centro_coste', $obra->centro_coste) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Penalizaciones -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Penalizaciones</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="tiene_penalizaciones"
                                           id="tienePenalizaciones" value="1"
                                           {{ old('tiene_penalizaciones', $obra->tiene_penalizaciones) ? 'checked' : '' }}
                                           onchange="togglePenalizacion()">
                                    <label class="form-check-label" for="tienePenalizaciones">Tiene penalizaciones</label>
                                </div>
                            </div>

                            <div class="col-12" id="importePenalizacionContainer"
                                 style="{{ old('tiene_penalizaciones', $obra->tiene_penalizaciones) ? '' : 'display: none;' }}">
                                <label class="form-label">Importe Penalización Prevista</label>
                                <div class="input-group">
                                    <input type="number" name="importe_penalizacion_prevista" class="form-control" step="0.01" min="0"
                                           value="{{ old('importe_penalizacion_prevista', $obra->importe_penalizacion_prevista) }}">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                <!-- Responsables -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Responsables</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Encargado</label>
                                <select name="encargado_id" class="form-select">
                                    <option value="">Sin asignar</option>
                                    @foreach($encargados as $encargado)
                                        <option value="{{ $encargado->id }}" {{ old('encargado_id', $obra->encargado_id) == $encargado->id ? 'selected' : '' }}>
                                            {{ $encargado->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Riesgo Operativo</label>
                                <select name="riesgo_operativo" class="form-select">
                                    <option value="bajo" {{ old('riesgo_operativo', $obra->riesgo_operativo) == 'bajo' ? 'selected' : '' }}>Bajo</option>
                                    <option value="medio" {{ old('riesgo_operativo', $obra->riesgo_operativo) == 'medio' ? 'selected' : '' }}>Medio</option>
                                    <option value="alto" {{ old('riesgo_operativo', $obra->riesgo_operativo) == 'alto' ? 'selected' : '' }}>Alto</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notas -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>Notas</h6>
                    </div>
                    <div class="card-body">
                        <textarea name="notas" class="form-control" rows="3">{{ old('notas', $obra->notas) }}</textarea>
                    </div>
                </div>

                <!-- Botones -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                    </button>
                    <a href="{{ route('obras.show', $obra) }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function togglePenalizacion() {
    const checkbox = document.getElementById('tienePenalizaciones');
    const container = document.getElementById('importePenalizacionContainer');
    container.style.display = checkbox.checked ? 'block' : 'none';
}
</script>
@endpush
@endsection
