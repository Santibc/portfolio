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
        @if(session('dup_warning'))
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('dup_warning') }}
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="confirmar_duplicado" value="1" id="confirmar_duplicado" required>
                    <label class="form-check-label fw-bold" for="confirmar_duplicado">Confirmo que NO es un duplicado, guardar de todas formas</label>
                </div>
            </div>
        @endif
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

                {{-- Importes e impuestos --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-currency-euro me-2"></i>Importes e impuestos</h5>
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold">Base imponible e IVA <span class="text-danger">*</span></label>
                        @php
                            $oldBases = old('iva_base', ['']);
                            $oldPcts = old('iva_pct', ['21']);
                        @endphp
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
                                            <option value="{{ $r }}" {{ (string)($oldPcts[$i] ?? '21') === $r ? 'selected' : '' }}>{{ $r }}% IVA</option>
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
                        <small class="d-block text-muted mt-1">Añade líneas si la factura tiene varios IVA (ej. 10% y 21%). Importe negativo = abono/devolución.</small>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label for="irpf_porcentaje" class="form-label">IRPF / Retención (%)</label>
                                <div class="input-group">
                                    <input type="number" name="irpf_porcentaje" id="irpf_porcentaje" class="form-control" step="0.01" min="0" max="100" value="{{ old('irpf_porcentaje', 0) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Para facturas con IRPF (ej. 15%). Deja 0 si no aplica.</small>
                            </div>
                        </div>

                        <div class="bg-light rounded p-3 mt-3">
                            <div class="d-flex justify-content-between"><span>Base imponible:</span><span id="displayBase">0,00 €</span></div>
                            <div class="d-flex justify-content-between"><span>IVA:</span><span id="displayIva">0,00 €</span></div>
                            <div class="d-flex justify-content-between text-danger"><span>IRPF:</span><span id="displayIrpf">-0,00 €</span></div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold"><span>TOTAL:</span><span id="displayTotal">0,00 €</span></div>
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
                                <input type="file" name="documento" id="documento" class="form-control @error('documento') is-invalid @enderror">
                                @error('documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Máximo 5MB.</small>
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
    (function() {
        const cont = document.getElementById('ivaLineas');
        const fmt = (n) => n.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function recalc() {
            let base = 0, iva = 0;
            cont.querySelectorAll('.iva-linea').forEach(row => {
                const b = parseFloat(row.querySelector('.iva-base').value) || 0;
                const p = parseFloat(row.querySelector('.iva-pct').value) || 0;
                const imp = b * p / 100;
                base += b; iva += imp;
                row.querySelector('.iva-importe-disp').value = fmt(imp);
            });
            const irpfPct = parseFloat(document.getElementById('irpf_porcentaje').value) || 0;
            const irpf = base * irpfPct / 100;
            document.getElementById('displayBase').textContent = fmt(base) + ' €';
            document.getElementById('displayIva').textContent = fmt(iva) + ' €';
            document.getElementById('displayIrpf').textContent = '-' + fmt(irpf) + ' €';
            document.getElementById('displayTotal').textContent = fmt(base + iva - irpf) + ' €';
        }

        document.getElementById('btnAddIva').addEventListener('click', function() {
            const row = cont.querySelector('.iva-linea').cloneNode(true);
            row.querySelector('.iva-base').value = '';
            row.querySelector('.iva-importe-disp').value = '0,00';
            cont.appendChild(row);
        });
        cont.addEventListener('input', recalc);
        cont.addEventListener('change', recalc);
        document.getElementById('irpf_porcentaje').addEventListener('input', recalc);
        cont.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-quitar-iva');
            if (btn && cont.querySelectorAll('.iva-linea').length > 1) {
                btn.closest('.iva-linea').remove();
                recalc();
            }
        });
        recalc();
    })();
</script>
@endpush
