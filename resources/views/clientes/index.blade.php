@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <x-sinden.page-header title="Clientes" description="Gestion de clientes de la empresa">
        <x-slot name="actions">
            <x-sinden.button variant="outline" icon="bi bi-file-earmark-excel"
                href="{{ route('recepcion.clientes.export-excel') }}">Excel</x-sinden.button>
            <x-sinden.button variant="outline" icon="bi bi-file-earmark-pdf"
                href="{{ route('recepcion.clientes.export-pdf') }}">PDF</x-sinden.button>
            <x-sinden.button variant="primary" icon="bi bi-plus-lg"
                href="{{ route('recepcion.clientes.create') }}">Nuevo Cliente</x-sinden.button>
        </x-slot>
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-people" :value="$totalClientes" title="Total Clientes" color="primary" />
        <x-sinden.stat-card icon="bi bi-person-check" :value="$clientesActivos" title="Activos" color="success" />
        <x-sinden.stat-card icon="bi bi-person-x" :value="$clientesInactivos" title="Inactivos" color="danger" />
        <x-sinden.stat-card icon="bi bi-person-plus" :value="$clientesRecientes" title="Ultimos 30 dias" color="info" />
    </div>

    {{-- DataTable --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-list-ul me-2 text-primary"></i>Listado de Clientes
                </h6>
                <span class="badge bg-light text-muted border" id="totalRegistros"></span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sinden-datatable" id="clientesTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Cedula/NIT</th>
                            <th>Correo</th>
                            <th>Celular (WhatsApp)</th>
                            <th>Estado</th>
                            <th>Creado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    var table = $('#clientesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("recepcion.clientes.index") }}',
        columns: [
            { data: 'id', name: 'id', width: '55px', className: 'text-center text-muted' },
            { data: 'nombre', name: 'nombre', className: 'fw-semibold' },
            { data: 'cedula', name: 'cedula' },
            { data: 'correo', name: 'correo' },
            { data: 'celular_1', name: 'celular_1' },
            { data: 'estado', name: 'activo', orderable: true, searchable: false, className: 'text-center' },
            { data: 'created_at', name: 'created_at', width: '100px', className: 'text-center' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-end', width: '120px' }
        ],
        dom: '<"d-flex flex-wrap align-items-center justify-content-between mb-2"<"d-flex align-items-center gap-2"lB>f>rt<"d-flex justify-content-between"ip>',
        buttons: [
            { extend: 'colvis', text: '<i class="bi bi-layout-three-columns"></i> Columnas', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[0, 'desc']],
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        drawCallback: function(settings) {
            var total = settings._iRecordsTotal || 0;
            $('#totalRegistros').text(total + ' registro' + (total !== 1 ? 's' : ''));
        }
    });
});

function toggleActivo(clienteId, nombre) {
    Swal.fire({
        title: 'Cambiar estado?',
        text: 'Desea cambiar el estado del cliente "' + nombre + '"?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#475569',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, cambiar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            fetch('{{ url("recepcion/clientes") }}/' + clienteId + '/toggle-activo', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) {
                if (!r.ok) return r.json().then(function(d) { throw d; });
                return r.json();
            })
            .then(function(data) {
                if (data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                    $('#clientesTable').DataTable().ajax.reload(null, false);
                }
            })
            .catch(function(err) {
                var msg = (err && err.message) ? err.message : 'No se pudo cambiar el estado';
                Swal.fire('Error', msg, 'error');
            });
        }
    });
}
</script>
@endpush
