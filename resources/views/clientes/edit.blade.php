@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Cliente</h1>
            <p class="text-muted mb-0">{{ $cliente->nombre_comercial }}</p>
        </div>
        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('clientes.update', $cliente) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <!-- Tipo de cliente -->
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Cliente <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                            <option value="privado" {{ old('tipo', $cliente->tipo) == 'privado' ? 'selected' : '' }}>Privado</option>
                            <option value="publico" {{ old('tipo', $cliente->tipo) == 'publico' ? 'selected' : '' }}>Público</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">CIF</label>
                        <input type="text" name="cif" class="form-control @error('cif') is-invalid @enderror"
                               value="{{ old('cif', $cliente->cif) }}" placeholder="B12345678">
                        @error('cif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Datos de la empresa -->
                    <div class="col-12">
                        <hr class="my-2">
                        <small class="text-muted fw-semibold">DATOS DE LA EMPRESA</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nombre Comercial <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_comercial" class="form-control @error('nombre_comercial') is-invalid @enderror"
                               value="{{ old('nombre_comercial', $cliente->nombre_comercial) }}" required>
                        @error('nombre_comercial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Razón Social</label>
                        <input type="text" name="razon_social" class="form-control @error('razon_social') is-invalid @enderror"
                               value="{{ old('razon_social', $cliente->razon_social) }}">
                        @error('razon_social')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $cliente->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dirección -->
                    <div class="col-12">
                        <hr class="my-2">
                        <small class="text-muted fw-semibold">DIRECCIÓN</small>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $cliente->direccion) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Código Postal</label>
                        <input type="text" name="codigo_postal" class="form-control" value="{{ old('codigo_postal', $cliente->codigo_postal) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $cliente->ciudad) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Provincia</label>
                        <input type="text" name="provincia" class="form-control" value="{{ old('provincia', $cliente->provincia) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">País</label>
                        <input type="text" name="pais" class="form-control" value="{{ old('pais', $cliente->pais) }}">
                    </div>

                    <!-- Persona de contacto -->
                    <div class="col-12">
                        <hr class="my-2">
                        <small class="text-muted fw-semibold">PERSONA DE CONTACTO</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="persona_contacto" class="form-control"
                               value="{{ old('persona_contacto', $cliente->persona_contacto) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono_contacto" class="form-control"
                               value="{{ old('telefono_contacto', $cliente->telefono_contacto) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email_contacto" class="form-control @error('email_contacto') is-invalid @enderror"
                               value="{{ old('email_contacto', $cliente->email_contacto) }}">
                        @error('email_contacto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Condiciones comerciales -->
                    <div class="col-12">
                        <hr class="my-2">
                        <small class="text-muted fw-semibold">CONDICIONES COMERCIALES</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Condiciones de Pago</label>
                        <select name="condiciones_pago" class="form-select">
                            <option value="">Seleccionar...</option>
                            @foreach(['Contado', '15 días', '30 días', '45 días', '60 días', '90 días'] as $opcion)
                                <option value="{{ $opcion }}" {{ old('condiciones_pago', $cliente->condiciones_pago) == $opcion ? 'selected' : '' }}>
                                    {{ $opcion }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Retención (%)</label>
                        <div class="input-group">
                            <input type="number" name="retencion_porcentaje" class="form-control"
                                   step="0.01" min="0" max="100"
                                   value="{{ old('retencion_porcentaje', $cliente->retencion_porcentaje) }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Notas</label>
                        <textarea name="notas" class="form-control" rows="3">{{ old('notas', $cliente->notas) }}</textarea>
                    </div>

                    <!-- Estado -->
                    <div class="col-12">
                        <hr class="my-2">
                        <small class="text-muted fw-semibold">ESTADO</small>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1"
                                   {{ old('activo', $cliente->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Cliente Activo</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="my-3">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
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
@endsection
