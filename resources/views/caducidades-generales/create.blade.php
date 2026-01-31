@extends('layouts.app')

@section('title', 'Nueva Caducidad de Empresa')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nueva Caducidad de Empresa</h1>
            <p class="text-muted mb-0">Registra una nueva certificación, seguro o documento de la empresa</p>
        </div>
        <a href="{{ route('caducidades-generales.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('caducidades-generales.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <!-- Tipo -->
                            <div class="col-md-6">
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach($tipos as $key => $label)
                                        <option value="{{ $key }}" {{ old('tipo') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre') }}" placeholder="Ej: Certificación ISO 9001:2015" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                          rows="3" placeholder="Descripción o notas adicionales...">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha Emisión -->
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Emisión</label>
                                <input type="date" name="fecha_emision" class="form-control @error('fecha_emision') is-invalid @enderror"
                                       value="{{ old('fecha_emision') }}">
                                @error('fecha_emision')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha Caducidad -->
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Caducidad <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_caducidad" class="form-control @error('fecha_caducidad') is-invalid @enderror"
                                       value="{{ old('fecha_caducidad') }}" required>
                                @error('fecha_caducidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Documento -->
                            <div class="col-md-8">
                                <label class="form-label">Documento</label>
                                <input type="file" name="documento" class="form-control @error('documento') is-invalid @enderror">
                                <small class="text-muted">Máximo 10MB.</small>
                                @error('documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Alerta Activa -->
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="alerta_activa"
                                           id="alerta_activa" value="1" {{ old('alerta_activa', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="alerta_activa">
                                        Generar alertas
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('caducidades-generales.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Guardar Caducidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Información</h6>
                    <p class="small text-muted mb-3">
                        Las caducidades de empresa permiten hacer seguimiento de documentos importantes como:
                    </p>
                    <ul class="small text-muted mb-0">
                        <li>Seguro de Responsabilidad Civil</li>
                        <li>Certificaciones ISO</li>
                        <li>Homologaciones y acreditaciones</li>
                        <li>Licencias de actividad</li>
                        <li>Pólizas de seguro</li>
                        <li>Otros documentos con caducidad</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-bell me-2"></i>Alertas</h6>
                    <p class="small text-muted mb-0">
                        Si activas las alertas, el sistema generará avisos automáticos cuando se acerque
                        la fecha de caducidad según la configuración de días de antelación.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
