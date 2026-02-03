@extends('layouts.app')

@section('title', 'Gestión de Cuadrillas')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header con componente --}}
    <x-manzer.page-header title="Gestión de Cuadrillas" description="Organiza los equipos de trabajo">
        <x-slot name="actions">
            @can('crear_cuadrillas')
            <x-manzer.button variant="primary" icon="bi bi-plus-lg" data-bs-toggle="modal" data-bs-target="#createCuadrillaModal">
                Nueva Cuadrilla
            </x-manzer.button>
            @endcan
        </x-slot>
    </x-manzer.page-header>

    {{-- Summary Cards con iconos GRANDES --}}
    <div class="summary-cards">
        <x-manzer.stat-card
            icon="bi bi-people-fill"
            :value="$cuadrillas->count()"
            title="Total Cuadrillas"
            color="primary"
        />
        <x-manzer.stat-card
            icon="bi bi-check-circle-fill"
            :value="$cuadrillas->where('activa', true)->count()"
            title="Cuadrillas Activas"
            color="success"
        />
        <x-manzer.stat-card
            icon="bi bi-person-fill"
            :value="$cuadrillas->sum(fn($c) => $c->trabajadoresActivos->count())"
            title="Trabajadores Asignados"
            color="info"
        />
    </div>

    {{-- Filtros --}}
    <div class="filters-row">
        <form action="{{ route('cuadrillas.index') }}" method="GET" class="d-flex flex-wrap gap-3 align-items-end w-100">
            <div class="filter-group" style="flex: 2; min-width: 250px;">
                <label>Buscar</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Nombre de cuadrilla..." value="{{ request('search') }}">
            </div>
            <div class="filter-group" style="flex: 1; min-width: 150px;">
                <label>Estado</label>
                <select name="activa" class="form-select">
                    <option value="">Todas</option>
                    <option value="1" {{ request('activa') === '1' ? 'selected' : '' }}>Activas</option>
                    <option value="0" {{ request('activa') === '0' ? 'selected' : '' }}>Inactivas</option>
                </select>
            </div>
            <div class="filter-group" style="flex: 0 0 auto;">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i>Buscar
                </button>
            </div>
        </form>
    </div>

    {{-- Grid de Cuadrillas --}}
    <div class="row g-4">
        @forelse($cuadrillas as $cuadrilla)
        <div class="col-md-6 col-lg-4">
            <x-manzer.crew-card
                :cuadrilla="$cuadrilla"
                :can-edit="auth()->user()->can('editar_cuadrillas')"
                :can-delete="auth()->user()->can('eliminar_cuadrillas')"
            />
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state">
                <i class="bi bi-people d-block"></i>
                <h3>No hay cuadrillas</h3>
                <p>No se encontraron cuadrillas que coincidan con los filtros.</p>
                @can('crear_cuadrillas')
                <x-manzer.button variant="primary" icon="bi bi-plus-lg" data-bs-toggle="modal" data-bs-target="#createCuadrillaModal" class="mt-3">
                    Crear Primera Cuadrilla
                </x-manzer.button>
                @endcan
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal Crear Cuadrilla --}}
<x-manzer.modal id="createCuadrillaModal" title="Nueva Cuadrilla">
    <form action="{{ route('cuadrillas.store') }}" method="POST" id="createCuadrillaForm">
        @csrf
        <x-manzer.form-group
            label="Nombre"
            name="nombre"
            type="text"
            placeholder="Ej: Cuadrilla Norte"
            required
        />

        <x-manzer.form-group
            label="Capataz"
            name="capataz_id"
            type="select"
            help="Solo trabajadores propios activos"
        >
            <option value="">Sin asignar</option>
            @foreach($trabajadoresDisponibles as $trabajador)
                <option value="{{ $trabajador->id }}">{{ $trabajador->nombre_completo }}</option>
            @endforeach
        </x-manzer.form-group>

        <x-manzer.form-group
            label="Descripción"
            name="descripcion"
            type="textarea"
            placeholder="Descripción opcional..."
            :rows="3"
        />
    </form>

    <x-slot name="footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <x-manzer.button type="submit" variant="primary" icon="bi bi-check-lg" form="createCuadrillaForm">
            Crear Cuadrilla
        </x-manzer.button>
    </x-slot>
</x-manzer.modal>

{{-- Modal Editar Cuadrilla --}}
<x-manzer.modal id="editCuadrillaModal" title="Editar Cuadrilla">
    <form action="" method="POST" id="editCuadrillaForm">
        @csrf
        @method('PUT')

        <x-manzer.form-group
            label="Nombre"
            name="nombre"
            type="text"
            required
        />

        <x-manzer.form-group
            label="Capataz"
            name="capataz_id"
            type="select"
        >
            <option value="">Sin asignar</option>
            @foreach($trabajadoresDisponibles as $trabajador)
                <option value="{{ $trabajador->id }}">{{ $trabajador->nombre_completo }}</option>
            @endforeach
        </x-manzer.form-group>

        <x-manzer.form-group
            label="Descripción"
            name="descripcion"
            type="textarea"
            :rows="3"
        />

        <div class="mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activa" id="editActiva" value="1">
                <label class="form-check-label" for="editActiva">Cuadrilla Activa</label>
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <x-manzer.button type="submit" variant="primary" icon="bi bi-check-lg">
                Guardar Cambios
            </x-manzer.button>
        </x-slot>
    </form>
</x-manzer.modal>

{{-- Form Eliminar (oculto) --}}
<form id="deleteCuadrillaForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function editCuadrilla(cuadrillaId) {
    fetch(`{{ url('cuadrillas') }}/${cuadrillaId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('editCuadrillaForm').querySelector('[name="nombre"]').value = data.nombre;
        document.getElementById('editCuadrillaForm').querySelector('[name="capataz_id"]').value = data.capataz_id || '';
        document.getElementById('editCuadrillaForm').querySelector('[name="descripcion"]').value = data.descripcion || '';
        document.getElementById('editActiva').checked = data.activa;

        document.getElementById('editCuadrillaForm').action = `{{ url('cuadrillas') }}/${cuadrillaId}`;
        new bootstrap.Modal(document.getElementById('editCuadrillaModal')).show();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo cargar la cuadrilla', 'error');
    });
}

function deleteCuadrilla(cuadrillaId, cuadrillaNombre) {
    Swal.fire({
        title: '¿Eliminar cuadrilla?',
        text: `¿Estás seguro de eliminar "${cuadrillaNombre}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteCuadrillaForm');
            form.action = `{{ url('cuadrillas') }}/${cuadrillaId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
