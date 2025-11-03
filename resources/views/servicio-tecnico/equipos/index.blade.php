<x-app-layout>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-camera-video me-2"></i>Equipos</h2>
                    <p class="text-muted">Gestión de cámaras y equipos de seguridad</p>
                </div>
                <a href="{{ route('st.equipos.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Equipo
                </a>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('st.equipos.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="tipo_equipo" class="form-label">Tipo de Equipo</label>
                    <select name="tipo_equipo" id="tipo_equipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="Cámara IP" {{ request('tipo_equipo') == 'Cámara IP' ? 'selected' : '' }}>Cámara IP</option>
                        <option value="Cámara Análoga" {{ request('tipo_equipo') == 'Cámara Análoga' ? 'selected' : '' }}>Cámara Análoga</option>
                        <option value="DVR" {{ request('tipo_equipo') == 'DVR' ? 'selected' : '' }}>DVR</option>
                        <option value="NVR" {{ request('tipo_equipo') == 'NVR' ? 'selected' : '' }}>NVR</option>
                        <option value="Monitor" {{ request('tipo_equipo') == 'Monitor' ? 'selected' : '' }}>Monitor</option>
                        <option value="Otro" {{ request('tipo_equipo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="operativo" {{ request('estado') == 'operativo' ? 'selected' : '' }}>Operativo</option>
                        <option value="en_reparacion" {{ request('estado') == 'en_reparacion' ? 'selected' : '' }}>En Reparación</option>
                        <option value="fuera_servicio" {{ request('estado') == 'fuera_servicio' ? 'selected' : '' }}>Fuera de Servicio</option>
                        <option value="en_bodega" {{ request('estado') == 'en_bodega' ? 'selected' : '' }}>En Bodega</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="en_garantia" class="form-label">Garantía</label>
                    <select name="en_garantia" id="en_garantia" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('en_garantia') == '1' ? 'selected' : '' }}>En Garantía</option>
                        <option value="0" {{ request('en_garantia') == '0' ? 'selected' : '' }}>Sin Garantía</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <a href="{{ route('st.equipos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de equipos --}}
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="equiposTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Cliente</th>
                            <th>Marca/Modelo</th>
                            <th>N° Serie</th>
                            <th>IP/MAC</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Garantía</th>
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
    $('#equiposTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("st.equipos.index") }}',
            data: function(d) {
                d.tipo_equipo = $('#tipo_equipo').val();
                d.estado = $('#estado').val();
                d.en_garantia = $('#en_garantia').val();
            }
        },
        columns: [
            { data: 'tipo_equipo', name: 'tipo_equipo' },
            { data: 'cliente', name: 'cliente.nombre_completo', orderable: false },
            { data: 'marca_modelo', name: 'marca', orderable: false },
            { data: 'numero_serie', name: 'numero_serie' },
            { data: 'ip_mac', name: 'ip_address', orderable: false },
            { data: 'ubicacion_instalacion', name: 'ubicacion_instalacion' },
            { data: 'estado', name: 'estado' },
            { data: 'garantia', name: 'en_garantia', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    // Eliminar equipo
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
