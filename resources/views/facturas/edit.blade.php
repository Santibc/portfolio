@extends('layouts.app')

@section('title', 'Editar Factura')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Factura</h1>
            <p class="text-muted mb-0">Borrador #{{ $factura->id }}</p>
        </div>
        <a href="{{ route('facturas.show', $factura) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    {{-- Errores de validación --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('facturas.update', $factura) }}" method="POST" id="formFactura">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Columna principal --}}
            <div class="col-lg-8">
                {{-- Datos generales --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Datos de la Factura</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}"
                                                data-retencion="{{ $cliente->retencion_porcentaje ?? 0 }}"
                                                {{ old('cliente_id', $factura->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nombre_comercial }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="obra_id" class="form-label">Obra (opcional)</label>
                                <select name="obra_id" id="obra_id" class="form-select @error('obra_id') is-invalid @enderror">
                                    <option value="">Sin obra asociada</option>
                                    @foreach($obras as $obra)
                                        <option value="{{ $obra->id }}"
                                                data-cliente="{{ $obra->cliente_id }}"
                                                {{ old('obra_id', $factura->obra_id) == $obra->id ? 'selected' : '' }}>
                                            {{ $obra->codigo }} - {{ $obra->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('obra_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="fecha_emision" class="form-label">Fecha Emisión <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_emision" id="fecha_emision"
                                       class="form-control @error('fecha_emision') is-invalid @enderror"
                                       value="{{ old('fecha_emision', $factura->fecha_emision->format('Y-m-d')) }}" required>
                                @error('fecha_emision')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="fecha_vencimiento" class="form-label">Fecha Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                                       class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                                       value="{{ old('fecha_vencimiento', $factura->fecha_vencimiento?->format('Y-m-d')) }}">
                                @error('fecha_vencimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label for="iva_porcentaje" class="form-label">IVA %</label>
                                <input type="number" name="iva_porcentaje" id="iva_porcentaje"
                                       class="form-control @error('iva_porcentaje') is-invalid @enderror"
                                       value="{{ old('iva_porcentaje', $factura->iva_porcentaje) }}" step="0.01" min="0" max="100">
                                @error('iva_porcentaje')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label for="retencion_porcentaje" class="form-label">Retención %</label>
                                <input type="number" name="retencion_porcentaje" id="retencion_porcentaje"
                                       class="form-control @error('retencion_porcentaje') is-invalid @enderror"
                                       value="{{ old('retencion_porcentaje', $factura->retencion_porcentaje) }}" step="0.01" min="0" max="100">
                                @error('retencion_porcentaje')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Líneas de factura --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-list-ol me-2"></i>Líneas de Factura</h5>
                        <button type="button" class="btn btn-sm btn-primary" onclick="agregarLinea()">
                            <i class="bi bi-plus-lg me-1"></i>Añadir Línea
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tablaLineas">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%">Concepto</th>
                                        <th style="width: 25%">Descripción</th>
                                        <th style="width: 10%" class="text-end">Cantidad</th>
                                        <th style="width: 12%" class="text-end">Precio Unit.</th>
                                        <th style="width: 8%" class="text-end">Dto %</th>
                                        <th style="width: 12%" class="text-end">Importe</th>
                                        <th style="width: 8%"></th>
                                    </tr>
                                </thead>
                                <tbody id="lineasBody">
                                    {{-- Las líneas existentes se cargan abajo --}}
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center py-4" id="sinLineas" style="display: none;">
                            <p class="text-muted mb-2">No hay líneas en la factura</p>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="agregarLinea()">
                                <i class="bi bi-plus-lg me-1"></i>Añadir primera línea
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Notas --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Notas</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="notas" id="notas" class="form-control" rows="3"
                                  placeholder="Notas o condiciones adicionales...">{{ old('notas', $factura->notas) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Columna lateral --}}
            <div class="col-lg-4">
                {{-- Resumen de totales --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Totales</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Base Imponible:</span>
                            <span class="fw-semibold" id="displayBase">0,00 €</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>IVA (<span id="displayIvaPct">21</span>%):</span>
                            <span id="displayIva">0,00 €</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Retención (<span id="displayRetencionPct">0</span>%):</span>
                            <span class="text-danger" id="displayRetencion">-0,00 €</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fs-5 fw-bold">TOTAL:</span>
                            <span class="fs-5 fw-bold text-primary" id="displayTotal">0,00 €</span>
                        </div>
                    </div>
                </div>

                {{-- Información --}}
                <div class="card border-0 shadow-sm bg-light mb-4">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Estado: Borrador</h6>
                        <p class="small text-muted mb-0">
                            Puedes editar todos los campos mientras la factura esté en borrador.
                            Una vez emitida, no se podrá modificar.
                        </p>
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save me-2"></i>Guardar Cambios
                    </button>
                    <button type="button" class="btn btn-success" onclick="emitirFactura()">
                        <i class="bi bi-send me-2"></i>Guardar y Emitir
                    </button>
                    <a href="{{ route('facturas.show', $factura) }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let lineaIndex = 0;

    // Cargar líneas existentes al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        const lineasExistentes = @json($factura->lineas);

        lineasExistentes.forEach(linea => {
            agregarLineaConDatos(linea);
        });

        if (lineasExistentes.length === 0) {
            agregarLinea();
        }

        calcularTotales();

        // Eventos para recalcular
        document.getElementById('iva_porcentaje').addEventListener('input', calcularTotales);
        document.getElementById('retencion_porcentaje').addEventListener('input', calcularTotales);
    });

    function agregarLineaConDatos(linea) {
        const tbody = document.getElementById('lineasBody');
        document.getElementById('sinLineas').style.display = 'none';

        const importe = (linea.cantidad * linea.precio_unitario) * (1 - linea.descuento_porcentaje / 100);

        const tr = document.createElement('tr');
        tr.className = 'linea-factura';
        tr.dataset.index = lineaIndex;
        tr.innerHTML = `
            <td>
                <input type="text" name="lineas[${lineaIndex}][concepto]" class="form-control form-control-sm" value="${linea.concepto || ''}" required>
            </td>
            <td>
                <input type="text" name="lineas[${lineaIndex}][descripcion]" class="form-control form-control-sm" value="${linea.descripcion || ''}">
            </td>
            <td>
                <input type="number" name="lineas[${lineaIndex}][cantidad]" class="form-control form-control-sm text-end cantidad" value="${linea.cantidad}" step="0.01" min="0.01" required onchange="calcularLinea(this)" onkeyup="calcularLinea(this)">
            </td>
            <td>
                <input type="number" name="lineas[${lineaIndex}][precio_unitario]" class="form-control form-control-sm text-end precio" value="${linea.precio_unitario}" step="0.01" min="0" required onchange="calcularLinea(this)" onkeyup="calcularLinea(this)">
            </td>
            <td>
                <input type="number" name="lineas[${lineaIndex}][descuento_porcentaje]" class="form-control form-control-sm text-end descuento" value="${linea.descuento_porcentaje || 0}" step="0.01" min="0" max="100" onchange="calcularLinea(this)" onkeyup="calcularLinea(this)">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-end importe" value="${formatearMoneda(importe)}" readonly disabled>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarLinea(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        lineaIndex++;
    }

    function agregarLinea() {
        const tbody = document.getElementById('lineasBody');
        document.getElementById('sinLineas').style.display = 'none';

        const tr = document.createElement('tr');
        tr.className = 'linea-factura';
        tr.dataset.index = lineaIndex;
        tr.innerHTML = `
            <td>
                <input type="text" name="lineas[${lineaIndex}][concepto]" class="form-control form-control-sm" placeholder="Concepto..." required>
            </td>
            <td>
                <input type="text" name="lineas[${lineaIndex}][descripcion]" class="form-control form-control-sm" placeholder="Descripción...">
            </td>
            <td>
                <input type="number" name="lineas[${lineaIndex}][cantidad]" class="form-control form-control-sm text-end cantidad" value="1" step="0.01" min="0.01" required onchange="calcularLinea(this)" onkeyup="calcularLinea(this)">
            </td>
            <td>
                <input type="number" name="lineas[${lineaIndex}][precio_unitario]" class="form-control form-control-sm text-end precio" value="0" step="0.01" min="0" required onchange="calcularLinea(this)" onkeyup="calcularLinea(this)">
            </td>
            <td>
                <input type="number" name="lineas[${lineaIndex}][descuento_porcentaje]" class="form-control form-control-sm text-end descuento" value="0" step="0.01" min="0" max="100" onchange="calcularLinea(this)" onkeyup="calcularLinea(this)">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-end importe" value="0,00 €" readonly disabled>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarLinea(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        lineaIndex++;
    }

    function eliminarLinea(btn) {
        const tr = btn.closest('tr');
        tr.remove();

        const lineas = document.querySelectorAll('.linea-factura');
        if (lineas.length === 0) {
            document.getElementById('sinLineas').style.display = 'block';
        }

        calcularTotales();
    }

    function calcularLinea(input) {
        const tr = input.closest('tr');
        const cantidad = parseFloat(tr.querySelector('.cantidad').value) || 0;
        const precio = parseFloat(tr.querySelector('.precio').value) || 0;
        const descuento = parseFloat(tr.querySelector('.descuento').value) || 0;

        const importe = (cantidad * precio) * (1 - descuento / 100);
        tr.querySelector('.importe').value = formatearMoneda(importe);

        calcularTotales();
    }

    function calcularTotales() {
        let baseImponible = 0;

        document.querySelectorAll('.linea-factura').forEach(tr => {
            const cantidad = parseFloat(tr.querySelector('.cantidad').value) || 0;
            const precio = parseFloat(tr.querySelector('.precio').value) || 0;
            const descuento = parseFloat(tr.querySelector('.descuento').value) || 0;
            const importe = (cantidad * precio) * (1 - descuento / 100);
            baseImponible += importe;
        });

        const ivaPct = parseFloat(document.getElementById('iva_porcentaje').value) || 0;
        const retencionPct = parseFloat(document.getElementById('retencion_porcentaje').value) || 0;

        const ivaImporte = baseImponible * (ivaPct / 100);
        const retencionImporte = baseImponible * (retencionPct / 100);
        const total = baseImponible + ivaImporte - retencionImporte;

        document.getElementById('displayBase').textContent = formatearMoneda(baseImponible);
        document.getElementById('displayIvaPct').textContent = ivaPct;
        document.getElementById('displayIva').textContent = formatearMoneda(ivaImporte);
        document.getElementById('displayRetencionPct').textContent = retencionPct;
        document.getElementById('displayRetencion').textContent = '-' + formatearMoneda(retencionImporte);
        document.getElementById('displayTotal').textContent = formatearMoneda(total);
    }

    function formatearMoneda(valor) {
        return valor.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    }

    function emitirFactura() {
        Swal.fire({
            title: '¿Guardar y emitir?',
            text: 'Se guardarán los cambios y se emitirá la factura. Una vez emitida no se podrá editar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, emitir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754',
        }).then((result) => {
            if (result.isConfirmed) {
                // Primero guardar, luego emitir
                const form = document.getElementById('formFactura');
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                }).then(response => {
                    if (response.redirected) {
                        // Ahora emitir
                        return fetch(`{{ url('facturas') }}/{{ $factura->id }}/emitir`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        });
                    }
                    return response;
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Factura emitida',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = `{{ url('facturas') }}/{{ $factura->id }}`;
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Error al emitir', 'error');
                    }
                }).catch(() => {
                    // Si falla AJAX, enviar formulario normal
                    form.submit();
                });
            }
        });
    }

    // Validación antes de enviar
    document.getElementById('formFactura').addEventListener('submit', function(e) {
        const lineas = document.querySelectorAll('.linea-factura');
        if (lineas.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Sin líneas',
                text: 'Debe añadir al menos una línea a la factura.',
            });
        }
    });
</script>
@endpush
