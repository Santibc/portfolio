<x-app-layout>
    <x-slot name="header">{{ isset($orden) ? 'Editar' : 'Nueva' }} Orden de Servicio</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row mb-4">
                <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('st.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('st.ordenes.index') }}">Órdenes</a></li>
                    <li class="breadcrumb-item active">{{ isset($orden) ? 'Editar' : 'Nueva' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Errores en el formulario</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ isset($orden) ? route('st.ordenes.update', $orden) : route('st.ordenes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($orden))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Información de la Orden</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">N° Orden <span class="text-danger">*</span></label>
                                <input type="text" name="numero_orden" class="form-control @error('numero_orden') is-invalid @enderror" value="{{ old('numero_orden', $numeroOrden ?? ($orden->numero_orden ?? '')) }}" readonly required>
                                @error('numero_orden')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha Recepción <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_recepcion" class="form-control @error('fecha_recepcion') is-invalid @enderror" value="{{ old('fecha_recepcion', isset($orden) ? $orden->fecha_recepcion->format('Y-m-d') : date('Y-m-d')) }}" required>
                                @error('fecha_recepcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                @php
                                    $cId    = old('cliente_id', isset($orden) ? $orden->cliente_id : '');
                                    $cLabel = '';
                                    if (isset($orden) && $orden->cliente) {
                                        $cLabel = $orden->cliente->nombre_contacto . ' — ' . $orden->cliente->numero_identificacion;
                                    }
                                @endphp
                                <select name="cliente_id" id="clienteSelect"
                                        class="form-select cliente-select2-ajax @error('cliente_id') is-invalid @enderror" required
                                        data-selected-id="{{ $cId }}"
                                        data-selected-label="{{ $cLabel }}">
                                    <option value=""></option>
                                </select>
                                @error('cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Equipo</label>
                                @php
                                    $eId    = old('st_equipo_id', isset($orden) ? $orden->st_equipo_id : '');
                                    $eLabel = '';
                                    if (isset($orden) && $orden->equipo) {
                                        $eLabel = trim($orden->equipo->marca . ' ' . $orden->equipo->modelo) . ' — S/N: ' . $orden->equipo->numero_serie;
                                    }
                                @endphp
                                <select name="st_equipo_id" id="equipoSelect"
                                        class="form-select equipo-select2-ajax @error('st_equipo_id') is-invalid @enderror"
                                        data-selected-id="{{ $eId }}"
                                        data-selected-label="{{ $eLabel }}">
                                    <option value=""></option>
                                </select>
                                @error('st_equipo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Servicio <span class="text-danger">*</span></label>
                                <select name="tipo_servicio" class="form-select @error('tipo_servicio') is-invalid @enderror" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Reparación" {{ old('tipo_servicio', isset($orden) ? $orden->tipo_servicio : '') == 'Reparación' ? 'selected' : '' }}>Reparación</option>
                                    <option value="Mantenimiento Preventivo" {{ old('tipo_servicio', isset($orden) ? $orden->tipo_servicio : '') == 'Mantenimiento Preventivo' ? 'selected' : '' }}>Mantenimiento Preventivo</option>
                                    <option value="Instalación" {{ old('tipo_servicio', isset($orden) ? $orden->tipo_servicio : '') == 'Instalación' ? 'selected' : '' }}>Instalación</option>
                                    <option value="Diagnóstico" {{ old('tipo_servicio', isset($orden) ? $orden->tipo_servicio : '') == 'Diagnóstico' ? 'selected' : '' }}>Diagnóstico</option>
                                    <option value="Garantía" {{ old('tipo_servicio', isset($orden) ? $orden->tipo_servicio : '') == 'Garantía' ? 'selected' : '' }}>Garantía</option>
                                </select>
                                @error('tipo_servicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prioridad <span class="text-danger">*</span></label>
                                <select name="prioridad" class="form-select @error('prioridad') is-invalid @enderror" required>
                                    <option value="baja" {{ old('prioridad', isset($orden) ? $orden->prioridad : 'media') == 'baja' ? 'selected' : '' }}>Baja</option>
                                    <option value="media" {{ old('prioridad', isset($orden) ? $orden->prioridad : 'media') == 'media' ? 'selected' : '' }}>Media</option>
                                    <option value="alta" {{ old('prioridad', isset($orden) ? $orden->prioridad : 'media') == 'alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="urgente" {{ old('prioridad', isset($orden) ? $orden->prioridad : 'media') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                </select>
                                @error('prioridad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción del Problema <span class="text-danger">*</span></label>
                            <textarea name="descripcion_problema" class="form-control @error('descripcion_problema') is-invalid @enderror" rows="4" required>{{ old('descripcion_problema', $orden->descripcion_problema ?? '') }}</textarea>
                            @error('descripcion_problema')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Accesorios Entregados</label>
                            <textarea name="accesorios_entregados" class="form-control @error('accesorios_entregados') is-invalid @enderror" rows="2" placeholder="Ej: Cable de video, fuente de alimentación, manual...">{{ old('accesorios_entregados', $orden->accesorios_entregados ?? '') }}</textarea>
                            @error('accesorios_entregados')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Técnico Asignado</label>
                                <select name="st_tecnico_id" class="form-select @error('st_tecnico_id') is-invalid @enderror">
                                    <option value="">Sin asignar</option>
                                    @foreach($tecnicos as $tecnico)
                                        <option value="{{ $tecnico->id }}" {{ old('st_tecnico_id', isset($orden) ? $orden->st_tecnico_id : '') == $tecnico->id ? 'selected' : '' }}>
                                            {{ $tecnico->nombre_completo }} - {{ $tecnico->especialidad }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('st_tecnico_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha Promesa Entrega</label>
                                <input type="date" name="fecha_promesa_entrega" class="form-control @error('fecha_promesa_entrega') is-invalid @enderror" value="{{ old('fecha_promesa_entrega', isset($orden) && $orden->fecha_promesa_entrega ? $orden->fecha_promesa_entrega->format('Y-m-d') : '') }}">
                                @error('fecha_promesa_entrega')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="3">{{ old('observaciones', $orden->observaciones ?? '') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                @if(isset($orden))
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">Costos</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Mano de Obra</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="costo_mano_obra" class="form-control @error('costo_mano_obra') is-invalid @enderror" step="0.01" value="{{ old('costo_mano_obra', $orden->costo_mano_obra ?? '') }}">
                                    @error('costo_mano_obra')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Repuestos</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" value="{{ number_format($orden->costo_repuestos ?? 0, 2) }}" readonly>
                                </div>
                                <small class="text-muted">Calculado automáticamente</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Total</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control fw-bold" value="{{ number_format($orden->costo_total ?? 0, 2) }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Información Adicional</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">
                            <i class="bi bi-info-circle me-1"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios
                        </p>
                        <p class="small text-muted mb-2">
                            <i class="bi bi-person me-1"></i> Al asignar un técnico, la orden cambiará a estado "Asignada"
                        </p>
                        <p class="small text-muted mb-0">
                            <i class="bi bi-calendar me-1"></i> La fecha de promesa es referencial
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 mb-4">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save me-2"></i>{{ isset($orden) ? 'Actualizar' : 'Crear' }} Orden
                </button>
                <a href="{{ route('st.ordenes.index') }}" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </a>
                </div>
            </div>
        </form>
        </div>
    </div>

@push('scripts')
<script>
$(document).ready(function() {
    // ===== Cliente con Select2 + buscador AJAX =====
    var $cliente = $('.cliente-select2-ajax');
    $cliente.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Buscar cliente por nombre, documento o email...',
        ajax: {
            url: "{{ route('clientes.buscar-ajax') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) { return { q: params.term, page: params.page || 1 }; },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return data;
            },
            cache: true
        }
    });

    // Pre-cargar selección si viene de edición
    var preClienteId    = $cliente.data('selected-id');
    var preClienteLabel = $cliente.data('selected-label');
    if (preClienteId && preClienteLabel) {
        $cliente.append(new Option(preClienteLabel, preClienteId, true, true)).trigger('change');
    }

    // ===== Equipo con Select2 + buscador AJAX, filtrado por cliente =====
    var $equipo = $('.equipo-select2-ajax');
    $equipo.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Buscar equipo (selecciona un cliente primero)...',
        allowClear: true,
        ajax: {
            url: "{{ route('st.equipos.buscar-ajax') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    cliente_id: $cliente.val()
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return data;
            },
            cache: false
        }
    });

    var preEquipoId    = $equipo.data('selected-id');
    var preEquipoLabel = $equipo.data('selected-label');
    if (preEquipoId && preEquipoLabel) {
        $equipo.append(new Option(preEquipoLabel, preEquipoId, true, true)).trigger('change');
    }

    // Cuando cambia el cliente, limpiar equipo seleccionado para forzar nueva búsqueda
    $cliente.on('change', function () {
        $equipo.val(null).trigger('change');
    });
});
</script>
@endpush
</x-app-layout>
