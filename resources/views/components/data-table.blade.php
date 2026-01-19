{{--
    Componente: Data Table
    Uso: <x-data-table id="productosTable" :columns="$columns" />

    Props:
    - id: string - ID único de la tabla (requerido)
    - ajaxUrl: string - URL para cargar datos vía AJAX (requerido)
    - columns: array - Configuración de columnas (requerido)
    - searchable: bool - Si muestra buscador - default: true
    - pageLength: int - Registros por página - default: 10
    - responsive: bool - Si es responsive - default: true
    - exportButtons: bool - Si muestra botones de exportación - default: false
    - order: array - Orden inicial [columna, dirección] - default: [[0, 'asc']]

    Estructura de columns:
    [
        ['data' => 'id', 'title' => 'ID'],
        ['data' => 'nombre', 'title' => 'Nombre'],
        ['data' => 'action', 'title' => 'Acciones', 'orderable' => false, 'searchable' => false],
    ]

    Ejemplo:
    <x-data-table
        id="clientesTable"
        ajaxUrl="{{ route('clientes.data') }}"
        :columns="[
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'nombre', 'title' => 'Nombre'],
            ['data' => 'email', 'title' => 'Email'],
            ['data' => 'action', 'title' => 'Acciones', 'orderable' => false],
        ]"
        :exportButtons="true"
    />
--}}

@props([
    'id',
    'ajaxUrl',
    'columns',
    'searchable' => true,
    'pageLength' => 10,
    'responsive' => true,
    'exportButtons' => false,
    'order' => [[0, 'asc']],
])

@php
    $tableId = $id;
    $columnsJson = json_encode($columns);
    $orderJson = json_encode($order);
@endphp

<div {{ $attributes->merge(['class' => 'table-responsive']) }}>
    <table id="{{ $tableId }}" class="table table-striped table-hover" style="width: 100%;">
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th>{{ $column['title'] ?? '' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{-- Contenido cargado vía AJAX --}}
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const {{ $tableId }}Config = {
        processing: true,
        serverSide: true,
        ajax: '{{ $ajaxUrl }}',
        columns: {!! $columnsJson !!},
        order: {!! $orderJson !!},
        pageLength: {{ $pageLength }},
        responsive: {{ $responsive ? 'true' : 'false' }},
        searching: {{ $searchable ? 'true' : 'false' }},
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>',
            search: 'Buscar:',
            lengthMenu: 'Mostrar _MENU_ registros',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            infoEmpty: 'Mostrando 0 a 0 de 0 registros',
            infoFiltered: '(filtrado de _MAX_ registros totales)',
            loadingRecords: 'Cargando...',
            zeroRecords: 'No se encontraron registros',
            emptyTable: 'No hay datos disponibles',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                previous: '<i class="bi bi-chevron-left"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>'
            }
        },
        @if($exportButtons)
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm'
            },
            {
                extend: 'print',
                text: '<i class="bi bi-printer"></i> Imprimir',
                className: 'btn btn-secondary btn-sm'
            }
        ],
        @endif
    };

    window.{{ $tableId }} = $('#{{ $tableId }}').DataTable({{ $tableId }}Config);
});
</script>
@endpush
