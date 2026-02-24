@extends('layouts.app')

@section('title', 'Bosquejos Matriz')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <x-sinden.page-header title="Bosquejos Matriz" description="Biblioteca de plantillas de bosquejos organizadas por grupos">
        <x-slot name="actions">
            @can('gestionar_bosquejos_matriz')
            <x-sinden.button variant="outline-primary" icon="bi bi-image"
                onclick="abrirModalSubirBosquejoIndividual()">Subir Bosquejo</x-sinden.button>
            <x-sinden.button variant="primary" icon="bi bi-folder-plus"
                onclick="$('#modalNuevoGrupo').modal('show')">Nuevo Grupo</x-sinden.button>
            @endcan
        </x-slot>
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-folder" :value="$totalGrupos" title="Total Grupos" color="primary" />
        <x-sinden.stat-card icon="bi bi-image" :value="$totalBosquejos" title="Total Bosquejos" color="info" />
        <x-sinden.stat-card icon="bi bi-images" :value="$bosquejosSinGrupo" title="Sin Grupo" color="warning" />
    </div>

    {{-- Accordion de Grupos --}}
    @if($grupos->count() > 0)
    <div class="accordion mt-4" id="acordeonGrupos">
        @foreach($grupos as $grupo)
        <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden" id="grupo-card-{{ $grupo->id }}">
            <h2 class="accordion-header">
                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                    type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse-{{ $grupo->id }}">
                    <i class="bi bi-folder2-open me-2 text-primary"></i>
                    <strong>{{ $grupo->nombre }}</strong>
                    <span class="badge bg-light text-muted border ms-2">
                        {{ $grupo->plantillas->count() }} bosquejo{{ $grupo->plantillas->count() !== 1 ? 's' : '' }}
                    </span>
                </button>
            </h2>
            <div id="collapse-{{ $grupo->id }}"
                class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                data-bs-parent="#acordeonGrupos">
                <div class="accordion-body">
                    {{-- Botones de accion del grupo --}}
                    @can('gestionar_bosquejos_matriz')
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick="abrirModalSubirBosquejo({{ $grupo->id }}, '{{ addslashes($grupo->nombre) }}')">
                            <i class="bi bi-upload me-1"></i> Subir Bosquejo
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="abrirModalRenombrar('grupo', {{ $grupo->id }}, '{{ addslashes($grupo->nombre) }}')">
                            <i class="bi bi-pencil me-1"></i> Renombrar
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="confirmarEliminarGrupo({{ $grupo->id }}, '{{ addslashes($grupo->nombre) }}', {{ $grupo->plantillas->count() }})">
                            <i class="bi bi-trash me-1"></i> Eliminar Grupo
                        </button>
                    </div>
                    @endcan

                    {{-- Grid de tarjetas de bosquejos --}}
                    @if($grupo->plantillas->count() > 0)
                    <div class="row g-3" id="bosquejos-grid-{{ $grupo->id }}">
                        @foreach($grupo->plantillas as $plantilla)
                        <div class="col-6 col-md-4 col-lg-3 col-xl-2" id="bosquejo-card-{{ $plantilla->id }}">
                            <div class="card bosquejo-card h-100 border-0 shadow-sm">
                                <div class="bosquejo-thumb-wrapper" onclick="verBosquejo('{{ asset($plantilla->ruta_archivo) }}', '{{ addslashes($plantilla->nombre) }}')">
                                    <img src="{{ asset($plantilla->ruta_miniatura ?? $plantilla->ruta_archivo) }}"
                                        class="card-img-top bosquejo-thumb"
                                        alt="{{ $plantilla->nombre }}"
                                        loading="lazy">
                                </div>
                                <div class="card-body p-2 text-center">
                                    <p class="card-text small mb-1 fw-semibold text-truncate" title="{{ $plantilla->nombre }}">
                                        {{ $plantilla->nombre }}
                                    </p>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('recepcion.bosquejos-matriz.bosquejos.descargar', $plantilla) }}"
                                            class="btn btn-outline-primary btn-sm" title="Descargar">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @can('gestionar_bosquejos_matriz')
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            title="Renombrar"
                                            onclick="abrirModalRenombrar('bosquejo', {{ $plantilla->id }}, '{{ addslashes($plantilla->nombre) }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            title="Eliminar"
                                            onclick="confirmarEliminarBosquejo({{ $plantilla->id }}, '{{ addslashes($plantilla->nombre) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-images" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2 mb-0">Este grupo no tiene bosquejos todavia.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Seccion de Bosquejos Individuales --}}
    @if($bosquejosSueltos->count() > 0)
    <div class="card border-0 shadow-sm mb-3 rounded-3 overflow-hidden mt-4" id="seccion-individuales">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <div>
                <i class="bi bi-image me-2 text-warning"></i>
                <strong>Bosquejos Individuales</strong>
                <span class="badge bg-light text-muted border ms-2">{{ $bosquejosSueltos->count() }}</span>
            </div>
            @can('gestionar_bosquejos_matriz')
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="abrirModalSubirBosquejoIndividual()">
                <i class="bi bi-upload me-1"></i> Subir Bosquejo
            </button>
            @endcan
        </div>
        <div class="card-body">
            <div class="row g-3" id="bosquejos-grid-individuales">
                @foreach($bosquejosSueltos as $plantilla)
                <div class="col-6 col-md-4 col-lg-3 col-xl-2" id="bosquejo-card-{{ $plantilla->id }}">
                    <div class="card bosquejo-card h-100 border-0 shadow-sm">
                        <div class="bosquejo-thumb-wrapper" onclick="verBosquejo('{{ asset($plantilla->ruta_archivo) }}', '{{ addslashes($plantilla->nombre) }}')">
                            <img src="{{ asset($plantilla->ruta_miniatura ?? $plantilla->ruta_archivo) }}"
                                class="card-img-top bosquejo-thumb"
                                alt="{{ $plantilla->nombre }}"
                                loading="lazy">
                        </div>
                        <div class="card-body p-2 text-center">
                            <p class="card-text small mb-1 fw-semibold text-truncate" title="{{ $plantilla->nombre }}">
                                {{ $plantilla->nombre }}
                            </p>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('recepcion.bosquejos-matriz.bosquejos.descargar', $plantilla) }}"
                                    class="btn btn-outline-primary btn-sm" title="Descargar">
                                    <i class="bi bi-download"></i>
                                </a>
                                @can('gestionar_bosquejos_matriz')
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    title="Renombrar"
                                    onclick="abrirModalRenombrar('bosquejo', {{ $plantilla->id }}, '{{ addslashes($plantilla->nombre) }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                    title="Eliminar"
                                    onclick="confirmarEliminarBosquejo({{ $plantilla->id }}, '{{ addslashes($plantilla->nombre) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($grupos->count() === 0 && $bosquejosSueltos->count() === 0)
    {{-- Estado vacio --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body text-center py-5">
            <i class="bi bi-folder-plus text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
            <h5 class="mt-3 text-muted">No hay bosquejos</h5>
            <p class="text-muted mb-3">Crea un grupo o sube un bosquejo individual para comenzar.</p>
            @can('gestionar_bosquejos_matriz')
            <div class="d-flex gap-2 justify-content-center">
                <x-sinden.button variant="outline-primary" icon="bi bi-image"
                    onclick="abrirModalSubirBosquejoIndividual()">Subir Bosquejo</x-sinden.button>
                <x-sinden.button variant="primary" icon="bi bi-folder-plus"
                    onclick="$('#modalNuevoGrupo').modal('show')">Crear Grupo</x-sinden.button>
            </div>
            @endcan
        </div>
    </div>
    @endif
</div>

{{-- ===== MODALES ===== --}}

@can('gestionar_bosquejos_matriz')
{{-- Modal: Nuevo Grupo --}}
<x-sinden.modal id="modalNuevoGrupo" title="Nuevo Grupo de Bosquejos">
    <form id="formNuevoGrupo" onsubmit="event.preventDefault(); guardarGrupo();">
        <div class="mb-3">
            <label for="grupo_nombre" class="form-label">
                <i class="bi bi-folder me-1"></i> Nombre del Grupo <span class="text-danger">*</span>
            </label>
            <input type="text" id="grupo_nombre" name="grupo_nombre"
                class="form-control" placeholder="Ej: Puertas Industriales" required maxlength="255">
        </div>
    </form>
    <x-slot name="footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <x-sinden.button variant="primary" icon="bi bi-check-lg" onclick="guardarGrupo()">Crear Grupo</x-sinden.button>
    </x-slot>
</x-sinden.modal>

{{-- Modal: Subir Bosquejo --}}
<x-sinden.modal id="modalSubirBosquejo" title="Subir Bosquejo">
    <form id="formSubirBosquejo" enctype="multipart/form-data" onsubmit="event.preventDefault(); subirBosquejo();">
        <input type="hidden" id="subir_grupo_id" name="grupo_bosquejo_id">
        <p class="text-muted mb-3">Grupo: <strong id="subir_grupo_nombre"></strong></p>

        <div class="mb-3">
            <label for="bosquejo_nombre" class="form-label">
                <i class="bi bi-tag me-1"></i> Nombre del Bosquejo <span class="text-danger">*</span>
            </label>
            <input type="text" id="bosquejo_nombre" name="bosquejo_nombre"
                class="form-control" placeholder="Ej: Puerta Corrediza 2m" required maxlength="255">
        </div>

        <div class="mb-3">
            <label for="archivo" class="form-label">
                <i class="bi bi-image me-1"></i> Imagen <span class="text-danger">*</span>
            </label>
            <input type="file" id="archivo" name="archivo"
                class="form-control" required accept="image/jpeg,image/png,image/webp">
            <small class="text-muted">Formatos: JPG, PNG, WebP. Maximo 10MB.</small>
        </div>

        <div id="previewImagen" class="text-center mt-2" style="display: none;">
            <img id="previewImg" src="" alt="Preview" style="max-height: 200px; max-width: 100%; border-radius: 8px;">
        </div>
    </form>
    <x-slot name="footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <x-sinden.button variant="primary" icon="bi bi-upload" id="btnSubirBosquejo" onclick="subirBosquejo()">Subir</x-sinden.button>
    </x-slot>
</x-sinden.modal>

{{-- Modal: Renombrar (compartido grupo/bosquejo) --}}
<x-sinden.modal id="modalRenombrar" title="Renombrar">
    <form id="formRenombrar" onsubmit="event.preventDefault(); guardarRenombrar();">
        <input type="hidden" id="renombrar_tipo">
        <input type="hidden" id="renombrar_id">
        <div class="mb-3">
            <label for="renombrar_nombre" class="form-label">
                <i class="bi bi-pencil me-1"></i> Nuevo Nombre <span class="text-danger">*</span>
            </label>
            <input type="text" id="renombrar_nombre" name="renombrar_nombre"
                class="form-control" required maxlength="255">
        </div>
    </form>
    <x-slot name="footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <x-sinden.button variant="primary" icon="bi bi-check-lg" onclick="guardarRenombrar()">Guardar</x-sinden.button>
    </x-slot>
</x-sinden.modal>
@endcan

{{-- Modal: Ver imagen completa (lightbox) --}}
<x-sinden.modal id="modalVerBosquejo" title="Bosquejo" size="lg">
    <div class="text-center">
        <img id="verBosquejoImg" src="" alt="" style="max-width: 100%; max-height: 70vh; border-radius: 8px;">
    </div>
</x-sinden.modal>

@endsection

@push('scripts')
<script>
$(function() {
    // Preview de imagen al seleccionar archivo
    $('#archivo').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                $('#previewImg').attr('src', ev.target.result);
                $('#previewImagen').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#previewImagen').hide();
        }
    });
});

