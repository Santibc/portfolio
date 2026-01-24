@extends('layouts.app')

@section('title', 'Caducidades de Empresa')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Caducidades de Empresa</h1>
            <p class="text-muted mb-0">Gestiona certificaciones, seguros y documentos de la empresa</p>
        </div>
        <a href="{{ route('caducidades-generales.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nueva Caducidad
        </a>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-calendar-x text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total</small>
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
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['vigentes'] }}</h3>
                            <small class="text-muted">Vigentes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-exclamation-circle text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['proximas'] }}</h3>
                            <small class="text-muted">Próximas (30d)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-x-circle text-danger fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['caducadas'] }}</h3>
                            <small class="text-muted">Caducadas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('caducidades-generales.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nombre, descripción..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $key => $label)
                            <option value="{{ $key }}" {{ request('tipo') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="vigente" {{ request('estado') == 'vigente' ? 'selected' : '' }}>Vigentes</option>
                        <option value="proxima" {{ request('estado') == 'proxima' ? 'selected' : '' }}>Próximas</option>
                        <option value="caducada" {{ request('estado') == 'caducada' ? 'selected' : '' }}>Caducadas</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('caducidades-generales.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nombre</th>
                            <th>Tipo</th>
                            <th>Fecha Emisión</th>
                            <th>Fecha Caducidad</th>
                            <th>Estado</th>
                            <th>Documento</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($caducidades as $caducidad)
                        @php
                            $hoy = now();
                            $caducada = $caducidad->fecha_caducidad <= $hoy;
                            $proxima = !$caducada && $caducidad->fecha_caducidad <= $hoy->copy()->addDays(30);
                            $vigente = !$caducada && !$proxima;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div>
                                    <h6 class="mb-0">{{ $caducidad->nombre }}</h6>
                                    @if($caducidad->descripcion)
                                        <small class="text-muted">{{ Str::limit($caducidad->descripcion, 50) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $tipos[$caducidad->tipo] ?? ucfirst(str_replace('_', ' ', $caducidad->tipo)) }}
                                </span>
                            </td>
                            <td>{{ $caducidad->fecha_emision?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                @if($caducada)
                                    <span class="text-danger fw-semibold">
                                        {{ $caducidad->fecha_caducidad->format('d/m/Y') }}
                                    </span>
                                @elseif($proxima)
                                    <span class="text-warning fw-semibold">
                                        {{ $caducidad->fecha_caducidad->format('d/m/Y') }}
                                    </span>
                                @else
                                    {{ $caducidad->fecha_caducidad->format('d/m/Y') }}
                                @endif
                            </td>
                            <td>
                                @if($caducada)
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="bi bi-x-circle me-1"></i>Caducada
                                    </span>
                                @elseif($proxima)
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="bi bi-exclamation-circle me-1"></i>Próxima
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>Vigente
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($caducidad->documento_path)
                                    <a href="{{ asset($caducidad->documento_path) }}" target="_blank"
                                       class="btn btn-sm btn-outline-info" title="Ver documento">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('caducidades-generales.show', $caducidad) }}"
                                       class="btn btn-sm btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('caducidades-generales.edit', $caducidad) }}"
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar"
                                            data-id="{{ $caducidad->id }}"
                                            data-nombre="{{ $caducidad->nombre }}" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                <p class="mb-0">No hay caducidades registradas</p>
                                <a href="{{ route('caducidades-generales.create') }}" class="btn btn-primary btn-sm mt-3">
                                    <i class="bi bi-plus-lg me-1"></i>Crear primera caducidad
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($caducidades->hasPages())
        <div class="card-footer bg-transparent">
            {{ $caducidades->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Form Eliminar -->
<form id="deleteForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;

            Swal.fire({
                title: '¿Eliminar caducidad?',
                text: `¿Estás seguro de eliminar "${nombre}"? Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteForm');
                    form.action = `{{ url('caducidades-generales') }}/${id}`;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
@endsection
