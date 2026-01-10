@extends('layouts.app')

@section('title', 'Nuevo Fichaje')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nuevo Fichaje</h1>
            <p class="text-muted mb-0">Registrar entrada/salida manualmente</p>
        </div>
        <a href="{{ route('fichajes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('fichajes.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <!-- Trabajador -->
                            <div class="col-md-6">
                                <label class="form-label">Trabajador <span class="text-danger">*</span></label>
                                <select name="trabajador_id" class="form-select @error('trabajador_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar trabajador...</option>
                                    @foreach($trabajadores as $trabajador)
                                        <option value="{{ $trabajador->id }}" {{ old('trabajador_id') == $trabajador->id ? 'selected' : '' }}>
                                            {{ $trabajador->nombre }} {{ $trabajador->apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('trabajador_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Obra -->
                            <div class="col-md-6">
                                <label class="form-label">Obra</label>
                                <select name="obra_id" class="form-select @error('obra_id') is-invalid @enderror">
                                    <option value="">Sin asignar</option>
                                    @foreach($obras as $obra)
                                        <option value="{{ $obra->id }}" {{ old('obra_id') == $obra->id ? 'selected' : '' }}>
                                            {{ $obra->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('obra_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha -->
                            <div class="col-md-4">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                                       value="{{ old('fecha', date('Y-m-d')) }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Hora Entrada -->
                            <div class="col-md-4">
                                <label class="form-label">Hora Entrada</label>
                                <input type="time" name="hora_entrada" class="form-control @error('hora_entrada') is-invalid @enderror"
                                       value="{{ old('hora_entrada') }}">
                                @error('hora_entrada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Hora Salida -->
                            <div class="col-md-4">
                                <label class="form-label">Hora Salida</label>
                                <input type="time" name="hora_salida" class="form-control @error('hora_salida') is-invalid @enderror"
                                       value="{{ old('hora_salida') }}">
                                @error('hora_salida')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Notas -->
                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <textarea name="notas" class="form-control" rows="3"
                                          placeholder="Observaciones o comentarios...">{{ old('notas') }}</textarea>
                            </div>

                            <div class="col-12">
                                <hr class="my-3">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('fichajes.index') }}" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>Registrar Fichaje
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle me-2"></i>Información
                    </h6>
                    <p class="card-text small text-muted">
                        Este formulario es para registrar fichajes manualmente. Las horas se calculan automáticamente
                        cuando se indican tanto la hora de entrada como la de salida.
                    </p>
                    <hr>
                    <h6 class="text-muted small">Cálculo de horas:</h6>
                    <ul class="small text-muted mb-0">
                        <li>Las primeras 8 horas son horas normales</li>
                        <li>Las horas adicionales se marcan como extras</li>
                        <li>Solo puede haber un fichaje por trabajador/día</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
