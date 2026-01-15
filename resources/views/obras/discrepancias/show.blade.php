@extends('layouts.app')

@section('title', 'Discrepancia ' . $discrepancia->periodo_formateado . ' - ' . $obra->codigo)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('obras.index') }}">Obras</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('obras.show', $obra) }}">{{ $obra->codigo }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('obras.discrepancias.index', $obra) }}">Discrepancias</a></li>
                    <li class="breadcrumb-item active">{{ $discrepancia->periodo_formateado }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">Discrepancia: {{ $discrepancia->periodo_formateado }}</h1>
            <p class="text-muted mb-0">{{ $obra->nombre }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('obras.discrepancias.edit', [$obra, $discrepancia]) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            <a href="{{ route('obras.discrepancias.index', $obra) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Importes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Desglose de Importes</h5>
                    @php
                        $estadoColors = ['pendiente' => 'warning', 'parcial' => 'info', 'resuelto' => 'success'];
                    @endphp
                    <span class="badge bg-{{ $estadoColors[$discrepancia->estado] }} fs-6">{{ ucfirst($discrepancia->estado) }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="bg-primary bg-opacity-10 rounded p-3 text-center">
                                <h3 class="mb-1 text-primary">{{ number_format($discrepancia->importe_producido_manzer, 2, ',', '.') }} €</h3>
                                <small class="text-muted">Producido Manzer</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-info bg-opacity-10 rounded p-3 text-center">
                                <h3 class="mb-1 text-info">{{ $discrepancia->importe_validado_cuadrilla ? number_format($discrepancia->importe_validado_cuadrilla, 2, ',', '.') . ' €' : '-' }}</h3>
                                <small class="text-muted">Validado Cuadrilla</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-success bg-opacity-10 rounded p-3 text-center">
                                <h3 class="mb-1 text-success">{{ $discrepancia->importe_aceptado_cliente ? number_format($discrepancia->importe_aceptado_cliente, 2, ',', '.') . ' €' : '-' }}</h3>
                                <small class="text-muted">Aceptado Cliente</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-{{ $discrepancia->importe_pendiente > 0 ? 'danger' : 'success' }} bg-opacity-10 rounded p-3 text-center">
                                <h3 class="mb-1 text-{{ $discrepancia->importe_pendiente > 0 ? 'danger' : 'success' }}">{{ number_format($discrepancia->importe_pendiente, 2, ',', '.') }} €</h3>
                                <small class="text-muted">Pendiente</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notas -->
            @if($discrepancia->notas)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Notas</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $discrepancia->notas }}</p>
                </div>
            </div>
            @endif

            <!-- Documento -->
            @if($discrepancia->documento_valoracion_path)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Documento de Valoracion</h5>
                </div>
                <div class="card-body">
                    <a href="{{ asset('storage/' . $discrepancia->documento_valoracion_path) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark me-2"></i>Ver Documento
                    </a>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Informacion</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Obra:</td>
                            <td><a href="{{ route('obras.show', $obra) }}">{{ $obra->codigo }}</a></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Periodo:</td>
                            <td><strong>{{ $discrepancia->periodo_formateado }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estado:</td>
                            <td>
                                <span class="badge bg-{{ $estadoColors[$discrepancia->estado] }}">{{ ucfirst($discrepancia->estado) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Respuesta Cliente:</td>
                            <td>{{ $discrepancia->fecha_respuesta_cliente?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        @if($discrepancia->fecha_resolucion)
                        <tr>
                            <td class="text-muted">Fecha Resolucion:</td>
                            <td>{{ $discrepancia->fecha_resolucion->format('d/m/Y') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Registrado por:</td>
                            <td>{{ $discrepancia->registrador->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Creado:</td>
                            <td>{{ $discrepancia->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Actualizado:</td>
                            <td>{{ $discrepancia->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
