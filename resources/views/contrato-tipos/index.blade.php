@extends('layouts.app')

@section('title', 'Tipos de Contrato')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Tipos de Contrato</h1>
            <p class="text-muted mb-0">Gestión de tipos para clasificar contratos</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTipo" onclick="abrirModalNuevo()">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Tipo
        </button>
    </div>

    {{-- Alertas de sesión --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-collection text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total Tipos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-file-earmark-text text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['contratos_total'] }}</h3>
                            <small class="text-muted">Contratos Asociados</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de tipos --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Listado de Tipos de Contrato</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th class="text-center">Contratos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tipos as $tipo)
                    <tr>
                        <td>
                            <strong>{{ $tipo->nombre }}</strong>
                        </td>
                        <td>
                            @if($tipo->descripcion)
                                <span class="text-muted">{{ Str::limit($tipo->descripcion, 50) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $tipo->contratos_count }}</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="abrirModalEditar({{ $tipo->id }}, '{{ addslashes($tipo->nombre) }}', '{{ addslashes($tipo->descripcion ?? '') }}')"
                                    title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($tipo->contratos_count == 0)
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="eliminarTipo({{ $tipo->id }}, '{{ addslashes($tipo->nombre) }}')"
                                    title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                <p class="mb-0">No hay tipos de contrato registrados.</p>
                                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalTipo" onclick="abrirModalNuevo()">
                                    <i class="bi bi-plus-lg me-2"></i>Crear primer tipo
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Info sobre tipos --}}
    <div class="card border-0 shadow-sm mt-4 bg-light">
        <div class="card-body">
            <h6 class="text-primary"><i class="bi bi-info-circle me-2"></i>Tipos de Contrato Sugeridos</h6>
            <div class="row mt-3">
                <div class="col-md-4">
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li><i class="bi bi-dot"></i> Contrato Fijo</li>
                        <li><i class="bi bi-dot"></i> Contrato Esporádico</li>
                        <li><i class="bi bi-dot"></i> Contrato de Servicios</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li><i class="bi bi-dot"></i> Contrato de Mantenimiento</li>
                        <li><i class="bi bi-dot"></i> Contrato Marco</li>
                        <li><i class="bi bi-dot"></i> Contrato de Salud</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li><i class="bi bi-dot"></i> Contrato de Subcontratación</li>
                        <li><i class="bi bi-dot"></i> Contrato de Obra y Servicio</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Crear/Editar Tipo --}}
<div class="modal fade" id="modalTipo" tabindex="-1" aria-labelledby="modalTipoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTipoLabel">Nuevo Tipo de Contrato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formTipo">
                <div class="modal-body">
                    <input type="hidden" id="tipoId" value="">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100" placeholder="Ej: Contrato de Mantenimiento">
                        <div class="invalid-feedback" id="nombreError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="500" placeholder="Descripción opcional del tipo de contrato"></textarea>
                        <div class="invalid-feedback" id="descripcionError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                        <i class="bi bi-check-lg me-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modalTipo = new bootstrap.Modal(document.getElementById('modalTipo'));

    function abrirModalNuevo() {
        document.getElementById('modalTipoLabel').textContent = 'Nuevo Tipo de Contrato';
        document.getElementById('formTipo').reset();
        document.getElementById('tipoId').value = '';
        limpiarErrores();
    }

    function abrirModalEditar(id, nombre, descripcion) {
        document.getElementById('modalTipoLabel').textContent = 'Editar Tipo de Contrato';
        document.getElementById('tipoId').value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('descripcion').value = descripcion || '';
        limpiarErrores();
        modalTipo.show();
    }

    function limpiarErrores() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    document.getElementById('formTipo').addEventListener('submit', async function(e) {
        e.preventDefault();
        limpiarErrores();

        const id = document.getElementById('tipoId').value;
        const url = id
            ? `{{ url('contrato-tipos') }}/${id}`
            : '{{ route('contrato-tipos.store') }}';
        const method = id ? 'PUT' : 'POST';

        const data = {
            nombre: document.getElementById('nombre').value,
            descripcion: document.getElementById('descripcion').value || null,
        };

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (response.ok && result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: result.message,
                    timer: 1500,
                    showConfirmButton: false,
                }).then(() => {
                    window.location.reload();
                });
            } else {
                if (response.status === 422 && result.errors) {
                    // Errores de validación
                    for (const [field, messages] of Object.entries(result.errors)) {
                        const input = document.getElementById(field);
                        const errorDiv = document.getElementById(field + 'Error');
                        if (input) input.classList.add('is-invalid');
                        if (errorDiv) errorDiv.textContent = messages[0];
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error al guardar el tipo de contrato.',
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión. Por favor, inténtalo de nuevo.',
            });
        }
    });

    function eliminarTipo(id, nombre) {
        Swal.fire({
            title: '¿Eliminar tipo de contrato?',
            html: `Se eliminará el tipo <strong>${nombre}</strong>.<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('contrato-tipos') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'No se pudo eliminar el tipo de contrato.',
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión.',
                    });
                }
            }
        });
    }
</script>
@endpush
