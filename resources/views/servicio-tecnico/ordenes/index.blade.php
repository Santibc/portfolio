<x-app-layout>
    <x-slot name="header">Órdenes de Servicio</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row mb-4">
                <div class="col-md-12 text-end">
                    <a href="{{ route('st.ordenes.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Nueva Orden
                    </a>
                </div>
            </div>

    {{-- Filtros --}}
    <div class="card shadow mb-3">
        <div class="card-body">
            <form id="filtrosForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" id="filtroEstado">
                        <option value="">Todos</option>
                        <option value="recibida">Recibida</option>
                        <option value="asignada">Asignada</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="pendiente_repuestos">Pendiente Repuestos</option>
                        <option value="completada">Completada</option>
                        <option value="entregada">Entregada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Prioridad</label>
                    <select name="prioridad" class="form-select" id="filtroPrioridad">
                        <option value="">Todas</option>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Técnico</label>
                    <select name="tecnico_id" class="form-select" id="filtroTecnico">
                        <option value="">Todos</option>
                        @foreach($tecnicos as $tecnico)
                            <option value="{{ $tecnico->id }}">{{ $tecnico->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary w-100" onclick="limpiarFiltros()">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table id="ordenesTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>N° Orden</th>
                        <th>Cliente</th>
                        <th>Técnico</th>
                        <th>Tipo Servicio</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha Recepción</th>
                        <th>Días</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
            </div>
        </div>
    </div>

@push('scripts')
<script>
let table;

$(document).ready(function() {
    table = $('#ordenesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("st.ordenes.index") }}',
            data: function(d) {
                d.estado = $('#filtroEstado').val();
                d.prioridad = $('#filtroPrioridad').val();
                d.tecnico_id = $('#filtroTecnico').val();
            }
        },
        columns: [
            { data: 'numero_orden', name: 'numero_orden' },
            { data: 'cliente_nombre', name: 'cliente_nombre' },
            { data: 'tecnico_nombre', name: 'tecnico_nombre' },
            { data: 'tipo_servicio', name: 'tipo_servicio' },
            { data: 'prioridad_badge', name: 'prioridad', orderable: false },
            { data: 'estado_badge', name: 'estado', orderable: false },
            { data: 'fecha_recepcion', name: 'fecha_recepcion' },
            { data: 'dias_transcurridos', name: 'dias_transcurridos', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[6, 'desc']],
        language: { url: '{{ asset("js/datatables/es-ES.json") }}' }
    });

    // Aplicar filtros al cambiar
    $('#filtroEstado, #filtroPrioridad, #filtroTecnico').on('change', function() {
        table.ajax.reload();
    });
});

function limpiarFiltros() {
    $('#filtrosForm')[0].reset();
    table.ajax.reload();
}

function cambiarEstado(ordenId) {
    // Implementar modal para cambiar estado
    Swal.fire({
        title: 'Cambiar Estado',
        input: 'select',
        inputOptions: {
            'recibida': 'Recibida',
            'asignada': 'Asignada',
            'en_proceso': 'En Proceso',
            'pendiente_repuestos': 'Pendiente Repuestos',
            'completada': 'Completada',
            'entregada': 'Entregada',
            'cancelada': 'Cancelada'
        },
        inputPlaceholder: 'Seleccione un estado',
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Debe seleccionar un estado'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/servicio-tecnico/ordenes/${ordenId}/cambiar-estado`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    nuevo_estado: result.value
                },
                success: function(response) {
                    Swal.fire('Actualizado!', response.message, 'success');
                    table.ajax.reload();
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo cambiar el estado', 'error');
                }
            });
        }
    });
}
</script>
@endpush
</x-app-layout>
