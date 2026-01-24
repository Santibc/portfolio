@extends('layouts.app')

@section('title', $caducidadGeneral->nombre)

@section('content')
<div class="container-fluid py-4">
    @php
        $hoy = now();
        $caducada = $caducidadGeneral->fecha_caducidad <= $hoy;
        $proxima = !$caducada && $caducidadGeneral->fecha_caducidad <= $hoy->copy()->addDays(30);
        $diasRestantes = $caducidadGeneral->fecha_caducidad->diffInDays($hoy, false);
    @endphp

    <!-- Alertas de estado -->
    @if($caducada)
    <div class="alert alert-danger d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div>
            <strong>Documento Caducado</strong> - Este documento caducó el {{ $caducidadGeneral->fecha_caducidad->format('d/m/Y') }}
            (hace {{ abs($diasRestantes) }} días).
        </div>
    </div>
    @elseif($proxima)
    <div class="alert alert-warning d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
        <div>
            <strong>Próximo a Caducar</strong> - Este documento caduca el {{ $caducidadGeneral->fecha_caducidad->format('d/m/Y') }}
            (en {{ $diasRestantes }} días).
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-secondary-subtle text-secondary">
                    {{ $tipos[$caducidadGeneral->tipo] ?? ucfirst(str_replace('_', ' ', $caducidadGeneral->tipo)) }}
                </span>
                @if($caducada)
                    <span class="badge bg-danger">Caducada</span>
                @elseif($proxima)
                    <span class="badge bg-warning">Próxima</span>
                @else
                    <span class="badge bg-success">Vigente</span>
                @endif
            </div>
            <h1 class="h3 mb-1">{{ $caducidadGeneral->nombre }}</h1>
            @if($caducidadGeneral->descripcion)
                <p class="text-muted mb-0">{{ $caducidadGeneral->descripcion }}</p>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('caducidades-generales.edit', $caducidadGeneral) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            <a href="{{ route('caducidades-generales.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <!-- Información General -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información General</h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%">Tipo</td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ $tipos[$caducidadGeneral->tipo] ?? ucfirst(str_replace('_', ' ', $caducidadGeneral->tipo)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nombre</td>
                                    <td class="fw-semibold">{{ $caducidadGeneral->nombre }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Alertas</td>
                                    <td>
                                        @if($caducidadGeneral->alerta_activa)
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="bi bi-bell me-1"></i>Activas
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                <i class="bi bi-bell-slash me-1"></i>Inactivas
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%">Fecha Emisión</td>
                                    <td>{{ $caducidadGeneral->fecha_emision?->format('d/m/Y') ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Fecha Caducidad</td>
                                    <td class="{{ $caducada ? 'text-danger fw-semibold' : ($proxima ? 'text-warning fw-semibold' : '') }}">
                                        {{ $caducidadGeneral->fecha_caducidad->format('d/m/Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Días Restantes</td>
                                    <td>
                                        @if($caducada)
                                            <span class="text-danger fw-semibold">Vencida hace {{ abs($diasRestantes) }} días</span>
                                        @else
                                            <span class="{{ $proxima ? 'text-warning' : 'text-success' }}">{{ $diasRestantes }} días</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($caducidadGeneral->descripcion)
                    <hr>
                    <div>
                        <h6 class="text-muted mb-2">Descripción</h6>
                        <p class="mb-0">{{ $caducidadGeneral->descripcion }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Documento -->
            @if($caducidadGeneral->documento_path)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-file-earmark-pdf me-2"></i>Documento Adjunto</h6>
                    <div class="d-flex gap-2">
                        <a href="{{ asset($caducidadGeneral->documento_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye me-1"></i>Ver
                        </a>
                        <a href="{{ asset($caducidadGeneral->documento_path) }}" download class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i>Descargar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ basename($caducidadGeneral->documento_path) }}</h6>
                            <small class="text-muted">Documento adjunto</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Columna Lateral -->
        <div class="col-lg-4">
            <!-- Estado Visual -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    @if($caducada)
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="text-danger mb-1">Caducada</h4>
                        <p class="text-muted mb-0">Este documento requiere renovación urgente</p>
                    @elseif($proxima)
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="text-warning mb-1">Próxima a Caducar</h4>
                        <p class="text-muted mb-0">Quedan {{ $diasRestantes }} días para la renovación</p>
                    @else
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="text-success mb-1">Vigente</h4>
                        <p class="text-muted mb-0">{{ $diasRestantes }} días hasta la caducidad</p>
                    @endif
                </div>
            </div>

            <!-- Información del Sistema -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Información del Sistema</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">ID</td>
                            <td><code>{{ $caducidadGeneral->id }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Creado</td>
                            <td>{{ $caducidadGeneral->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Modificado</td>
                            <td>{{ $caducidadGeneral->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
