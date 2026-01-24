@extends('layouts.app')

@section('title', 'Nuevo Gasto')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nuevo Gasto</h1>
            <p class="text-muted mb-0">Registrar un nuevo gasto</p>
        </div>
        <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary">
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

    <form action="{{ route('gastos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Formulario principal --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información del Gasto</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Categoría --}}
                            <div class="col-md-6">
                                <label for="gasto_categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                                <select name="gasto_categoria_id" id="gasto_categoria_id" class="form-select @error('gasto_categoria_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar categoría...</option>
                                    <optgroup label="Directos">
                                        @foreach($categorias->where('tipo', 'directo') as $cat)
                                            <option value="{{ $cat->id }}" {{ old('gasto_categoria_id') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->nombre }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Indirectos">
                                        @foreach($categorias->where('tipo', 'indirecto') as $cat)
                                            <option value="{{ $cat->id }}" {{ old('gasto_categoria_id') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->nombre }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('gasto_categoria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Obra --}}
                            <div class="col-md-6">
                                <label for="obra_id" class="form-label">Obra</label>
                                <select name="obra_id" id="obra_id" class="form-select @error('obra_id') is-invalid @enderror">
                                    <option value="">Sin obra (gasto general)</option>
                                    @foreach($obras as $obra)
                                        <option value="{{ $obra->id }}" {{ old('obra_id') == $obra->id ? 'selected' : '' }}>
                                            {{ $obra->codigo }} - {{ $obra->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('obra_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Dejar vacío para gastos indirectos/generales</small>
                            </div>

                            {{-- Concepto --}}
                            <div class="col-12">
                                <label for="concepto" class="form-label">Concepto <span class="text-danger">*</span></label>
                                <input type="text" name="concepto" id="concepto" class="form-control @error('concepto') is-invalid @enderror"
                                       value="{{ old('concepto') }}" required maxlength="255" placeholder="Descripción breve del gasto">
                                @error('concepto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Proveedor --}}
                            <div class="col-md-6">
                                <label for="proveedor" class="form-label">Proveedor</label>
                                <input type="text" name="proveedor" id="proveedor" class="form-control @error('proveedor') is-invalid @enderror"
                                       value="{{ old('proveedor') }}" maxlength="255" placeholder="Nombre del proveedor">
                                @error('proveedor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Fecha --}}
                            <div class="col-md-6">
                                <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror"
                                       value="{{ old('fecha', date('Y-m-d')) }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Descripción --}}
                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                          rows="2" placeholder="Descripción detallada (opcional)">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
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
                                <label for="importe" class="form-label">Base Imponible <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="importe" id="importe" class="form-control @error('importe') is-invalid @enderror"
                                           value="{{ old('importe') }}" required min="0.01" step="0.01" placeholder="0,00">
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

                {{-- Datos adicionales --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Datos Adicionales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Fecha vencimiento --}}
                            <div class="col-md-6">
                                <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                                       value="{{ old('fecha_vencimiento') }}">
                                @error('fecha_vencimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Para alertas de pago pendiente</small>
                            </div>

                            {{-- Forma de pago --}}
                            <div class="col-md-6">
                                <label for="forma_pago" class="form-label">Forma de Pago</label>
                                <select name="forma_pago" id="forma_pago" class="form-select @error('forma_pago') is-invalid @enderror">
                                    <option value="">Seleccionar...</option>
                                    <option value="Transferencia" {{ old('forma_pago') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                    <option value="Tarjeta" {{ old('forma_pago') == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                    <option value="Efectivo" {{ old('forma_pago') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="Cheque" {{ old('forma_pago') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="Domiciliación" {{ old('forma_pago') == 'Domiciliación' ? 'selected' : '' }}>Domiciliación</option>
                                </select>
                                @error('forma_pago')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Documento --}}
                            <div class="col-12">
                                <label for="documento" class="form-label">Documento/Factura</label>
                                <input type="file" name="documento" id="documento" class="form-control @error('documento') is-invalid @enderror"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @error('documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Formatos: PDF, JPG, PNG. Máximo 5MB.</small>
                            </div>

                            {{-- Notas --}}
                            <div class="col-12">
                                <label for="notas" class="form-label">Notas</label>
                                <textarea name="notas" id="notas" class="form-control @error('notas') is-invalid @enderror"
                                          rows="2" placeholder="Notas internas (opcional)">{{ old('notas') }}</textarea>
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
                            Los gastos se clasifican en:
                        </p>
                        <ul class="text-muted small mb-0">
                            <li><strong>Directos:</strong> Asociados a una obra específica</li>
                            <li><strong>Indirectos:</strong> Gastos generales de empresa</li>
                        </ul>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Registrar Gasto
                    </button>
                    <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary">
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
    function calcularTotal() {
        const importe = parseFloat(document.getElementById('importe').value) || 0;
        const ivaPct = parseFloat(document.getElementById('iva_porcentaje').value) || 0;

        const ivaImporte = importe * (ivaPct / 100);
        const total = importe + ivaImporte;

        // Formatear números
        const formatear = (num) => num.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('importe_total').value = formatear(total);
        document.getElementById('displayBase').textContent = formatear(importe) + ' €';
        document.getElementById('displayIvaPct').textContent = ivaPct;
        document.getElementById('displayIva').textContent = formatear(ivaImporte) + ' €';
        document.getElementById('displayTotal').textContent = formatear(total) + ' €';
    }

    document.getElementById('importe').addEventListener('input', calcularTotal);
    document.getElementById('iva_porcentaje').addEventListener('change', calcularTotal);

    // Calcular al cargar
    calcularTotal();
</script>
@endpush
