@extends('layouts.app')

@section('title', 'Gestión de Gastos')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gastos</h1>
            <p class="text-muted mb-0">Registro y seguimiento de gastos</p>
        </div>
        <div>
            <a href="{{ route('gasto-categorias.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-tags me-2"></i>Categorías
            </a>
            <a href="{{ route('gastos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Gasto
            </a>
        </div>
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
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-arrow-up-circle text-danger fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['total'], 2, ',', '.') }} €</h3>
                            <small class="text-muted">Total Gastos</small>
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
                            <small class="text-muted">Pendiente de Pago</small>
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
                            <h3 class="mb-0">{{ number_format($stats['pagado'], 2, ',', '.') }} €</h3>
                            <small class="text-muted">Pagado</small>
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
            <form method="GET" action="{{ route('gastos.index') }}">
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
                        <label class="form-label">Categoría</label>
                        <select name="gasto_categoria_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ request('gasto_categoria_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="">Todos</option>
                            <option value="directo" {{ request('tipo') == 'directo' ? 'selected' : '' }}>Directo</option>
                            <option value="indirecto" {{ request('tipo') == 'indirecto' ? 'selected' : '' }}>Indirecto</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
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
                </div>
                <div class="row mt-3">
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i>Filtrar
                        </button>
                        <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-2"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de gastos --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Obra</th>
                        <th>Categoría</th>
                        <th>Concepto</th>
                        <th>Proveedor</th>
                        <th class="text-end">Importe</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gastos as $gasto)
                    <tr>
                        <td>{{ $gasto->fecha->format('d/m/Y') }}</td>
                        <td>
                            @if($gasto->obra)
                                <a href="{{ route('obras.show', $gasto->obra) }}" class="text-decoration-none">
                                    {{ $gasto->obra->codigo }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $gasto->categoria->tipo == 'directo' ? 'success' : 'info' }} bg-opacity-10 text-{{ $gasto->categoria->tipo == 'directo' ? 'success' : 'info' }}">
                                {{ $gasto->categoria->nombre }}
                            </span>
                        </td>
                        <td>
                            <span title="{{ $gasto->concepto }}">
                                {{ Str::limit($gasto->concepto, 40) }}
                            </span>
                        </td>
                        <td>{{ $gasto->proveedor ?? '-' }}</td>
                        <td class="text-end fw-semibold">
                            {{ number_format($gasto->importe_total, 2, ',', '.') }} €
                        </td>
                        <td class="text-center">
                            @if($gasto->estado == 'pagado')
                                <span class="badge bg-success">Pagado</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($gasto->documento_path)
                                <a href="{{ asset($gasto->documento_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver documento">
                                    <i class="bi bi-file-earmark"></i>
                                </a>
                            @endif
                            @if($gasto->estado == 'pendiente')
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="marcarPagado({{ $gasto->id }})" title="Marcar como pagado">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="marcarPendiente({{ $gasto->id }})" title="Marcar como pendiente">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            @endif
                            <a href="{{ route('gastos.show', $gasto) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('gastos.edit', $gasto) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarGasto({{ $gasto->id }}, '{{ addslashes($gasto->concepto) }}')" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                <p class="mb-0">No hay gastos registrados.</p>
                                <a href="{{ route('gastos.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-lg me-2"></i>Registrar primer gasto
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($gastos->hasPages())
        <div class="card-footer bg-white">
            {{ $gastos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function marcarPagado(id) {
        Swal.fire({
            title: 'Marcar como pagado',
            html: '<input type="date" id="fechaPago" class="form-control" value="{{ date('Y-m-d') }}">',
            showCancelButton: true,
            confirmButtonText: 'Marcar Pagado',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754',
        }).then(async (result) => {
            if (result.isConfirmed) {
                const fechaPago = document.getElementById('fechaPago').value;
                try {
                    const response = await fetch(`{{ url('gastos') }}/${id}/marcar-pagado`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ fecha_pago: fechaPago }),
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
            text: 'El gasto volverá al estado pendiente de pago.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, marcar pendiente',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('gastos') }}/${id}/marcar-pendiente`, {
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

    function eliminarGasto(id, concepto) {
        Swal.fire({
            title: '¿Eliminar gasto?',
            html: `Se eliminará el gasto: <strong>${concepto}</strong>.<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('gastos') }}/${id}`, {
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
