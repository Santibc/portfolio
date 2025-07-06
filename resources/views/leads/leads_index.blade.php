<x-app-layout>
    <x-slot name="header">
        {{ __('Leads') }}
    </x-slot>

    <div class="py-12" style="padding-top: 0;"  >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h4 class="text-2xl font-semibold mb-4">Leads</h4>

                    <div class="border dark:border-gray-700 rounded-lg">
                        <div class="overflow-x-auto">

                        <table id="leads-table" class="table-responsive w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-b dark:border-gray-600">
                                    <th class="px-6 py-3" data-priority="1">Acciones</th>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3">Teléfono </th>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = $('#leads-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        autoWidth: false,
        ajax: "{{ route('leads') }}",
columns: [
        { // Columna de Acciones - MOVIDA AL INICIO
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        className: 'noVis' // Puedes añadir 'noVis' si no quieres que sea ocultable por ColVis
    },
    { data: 'nombre', name: 'nombre' },
    { data: 'email', name: 'email' },
    { data: 'telefono', name: 'telefono' },
    { data: 'instagram_user', name: 'instagram_user' },
],

        // 🔧 Distribuir controles y paginación
dom: "<'flex flex-wrap justify-between items-center mb-4'<'relative'B>f>" + 
     "t" + 
     "<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>"
        ,

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
            },
{
    text: 'Importar leads',
    className: 'btn btn-outline-info',
    action: function () {
        const tableWrapper = $('#leads-table').closest('.dataTables_wrapper');

        // 🔄 Mostrar loader o deshabilitar controles
        tableWrapper.css('opacity', '0.5');
        Swal.fire({
            title: 'Importando leads...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // ✅ Llamada AJAX para importar
        $.ajax({
            url: "{{ route('importar_leads') }}", // <-- Asegúrate de definir esta ruta
            method: 'GET',
            data: {
                _token: '{{ csrf_token() }}' // Necesario si es una ruta protegida por CSRF
            },
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Importación completa',
                    text: response.message || 'Los leads se importaron correctamente'
                });

                // 🔄 Recargar tabla
                $('#leads-table').DataTable().ajax.reload();
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al importar',
                    text: xhr.responseJSON?.message || 'Ocurrió un error inesperado'
                });
            },
            complete: function () {
                tableWrapper.css('opacity', '1');
            }
        });
    }
},

{
    text: 'Nuevo',
    className: 'btn btn-outline-primary',
    action: function () {
        window.location.href = "{{ route('usuarios.form') }}";
    }
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
                .addClass('bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded shadow-md mt-2 p-2')
                .css({
                    position: 'absolute',
                    'z-index': 999,
                    top: 'calc(100% + 0.5rem)',
                    left: '0',
                    right: 'auto'
                });

            $('.dt-button-collection button')
                .removeClass()
                .addClass('block w-full text-left text-sm text-gray-800 dark:text-gray-200 px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150');
        }, 50);
    });
});
</script>
@endpush

</x-app-layout>