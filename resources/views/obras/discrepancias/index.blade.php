@extends('layouts.app')

@section('title', 'Discrepancias - ' . $obra->codigo)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('obras.index') }}">Obras</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('obras.show', $obra) }}">{{ $obra->codigo }}</a></li>
                    <li class="breadcrumb-item active">Discrepancias</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">Discrepancias de Valoracion</h1>
            <p class="text-muted mb-0">{{ $obra->nombre }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('obras.discrepancias.create', $obra) }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Registrar Discrepancia
            </a>
            <a href="{{ route('obras.show', $obra) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver a Obra
            </a>
        </div>
    </div>

    <!-- Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ number_format($discrepancias->sum('importe_producido_manzer'), 0, ',', '.') }} €</h3>
                    <small class="text-muted">Total Producido</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">{{ number_format($discrepancias->sum('importe_aceptado_cliente'), 0, ',', '.') }} €</h3>
                    <small class="text-muted">Total Aceptado</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-danger">{{ number_format($discrepancias->sum('importe_pendiente'), 0, ',', '.') }} €</h3>
                    <small class="text-muted">Total Pendiente</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-info">{{ $discrepancias->total() }}</h3>
                    <small class="text-muted">Registros</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($discrepancias->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Periodo</th>
                                <th class="text-end">Producido Manzer</th>
                                <th class="text-end">Validado Cuadrilla</th>
                                <th class="text-end">Aceptado Cliente</th>
                                <th class="text-end">Pendiente</th>
                                <th>Estado</th>
                                <th>Fecha Respuesta</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discrepancias as $discrepancia)
                            <tr>
                                <td><strong>{{ $discrepancia->periodo_formateado }}</strong></td>
                                <td class="text-end">{{ number_format($discrepancia->importe_producido_manzer, 2, ',', '.') }} €</td>
                                <td class="text-end">{{ $discrepancia->importe_validado_cuadrilla ? number_format($discrepancia->importe_validado_cuadrilla, 2, ',', '.') . ' €' : '-' }}</td>
                                <td class="text-end">{{ $discrepancia->importe_aceptado_cliente ? number_format($discrepancia->importe_aceptado_cliente, 2, ',', '.') . ' €' : '-' }}</td>
                                <td class="text-end fw-bold text-{{ $discrepancia->importe_pendiente > 0 ? 'danger' : 'success' }}">
                                    {{ number_format($discrepancia->importe_pendiente, 2, ',', '.') }} €
                                </td>
                                <td>
                                    @php
                                        $estadoColors = ['pendiente' => 'warning', 'parcial' => 'info', 'resuelto' => 'success'];
                                    @endphp
                                    <span class="badge bg-{{ $estadoColors[$discrepancia->estado] }}-subtle text-{{ $estadoColors[$discrepancia->estado] }}">
                                        {{ ucfirst($discrepancia->estado) }}
                                    </span>
                                </td>
                                <td>{{ $discrepancia->fecha_respuesta_cliente?->format('d/m/Y') ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('obras.discrepancias.show', [$obra, $discrepancia]) }}" class="btn btn-outline-secondary" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('obras.discrepancias.edit', [$obra, $discrepancia]) }}" class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    {{ $discrepancias->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                    <p class="mb-0">No hay discrepancias registradas para esta obra</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
