<x-app-layout>
    <x-slot name="header">Clientes - Servicio Técnico</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">Listado de Clientes</h4>
                        <a href="{{ route('st.clientes.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Nuevo Cliente
                        </a>
                    </div>

                    {{-- Filtros --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Buscar (nombre, documento, email)</label>
                            <input type="text" id="filtroBusqueda" class="form-control"
                                   placeholder="Escribe para filtrar...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Tipo de cliente</label>
                            <select id="filtroTipoCliente" class="form-select select2-search"
                                    data-placeholder="Todos" data-allow-clear="1">
                                <option value=""></option>
                                <option value="particular">Particular</option>
                                <option value="empresa">Empresa</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Tipo doc.</label>
                            <select id="filtroTipoDoc" class="form-select select2-search"
                                    data-placeholder="Todos" data-allow-clear="1">
                                <option value=""></option>
                                <option value="CC">CC</option>
                                <option value="NIT">NIT</option>
                                <option value="CE">CE</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Estado</label>
                            <select id="filtroActivo" class="form-select select2-search"
                                    data-placeholder="Todos" data-allow-clear="1">
                                <option value=""></option>
                                <option value="1" selected>Activos</option>
                                <option value="0">Inactivos</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" id="btnLimpiarFiltros" class="btn btn-outline-secondary w-100" title="Limpiar filtros">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </div>

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
        ajax: {
            url: '{{ route("st.clientes.index") }}',
            data: function (d) {
                d.busqueda = $('#filtroBusqueda').val();
                d.tipo_cliente = $('#filtroTipoCliente').val();
                d.tipo_documento = $('#filtroTipoDoc').val();
                d.activo = $('#filtroActivo').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'tipo_documento', name: 'tipo_documento' },
            { data: 'numero_documento', name: 'numero_identificacion' },
            { data: 'nombre_completo', name: 'nombre_contacto' },
            { data: 'celular', name: 'celular' },
            { data: 'email', name: 'email' },
            { data: 'tipo_cliente_badge', name: 'tipo_cliente', orderable: false },
            { data: 'equipos_count', name: 'equipos_count', searchable: false },
            { data: 'estado_badge', name: 'activo', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: { url: '{{ asset("js/datatables/es-ES.json") }}' }
    });

    // Auto-aplicar filtros
    $('#filtroTipoCliente, #filtroActivo, #filtroTipoDoc').on('change', function () {
        table.ajax.reload();
    });

    var bTimer = null;
    $('#filtroBusqueda').on('input', function () {
        clearTimeout(bTimer);
        bTimer = setTimeout(function () { table.ajax.reload(); }, 300);
    });

    // Limpiar filtros
    $('#btnLimpiarFiltros').on('click', function () {
        $('#filtroBusqueda').val('');
        $('#filtroTipoCliente').val(null).trigger('change');
        $('#filtroTipoDoc').val(null).trigger('change');
        $('#filtroActivo').val('1').trigger('change'); // vuelve al default "Activos"
    });

    // Cargar inicialmente con activo=1
    $('#filtroActivo').trigger('change');
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
