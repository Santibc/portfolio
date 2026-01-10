@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de Clientes</h1>
            <p class="text-muted mb-0">Administra los clientes de la empresa</p>
        </div>
        @can('crear_clientes')
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClienteModal">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Cliente
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
                            <i class="bi bi-building text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total Clientes</small>
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
                            <h3 class="mb-0">{{ $stats['activos'] }}</h3>
                            <small class="text-muted">Activos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-bank text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['publicos'] }}</h3>
                            <small class="text-muted">Públicos</small>
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
                            <i class="bi bi-briefcase text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['privados'] }}</h3>
                            <small class="text-muted">Privados</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('clientes.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nombre, razón social, CIF..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="publico" {{ request('tipo') == 'publico' ? 'selected' : '' }}>Público</option>
                        <option value="privado" {{ request('tipo') == 'privado' ? 'selected' : '' }}>Privado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="activo" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Provincia</label>
                    <select name="provincia" class="form-select">
                        <option value="">Todas</option>
                        @foreach($provincias as $provincia)
                        <option value="{{ $provincia }}" {{ request('provincia') == $provincia ? 'selected' : '' }}>
                            {{ $provincia }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Clientes -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="clientesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Cliente</th>
                            <th>CIF</th>
                            <th>Tipo</th>
                            <th>Contacto</th>
                            <th>Ubicación</th>
                            <th class="text-center">Obras</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-{{ $cliente->tipo === 'publico' ? 'info' : 'warning' }} text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-{{ $cliente->tipo === 'publico' ? 'bank' : 'briefcase' }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $cliente->nombre_comercial }}</h6>
                                        @if($cliente->razon_social && $cliente->razon_social !== $cliente->nombre_comercial)
                                            <small class="text-muted">{{ $cliente->razon_social }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $cliente->cif ?? '-' }}</td>
                            <td>
                                @if($cliente->tipo === 'publico')
                                    <span class="badge bg-info-subtle text-info">Público</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">Privado</span>
                                @endif
                            </td>
                            <td>
                                @if($cliente->persona_contacto)
                                    <small><i class="bi bi-person me-1"></i>{{ $cliente->persona_contacto }}</small><br>
                                @endif
                                @if($cliente->telefono)
                                    <small><i class="bi bi-telephone me-1"></i>{{ $cliente->telefono }}</small>
                                @endif
                            </td>
                            <td>
                                @if($cliente->ciudad || $cliente->provincia)
                                    <small>{{ $cliente->ciudad }}{{ $cliente->ciudad && $cliente->provincia ? ', ' : '' }}{{ $cliente->provincia }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($cliente->obras_count > 0)
                                    <span class="badge bg-primary">{{ $cliente->obras_count }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>
                                @if($cliente->activo)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>Activo
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="bi bi-x-circle me-1"></i>Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('editar_clientes')
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="editCliente({{ $cliente->id }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @endcan
                                    @can('eliminar_clientes')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteCliente({{ $cliente->id }}, '{{ $cliente->nombre_comercial }}')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No hay clientes que mostrar
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Cliente -->
<div class="modal fade" id="createClienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('clientes.store') }}" method="POST" id="createClienteForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Tipo de cliente -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Cliente <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select" required>
                                <option value="privado">Privado</option>
                                <option value="publico">Público</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">CIF</label>
                            <input type="text" name="cif" class="form-control" placeholder="B12345678">
                        </div>

                        <!-- Datos de la empresa -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">DATOS DE LA EMPRESA</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nombre Comercial <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_comercial" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Razón Social</label>
                            <input type="text" name="razon_social" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="900000000">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <!-- Dirección -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">DIRECCIÓN</small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Código Postal</label>
                            <input type="text" name="codigo_postal" class="form-control" placeholder="08001">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Provincia</label>
                            <input type="text" name="provincia" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">País</label>
                            <input type="text" name="pais" class="form-control" value="España">
                        </div>

                        <!-- Persona de contacto -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">PERSONA DE CONTACTO</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="persona_contacto" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono_contacto" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email_contacto" class="form-control">
                        </div>

                        <!-- Condiciones comerciales -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">CONDICIONES COMERCIALES</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Condiciones de Pago</label>
                            <select name="condiciones_pago" class="form-select">
                                <option value="">Seleccionar...</option>
                                <option value="Contado">Contado</option>
                                <option value="15 días">15 días</option>
                                <option value="30 días">30 días</option>
                                <option value="45 días">45 días</option>
                                <option value="60 días">60 días</option>
                                <option value="90 días">90 días</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Retención (%)</label>
                            <div class="input-group">
                                <input type="number" name="retencion_porcentaje" class="form-control" step="0.01" min="0" max="100" value="0">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea name="notas" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Cliente -->
<div class="modal fade" id="editClienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="editClienteForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Tipo de cliente -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Cliente <span class="text-danger">*</span></label>
                            <select name="tipo" id="editTipo" class="form-select" required>
                                <option value="privado">Privado</option>
                                <option value="publico">Público</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">CIF</label>
                            <input type="text" name="cif" id="editCif" class="form-control">
                        </div>

                        <!-- Datos de la empresa -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">DATOS DE LA EMPRESA</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nombre Comercial <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_comercial" id="editNombreComercial" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Razón Social</label>
                            <input type="text" name="razon_social" id="editRazonSocial" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="editTelefono" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control">
                        </div>

                        <!-- Dirección -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">DIRECCIÓN</small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" id="editDireccion" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Código Postal</label>
                            <input type="text" name="codigo_postal" id="editCodigoPostal" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" id="editCiudad" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Provincia</label>
                            <input type="text" name="provincia" id="editProvincia" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">País</label>
                            <input type="text" name="pais" id="editPais" class="form-control">
                        </div>

                        <!-- Persona de contacto -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">PERSONA DE CONTACTO</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="persona_contacto" id="editPersonaContacto" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono_contacto" id="editTelefonoContacto" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email_contacto" id="editEmailContacto" class="form-control">
                        </div>

                        <!-- Condiciones comerciales -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">CONDICIONES COMERCIALES</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Condiciones de Pago</label>
                            <select name="condiciones_pago" id="editCondicionesPago" class="form-select">
                                <option value="">Seleccionar...</option>
                                <option value="Contado">Contado</option>
                                <option value="15 días">15 días</option>
                                <option value="30 días">30 días</option>
                                <option value="45 días">45 días</option>
                                <option value="60 días">60 días</option>
                                <option value="90 días">90 días</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Retención (%)</label>
                            <div class="input-group">
                                <input type="number" name="retencion_porcentaje" id="editRetencionPorcentaje" class="form-control" step="0.01" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea name="notas" id="editNotas" class="form-control" rows="2"></textarea>
                        </div>

                        <!-- Estado -->
                        <div class="col-12">
                            <hr class="my-2">
                            <small class="text-muted fw-semibold">ESTADO</small>
                        </div>

                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" id="editActivo" value="1">
                                <label class="form-check-label" for="editActivo">Cliente Activo</label>
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
<form id="deleteClienteForm" method="POST" class="d-none">
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
function editCliente(clienteId) {
    fetch(`{{ url('clientes') }}/${clienteId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('editTipo').value = data.tipo;
        document.getElementById('editCif').value = data.cif || '';
        document.getElementById('editNombreComercial').value = data.nombre_comercial;
        document.getElementById('editRazonSocial').value = data.razon_social || '';
        document.getElementById('editTelefono').value = data.telefono || '';
        document.getElementById('editEmail').value = data.email || '';
        document.getElementById('editDireccion').value = data.direccion || '';
        document.getElementById('editCodigoPostal').value = data.codigo_postal || '';
        document.getElementById('editCiudad').value = data.ciudad || '';
        document.getElementById('editProvincia').value = data.provincia || '';
        document.getElementById('editPais').value = data.pais || 'España';
        document.getElementById('editPersonaContacto').value = data.persona_contacto || '';
        document.getElementById('editTelefonoContacto').value = data.telefono_contacto || '';
        document.getElementById('editEmailContacto').value = data.email_contacto || '';
        document.getElementById('editCondicionesPago').value = data.condiciones_pago || '';
        document.getElementById('editRetencionPorcentaje').value = data.retencion_porcentaje || 0;
        document.getElementById('editNotas').value = data.notas || '';
        document.getElementById('editActivo').checked = data.activo;

        document.getElementById('editClienteForm').action = `{{ url('clientes') }}/${clienteId}`;
        new bootstrap.Modal(document.getElementById('editClienteModal')).show();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo cargar el cliente', 'error');
    });
}

function deleteCliente(clienteId, clienteNombre) {
    Swal.fire({
        title: '¿Eliminar cliente?',
        text: `¿Estás seguro de eliminar "${clienteNombre}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteClienteForm');
            form.action = `{{ url('clientes') }}/${clienteId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
