@extends('layouts.app')

@section('title', 'Nueva Discrepancia - ' . $obra->codigo)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('obras.index') }}">Obras</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('obras.show', $obra) }}">{{ $obra->codigo }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('obras.discrepancias.index', $obra) }}">Discrepancias</a></li>
                    <li class="breadcrumb-item active">Nueva</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">Registrar Discrepancia</h1>
            <p class="text-muted mb-0">{{ $obra->nombre }}</p>
        </div>
        <a href="{{ route('obras.discrepancias.index', $obra) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('obras.discrepancias.store', $obra) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Datos de la Discrepancia</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Periodo (Mes) <span class="text-danger">*</span></label>
                                <input type="month" name="periodo_mes" class="form-control @error('periodo_mes') is-invalid @enderror"
                                       value="{{ old('periodo_mes', $periodo) }}" required>
                                @error('periodo_mes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12"><hr class="my-2"></div>

                            <div class="col-md-4">
                                <label class="form-label">Importe Producido (Manzer) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="importe_producido_manzer" class="form-control @error('importe_producido_manzer') is-invalid @enderror"
                                           step="0.01" min="0" value="{{ old('importe_producido_manzer', $importeProducidoManzer) }}" required>
                                    <span class="input-group-text">€</span>
                                </div>
                                <small class="text-muted">Calculado de los partes diarios del periodo</small>
                                @error('importe_producido_manzer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Importe Validado (Cuadrilla)</label>
                                <div class="input-group">
                                    <input type="number" name="importe_validado_cuadrilla" class="form-control @error('importe_validado_cuadrilla') is-invalid @enderror"
                                           step="0.01" min="0" value="{{ old('importe_validado_cuadrilla') }}">
                                    <span class="input-group-text">€</span>
                                </div>
                                <small class="text-muted">Lo que la cuadrilla valida</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Importe Aceptado (Cliente)</label>
                                <div class="input-group">
                                    <input type="number" name="importe_aceptado_cliente" class="form-control @error('importe_aceptado_cliente') is-invalid @enderror"
                                           step="0.01" min="0" value="{{ old('importe_aceptado_cliente') }}" id="importeAceptado">
                                    <span class="input-group-text">€</span>
                                </div>
                                <small class="text-muted">Lo que el cliente acepta pagar</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha Respuesta Cliente</label>
                                <input type="date" name="fecha_respuesta_cliente" class="form-control @error('fecha_respuesta_cliente') is-invalid @enderror"
                                       value="{{ old('fecha_respuesta_cliente') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <textarea name="notas" class="form-control @error('notas') is-invalid @enderror" rows="3"
                                          placeholder="Observaciones sobre la discrepancia...">{{ old('notas') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Documento de Valoracion</label>
                                <input type="file" name="documento_valoracion" class="form-control @error('documento_valoracion') is-invalid @enderror"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">PDF o imagen (max 10MB)</small>
                                @error('documento_valoracion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Resumen -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Resumen</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr>
                                <td class="text-muted">Obra:</td>
                                <td><strong>{{ $obra->codigo }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Periodo:</td>
                                <td id="periodoDisplay">{{ \Carbon\Carbon::createFromFormat('Y-m', $periodo)->translatedFormat('F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Producido:</td>
                                <td class="text-primary fw-bold" id="producidoDisplay">{{ number_format($importeProducidoManzer, 2, ',', '.') }} €</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pendiente:</td>
                                <td class="text-danger fw-bold" id="pendienteDisplay">{{ number_format($importeProducidoManzer, 2, ',', '.') }} €</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Botones -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Registrar Discrepancia
                    </button>
                    <a href="{{ route('obras.discrepancias.index', $obra) }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const importeAceptado = document.getElementById('importeAceptado');
    const producidoDisplay = document.getElementById('producidoDisplay');
    const pendienteDisplay = document.getElementById('pendienteDisplay');

    importeAceptado.addEventListener('input', function() {
        const producido = {{ $importeProducidoManzer }};
        const aceptado = parseFloat(this.value) || 0;
        const pendiente = producido - aceptado;

        pendienteDisplay.textContent = new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: 'EUR'
        }).format(pendiente);

        pendienteDisplay.classList.toggle('text-danger', pendiente > 0);
        pendienteDisplay.classList.toggle('text-success', pendiente <= 0);
    });
</script>
@endpush
@endsection
