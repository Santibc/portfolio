<x-app-layout>
    <x-slot name="header">
        {{ __('Llamadas') }}
    </x-slot>

    <div class="py-12" style="padding-top: 0;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="text-2xl font-semibold mb-4">Llamadas</h4>

                    <div class="border border-gray-300 rounded-lg">
                        <div class="overflow-x-auto">

                        <table id="llamadas-table" class="table-responsive w-full text-sm text-left text-gray-700">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                <tr class="border-b border-gray-300">
                                    <th class="px-6 py-3" data-priority="1">Acciones</th>
                                    <th class="px-6 py-3">Lead</th>
                                    <th class="px-6 py-3">Nombre del Evento</th>
                                    <th class="px-6 py-3">Usuario</th>
                                    <th class="px-6 py-3">Estado</th>
                                    <th class="px-6 py-3">Inicio</th>
                                    <th class="px-6 py-3">Fin</th>
                                    <th class="px-6 py-3">Enlace de Ingreso</th>
                                    <th class="px-6 py-3">Tipo de Evento (URI)</th>
                                    <th class="px-6 py-3">Comentarios</th>
                                    <th class="px-6 py-3">Cancelado por</th>
                                    <th class="px-6 py-3">Motivo de Cancelación</th>
                                    <th class="px-6 py-3">Estado en Pipeline</th>
                                    <th class="px-6 py-3">URI</th>                               
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

    <!-- Modal Respuestas -->
    <div class="modal fade" id="modalRespuestas" tabindex="-1" aria-labelledby="modalRespuestasLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRespuestasLabel">Respuestas de la llamada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="contenidoRespuestas">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status" id="loaderRespuestas" style="display: none;">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Lead -->
    <div class="modal fade" id="modalLeadInfo" tabindex="-1" aria-labelledby="modalLeadInfoLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Información del Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="contenidoLead">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status" id="loaderLead" style="display: none;">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        const results = regex.exec(location.search);
        return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    $(document).on('click', '.ver-lead-btn', function () {
        var leadId = $(this).data('id');
        $('#loaderLead').show();
        $('#contenidoLead').html('');

        $.ajax({
            url: '/leads/' + leadId + '/info-json',
            method: 'GET',
            success: function (lead) {
                $('#loaderLead').hide();
                let html = `<ul class="list-group">
                    <li class="list-group-item"><strong>Nombre:</strong> ${lead.nombre}</li>
                    <li class="list-group-item"><strong>Email:</strong> ${lead.email}</li>
                    <li class="list-group-item"><strong>Teléfono:</strong> ${lead.telefono ?? '-'}</li>
                    <li class="list-group-item"><strong>Instagram:</strong> ${lead.instagram_user ?? '-'}</li>
                </ul>`;
                $('#contenidoLead').html(html);
                $('#modalLeadInfo').modal('show');
            },
            error: function () {
                $('#loaderLead').hide();
                $('#contenidoLead').html('<div class="alert alert-danger">Error al cargar la información del lead.</div>');
            }
        });
    });

    $(document).on('click', '.ver-respuestas-btn', function () {
        var llamadaId = $(this).data('id');
        $('#loaderRespuestas').show();
        $('#contenidoRespuestas').html('');

        $.ajax({
            url: '/llamadas/' + llamadaId + '/respuestas-json',
            method: 'GET',
            success: function (respuestas) {
                $('#loaderRespuestas').hide();
                if (respuestas.length === 0) {
                    $('#contenidoRespuestas').html('<p class="text-muted">No hay respuestas registradas para esta llamada.</p>');
                } else {
                    let html = '<ul class="list-group">';
                    respuestas.forEach(function (item) {
                        html += `<li class="list-group-item"><strong>${item.pregunta}:</strong><br> ${item.respuesta}</li>`;
                    });
                    html += '</ul>';
                    $('#contenidoRespuestas').html(html);
                }
                $('#modalRespuestas').modal('show');
            },
            error: function () {
                $('#loaderRespuestas').hide();
                $('#contenidoRespuestas').html('<div class="alert alert-danger">Ocurrió un error al cargar las respuestas.</div>');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const table = $('#llamadas-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollX: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('llamadas') }}",
                data: function (d) {
                    d.lead_id = getUrlParameter('lead_id');
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'noVis' },
                { data: 'lead', name: 'lead_id', orderable: false, searchable: false },
                { data: 'nombre_evento', name: 'nombre_evento' },
                { data: 'user_id', name: 'user_id' },
                { data: 'status', name: 'status' },
                { data: 'start_time', name: 'start_time' },
                { data: 'end_time', name: 'end_time' },
                { data: 'join_url', name: 'join_url' },
                { data: 'event_type_uri', name: 'event_type_uri' },
                { data: 'comentarios', name: 'comentarios' },
                { data: 'cancelado_por', name: 'cancelado_por' },
                { data: 'motivo_cancelacion', name: 'motivo_cancelacion' },
                { data: 'pipeline_status', name: 'pipeline_status' },
                { data: 'uri', name: 'uri' }
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
    });
</script>
@endpush

</x-app-layout>
