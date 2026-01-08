@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Tamaños de Ramo</h2>
            <p class="text-muted">Gestiona los tamaños disponibles para "Arma tu Ramo"</p>
        </div>
        <a href="{{ route('admin.tamanos-ramo.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Tamaño
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($tamanos->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-flower-daffodil fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay tamaños de ramo configurados</h5>
                    <p class="text-muted">Agrega el primer tamaño para comenzar</p>
                    <a href="{{ route('admin.tamanos-ramo.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-2"></i>Crear Primer Tamaño
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tamanosTable">
                        <thead class="table-light">
                            <tr>
                                <th width="80">Orden</th>
                                <th width="100">Imagen</th>
                                <th>Nombre</th>
                                <th width="120">Cant. Flores</th>
                                <th width="120">Precio Base</th>
                                <th width="150">Estado</th>
                                <th width="180" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tamanos as $tamano)
                                <tr data-id="{{ $tamano->id }}">
                                    <td>
                                        <span class="badge bg-secondary">{{ $tamano->orden }}</span>
                                    </td>
                                    <td>
                                        @if($tamano->imagen)
                                            <img src="{{ $tamano->imagen_url }}"
                                                 alt="{{ $tamano->nombre }}"
                                                 class="img-thumbnail"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width: 60px; height: 60px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $tamano->nombre }}</strong>
                                        @if($tamano->descripcion)
                                            <br><small class="text-muted">{{ Str::limit($tamano->descripcion, 60) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $tamano->cantidad_flores_min }} - {{ $tamano->cantidad_flores_max }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($tamano->precio_base, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-activo"
                                                   type="checkbox"
                                                   data-id="{{ $tamano->id }}"
                                                   {{ $tamano->activo ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                <span class="badge {{ $tamano->activo ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $tamano->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.tamanos-ramo.edit', $tamano) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger btn-eliminar"
                                                data-id="{{ $tamano->id }}"
                                                data-nombre="{{ $tamano->nombre }}"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Información:</strong> Los tamaños de ramo definen las opciones que los clientes pueden seleccionar al crear un ramo personalizado.
            Cada tamaño tiene un precio base (por envoltura/preparación) y un rango de flores permitidas.
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<form id="formEliminar" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Toggle activo/inactivo
    $('.toggle-activo').on('change', function() {
        const checkbox = $(this);
        const tamanoId = checkbox.data('id');
        const isChecked = checkbox.is(':checked');

        $.ajax({
            url: `/admin/tamanos-ramo/${tamanoId}/toggle-activo`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                const badge = checkbox.siblings('label').find('.badge');
                if (response.activo) {
                    badge.removeClass('bg-secondary').addClass('bg-success').text('Activo');
                } else {
                    badge.removeClass('bg-success').addClass('bg-secondary').text('Inactivo');
                }

                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            },
            error: function() {
                checkbox.prop('checked', !isChecked);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cambiar el estado',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    });

    // Eliminar tamaño
    $('.btn-eliminar').on('click', function() {
        const btn = $(this);
        const tamanoId = btn.data('id');
        const nombre = btn.data('nombre');

        Swal.fire({
            title: '¿Eliminar tamaño?',
            html: `¿Estás seguro de eliminar el tamaño <strong>${nombre}</strong>?<br><small class="text-muted">Esta acción no se puede deshacer</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('#formEliminar');
                form.attr('action', `/admin/tamanos-ramo/${tamanoId}`);
                form.submit();
            }
        });
    });

    // DataTables si hay muchos registros
    @if($tamanos->count() > 10)
        $('#tamanosTable').DataTable({
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                }
            },
            order: [[0, 'asc']],
            pageLength: 25
        });
    @endif
});
</script>
@endpush
