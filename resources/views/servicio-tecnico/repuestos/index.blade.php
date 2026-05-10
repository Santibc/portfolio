<x-app-layout>
    <x-slot name="header">Repuestos</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted">Gestión de inventario de repuestos y accesorios</p>
                        </div>
                <a href="{{ route('st.repuestos.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Repuesto
                </a>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('st.repuestos.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select name="categoria" id="categoria" class="form-select select2-search"
                            data-placeholder="Todas" data-allow-clear="1">
                        <option value=""></option>
                        <option value="Lente" {{ request('categoria') == 'Lente' ? 'selected' : '' }}>Lente</option>
                        <option value="Sensor" {{ request('categoria') == 'Sensor' ? 'selected' : '' }}>Sensor</option>
                        <option value="Fuente de poder" {{ request('categoria') == 'Fuente de poder' ? 'selected' : '' }}>Fuente de poder</option>
                        <option value="Cable" {{ request('categoria') == 'Cable' ? 'selected' : '' }}>Cable</option>
                        <option value="Conector" {{ request('categoria') == 'Conector' ? 'selected' : '' }}>Conector</option>
                        <option value="Disco duro" {{ request('categoria') == 'Disco duro' ? 'selected' : '' }}>Disco duro</option>
                        <option value="Carcasa" {{ request('categoria') == 'Carcasa' ? 'selected' : '' }}>Carcasa</option>
                        <option value="LED IR" {{ request('categoria') == 'LED IR' ? 'selected' : '' }}>LED IR</option>
                        <option value="Otro" {{ request('categoria') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="buscar" class="form-label">Buscar</label>
                    <input type="text" name="buscar" id="buscar" class="form-control"
                           value="{{ request('buscar') }}" placeholder="Código o nombre">
                </div>
                <div class="col-md-3">
                    <label for="stock_bajo" class="form-label">Stock</label>
                    <select name="stock_bajo" id="stock_bajo" class="form-select select2-search"
                            data-placeholder="Todos" data-allow-clear="1">
                        <option value=""></option>
                        <option value="1" {{ request('stock_bajo') == '1' ? 'selected' : '' }}>Stock Bajo</option>
                        <option value="0" {{ request('stock_bajo') == '0' ? 'selected' : '' }}>Stock Normal</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <a href="{{ route('st.repuestos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Resumen de stock --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Repuestos</h6>
                    <h3 class="text-primary">{{ $stats['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted">En Stock</h6>
                    <h3 class="text-success">{{ $stats['en_stock'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6 class="text-muted">Stock Bajo</h6>
                    <h3 class="text-warning">{{ $stats['stock_bajo'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h6 class="text-muted">Sin Stock</h6>
                    <h3 class="text-danger">{{ $stats['sin_stock'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de repuestos --}}
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="repuestosTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Marca/Modelo</th>
                            <th>Stock Actual</th>
                            <th>Stock Mín.</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DataTable will populate this --}}
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
$(document).ready(function() {
    var repuestosTable = $('#repuestosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("st.repuestos.index") }}',
            data: function(d) {
                d.categoria = $('#categoria').val();
                d.buscar = $('#buscar').val();
                d.stock_bajo = $('#stock_bajo').val();
            }
        },
        columns: [
            { data: 'codigo', name: 'codigo' },
            { data: 'nombre', name: 'nombre' },
            { data: 'categoria', name: 'categoria' },
            { data: 'marca_modelo', name: 'marca_modelo', orderable: false },
            { data: 'stock_actual', name: 'stock_actual' },
            { data: 'stock_minimo', name: 'stock_minimo' },
            { data: 'precio_compra', name: 'precio_costo' },
            { data: 'estado', name: 'activo', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: { url: '{{ asset("js/datatables/es-ES.json") }}' }
    });

    // Auto-aplicar filtros al cambiar / escribir
    $('#categoria, #stock_bajo').on('change', function () { repuestosTable.ajax.reload(); });
    var buscarTimer = null;
    $('#buscar').on('input', function () {
        clearTimeout(buscarTimer);
        buscarTimer = setTimeout(function () { repuestosTable.ajax.reload(); }, 300);
    });

    // Eliminar repuesto
    $(document).on('click', '.btn-eliminar', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede revertir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
</x-app-layout>
