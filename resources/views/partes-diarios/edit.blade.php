@extends('layouts.app')

@section('title', $partes_diario->es_mensual ? 'Editar Parte Mensual' : 'Editar Parte Diario')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                Editar {{ $partes_diario->es_mensual ? 'Parte Mensual' : 'Parte Diario' }}
                @if($partes_diario->es_mensual)
                    <span class="badge bg-info-subtle text-info fs-6 ms-2">Mensual</span>
                @endif
            </h1>
            <p class="text-muted mb-0">
                {{ $partes_diario->obra->nombre }} - {{ $partes_diario->fecha_display }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('partes-diarios.show', $partes_diario) }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye me-2"></i>Ver
            </a>
            <a href="{{ route('partes-diarios.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <form action="{{ route('partes-diarios.update', $partes_diario) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
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
                                <label class="form-label">Obra</label>
                                <input type="text" class="form-control" readonly
                                       value="{{ $partes_diario->obra->nombre }}">
                            </div>

                            @if($partes_diario->es_mensual)
                                <div class="col-md-3">
                                    <label class="form-label">Periodo</label>
                                    <input type="text" class="form-control" readonly
                                           value="{{ $partes_diario->fecha_display }}">
                                </div>
                            @else
                                <div class="col-md-3">
                                    <label class="form-label">Fecha</label>
                                    <input type="text" class="form-control" readonly
                                           value="{{ $partes_diario->fecha->format('d/m/Y') }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Jornada <span class="text-danger">*</span></label>
                                    <select name="jornada" class="form-select @error('jornada') is-invalid @enderror" required>
                                        <option value="diurna" {{ old('jornada', $partes_diario->jornada) == 'diurna' ? 'selected' : '' }}>Diurna</option>
                                        <option value="nocturna" {{ old('jornada', $partes_diario->jornada) == 'nocturna' ? 'selected' : '' }}>Nocturna</option>
                                    </select>
                                    @error('jornada')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="col-12">
                                <hr class="my-2">
                                <small class="text-muted fw-semibold">DATOS ADIF</small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Línea</label>
                                <input type="text" name="linea" class="form-control"
                                       value="{{ old('linea', $partes_diario->linea) }}" placeholder="Ej: 400">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Trayecto</label>
                                <input type="text" name="trayecto" class="form-control"
                                       value="{{ old('trayecto', $partes_diario->trayecto) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gerencia/Jefatura</label>
                                <input type="text" name="gerencia_jefatura" class="form-control"
                                       value="{{ old('gerencia_jefatura', $partes_diario->gerencia_jefatura) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Brigada</label>
                                <input type="text" name="brigada" class="form-control"
                                       value="{{ old('brigada', $partes_diario->brigada) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Producción Dinámica -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bar-chart me-2"></i>{{ $partes_diario->es_mensual ? 'Producción del Periodo' : 'Producción del Día' }}
                        </h5>
                        @unless(auth()->user()->hasRole('Encargado'))
                            <span class="badge bg-success" id="totalImporteLabel">{{ $partes_diario->importe_total_formateado }}</span>
                        @endunless
                    </div>
                    <div class="card-body">
                        @php
                            $conceptos = $partes_diario->obra->conceptosProduccion->where('activo', true);
                            $produccionesExistentes = $partes_diario->producciones->keyBy('concepto_produccion_id');
                        @endphp

                        @if($conceptos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 80px">Código</th>
                                            <th>Concepto</th>
                                            <th style="width: 100px">Unidad</th>
                                            @unless(auth()->user()->hasRole('Encargado'))
                                                <th style="width: 120px" class="text-end">Precio Unit.</th>
                                            @endunless
                                            <th style="width: 120px">Cantidad</th>
                                            @unless(auth()->user()->hasRole('Encargado'))
                                                <th style="width: 120px" class="text-end">Importe</th>
                                            @endunless
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($conceptos as $index => $concepto)
                                            @php
                                                $produccionExistente = $produccionesExistentes->get($concepto->id);
                                                $cantidadActual = $produccionExistente ? $produccionExistente->cantidad : 0;
                                                $importeActual = $produccionExistente ? $produccionExistente->importe_calculado : 0;
                                            @endphp
                                            <tr>
                                                <td><code class="fw-bold">{{ $concepto->codigo }}</code></td>
                                                <td>
                                                    {{ $concepto->nombre }}
                                                    <input type="hidden" name="producciones[{{ $index }}][concepto_id]" value="{{ $concepto->id }}">
                                                </td>
                                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $concepto->unidad }}</span></td>
                                                @unless(auth()->user()->hasRole('Encargado'))
                                                    <td class="text-end">{{ number_format($concepto->precio_unitario, 2, ',', '.') }} €</td>
                                                @endunless
                                                <td>
                                                    <input type="number"
                                                           name="producciones[{{ $index }}][cantidad]"
                                                           class="form-control form-control-sm cantidad-input"
                                                           step="0.01"
                                                           min="0"
                                                           value="{{ old('producciones.'.$index.'.cantidad', $cantidadActual) }}"
                                                           data-precio="{{ $concepto->precio_unitario }}"
                                                           data-row="{{ $index }}">
                                                </td>
                                                @unless(auth()->user()->hasRole('Encargado'))
                                                    <td class="text-end fw-semibold importe-cell" id="importe-{{ $index }}">{{ number_format($importeActual, 2, ',', '.') }} €</td>
                                                @endunless
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            @unless(auth()->user()->hasRole('Encargado'))
                                                <th colspan="5" class="text-end">Total:</th>
                                                <th class="text-end" id="totalImporte">{{ $partes_diario->importe_total_formateado }}</th>
                                            @else
                                                <th colspan="3" class="text-end">Producción Registrada</th>
                                            @endunless
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-exclamation-circle fs-1 d-block mb-2"></i>
                                <p class="mb-0">Esta obra no tiene conceptos de producción configurados</p>
                                <small>Configura los conceptos en la sección de Obras</small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Campos legacy ocultos (para compatibilidad temporal) -->
                <input type="hidden" name="desbroce_total_m2" value="{{ $partes_diario->desbroce_total_m2 }}">
                <input type="hidden" name="desbroce_p5_m2" value="{{ $partes_diario->desbroce_p5_m2 }}">
                <input type="hidden" name="desbroce_p6_m2" value="{{ $partes_diario->desbroce_p6_m2 }}">
                <input type="hidden" name="limpieza_p8_m2" value="{{ $partes_diario->limpieza_p8_m2 }}">
                <input type="hidden" name="herbicida_p4_m2" value="{{ $partes_diario->herbicida_p4_m2 }}">
                <input type="hidden" name="talas_unidades" value="{{ $partes_diario->talas_unidades }}">
                <input type="hidden" name="podas_unidades" value="{{ $partes_diario->podas_unidades }}">

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
                                          placeholder="Observaciones del día...">{{ old('observaciones', $partes_diario->observaciones) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Incidencias</label>
                                <textarea name="incidencias" class="form-control" rows="3"
                                          placeholder="Incidencias o problemas...">{{ old('incidencias', $partes_diario->incidencias) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Estado -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Estado:</span>
                            @switch($partes_diario->estado)
                                @case('borrador')
                                    <span class="badge bg-secondary">Borrador</span>
                                    @break
                                @case('completado')
                                    <span class="badge bg-warning text-dark">Pendiente validación</span>
                                    @break
                                @case('validado')
                                    <span class="badge bg-success">Validado</span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>

                @include('partes-diarios.partials.trabajadores-selector', [
                    'preselected' => old('trabajadores', $partes_diario->trabajadores->pluck('trabajador_id')->toArray()),
                    'obraId' => $partes_diario->obra_id,
                ])

                <!-- Documentos -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-paperclip me-2"></i>Documentos y Fotos
                        </h5>
                        @if($partes_diario->documentos->count() > 0)
                            <span class="badge bg-primary">{{ $partes_diario->documentos->count() }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        {{-- Documentos existentes --}}
                        @if($partes_diario->documentos->count() > 0)
                            <div class="mb-3">
                                @foreach($partes_diario->documentos as $doc)
                                    <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $ext = strtolower(pathinfo($doc->archivo_nombre_original, PATHINFO_EXTENSION));
                                                $iconClass = match(true) {
                                                    in_array($ext, ['jpg','jpeg','png','gif','webp']) => 'bi-file-image text-success',
                                                    $ext === 'pdf' => 'bi-file-pdf text-danger',
                                                    in_array($ext, ['doc','docx']) => 'bi-file-word text-primary',
                                                    in_array($ext, ['xls','xlsx']) => 'bi-file-excel text-success',
                                                    default => 'bi-file-earmark text-secondary',
                                                };
                                            @endphp
                                            <i class="bi {{ $iconClass }} fs-5 me-2"></i>
                                            <a href="{{ asset($doc->archivo_path) }}" target="_blank" class="text-decoration-none small">
                                                {{ Str::limit($doc->archivo_nombre_original, 25) }}
                                            </a>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="eliminarDocumento({{ $partes_diario->id }}, {{ $doc->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Subir nuevos --}}
                        <label class="form-label small text-muted">Añadir nuevos archivos:</label>
                        <input type="file" name="documentos[]" class="form-control form-control-sm" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                        <div class="form-text">Máximo 10MB por archivo.</div>
                        @error('documentos.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                    </button>
                    <a href="{{ route('partes-diarios.show', $partes_diario) }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Check if user can view prices (not Encargado role)
    const canViewPrices = @json(!auth()->user()->hasRole('Encargado'));

    // Calcular importes en tiempo real
    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.addEventListener('input', function() {
            updateRowImporte(this);
            updateTotals();
        });
    });

    function updateRowImporte(input) {
        const cantidad = parseFloat(input.value) || 0;
        const precio = parseFloat(input.dataset.precio) || 0;
        const row = input.dataset.row;
        const importe = cantidad * precio;

        // Only update display if element exists (not Encargado role)
        const importeEl = document.getElementById(`importe-${row}`);
        if (importeEl) {
            importeEl.textContent = formatCurrency(importe);
        }
    }

    function updateTotals() {
        let total = 0;
        document.querySelectorAll('.cantidad-input').forEach(input => {
            const cantidad = parseFloat(input.value) || 0;
            const precio = parseFloat(input.dataset.precio) || 0;
            total += cantidad * precio;
        });

        // Only update display elements if they exist (not Encargado role)
        const totalImporteEl = document.getElementById('totalImporte');
        const totalImporteLabelEl = document.getElementById('totalImporteLabel');

        if (totalImporteEl && totalImporteLabelEl) {
            const formattedTotal = formatCurrency(total);
            totalImporteEl.textContent = formattedTotal;
            totalImporteLabelEl.textContent = formattedTotal;
        }
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: 'EUR'
        }).format(value);
    }

    function eliminarDocumento(parteId, docId) {
        Swal.fire({
            title: '¿Eliminar documento?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/partes-diarios/' + parteId + '/documentos/' + docId;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection
