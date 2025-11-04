<x-app-layout>
    <x-slot name="header">Técnicos</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted">Gestión de personal técnico</p>
                        </div>
                <a href="{{ route('st.tecnicos.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Nuevo Técnico
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtros</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Especialidad</label>
                    <input type="text" id="filtro-especialidad" class="form-control" placeholder="Buscar por especialidad...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select id="filtro-activo" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" id="btn-limpiar-filtros" class="btn btn-secondary w-100">
                        <i class="bi bi-x-circle me-2"></i>Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-table me-2"></i>Listado de Técnicos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabla-tecnicos" class="table table-hover table-striped" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre Completo</th>
                            <th>Documento</th>
                            <th>Especialidad</th>
                            <th>Contacto</th>
                            <th>Fecha Ingreso</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Los datos se cargan vía AJAX --}}
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: white !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #0b5ed7 !important;
        border-color: #0b5ed7 !important;
        color: white !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    const table = $('#tabla-tecnicos').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('st.tecnicos.index') }}",
            data: function(d) {
                d.especialidad = $('#filtro-especialidad').val();
                d.activo = $('#filtro-activo').val();
            }
        },
        columns: [
            { data: 'id', name: 'id', width: '50px' },
            { data: 'codigo', name: 'codigo', width: '100px' },
            { data: 'nombre_completo', name: 'nombre_completo' },
            { data: 'documento', name: 'documento' },
            { data: 'especialidad', name: 'especialidad' },
            { data: 'contacto', name: 'contacto', orderable: false, searchable: false },
            { data: 'fecha_ingreso', name: 'fecha_ingreso' },
            { data: 'estado', name: 'activo', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 25,
        order: [[0, 'desc']]
    });

    // Aplicar filtros
    $('#filtro-especialidad, #filtro-activo').on('change keyup', function() {
        table.draw();
    });

    // Limpiar filtros
    $('#btn-limpiar-filtros').on('click', function() {
        $('#filtro-especialidad').val('');
        $('#filtro-activo').val('');
        table.draw();
    });

    // Confirmación de eliminación
    $(document).on('click', '.btn-eliminar', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: '¿Estás seguro?',
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
