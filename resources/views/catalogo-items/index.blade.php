@extends('layouts.app')

@section('title', 'Catalogo de Items')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <x-sinden.page-header title="Catalogo de Items" description="Gestion de productos y servicios del catalogo">
        <x-slot name="actions">
            @can('editar_catalogo_items')
            <x-sinden.button variant="outline" icon="bi bi-file-earmark-excel"
                href="{{ route('recepcion.items.export-excel') }}">Excel</x-sinden.button>
            <x-sinden.button variant="outline" icon="bi bi-file-earmark-pdf"
                href="{{ route('recepcion.items.export-pdf') }}">PDF</x-sinden.button>
            @endcan
            @can('crear_catalogo_items')
            <x-sinden.button variant="primary" icon="bi bi-plus-lg"
                href="{{ route('recepcion.items.create') }}">Nuevo Item</x-sinden.button>
            @endcan
        </x-slot>
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-tags" :value="$totalItems" title="Total Items" color="primary" />
        <x-sinden.stat-card icon="bi bi-check-circle" :value="$itemsActivos" title="Activos" color="success" />
        <x-sinden.stat-card icon="bi bi-gear" :value="$itemsServicios" title="Servicios" color="info" />
        <x-sinden.stat-card icon="bi bi-box" :value="$itemsMateriales" title="Materiales" color="warning" />
    </div>

    {{-- DataTable --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-list-ul me-2 text-primary"></i>Listado de Items
                </h6>
                <span class="badge bg-light text-muted border" id="totalRegistros"></span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sinden-datatable" id="itemsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Codigo</th>
                            <th>Descripcion</th>
                            <th>Categoria</th>
                            <th>Precio Unit.</th>
                            <th>IVA</th>
                            <th>Estado</th>
                            <th>Creado</th>
                            @if($canEdit)
                            <th class="text-end">Acciones</th>
                            @endif
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
    var columns = [
        { data: 'id', name: 'id', width: '55px', className: 'text-center text-muted' },
        { data: 'codigo', name: 'codigo', className: 'fw-semibold' },
        { data: 'descripcion', name: 'descripcion' },
        { data: 'categoria_label', name: 'categoria', orderable: true, searchable: true, className: 'text-center' },
        { data: 'precio_formato', name: 'precio_unitario', className: 'text-end', orderable: true, searchable: false },
        { data: 'iva_formato', name: 'porcentaje_iva', width: '60px', className: 'text-center', orderable: true, searchable: false },
        { data: 'estado', name: 'activo', orderable: true, searchable: false, className: 'text-center' },
        { data: 'created_at', name: 'created_at', width: '100px', className: 'text-center' }
    ];

    @if($canEdit)
    columns.push({ data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-end', width: '100px' });
    @endif

    var table = $('#itemsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route($routePrefix . ".items.index") }}',
        columns: columns,
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

@can('editar_catalogo_items')
function toggleActivo(itemId, codigo) {
    Swal.fire({
        title: 'Cambiar estado?',
        text: 'Desea cambiar el estado del item "' + codigo + '"?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4A7C59',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, cambiar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            fetch('{{ url("recepcion/items") }}/' + itemId + '/toggle-activo', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) { return r.json(); })
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
                    $('#itemsTable').DataTable().ajax.reload(null, false);
                }
            })
            .catch(function() {
                Swal.fire('Error', 'No se pudo cambiar el estado', 'error');
            });
        }
    });
}
@endcan
</script>
@endpush