@can('gestionar_bosquejos_matriz')
// ===== CREAR GRUPO =====
function guardarGrupo() {
    var nombre = $('#grupo_nombre').val().trim();
    if (!nombre) {
        Swal.fire('Error', 'El nombre del grupo es obligatorio.', 'error');
        return;
    }

    $.ajax({
        url: '{{ route("recepcion.bosquejos-matriz.grupos.store") }}',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        contentType: 'application/json',
        data: JSON.stringify({ nombre: nombre }),
        success: function(data) {
            if (data.success) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                $('#modalNuevoGrupo').modal('hide');
                $('#grupo_nombre').val('');
                location.reload();
            }
        },
        error: function(xhr) {
            var msg = 'No se pudo crear el grupo.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
            }
            Swal.fire('Error', msg, 'error');
        }
    });
}

// ===== RENOMBRAR (grupo o bosquejo) =====
function abrirModalRenombrar(tipo, id, nombreActual) {
    $('#renombrar_tipo').val(tipo);
    $('#renombrar_id').val(id);
    $('#renombrar_nombre').val(nombreActual);
    $('#modalRenombrar').modal('show');
    setTimeout(function() { $('#renombrar_nombre').focus().select(); }, 500);
}

function guardarRenombrar() {
    var tipo = $('#renombrar_tipo').val();
    var id = $('#renombrar_id').val();
    var nombre = $('#renombrar_nombre').val().trim();

    if (!nombre) {
        Swal.fire('Error', 'El nombre es obligatorio.', 'error');
        return;
    }

    var url = tipo === 'grupo'
        ? '{{ url("recepcion/bosquejos-matriz/grupos") }}/' + id
        : '{{ url("recepcion/bosquejos-matriz/bosquejos") }}/' + id;

    $.ajax({
        url: url,
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        contentType: 'application/json',
        data: JSON.stringify({ nombre: nombre }),
        success: function(data) {
            if (data.success) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                $('#modalRenombrar').modal('hide');
                location.reload();
            }
        },
        error: function() {
            Swal.fire('Error', 'No se pudo renombrar.', 'error');
        }
    });
}

