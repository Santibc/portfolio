@extends('layouts.app')

@section('title', 'Editar Cuadrilla')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('cuadrillas.index') }}" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-1">Editar Cuadrilla</h1>
                <p class="text-muted mb-0">{{ $cuadrilla->nombre }}</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('cuadrillas.update', $cuadrilla) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $cuadrilla->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Capataz</label>
                            <select name="capataz_id" class="form-select @error('capataz_id') is-invalid @enderror">
                                <option value="">Sin asignar</option>
                                @foreach($trabajadoresDisponibles as $trabajador)
                                    <option value="{{ $trabajador->id }}" {{ old('capataz_id', $cuadrilla->capataz_id) == $trabajador->id ? 'selected' : '' }}>
                                        {{ $trabajador->nombre_completo }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Trabajadores activos</small>
                            @error('capataz_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                      rows="3">{{ old('descripcion', $cuadrilla->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activa" id="activa" value="1"
                                       {{ old('activa', $cuadrilla->activa) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activa">Cuadrilla Activa</label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('cuadrillas.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
