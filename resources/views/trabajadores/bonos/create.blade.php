@extends('layouts.app')

@section('title', 'Nuevo Bono')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nuevo Bono</h1>
            <p class="text-muted mb-0">Registrar bono o prima para un trabajador</p>
        </div>
        <a href="{{ route('trabajadores.bonos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('trabajadores.bonos.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Datos del Bono</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Trabajadores <span class="text-danger">*</span></label>
                                <div class="border rounded p-2" style="max-height:220px; overflow-y:auto;">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" id="selAllTrab">
                                        <label class="form-check-label fw-semibold" for="selAllTrab">Seleccionar todos</label>
                                    </div>
                                    <hr class="my-1">
                                    <div class="row g-1">
                                        @foreach($trabajadores as $trabajador)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input chk-trab" type="checkbox" name="trabajadores[]" value="{{ $trabajador->id }}" id="trab{{ $trabajador->id }}"
                                                    {{ collect(old('trabajadores', $trabajadorId ? [$trabajadorId] : []))->contains($trabajador->id) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="trab{{ $trabajador->id }}">{{ $trabajador->apellidos }}, {{ $trabajador->nombre }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('trabajadores')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                <small class="text-muted">Puedes seleccionar varios para asignarles el mismo bono.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Obra (opcional)</label>
                                <select name="obra_id" class="form-select @error('obra_id') is-invalid @enderror">
                                    <option value="">Sin obra asociada</option>
                                    @foreach($obras as $obra)
                                        <option value="{{ $obra->id }}" {{ old('obra_id', $obraId ?? '') == $obra->id ? 'selected' : '' }}>
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
                                    <option value="prima_produccion" {{ old('tipo') == 'prima_produccion' ? 'selected' : '' }}>Prima Produccion</option>
                                    <option value="bono_especial" {{ old('tipo') == 'bono_especial' ? 'selected' : '' }}>Bono Especial</option>
                                    <option value="plus_nocturnidad" {{ old('tipo') == 'plus_nocturnidad' ? 'selected' : '' }}>Plus Nocturnidad</option>
                                    <option value="horas" {{ old('tipo') == 'horas' ? 'selected' : '' }}>Horas</option>
                                    <option value="otro" {{ old('tipo') == 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                                       value="{{ old('fecha', date('Y-m-d')) }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Importe <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="importe" id="importeInput" class="form-control @error('importe') is-invalid @enderror"
                                           step="0.01" min="0" value="{{ old('importe') }}" required placeholder="0.00">
                                    <span class="input-group-text">€</span>
                                </div>
                                @error('importe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4" id="tipoHoraWrapper" style="{{ old('tipo') == 'horas' ? '' : 'display:none;' }}">
                                <label class="form-label">Tipo de hora</label>
                                <select name="tipo_hora_id" id="tipoHoraSelect" class="form-select">
                                    <option value="">Manual (sin tipo)</option>
                                    @foreach($tiposHora as $th)
                                        <option value="{{ $th->id }}" data-precio="{{ $th->precio_hora }}" {{ old('tipo_hora_id') == $th->id ? 'selected' : '' }}>{{ $th->nombre }} ({{ number_format($th->precio_hora, 2, ',', '.') }} €/h)</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Importe = horas × precio.</small>
                            </div>

                            <div class="col-md-4" id="horasWrapper" style="{{ old('tipo') == 'horas' ? '' : 'display: none;' }}">
                                <label class="form-label">Horas</label>
                                <div class="input-group">
                                    <input type="number" name="horas" id="horasInput" class="form-control @error('horas') is-invalid @enderror"
                                           step="0.01" min="0" max="999.99" value="{{ old('horas') }}" placeholder="0.00">
                                    <span class="input-group-text">h</span>
                                </div>
                                @error('horas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Concepto <span class="text-danger">*</span></label>
                                <input type="text" name="concepto" class="form-control @error('concepto') is-invalid @enderror"
                                       value="{{ old('concepto') }}" required placeholder="Descripcion del bono o prima">
                                @error('concepto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <textarea name="notas" class="form-control @error('notas') is-invalid @enderror" rows="2"
                                          placeholder="Notas adicionales...">{{ old('notas') }}</textarea>
                            </div>

                            <div class="col-12"><hr class="my-2"></div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="pagado" id="pagado" value="1" {{ old('pagado') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pagado">Ya esta pagado</label>
                                </div>
                            </div>

                            <div class="col-md-6" id="fechaPagoWrapper" style="{{ old('pagado') ? '' : 'display: none;' }}">
                                <label class="form-label">Fecha de Pago</label>
                                <input type="date" name="fecha_pago" id="fechaPago" class="form-control"
                                       value="{{ old('fecha_pago', date('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Info -->
                <div class="card border-0 shadow-sm bg-light mb-4">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Tipos de Bonos
                        </h6>
                        <ul class="small text-muted mb-0 ps-3">
                            <li><strong>Prima Produccion:</strong> Bono por rendimiento en produccion</li>
                            <li><strong>Bono Especial:</strong> Bonificaciones extraordinarias</li>
                            <li><strong>Plus Nocturnidad:</strong> Complemento por trabajo nocturno</li>
                            <li><strong>Horas:</strong> Bono por horas trabajadas (ej. horas extras)</li>
                            <li><strong>Otro:</strong> Cualquier otro tipo de bono</li>
                        </ul>
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Registrar Bono
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

    // Toggle campos de horas + tipo de hora
    const tipoSelect = document.getElementById('tipoSelect');
    const horasWrapper = document.getElementById('horasWrapper');
    const tipoHoraWrapper = document.getElementById('tipoHoraWrapper');
    const horasInput = document.getElementById('horasInput');
    const tipoHoraSelect = document.getElementById('tipoHoraSelect');
    const importeInput = document.getElementById('importeInput');

    function toggleHorasField() {
        const isHoras = tipoSelect.value === 'horas';
        horasWrapper.style.display = isHoras ? 'block' : 'none';
        tipoHoraWrapper.style.display = isHoras ? 'block' : 'none';
    }

    function recalcImporte() {
        const opt = tipoHoraSelect.options[tipoHoraSelect.selectedIndex];
        const precio = opt ? parseFloat(opt.dataset.precio || 0) : 0;
        const horas = parseFloat(horasInput.value) || 0;
        if (precio > 0 && horas > 0) {
            importeInput.value = (precio * horas).toFixed(2);
        }
    }

    tipoSelect.addEventListener('change', toggleHorasField);
    tipoHoraSelect.addEventListener('change', recalcImporte);
    horasInput.addEventListener('input', recalcImporte);
    toggleHorasField();

    // Seleccionar todos los trabajadores
    const selAll = document.getElementById('selAllTrab');
    if (selAll) {
        selAll.addEventListener('change', function() {
            document.querySelectorAll('.chk-trab').forEach(c => c.checked = this.checked);
        });
    }
</script>
@endpush
@endsection
