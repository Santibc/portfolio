<x-app-layout>
    <x-slot name="header">
        {{ __('Usuarios') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                        <table id="users-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-b dark:border-gray-600">
                                    <th scope="col" class="px-6 py-3 text-left align-middle font-medium">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left align-middle font-medium">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left align-middle font-medium">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left align-middle font-medium">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize DataTables
            $('#users-table').DataTable({
                // (l)ength, (f)ilter, (t)able, (i)nfo, (p)agination
                dom: "<'flex items-center justify-between mb-4'<'w-auto'l><'w-auto'f>>" +
                     "<'w-full'tr>" +
                     "<'flex items-center justify-between mt-4'<'w-auto'i><'w-auto'p>>",
                processing: true,
                serverSide: true,
                ajax: "{{ route('usuarios') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                language: {
                    url: '{{ asset("js/datatables/es-ES.json") }}',
                },
                // Combined and corrected callback to apply Tailwind classes
                drawCallback: function(settings) {
                    // Style for rows, similar to Shadcn
                    $('#users-table tbody tr').addClass('bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
                    $('#users-table tbody td').addClass('px-6 py-4 align-middle');

                    // --- Styling for DataTables generated elements ---

                    const wrapper = $(this.api().table().container()).closest('.dataTables_wrapper');

                    // Style "Show entries" dropdown
                    wrapper.find('.dataTables_length label').addClass('inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300');
                    wrapper.find('.dataTables_length select').addClass('ml-2 mr-2 w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300');

                    // Style "Search" input
                    wrapper.find('.dataTables_filter label').addClass('inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300');
                    wrapper.find('.dataTables_filter input').addClass('ml-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300');

                    // Style table information text
                    wrapper.find('.dataTables_info').addClass('text-sm text-gray-700 dark:text-gray-300');

                    // Style pagination buttons
                    wrapper.find('.dataTables_paginate .paginate_button').addClass('relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-500 bg-white border border-gray-300 ms-1 hover:bg-gray-50 focus:outline-none disabled:opacity-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-white rounded-md');
                    wrapper.find('.dataTables_paginate .paginate_button.current').addClass('z-10 bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:text-white dark:border-indigo-500').removeClass('text-gray-500 bg-white dark:bg-gray-800 dark:text-gray-400');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>