// ===== ELIMINAR GRUPO =====
function confirmarEliminarGrupo(grupoId, nombre, cantidadBosquejos) {
    var texto = cantidadBosquejos > 0
        ? 'Se eliminara el grupo "' + nombre + '" y sus ' + cantidadBosquejos + ' bosquejo(s). Esta accion no se puede deshacer.'
        : 'Se eliminara el grupo "' + nombre + '". Esta accion no se puede deshacer.';

    Swal.fire({
        title: 'Eliminar grupo?',
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url("recepcion/bosquejos-matriz/grupos") }}/' + grupoId,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(data) {
                    if (data.success) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                        $('#grupo-card-' + grupoId).fadeOut(300, function() { $(this).remove(); });
                        setTimeout(function() { location.reload(); }, 1500);
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo eliminar el grupo.', 'error');
                }
            });
        }
    });
}

// ===== SUBIR BOSQUEJO =====
function abrirModalSubirBosquejo(grupoId, grupoNombre) {
    $('#subir_grupo_id').val(grupoId);
    $('#subir_grupo_nombre').text(grupoNombre).show();
    $('#bosquejo_nombre').val('');
    $('#archivo').val('');
    $('#previewImagen').hide();
    $('#btnSubirBosquejo').prop('disabled', false);
    $('#modalSubirBosquejo').modal('show');
    setTimeout(function() { $('#bosquejo_nombre').focus(); }, 500);
}

