@extends('layouts.app')

@section('title', 'Nueva Obra')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nueva Obra</h1>
            <p class="text-muted mb-0">Registra un nuevo proyecto en el sistema</p>
        </div>
        <a href="{{ route('obras.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('obras.store') }}" method="POST">
        @csrf
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
                                       value="{{ old('codigo', $codigoSugerido) }}" required>
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nombre_comercial }}
                                            @if($cliente->tipo === 'publico') (Público) @endif
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
                                        <option value="{{ $tipo->id }}" {{ old('obra_tipo_id') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
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
                                <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Localidad</label>
                                <input type="text" name="localidad" class="form-control" value="{{ old('localidad') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Provincia</label>
                                <input type="text" name="provincia" class="form-control" value="{{ old('provincia') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Código Postal</label>
                                <input type="text" name="codigo_postal" class="form-control" value="{{ old('codigo_postal') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Latitud</label>
                                <input type="number" name="coordenadas_lat" class="form-control" step="0.00000001"
                                       value="{{ old('coordenadas_lat') }}" placeholder="41.3851">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Longitud</label>
                                <input type="number" name="coordenadas_lng" class="form-control" step="0.00000001"
                                       value="{{ old('coordenadas_lng') }}" placeholder="2.1734">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos ADIF (específicos para trabajos ferroviarios) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-train-front me-2"></i>Datos ADIF (Ferroviarios)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Línea</label>
                                <input type="text" name="linea" class="form-control" value="{{ old('linea') }}"
                                       placeholder="L220 E1">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Trayecto</label>
                                <input type="text" name="trayecto" class="form-control" value="{{ old('trayecto') }}"
                                       placeholder="Calaf - Manresa">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">PK Inicio</label>
                                <input type="text" name="pk_inicio" class="form-control" value="{{ old('pk_inicio') }}"
                                       placeholder="262+450">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">PK Fin</label>
                                <input type="text" name="pk_fin" class="form-control" value="{{ old('pk_fin') }}"
                                       placeholder="265+200">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gerencia/Jefatura</label>
                                <input type="text" name="gerencia_jefatura" class="form-control" value="{{ old('gerencia_jefatura') }}"
                                       placeholder="BCN">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Distrito</label>
                                <input type="text" name="distrito" class="form-control" value="{{ old('distrito') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">
                <!-- Fechas -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-calendar me-2"></i>Fechas</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Fecha Inicio Prevista</label>
                                <input type="date" name="fecha_inicio_prevista" class="form-control"
                                       value="{{ old('fecha_inicio_prevista') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Fecha Fin Prevista</label>
                                <input type="date" name="fecha_fin_prevista" class="form-control"
                                       value="{{ old('fecha_fin_prevista') }}">
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
                            <div class="col-12">
                                <label class="form-label">Fecha Inicio Facturación</label>
                                <input type="date" name="fecha_facturacion_inicio" class="form-control"
                                       value="{{ old('fecha_facturacion_inicio') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Fecha Fin Facturación</label>
                                <input type="date" name="fecha_facturacion_fin" class="form-control"
                                       value="{{ old('fecha_facturacion_fin') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Economía -->
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
                                           value="{{ old('presupuesto') }}">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Coste Estimado</label>
                                <div class="input-group">
                                    <input type="number" name="coste_estimado" class="form-control" step="0.01" min="0"
                                           value="{{ old('coste_estimado') }}">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Centro de Coste</label>
                                <input type="text" name="centro_coste" class="form-control" value="{{ old('centro_coste') }}">
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
                                           id="tienePenalizaciones" value="1" {{ old('tiene_penalizaciones') ? 'checked' : '' }}
                                           onchange="togglePenalizacion()">
                                    <label class="form-check-label" for="tienePenalizaciones">Tiene penalizaciones</label>
                                </div>
                            </div>

                            <div class="col-12" id="importePenalizacionContainer" style="{{ old('tiene_penalizaciones') ? '' : 'display: none;' }}">
                                <label class="form-label">Importe Penalización Prevista</label>
                                <div class="input-group">
                                    <input type="number" name="importe_penalizacion_prevista" class="form-control" step="0.01" min="0"
                                           value="{{ old('importe_penalizacion_prevista') }}">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                        <option value="{{ $encargado->id }}" {{ old('encargado_id') == $encargado->id ? 'selected' : '' }}>
                                            {{ $encargado->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Riesgo Operativo</label>
                                <select name="riesgo_operativo" class="form-select">
                                    <option value="bajo" {{ old('riesgo_operativo', 'bajo') == 'bajo' ? 'selected' : '' }}>Bajo</option>
                                    <option value="medio" {{ old('riesgo_operativo') == 'medio' ? 'selected' : '' }}>Medio</option>
                                    <option value="alto" {{ old('riesgo_operativo') == 'alto' ? 'selected' : '' }}>Alto</option>
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
                        <textarea name="notas" class="form-control" rows="3">{{ old('notas') }}</textarea>
                    </div>
                </div>

                <!-- Botones -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Crear Obra
                    </button>
                    <a href="{{ route('obras.index') }}" class="btn btn-outline-secondary">Cancelar</a>
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
