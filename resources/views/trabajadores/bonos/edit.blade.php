@extends('layouts.app')

@section('title', 'Editar Bono')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Bono</h1>
            <p class="text-muted mb-0">{{ $bono->trabajador->nombre }} {{ $bono->trabajador->apellidos }} - {{ $bono->fecha->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('trabajadores.bonos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('trabajadores.bonos.update', $bono) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Datos del Bono</h5>
                        @if($bono->pagado)
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Pagado</span>
                        @else
                            <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Pendiente</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Trabajador <span class="text-danger">*</span></label>
                                <select name="trabajador_id" class="form-select @error('trabajador_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar trabajador...</option>
                                    @foreach($trabajadores as $trabajador)
                                        <option value="{{ $trabajador->id }}" {{ old('trabajador_id', $bono->trabajador_id) == $trabajador->id ? 'selected' : '' }}>
                                            {{ $trabajador->apellidos }}, {{ $trabajador->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('trabajador_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Obra (opcional)</label>
                                <select name="obra_id" class="form-select @error('obra_id') is-invalid @enderror">
                                    <option value="">Sin obra asociada</option>
                                    @foreach($obras as $obra)
                                        <option value="{{ $obra->id }}" {{ old('obra_id', $bono->obra_id) == $obra->id ? 'selected' : '' }}>
                                            {{ $obra->codigo }} - {{ $obra->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('obra_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select name="tipo" id="tipoSelect" class="form-select @error('tipo') is-invalid @enderror" required>
                                    <option value="prima_produccion" {{ old('tipo', $bono->tipo) == 'prima_produccion' ? 'selected' : '' }}>Prima Produccion</option>
                                    <option value="bono_especial" {{ old('tipo', $bono->tipo) == 'bono_especial' ? 'selected' : '' }}>Bono Especial</option>
                                    <option value="plus_nocturnidad" {{ old('tipo', $bono->tipo) == 'plus_nocturnidad' ? 'selected' : '' }}>Plus Nocturnidad</option>
                                    <option value="horas" {{ old('tipo', $bono->tipo) == 'horas' ? 'selected' : '' }}>Horas</option>
                                    <option value="otro" {{ old('tipo', $bono->tipo) == 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                                       value="{{ old('fecha', $bono->fecha->format('Y-m-d')) }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Importe <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="importe" class="form-control @error('importe') is-invalid @enderror"
                                           step="0.01" min="0" value="{{ old('importe', $bono->importe) }}" required>
                                    <span class="input-group-text">€</span>
                                </div>
                                @error('importe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4" id="horasWrapper" style="{{ old('tipo', $bono->tipo) == 'horas' ? '' : 'display: none;' }}">
                                <label class="form-label">Horas</label>
                                <div class="input-group">
                                    <input type="number" name="horas" id="horasInput" class="form-control @error('horas') is-invalid @enderror"
                                           step="0.01" min="0" max="999.99" value="{{ old('horas', $bono->horas) }}" placeholder="0.00">
                                    <span class="input-group-text">h</span>
                                </div>
                                @error('horas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Concepto <span class="text-danger">*</span></label>
                                <input type="text" name="concepto" class="form-control @error('concepto') is-invalid @enderror"
                                       value="{{ old('concepto', $bono->concepto) }}" required>
                                @error('concepto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <textarea name="notas" class="form-control @error('notas') is-invalid @enderror" rows="2">{{ old('notas', $bono->notas) }}</textarea>
                            </div>

                            <div class="col-12"><hr class="my-2"></div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="pagado" id="pagado" value="1" {{ old('pagado', $bono->pagado) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pagado">Esta pagado</label>
                                </div>
                            </div>

                            <div class="col-md-6" id="fechaPagoWrapper" style="{{ old('pagado', $bono->pagado) ? '' : 'display: none;' }}">
                                <label class="form-label">Fecha de Pago</label>
                                <input type="date" name="fecha_pago" id="fechaPago" class="form-control"
                                       value="{{ old('fecha_pago', $bono->fecha_pago?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Informacion</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr>
                                <td class="text-muted">Registrado por:</td>
                                <td>{{ $bono->registrador->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Creado:</td>
                                <td>{{ $bono->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                    </button>
                    <a href="{{ route('trabajadores.bonos.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const pagadoCheck = document.getElementById('pagado');
    const fechaPagoWrapper = document.getElementById('fechaPagoWrapper');

    pagadoCheck.addEventListener('change', function() {
        fechaPagoWrapper.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle campo horas
    const tipoSelect = document.getElementById('tipoSelect');
    const horasWrapper = document.getElementById('horasWrapper');
    const horasInput = document.getElementById('horasInput');

    function toggleHorasField() {
        if (tipoSelect.value === 'horas') {
            horasWrapper.style.display = 'block';
            horasInput.removeAttribute('disabled');
        } else {
            horasWrapper.style.display = 'none';
            horasInput.setAttribute('disabled', 'disabled');
        }
    }

    tipoSelect.addEventListener('change', toggleHorasField);
    // Ejecutar al cargar si hay old() value
    toggleHorasField();
</script>
@endpush
@endsection
