@extends('layouts.app')

@section('title', 'Gestión de Trabajadores')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de Trabajadores</h1>
            <p class="text-muted mb-0">Administra el personal de la empresa</p>
        </div>
        @can('crear_trabajadores')
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTrabajadorModal">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Trabajador
        </button>
        @endcan
    </div>

    <!-- Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-people-fill text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $trabajadores->count() }}</h3>
                            <small class="text-muted">Total Trabajadores</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $trabajadores->where('activo', true)->count() }}</h3>
                            <small class="text-muted">Activos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @unlessrole('Encargado')
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-building text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $trabajadores->where('tipo_relacion', 'propio')->count() }}</h3>
                            <small class="text-muted">Propios</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-diagram-3 text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $trabajadores->where('tipo_relacion', 'subcontrata')->count() }}</h3>
                            <small class="text-muted">Subcontratas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endunlessrole
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('trabajadores.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nombre, apellidos o DNI..." value="{{ request('search') }}">
                </div>
                @unlessrole('Encargado')
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select name="tipo_relacion" class="form-select">
                        <option value="">Todos</option>
                        <option value="propio" {{ request('tipo_relacion') == 'propio' ? 'selected' : '' }}>Propio</option>
                        <option value="subcontrata" {{ request('tipo_relacion') == 'subcontrata' ? 'selected' : '' }}>Subcontrata</option>
                    </select>
                </div>
                @endunlessrole
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="activo" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                @unlessrole('Encargado')
                <div class="col-md-2">
                    <label class="form-label">Subcontrata</label>
                    <select name="subcontrata_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($subcontratas as $subcontrata)
                        <option value="{{ $subcontrata->id }}" {{ request('subcontrata_id') == $subcontrata->id ? 'selected' : '' }}>
                            {{ $subcontrata->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endunlessrole
                <div class="col-md-2">
                    <label class="form-label">Cuadrilla</label>
                    <select name="cuadrilla_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($cuadrillas as $cuadrilla)
                        <option value="{{ $cuadrilla->id }}" {{ request('cuadrilla_id') == $cuadrilla->id ? 'selected' : '' }}>
                            {{ $cuadrilla->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Trabajadores -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="trabajadoresTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Trabajador</th>
                            <th>DNI</th>
                            @unlessrole('Encargado')
                            <th>Tipo</th>
                            @endunlessrole
                            <th>Cuadrilla</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trabajadores as $trabajador)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    @if($trabajador->hasProfilePhoto())
                                        <img src="{{ $trabajador->profile_photo_url }}"
                                             alt="{{ $trabajador->nombre_completo }}"
                                             class="rounded-circle me-3"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-{{ auth()->user()->hasRole('Encargado') ? 'secondary' : ($trabajador->tipo_relacion === 'propio' ? 'primary' : 'warning') }} text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            {{ $trabajador->initials }}
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $trabajador->nombre_completo }}</h6>
                                        <small class="text-muted">{{ $trabajador->categoria_convenio ?? 'Sin categoría' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $trabajador->dni }}</td>
                            @unlessrole('Encargado')
                            <td>
                                @if($trabajador->tipo_relacion === 'propio')
                                    <span class="badge bg-primary-subtle text-primary">Propio</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">
                                        {{ $trabajador->subcontrata?->nombre ?? 'Subcontrata' }}
                                    </span>
                                @endif
                            </td>
                            @endunlessrole
                            <td>
                                @php $cuadrillaActual = $trabajador->cuadrillas->where('pivot.activo', true)->first(); @endphp
                                @if($cuadrillaActual)
                                    <span class="badge bg-info-subtle text-info">{{ $cuadrillaActual->nombre }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($trabajador->telefono)
                                    <small><i class="bi bi-telephone me-1"></i>{{ $trabajador->telefono }}</small><br>
                                @endif
                                @if($trabajador->email)
                                    <small><i class="bi bi-envelope me-1"></i>{{ $trabajador->email }}</small>
                                @endif
                            </td>
                            <td>
                                @if($trabajador->activo)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>Activo
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="bi bi-x-circle me-1"></i>Baja
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('trabajadores.show', $trabajador) }}" class="btn btn-sm btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('editar_trabajadores')
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="editTrabajador({{ $trabajador->id }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @endcan
                                    @can('eliminar_trabajadores')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteTrabajador({{ $trabajador->id }}, '{{ $trabajador->nombre_completo }}')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('Encargado') ? '6' : '7' }}" class="text-center py-4 text-muted">
                                No hay trabajadores que mostrar
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Trabajador -->
<div class="modal fade" id="createTrabajadorModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('trabajadores.store') }}" method="POST" id="createTrabajadorForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Trabajador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        @unlessrole('Encargado')
                        <!-- Tipo de relación -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Relación <span class="text-danger">*</span></label>
                            <select name="tipo_relacion" id="createTipoRelacion" class="form-select" required onchange="toggleSubcontrata('create')">
                                <option value="propio">Propio</option>
                                <option value="subcontrata">Subcontrata</option>
                            </select>
                        </div>

                        <!-- Subcontrata (solo si es subcontrata) -->
                        <div class="col-md-6" id="createSubcontrataContainer" style="display: none;">
                            <label class="form-label">Subcontrata <span class="text-danger">*</span></label>
                            <select name="subcontrata_id" id="createSubcontrataId" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($subcontratas as $subcontrata)
                                    <option value="{{ $subcontrata->id }}">{{ $subcontrata->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="tipo_relacion" value="propio">
                        @endunlessrole

                        <!-- Datos personales -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">DATOS PERSONALES</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">DNI <span class="text-danger">*</span></label>
                            <input type="text" name="dni" class="form-control" required placeholder="12345678A">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="600000000">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Fecha Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" class="form-control">
                        </div>

                        <!-- Datos laborales -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">DATOS LABORALES</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Fecha Alta <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_alta" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Categoría Convenio</label>
                            <input type="text" name="categoria_convenio" class="form-control" placeholder="Oficial 1ª, Peón...">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Cuadrilla</label>
                            <select name="cuadrilla_id" class="form-select">
                                <option value="">Sin asignar</option>
                                @foreach($cuadrillas as $cuadrilla)
                                    <option value="{{ $cuadrilla->id }}">{{ $cuadrilla->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        @unlessrole('Encargado')
                        <!-- Datos económicos (solo para propios) -->
                        <div class="col-12" id="createDatosEconomicos">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">DATOS ECONÓMICOS</small>
                        </div>

                        <div class="col-md-3" id="createSalarioContainer">
                            <label class="form-label">Salario Bruto Mensual</label>
                            <div class="input-group">
                                <input type="number" name="salario_bruto_mensual" class="form-control" step="0.01" min="0">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="col-md-3" id="createCosteDiaContainer">
                            <label class="form-label">Coste Empresa/Día</label>
                            <div class="input-group">
                                <input type="number" name="coste_empresa_dia" class="form-control" step="0.01" min="0">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="col-md-3" id="createCosteHoraContainer">
                            <label class="form-label">Coste/Hora</label>
                            <div class="input-group">
                                <input type="number" name="coste_hora" class="form-control" step="0.01" min="0">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="col-md-3" id="createVacacionesContainer">
                            <label class="form-label">Vacaciones Anuales</label>
                            <div class="input-group">
                                <input type="number" name="vacaciones_anuales" class="form-control" value="22" min="0">
                                <span class="input-group-text">días</span>
                            </div>
                        </div>
                        @endunlessrole
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Trabajador</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Trabajador -->
<div class="modal fade" id="editTrabajadorModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="editTrabajadorForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Trabajador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        @unlessrole('Encargado')
                        <!-- Tipo de relación -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Relación <span class="text-danger">*</span></label>
                            <select name="tipo_relacion" id="editTipoRelacion" class="form-select" required onchange="toggleSubcontrata('edit')">
                                <option value="propio">Propio</option>
                                <option value="subcontrata">Subcontrata</option>
                            </select>
                        </div>

                        <!-- Subcontrata -->
                        <div class="col-md-6" id="editSubcontrataContainer" style="display: none;">
                            <label class="form-label">Subcontrata <span class="text-danger">*</span></label>
                            <select name="subcontrata_id" id="editSubcontrataId" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($subcontratas as $subcontrata)
                                    <option value="{{ $subcontrata->id }}">{{ $subcontrata->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endunlessrole

                        <!-- Datos personales -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">DATOS PERSONALES</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="editNombre" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" id="editApellidos" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">DNI <span class="text-danger">*</span></label>
                            <input type="text" name="dni" id="editDni" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="editTelefono" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Fecha Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="editFechaNacimiento" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" id="editDireccion" class="form-control">
                        </div>

                        <!-- Datos laborales -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">DATOS LABORALES</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Fecha Alta <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_alta" id="editFechaAlta" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Categoría Convenio</label>
                            <input type="text" name="categoria_convenio" id="editCategoriaConvenio" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Cuadrilla</label>
                            <select name="cuadrilla_id" id="editCuadrillaId" class="form-select">
                                <option value="">Sin asignar</option>
                                @foreach($cuadrillas as $cuadrilla)
                                    <option value="{{ $cuadrilla->id }}">{{ $cuadrilla->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        @unlessrole('Encargado')
                        <!-- Datos económicos -->
                        <div class="col-12" id="editDatosEconomicos">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">DATOS ECONÓMICOS</small>
                        </div>

                        <div class="col-md-3" id="editSalarioContainer">
                            <label class="form-label">Salario Bruto Mensual</label>
                            <div class="input-group">
                                <input type="number" name="salario_bruto_mensual" id="editSalarioBrutoMensual" class="form-control" step="0.01" min="0">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="col-md-3" id="editCosteDiaContainer">
                            <label class="form-label">Coste Empresa/Día</label>
                            <div class="input-group">
                                <input type="number" name="coste_empresa_dia" id="editCosteEmpresaDia" class="form-control" step="0.01" min="0">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="col-md-3" id="editCosteHoraContainer">
                            <label class="form-label">Coste/Hora</label>
                            <div class="input-group">
                                <input type="number" name="coste_hora" id="editCosteHora" class="form-control" step="0.01" min="0">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="col-md-3" id="editVacacionesContainer">
                            <label class="form-label">Vacaciones Anuales</label>
                            <div class="input-group">
                                <input type="number" name="vacaciones_anuales" id="editVacacionesAnuales" class="form-control" min="0">
                                <span class="input-group-text">días</span>
                            </div>
                        </div>
                        @endunlessrole

                        <!-- Estado -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">ESTADO</small>
                        </div>

                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" id="editActivo" value="1">
                                <label class="form-check-label" for="editActivo">Trabajador Activo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Eliminar -->
<form id="deleteTrabajadorForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<style>
.modal.show {
    display: block !important;
}
.modal-dialog.modal-lg {
    max-width: 800px !important;
    width: 800px !important;
}
.modal-dialog {
    margin: 1.75rem auto !important;
    position: relative !important;
    transform: none !important;
}
.modal.show .modal-dialog {
    transform: none !important;
}
.modal-content {
    width: 100% !important;
    position: relative !important;
}
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5) !important;
}
</style>
@endpush

@push('scripts')
<script>
function toggleSubcontrata(prefix) {
    const tipoRelacion = document.getElementById(prefix + 'TipoRelacion').value;
    const subcontrataContainer = document.getElementById(prefix + 'SubcontrataContainer');
    const datosEconomicos = document.getElementById(prefix + 'DatosEconomicos');
    const salarioContainer = document.getElementById(prefix + 'SalarioContainer');
    const costeDiaContainer = document.getElementById(prefix + 'CosteDiaContainer');
    const costeHoraContainer = document.getElementById(prefix + 'CosteHoraContainer');
    const vacacionesContainer = document.getElementById(prefix + 'VacacionesContainer');

    if (tipoRelacion === 'subcontrata') {
        subcontrataContainer.style.display = 'block';
        // Ocultar datos económicos para subcontratas
        if (datosEconomicos) datosEconomicos.style.display = 'none';
        if (salarioContainer) salarioContainer.style.display = 'none';
        if (costeDiaContainer) costeDiaContainer.style.display = 'none';
        if (costeHoraContainer) costeHoraContainer.style.display = 'none';
        if (vacacionesContainer) vacacionesContainer.style.display = 'none';
    } else {
        subcontrataContainer.style.display = 'none';
        // Mostrar datos económicos para propios
        if (datosEconomicos) datosEconomicos.style.display = 'block';
        if (salarioContainer) salarioContainer.style.display = 'block';
        if (costeDiaContainer) costeDiaContainer.style.display = 'block';
        if (costeHoraContainer) costeHoraContainer.style.display = 'block';
        if (vacacionesContainer) vacacionesContainer.style.display = 'block';
    }
}

function editTrabajador(trabajadorId) {
    fetch(`{{ url('trabajadores') }}/${trabajadorId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('editTipoRelacion').value = data.tipo_relacion;
        document.getElementById('editSubcontrataId').value = data.subcontrata_id || '';
        document.getElementById('editNombre').value = data.nombre;
        document.getElementById('editApellidos').value = data.apellidos;
        document.getElementById('editDni').value = data.dni;
        document.getElementById('editEmail').value = data.email || '';
        document.getElementById('editTelefono').value = data.telefono || '';
        document.getElementById('editFechaNacimiento').value = data.fecha_nacimiento || '';
        document.getElementById('editDireccion').value = data.direccion || '';
        document.getElementById('editFechaAlta').value = data.fecha_alta || '';
        document.getElementById('editCategoriaConvenio').value = data.categoria_convenio || '';
        document.getElementById('editCuadrillaId').value = data.cuadrilla_id || '';
        document.getElementById('editSalarioBrutoMensual').value = data.salario_bruto_mensual || '';
        document.getElementById('editCosteEmpresaDia').value = data.coste_empresa_dia || '';
        document.getElementById('editCosteHora').value = data.coste_hora || '';
        document.getElementById('editVacacionesAnuales').value = data.vacaciones_anuales || '';
        document.getElementById('editActivo').checked = data.activo;

        toggleSubcontrata('edit');

        document.getElementById('editTrabajadorForm').action = `{{ url('trabajadores') }}/${trabajadorId}`;
        new bootstrap.Modal(document.getElementById('editTrabajadorModal')).show();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo cargar el trabajador', 'error');
    });
}

function deleteTrabajador(trabajadorId, trabajadorNombre) {
    Swal.fire({
        title: '¿Eliminar trabajador?',
        text: `¿Estás seguro de eliminar a "${trabajadorNombre}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteTrabajadorForm');
            form.action = `{{ url('trabajadores') }}/${trabajadorId}`;
            form.submit();
        }
    });
}

// Inicializar estado de modales
document.addEventListener('DOMContentLoaded', function() {
    toggleSubcontrata('create');
});
</script>
@endpush
@endsection
