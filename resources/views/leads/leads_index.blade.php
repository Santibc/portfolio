<x-app-layout>
    <x-slot name="header">
        {{ __('Leads') }}
    </x-slot>

    <div class="py-12" style="padding-top: 0;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="text-2xl font-semibold mb-4">Leads</h4>

                    <div class="border border-gray-300 rounded-lg">
                        <div class="overflow-x-auto">

                        <table id="leads-table" class="table-responsive w-full text-sm text-left text-gray-700">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                <tr class="border-b border-gray-300">
                                    <th class="px-6 py-3" data-priority="1">Acciones</th>
                                    <th class="px-6 py-3">Estado Pipeline</th>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3">Teléfono</th>
                                    <th class="px-6 py-3">Instagram</th>
                                </tr>
                            </thead>

                            <tbody></tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para comentarios -->
    <div class="modal fade" id="comentarioModal" tabindex="-1" aria-labelledby="comentarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="comentarioModalLabel">Añadir Comentario al Cambio de Estado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="comentarioForm">
                        <input type="hidden" id="leadId" name="lead_id">
                        <input type="hidden" id="statusId" name="status_id">
                        <div class="mb-3">
                            <label for="comentarioText" class="form-label">Comentario</label>
                            <textarea class="form-control" id="comentarioText" name="comentario" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarComentario">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para información de venta -->
    <div class="modal fade" id="saleInfoModal" tabindex="-1" aria-labelledby="saleInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6"><strong>Nombre:</strong> <span id="modal-nombre"></span></div>
                        <div class="col-md-6"><strong>Apellido:</strong> <span id="modal-apellido"></span></div>
                        <div class="col-md-6"><strong>Email:</strong> <span id="modal-email"></span></div>
                        <div class="col-md-6"><strong>Teléfono:</strong> <span id="modal-telefono"></span></div>
                        <div class="col-md-6"><strong>ID Personal:</strong> <span id="modal-identificacion"></span></div>
                        <div class="col-md-6"><strong>Domicilio:</strong> <span id="modal-domicilio"></span></div>
                        <div class="col-md-6"><strong>Método de Pago:</strong> <span id="modal-metodo-pago"></span></div>
                        <div class="col-md-6"><strong>Tipo Acuerdo:</strong> <span id="modal-tipo-acuerdo"></span></div>
                        <div class="col-12 mt-2"><strong>Comentarios:</strong> <span id="modal-comentarios"></span></div>
                        <div class="mt-3 text-end">
                            <a id="btnDescargarComprobante" href="#" target="_blank" class="btn btn-outline-success">
                                <i class="bi bi-download me-1"></i> Descargar Comprobante de Pago
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para historial de cambios -->
    <div class="modal fade" id="logsModal" tabindex="-1" aria-labelledby="logsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Historial de Cambios de Estado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Estado anterior</th>
                                <th>Estado nuevo</th>
                                <th>Comentario</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="logsTableBody">
                            <tr><td colspan="5" class="text-center">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    $(document).on('click', '.view-logs-btn', function () {
        const leadId = $(this).data('lead-id');
        $('#logsModal').modal('show');
        $('#logsTableBody').html('<tr><td colspan="5" class="text-center">Cargando...</td></tr>');

        $.get(`/leads/${leadId}/logs`, function (data) {
            if (data.length === 0) {
                $('#logsTableBody').html('<tr><td colspan="5" class="text-center">Sin registros</td></tr>');
                return;
            }

            let rows = '';
            data.forEach(log => {
                rows += `
                    <tr>
                        <td>${log.estado_anterior}</td>
                        <td>${log.estado_nuevo}</td>
                        <td>${log.comentario}</td>
                        <td>${log.usuario}</td>
                        <td>${log.fecha}</td>
                    </tr>
                `;
            });

            $('#logsTableBody').html(rows);
        }).fail(() => {
            $('#logsTableBody').html('<tr><td colspan="5" class="text-danger text-center">Error al cargar los logs.</td></tr>');
        });
    });

    $(document).on('click', '.view-sale-btn', function () {
        $('#modal-nombre').text($(this).data('nombre'));
        $('#modal-apellido').text($(this).data('apellido'));
        $('#modal-email').text($(this).data('email'));
        $('#modal-telefono').text($(this).data('telefono'));
        $('#modal-identificacion').text($(this).data('identificacion'));
        $('#modal-domicilio').text($(this).data('domicilio'));
        $('#modal-metodo-pago').text($(this).data('metodo_pago'));
        $('#modal-tipo-acuerdo').text($(this).data('tipo_acuerdo'));
        $('#modal-comentarios').text($(this).data('comentarios'));
        const comprobanteUrl = $(this).data('comprobante');
        $('#btnDescargarComprobante').attr('href', comprobanteUrl);
        $('#saleInfoModal').modal('show');
    });

    document.addEventListener('DOMContentLoaded', function () {
        const table = $('#leads-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollX: true,
            autoWidth: false,
            ajax: "{{ route('leads') }}",
            columns: [
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'noVis'
                },
                { 
                    data: 'pipeline_status', 
                    name: 'pipeline_status',
                    orderable: false, 
                    searchable: true
                },
                { data: 'nombre', name: 'nombre' },
                { data: 'email', name: 'email' },
                { data: 'telefono', name: 'telefono' },
                { data: 'instagram_user', name: 'instagram_user' }
            ],

            dom: "<'flex flex-wrap justify-between items-center mb-4'<'relative'B>f>" + 
                 "t" + 
                 "<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",

            buttons: [
                {
                    extend: 'pageLength',
                    className: 'btn btn-outline-dark',
                    text: 'Filas '
                },
                {
                    extend: 'colvis',
                    text: 'Columnas',
                    columns: ':not(.noVis)',
                    className: 'btn btn-outline-dark'
                },
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-outline-success'
                }
            ],
            language: {
                url: '{{ asset("js/datatables/es-ES.json") }}',
                buttons: {
                    pageLength: {
                        _: "Mostrar %d filas",
                        '-1': "Mostrar todos"
                    }
                }
            },
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
        });

        // Configuración de botones
        table.on('buttons-action', function () {
            setTimeout(() => {
                $('.dt-button-collection')
                    .addClass('bg-white border border-gray-300 rounded shadow-md mt-2 p-2')
                    .css({
                        position: 'absolute',
                        'z-index': 999,
                        top: 'calc(100% + 0.5rem)',
                        left: '0',
                        right: 'auto'
                    });

                $('.dt-button-collection button')
                    .removeClass()
                    .addClass('block w-full text-left text-sm text-gray-800 px-4 py-2 rounded hover:bg-gray-100 cursor-pointer transition-colors duration-150');
            }, 50);
        });

        // Variables para manejar el estado del pipeline
        let originalStatusId;

        $('#leads-table').on('focus', '.pipeline-status-select', function () {
            originalStatusId = $(this).val();
        });

        $('#leads-table').on('change', '.pipeline-status-select', function () {
            const leadId = $(this).data('lead-id');
            const statusId = $(this).val();
            const selectElement = $(this);

            $('#comentarioModal #leadId').val(leadId);
            $('#comentarioModal #statusId').val(statusId);
            $('#comentarioModal').modal('show');
            
            $('#comentarioModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
                if (!$('#comentarioForm').data('submitted')) {
                    selectElement.val(originalStatusId);
                }
                $('#comentarioForm').data('submitted', false);
                $('#comentarioText').val('');
            });
        });

        $('#guardarComentario').on('click', function() {
            const leadId = $('#comentarioModal #leadId').val();
            const statusId = $('#comentarioModal #statusId').val();
            const comentario = $('#comentarioModal #comentarioText').val();

            $.ajax({
                url: `/leads/${leadId}/update-status`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status_id: statusId,
                    comentario: comentario
                },
                success: function(response) {
                    $('#comentarioModal').modal('hide');
                    $('#comentarioForm').data('submitted', true);
                    $('#leads-table').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    alert('Error al actualizar el estado.');
                }
            });
        });
    });
</script>
@endpush

</x-app-layout>