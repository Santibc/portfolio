@extends('layouts.app')

@section('title', 'Detalle de Gasto')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Detalle de Gasto</h1>
            <p class="text-muted mb-0">{{ $gasto->concepto }}</p>
        </div>
        <div>
            @if($gasto->estado == 'pendiente')
                <span class="badge bg-warning text-dark fs-6 me-2">Pendiente</span>
            @else
                <span class="badge bg-success fs-6 me-2">Pagado</span>
            @endif
            <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
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

    <div class="row">
        {{-- Información principal --}}
        <div class="col-lg-8">
            {{-- Datos del gasto --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información del Gasto</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Categoría</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $gasto->categoria->tipo == 'directo' ? 'success' : 'info' }}">
                                    {{ $gasto->categoria->tipo == 'directo' ? 'Directo' : 'Indirecto' }}
                                </span>
                                {{ $gasto->categoria->nombre }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Obra</label>
                            <p class="mb-0">
                                @if($gasto->obra)
                                    <a href="{{ route('obras.show', $gasto->obra) }}" class="text-decoration-none">
                                        {{ $gasto->obra->codigo }} - {{ $gasto->obra->nombre }}
                                    </a>
                                @else
                                    <span class="text-muted">Gasto general (sin obra)</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Concepto</label>
                            <p class="mb-0 fw-semibold">{{ $gasto->concepto }}</p>
                        </div>
                        @if($gasto->descripcion)
                        <div class="col-12">
                            <label class="form-label text-muted small">Descripción</label>
                            <p class="mb-0">{{ $gasto->descripcion }}</p>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Proveedor</label>
                            <p class="mb-0">{{ $gasto->proveedor ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Fecha del Gasto</label>
                            <p class="mb-0">{{ $gasto->fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Importes --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-currency-euro me-2"></i>Desglose de Importes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">Base Imponible:</td>
                                    <td class="text-end">{{ number_format($gasto->importe, 2, ',', '.') }} €</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">IVA ({{ number_format($gasto->iva_porcentaje, 0) }}%):</td>
                                    <td class="text-end">{{ number_format($gasto->iva_importe, 2, ',', '.') }} €</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold fs-5">TOTAL:</td>
                                    <td class="text-end fw-bold fs-5 text-danger">{{ number_format($gasto->importe_total, 2, ',', '.') }} €</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Datos adicionales --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Datos Adicionales</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Estado</label>
                            <p class="mb-0">
                                @if($gasto->estado == 'pagado')
                                    <span class="badge bg-success fs-6">Pagado</span>
                                @else
                                    <span class="badge bg-warning text-dark fs-6">Pendiente</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha de Vencimiento</label>
                            <p class="mb-0">
                                @if($gasto->fecha_vencimiento)
                                    {{ $gasto->fecha_vencimiento->format('d/m/Y') }}
                                    @if($gasto->estado == 'pendiente' && $gasto->fecha_vencimiento->isPast())
                                        <span class="badge bg-danger ms-2">Vencido</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha de Pago</label>
                            <p class="mb-0">{{ $gasto->fecha_pago?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Forma de Pago</label>
                            <p class="mb-0">{{ $gasto->forma_pago ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Documento</label>
                            <p class="mb-0">
                                @if($gasto->documento_path)
                                    <a href="{{ asset($gasto->documento_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark me-2"></i>Ver documento
                                    </a>
                                @else
                                    <span class="text-muted">Sin documento adjunto</span>
                                @endif
                            </p>
                        </div>
                        @if($gasto->notas)
                        <div class="col-12">
                            <label class="form-label text-muted small">Notas</label>
                            <p class="mb-0">{{ $gasto->notas }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Acciones --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($gasto->estado == 'pendiente')
                            <button type="button" class="btn btn-success" onclick="marcarPagado({{ $gasto->id }}, '{{ optional($gasto->fecha_vencimiento)->format('Y-m-d') ?: optional($gasto->fecha)->format('Y-m-d') }}')">
                                <i class="bi bi-check-lg me-2"></i>Marcar como Pagado
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-warning" onclick="marcarPendiente({{ $gasto->id }})">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Marcar como Pendiente
                            </button>
                        @endif
                        <a href="{{ route('gastos.edit', $gasto) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Editar Gasto
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="eliminarGasto({{ $gasto->id }}, '{{ addslashes($gasto->concepto) }}')">
                            <i class="bi bi-trash me-2"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Información del registro --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Registro</h5>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Creado:</span>
                            <span>{{ $gasto->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Actualizado:</span>
                            <span>{{ $gasto->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info obra si existe --}}
            @if($gasto->obra)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Obra Asociada</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-1">{{ $gasto->obra->codigo }}</h6>
                    <p class="text-muted small mb-2">{{ $gasto->obra->nombre }}</p>
                    @if($gasto->obra->cliente)
                        <p class="small mb-2">
                            <i class="bi bi-person me-1"></i>{{ $gasto->obra->cliente->nombre_comercial }}
                        </p>
                    @endif
                    <a href="{{ route('obras.show', $gasto->obra) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Ver Obra
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function marcarPagado(id, fechaSugerida) {
        Swal.fire({
            title: 'Marcar como pagado',
            html: '<input type="date" id="fechaPago" class="form-control" value="' + (fechaSugerida || '{{ date('Y-m-d') }}') + '">',
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
                        }).then(() => window.location.href = '{{ route('gastos.index') }}');
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
