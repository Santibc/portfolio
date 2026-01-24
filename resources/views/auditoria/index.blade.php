@extends('layouts.app')

@section('title', 'Registro de Auditoría')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Registro de Auditoría</h1>
            <p class="text-muted mb-0">Historial de acciones realizadas en el sistema</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('auditoria.exportar', request()->query()) }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i>Exportar CSV
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-journal-text text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['total_hoy']) }}</h3>
                            <small class="text-muted">Registros Hoy</small>
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
                            <i class="bi bi-plus-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['creaciones_hoy']) }}</h3>
                            <small class="text-muted">Creaciones Hoy</small>
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
                            <i class="bi bi-pencil text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['ediciones_hoy']) }}</h3>
                            <small class="text-muted">Ediciones Hoy</small>
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
                            <i class="bi bi-trash text-danger fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['eliminaciones_hoy']) }}</h3>
                            <small class="text-muted">Eliminaciones Hoy</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('auditoria.index') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Usuario</label>
                    <select name="user_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ request('user_id') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tabla</label>
                    <select name="tabla" class="form-select">
                        <option value="">Todas</option>
                        @foreach($tablasUnicas as $tabla)
                            <option value="{{ $tabla }}" {{ request('tabla') == $tabla ? 'selected' : '' }}>
                                {{ $tablaLabels[$tabla] ?? ucfirst(str_replace('_', ' ', $tabla)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Acción</label>
                    <select name="accion" class="form-select">
                        <option value="">Todas</option>
                        @foreach($acciones as $accion)
                            <option value="{{ $accion }}" {{ request('accion') == $accion ? 'selected' : '' }}>
                                {{ ucfirst($accion) }}
                            </option>
                        @endforeach
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
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('auditoria.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Auditoría -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Fecha/Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Módulo</th>
                            <th>Registro</th>
                            <th>IP</th>
                            <th class="text-end pe-4">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditorias as $auditoria)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-medium">{{ $auditoria->created_at ? $auditoria->created_at->format('d/m/Y') : '-' }}</span>
                                <br>
                                <small class="text-muted">{{ $auditoria->created_at ? $auditoria->created_at->format('H:i:s') : '' }}</small>
                            </td>
                            <td>
                                @if($auditoria->user)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 32px; height: 32px; font-size: 14px;">
                                                {{ strtoupper(substr($auditoria->user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <span>{{ $auditoria->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">Sistema</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeColor = $accionColores[$auditoria->accion] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }}">
                                    @switch($auditoria->accion)
                                        @case('crear')
                                            <i class="bi bi-plus-circle me-1"></i>
                                            @break
                                        @case('editar')
                                            <i class="bi bi-pencil me-1"></i>
                                            @break
                                        @case('eliminar')
                                            <i class="bi bi-trash me-1"></i>
                                            @break
                                        @case('login')
                                            <i class="bi bi-box-arrow-in-right me-1"></i>
                                            @break
                                        @case('logout')
                                            <i class="bi bi-box-arrow-right me-1"></i>
                                            @break
                                        @default
                                            <i class="bi bi-activity me-1"></i>
                                    @endswitch
                                    {{ ucfirst($auditoria->accion) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $tablaLabels[$auditoria->tabla] ?? ucfirst(str_replace('_', ' ', $auditoria->tabla)) }}
                                </span>
                            </td>
                            <td>
                                @if($auditoria->registro_id)
                                    <code class="text-primary">#{{ $auditoria->registro_id }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($auditoria->ip_address)
                                    <small class="text-muted font-monospace">{{ $auditoria->ip_address }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('auditoria.show', $auditoria) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                                <p class="mb-0">No hay registros de auditoría</p>
                                <small>Los registros aparecerán aquí cuando se realicen acciones en el sistema</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($auditorias->hasPages())
        <div class="card-footer bg-transparent">
            {{ $auditorias->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
