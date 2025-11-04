<x-app-layout>
    <x-slot name="header">Clientes - Servicio Técnico</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 text-end">
                <a href="{{ route('st.clientes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Cliente
                </a>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <table id="clientesTable" class="table-responsive w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-100">
                            <tr>
                                <th>ID</th>
                                <th>Tipo Doc</th>
                                <th>Documento</th>
                                <th>Nombre / Razón Social</th>
                                <th>Celular</th>
                                <th>Email</th>
                                <th>Tipo Cliente</th>
                                <th>Equipos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
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
document.addEventListener('DOMContentLoaded', function() {
    const table = $('#clientesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        ajax: '{{ route("st.clientes.index") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'tipo_documento', name: 'tipo_documento' },
            { data: 'numero_documento', name: 'numero_documento' },
            { data: 'nombre_completo', name: 'nombre_completo' },
            { data: 'celular', name: 'celular' },
            { data: 'email', name: 'email' },
            { data: 'tipo_cliente_badge', name: 'tipo_cliente', orderable: false },
            { data: 'equipos_count', name: 'equipos_count', searchable: false },
            { data: 'estado_badge', name: 'activo', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});

function eliminar(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Se desactivará este cliente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, desactivar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/servicio-tecnico/clientes/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire('Desactivado!', response.message, 'success');
                    $('#clientesTable').DataTable().ajax.reload();
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo desactivar el cliente', 'error');
                }
            });
        }
    });
}
</script>
@endpush
</x-app-layout>
