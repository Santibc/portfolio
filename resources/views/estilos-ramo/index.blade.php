<x-app-layout>
    <x-slot name="header">Estilos de Ramo</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes de éxito/error --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">
                            <i class="bi bi-palette text-primary"></i> Estilos de Ramo
                        </h4>
                        <a href="{{ route('estilos-ramo.form') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Nuevo Estilo
                        </a>
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i>
                        Los estilos definen la personalidad del ramo (Romantico, Elegante, Silvestre). Cada estilo tiene un precio base y rango de flores permitidas.
                    </div>

                    <table id="estilos-table" class="table table-striped table-hover w-100">
                        <thead class="table-dark">
                            <tr>
                                <th width="80">Acciones</th>
                                <th width="80">Imagen</th>
                                <th>Nombre</th>
                                <th width="120">Precio Base</th>
                                <th width="140">Rango Flores</th>
                                <th width="60">Orden</th>
                                <th width="100">Estado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const table = $('#estilos-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('estilos-ramo.index') }}",
            columns: [
                { data: 'action', orderable: false, searchable: false },
                { data: 'imagen_preview', orderable: false, searchable: false },
                { data: 'nombre', name: 'nombre' },
                { data: 'precio_formateado', orderable: false, searchable: false },
                { data: 'rango_flores', orderable: false, searchable: false },
                { data: 'orden', name: 'orden' },
                { data: 'estado', orderable: false, searchable: false },
            ],
            order: [[5, 'asc'], [2, 'asc']],
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
            dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        });

        // Eliminar estilo
        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: '¿Eliminar este estilo?',
                text: 'Esta accion no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('estilos-ramo.eliminar') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.ajax.reload();
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo eliminar el estilo'
                            });
                        }
                    });
                }
            });
        });
    });
    </script>
    @endpush
</x-app-layout>
