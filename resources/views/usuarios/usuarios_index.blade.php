<x-app-layout>
    <x-slot name="header">

            {{ __('Usuarios') }}

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Contenedor de la tabla con borde y redondeado -->
                    <div class="border dark:border-gray-700 rounded-lg">
                        <table id="users-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <!-- Estilo Shadcn para el encabezado -->
                                <tr class="border-b dark:border-gray-600">
                                    <th scope="col" class="px-6 py-3 text-left align-middle font-medium">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left align-middle font-medium">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left align-middle font-medium">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left align-middle font-medium">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- El contenido se cargará dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Esperar a que el DOM esté completamente cargado
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar DataTables
            $('#users-table').DataTable({
                 dom: "<'flex items-center justify-between mb-4'lf>tr<'flex items-center justify-between mt-4'ip>",
                 drawCallback: function(settings) {
                    $('#users-table tbody tr').addClass('bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
                    $('#users-table tbody td').addClass('px-6 py-4 align-middle');

                    // Estilizar "Mostrar registros"
                    $('.dataTables_length label').addClass('inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300');
                    $('.dataTables_length select').addClass('block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300');

                    // Estilizar "Buscar"
                    $('.dataTables_filter label').addClass('inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300');
                    $('.dataTables_filter input').addClass('ml-2 block w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300');

                    // Estilizar la información
                    $('.dataTables_info').addClass('text-sm text-gray-700 dark:text-gray-300');

                    // Estilizar la paginación
                    $('.dataTables_paginate').addClass('mt-3');
                    $('.paginate_button').addClass('relative inline-flex items-center px-2 py-1 text-sm font-semibold text-gray-500 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-white');
                    $('.paginate_button.previous, .paginate_button.next').addClass('rounded');
                    $('.paginate_button.current').addClass('z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-gray-700 dark:border-indigo-500 dark:text-indigo-400');
                },
                processing: true,
                serverSide: true,
                ajax: "{{ route('usuarios') }}", // Tu ruta API
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                // Personalización del lenguaje si lo necesitas
                language: {
                    url: '{{ asset("js/datatables/es-ES.json") }}',
                              },
                // Aplicar clases de Tailwind a los elementos generados por DataTables
                drawCallback: function(settings) {
                    // Clases para las filas - similar a shadcn
                    $('#users-table tbody tr').addClass('bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
                    $('#users-table tbody td').addClass('px-6 py-4 align-middle');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
