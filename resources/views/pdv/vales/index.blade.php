<x-app-layout>
    @section('title', 'Vales de Caja')
    @push('styles')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    @endpush

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-ticket-perforated me-2"></i>Vales de Caja</h4>
            <div>
                @if($sesionActiva)
                    <button class="btn text-white" style="background: var(--miracle-pink);" onclick="nuevoVale()">
                        <i class="bi bi-plus-lg me-1"></i>Nuevo Vale
                    </button>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select id="filtroEstado" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="redimido">Redimido</option>
                            <option value="anulado">Anulado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filtroCaja" class="form-select form-select-sm">
                            <option value="">Todas las cajas</option>
                            @foreach($cajas as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="tablaVales" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Caja</th>
                            <th>Descripción</th>
                            <th class="text-end">Monto</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th width="100">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let tabla;
        $(function() {
            tabla = $('#tablaVales').DataTable({
                processing: true, serverSide: true,
                ajax: { url: '{{ route("pdv.vales.index") }}', data: d => { d.estado = $('#filtroEstado').val(); d.caja_id = $('#filtroCaja').val(); } },
                columns: [
                    { data: 'caja_nombre', orderable: false },
                    { data: 'descripcion' },
                    { data: 'monto', render: v => '$' + parseFloat(v).toFixed(2), className: 'text-end' },
                    { data: 'usuario_nombre', orderable: false },
                    { data: 'estado_badge', orderable: false },
                    { data: 'created_at' },
                    { data: 'action', orderable: false, searchable: false },
                ],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                order: [[5, 'desc']],
            });
            $('#filtroEstado, #filtroCaja').change(() => tabla.ajax.reload());
        });

        function nuevoVale() {
            Swal.fire({
                title: 'Nuevo Vale',
                html: `<input id="swalDesc" class="form-control mb-2" placeholder="Descripción (ej: Vale café cliente)">
                    <div class="input-group"><span class="input-group-text">$</span><input id="swalMonto" class="form-control" type="number" step="0.01" min="0.01" placeholder="Monto"></div>`,
                showCancelButton: true, confirmButtonText: 'Crear Vale',
                preConfirm: () => {
                    const desc = document.getElementById('swalDesc').value;
                    const monto = document.getElementById('swalMonto').value;
                    if (!desc || !monto || parseFloat(monto) <= 0) { Swal.showValidationMessage('Complete todos los campos'); return false; }
                    return { descripcion: desc, monto: parseFloat(monto) };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('{{ route("pdv.vales.guardar") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify(result.value),
                    }).then(r => r.json()).then(data => {
                        Swal.fire(data.exito ? 'Creado' : 'Error', data.mensaje, data.exito ? 'success' : 'error');
                        tabla.ajax.reload();
                    });
                }
            });
        }

        function redimirVale(id) {
            Swal.fire({ title: '¿Redimir vale?', icon: 'question', showCancelButton: true, confirmButtonText: 'Redimir' })
            .then(result => {
                if (result.isConfirmed) {
                    fetch(`/pdv/vales/${id}/redimir`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
                    .then(r => r.json()).then(data => { Swal.fire(data.exito ? 'Redimido' : 'Error', data.mensaje, data.exito ? 'success' : 'error'); tabla.ajax.reload(); });
                }
            });
        }

        function anularVale(id) {
            Swal.fire({ title: 'Anular Vale', input: 'textarea', inputLabel: 'Motivo', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Anular',
                inputValidator: v => !v || v.length < 5 ? 'Mínimo 5 caracteres' : null })
            .then(result => {
                if (result.isConfirmed) {
                    fetch(`/pdv/vales/${id}/anular`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({ motivo_anulacion: result.value }) })
                    .then(r => r.json()).then(data => { Swal.fire(data.exito ? 'Anulado' : 'Error', data.mensaje, data.exito ? 'success' : 'error'); tabla.ajax.reload(); });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
