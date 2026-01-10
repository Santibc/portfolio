@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nuevo Cliente</h1>
            <p class="text-muted mb-0">Registra un nuevo cliente en el sistema</p>
        </div>
        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('clientes.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <!-- Tipo de cliente -->
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Cliente <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                            <option value="privado" {{ old('tipo') == 'privado' ? 'selected' : '' }}>Privado</option>
                            <option value="publico" {{ old('tipo') == 'publico' ? 'selected' : '' }}>Público</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">CIF</label>
                        <input type="text" name="cif" class="form-control @error('cif') is-invalid @enderror"
                               value="{{ old('cif') }}" placeholder="B12345678">
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
                               value="{{ old('nombre_comercial') }}" required>
                        @error('nombre_comercial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Razón Social</label>
                        <input type="text" name="razon_social" class="form-control @error('razon_social') is-invalid @enderror"
                               value="{{ old('razon_social') }}">
                        @error('razon_social')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" placeholder="900000000">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
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
                        <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Código Postal</label>
                        <input type="text" name="codigo_postal" class="form-control" value="{{ old('codigo_postal') }}" placeholder="08001">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Provincia</label>
                        <input type="text" name="provincia" class="form-control" value="{{ old('provincia') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">País</label>
                        <input type="text" name="pais" class="form-control" value="{{ old('pais', 'España') }}">
                    </div>

                    <!-- Persona de contacto -->
                    <div class="col-12">
                        <hr class="my-2">
                        <small class="text-muted fw-semibold">PERSONA DE CONTACTO</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="persona_contacto" class="form-control" value="{{ old('persona_contacto') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono_contacto" class="form-control" value="{{ old('telefono_contacto') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email_contacto" class="form-control @error('email_contacto') is-invalid @enderror"
                               value="{{ old('email_contacto') }}">
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
                            <option value="Contado" {{ old('condiciones_pago') == 'Contado' ? 'selected' : '' }}>Contado</option>
                            <option value="15 días" {{ old('condiciones_pago') == '15 días' ? 'selected' : '' }}>15 días</option>
                            <option value="30 días" {{ old('condiciones_pago') == '30 días' ? 'selected' : '' }}>30 días</option>
                            <option value="45 días" {{ old('condiciones_pago') == '45 días' ? 'selected' : '' }}>45 días</option>
                            <option value="60 días" {{ old('condiciones_pago') == '60 días' ? 'selected' : '' }}>60 días</option>
                            <option value="90 días" {{ old('condiciones_pago') == '90 días' ? 'selected' : '' }}>90 días</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Retención (%)</label>
                        <div class="input-group">
                            <input type="number" name="retencion_porcentaje" class="form-control"
                                   step="0.01" min="0" max="100" value="{{ old('retencion_porcentaje', 0) }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Notas</label>
                        <textarea name="notas" class="form-control" rows="3">{{ old('notas') }}</textarea>
                    </div>

                    <div class="col-12">
                        <hr class="my-3">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Crear Cliente
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
