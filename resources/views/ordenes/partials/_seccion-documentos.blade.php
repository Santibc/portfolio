{{-- Seccion: Documentos adjuntos (aparece en create/edit del wizard) --}}
<div class="card shadow-sm mb-4" id="seccionDocumentos">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><i class="bi bi-paperclip me-2 text-primary"></i>Documentos adjuntos</h5>
        <small class="text-muted">Cualquier tipo de archivo (max. 50 MB)</small>
    </div>
    <div class="card-body">

        @php $ordenIdActual = isset($orden) ? $orden->id : null; @endphp

        {{-- Mensaje de guardado previo (solo create sin borrador) --}}
        <div id="docsRequiereGuardado" class="alert alert-info d-{{ $ordenIdActual ? 'none' : 'block' }}">
            <i class="bi bi-info-circle me-1"></i>
            Guarda la orden como borrador para poder adjuntar documentos.
            <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="guardarOrden(false)">
                <i class="bi bi-save me-1"></i> Guardar borrador ahora
            </button>
        </div>

        <div id="docsUploadWrapper" class="{{ $ordenIdActual ? '' : 'd-none' }}">
            <div class="d-flex gap-2 align-items-center flex-wrap mb-3">
                <input type="file" id="inputDocumento" class="form-control" multiple style="max-width: 420px;">
                <button type="button" class="btn btn-primary" id="btnSubirDocumento" onclick="subirDocumentosSeleccionados()">
                    <i class="bi bi-cloud-upload me-1"></i> Subir
                </button>
                <span id="docsUploadProgreso" class="text-muted small d-none">
                    <i class="bi bi-hourglass-split me-1"></i><span id="docsUploadProgresoTexto">Subiendo...</span>
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0" id="tablaDocumentos">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Nombre</th>
                        <th style="width: 100px;">Tamaño</th>
                        <th style="width: 150px;">Subido por</th>
                        <th style="width: 140px;">Fecha</th>
                        <th style="width: 150px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyDocumentos">
                    @if(isset($orden) && $orden->documentos && $orden->documentos->count() > 0)
                        @foreach($orden->documentos as $doc)
                            <tr data-doc-id="{{ $doc->id }}">
                                <td class="text-center"><i class="bi {{ $doc->icono }} fs-4"></i></td>
                                <td class="text-break">{{ $doc->nombre_original }}</td>
                                <td>{{ $doc->tamano_legible }}</td>
                                <td>{{ optional($doc->subidoPorUsuario)->name ?? '-' }}</td>
                                <td>{{ optional($doc->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ $doc->url_descarga }}" class="btn btn-sm btn-outline-primary" title="Descargar">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="eliminarDocumento({{ $doc->id }}, '{{ route('recepcion.ordenes.documentos.eliminar', ['orden' => $orden->id, 'documento' => $doc->id]) }}')"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    <tr id="docsVacio" class="{{ (isset($orden) && $orden->documentos && $orden->documentos->count() > 0) ? 'd-none' : '' }}">
                        <td colspan="6" class="text-center text-muted py-3">
                            <i class="bi bi-inbox me-1"></i> Sin documentos adjuntos.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    window.DOCUMENTOS_ROUTES = {
        subirBase: '{{ url("recepcion/ordenes") }}',
        csrfToken: '{{ csrf_token() }}'
    };

    function getOrdenIdActual() {
        var v = $('#orden_id').val();
        return v ? parseInt(v, 10) : null;
    }

    function refrescarVisibilidadUpload() {
        var id = getOrdenIdActual();
        if (id) {
            $('#docsRequiereGuardado').removeClass('d-block').addClass('d-none');
            $('#docsUploadWrapper').removeClass('d-none');
        } else {
            $('#docsRequiereGuardado').removeClass('d-none').addClass('d-block');
            $('#docsUploadWrapper').addClass('d-none');
        }
    }

    // Re-chequear al escribir orden_id (cuando guardarOrden actualiza el hidden)
    $(document).on('change', '#orden_id', refrescarVisibilidadUpload);
    // Polling ligero por si el cambio se hace via .val() (no dispara change)
    setInterval(refrescarVisibilidadUpload, 2000);
    $(document).ready(refrescarVisibilidadUpload);

    window.subirDocumentosSeleccionados = function() {
        var ordenId = getOrdenIdActual();
        if (!ordenId) {
            Swal.fire('Atencion', 'Guarda el borrador primero.', 'info');
            return;
        }
        var input = document.getElementById('inputDocumento');
        if (!input.files || input.files.length === 0) {
            Swal.fire('Atencion', 'Selecciona al menos un archivo.', 'info');
            return;
        }
        var files = Array.from(input.files);
        var total = files.length, subidos = 0;

        $('#docsUploadProgreso').removeClass('d-none');
        $('#btnSubirDocumento').prop('disabled', true);

        function siguiente() {
            if (files.length === 0) {
                $('#docsUploadProgreso').addClass('d-none');
                $('#btnSubirDocumento').prop('disabled', false);
                input.value = '';
                Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                    title: subidos + ' de ' + total + ' archivo(s) subidos.',
                    showConfirmButton: false, timer: 2500 });
                return;
            }
            var file = files.shift();
            $('#docsUploadProgresoTexto').text('Subiendo ' + (subidos + 1) + ' de ' + total + '...');

            var fd = new FormData();
            fd.append('archivo', file);
            fd.append('_token', DOCUMENTOS_ROUTES.csrfToken);

            $.ajax({
                url: DOCUMENTOS_ROUTES.subirBase + '/' + ordenId + '/documentos',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': DOCUMENTOS_ROUTES.csrfToken },
                success: function(resp) {
                    if (resp.success) {
                        agregarFilaDocumento(resp.documento);
                        subidos++;
                    }
                },
                error: function(xhr) {
                    var msg = 'Error subiendo ' + file.name;
                    if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.archivo) {
                        msg = xhr.responseJSON.errors.archivo[0];
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                },
                complete: function() { siguiente(); }
            });
        }
        siguiente();
    };

    function agregarFilaDocumento(doc) {
        $('#docsVacio').addClass('d-none');
        var html = '<tr data-doc-id="' + doc.id + '">' +
            '<td class="text-center"><i class="bi ' + doc.icono + ' fs-4"></i></td>' +
            '<td class="text-break">' + $('<div>').text(doc.nombre_original).html() + '</td>' +
            '<td>' + doc.tamano_legible + '</td>' +
            '<td>' + $('<div>').text(doc.subido_por).html() + '</td>' +
            '<td>' + (doc.created_at || '-') + '</td>' +
            '<td class="text-end">' +
                '<a href="' + doc.url_descarga + '" class="btn btn-sm btn-outline-primary" title="Descargar"><i class="bi bi-download"></i></a> ' +
                '<button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDocumento(' + doc.id + ', \'' + doc.url_eliminar + '\')" title="Eliminar"><i class="bi bi-trash"></i></button>' +
            '</td>' +
            '</tr>';
        $('#tbodyDocumentos').prepend(html);
    }

    window.eliminarDocumento = function(id, url) {
        Swal.fire({
            title: '¿Eliminar documento?',
            text: 'Esta accion no se puede deshacer.',
            icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Si, eliminar', cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(function(r) {
            if (!r.isConfirmed) return;
            $.ajax({
                url: url,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': DOCUMENTOS_ROUTES.csrfToken },
                success: function(resp) {
                    if (resp.success) {
                        $('tr[data-doc-id="' + id + '"]').remove();
                        if ($('#tbodyDocumentos tr[data-doc-id]').length === 0) {
                            $('#docsVacio').removeClass('d-none');
                        }
                    }
                },
                error: function() { Swal.fire('Error', 'No se pudo eliminar.', 'error'); }
            });
        });
    };
})();
</script>
@endpush
