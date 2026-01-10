@extends('layouts.app')

@section('title', 'Resumen de Fichajes')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Resumen de Fichajes</h1>
            <p class="text-muted mb-0">Vista consolidada por trabajador</p>
        </div>
        <a href="{{ route('fichajes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <!-- Filtro de período -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('fichajes.resumen') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Mes</label>
                    <select name="mes" class="form-select">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Año</label>
                    <select name="anio" class="form-select">
                        @for($i = now()->year; $i >= now()->year - 2; $i--)
                            <option value="{{ $i }}" {{ $anio == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de resumen -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-calendar3 me-2"></i>
                {{ \Carbon\Carbon::create($anio, $mes)->translatedFormat('F Y') }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Trabajador</th>
                            <th class="text-center">Días Trabajados</th>
                            <th class="text-center">Horas Trabajadas</th>
                            <th class="text-center">Horas Extra</th>
                            <th class="text-center">Total Horas</th>
                            <th class="text-center">Pendientes</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDias = 0;
                            $totalHoras = 0;
                            $totalExtra = 0;
                            $totalPendientes = 0;
                        @endphp
                        @forelse($trabajadores as $trabajador)
                            @php
                                $totalDias += $trabajador['dias_trabajados'];
                                $totalHoras += $trabajador['horas_trabajadas'];
                                $totalExtra += $trabajador['horas_extra'];
                                $totalPendientes += $trabajador['fichajes_pendientes'];
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('trabajadores.show', $trabajador['id']) }}" class="text-decoration-none">
                                        {{ $trabajador['nombre'] }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $trabajador['dias_trabajados'] }} días
                                    </span>
                                </td>
                                <td class="text-center">
                                    <strong>{{ number_format($trabajador['horas_trabajadas'], 1) }}h</strong>
                                </td>
                                <td class="text-center">
                                    @if($trabajador['horas_extra'] > 0)
                                        <span class="badge bg-info">+{{ number_format($trabajador['horas_extra'], 1) }}h</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <strong class="text-success">
                                        {{ number_format($trabajador['horas_trabajadas'] + $trabajador['horas_extra'], 1) }}h
                                    </strong>
                                </td>
                                <td class="text-center">
                                    @if($trabajador['fichajes_pendientes'] > 0)
                                        <span class="badge bg-warning text-dark">
                                            {{ $trabajador['fichajes_pendientes'] }}
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="bi bi-check"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('fichajes.index', ['trabajador_id' => $trabajador['id'], 'fecha_desde' => \Carbon\Carbon::create($anio, $mes, 1)->format('Y-m-d'), 'fecha_hasta' => \Carbon\Carbon::create($anio, $mes, 1)->endOfMonth()->format('Y-m-d')]) }}"
                                       class="btn btn-sm btn-outline-primary" title="Ver fichajes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No hay datos para este período
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($trabajadores) > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th>TOTALES</th>
                            <th class="text-center">{{ $totalDias }} días</th>
                            <th class="text-center">{{ number_format($totalHoras, 1) }}h</th>
                            <th class="text-center">{{ number_format($totalExtra, 1) }}h</th>
                            <th class="text-center text-success">{{ number_format($totalHoras + $totalExtra, 1) }}h</th>
                            <th class="text-center">
                                @if($totalPendientes > 0)
                                    <span class="badge bg-warning text-dark">{{ $totalPendientes }}</span>
                                @else
                                    <span class="badge bg-success"><i class="bi bi-check"></i></span>
                                @endif
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
