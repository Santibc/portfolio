@extends('layouts.app')

@section('title', 'Editar Fichaje')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Fichaje</h1>
            <p class="text-muted mb-0">
                {{ $fichaje->trabajador->nombre }} {{ $fichaje->trabajador->apellidos }} -
                {{ $fichaje->fecha->format('d/m/Y') }}
            </p>
        </div>
        <a href="{{ route('fichajes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('fichajes.update', $fichaje) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <!-- Trabajador (read-only) -->
                            <div class="col-md-6">
                                <label class="form-label">Trabajador</label>
                                <input type="text" class="form-control" readonly
                                       value="{{ $fichaje->trabajador->nombre }} {{ $fichaje->trabajador->apellidos }}">
                            </div>

                            <!-- Fecha (read-only) -->
                            <div class="col-md-6">
                                <label class="form-label">Fecha</label>
                                <input type="text" class="form-control" readonly
                                       value="{{ $fichaje->fecha->format('d/m/Y') }}">
                            </div>

                            <!-- Obra -->
                            <div class="col-md-12">
                                <label class="form-label">Obra</label>
                                <select name="obra_id" class="form-select @error('obra_id') is-invalid @enderror">
                                    <option value="">Sin asignar</option>
                                    @foreach($obras as $obra)
                                        <option value="{{ $obra->id }}" {{ old('obra_id', $fichaje->obra_id) == $obra->id ? 'selected' : '' }}>
                                            {{ $obra->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('obra_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Hora Entrada -->
                            <div class="col-md-6">
                                <label class="form-label">Hora Entrada</label>
                                <input type="time" name="hora_entrada" class="form-control @error('hora_entrada') is-invalid @enderror"
                                       value="{{ old('hora_entrada', $fichaje->hora_entrada ? \Carbon\Carbon::parse($fichaje->hora_entrada)->format('H:i') : '') }}">
                                @error('hora_entrada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($fichaje->latitud_entrada)
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt"></i>
                                        {{ $fichaje->latitud_entrada }}, {{ $fichaje->longitud_entrada }}
                                    </small>
                                @endif
                            </div>

                            <!-- Hora Salida -->
                            <div class="col-md-6">
                                <label class="form-label">Hora Salida</label>
                                <input type="time" name="hora_salida" class="form-control @error('hora_salida') is-invalid @enderror"
                                       value="{{ old('hora_salida', $fichaje->hora_salida ? \Carbon\Carbon::parse($fichaje->hora_salida)->format('H:i') : '') }}">
                                @error('hora_salida')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($fichaje->latitud_salida)
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt"></i>
                                        {{ $fichaje->latitud_salida }}, {{ $fichaje->longitud_salida }}
                                    </small>
                                @endif
                            </div>

                            <!-- Motivo corrección -->
                            <div class="col-12">
                                <label class="form-label">Motivo de corrección</label>
                                <textarea name="motivo_correccion" class="form-control @error('motivo_correccion') is-invalid @enderror"
                                          rows="2" placeholder="Explica el motivo de la corrección...">{{ old('motivo_correccion', $fichaje->motivo_correccion) }}</textarea>
                                @error('motivo_correccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Obligatorio si cambias las horas</small>
                            </div>

                            <!-- Notas -->
                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <textarea name="notas" class="form-control" rows="3"
                                          placeholder="Observaciones o comentarios...">{{ old('notas', $fichaje->notas) }}</textarea>
                            </div>

                            <div class="col-12">
                                <hr class="my-3">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('fichajes.index') }}" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Estado actual -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="card-title">Estado del Fichaje</h6>
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Horas trabajadas:</td>
                            <td><strong>{{ $fichaje->horas_trabajadas ? number_format($fichaje->horas_trabajadas, 1) . 'h' : '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Horas extra:</td>
                            <td><strong>{{ $fichaje->horas_extra > 0 ? number_format($fichaje->horas_extra, 1) . 'h' : '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estado:</td>
                            <td>
                                @if($fichaje->validado)
                                    <span class="badge bg-success">Validado</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                        @if($fichaje->validado)
                        <tr>
                            <td class="text-muted">Validado por:</td>
                            <td>{{ $fichaje->validadoPor->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fecha validación:</td>
                            <td>{{ $fichaje->fecha_validacion ? $fichaje->fecha_validacion->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($fichaje->corregido)
            <div class="card border-0 shadow-sm border-warning">
                <div class="card-body">
                    <h6 class="card-title text-warning">
                        <i class="bi bi-pencil me-2"></i>Corrección
                    </h6>
                    <p class="small mb-1">
                        <strong>Por:</strong> {{ $fichaje->corregidoPor->name ?? 'N/A' }}
                    </p>
                    <p class="small mb-0">
                        <strong>Motivo:</strong> {{ $fichaje->motivo_correccion ?: 'No especificado' }}
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
