@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" id="ordenWizardApp">

    {{-- Sticky Wizard Header --}}
    <div class="wizard-header-sticky" id="wizardHeaderSticky">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2" style="white-space: nowrap;">
                <a href="{{ route('recepcion.ordenes.show', $orden) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h1 class="page-title mb-0">Editar Orden {{ $orden->numero_orden ?? '(Borrador #' . $orden->id . ')' }}</h1>
            </div>
            <div class="wizard-steps mb-0">
                <div class="wizard-step active" data-step="1" onclick="irASeccion(1)">
                    <i class="bi bi-person"></i> <span class="d-none d-md-inline">Cliente</span>
                </div>
                <div class="wizard-step" data-step="2" onclick="irASeccion(2)">
                    <i class="bi bi-puzzle"></i> <span class="d-none d-md-inline">Piezas</span>
                </div>
                <div class="wizard-step" data-step="3" onclick="irASeccion(3)">
                    <i class="bi bi-cart3"></i> <span class="d-none d-md-inline">Items</span>
                </div>
                <div class="wizard-step" data-step="4" onclick="irASeccion(4)">
                    <i class="bi bi-pen"></i> <span class="d-none d-md-inline">Firma</span>
                </div>
                <div class="wizard-step" data-step="5" onclick="irASeccion(5)">
                    <i class="bi bi-person-gear"></i> <span class="d-none d-md-inline">Operario</span>
                </div>
                <div class="wizard-step" data-step="6" onclick="irASeccion(6)">
                    <i class="bi bi-cash-coin"></i> <span class="d-none d-md-inline">Pagos</span>
                </div>
                <div class="wizard-step" data-step="7" onclick="irASeccion(7)">
                    <i class="bi bi-calendar3"></i> <span class="d-none d-md-inline">Fechas</span>
                </div>
            </div>
            <span id="autoguardadoIndicator" class="text-muted small" style="display:none;">
                <i class="bi bi-check-circle text-success me-1"></i><span id="autoguardadoTexto"></span>
            </span>
        </div>
    </div>

    {{-- Hidden order ID --}}
    <input type="hidden" id="orden_id" value="{{ $orden->id }}">

    {{-- 7 Secciones (same partials as create) --}}
    @include('ordenes.partials._seccion-cliente')
    @include('ordenes.partials._seccion-bosquejos-piezas')
    @include('ordenes.partials._seccion-items')
    @include('ordenes.partials._seccion-firma')
    @include('ordenes.partials._seccion-operario')
    @include('ordenes.partials._seccion-pagos')
    @include('ordenes.partials._seccion-fechas')

    {{-- Botones de accion --}}
    <div class="d-flex justify-content-end gap-2 mb-4">
        @if($orden->estado_trabajo === 'borrador')
            <button type="button" class="btn btn-outline-primary btn-lg" id="btnGuardar" onclick="guardarOrden(false)">
                <i class="bi bi-save me-1"></i> Guardar Borrador
            </button>
            <button type="button" class="btn btn-primary btn-lg" id="btnGenerar" onclick="generarOrden()">
                <i class="bi bi-check-circle me-1"></i> Generar Orden
            </button>
        @else
            <button type="button" class="btn btn-primary btn-lg" id="btnGuardar" onclick="guardarOrden(false)">
                <i class="bi bi-save me-1"></i> Guardar Cambios
            </button>
        @endif
    </div>

</div>

{{-- ====================================== --}}
{{-- MODALES (same as create) --}}
{{-- ====================================== --}}

{{-- Modal: Crear Cliente Inline --}}
<div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold"><i class="bi bi-person-plus me-2 text-primary"></i>Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium">Nombre <span class="text-danger">*</span></label>
                        <input type="text" id="nuevoClienteNombre" class="form-control" placeholder="Nombre completo del cliente">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Celular Principal</label>
                        <input type="text" id="nuevoClienteCelular1" class="form-control" placeholder="Ej: 3001234567">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Celular Secundario</label>
                        <input type="text" id="nuevoClienteCelular2" class="form-control" placeholder="Opcional">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Correo</label>
                        <input type="email" id="nuevoClienteCorreo" class="form-control" placeholder="correo@ejemplo.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Direccion</label>
                        <input type="text" id="nuevoClienteDireccion" class="form-control" placeholder="Direccion del cliente">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="crearClienteInline()">
                    <i class="bi bi-check-lg me-1"></i> Crear y Seleccionar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Seleccionar Bosquejo de Matriz --}}