function abrirModalSubirBosquejoIndividual() {
    $('#subir_grupo_id').val('');
    $('#subir_grupo_nombre').text('Sin grupo (individual)').show();
    $('#bosquejo_nombre').val('');
    $('#archivo').val('');
    $('#previewImagen').hide();
    $('#btnSubirBosquejo').prop('disabled', false);
    $('#modalSubirBosquejo').modal('show');
    setTimeout(function() { $('#bosquejo_nombre').focus(); }, 500);
}

function subirBosquejo() {
    var nombre = $('#bosquejo_nombre').val().trim();
    var grupoId = $('#subir_grupo_id').val();
    var archivoInput = document.getElementById('archivo');

    if (!nombre) {
        Swal.fire('Error', 'El nombre del bosquejo es obligatorio.', 'error');
        return;
    }
    if (!archivoInput.files[0]) {
        Swal.fire('Error', 'Debe seleccionar una imagen.', 'error');
        return;
    }

    var formData = new FormData();
    formData.append('nombre', nombre);
    if (grupoId) {
        formData.append('grupo_bosquejo_id', grupoId);
    }
    formData.append('archivo', archivoInput.files[0]);

    $('#btnSubirBosquejo').prop('disabled', true);

    $.ajax({
        url: '{{ route("recepcion.bosquejos-matriz.bosquejos.store") }}',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.success) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                $('#modalSubirBosquejo').modal('hide');
                location.reload();
            } else {
                $('#btnSubirBosquejo').prop('disabled', false);
                Swal.fire('Error', data.message || 'No se pudo subir el bosquejo.', 'error');
            }
        },
        error: function(xhr) {
            $('#btnSubirBosquejo').prop('disabled', false);
            var msg = 'No se pudo subir el bosquejo.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire('Error', msg, 'error');
        }
    });
}

// ===== ELIMINAR BOSQUEJO =====
function confirmarEliminarBosquejo(bosquejoId, nombre) {
    Swal.fire({
        title: 'Eliminar bosquejo?',
        text: 'Se eliminara "' + nombre + '" y sus archivos. Esta accion no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url("recepcion/bosquejos-matriz/bosquejos") }}/' + bosquejoId,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(data) {
                    if (data.success) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                        $('#bosquejo-card-' + bosquejoId).fadeOut(300, function() { $(this).remove(); });
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo eliminar el bosquejo.', 'error');
                }
            });
        }
    });
}
@endcan

// ===== VER IMAGEN COMPLETA (lightbox) =====
function verBosquejo(url, nombre) {
    $('#verBosquejoImg').attr('src', url);
    $('#modalVerBosquejoLabel').text(nombre);
    $('#modalVerBosquejo').modal('show');
}
</script>
@endpush
