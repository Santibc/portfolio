@extends('layouts.app')

@section('title', $cuadrilla->nombre)

@section('content')
<div class="container-fluid py-4">
    {{-- Crew Profile Header --}}
    <div class="crew-profile-header mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('cuadrillas.index') }}" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div class="crew-avatar {{ $cuadrilla->activa ? 'bg-primary' : 'bg-secondary' }}">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="crew-info">
                <h1 class="crew-name">{{ $cuadrilla->nombre }}</h1>
                <div class="crew-badges">
                    @if($cuadrilla->activa)
                        <x-manzer.badge variant="success">Activa</x-manzer.badge>
                    @else
                        <x-manzer.badge variant="secondary">Inactiva</x-manzer.badge>
                    @endif
                    <x-manzer.badge variant="info">{{ $cuadrilla->trabajadoresActivos->count() }} miembros</x-manzer.badge>
                    @if($cuadrilla->capataz)
                        <x-manzer.badge variant="warning">Capataz: {{ $cuadrilla->capataz->nombre_completo }}</x-manzer.badge>
                    @endif
                </div>
            </div>
        </div>
        <div class="crew-actions">
            @can('editar_cuadrillas')
            <x-manzer.button variant="outline-primary" icon="bi bi-pencil" :href="route('cuadrillas.edit', $cuadrilla)">
                Editar
            </x-manzer.button>
            @endcan
        </div>
    </div>

    {{-- Summary Cards --}}
    @php
        $obrasActivas = $cuadrilla->obras->where('pivot.activo', true);
        $trabajadoresPropios = $cuadrilla->trabajadoresActivos->where('tipo_relacion', 'propio')->count();
        $trabajadoresSubcontrata = $cuadrilla->trabajadoresActivos->where('tipo_relacion', 'subcontrata')->count();
    @endphp
    <div class="summary-cards">
        <x-manzer.stat-card
            icon="bi bi-person-fill"
            :value="$cuadrilla->trabajadoresActivos->count()"
            title="Miembros Activos"
            color="primary"
        />
        <x-manzer.stat-card
            icon="bi bi-person-badge"
            :value="$trabajadoresPropios"
            title="Propios"
            color="info"
        />
        <x-manzer.stat-card
            icon="bi bi-building"
            :value="$trabajadoresSubcontrata"
            title="Subcontrata"
            color="warning"
        />
        <x-manzer.stat-card
            icon="bi bi-geo-alt"
            :value="$obrasActivas->count()"
            title="Obras Asignadas"
            color="success"
        />
    </div>

    {{-- Main Content --}}
    <div class="row g-4">
        {{-- Left Column - Info Cards --}}
        <div class="col-lg-4">
            {{-- Información General --}}
            <div class="info-card mb-4">
                <div class="info-card-header">
                    <div class="info-card-icon bg-primary">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>
                    <h5 class="info-card-title">Información General</h5>
                </div>
                <div class="info-card-body">
                    <div class="info-item">
                        <span class="info-label">Nombre</span>
                        <span class="info-value fw-bold">{{ $cuadrilla->nombre }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Estado</span>
                        <span class="info-value">
                            @if($cuadrilla->activa)
                                <x-manzer.badge variant="success">Activa</x-manzer.badge>
                            @else
                                <x-manzer.badge variant="secondary">Inactiva</x-manzer.badge>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Capataz</span>
                        <span class="info-value">
                            @if($cuadrilla->capataz)
                                <a href="{{ route('trabajadores.show', $cuadrilla->capataz) }}" class="text-decoration-none">
                                    <i class="bi bi-person-badge me-1"></i>{{ $cuadrilla->capataz->nombre_completo }}
                                </a>
                            @else
                                <span class="text-muted">Sin asignar</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Miembros</span>
                        <span class="info-value fw-bold">{{ $cuadrilla->trabajadoresActivos->count() }}</span>
                    </div>
                    @if($cuadrilla->descripcion)
                    <div class="info-item flex-column align-items-start">
                        <span class="info-label mb-1">Descripción</span>
                        <span class="info-value text-start">{{ $cuadrilla->descripcion }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Obras Asignadas --}}
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon bg-success">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h5 class="info-card-title">Obras Asignadas</h5>
                </div>
                <div class="info-card-body">
                    @if($obrasActivas->count() > 0)
                        <div class="obras-list">
                            @foreach($obrasActivas as $obra)
                            <div class="obra-item">
                                <div class="obra-icon">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div class="obra-info">
                                    <span class="obra-nombre">{{ $obra->nombre }}</span>
                                    <small class="obra-cliente text-muted">{{ $obra->cliente?->nombre ?? 'Sin cliente' }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            <span>Sin obras asignadas</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column - Members --}}
        <div class="col-lg-8">
            {{-- Miembros Activos --}}
            <div class="content-tabs-card">
                <div class="tabs-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-fill me-2"></i>Miembros de la Cuadrilla</h5>
                    @can('editar_cuadrillas')
                    <x-manzer.button variant="primary" icon="bi bi-plus-lg" size="sm" data-bs-toggle="modal" data-bs-target="#addTrabajadorModal">
                        Añadir Miembro
                    </x-manzer.button>
                    @endcan
                </div>
                <div class="tabs-content">
                    @if($cuadrilla->trabajadoresActivos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Trabajador</th>
                                    <th>DNI</th>
                                    <th>Tipo</th>
                                    <th>Incorporación</th>
                                    <th>Contacto</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cuadrilla->trabajadoresActivos as $trabajador)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm {{ $trabajador->id === $cuadrilla->capataz_id ? 'bg-warning' : 'bg-primary' }} text-white me-3">
                                                {{ strtoupper(substr($trabajador->nombre, 0, 1)) }}{{ strtoupper(substr($trabajador->apellidos, 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="fw-medium">
                                                    {{ $trabajador->nombre_completo }}
                                                    @if($trabajador->id === $cuadrilla->capataz_id)
                                                        <x-manzer.badge variant="warning">Capataz</x-manzer.badge>
                                                    @endif
                                                </span>
                                                <small class="d-block text-muted">{{ $trabajador->categoria_convenio ?? 'Sin categoría' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>{{ $trabajador->dni }}</code></td>
                                    <td>
                                        @if($trabajador->tipo_relacion === 'propio')
                                            <x-manzer.badge variant="primary">Propio</x-manzer.badge>
                                        @else
                                            <x-manzer.badge variant="warning">Subcontrata</x-manzer.badge>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $trabajador->pivot->fecha_incorporacion ? \Carbon\Carbon::parse($trabajador->pivot->fecha_incorporacion)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td>
                                        @if($trabajador->telefono)
                                            <a href="tel:{{ $trabajador->telefono }}" class="text-decoration-none">
                                                <i class="bi bi-telephone me-1"></i>{{ $trabajador->telefono }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('trabajadores.show', $trabajador) }}" class="btn btn-sm btn-outline-info" title="Ver trabajador">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @can('editar_cuadrillas')
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="removeTrabajador({{ $trabajador->id }}, '{{ $trabajador->nombre_completo }}')"
                                                    title="Quitar de cuadrilla">
                                                <i class="bi bi-person-dash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-tab-state">
                        <i class="bi bi-people"></i>
                        <h5>Sin miembros</h5>
                        <p>Esta cuadrilla no tiene miembros asignados.</p>
                        @can('editar_cuadrillas')
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTrabajadorModal">
                            <i class="bi bi-plus-lg me-1"></i>Añadir Primer Miembro
                        </button>
                        @endcan
                    </div>
                    @endif
                </div>
            </div>

            {{-- Historial de Miembros --}}
            @php $trabajadoresAnteriores = $cuadrilla->trabajadores->where('pivot.activo', false); @endphp
            @if($trabajadoresAnteriores->count() > 0)
            <div class="content-tabs-card mt-4">
                <div class="tabs-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Miembros</h5>
                </div>
                <div class="tabs-content">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Trabajador</th>
                                    <th>Incorporación</th>
                                    <th>Salida</th>
                                    <th>Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trabajadoresAnteriores as $trabajador)
                                @php
                                    $fechaIncorp = $trabajador->pivot->fecha_incorporacion ? \Carbon\Carbon::parse($trabajador->pivot->fecha_incorporacion) : null;
                                    $fechaSalida = $trabajador->pivot->fecha_salida ? \Carbon\Carbon::parse($trabajador->pivot->fecha_salida) : null;
                                    $duracion = $fechaIncorp && $fechaSalida ? $fechaIncorp->diffInDays($fechaSalida) : null;
                                @endphp
                                <tr class="text-muted">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-secondary text-white me-3">
                                                {{ strtoupper(substr($trabajador->nombre, 0, 1)) }}{{ strtoupper(substr($trabajador->apellidos, 0, 1)) }}
                                            </div>
                                            <span>{{ $trabajador->nombre_completo }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $fechaIncorp?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ $fechaSalida?->format('d/m/Y') ?? '-' }}</td>
                                    <td>
                                        @if($duracion !== null)
                                            <x-manzer.badge variant="secondary">{{ $duracion }} días</x-manzer.badge>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Añadir Trabajador --}}
<div class="modal fade" id="addTrabajadorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('cuadrillas.trabajadores.add', $cuadrilla) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Añadir Miembro a la Cuadrilla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($trabajadoresDisponibles->count() > 0)
                    <x-manzer.form-group label="Seleccionar Trabajador" name="trabajador_id" type="select" required>
                        <option value="">Seleccionar...</option>
                        @foreach($trabajadoresDisponibles as $trabajador)
                            <option value="{{ $trabajador->id }}">
                                {{ $trabajador->nombre_completo }}
                                ({{ $trabajador->tipo_relacion === 'propio' ? 'Propio' : 'Subcontrata' }})
                            </option>
                        @endforeach
                    </x-manzer.form-group>
                    <x-manzer.alert type="info" message="Si el trabajador está en otra cuadrilla, será transferido automáticamente a esta." />
                    @else
                    <x-manzer.alert type="warning" message="No hay trabajadores disponibles para añadir. Todos los trabajadores activos ya están en esta cuadrilla." />
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    @if($trabajadoresDisponibles->count() > 0)
                    <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Añadir a Cuadrilla</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Form Quitar Trabajador --}}
