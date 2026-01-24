@extends('layouts.app')

@section('title', 'Detalle de Ingreso')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Detalle de Ingreso</h1>
            <p class="text-muted mb-0">{{ $ingreso->concepto }}</p>
        </div>
        <div>
            @if($ingreso->estado == 'pendiente')
                <span class="badge bg-warning text-dark fs-6 me-2">Pendiente</span>
            @else
                <span class="badge bg-success fs-6 me-2">Cobrado</span>
            @endif
            <a href="{{ route('ingresos.index') }}" class="btn btn-outline-secondary">
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
            {{-- Datos del ingreso --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información del Ingreso</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Obra</label>
                            <p class="mb-0">
                                @if($ingreso->obra)
                                    <a href="{{ route('obras.show', $ingreso->obra) }}" class="text-decoration-none">
                                        {{ $ingreso->obra->codigo }} - {{ $ingreso->obra->nombre }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Cliente</label>
                            <p class="mb-0">
                                @if($ingreso->cliente)
                                    <a href="{{ route('clientes.show', $ingreso->cliente) }}" class="text-decoration-none">
                                        {{ $ingreso->cliente->nombre_comercial }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Concepto</label>
                            <p class="mb-0 fw-semibold">{{ $ingreso->concepto }}</p>
                        </div>
                        @if($ingreso->descripcion)
                        <div class="col-12">
                            <label class="form-label text-muted small">Descripción</label>
                            <p class="mb-0">{{ $ingreso->descripcion }}</p>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Fecha del Ingreso</label>
                            <p class="mb-0">{{ $ingreso->fecha->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Fecha Prevista de Cobro</label>
                            <p class="mb-0">
                                @if($ingreso->fecha_prevista_cobro)
                                    {{ $ingreso->fecha_prevista_cobro->format('d/m/Y') }}
                                    @if($ingreso->estado == 'pendiente' && $ingreso->fecha_prevista_cobro->isPast())
                                        <span class="badge bg-danger ms-2">Vencido</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
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
                                    <td class="text-end">{{ number_format($ingreso->importe, 2, ',', '.') }} €</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">IVA ({{ number_format($ingreso->iva_porcentaje, 0) }}%):</td>
                                    <td class="text-end text-success">+ {{ number_format($ingreso->iva_importe, 2, ',', '.') }} €</td>
                                </tr>
                                @if($ingreso->retencion_porcentaje > 0)
                                <tr>
                                    <td class="text-muted">Retención ({{ number_format($ingreso->retencion_porcentaje, 0) }}%):</td>
                                    <td class="text-end text-danger">- {{ number_format($ingreso->retencion_importe, 2, ',', '.') }} €</td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="fw-bold fs-5">TOTAL A COBRAR:</td>
                                    <td class="text-end fw-bold fs-5 text-success">{{ number_format($ingreso->importe_total, 2, ',', '.') }} €</td>
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
                                @if($ingreso->estado == 'cobrado')
                                    <span class="badge bg-success fs-6">Cobrado</span>
                                @else
                                    <span class="badge bg-warning text-dark fs-6">Pendiente</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Forma de Pago</label>
                            <p class="mb-0">{{ $ingreso->forma_pago ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha de Cobro</label>
                            <p class="mb-0">{{ $ingreso->fecha_cobro?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        @if($ingreso->notas)
                        <div class="col-12">
                            <label class="form-label text-muted small">Notas</label>
                            <p class="mb-0">{{ $ingreso->notas }}</p>
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
                        @if($ingreso->estado == 'pendiente')
                            <button type="button" class="btn btn-success" onclick="marcarCobrado({{ $ingreso->id }})">
                                <i class="bi bi-check-lg me-2"></i>Marcar como Cobrado
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-warning" onclick="marcarPendiente({{ $ingreso->id }})">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Marcar como Pendiente
                            </button>
                        @endif
                        <a href="{{ route('ingresos.edit', $ingreso) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Editar Ingreso
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="eliminarIngreso({{ $ingreso->id }}, '{{ addslashes($ingreso->concepto) }}')">
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
                            <span>{{ $ingreso->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Actualizado:</span>
                            <span>{{ $ingreso->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info obra si existe --}}
            @if($ingreso->obra)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Obra Asociada</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-1">{{ $ingreso->obra->codigo }}</h6>
                    <p class="text-muted small mb-2">{{ $ingreso->obra->nombre }}</p>
                    @if($ingreso->obra->cliente)
                        <p class="small mb-2">
                            <i class="bi bi-person me-1"></i>{{ $ingreso->obra->cliente->nombre_comercial }}
                        </p>
                    @endif
                    <a href="{{ route('obras.show', $ingreso->obra) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Ver Obra
                    </a>
                </div>
            </div>
            @endif

            {{-- Info cliente si existe --}}
            @if($ingreso->cliente)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Cliente</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-1">{{ $ingreso->cliente->nombre_comercial }}</h6>
                    @if($ingreso->cliente->razon_social && $ingreso->cliente->razon_social != $ingreso->cliente->nombre_comercial)
                        <p class="text-muted small mb-2">{{ $ingreso->cliente->razon_social }}</p>
                    @endif
                    @if($ingreso->cliente->cif)
                        <p class="small mb-2">
                            <i class="bi bi-card-text me-1"></i>{{ $ingreso->cliente->cif }}
                        </p>
                    @endif
                    <a href="{{ route('clientes.show', $ingreso->cliente) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Ver Cliente
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
                        }).then(() => window.location.href = '{{ route('ingresos.index') }}');
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
