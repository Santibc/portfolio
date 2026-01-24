@extends('layouts.app')

@section('title', 'Categorías de Gastos')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Categorías de Gastos</h1>
            <p class="text-muted mb-0">Gestión de categorías para clasificar gastos</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCategoria" onclick="abrirModalNuevo()">
            <i class="bi bi-plus-lg me-2"></i>Nueva Categoría
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
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-tags text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total Categorías</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-arrow-right-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['directos'] }}</h3>
                            <small class="text-muted">Gastos Directos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-arrow-left-right text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['indirectos'] }}</h3>
                            <small class="text-muted">Gastos Indirectos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de categorías --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Listado de Categorías</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th class="text-center">Gastos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorias as $categoria)
                    <tr>
                        <td>
                            @if($categoria->codigo)
                                <code>{{ $categoria->codigo }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $categoria->nombre }}</strong>
                        </td>
                        <td>
                            @if($categoria->tipo === 'directo')
                                <span class="badge bg-success">Directo</span>
                            @else
                                <span class="badge bg-info">Indirecto</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $categoria->gastos_count }}</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="abrirModalEditar({{ $categoria->id }}, '{{ $categoria->nombre }}', '{{ $categoria->codigo }}', '{{ $categoria->tipo }}')"
                                    title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($categoria->gastos_count == 0)
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="eliminarCategoria({{ $categoria->id }}, '{{ $categoria->nombre }}')"
                                    title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                <p class="mb-0">No hay categorías registradas.</p>
                                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalCategoria" onclick="abrirModalNuevo()">
                                    <i class="bi bi-plus-lg me-2"></i>Crear primera categoría
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
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-success"><i class="bi bi-arrow-right-circle me-2"></i>Gastos Directos</h6>
                    <p class="text-muted mb-0 small">
                        Son los gastos directamente asociados a una obra específica: personal, subcontratas, maquinaria, combustible, materiales.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-info"><i class="bi bi-arrow-left-right me-2"></i>Gastos Indirectos</h6>
                    <p class="text-muted mb-0 small">
                        Son gastos generales de la empresa no asociados a obras específicas: gestoría, seguros, alquileres, administración.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Crear/Editar Categoría --}}
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCategoriaLabel">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCategoria">
                <div class="modal-body">
                    <input type="hidden" id="categoriaId" value="">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100" placeholder="Ej: Combustible">
                        <div class="invalid-feedback" id="nombreError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" maxlength="20" placeholder="Ej: COMB">
                        <small class="text-muted">Código corto para identificación rápida (opcional)</small>
                        <div class="invalid-feedback" id="codigoError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="directo">Directo (asociado a obras)</option>
                            <option value="indirecto">Indirecto (gastos generales)</option>
                        </select>
                        <div class="invalid-feedback" id="tipoError"></div>
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
    const modalCategoria = new bootstrap.Modal(document.getElementById('modalCategoria'));

    function abrirModalNuevo() {
        document.getElementById('modalCategoriaLabel').textContent = 'Nueva Categoría';
        document.getElementById('formCategoria').reset();
        document.getElementById('categoriaId').value = '';
        limpiarErrores();
    }

    function abrirModalEditar(id, nombre, codigo, tipo) {
        document.getElementById('modalCategoriaLabel').textContent = 'Editar Categoría';
        document.getElementById('categoriaId').value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('codigo').value = codigo || '';
        document.getElementById('tipo').value = tipo;
        limpiarErrores();
        modalCategoria.show();
    }

    function limpiarErrores() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    document.getElementById('formCategoria').addEventListener('submit', async function(e) {
        e.preventDefault();
        limpiarErrores();

        const id = document.getElementById('categoriaId').value;
        const url = id
            ? `{{ url('gasto-categorias') }}/${id}`
            : '{{ route('gasto-categorias.store') }}';
        const method = id ? 'PUT' : 'POST';

        const data = {
            nombre: document.getElementById('nombre').value,
            codigo: document.getElementById('codigo').value || null,
            tipo: document.getElementById('tipo').value,
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
                        text: result.message || 'Error al guardar la categoría.',
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

    function eliminarCategoria(id, nombre) {
        Swal.fire({
            title: '¿Eliminar categoría?',
            html: `Se eliminará la categoría <strong>${nombre}</strong>.<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('gasto-categorias') }}/${id}`, {
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
                            title: 'Eliminada',
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
                            text: data.message || 'No se pudo eliminar la categoría.',
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
