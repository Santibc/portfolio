<x-app-layout>
    <x-slot name="header">
        {{ $cliente->exists ? 'Editar Cliente' : 'Nuevo Cliente' }}
    </x-slot>

    @push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .tipo-cliente-card {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid #dee2e6;
        }
        .tipo-cliente-card:hover {
            border-color: #0d6efd;
        }
        .tipo-cliente-card.selected {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        .tipo-cliente-card .card-body {
            padding: 1rem;
        }
        .juridica-fields {
            display: none;
        }
        .juridica-fields.show {
            display: block;
        }
        .sucursal-item, .documento-item {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }
        .badge-principal {
            font-size: 0.7rem;
        }
    </style>
    @endpush

    <div class="container py-4">
        {{-- Mensajes Flash --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <strong>Se encontraron errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Formulario Principal --}}
        <form method="POST" action="{{ route('clientes.guardar') }}" id="formCliente" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ old('id', $cliente->id) }}">
            <input type="hidden" name="pais_id" value="{{ $pais_id }}">

            {{-- Card: Tipo de Cliente --}}
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="section-title"><i class="bi bi-person-badge me-2"></i>Tipo de Cliente</h5>
                    <div class="row">
                        @foreach($tiposCliente as $key => $label)
                        <div class="col-md-6 mb-3">
                            <div class="card tipo-cliente-card {{ old('tipo_cliente', $cliente->tipo_cliente) == $key ? 'selected' : '' }}"
                                 onclick="seleccionarTipoCliente('{{ $key }}')">
                                <div class="card-body text-center">
                                    <i class="bi {{ $key == 'natural' ? 'bi-person' : 'bi-building' }} fs-1 mb-2 d-block"></i>
                                    <h6 class="mb-0">{{ $label }}</h6>
                                    <small class="text-muted">
                                        {{ $key == 'natural' ? 'Cliente particular' : 'Empresa o negocio' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="tipo_cliente" id="tipo_cliente"
                           value="{{ old('tipo_cliente', $cliente->tipo_cliente ?? 'natural') }}">
                    @error('tipo_cliente') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Card: Datos Persona Juridica (condicional) --}}
            <div class="card shadow mb-4 juridica-fields {{ old('tipo_cliente', $cliente->tipo_cliente) == 'juridica' ? 'show' : '' }}" id="juridicaFields">
                <div class="card-body">
                    <h5 class="section-title"><i class="bi bi-building me-2"></i>Datos de Empresa</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Razon Social <span class="text-danger">*</span></label>
                            <input name="razon_social" type="text" class="form-control"
                                   value="{{ old('razon_social', $cliente->razon_social) }}">
                            @error('razon_social') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">NIT <span class="text-danger">*</span></label>
                            <input name="nit" type="text" class="form-control"
                                   value="{{ old('nit', $cliente->nit) }}">
                            @error('nit') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Representante Legal</label>
                            <input name="representante_legal" type="text" class="form-control"
                                   value="{{ old('representante_legal', $cliente->representante_legal) }}">
                            @error('representante_legal') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Datos Basicos --}}
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="section-title"><i class="bi bi-person-lines-fill me-2"></i>Datos del Contacto</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Identificacion <span class="text-danger req-natural">*</span></label>
                            <input name="numero_identificacion" type="text" class="form-control"
                                   value="{{ old('numero_identificacion', $cliente->numero_identificacion) }}">
                            @error('numero_identificacion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nombre Contacto <span class="text-danger req-natural">*</span></label>
                            <input name="nombre_contacto" type="text" class="form-control"
                                   value="{{ old('nombre_contacto', $cliente->nombre_contacto) }}">
                            @error('nombre_contacto') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Telefono</label>
                            <input name="telefono" type="text" class="form-control"
                                   value="{{ old('telefono', $cliente->telefono) }}">
                            @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input name="email" type="email" class="form-control"
                                   value="{{ old('email', $cliente->email) }}">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Direccion</label>
                            <input name="direccion" type="text" class="form-control"
                                   value="{{ old('direccion', $cliente->direccion) }}">
                            @error('direccion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Ubicacion --}}
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="section-title"><i class="bi bi-geo-alt me-2"></i>Ubicacion</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Departamento <span class="text-danger">*</span></label>
                            <select id="departamento-select" name="departamento_id" class="form-select select2">
                                <option value="">-- Seleccionar --</option>
                                @foreach($departamentos as $id => $nombre)
                                <option value="{{ $id }}"
                                    {{ old('departamento_id', $cliente->ciudad->departamento_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('departamento_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ciudad <span class="text-danger">*</span></label>
                            <select id="ciudad-select" name="ciudad_id" class="form-select select2">
                                <option value="">-- Seleccionar --</option>
                                @if($cliente->exists && $cliente->ciudad)
                                    @foreach(\App\Models\Ciudad::where('departamento_id', $cliente->ciudad->departamento_id)->pluck('nombre', 'id') as $id => $ciudad)
                                    <option value="{{ $id }}"
                                        {{ old('ciudad_id', $cliente->ciudad_id) == $id ? 'selected' : '' }}>
                                        {{ $ciudad }}
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('ciudad_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Configuracion Comercial --}}
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="section-title"><i class="bi bi-cash-stack me-2"></i>Configuracion Comercial</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vendedor Asignado</label>
                            <select name="vendedor_id" class="form-select"
                                @if($cliente->exists && !auth()->user()->hasRole(['admin', 'auxiliar_administrativo'])) disabled @endif>
                                <option value="">-- Seleccionar --</option>
                                @foreach($vendedores as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('vendedor_id', $cliente->vendedor_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                                @endforeach
                            </select>
                            @if($cliente->exists && !auth()->user()->hasRole(['admin', 'auxiliar_administrativo']))
                            <input type="hidden" name="vendedor_id" value="{{ $cliente->vendedor_id }}">
                            <small class="text-muted">Solo administradores pueden cambiar el vendedor asignado.</small>
                            @endif
                            @error('vendedor_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lista de Precio <span class="text-danger">*</span></label>
                            <select name="lista_precio_id" class="form-select"
                                @if($cliente->exists && !auth()->user()->hasRole(['admin', 'auxiliar_administrativo'])) disabled @endif>
                                <option value="">-- Seleccionar --</option>
                                @foreach($listas as $id => $nombre)
                                <option value="{{ $id }}"
                                    {{ old('lista_precio_id', $cliente->lista_precio_id) == $id ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                                @endforeach
                            </select>
                            @if($cliente->exists && !auth()->user()->hasRole(['admin', 'auxiliar_administrativo']))
                            <input type="hidden" name="lista_precio_id" value="{{ $cliente->lista_precio_id }}">
                            <small class="text-muted">Solo administradores pueden cambiar la lista de precios.</small>
                            @endif
                            @error('lista_precio_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Valor Flete</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input name="valor_flete" type="number" step="0.01" min="0" class="form-control"
                                       value="{{ old('valor_flete', $cliente->valor_flete) }}">
                            </div>
                            @error('valor_flete') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aplica_flete" id="aplica_flete" value="1"
                                       {{ old('aplica_flete', $cliente->aplica_flete) ? 'checked' : '' }}>
                                <label class="form-check-label" for="aplica_flete">Aplica Flete</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $cliente->observaciones) }}</textarea>
                            @error('observaciones') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Sucursales --}}
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Sucursales</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="abrirModalSucursal()">
                        <i class="bi bi-plus-lg me-1"></i>Agregar Sucursal
                    </button>
                </div>
                <div class="card-body">
                    <div id="listaSucursales">
                        @if($cliente->exists)
                            @forelse($sucursales as $sucursal)
                            <div class="sucursal-item" id="sucursal-{{ $sucursal->id }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            {{ $sucursal->nombre }}
                                            @if($sucursal->es_principal)
                                            <span class="badge bg-success badge-principal ms-2">Principal</span>
                                            @endif
                                        </h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $sucursal->direccion }}
                                            @if($sucursal->ciudad)
                                            - {{ $sucursal->ciudad->nombre }}
                                            @endif
                                        </p>
                                        @if($sucursal->telefono || $sucursal->email)
                                        <p class="mb-0 text-muted small">
                                            @if($sucursal->telefono)
                                            <i class="bi bi-telephone me-1"></i>{{ $sucursal->telefono }}
                                            @endif
                                            @if($sucursal->email)
                                            <i class="bi bi-envelope ms-2 me-1"></i>{{ $sucursal->email }}
                                            @endif
                                        </p>
                                        @endif
                                        @if($sucursal->contacto)
                                        <p class="mb-0 text-muted small">
                                            <i class="bi bi-person me-1"></i>Contacto: {{ $sucursal->contacto }}
                                        </p>
                                        @endif
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarSucursal({{ $sucursal->id }}, {{ json_encode($sucursal) }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarSucursal({{ $sucursal->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted text-center" id="sinSucursales">No hay sucursales registradas.</p>
                            @endforelse
                        @else
                            <p class="text-muted text-center" id="sinSucursales">No hay sucursales registradas.</p>
                        @endif
                    </div>
                </div>
            </div>
            <input type="hidden" name="sucursales" id="sucursalesData" value="">

            {{-- Card: Documentos --}}
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Documentos</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="abrirModalDocumento()">
                        <i class="bi bi-upload me-1"></i>Subir Documento
                    </button>
                </div>
                <div class="card-body">
                    <div id="listaDocumentos">
                        @if($cliente->exists)
                            @forelse($documentos as $documento)
                            <div class="documento-item" id="documento-{{ $documento->id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="bi {{ $documento->icono }} fs-3 me-3"></i>
                                        <div>
                                            <h6 class="mb-0">{{ $documento->nombre }}</h6>
                                            <small class="text-muted">
                                                {{ $documento->tipo_nombre }} |
                                                {{ $documento->tamano_formateado }} |
                                                Subido por {{ $documento->subidoPor?->name ?? 'Sistema' }}
                                                el {{ $documento->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('clientes.documentos.descargar', $documento) }}" class="btn btn-sm btn-outline-success" title="Descargar">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDocumento({{ $documento->id }})" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted text-center" id="sinDocumentos">No hay documentos cargados.</p>
                            @endforelse
                        @else
                            <p class="text-muted text-center" id="sinDocumentos">No hay documentos cargados.</p>
                        @endif
                    </div>
                    {{-- Contenedor para archivos en modo creacion --}}
                    <div id="documentosNuevosContainer"></div>
                </div>
            </div>

            {{-- Botones de accion --}}
            <div class="d-flex justify-content-between mb-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i>Guardar Cliente
                </button>
                <a href="{{ route('clientes') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-x-lg me-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>

    {{-- Modal Sucursal --}}
    <div class="modal fade" id="modalSucursal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSucursalTitle">Nueva Sucursal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formSucursal">
                    <div class="modal-body">
                        <input type="hidden" name="sucursal_id" id="sucursal_id">
                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="sucursal_nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Direccion <span class="text-danger">*</span></label>
                            <input type="text" name="direccion" id="sucursal_direccion" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ciudad</label>
                            <select name="ciudad_id" id="sucursal_ciudad" class="form-select">
                                <option value="">-- Seleccionar --</option>
                                @foreach(\App\Models\Ciudad::orderBy('nombre')->pluck('nombre', 'id') as $id => $nombre)
                                <option value="{{ $id }}">{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefono</label>
                                <input type="text" name="telefono" id="sucursal_telefono" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="sucursal_email" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contacto</label>
                            <input type="text" name="contacto" id="sucursal_contacto" class="form-control">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="es_principal" id="sucursal_principal" value="1">
                            <label class="form-check-label" for="sucursal_principal">
                                Marcar como sucursal principal
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Documento --}}
    <div class="modal fade" id="modalDocumento" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Subir Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formDocumento" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Documento <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="documento_nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Documento</label>
                            <select name="tipo" id="documento_tipo" class="form-select">
                                <option value="">-- Seleccionar --</option>
                                @foreach($tiposDocumento as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Archivo(s) <span class="text-danger">*</span></label>
                            <input type="file" name="documento" id="documento_archivo" class="form-control" multiple required>
                            <small class="text-muted">Cualquier tipo de archivo. Maximo 50MB por archivo.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Subir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    const clienteId = {{ $cliente->id ?? 'null' }};
    const esEdicion = clienteId !== null;

    // Arrays para almacenar datos temporales en modo creacion
    let sucursalesTemp = [];
    let documentosTemp = [];
    let contadorSucursales = 0;
    let contadorDocumentos = 0;

    $(document).ready(function(){
        // Inicializamos Select2
        $('.select2').select2({theme: 'bootstrap-5', width: '100%'});

        // Ocultar asteriscos si es jurídica al cargar
        if ($('#tipo_cliente').val() === 'juridica') {
            $('.req-natural').hide();
        }

        // Al cambiar departamento, recargamos ciudades
        $('#departamento-select').on('change', function(){
            let depId = $(this).val();
            $('#ciudad-select').empty().append('<option>Buscando...</option>');
            if (!depId) {
                $('#ciudad-select').empty().append('<option value="">-- Seleccionar --</option>');
                return;
            }
            $.getJSON("{{ route('ajax.ciudades') }}", { departamento_id: depId })
             .done(function(data){
                 let $ciudad = $('#ciudad-select').empty().append('<option value="">-- Seleccionar --</option>');
                 data.forEach(c => {
                     $ciudad.append(`<option value="${c.id}">${c.nombre}</option>`);
                 });
                 $ciudad.trigger('change.select2');
             });
        });

        // Form Sucursal
        $('#formSucursal').on('submit', function(e) {
            e.preventDefault();
            guardarSucursal();
        });

        // Form Documento
        $('#formDocumento').on('submit', function(e) {
            e.preventDefault();
            subirDocumento();
        });
    });

    // Funciones Tipo Cliente
    function seleccionarTipoCliente(tipo) {
        $('#tipo_cliente').val(tipo);
        $('.tipo-cliente-card').removeClass('selected');
        $(`.tipo-cliente-card:has(i.bi-${tipo === 'natural' ? 'person' : 'building'})`).closest('.tipo-cliente-card').addClass('selected');

        if (tipo === 'juridica') {
            $('#juridicaFields').addClass('show');
            $('.req-natural').hide();
        } else {
            $('#juridicaFields').removeClass('show');
            $('.req-natural').show();
        }
    }

    // =========================================
    // Funciones Sucursales
    // =========================================
    function abrirModalSucursal() {
        $('#modalSucursalTitle').text('Nueva Sucursal');
        $('#formSucursal')[0].reset();
        $('#sucursal_id').val('');
        new bootstrap.Modal('#modalSucursal').show();
    }

    function editarSucursal(id, data) {
        $('#modalSucursalTitle').text('Editar Sucursal');
        $('#sucursal_id').val(id);
        $('#sucursal_nombre').val(data.nombre);
        $('#sucursal_direccion').val(data.direccion);
        $('#sucursal_ciudad').val(data.ciudad_id);
        $('#sucursal_telefono').val(data.telefono);
        $('#sucursal_email').val(data.email);
        $('#sucursal_contacto').val(data.contacto);
        $('#sucursal_principal').prop('checked', data.es_principal);
        new bootstrap.Modal('#modalSucursal').show();
    }

    function guardarSucursal() {
        const formData = {
            sucursal_id: $('#sucursal_id').val(),
            nombre: $('#sucursal_nombre').val(),
            direccion: $('#sucursal_direccion').val(),
            ciudad_id: $('#sucursal_ciudad').val(),
            telefono: $('#sucursal_telefono').val(),
            email: $('#sucursal_email').val(),
            contacto: $('#sucursal_contacto').val(),
            es_principal: $('#sucursal_principal').is(':checked') ? 1 : 0,
        };

        if (esEdicion) {
            // Modo edicion: guardar via AJAX
            formData._token = '{{ csrf_token() }}';
            $.post(`/clientes/${clienteId}/sucursales`, formData)
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Exito', response.message, 'success');
                        bootstrap.Modal.getInstance('#modalSucursal').hide();
                        location.reload();
                    }
                })
                .fail(function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error al guardar', 'error');
                });
        } else {
            // Modo creacion: guardar en array temporal
            const tempId = 'temp_' + (++contadorSucursales);
            formData.tempId = tempId;
            formData.ciudad_nombre = $('#sucursal_ciudad option:selected').text();

            sucursalesTemp.push(formData);
            actualizarSucursalesData();
            renderizarSucursalesTemp();

            bootstrap.Modal.getInstance('#modalSucursal').hide();
            Swal.fire({
                icon: 'success',
                title: 'Sucursal agregada',
                text: 'Se guardara cuando guarde el cliente',
                timer: 1500,
                showConfirmButton: false
            });
        }
    }

    function eliminarSucursal(id) {
        Swal.fire({
            title: 'Eliminar sucursal?',
            text: 'Esta accion no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Si, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                if (esEdicion) {
                    $.ajax({
                        url: `/sucursales/${id}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                $(`#sucursal-${id}`).fadeOut(300, function() { $(this).remove(); });
                                Swal.fire('Eliminado', response.message, 'success');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Error al eliminar', 'error');
                        }
                    });
                } else {
                    // Modo creacion: eliminar del array temporal
                    sucursalesTemp = sucursalesTemp.filter(s => s.tempId !== id);
                    actualizarSucursalesData();
                    $(`#sucursal-${id}`).fadeOut(300, function() { $(this).remove(); });
                    if (sucursalesTemp.length === 0) {
                        $('#listaSucursales').html('<p class="text-muted text-center" id="sinSucursales">No hay sucursales registradas.</p>');
                    }
                }
            }
        });
    }

    function editarSucursalTemp(tempId) {
        const sucursal = sucursalesTemp.find(s => s.tempId === tempId);
        if (sucursal) {
            $('#modalSucursalTitle').text('Editar Sucursal');
            $('#sucursal_id').val(tempId);
            $('#sucursal_nombre').val(sucursal.nombre);
            $('#sucursal_direccion').val(sucursal.direccion);
            $('#sucursal_ciudad').val(sucursal.ciudad_id);
            $('#sucursal_telefono').val(sucursal.telefono);
            $('#sucursal_email').val(sucursal.email);
            $('#sucursal_contacto').val(sucursal.contacto);
            $('#sucursal_principal').prop('checked', sucursal.es_principal);
            new bootstrap.Modal('#modalSucursal').show();
        }
    }

    function actualizarSucursalesData() {
        $('#sucursalesData').val(JSON.stringify(sucursalesTemp));
    }

    function renderizarSucursalesTemp() {
        $('#sinSucursales').remove();
        const container = $('#listaSucursales');

        // Remover sucursales temporales existentes
        container.find('[id^="sucursal-temp_"]').remove();

        sucursalesTemp.forEach(s => {
            const html = `
                <div class="sucursal-item" id="sucursal-${s.tempId}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                ${s.nombre}
                                ${s.es_principal ? '<span class="badge bg-success badge-principal ms-2">Principal</span>' : ''}
                                <span class="badge bg-warning ms-2">Pendiente</span>
                            </h6>
                            <p class="mb-1 text-muted small">
                                <i class="bi bi-geo-alt me-1"></i>${s.direccion}
                                ${s.ciudad_id ? ' - ' + s.ciudad_nombre : ''}
                            </p>
                            ${s.telefono || s.email ? `
                            <p class="mb-0 text-muted small">
                                ${s.telefono ? '<i class="bi bi-telephone me-1"></i>' + s.telefono : ''}
                                ${s.email ? '<i class="bi bi-envelope ms-2 me-1"></i>' + s.email : ''}
                            </p>` : ''}
                            ${s.contacto ? `<p class="mb-0 text-muted small"><i class="bi bi-person me-1"></i>Contacto: ${s.contacto}</p>` : ''}
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarSucursalTemp('${s.tempId}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarSucursal('${s.tempId}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.append(html);
        });
    }

    // =========================================
    // Funciones Documentos
    // =========================================
    function abrirModalDocumento() {
        $('#formDocumento')[0].reset();
        new bootstrap.Modal('#modalDocumento').show();
    }

    function subirDocumento() {
        const archivos = $('#documento_archivo')[0].files;
        const nombreBase = $('#documento_nombre').val();
        const tipo = $('#documento_tipo').val();

        if (archivos.length === 0) {
            Swal.fire('Error', 'Debe seleccionar al menos un archivo', 'error');
            return;
        }

        if (esEdicion) {
            // Modo edicion: subir via AJAX (uno por uno)
            let subidos = 0;
            const total = archivos.length;

            for (let i = 0; i < archivos.length; i++) {
                const formData = new FormData();
                const nombre = archivos.length === 1 ? nombreBase : `${nombreBase} (${i + 1})`;
                formData.append('nombre', nombre);
                formData.append('tipo', tipo);
                formData.append('documento', archivos[i]);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: `/clientes/${clienteId}/documentos`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        subidos++;
                        if (subidos === total) {
                            Swal.fire('Exito', `${total} documento(s) subido(s) correctamente`, 'success');
                            bootstrap.Modal.getInstance('#modalDocumento').hide();
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error al subir', 'error');
                    }
                });
            }
        } else {
            // Modo creacion: agregar al formulario y mostrar en lista
            const container = $('#documentosNuevosContainer');
            $('#sinDocumentos').remove();

            for (let i = 0; i < archivos.length; i++) {
                const tempId = 'doc_' + (++contadorDocumentos);
                const archivo = archivos[i];
                const nombre = archivos.length === 1 ? nombreBase : `${nombreBase} (${i + 1})`;

                // Agregar input file oculto con el archivo
                const inputFile = $(`<input type="file" name="documentos[]" style="display:none" id="file_${tempId}">`);
                const dt = new DataTransfer();
                dt.items.add(archivo);
                inputFile[0].files = dt.files;
                container.append(inputFile);

                // Agregar inputs para nombre y tipo
                container.append(`<input type="hidden" name="documentos_nombres[]" value="${nombre}" id="nombre_${tempId}">`);
                container.append(`<input type="hidden" name="documentos_tipos[]" value="${tipo}" id="tipo_${tempId}">`);

                // Determinar icono segun extension
                const extension = archivo.name.split('.').pop().toLowerCase();
                const icono = obtenerIcono(extension);

                // Agregar a la lista visual
                const html = `
                    <div class="documento-item" id="documento-${tempId}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi ${icono} fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-0">${nombre} <span class="badge bg-warning">Pendiente</span></h6>
                                    <small class="text-muted">
                                        ${archivo.name} | ${formatearTamano(archivo.size)}
                                    </small>
                                </div>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDocumentoTemp('${tempId}')" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $('#listaDocumentos').append(html);

                documentosTemp.push({ tempId, nombre, tipo, archivo: archivo.name });
            }

            bootstrap.Modal.getInstance('#modalDocumento').hide();
            Swal.fire({
                icon: 'success',
                title: `${archivos.length} documento(s) agregado(s)`,
                text: 'Se guardaran cuando guarde el cliente',
                timer: 1500,
                showConfirmButton: false
            });
        }
    }

    function eliminarDocumento(id) {
        Swal.fire({
            title: 'Eliminar documento?',
            text: 'El archivo sera eliminado permanentemente',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Si, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/documentos-cliente/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $(`#documento-${id}`).fadeOut(300, function() { $(this).remove(); });
                            Swal.fire('Eliminado', response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error al eliminar', 'error');
                    }
                });
            }
        });
    }

    function eliminarDocumentoTemp(tempId) {
        Swal.fire({
            title: 'Eliminar documento?',
            text: 'El archivo sera removido de la lista',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Si, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Remover inputs del formulario
                $(`#file_${tempId}, #nombre_${tempId}, #tipo_${tempId}`).remove();
                // Remover de la lista visual
                $(`#documento-${tempId}`).fadeOut(300, function() { $(this).remove(); });
                // Remover del array
                documentosTemp = documentosTemp.filter(d => d.tempId !== tempId);

                if (documentosTemp.length === 0 && !esEdicion) {
                    $('#listaDocumentos').html('<p class="text-muted text-center" id="sinDocumentos">No hay documentos cargados.</p>');
                }
            }
        });
    }

    // Utilidades
    function obtenerIcono(extension) {
        const iconos = {
            'pdf': 'bi-file-earmark-pdf text-danger',
            'doc': 'bi-file-earmark-word text-primary',
            'docx': 'bi-file-earmark-word text-primary',
            'xls': 'bi-file-earmark-excel text-success',
            'xlsx': 'bi-file-earmark-excel text-success',
            'ppt': 'bi-file-earmark-ppt text-warning',
            'pptx': 'bi-file-earmark-ppt text-warning',
            'jpg': 'bi-file-earmark-image text-info',
            'jpeg': 'bi-file-earmark-image text-info',
            'png': 'bi-file-earmark-image text-info',
            'gif': 'bi-file-earmark-image text-info',
            'zip': 'bi-file-earmark-zip text-secondary',
            'rar': 'bi-file-earmark-zip text-secondary',
            'txt': 'bi-file-earmark-text text-dark',
        };
        return iconos[extension] || 'bi-file-earmark text-secondary';
    }

    function formatearTamano(bytes) {
        if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + ' MB';
        } else if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + ' KB';
        }
        return bytes + ' bytes';
    }
    </script>
    @endpush
</x-app-layout>
