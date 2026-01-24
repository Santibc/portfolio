@extends('layouts.app')

@section('title', 'Gestión de Ingresos')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Ingresos</h1>
            <p class="text-muted mb-0">Registro y seguimiento de ingresos</p>
        </div>
        <a href="{{ route('ingresos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Ingreso
        </a>
    </div>

    {{-- Alertas de sesión --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-arrow-down-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['total'], 2, ',', '.') }} €</h3>
                            <small class="text-muted">Total Ingresos</small>
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
                            <i class="bi bi-clock text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['pendiente'], 2, ',', '.') }} €</h3>
                            <small class="text-muted">Pendiente de Cobro</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-check-circle text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['cobrado'], 2, ',', '.') }} €</h3>
                            <small class="text-muted">Cobrado</small>
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
                            <i class="bi bi-calendar-month text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['este_mes'], 2, ',', '.') }} €</h3>
                            <small class="text-muted">Este Mes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ingresos.index') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Obra</label>
                        <select name="obra_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($obras as $obra)
                                <option value="{{ $obra->id }}" {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                                    {{ $obra->codigo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Cliente</label>
                        <select name="cliente_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre_comercial }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="cobrado" {{ request('estado') == 'cobrado' ? 'selected' : '' }}>Cobrado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('ingresos.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de ingresos --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Obra</th>
                        <th>Cliente</th>
                        <th>Concepto</th>
                        <th class="text-end">Base</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ingresos as $ingreso)
                    <tr>
                        <td>{{ $ingreso->fecha->format('d/m/Y') }}</td>
                        <td>
                            @if($ingreso->obra)
                                <a href="{{ route('obras.show', $ingreso->obra) }}" class="text-decoration-none">
                                    {{ $ingreso->obra->codigo }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $ingreso->cliente?->nombre_comercial ?? '-' }}</td>
                        <td>
                            <span title="{{ $ingreso->concepto }}">
                                {{ Str::limit($ingreso->concepto, 40) }}
                            </span>
                        </td>
                        <td class="text-end">
                            {{ number_format($ingreso->importe, 2, ',', '.') }} €
                        </td>
                        <td class="text-end fw-semibold">
                            {{ number_format($ingreso->importe_total, 2, ',', '.') }} €
                        </td>
                        <td class="text-center">
                            @if($ingreso->estado == 'cobrado')
                                <span class="badge bg-success">Cobrado</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($ingreso->estado == 'pendiente')
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="marcarCobrado({{ $ingreso->id }})" title="Marcar como cobrado">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="marcarPendiente({{ $ingreso->id }})" title="Marcar como pendiente">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            @endif
                            <a href="{{ route('ingresos.show', $ingreso) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('ingresos.edit', $ingreso) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarIngreso({{ $ingreso->id }}, '{{ addslashes($ingreso->concepto) }}')" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                <p class="mb-0">No hay ingresos registrados.</p>
                                <a href="{{ route('ingresos.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-lg me-2"></i>Registrar primer ingreso
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ingresos->hasPages())
        <div class="card-footer bg-white">
            {{ $ingresos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function marcarCobrado(id) {
        Swal.fire({
            title: 'Marcar como cobrado',
            html: '<input type="date" id="fechaCobro" class="form-control" value="{{ date('Y-m-d') }}">',
            showCancelButton: true,
            confirmButtonText: 'Marcar Cobrado',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754',
        }).then(async (result) => {
            if (result.isConfirmed) {
                const fechaCobro = document.getElementById('fechaCobro').value;
                try {
                    const response = await fetch(`{{ url('ingresos') }}/${id}/marcar-cobrado`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ fecha_cobro: fechaCobro }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            }
        });
    }

    function marcarPendiente(id) {
        Swal.fire({
            title: '¿Marcar como pendiente?',
            text: 'El ingreso volverá al estado pendiente de cobro.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, marcar pendiente',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('ingresos') }}/${id}/marcar-pendiente`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            }
        });
    }

    function eliminarIngreso(id, concepto) {
        Swal.fire({
            title: '¿Eliminar ingreso?',
            html: `Se eliminará el ingreso: <strong>${concepto}</strong>.<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('ingresos') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            }
        });
    }
</script>
@endpush
