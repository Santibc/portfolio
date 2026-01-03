<x-app-layout>
    <x-slot name="header">Flores Disponibles</x-slot>

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
                            <i class="bi bi-flower1 text-pink-500"></i> Gestión de Flores
                        </h4>
                        <a href="{{ route('flores.form') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Nueva Flor
                        </a>
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i>
                        Estas flores estarán disponibles para que los clientes armen sus ramos personalizados en la sección "Arma tu Ramo".
                    </div>

                    <table id="flores-table" class="table table-striped table-hover w-100">
                        <thead class="table-dark">
                            <tr>
                                <th width="80">Acciones</th>
                                <th width="80">Imagen</th>
                                <th>Nombre</th>
                                <th width="60">Color</th>
                                <th width="100">Precio/U</th>
                                <th width="80">Stock</th>
                                <th width="80">Min</th>
                                <th width="80">Max</th>
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
        const table = $('#flores-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('flores.index') }}",
            columns: [
                { data: 'action', orderable: false, searchable: false },
                { data: 'imagen_preview', orderable: false, searchable: false },
                { data: 'nombre', name: 'nombre' },
                { data: 'color_badge', orderable: false, searchable: false },
                { data: 'precio_formateado', orderable: false, searchable: false },
                { data: 'stock', name: 'stock' },
                { data: 'cantidad_minima', name: 'cantidad_minima' },
                { data: 'cantidad_maxima', name: 'cantidad_maxima' },
                { data: 'orden', name: 'orden' },
                { data: 'estado', orderable: false, searchable: false },
            ],
            order: [[8, 'asc'], [2, 'asc']], // Ordenar por orden, luego nombre
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        });

        // Eliminar flor
        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: '¿Eliminar esta flor?',
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
                        url: "{{ route('flores.eliminar') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminada',
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
                                text: 'No se pudo eliminar la flor'
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
