@extends('layouts.app')

@section('title', 'Editar Subcontrata')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('subcontratas.index') }}">Subcontratas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('subcontratas.show', $subcontrata) }}">{{ $subcontrata->nombre }}</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Editar Subcontrata</h1>
        </div>
        <a href="{{ route('subcontratas.show', $subcontrata) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('subcontratas.update', $subcontrata) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <!-- Datos de la empresa -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-building me-2"></i>Datos de la Empresa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre', $subcontrata->nombre) }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Razón Social</label>
                                <input type="text" name="razon_social" class="form-control @error('razon_social') is-invalid @enderror"
                                       value="{{ old('razon_social', $subcontrata->razon_social) }}">
                                @error('razon_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">CIF</label>
                                <input type="text" name="cif" class="form-control @error('cif') is-invalid @enderror"
                                       value="{{ old('cif', $subcontrata->cif) }}" placeholder="B12345678">
                                @error('cif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                       value="{{ old('telefono', $subcontrata->telefono) }}" placeholder="900000000">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $subcontrata->email) }}" placeholder="contacto@empresa.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Dirección</label>
                                <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                                          rows="2">{{ old('direccion', $subcontrata->direccion) }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Persona de contacto -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Persona de Contacto</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nombre del Contacto</label>
                                <input type="text" name="persona_contacto" class="form-control @error('persona_contacto') is-invalid @enderror"
                                       value="{{ old('persona_contacto', $subcontrata->persona_contacto) }}">
                                @error('persona_contacto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notas -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-sticky me-2"></i>Notas</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="notas" class="form-control @error('notas') is-invalid @enderror"
                                  rows="3" placeholder="Observaciones adicionales...">{{ old('notas', $subcontrata->notas) }}</textarea>
                        @error('notas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Tarifas -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-currency-euro me-2"></i>Tarifas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Tarifa por Hora</label>
                                <div class="input-group">
                                    <input type="number" name="tarifa_hora" class="form-control @error('tarifa_hora') is-invalid @enderror"
                                           value="{{ old('tarifa_hora', $subcontrata->tarifa_hora) }}" step="0.01" min="0">
                                    <span class="input-group-text">€/h</span>
                                </div>
                                @error('tarifa_hora')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Tarifa por Día</label>
                                <div class="input-group">
                                    <input type="number" name="tarifa_dia" class="form-control @error('tarifa_dia') is-invalid @enderror"
                                           value="{{ old('tarifa_dia', $subcontrata->tarifa_dia) }}" step="0.01" min="0">
                                    <span class="input-group-text">€/día</span>
                                </div>
                                @error('tarifa_dia')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado actual -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-toggle-on me-2"></i>Estado Actual</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            @if($subcontrata->activa)
                                <span class="badge bg-success-subtle text-success fs-6">
                                    <i class="bi bi-check-circle me-1"></i>Activa
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fs-6">
                                    <i class="bi bi-x-circle me-1"></i>Inactiva
                                </span>
                            @endif

                            @if($subcontrata->homologada)
                                <span class="badge bg-info-subtle text-info fs-6">
                                    <i class="bi bi-award me-1"></i>Homologada
                                    @if($subcontrata->fecha_homologacion)
                                        <small>({{ $subcontrata->fecha_homologacion->format('d/m/Y') }})</small>
                                    @endif
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary fs-6">
                                    <i class="bi bi-dash-circle me-1"></i>No homologada
                                </span>
                            @endif
                        </div>
                        <hr>
                        <small class="text-muted">
                            El estado y la homologación se gestionan desde la vista de detalle de la subcontrata.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-end gap-2">
                <a href="{{ route('subcontratas.show', $subcontrata) }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
