@extends('layouts.app')

@section('title', 'Editar Discrepancia - ' . $obra->codigo)

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
                    <li class="breadcrumb-item active">{{ $discrepancia->periodo_formateado }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">Editar Discrepancia</h1>
            <p class="text-muted mb-0">{{ $discrepancia->periodo_formateado }} - {{ $obra->nombre }}</p>
        </div>
        <a href="{{ route('obras.discrepancias.index', $obra) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('obras.discrepancias.update', [$obra, $discrepancia]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Datos de la Discrepancia</h5>
                        @php
                            $estadoColors = ['pendiente' => 'warning', 'parcial' => 'info', 'resuelto' => 'success'];
                        @endphp
                        <span class="badge bg-{{ $estadoColors[$discrepancia->estado] }}">{{ ucfirst($discrepancia->estado) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Periodo</label>
                                <input type="text" class="form-control" value="{{ $discrepancia->periodo_formateado }}" disabled>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Importe Producido (Manzer)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ number_format($discrepancia->importe_producido_manzer, 2, ',', '.') }}" disabled>
                                    <span class="input-group-text">€</span>
                                </div>
                                <small class="text-muted">No editable - viene de los partes</small>
                            </div>

                            <div class="col-12"><hr class="my-2"></div>

                            <div class="col-md-4">
                                <label class="form-label">Importe Validado (Cuadrilla)</label>
                                <div class="input-group">
                                    <input type="number" name="importe_validado_cuadrilla" class="form-control @error('importe_validado_cuadrilla') is-invalid @enderror"
                                           step="0.01" min="0" value="{{ old('importe_validado_cuadrilla', $discrepancia->importe_validado_cuadrilla) }}">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Importe Aceptado (Cliente)</label>
                                <div class="input-group">
                                    <input type="number" name="importe_aceptado_cliente" class="form-control @error('importe_aceptado_cliente') is-invalid @enderror"
                                           step="0.01" min="0" value="{{ old('importe_aceptado_cliente', $discrepancia->importe_aceptado_cliente) }}" id="importeAceptado">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha Respuesta Cliente</label>
                                <input type="date" name="fecha_respuesta_cliente" class="form-control @error('fecha_respuesta_cliente') is-invalid @enderror"
                                       value="{{ old('fecha_respuesta_cliente', $discrepancia->fecha_respuesta_cliente?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <textarea name="notas" class="form-control @error('notas') is-invalid @enderror" rows="3"
                                          placeholder="Observaciones sobre la discrepancia...">{{ old('notas', $discrepancia->notas) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Documento de Valoracion</label>
                                @if($discrepancia->documento_valoracion_path)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $discrepancia->documento_valoracion_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-earmark me-1"></i>Ver documento actual
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="documento_valoracion" class="form-control @error('documento_valoracion') is-invalid @enderror"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">PDF o imagen (max 10MB). Dejar vacio para mantener el actual.</small>
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
                                <td class="text-muted">Producido:</td>
                                <td class="text-primary fw-bold">{{ number_format($discrepancia->importe_producido_manzer, 2, ',', '.') }} €</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Aceptado:</td>
                                <td class="text-success fw-bold" id="aceptadoDisplay">{{ number_format($discrepancia->importe_aceptado_cliente ?? 0, 2, ',', '.') }} €</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pendiente:</td>
                                <td class="text-{{ $discrepancia->importe_pendiente > 0 ? 'danger' : 'success' }} fw-bold" id="pendienteDisplay">
                                    {{ number_format($discrepancia->importe_pendiente, 2, ',', '.') }} €
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Registrado por:</td>
                                <td>{{ $discrepancia->registrador->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Creado:</td>
                                <td>{{ $discrepancia->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Botones -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                    </button>
                    @if($discrepancia->estado !== 'resuelto')
                    <button type="button" class="btn btn-success" id="btnResolver">
                        <i class="bi bi-check-all me-2"></i>Marcar como Resuelto
                    </button>
                    @endif
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
    const pendienteDisplay = document.getElementById('pendienteDisplay');
    const producido = {{ $discrepancia->importe_producido_manzer }};

    importeAceptado.addEventListener('input', function() {
        const aceptado = parseFloat(this.value) || 0;
        const pendiente = producido - aceptado;

        pendienteDisplay.textContent = new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: 'EUR'
        }).format(pendiente);

        pendienteDisplay.classList.toggle('text-danger', pendiente > 0);
        pendienteDisplay.classList.toggle('text-success', pendiente <= 0);
    });

    // Marcar como resuelto
    const btnResolver = document.getElementById('btnResolver');
    if (btnResolver) {
        btnResolver.addEventListener('click', function() {
            Swal.fire({
                title: 'Marcar como Resuelto',
                html: '<p>Esta accion marcara la discrepancia como resuelta.</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Si, marcar resuelto',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("obras.discrepancias.resolver", [$obra, $discrepancia]) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Resuelto',
                                text: data.message,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Error al procesar la solicitud', 'error');
                    });
                }
            });
        });
    }
</script>
@endpush
@endsection
