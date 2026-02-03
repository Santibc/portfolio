@extends('layouts.app')

@section('title', 'Nuevo Contrato')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nuevo Contrato</h1>
            <p class="text-muted mb-0">Crear un nuevo contrato</p>
        </div>
        <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    {{-- Errores --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle me-2"></i>Error:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('contratos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Formulario principal --}}
            <div class="col-lg-8">
                {{-- Información básica --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Información del Contrato</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Tipo de contrato --}}
                            <div class="col-md-6">
                                <label for="contrato_tipo_id" class="form-label">Tipo de Contrato <span class="text-danger">*</span></label>
                                <select name="contrato_tipo_id" id="contrato_tipo_id" class="form-select @error('contrato_tipo_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('contrato_tipo_id') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('contrato_tipo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Código --}}
                            <div class="col-md-6">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="text" name="codigo" id="codigo" class="form-control @error('codigo') is-invalid @enderror"
                                       value="{{ old('codigo', $codigoSugerido) }}" maxlength="50" placeholder="Auto-generado si se deja vacío">
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Dejar vacío para generar automáticamente</small>
                            </div>

                            {{-- Título --}}
                            <div class="col-12">
                                <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                                <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror"
                                       value="{{ old('titulo') }}" required maxlength="255" placeholder="Título descriptivo del contrato">
                                @error('titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Descripción --}}
                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                          rows="3" placeholder="Descripción detallada del contrato (opcional)">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Parte contratante --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Parte Contratante</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info small mb-3">
                                    <i class="bi bi-info-circle me-2"></i>Seleccione un cliente O una subcontrata (no ambos)
                                </div>
                            </div>

                            {{-- Cliente --}}
                            <div class="col-md-6">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror">
                                    <option value="">Sin cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nombre_comercial ?? $cliente->razon_social }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Subcontrata --}}
                            <div class="col-md-6">
                                <label for="subcontrata_id" class="form-label">Subcontrata</label>
                                <select name="subcontrata_id" id="subcontrata_id" class="form-select @error('subcontrata_id') is-invalid @enderror">
                                    <option value="">Sin subcontrata</option>
                                    @foreach($subcontratas as $sub)
                                        <option value="{{ $sub->id }}" {{ old('subcontrata_id') == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subcontrata_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fechas --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Fechas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Fecha firma --}}
                            <div class="col-md-4">
                                <label for="fecha_firma" class="form-label">Fecha de Firma</label>
                                <input type="date" name="fecha_firma" id="fecha_firma" class="form-control @error('fecha_firma') is-invalid @enderror"
                                       value="{{ old('fecha_firma') }}">
                                @error('fecha_firma')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Fecha inicio --}}
                            <div class="col-md-4">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror"
                                       value="{{ old('fecha_inicio') }}">
                                @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Fecha fin --}}
                            <div class="col-md-4">
                                <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror"
                                       value="{{ old('fecha_fin') }}">
                                @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Importes --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-currency-euro me-2"></i>Importes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Importe base --}}
                            <div class="col-md-4">
                                <label for="importe" class="form-label">Base Imponible</label>
                                <div class="input-group">
                                    <input type="number" name="importe" id="importe" class="form-control @error('importe') is-invalid @enderror"
                                           value="{{ old('importe') }}" min="0" step="0.01" placeholder="0,00">
                                    <span class="input-group-text">€</span>
                                </div>
                                @error('importe')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- IVA --}}
                            <div class="col-md-4">
                                <label for="iva_porcentaje" class="form-label">IVA <span class="text-danger">*</span></label>
                                <select name="iva_porcentaje" id="iva_porcentaje" class="form-select @error('iva_porcentaje') is-invalid @enderror" required>
                                    <option value="0" {{ old('iva_porcentaje') == '0' ? 'selected' : '' }}>0%</option>
                                    <option value="4" {{ old('iva_porcentaje') == '4' ? 'selected' : '' }}>4%</option>
                                    <option value="10" {{ old('iva_porcentaje') == '10' ? 'selected' : '' }}>10%</option>
                                    <option value="21" {{ old('iva_porcentaje', '21') == '21' ? 'selected' : '' }}>21%</option>
                                </select>
                                @error('iva_porcentaje')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Total calculado --}}
                            <div class="col-md-4">
                                <label class="form-label">Total</label>
                                <div class="input-group">
                                    <input type="text" id="importe_total" class="form-control fw-bold bg-light" readonly value="0,00">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                        </div>

                        {{-- Desglose --}}
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="bg-light rounded p-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Base imponible:</span>
                                        <span id="displayBase">0,00 €</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>IVA (<span id="displayIvaPct">21</span>%):</span>
                                        <span id="displayIva">0,00 €</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>TOTAL:</span>
                                        <span id="displayTotal">0,00 €</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Retención de garantía --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-lock me-2"></i>Retención de Garantía</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Tiene retención --}}
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="tiene_retencion" id="tiene_retencion" value="1"
                                           {{ old('tiene_retencion') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tiene_retencion">
                                        El contrato tiene retención de garantía
                                    </label>
                                </div>
                            </div>

                            {{-- Campos de retención (ocultos por defecto) --}}
                            <div id="campos_retencion" style="display: {{ old('tiene_retencion') ? 'block' : 'none' }};">
                                <div class="row g-3 mt-2">
                                    {{-- Porcentaje retención --}}
                                    <div class="col-md-4">
                                        <label for="retencion_porcentaje" class="form-label">Porcentaje de Retención</label>
                                        <div class="input-group">
                                            <input type="number" name="retencion_porcentaje" id="retencion_porcentaje" class="form-control @error('retencion_porcentaje') is-invalid @enderror"
                                                   value="{{ old('retencion_porcentaje', 5) }}" min="0" max="100" step="0.01">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('retencion_porcentaje')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Importe retenido (calculado) --}}
                                    <div class="col-md-4">
                                        <label class="form-label">Importe Retenido</label>
                                        <div class="input-group">
                                            <input type="text" id="importe_retenido" class="form-control bg-light" readonly value="0,00">
                                            <span class="input-group-text">€</span>
                                        </div>
                                    </div>

                                    {{-- Fecha liberación prevista --}}
                                    <div class="col-md-4">
                                        <label for="fecha_liberacion_garantia" class="form-label">Fecha Liberación Prevista</label>
                                        <input type="date" name="fecha_liberacion_garantia" id="fecha_liberacion_garantia" class="form-control @error('fecha_liberacion_garantia') is-invalid @enderror"
                                               value="{{ old('fecha_liberacion_garantia') }}">
                                        @error('fecha_liberacion_garantia')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Documento y notas --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-paperclip me-2"></i>Documento y Notas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Documento PDF --}}
                            <div class="col-12">
                                <label for="documento" class="form-label">Documento del Contrato</label>
                                <input type="file" name="documento" id="documento" class="form-control @error('documento') is-invalid @enderror">
                                @error('documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Máximo 10MB.</small>
                            </div>

                            {{-- Notas --}}
                            <div class="col-12">
                                <label for="notas" class="form-label">Notas</label>
                                <textarea name="notas" id="notas" class="form-control @error('notas') is-invalid @enderror"
                                          rows="3" placeholder="Notas internas (opcional)">{{ old('notas') }}</textarea>
                                @error('notas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Info --}}
                <div class="card border-0 shadow-sm bg-light mb-4">
                    <div class="card-body">
                        <h6 class="text-primary"><i class="bi bi-info-circle me-2"></i>Información</h6>
                        <p class="text-muted small mb-2">
                            El contrato se creará en estado <strong>Borrador</strong>.
                        </p>
                        <p class="text-muted small mb-0">
                            Después de guardarlo, podrás:
                        </p>
                        <ul class="text-muted small mb-0">
                            <li>Revisar los datos</li>
                            <li>Activar el contrato</li>
                            <li>Gestionar la garantía</li>
                        </ul>
                    </div>
                </div>

                {{-- Opciones adicionales --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Opciones</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="renovacion_automatica" id="renovacion_automatica" value="1"
                                   {{ old('renovacion_automatica') ? 'checked' : '' }}>
                            <label class="form-check-label" for="renovacion_automatica">
                                Renovación automática
                            </label>
                        </div>

                        <div class="mb-0">
                            <label for="dias_preaviso_vencimiento" class="form-label small">Días preaviso vencimiento</label>
                            <input type="number" name="dias_preaviso_vencimiento" id="dias_preaviso_vencimiento" class="form-control form-control-sm"
                                   value="{{ old('dias_preaviso_vencimiento', 30) }}" min="1" max="365">
                            <small class="text-muted">Para alertas de vencimiento próximo</small>
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Crear Contrato
                    </button>
                    <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-2"></i>Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Toggle campos de retención
    document.getElementById('tiene_retencion').addEventListener('change', function() {
        document.getElementById('campos_retencion').style.display = this.checked ? 'block' : 'none';
        calcularTotal();
    });

    // Limpiar selector contrario cuando se selecciona uno
    document.getElementById('cliente_id').addEventListener('change', function() {
        if (this.value) {
            document.getElementById('subcontrata_id').value = '';
        }
    });
    document.getElementById('subcontrata_id').addEventListener('change', function() {
        if (this.value) {
            document.getElementById('cliente_id').value = '';
        }
    });

    // Calcular totales
    function calcularTotal() {
        const importe = parseFloat(document.getElementById('importe').value) || 0;
        const ivaPct = parseFloat(document.getElementById('iva_porcentaje').value) || 0;
        const tieneRetencion = document.getElementById('tiene_retencion').checked;
        const retencionPct = parseFloat(document.getElementById('retencion_porcentaje').value) || 0;

        const ivaImporte = importe * (ivaPct / 100);
        const total = importe + ivaImporte;
        const importeRetenido = tieneRetencion ? importe * (retencionPct / 100) : 0;

        // Formatear números
        const formatear = (num) => num.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('importe_total').value = formatear(total);
        document.getElementById('displayBase').textContent = formatear(importe) + ' €';
        document.getElementById('displayIvaPct').textContent = ivaPct;
        document.getElementById('displayIva').textContent = formatear(ivaImporte) + ' €';
        document.getElementById('displayTotal').textContent = formatear(total) + ' €';
        document.getElementById('importe_retenido').value = formatear(importeRetenido);
    }

    document.getElementById('importe').addEventListener('input', calcularTotal);
    document.getElementById('iva_porcentaje').addEventListener('change', calcularTotal);
    document.getElementById('retencion_porcentaje').addEventListener('input', calcularTotal);

    // Calcular al cargar
    calcularTotal();
</script>
@endpush