<form id="removeTrabajadorForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<style>
/* Crew Profile Header */
.crew-profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--manzer-white, #fff);
    padding: 1.5rem 2rem;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.crew-avatar {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    margin-right: 1.5rem;
}

.crew-info {
    flex: 1;
}

.crew-name {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--manzer-primary, #4A7C59);
    margin: 0 0 0.5rem 0;
}

.crew-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.crew-actions {
    display: flex;
    gap: 0.75rem;
}

/* Info Card */
.info-card {
    background: var(--manzer-white, #fff);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.info-card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, rgba(74, 124, 89, 0.05) 0%, rgba(74, 124, 89, 0.1) 100%);
    border-bottom: 1px solid var(--manzer-border, #e5e7eb);
}

.info-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.info-card-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--manzer-text-primary, #1f2937);
}

.info-card-body {
    padding: 1.25rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--manzer-border, #e5e7eb);
}

.info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.info-label {
    color: var(--manzer-text-secondary, #6b7280);
    font-size: 0.9rem;
    min-width: 100px;
}

.info-value {
    text-align: right;
    flex: 1;
    color: var(--manzer-text-primary, #1f2937);
}

/* Obras List */
.obras-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.obra-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: var(--manzer-light, #f8f9fa);
    border-radius: 10px;
    transition: all 0.2s;
}

.obra-item:hover {
    background: rgba(74, 124, 89, 0.1);
}

.obra-icon {
    width: 40px;
    height: 40px;
    background: var(--manzer-primary, #4A7C59);
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.obra-info {
    display: flex;
    flex-direction: column;
}

.obra-nombre {
    font-weight: 600;
    color: var(--manzer-text-primary, #1f2937);
}

.obra-cliente {
    font-size: 0.85rem;
}

/* Content Tabs Card */
.content-tabs-card {
    background: var(--manzer-white, #fff);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.tabs-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, rgba(74, 124, 89, 0.05) 0%, rgba(74, 124, 89, 0.1) 100%);
    border-bottom: 1px solid var(--manzer-border, #e5e7eb);
}

.tabs-header h5 {
    color: var(--manzer-primary, #4A7C59);
    font-weight: 600;
}

.tabs-content {
    padding: 0;
}

.tabs-content .table {
    margin-bottom: 0;
}

.tabs-content .table thead {
    background: var(--manzer-light, #f8f9fa);
}

.tabs-content .table th {
    font-weight: 600;
    color: var(--manzer-text-primary, #1f2937);
    border-bottom: 2px solid var(--manzer-border, #e5e7eb);
    padding: 1rem 1.25rem;
}

.tabs-content .table td {
    padding: 1rem 1.25rem;
    vertical-align: middle;
}

/* Empty State */
.empty-tab-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--manzer-text-secondary, #6b7280);
}

.empty-tab-state i {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 1rem;
    display: block;
}

.empty-tab-state h5 {
    color: var(--manzer-text-primary, #1f2937);
    margin-bottom: 0.5rem;
}

.empty-tab-state p {
    margin-bottom: 1.5rem;
}

.empty-tab-state .btn {
    padding: 0.375rem 0.75rem !important;
    font-size: 0.875rem !important;
}

/* Avatar */
.avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: 600;
}

.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 0.85rem;
}

/* Responsive */
@media (max-width: 992px) {
    .crew-profile-header {
        flex-direction: column;
        gap: 1.5rem;
        text-align: center;
    }

    .crew-profile-header > .d-flex {
        flex-direction: column;
    }

    .crew-avatar {
        margin: 0 auto 1rem;
    }

    .crew-badges {
        justify-content: center;
    }

    .crew-actions {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
function removeTrabajador(trabajadorId, trabajadorNombre) {
    Swal.fire({
        title: '¿Quitar de la cuadrilla?',
        text: `¿Estás seguro de quitar a "${trabajadorNombre}" de esta cuadrilla?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('removeTrabajadorForm');
            form.action = `{{ url('cuadrillas/' . $cuadrilla->id . '/trabajadores') }}/${trabajadorId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
