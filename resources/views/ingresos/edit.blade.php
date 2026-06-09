@extends('layouts.app')

@section('title', 'Editar Ingreso')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Ingreso</h1>
            <p class="text-muted mb-0">{{ $ingreso->concepto }}</p>
        </div>
        <div>
            @if($ingreso->estado == 'pendiente')
                <span class="badge bg-warning text-dark fs-6 me-2">Pendiente</span>
            @else
                <span class="badge bg-success fs-6 me-2">Cobrado</span>
            @endif
            <a href="{{ route('ingresos.show', $ingreso) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
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

    <form action="{{ route('ingresos.update', $ingreso) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            {{-- Formulario principal --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información del Ingreso</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Obra --}}
                            <div class="col-md-6">
                                <label for="obra_id" class="form-label">Obra <span class="text-danger">*</span></label>
                                <select name="obra_id" id="obra_id" class="form-select @error('obra_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar obra...</option>
                                    @foreach($obras as $obra)
                                        <option value="{{ $obra->id }}"
                                                data-cliente-id="{{ $obra->cliente_id }}"
                                                data-cliente-nombre="{{ $obra->cliente?->nombre_comercial }}"
                                                data-retencion="{{ $obra->cliente?->retencion_porcentaje ?? 0 }}"
                                                {{ old('obra_id', $ingreso->obra_id) == $obra->id ? 'selected' : '' }}>
                                            {{ $obra->codigo }} - {{ $obra->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('obra_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Cliente --}}
                            <div class="col-md-6">
                                <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}"
                                                data-retencion="{{ $cliente->retencion_porcentaje ?? 0 }}"
                                                {{ old('cliente_id', $ingreso->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nombre_comercial }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Se auto-selecciona al elegir obra</small>
                            </div>

                            {{-- Concepto --}}
                            <div class="col-12">
                                <label for="concepto" class="form-label">Concepto <span class="text-danger">*</span></label>
                                <input type="text" name="concepto" id="concepto" class="form-control @error('concepto') is-invalid @enderror"
                                       value="{{ old('concepto', $ingreso->concepto) }}" required maxlength="255" placeholder="Descripción breve del ingreso">
                                @error('concepto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Fecha --}}
                            <div class="col-md-6">
                                <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror"
                                       value="{{ old('fecha', $ingreso->fecha->format('Y-m-d')) }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Fecha prevista cobro --}}
                            <div class="col-md-6">
                                <label for="fecha_prevista_cobro" class="form-label">Fecha Prevista de Cobro</label>
                                <input type="date" name="fecha_prevista_cobro" id="fecha_prevista_cobro" class="form-control @error('fecha_prevista_cobro') is-invalid @enderror"
                                       value="{{ old('fecha_prevista_cobro', $ingreso->fecha_prevista_cobro?->format('Y-m-d')) }}">
                                @error('fecha_prevista_cobro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Descripción --}}
                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                          rows="2" placeholder="Descripción detallada (opcional)">{{ old('descripcion', $ingreso->descripcion) }}</textarea>
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
                        @php
                            $desglose = $ingreso->desglose_iva ?: [['base' => $ingreso->importe, 'porcentaje' => $ingreso->iva_porcentaje]];
                            $oldBases = old('iva_base', array_map(fn($l) => $l['base'], $desglose));
                            $oldPcts = old('iva_pct', array_map(fn($l) => $l['porcentaje'], $desglose));
                        @endphp
                        <label class="form-label fw-semibold">Base imponible e IVA <span class="text-danger">*</span></label>
                        <div id="ivaLineas">
                            @foreach($oldBases as $i => $b)
                            <div class="row g-2 mb-2 iva-linea align-items-center">
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input type="number" name="iva_base[]" class="form-control iva-base" step="0.01" placeholder="Base imponible" value="{{ $b }}">
                                        <span class="input-group-text">€</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="iva_pct[]" class="form-select iva-pct">
                                        @foreach(['0','4','10','21'] as $r)
                                            <option value="{{ $r }}" {{ (float)($oldPcts[$i] ?? 21) == (float)$r ? 'selected' : '' }}>{{ $r }}% IVA</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control iva-importe-disp bg-light" readonly value="0,00">
                                        <span class="input-group-text">€</span>
                                    </div>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-quitar-iva" title="Quitar"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" id="btnAddIva" class="btn btn-sm btn-outline-primary mt-1">
                            <i class="bi bi-plus-lg me-1"></i>Añadir otro tipo de IVA
                        </button>
                        <small class="d-block text-muted mt-1">Añade líneas si la factura tiene varios IVA (ej. 10% y 21%). Importe negativo = abono.</small>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label for="retencion_porcentaje" class="form-label">Retención / IRPF (%)</label>
                                <div class="input-group">
                                    <input type="number" name="retencion_porcentaje" id="retencion_porcentaje" class="form-control @error('retencion_porcentaje') is-invalid @enderror"
                                           value="{{ old('retencion_porcentaje', $ingreso->retencion_porcentaje) }}" min="0" max="100" step="0.01">
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('retencion_porcentaje')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Se carga automático del cliente</small>
                            </div>
                        </div>

                        <div class="bg-light rounded p-3 mt-3">
                            <div class="d-flex justify-content-between"><span>Base imponible:</span><span id="displayBase">0,00 €</span></div>
                            <div class="d-flex justify-content-between text-success"><span>+ IVA:</span><span id="displayIva">0,00 €</span></div>
                            <div class="d-flex justify-content-between text-danger"><span>- Retención:</span><span id="displayRetencion">0,00 €</span></div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold fs-5"><span>TOTAL A COBRAR:</span><span id="displayTotal" class="text-success">0,00 €</span></div>
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
                            {{-- Forma de pago --}}
                            <div class="col-md-6">
                                <label for="forma_pago" class="form-label">Forma de Pago</label>
                                <select name="forma_pago" id="forma_pago" class="form-select @error('forma_pago') is-invalid @enderror">
                                    <option value="">Seleccionar...</option>
                                    <option value="Transferencia" {{ old('forma_pago', $ingreso->forma_pago) == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                    <option value="Cheque" {{ old('forma_pago', $ingreso->forma_pago) == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="Efectivo" {{ old('forma_pago', $ingreso->forma_pago) == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="Pagaré" {{ old('forma_pago', $ingreso->forma_pago) == 'Pagaré' ? 'selected' : '' }}>Pagaré</option>
                                </select>
                                @error('forma_pago')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Notas --}}
                            <div class="col-12">
                                <label for="notas" class="form-label">Notas</label>
                                <textarea name="notas" id="notas" class="form-control @error('notas') is-invalid @enderror"
                                          rows="2" placeholder="Notas internas (opcional)">{{ old('notas', $ingreso->notas) }}</textarea>
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
                            Los ingresos se asocian a una obra y cliente.
                        </p>
                        <ul class="text-muted small mb-0">
                            <li>Al seleccionar una obra, se carga automáticamente el cliente</li>
                            <li>La retención se toma del porcentaje configurado en el cliente</li>
                            <li>El total = Base + IVA - Retención</li>
                        </ul>
                    </div>
                </div>

                {{-- Información del registro --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Registro</h5>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Creado:</span>
                                <span>{{ $ingreso->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Actualizado:</span>
                                <span>{{ $ingreso->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($ingreso->fecha_cobro)
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Fecha cobro:</span>
                                <span>{{ $ingreso->fecha_cobro->format('d/m/Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                    </button>
                    <a href="{{ route('ingresos.show', $ingreso) }}" class="btn btn-outline-secondary">
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
    // Auto-seleccionar cliente cuando se elige obra
    document.getElementById('obra_id').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            const clienteId = option.dataset.clienteId;
            const retencion = option.dataset.retencion || 0;

            document.getElementById('cliente_id').value = clienteId;
            document.getElementById('retencion_porcentaje').value = retencion;
            calcularTotal();
        }
    });

    // Actualizar retención cuando se cambia cliente manualmente
    document.getElementById('cliente_id').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            const retencion = option.dataset.retencion || 0;
            document.getElementById('retencion_porcentaje').value = retencion;
            calcularTotal();
        }
    });

    const ivaCont = document.getElementById('ivaLineas');
    const fmtEs = (n) => n.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function calcularTotal() {
        let base = 0, iva = 0;
        ivaCont.querySelectorAll('.iva-linea').forEach(row => {
            const b = parseFloat(row.querySelector('.iva-base').value) || 0;
            const p = parseFloat(row.querySelector('.iva-pct').value) || 0;
            const imp = b * p / 100;
            base += b; iva += imp;
            row.querySelector('.iva-importe-disp').value = fmtEs(imp);
        });
        const retPct = parseFloat(document.getElementById('retencion_porcentaje').value) || 0;
        const retencion = base * retPct / 100;
        document.getElementById('displayBase').textContent = fmtEs(base) + ' €';
        document.getElementById('displayIva').textContent = fmtEs(iva) + ' €';
        document.getElementById('displayRetencion').textContent = '-' + fmtEs(retencion) + ' €';
        document.getElementById('displayTotal').textContent = fmtEs(base + iva - retencion) + ' €';
    }

    document.getElementById('btnAddIva').addEventListener('click', function() {
        const row = ivaCont.querySelector('.iva-linea').cloneNode(true);
        row.querySelector('.iva-base').value = '';
        row.querySelector('.iva-importe-disp').value = '0,00';
        ivaCont.appendChild(row);
    });
    ivaCont.addEventListener('input', calcularTotal);
    ivaCont.addEventListener('change', calcularTotal);
    ivaCont.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-quitar-iva');
        if (btn && ivaCont.querySelectorAll('.iva-linea').length > 1) {
            btn.closest('.iva-linea').remove();
            calcularTotal();
        }
    });
    document.getElementById('retencion_porcentaje').addEventListener('input', calcularTotal);

    // Calcular al cargar
    calcularTotal();
</script>
@endpush
