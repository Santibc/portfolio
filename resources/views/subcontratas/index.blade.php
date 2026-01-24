@extends('layouts.app')

@section('title', 'Gestión de Subcontratas')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de Subcontratas</h1>
            <p class="text-muted mb-0">Administra las empresas subcontratadas</p>
        </div>
        @can('crear_subcontratas')
        <a href="{{ route('subcontratas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nueva Subcontrata
        </a>
        @endcan
    </div>

    <!-- Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-people-fill text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total Subcontratas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['activas'] }}</h3>
                            <small class="text-muted">Activas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-award-fill text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['homologadas'] }}</h3>
                            <small class="text-muted">Homologadas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-{{ $stats['documentos_vencidos'] > 0 ? 'danger' : 'warning' }} bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-exclamation-triangle-fill text-{{ $stats['documentos_vencidos'] > 0 ? 'danger' : 'warning' }} fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['documentos_vencidos'] }}</h3>
                            <small class="text-muted">Docs. Vencidos</small>
                            @if($stats['documentos_proximos'] > 0)
                                <br><small class="text-warning">{{ $stats['documentos_proximos'] }} próximos</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('subcontratas.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nombre, razón social, CIF..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="activa" class="form-select">
                        <option value="">Todas</option>
                        <option value="1" {{ request('activa') === '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ request('activa') === '0' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Homologación</label>
                    <select name="homologada" class="form-select">
                        <option value="">Todas</option>
                        <option value="1" {{ request('homologada') === '1' ? 'selected' : '' }}>Homologadas</option>
                        <option value="0" {{ request('homologada') === '0' ? 'selected' : '' }}>No homologadas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Documentación</label>
                    <select name="docs_vencidos" class="form-select">
                        <option value="">Todas</option>
                        <option value="1" {{ request('docs_vencidos') === '1' ? 'selected' : '' }}>Con docs. vencidos</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('subcontratas.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Subcontratas -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Subcontrata</th>
                            <th>CIF</th>
                            <th>Contacto</th>
                            <th class="text-center">Trabajadores</th>
                            <th class="text-center">Obras</th>
                            <th class="text-center">Docs. CAE</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subcontratas as $subcontrata)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $subcontrata->nombre }}</h6>
                                        @if($subcontrata->razon_social && $subcontrata->razon_social !== $subcontrata->nombre)
                                            <small class="text-muted">{{ $subcontrata->razon_social }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $subcontrata->cif ?? '-' }}</td>
                            <td>
                                @if($subcontrata->persona_contacto)
                                    <small><i class="bi bi-person me-1"></i>{{ $subcontrata->persona_contacto }}</small><br>
                                @endif
                                @if($subcontrata->telefono)
                                    <small><i class="bi bi-telephone me-1"></i>{{ $subcontrata->telefono }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($subcontrata->trabajadores_count > 0)
                                    <span class="badge bg-primary">{{ $subcontrata->trabajadores_count }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($subcontrata->obras_count > 0)
                                    <span class="badge bg-info">{{ $subcontrata->obras_count }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $docsVencidos = $subcontrata->documentos_cae_vencidos;
                                    $docsProximos = $subcontrata->documentos_cae_proximos;
                                @endphp
                                @if($docsVencidos > 0)
                                    <span class="badge bg-danger" title="{{ $docsVencidos }} vencidos">
                                        {{ $subcontrata->documentos_cae_count }}
                                        <i class="bi bi-exclamation-circle ms-1"></i>
                                    </span>
                                @elseif($docsProximos > 0)
                                    <span class="badge bg-warning text-dark" title="{{ $docsProximos }} próximos a vencer">
                                        {{ $subcontrata->documentos_cae_count }}
                                        <i class="bi bi-clock ms-1"></i>
                                    </span>
                                @else
                                    <span class="badge bg-secondary">{{ $subcontrata->documentos_cae_count }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if($subcontrata->activa)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bi bi-check-circle me-1"></i>Activa
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">
                                            <i class="bi bi-x-circle me-1"></i>Inactiva
                                        </span>
                                    @endif
                                    @if($subcontrata->homologada)
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="bi bi-award me-1"></i>Homologada
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('subcontratas.show', $subcontrata) }}" class="btn btn-sm btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('editar_subcontratas')
                                    <a href="{{ route('subcontratas.edit', $subcontrata) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('eliminar_subcontratas')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteSubcontrata({{ $subcontrata->id }}, '{{ $subcontrata->nombre }}')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No hay subcontratas que mostrar
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Form Eliminar -->
<form id="deleteSubcontrataForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteSubcontrata(subcontrataId, subcontrataNombre) {
    Swal.fire({
        title: '¿Eliminar subcontrata?',
        text: `¿Estás seguro de eliminar "${subcontrataNombre}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteSubcontrataForm');
            form.action = `{{ url('subcontratas') }}/${subcontrataId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
