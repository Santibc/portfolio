@extends('layouts.app')

@section('title', 'Nuevo Parte Diario')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nuevo Parte Diario</h1>
            <p class="text-muted mb-0">Registrar trabajo realizado en una obra</p>
        </div>
        <a href="{{ route('partes-diarios.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('partes-diarios.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <!-- Datos básicos -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Datos del Parte</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Obra <span class="text-danger">*</span></label>
                                <select name="obra_id" class="form-select @error('obra_id') is-invalid @enderror" required id="obraSelect">
                                    <option value="">Seleccionar obra...</option>
                                    @foreach($obras as $obra)
                                        <option value="{{ $obra->id }}"
                                                data-linea="{{ $obra->linea }}"
                                                data-trayecto="{{ $obra->trayecto }}"
                                                data-conceptos="{{ $obra->conceptosProduccion->toJson() }}"
                                                {{ (old('obra_id', $obraSeleccionada?->id) == $obra->id) ? 'selected' : '' }}>
                                            {{ $obra->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('obra_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                                       value="{{ old('fecha', date('Y-m-d')) }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jornada <span class="text-danger">*</span></label>
                                <select name="jornada" class="form-select @error('jornada') is-invalid @enderror" required>
                                    <option value="diurna" {{ old('jornada') == 'diurna' ? 'selected' : '' }}>Diurna</option>
                                    <option value="nocturna" {{ old('jornada') == 'nocturna' ? 'selected' : '' }}>Nocturna</option>
                                </select>
                                @error('jornada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                                <small class="text-muted fw-semibold">DATOS ADIF</small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Línea</label>
                                <input type="text" name="linea" class="form-control" id="lineaInput"
                                       value="{{ old('linea', $obraSeleccionada?->linea) }}" placeholder="Ej: 400">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Trayecto</label>
                                <input type="text" name="trayecto" class="form-control" id="trayectoInput"
                                       value="{{ old('trayecto', $obraSeleccionada?->trayecto) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gerencia/Jefatura</label>
                                <input type="text" name="gerencia_jefatura" class="form-control"
                                       value="{{ old('gerencia_jefatura') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Brigada</label>
                                <input type="text" name="brigada" class="form-control"
                                       value="{{ old('brigada', 'MANZER') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Producción Dinámica (según conceptos de la obra) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bar-chart me-2"></i>Producción del Día
                        </h5>
                        <span class="badge bg-primary" id="totalImporteLabel">0.00 €</span>
                    </div>
                    <div class="card-body">
                        <div id="produccionContainer">
                            <!-- Se carga dinámicamente según la obra seleccionada -->
                            <div class="text-center py-4 text-muted" id="noObraSelected">
                                <i class="bi bi-arrow-up-circle fs-1 d-block mb-2"></i>
                                <p class="mb-0">Selecciona una obra para ver sus conceptos de producción</p>
                            </div>
                            <div class="text-center py-4 text-muted d-none" id="noConceptos">
                                <i class="bi bi-exclamation-circle fs-1 d-block mb-2"></i>
                                <p class="mb-0">Esta obra no tiene conceptos de producción configurados</p>
                                <small>Configura los conceptos en la sección de Obras</small>
                            </div>
                            <div class="d-none" id="conceptosTableWrapper">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0" id="conceptosTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 80px">Código</th>
                                                <th>Concepto</th>
                                                <th style="width: 100px">Unidad</th>
                                                <th style="width: 120px" class="text-end">Precio Unit.</th>
                                                <th style="width: 120px">Cantidad</th>
                                                <th style="width: 120px" class="text-end">Importe</th>
                                            </tr>
                                        </thead>
                                        <tbody id="conceptosTableBody">
                                            <!-- Se llena dinámicamente -->
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="5" class="text-end">Total:</th>
                                                <th class="text-end" id="totalImporte">0.00 €</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos legacy ocultos (para compatibilidad temporal) -->
                <input type="hidden" name="desbroce_total_m2" value="0">
                <input type="hidden" name="desbroce_p5_m2" value="0">
                <input type="hidden" name="desbroce_p6_m2" value="0">
                <input type="hidden" name="limpieza_p8_m2" value="0">
                <input type="hidden" name="herbicida_p4_m2" value="0">
                <input type="hidden" name="talas_unidades" value="0">
                <input type="hidden" name="podas_unidades" value="0">

                <!-- Observaciones -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Observaciones e Incidencias</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="3"
                                          placeholder="Observaciones del día...">{{ old('observaciones') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Incidencias</label>
                                <textarea name="incidencias" class="form-control" rows="3"
                                          placeholder="Incidencias o problemas...">{{ old('incidencias') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Trabajadores -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Trabajadores</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Selecciona los trabajadores que participaron:</p>
                        <div class="trabajadores-list" style="max-height: 400px; overflow-y: auto;">
                            @foreach($trabajadores as $trabajador)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           name="trabajadores[]" value="{{ $trabajador->id }}"
                                           id="trab{{ $trabajador->id }}"
                                           {{ in_array($trabajador->id, old('trabajadores', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="trab{{ $trabajador->id }}">
                                        {{ $trabajador->nombre }} {{ $trabajador->apellidos }}
                                        @if($trabajador->categoria)
                                            <br><small class="text-muted">{{ $trabajador->categoria }}</small>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Información
                        </h6>
                        <p class="card-text small text-muted mb-0">
                            El parte se creará como <strong>borrador</strong>. Podrás editarlo y añadir más
                            detalles antes de marcarlo como completado para su validación.
                        </p>
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Crear Parte
                    </button>
                    <a href="{{ route('partes-diarios.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const obraSelect = document.getElementById('obraSelect');
    const noObraSelected = document.getElementById('noObraSelected');
    const noConceptos = document.getElementById('noConceptos');
    const conceptosTableWrapper = document.getElementById('conceptosTableWrapper');
    const conceptosTableBody = document.getElementById('conceptosTableBody');
    const totalImporte = document.getElementById('totalImporte');
    const totalImporteLabel = document.getElementById('totalImporteLabel');

    // Auto-rellenar datos ADIF y cargar conceptos al seleccionar obra
    obraSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        document.getElementById('lineaInput').value = option.dataset.linea || '';
        document.getElementById('trayectoInput').value = option.dataset.trayecto || '';

        loadConceptos(option);
    });

    function loadConceptos(option) {
        const obraId = option.value;

        // Reset
        conceptosTableBody.innerHTML = '';
        noObraSelected.classList.add('d-none');
        noConceptos.classList.add('d-none');
        conceptosTableWrapper.classList.add('d-none');

        if (!obraId) {
            noObraSelected.classList.remove('d-none');
            updateTotals();
            return;
        }

        try {
            const conceptos = JSON.parse(option.dataset.conceptos || '[]');

            if (conceptos.length === 0) {
                noConceptos.classList.remove('d-none');
                updateTotals();
                return;
            }

            // Build table rows
            conceptos.forEach((concepto, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><code class="fw-bold">${concepto.codigo}</code></td>
                    <td>
                        ${concepto.nombre}
                        <input type="hidden" name="producciones[${index}][concepto_id]" value="${concepto.id}">
                    </td>
                    <td><span class="badge bg-secondary-subtle text-secondary">${concepto.unidad}</span></td>
                    <td class="text-end">${formatCurrency(concepto.precio_unitario)}</td>
                    <td>
                        <input type="number"
                               name="producciones[${index}][cantidad]"
                               class="form-control form-control-sm cantidad-input"
                               step="0.01"
                               min="0"
                               value="0"
                               data-precio="${concepto.precio_unitario}"
                               data-row="${index}">
                    </td>
                    <td class="text-end fw-semibold importe-cell" id="importe-${index}">0.00 €</td>
                `;
                conceptosTableBody.appendChild(row);
            });

            // Add event listeners to cantidad inputs
            document.querySelectorAll('.cantidad-input').forEach(input => {
                input.addEventListener('input', function() {
                    updateRowImporte(this);
                    updateTotals();
                });
            });

            conceptosTableWrapper.classList.remove('d-none');
            updateTotals();

        } catch (e) {
            console.error('Error parsing conceptos:', e);
            noConceptos.classList.remove('d-none');
        }
    }

    function updateRowImporte(input) {
        const cantidad = parseFloat(input.value) || 0;
        const precio = parseFloat(input.dataset.precio) || 0;
        const row = input.dataset.row;
        const importe = cantidad * precio;

        document.getElementById(`importe-${row}`).textContent = formatCurrency(importe);
    }

    function updateTotals() {
        let total = 0;
        document.querySelectorAll('.cantidad-input').forEach(input => {
            const cantidad = parseFloat(input.value) || 0;
            const precio = parseFloat(input.dataset.precio) || 0;
            total += cantidad * precio;
        });

        const formattedTotal = formatCurrency(total);
        totalImporte.textContent = formattedTotal;
        totalImporteLabel.textContent = formattedTotal;

        // Color coding
        if (total > 0) {
            totalImporteLabel.classList.remove('bg-primary');
            totalImporteLabel.classList.add('bg-success');
        } else {
            totalImporteLabel.classList.remove('bg-success');
            totalImporteLabel.classList.add('bg-primary');
        }
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: 'EUR'
        }).format(value);
    }

    // Initialize on page load if obra is pre-selected
    document.addEventListener('DOMContentLoaded', function() {
        if (obraSelect.value) {
            const option = obraSelect.options[obraSelect.selectedIndex];
            loadConceptos(option);
        }
    });
</script>
@endpush
@endsection
