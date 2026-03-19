<x-app-layout>
    @section('title', 'Configuración de Cajas')

    @push('styles')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    @endpush

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2"></i>Configuración de Cajas</h4>
            <div>
                <a href="{{ route('pdv.cajas.configuracion') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-gear me-1"></i>Configuración General
                </a>
                <a href="{{ route('pdv.cajas.form') }}" class="btn text-white" style="background: var(--miracle-pink);">
                    <i class="bi bi-plus-lg me-1"></i>Nueva Caja
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="tablaCajas" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Cajero Asignado</th>
                            <th>Estado</th>
                            <th>Activo</th>
                            <th width="120">Acciones</th>
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
        $(function() {
            $('#tablaCajas').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("pdv.cajas.index") }}',
                columns: [
                    { data: 'codigo', name: 'codigo' },
                    { data: 'nombre', name: 'nombre' },
                    { data: 'ubicacion_nombre', name: 'ubicacion_nombre', orderable: false },
                    { data: 'cajero_nombre', name: 'cajero_nombre', orderable: false },
                    { data: 'estado_badge', name: 'estado', orderable: false },
                    { data: 'activo_badge', name: 'activo', orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                order: [[0, 'asc']],
            });
        });

        function toggleEstado(id) {
            Swal.fire({
                title: '¿Cambiar estado?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/pdv/cajas/${id}/toggle`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            $('#tablaCajas').DataTable().ajax.reload();
                            Swal.fire('Listo', 'Estado actualizado', 'success');
                        } else {
                            Swal.fire('Error', data.error || 'Error al cambiar estado', 'error');
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
