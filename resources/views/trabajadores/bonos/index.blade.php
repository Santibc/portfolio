@extends('layouts.app')

@section('title', 'Bonos y Primas')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Bonos y Primas</h1>
            <p class="text-muted mb-0">Gestion manual de bonos y primas de trabajadores</p>
        </div>
        <a href="{{ route('trabajadores.bonos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Bono
        </a>
    </div>

    <!-- Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 text-warning">{{ number_format($totalPendiente, 2, ',', '.') }} €</h3>
                            <small class="text-muted">Pendiente de Pago</small>
                        </div>
                        <i class="bi bi-clock-history fs-1 text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 text-success">{{ number_format($totalPagado, 2, ',', '.') }} €</h3>
                            <small class="text-muted">Total Pagado</small>
                        </div>
                        <i class="bi bi-check-circle fs-1 text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 text-primary">{{ $bonos->total() }}</h3>
                            <small class="text-muted">Total Registros</small>
                        </div>
                        <i class="bi bi-list-ul fs-1 text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('trabajadores.bonos.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Trabajador</label>
                        <select name="trabajador_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($trabajadores as $trabajador)
                                <option value="{{ $trabajador->id }}" {{ request('trabajador_id') == $trabajador->id ? 'selected' : '' }}>
                                    {{ $trabajador->nombre }} {{ $trabajador->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="">Todos</option>
                            <option value="prima_produccion" {{ request('tipo') == 'prima_produccion' ? 'selected' : '' }}>Prima Produccion</option>
                            <option value="bono_especial" {{ request('tipo') == 'bono_especial' ? 'selected' : '' }}>Bono Especial</option>
                            <option value="plus_nocturnidad" {{ request('tipo') == 'plus_nocturnidad' ? 'selected' : '' }}>Plus Nocturnidad</option>
                            <option value="otro" {{ request('tipo') == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="pagado" class="form-select">
                            <option value="">Todos</option>
                            <option value="no" {{ request('pagado') == 'no' ? 'selected' : '' }}>Pendiente</option>
                            <option value="si" {{ request('pagado') == 'si' ? 'selected' : '' }}>Pagado</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('trabajadores.bonos.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($bonos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Trabajador</th>
                                <th>Concepto</th>
                                <th>Tipo</th>
                                <th>Obra</th>
                                <th class="text-end">Importe</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bonos as $bono)
                            <tr>
                                <td>{{ $bono->fecha->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('trabajadores.show', $bono->trabajador) }}">
                                        {{ $bono->trabajador->nombre }} {{ $bono->trabajador->apellidos }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($bono->concepto, 40) }}</td>
                                <td>
                                    @php
                                        $tipoColors = [
                                            'prima_produccion' => 'success',
                                            'bono_especial' => 'info',
                                            'plus_nocturnidad' => 'primary',
                                            'otro' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $tipoColors[$bono->tipo] }}-subtle text-{{ $tipoColors[$bono->tipo] }}">
                                        {{ $bono->tipo_formateado }}
                                    </span>
                                </td>
                                <td>
                                    @if($bono->obra)
                                        <a href="{{ route('obras.show', $bono->obra) }}">{{ $bono->obra->codigo }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">{{ $bono->importe_formateado }}</td>
                                <td>
                                    @if($bono->pagado)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bi bi-check-circle me-1"></i>Pagado
                                        </span>
                                        @if($bono->fecha_pago)
                                            <br><small class="text-muted">{{ $bono->fecha_pago->format('d/m/Y') }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">
                                            <i class="bi bi-clock me-1"></i>Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        @if(!$bono->pagado)
                                        <button type="button" class="btn btn-outline-success btn-pagar" data-id="{{ $bono->id }}" title="Marcar pagado">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-outline-warning btn-pendiente" data-id="{{ $bono->id }}" title="Marcar pendiente">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        @endif
                                        <a href="{{ route('trabajadores.bonos.edit', $bono) }}" class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-eliminar" data-id="{{ $bono->id }}" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    {{ $bonos->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-gift fs-1 d-block mb-2"></i>
                    <p class="mb-0">No hay bonos registrados</p>
                    <a href="{{ route('trabajadores.bonos.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-lg me-2"></i>Registrar Primer Bono
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Marcar como pagado
    document.querySelectorAll('.btn-pagar').forEach(btn => {
        btn.addEventListener('click', function() {
            const bonoId = this.dataset.id;

            Swal.fire({
                title: 'Marcar como Pagado',
                html: '<label class="form-label">Fecha de pago</label><input type="date" id="fechaPago" class="form-control" value="' + new Date().toISOString().split('T')[0] + '">',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Confirmar Pago',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    return document.getElementById('fechaPago').value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/trabajadores/bonos/${bonoId}/pagar`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ fecha_pago: result.value })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Pagado', timer: 1500 }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            });
        });
    });

    // Marcar como pendiente
    document.querySelectorAll('.btn-pendiente').forEach(btn => {
        btn.addEventListener('click', function() {
            const bonoId = this.dataset.id;

            Swal.fire({
                title: 'Marcar como Pendiente',
                text: 'Se revertira el estado del bono a pendiente de pago.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                confirmButtonText: 'Si, marcar pendiente',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/trabajadores/bonos/${bonoId}/pendiente`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Actualizado', timer: 1500 }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            });
        });
    });

    // Eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const bonoId = this.dataset.id;

            Swal.fire({
                title: 'Eliminar Bono',
                text: 'Esta accion no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/trabajadores/bonos/${bonoId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1500 }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