<div class="modal fade" id="modalBosquejoMatriz" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold"><i class="bi bi-grid-3x3 me-2 text-primary"></i>Seleccionar de Matriz de Bosquejos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3" id="matrizBosquejosContent">
                @forelse($gruposBosquejos as $grupo)
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-semibold mb-0">
                                <i class="bi bi-folder me-1 text-warning"></i> {{ $grupo->nombre }}
                                <span class="badge bg-light text-muted border ms-1">{{ $grupo->plantillas->count() }}</span>
                            </h6>
                            @if($grupo->plantillas->count() > 0)
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertarGrupoCompleto({{ $grupo->id }})">
                                    <i class="bi bi-folder-plus me-1"></i> Insertar Grupo ({{ $grupo->plantillas->count() }} piezas)
                                </button>
                            @endif
                        </div>
                        <div class="row g-2">
                            @foreach($grupo->plantillas as $plantilla)
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                    <div class="card border cursor-pointer plantilla-card"
                                         onclick="seleccionarPlantillaMatriz({{ $plantilla->id }}, '{{ addslashes($plantilla->nombre) }}', '{{ $plantilla->ruta_archivo }}', '{{ $plantilla->ruta_miniatura ?: $plantilla->ruta_archivo }}')">
                                        <img src="{{ asset($plantilla->ruta_miniatura ?: $plantilla->ruta_archivo) }}"
                                             class="card-img-top" alt="{{ $plantilla->nombre }}"
                                             style="height:100px; object-fit:cover;">
                                        <div class="card-body p-2 text-center">
                                            <small class="text-truncate d-block">{{ $plantilla->nombre }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                @endforelse

                @if(isset($bosquejosSueltos) && $bosquejosSueltos->count() > 0)
                    <div class="mb-3">
                        <h6 class="fw-semibold mb-2">
                            <i class="bi bi-image me-1 text-info"></i> Bosquejos Individuales
                            <span class="badge bg-light text-muted border ms-1">{{ $bosquejosSueltos->count() }}</span>
                        </h6>
                        <div class="row g-2">
                            @foreach($bosquejosSueltos as $plantilla)
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                    <div class="card border cursor-pointer plantilla-card"
                                         onclick="seleccionarPlantillaMatriz({{ $plantilla->id }}, '{{ addslashes($plantilla->nombre) }}', '{{ $plantilla->ruta_archivo }}', '{{ $plantilla->ruta_miniatura ?: $plantilla->ruta_archivo }}')">
                                        <img src="{{ asset($plantilla->ruta_miniatura ?: $plantilla->ruta_archivo) }}"
                                             class="card-img-top" alt="{{ $plantilla->nombre }}"
                                             style="height:100px; object-fit:cover;">
                                        <div class="card-body p-2 text-center">
                                            <small class="text-truncate d-block">{{ $plantilla->nombre }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($gruposBosquejos->count() === 0 && (!isset($bosquejosSueltos) || $bosquejosSueltos->count() === 0))
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                        <p>No hay bosquejos en la matriz.</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Dibujo Tablet --}}
<div class="modal fade" id="modalDibujoTablet" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold"><i class="bi bi-pencil-square me-2 text-primary"></i>Dibujar Bosquejo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3 pb-0 px-0">
                <div class="d-flex gap-2 mb-2 flex-wrap px-3">
                    <button type="button" class="btn btn-sm btn-dark" onclick="cambiarColorDibujo('#000000')">Negro</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="cambiarColorDibujo('#dc3545')">Rojo</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="cambiarColorDibujo('#0d6efd')">Azul</button>
                    <button type="button" class="btn btn-sm btn-success" onclick="cambiarColorDibujo('#198754')">Verde</button>
                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="cambiarColorDibujo('#ffffff')" style="background: #ffffff;">Blanco</button>
                    <span class="vr mx-1"></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cambiarGrosorDibujo(1)">Ultra Fino</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cambiarGrosorDibujo(2)">Fino</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cambiarGrosorDibujo(4)">Medio</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cambiarGrosorDibujo(8)">Grueso</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cambiarGrosorDibujo(20)">Ultra Grueso</button>
                    <span class="vr mx-1"></span>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="deshacerDibujo()"><i class="bi bi-arrow-counterclockwise"></i> Deshacer</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="limpiarDibujo()"><i class="bi bi-eraser"></i> Limpiar</button>
                </div>
                <canvas id="dibujoCanvas" width="900" height="500" style="border: 1px solid #dee2e6; background: white; cursor: crosshair; width: 100%; display: block;"></canvas>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarDibujo" onclick="guardarDibujoComoImagen()">
                    <i class="bi bi-save me-1"></i> Guardar Dibujo
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .plantilla-card { transition: transform 0.15s, box-shadow 0.15s; }
    .plantilla-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .cursor-pointer { cursor: pointer; }
    .border-dashed { border-style: dashed !important; }
    .firma-existente { max-height: 120px; border: 1px solid #dee2e6; border-radius: 0.375rem; }
</style>
@endpush

@push('scripts')
<script>
// Datos del servidor para JS
var WIZARD_CONFIG = {
    materiales: @json($materiales),
    calibres: @json($calibres),
    ivaDefecto: {{ $ivaDefecto }},
    autoSaveInterval: {{ $autoSaveInterval }},
    csrfToken: '{{ csrf_token() }}'
};

var ROUTES = {
    guardar: '{{ route("recepcion.ordenes.update", $orden) }}',
    generar: '{{ $orden->estado_trabajo === "borrador" ? route("recepcion.ordenes.generar") : route("recepcion.ordenes.update", $orden) }}',
    subirBosquejo: '{{ route("recepcion.ordenes.subir-bosquejo") }}',
    crearCliente: '{{ route("recepcion.ordenes.crear-cliente-inline") }}',
    clienteAutocomplete: '{{ route("recepcion.clientes.autocomplete") }}',
    itemAutocomplete: '{{ route("recepcion.items.autocomplete") }}',
    panel: '{{ route("recepcion.ordenes.show", $orden) }}'
};

// Modo edicion
var EDIT_MODE = true;
var ORDEN_DATA = @json($ordenData);
</script>
<script src="{{ asset('js/firma-canvas.js') }}"></script>
<script src="{{ asset('js/dibujo-canvas.js') }}"></script>
<script src="{{ asset('js/orden-wizard.js') }}"></script>
<script src="{{ asset('js/orden-edit-init.js') }}"></script>
@endpush
