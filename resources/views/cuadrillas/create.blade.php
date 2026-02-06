@extends('layouts.app')

@section('title', 'Nueva Cuadrilla')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('cuadrillas.index') }}" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-1">Nueva Cuadrilla</h1>
                <p class="text-muted mb-0">Crear un nuevo equipo de trabajo</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('cuadrillas.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}" required placeholder="Ej: Cuadrilla Norte">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Capataz</label>
                            <select name="capataz_id" class="form-select @error('capataz_id') is-invalid @enderror">
                                <option value="">Sin asignar</option>
                                @foreach($trabajadoresDisponibles as $trabajador)
                                    <option value="{{ $trabajador->id }}" {{ old('capataz_id') == $trabajador->id ? 'selected' : '' }}>
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
                                      rows="3" placeholder="Descripción opcional de la cuadrilla...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('cuadrillas.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Crear Cuadrilla</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
