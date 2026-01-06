@extends('layouts.app')

@section('title', 'Carrusel de Imágenes')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Carrusel de Imágenes</h1>
            <p class="text-muted mb-0">Administra las imágenes del carrusel de tu tienda</p>
        </div>
        <a href="{{ route('carrusel.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva imagen
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($imagenes->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-images text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 mb-2">No hay imágenes en el carrusel</h5>
                <p class="text-muted mb-4">Agrega imágenes para mostrar en el hero de tu tienda</p>
                <a href="{{ route('carrusel.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Agregar primera imagen
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-info-circle me-1"></i> Arrastra las imágenes para reordenarlas</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnGuardarOrden" style="display: none;">
                    <i class="bi bi-check-lg me-1"></i> Guardar orden
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40"></th>
                                <th width="120">Imagen</th>
                                <th>Título</th>
                                <th>Enlace</th>
                                <th width="120">Vigencia</th>
                                <th width="100" class="text-center">Estado</th>
                                <th width="140" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-carrusel">
                            @foreach($imagenes as $imagen)
                            <tr data-id="{{ $imagen->id }}">
                                <td class="handle text-center" style="cursor: grab;">
                                    <i class="bi bi-grip-vertical text-muted"></i>
                                </td>
                                <td>
                                    @if($imagen->imagen_url)
                                        <img src="{{ $imagen->imagen_url }}" alt="{{ $imagen->titulo }}"
                                             class="img-thumbnail" style="width: 100px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                             style="width: 100px; height: 60px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $imagen->titulo ?: 'Sin título' }}</strong>
                                    @if($imagen->descripcion)
                                        <br><small class="text-muted">{{ Str::limit($imagen->descripcion, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($imagen->link)
                                        <a href="{{ $imagen->link }}" target="_blank" class="text-decoration-none">
                                            <i class="bi bi-link-45deg me-1"></i>{{ Str::limit($imagen->link, 30) }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($imagen->fecha_inicio || $imagen->fecha_fin)
                                        <small>
                                            @if($imagen->fecha_inicio)
                                                <i class="bi bi-calendar-event me-1"></i>{{ $imagen->fecha_inicio->format('d/m/Y') }}
                                            @endif
                                            @if($imagen->fecha_fin)
                                                <br><i class="bi bi-calendar-x me-1"></i>{{ $imagen->fecha_fin->format('d/m/Y') }}
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">Siempre</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input toggle-activo" type="checkbox"
                                               data-id="{{ $imagen->id }}"
                                               {{ $imagen->activo ? 'checked' : '' }}>
                                    </div>
                                    @if($imagen->estaActivo())
                                        <small class="text-success">Visible</small>
                                    @else
                                        <small class="text-muted">Oculto</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('carrusel.edit', $imagen->id) }}"
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar"
                                            data-id="{{ $imagen->id }}" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-eye me-2"></i>Vista previa del carrusel</h6>
            </div>
            <div class="card-body">
                <div id="carruselPreview" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($imagenes->where('activo', true) as $key => $img)
                            <button type="button" data-bs-target="#carruselPreview"
                                    data-bs-slide-to="{{ $key }}"
                                    class="{{ $key === 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner" style="border-radius: 12px; overflow: hidden;">
                        @foreach($imagenes->where('activo', true) as $key => $img)
                            <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                <img src="{{ $img->imagen_url }}" class="d-block w-100"
                                     style="height: 300px; object-fit: cover;"
                                     alt="{{ $img->titulo }}">
                                @if($img->titulo || $img->descripcion)
                                    <div class="carousel-caption d-none d-md-block">
                                        @if($img->titulo)
                                            <h5>{{ $img->titulo }}</h5>
                                        @endif
                                        @if($img->descripcion)
                                            <p>{{ $img->descripcion }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($imagenes->where('activo', true)->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#carruselPreview" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carruselPreview" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .handle:hover {
        background-color: #f8f9fa;
    }
    .sortable-ghost {
        opacity: 0.4;
        background-color: #e3f2fd;
    }
    .sortable-chosen {
        background-color: #fff3e0;
    }
    .toggle-activo {
        cursor: pointer;
        width: 2.5rem;
    }
    .carousel-caption {
        background: rgba(0,0,0,0.5);
        border-radius: 8px;
        padding: 16px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Configurar CSRF token para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Sortable para reordenar
    const sortable = new Sortable(document.getElementById('sortable-carrusel'), {
        handle: '.handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function() {
            $('#btnGuardarOrden').show();
        }
    });

    // Guardar orden
    $('#btnGuardarOrden').on('click', function() {
        const btn = $(this);
        const orden = {};

        $('#sortable-carrusel tr').each(function(index) {
            const id = $(this).data('id');
            orden[id] = index;
        });

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Guardando...');

        $.ajax({
            url: '{{ route("carrusel.updateOrden") }}',
            method: 'POST',
            data: { orden: orden },
            success: function(response) {
                btn.hide().prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Guardar orden');
                showToast('success', response.message);
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Guardar orden');
                showToast('error', 'Error al guardar el orden');
            }
        });
    });

    // Toggle activo
    $('.toggle-activo').on('change', function() {
        const id = $(this).data('id');
        const checkbox = $(this);

        $.ajax({
            url: '/carrusel/' + id + '/toggle',
            method: 'POST',
            success: function(response) {
                showToast('success', response.message);
                setTimeout(() => location.reload(), 500);
            },
            error: function() {
                checkbox.prop('checked', !checkbox.prop('checked'));
                showToast('error', 'Error al cambiar estado');
            }
        });
    });

    // Eliminar imagen
    $('.btn-eliminar').on('click', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');

        Swal.fire({
            title: '¿Eliminar imagen?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/carrusel/' + id,
                    method: 'DELETE',
                    success: function(response) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                            if ($('#sortable-carrusel tr').length === 0) {
                                location.reload();
                            }
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminada',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo eliminar la imagen'
                        });
                    }
                });
            }
        });
    });

    // Toast notification con SweetAlert2
    function showToast(type, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        Toast.fire({
            icon: type,
            title: message
        });
    }
});
</script>
@endpush
