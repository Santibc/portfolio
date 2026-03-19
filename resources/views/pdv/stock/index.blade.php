<x-app-layout>
    @section('title', 'Stock Disponible')

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Stock Disponible</h4>
        </div>

        {{-- Filtros --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small mb-1">Ubicacion</label>
                        <select id="filtroUbicacion" class="form-select form-select-sm">
                            <option value="">Todas las ubicaciones</option>
                            @foreach($ubicaciones as $ub)
                                <option value="{{ $ub->id }}">{{ $ub->nombre }} ({{ $ub->tipo }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm text-white w-100" style="background: var(--miracle-pink);" onclick="aplicarFiltro()">
                            <i class="bi bi-funnel me-1"></i>Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-outline-secondary w-100" onclick="limpiarFiltro()">
                            <i class="bi bi-x-lg me-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="stock-table" class="table table-hover mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Variante</th>
                                <th>Ubicacion</th>
                                <th class="text-center">Stock</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const table = $('#stock-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pdv.stock.index') }}",
                data: function(d) {
                    const ub = document.getElementById('filtroUbicacion').value;
                    if (ub) d.ubicacion_id = ub;
                }
            },
            columns: [
                { data: 'producto_nombre', name: 'producto_nombre' },
                { data: 'variante_nombre', name: 'variante_nombre', orderable: false },
                { data: 'ubicacion_nombre', name: 'ubicacion_nombre', orderable: false },
                { data: 'stock_display', name: 'cantidad_disponible', className: 'text-center' }
            ],
            order: [[0, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });

        function aplicarFiltro() {
            table.ajax.reload();
        }

        function limpiarFiltro() {
            document.getElementById('filtroUbicacion').value = '';
            table.ajax.reload();
        }
    </script>
    @endpush
</x-app-layout>
