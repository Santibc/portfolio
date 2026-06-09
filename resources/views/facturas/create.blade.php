@extends('layouts.app')

@section('title', 'Nueva Factura')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nueva Factura</h1>
            <p class="text-muted mb-0">Crear factura en borrador</p>
        </div>
        <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary">
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

    <form action="{{ route('facturas.store') }}" method="POST" id="formFactura">
        @csrf

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
                                                {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
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
                                                {{ old('obra_id') == $obra->id ? 'selected' : '' }}>
                                            {{ $obra->codigo }} - {{ $obra->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('obra_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label for="serie" class="form-label">Serie</label>
                                <input type="text" name="serie" id="serie" class="form-control @error('serie') is-invalid @enderror" value="{{ old('serie', 'F') }}" maxlength="20">
                                @error('serie')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="numero" class="form-label">Número (opcional)</label>
                                <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror" value="{{ old('numero') }}" maxlength="50" placeholder="Ej: F-2026-00001">
                                <small class="text-muted">Déjalo vacío para numeración automática al emitir.</small>
                                @error('numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_emision" class="form-label">Fecha Emisión <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_emision" id="fecha_emision"
                                       class="form-control @error('fecha_emision') is-invalid @enderror"
                                       value="{{ old('fecha_emision', date('Y-m-d')) }}" required>
                                @error('fecha_emision')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="fecha_vencimiento" class="form-label">Fecha Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                                       class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                                       value="{{ old('fecha_vencimiento') }}">
                                @error('fecha_vencimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label for="iva_porcentaje" class="form-label">IVA %</label>
                                <input type="number" name="iva_porcentaje" id="iva_porcentaje"
                                       class="form-control @error('iva_porcentaje') is-invalid @enderror"
                                       value="{{ old('iva_porcentaje', 21) }}" step="0.01" min="0" max="100">
                                @error('iva_porcentaje')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label for="retencion_porcentaje" class="form-label">Retención %</label>
                                <input type="number" name="retencion_porcentaje" id="retencion_porcentaje"
                                       class="form-control @error('retencion_porcentaje') is-invalid @enderror"
                                       value="{{ old('retencion_porcentaje', 0) }}" step="0.01" min="0" max="100">
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
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-success me-1" onclick="agregarGrupo()">
                                <i class="bi bi-folder-plus me-1"></i>Añadir Grupo
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="agregarLinea()">
                                <i class="bi bi-plus-lg me-1"></i>Añadir Línea
                            </button>
                        </div>
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
                                    {{-- Las líneas se agregan dinámicamente --}}
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center py-4" id="sinLineas">
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
                                  placeholder="Notas o condiciones adicionales...">{{ old('notas') }}</textarea>
                    </div>
                </div>

                {{-- Pie de página PDF --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-file-pdf me-2"></i>Pie de Página PDF
                            <small class="text-muted" data-bs-toggle="tooltip" title="Este texto aparecerá en el pie de página del PDF generado">
                                <i class="bi bi-info-circle"></i>
                            </small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <textarea name="footer_text" id="footer_text"
                                      class="form-control @error('footer_text') is-invalid @enderror"
                                      rows="2"
                                      maxlength="1000"
                                      placeholder="Texto del pie de página para el PDF...">{{ old('footer_text', 'MANZER AGROFORESTAL, S.R.L.U. | CIF: B12345678 | Inscrita en el Registro Mercantil de Barcelona') }}</textarea>
                            @error('footer_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Este texto aparecerá en el pie de todas las páginas del PDF</small>
                            <small class="text-muted">
                                <span id="charCount">{{ strlen(old('footer_text', 'MANZER AGROFORESTAL, S.R.L.U. | CIF: B12345678 | Inscrita en el Registro Mercantil de Barcelona')) }}</span>/1000 caracteres
                            </small>
                        </div>
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
                        <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Información</h6>
                        <ul class="small text-muted mb-0">
                            <li>La factura se creará como <strong>borrador</strong></li>
                            <li>El número se genera al <strong>emitir</strong></li>
                            <li>Puedes editar mientras esté en borrador</li>
                        </ul>
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save me-2"></i>Guardar Borrador
                    </button>
                    <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary">
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
    let grupoIndex = 0;

    // Agregar primer grupo al cargar
    document.addEventListener('DOMContentLoaded', function() {
        agregarGrupo();

        // Evento para cambio de cliente
        document.getElementById('cliente_id').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const retencion = option.dataset.retencion || 0;
            document.getElementById('retencion_porcentaje').value = retencion;
            calcularTotales();
        });

        // Evento para cambio de obra - autoseleccionar cliente
        document.getElementById('obra_id').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const clienteId = option.dataset.cliente;
            if (clienteId) {
                document.getElementById('cliente_id').value = clienteId;
                document.getElementById('cliente_id').dispatchEvent(new Event('change'));
            }
        });

        // Eventos para recalcular
        document.getElementById('iva_porcentaje').addEventListener('input', calcularTotales);
        document.getElementById('retencion_porcentaje').addEventListener('input', calcularTotales);
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ====== GRUPOS ======

    function agregarGrupo(nombreGrupo = '', autoAddLine = true) {
        const tbody = document.getElementById('lineasBody');
        document.getElementById('sinLineas').style.display = 'none';

        const gId = 'grupo-' + grupoIndex;
        grupoIndex++;

        const tr = document.createElement('tr');
        tr.className = 'grupo-header';
        tr.dataset.grupoId = gId;
        tr.style.backgroundColor = '#e8f5e9';
        tr.innerHTML = `
            <td colspan="5" style="padding: 8px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-folder-fill text-success me-2"></i>
                    <input type="text" class="form-control form-control-sm grupo-nombre fw-bold"
                           placeholder="Nombre del grupo..."
                           value="${escapeHtml(nombreGrupo)}"
                           onchange="actualizarNombreGrupo(this)"
                           onkeyup="actualizarNombreGrupo(this)"
                           style="max-width: 400px;">
                </div>
            </td>
            <td class="text-end fw-bold grupo-subtotal" style="padding: 8px; white-space: nowrap;">
                0,00 €
            </td>
            <td class="text-center" style="padding: 8px;">
                <button type="button" class="btn btn-sm btn-outline-success me-1"
                        onclick="agregarLineaEnGrupo(this)" title="Añadir línea al grupo">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger"
                        onclick="eliminarGrupo(this)" title="Eliminar grupo completo">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        if (autoAddLine) {
            agregarLineaEnGrupo(tr.querySelector('.btn-outline-success'));
        }
    }

    function agregarLineaEnGrupo(btn) {
        const grupoHeaderTr = btn.closest('tr.grupo-header');
        const gId = grupoHeaderTr.dataset.grupoId;
        const grupoNombre = grupoHeaderTr.querySelector('.grupo-nombre').value;

        // Encontrar la última línea hija de este grupo
        let insertAfter = grupoHeaderTr;
        let sibling = grupoHeaderTr.nextElementSibling;
        while (sibling && sibling.dataset.grupo === gId) {
            insertAfter = sibling;
            sibling = sibling.nextElementSibling;
        }

        const tr = document.createElement('tr');
        tr.className = 'linea-factura';
        tr.dataset.index = lineaIndex;
        tr.dataset.grupo = gId;
        tr.style.backgroundColor = '#f8fdf8';
        tr.innerHTML = `
            <td style="padding-left: 30px;">
                <input type="hidden" name="lineas[${lineaIndex}][grupo]" class="grupo-value" value="${escapeHtml(grupoNombre)}">
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

        insertAfter.after(tr);
        lineaIndex++;
        calcularTotales();
    }

    function actualizarNombreGrupo(input) {
        const grupoHeaderTr = input.closest('tr.grupo-header');
        const gId = grupoHeaderTr.dataset.grupoId;
        const nuevoNombre = input.value;

        let sibling = grupoHeaderTr.nextElementSibling;
        while (sibling && sibling.dataset.grupo === gId) {
            const hiddenInput = sibling.querySelector('.grupo-value');
            if (hiddenInput) {
                hiddenInput.value = nuevoNombre;
            }
            sibling = sibling.nextElementSibling;
        }
    }

    function eliminarGrupo(btn) {
        const grupoHeaderTr = btn.closest('tr.grupo-header');
        const gId = grupoHeaderTr.dataset.grupoId;

        // Eliminar todas las líneas hijas
        let sibling = grupoHeaderTr.nextElementSibling;
        while (sibling && sibling.dataset.grupo === gId) {
            const next = sibling.nextElementSibling;
            sibling.remove();
            sibling = next;
        }

        grupoHeaderTr.remove();

        const lineas = document.querySelectorAll('.linea-factura');
        const grupos = document.querySelectorAll('.grupo-header');
        if (lineas.length === 0 && grupos.length === 0) {
            document.getElementById('sinLineas').style.display = 'block';
        }

        calcularTotales();
    }

    // ====== LÍNEAS ======

    function agregarLinea() {
        const tbody = document.getElementById('lineasBody');
        const sinLineas = document.getElementById('sinLineas');
        sinLineas.style.display = 'none';

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
        const gId = tr.dataset.grupo;
        tr.remove();

        // Si la línea pertenecía a un grupo, verificar si el grupo queda vacío
        if (gId) {
            const grupoHeader = document.querySelector(`tr.grupo-header[data-grupo-id="${gId}"]`);
            if (grupoHeader) {
                const remaining = document.querySelectorAll(`tr.linea-factura[data-grupo="${gId}"]`);
                if (remaining.length === 0) {
                    grupoHeader.remove();
                }
            }
        }

        const lineas = document.querySelectorAll('.linea-factura');
        const grupos = document.querySelectorAll('.grupo-header');
        if (lineas.length === 0 && grupos.length === 0) {
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

        // Actualizar subtotales de grupos
        document.querySelectorAll('.grupo-header').forEach(grupoTr => {
            const gId = grupoTr.dataset.grupoId;
            let subtotal = 0;
            document.querySelectorAll(`tr.linea-factura[data-grupo="${gId}"]`).forEach(tr => {
                const cantidad = parseFloat(tr.querySelector('.cantidad').value) || 0;
                const precio = parseFloat(tr.querySelector('.precio').value) || 0;
                const descuento = parseFloat(tr.querySelector('.descuento').value) || 0;
                subtotal += (cantidad * precio) * (1 - descuento / 100);
            });
            grupoTr.querySelector('.grupo-subtotal').textContent = formatearMoneda(subtotal);
        });

        const ivaPct = parseFloat(document.getElementById('iva_porcentaje').value) || 0;
        const retencionPct = parseFloat(document.getElementById('retencion_porcentaje').value) || 0;

        const ivaImporte = baseImponible * (ivaPct / 100);
        const retencionImporte = baseImponible * (retencionPct / 100);
        const total = baseImponible + ivaImporte - retencionImporte;

        // Actualizar display
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
            return;
        }

        // Validar que todos los grupos tengan nombre
        let grupoSinNombre = false;
        document.querySelectorAll('.grupo-header').forEach(grupoTr => {
            const nombre = grupoTr.querySelector('.grupo-nombre').value.trim();
            if (!nombre) {
                grupoSinNombre = true;
            }
        });

        if (grupoSinNombre) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Grupo sin nombre',
                text: 'Todos los grupos deben tener un nombre.',
            });
        }
    });

    // Character counter for footer text
    document.getElementById('footer_text').addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length;
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endpush
