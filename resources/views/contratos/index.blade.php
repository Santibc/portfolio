@extends('layouts.app')

@section('title', 'Gestión de Contratos')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Contratos</h1>
            <p class="text-muted mb-0">Gestión de contratos y garantías</p>
        </div>
        <div>
            <a href="{{ route('contratos.export.excel', request()->query()) }}" class="btn btn-outline-success me-2">
                <i class="bi bi-file-earmark-excel me-2"></i>Exportar Excel
            </a>
            @role('Administrador')
            <a href="{{ route('contrato-tipos.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-collection me-2"></i>Tipos
            </a>
            @endrole
            @role('Administrador|Contabilidad')
            <a href="{{ route('contratos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Contrato
            </a>
            @endrole
        </div>
    </div>

    {{-- Alertas de sesión --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total Contratos</small>
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
                            <h3 class="mb-0">{{ $stats['activos'] }}</h3>
                            <small class="text-muted">Activos</small>
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
                            <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['proximos_vencer'] }}</h3>
                            <small class="text-muted">Por Vencer (30 días)</small>
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
                            <i class="bi bi-lock text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['importe_retenido'], 2, ',', '.') }} €</h3>
                            <small class="text-muted">Garantías Retenidas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('contratos.index') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Código, título...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            @foreach(\App\Models\Contrato::ESTADOS as $key => $label)
                                <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select name="contrato_tipo_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id }}" {{ request('contrato_tipo_id') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
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
                        <label class="form-label">Subcontrata</label>
                        <select name="subcontrata_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($subcontratas as $sub)
                                <option value="{{ $sub->id }}" {{ request('subcontrata_id') == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Con Retención</label>
                        <select name="tiene_retencion" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" {{ request('tiene_retencion') === '1' ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ request('tiene_retencion') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i>Filtrar
                        </button>
                        <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-2"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de contratos --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Cliente/Subcontrata</th>
                        <th>Vigencia</th>
                        <th class="text-end">Importe</th>
                        <th class="text-center">Garantía</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contratos as $contrato)
                    <tr>
                        <td>
                            <a href="{{ route('contratos.show', $contrato) }}" class="text-decoration-none fw-semibold">
                                {{ $contrato->codigo }}
                            </a>
                        </td>
                        <td>
                            <span title="{{ $contrato->titulo }}">
                                {{ Str::limit($contrato->titulo, 35) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                {{ $contrato->tipo->nombre ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted d-block">{{ $contrato->parte_tipo }}</small>
                            {{ Str::limit($contrato->parte_nombre, 25) }}
                        </td>
                        <td>
                            @if($contrato->fecha_inicio && $contrato->fecha_fin)
                                <span class="d-block">{{ $contrato->fecha_inicio->format('d/m/Y') }}</span>
                                <small class="text-muted">{{ $contrato->fecha_fin->format('d/m/Y') }}</small>
                                @if($contrato->proximo_a_vencer)
                                    <br><small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Vence en {{ $contrato->dias_para_vencer }} días</small>
                                @elseif($contrato->vencido && $contrato->estado == 'activo')
                                    <br><small class="text-danger"><i class="bi bi-x-circle"></i> Vencido</small>
                                @endif
                            @elseif($contrato->fecha_inicio)
                                {{ $contrato->fecha_inicio->format('d/m/Y') }}
                                <small class="text-muted d-block">Sin fecha fin</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <span class="fw-semibold">{{ number_format($contrato->importe_total, 2, ',', '.') }} €</span>
                            @if($contrato->importe)
                                <small class="text-muted d-block">Base: {{ number_format($contrato->importe, 2, ',', '.') }} €</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($contrato->tiene_retencion)
                                <span class="badge bg-{{ $contrato->garantia_badge }}"
                                      title="{{ \App\Models\Contrato::ESTADOS_GARANTIA[$contrato->estado_garantia] ?? 'N/A' }}">
                                    @if($contrato->estado_garantia === \App\Models\Contrato::ESTADO_GARANTIA_LIBERADA)
                                        <i class="bi bi-unlock"></i> Liberada
                                    @elseif($contrato->estado_garantia === \App\Models\Contrato::ESTADO_GARANTIA_PARCIALMENTE_LIBERADA)
                                        <i class="bi bi-unlock"></i> Parcial
                                    @else
                                        <i class="bi bi-lock"></i> {{ number_format($contrato->importe_retenido, 2, ',', '.') }} €
                                    @endif
                                </span>
                                @if($contrato->estado_garantia === \App\Models\Contrato::ESTADO_GARANTIA_PARCIALMENTE_LIBERADA)
                                    <br><small class="text-muted">{{ $contrato->porcentaje_total_liberado }}% liberado</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $contrato->estado_badge }}">
                                {{ \App\Models\Contrato::ESTADOS[$contrato->estado] ?? $contrato->estado }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($contrato->documento_path)
                                <a href="{{ asset($contrato->documento_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver documento">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            @endif
                            <a href="{{ route('contratos.show', $contrato) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            @role('Administrador|Contabilidad')
                            <a href="{{ route('contratos.edit', $contrato) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endrole
                            @role('Administrador')
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarContrato({{ $contrato->id }}, '{{ addslashes($contrato->titulo) }}')" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endrole
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-file-earmark-text fs-1 d-block mb-3"></i>
                                <p class="mb-0">No hay contratos registrados.</p>
                                @role('Administrador|Contabilidad')
                                <a href="{{ route('contratos.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-lg me-2"></i>Crear primer contrato
                                </a>
                                @endrole
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contratos->hasPages())
        <div class="card-footer bg-white">
            {{ $contratos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function eliminarContrato(id, titulo) {
        Swal.fire({
            title: '¿Eliminar contrato?',
            html: `Se eliminará: <strong>${titulo}</strong>.<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('contratos') }}/${id}`, {
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
