<x-app-layout>
    @section('title', 'Nueva Venta')

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .pos-container { min-height: calc(100vh - 120px); }
        .pos-search-input { font-size: 1.1rem; padding: .75rem 1rem; }
        .pos-table th { font-size: .85rem; text-transform: uppercase; letter-spacing: .5px; }
        .pos-table td { vertical-align: middle; }
        .pos-table .qty-input { width: 70px; text-align: center; }
        .pos-table .price-input { width: 100px; text-align: right; }
        .pos-table .disc-input { width: 80px; text-align: center; }
        .pos-totals { background: #f8f9fa; border-radius: .5rem; }
        .pos-totals .total-final { font-size: 2rem; font-weight: 700; color: var(--miracle-pink); }
        .search-results { position: absolute; z-index: 1050; width: 100%; max-height: 300px; overflow-y: auto; box-shadow: 0 4px 15px rgba(0,0,0,.15); border: 1px solid #dee2e6; border-top: none; }
        .search-result-item { cursor: pointer; transition: background .15s; }
        .search-result-item:hover { background: var(--miracle-pink-light) !important; }
        .stock-badge-ok { background: #d4edda; color: #155724; }
        .stock-badge-low { background: #fff3cd; color: #856404; }
        .stock-badge-zero { background: #f8d7da; color: #721c24; }
        .variant-chip { cursor: pointer; border: 2px solid #dee2e6; transition: all .2s; }
        .variant-chip:hover, .variant-chip.selected { border-color: var(--miracle-pink); background: var(--miracle-pink-light); }
        .client-card { border-left: 4px solid var(--miracle-lilac); }
        .prefactura-badge { position: fixed; bottom: 20px; right: 20px; z-index: 1040; }
        .btn-miracle { background: var(--miracle-pink); color: white; border: none; }
        .btn-miracle:hover { background: var(--miracle-pink-hover); color: white; }
    </style>
    @endpush

    <div class="container-fluid py-3 pos-container">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('pdv.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h5 class="fw-bold mb-0"><i class="bi bi-cart-plus me-2"></i>Nueva Venta</h5>
                @if($sesion)
                    <span class="badge bg-success"><i class="bi bi-unlock me-1"></i>{{ $sesion->caja->nombre }}</span>
                @endif
                @if(($siigoModoTest ?? false) && ($siigoActivo ?? false))
                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>SIIGO MODO PRUEBA</span>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('pdv.prefacturas.pendientes') }}" class="btn btn-outline-warning btn-sm position-relative" id="btnPrefacturas">
                    <i class="bi bi-receipt me-1"></i>Prefacturas
                    <span class="badge bg-danger rounded-pill ms-1 d-none" id="prefacturasCount">0</span>
                </a>
                <button class="btn btn-outline-secondary btn-sm" onclick="limpiarVenta()">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
                </button>
            </div>
        </div>

        @if(isset($prefactura) && $prefactura)
        <div class="alert alert-info d-flex align-items-center justify-content-between mb-3" role="alert">
            <div>
                <i class="bi bi-receipt me-2"></i>
                Editando Prefactura <strong>{{ $prefactura->numero_prefactura }}</strong>
                — Creada por <strong>{{ $prefactura->usuarioCreador->name ?? '-' }}</strong>
                <small class="text-muted ms-2">({{ $prefactura->created_at->diffForHumans() }})</small>
            </div>
            <a href="{{ route('pdv.prefacturas.pendientes') }}" class="btn btn-sm btn-outline-info">
                <i class="bi bi-arrow-left me-1"></i>Volver a Prefacturas
            </a>
        </div>
        @endif

        <div class="row g-3">
            {{-- LEFT: Products Table --}}
            <div class="col-lg-8">
                {{-- Search + Client Row --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-7 position-relative">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscarProducto" class="form-control pos-search-input"
                                   placeholder="Buscar producto por nombre o referencia..." autocomplete="off">
                        </div>
                        <div id="resultadosBusqueda" class="search-results bg-white rounded-bottom d-none"></div>
                    </div>
                    <div class="col-md-5">
                        <div class="position-relative">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                                <input type="text" id="buscarCliente" class="form-control"
                                       placeholder="CC, NIT, Nombre, Tel, Email..." autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" onclick="mostrarModalNuevoCliente()">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                            </div>
                            <div id="resultadosCliente" class="search-results bg-white rounded-bottom d-none"></div>
                        </div>
                    </div>
                </div>

                {{-- Client Info --}}
                <div id="clienteInfo" class="mb-3 d-none">
                    <div class="card border-0 shadow-sm client-card">
                        <div class="card-body py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <strong id="clienteNombre"></strong>
                                <span class="text-muted ms-2" id="clienteDocumento"></span>
                                <span class="text-muted ms-2" id="clienteTelefono"></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark" id="clienteListaPrecio"></span>
                                <button class="btn btn-sm btn-outline-danger" onclick="quitarCliente()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Price List Selector --}}
                <div class="d-flex align-items-center gap-2 mb-3">
                    <label class="form-label mb-0 fw-semibold small">Lista de precios:</label>
                    <select id="listaPrecio" class="form-select form-select-sm" style="width: 200px;">
                        @foreach($listasPrecios as $lp)
                            <option value="{{ $lp->id }}" {{ $lp->id == $listaPrecioDefault ? 'selected' : '' }}>
                                {{ $lp->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Items Table --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 pos-table" id="tablaItems">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>Producto</th>
                                        <th>Ref.</th>
                                        <th>Variante</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end">P. Unit.</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    <tr id="sinProductos">
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="bi bi-cart display-4 d-block mb-2"></i>
                                            Busque y agregue productos a la venta
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Totals + Payment --}}
            <div class="col-lg-4">
                {{-- Totals Panel --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body pos-totals">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-semibold" id="subtotalDisplay">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted" style="white-space: nowrap;">Desc. global:</span>
                            <div class="d-flex align-items-center gap-1 ms-2">
                                <input type="number" id="descuentoGlobal" class="form-control form-control-sm text-end"
                                       value="0" min="0" step="0.01" style="width: 80px;"
                                       @if(!($esAdmin ?? false)) data-requiere-pin="{{ $requierePinDescuento ? 'true' : 'false' }}" @endif>
                                <select id="descuentoGlobalTipo" class="form-select form-select-sm" style="width: 52px; padding: .25rem .3rem;">
                                    <option value="%">%</option>
                                    <option value="$">$</option>
                                </select>
                            </div>
                        </div>
                        @if($ivaPorcentaje > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">IVA ({{ $ivaPorcentaje }}%):</span>
                            <span class="fw-semibold" id="ivaDisplay">$0.00</span>
                        </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold">TOTAL:</span>
                            <span class="total-final" id="totalDisplay">$0.00</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted" id="itemsCountDisplay">0 productos</small>
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Forma de Pago</h6>
                    </div>
                    <div class="card-body">
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="metodoPago" id="pagoEfectivo" value="efectivo" checked>
                            <label class="btn btn-outline-success" for="pagoEfectivo"><i class="bi bi-cash me-1"></i>Efectivo</label>

                            <input type="radio" class="btn-check" name="metodoPago" id="pagoTransferencia" value="transferencia">
                            <label class="btn btn-outline-info" for="pagoTransferencia"><i class="bi bi-phone me-1"></i>Transfer.</label>

                            <input type="radio" class="btn-check" name="metodoPago" id="pagoMixto" value="mixto">
                            <label class="btn btn-outline-primary" for="pagoMixto"><i class="bi bi-layers me-1"></i>Mixto</label>
                        </div>

                        {{-- Cash Payment --}}
                        <div id="seccionEfectivo">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Monto recibido</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="montoRecibido" class="form-control" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="p-2 rounded text-center" id="cambioContainer" style="display: none;">
                                <small class="text-muted">Cambio:</small>
                                <div class="fs-4 fw-bold text-success" id="cambioDisplay">$0.00</div>
                            </div>
                        </div>

                        {{-- Transfer Payment --}}
                        <div id="seccionTransferencia" style="display: none;">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Tipo</label>
                                <select id="tipoTransferencia" class="form-select form-select-sm">
                                    <option value="nequi">Nequi</option>
                                    <option value="daviplata">Daviplata</option>
                                    <option value="transferencia_bancaria">Transferencia Bancaria</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Monto transferencia</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="montoTransferencia" class="form-control" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Comprobante (opcional)</label>
                                <input type="file" id="archivoComprobante" class="form-control form-control-sm" accept="image/*,.pdf">
                            </div>
                        </div>

                        {{-- Mixed Payment --}}
                        <div id="seccionMixto" style="display: none;">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Efectivo</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="montoMixtoEfectivo" class="form-control" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Transferencia</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="montoMixtoTransferencia" class="form-control" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Tipo transferencia</label>
                                <select id="tipoTransferenciaMixto" class="form-select form-select-sm">
                                    <option value="nequi">Nequi</option>
                                    <option value="daviplata">Daviplata</option>
                                    <option value="transferencia_bancaria">Transferencia Bancaria</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Comprobante (opcional)</label>
                                <input type="file" id="archivoComprobanteMixto" class="form-control form-control-sm" accept="image/*,.pdf">
                            </div>
                            <div class="p-2 bg-light rounded text-center">
                                <small>Total cubierto: <strong id="totalCubierto">$0.00</strong></small>
                                <br><small class="text-danger d-none" id="faltanteMixto">Falta: $0.00</small>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="mt-3">
                            <textarea id="notasVenta" class="form-control form-control-sm" rows="2" placeholder="Notas (opcional)"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Process Button --}}
                <button id="btnProcesar" class="btn btn-miracle btn-lg w-100 py-3" onclick="procesarVenta()" disabled>
                    @if(isset($prefactura) && $prefactura)
                        <i class="bi bi-check-circle me-2"></i>Procesar Prefactura
                    @else
                        <i class="bi bi-check-circle me-2"></i>Procesar Venta
                    @endif
                </button>
            </div>
        </div>
    </div>

    {{-- Modal: New Client --}}
    <div class="modal fade" id="modalNuevoCliente" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Nuevo Cliente</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" id="nuevoClienteNombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Documento / NIT</label>
                        <input type="text" id="nuevoClienteDocumento" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" id="nuevoClienteTelefono" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" id="nuevoClienteEmail" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-miracle" onclick="guardarNuevoCliente()">
                        <i class="bi bi-check me-1"></i>Guardar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: PIN Authorization --}}
    <div class="modal fade" id="modalPin" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-body text-center px-4 py-4">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 56px; height: 56px;">
                            <i class="bi bi-shield-lock text-miracle" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">PIN de Autorización</h6>
                    <p class="text-muted small mb-3" id="pinMotivo">Se requiere PIN de administrador</p>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <input type="password" class="pin-digit form-control text-center fw-bold" maxlength="1" inputmode="numeric" pattern="[0-9]" style="width: 52px; height: 52px; font-size: 1.4rem; border-radius: 12px; border: 2px solid #dee2e6;" data-index="0">
                        <input type="password" class="pin-digit form-control text-center fw-bold" maxlength="1" inputmode="numeric" pattern="[0-9]" style="width: 52px; height: 52px; font-size: 1.4rem; border-radius: 12px; border: 2px solid #dee2e6;" data-index="1">
                        <input type="password" class="pin-digit form-control text-center fw-bold" maxlength="1" inputmode="numeric" pattern="[0-9]" style="width: 52px; height: 52px; font-size: 1.4rem; border-radius: 12px; border: 2px solid #dee2e6;" data-index="2">
                        <input type="password" class="pin-digit form-control text-center fw-bold" maxlength="1" inputmode="numeric" pattern="[0-9]" style="width: 52px; height: 52px; font-size: 1.4rem; border-radius: 12px; border: 2px solid #dee2e6;" data-index="3">
                    </div>
                    <input type="hidden" id="inputPin" value="">
                    <div class="text-danger small mb-3 d-none" id="pinError">
                        <i class="bi bi-exclamation-circle me-1"></i>PIN incorrecto
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                        <button type="button" class="btn btn-miracle btn-sm flex-fill" onclick="verificarPinSubmit()" id="btnVerificarPin" disabled style="border-radius: 10px;">Verificar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    <div class="modal fade" id="modalExito" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center py-5">
                    <i class="bi bi-check-circle display-1 text-success mb-3"></i>
                    <h4 class="fw-bold">Venta Exitosa</h4>
                    <p class="text-muted" id="exitoMensaje"></p>
                    {{-- Factura info display --}}
                    <div id="exitoFacturaInfo" class="d-none mt-3 p-3 bg-light rounded text-start">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-receipt-cutoff"></i>
                            <strong>Factura Electrónica{{ ($siigoModoTest ?? false) ? ' (PRUEBA)' : '' }}:</strong>
                            <span id="exitoFacturaEstado"></span>
                        </div>
                        <div id="exitoFacturaNumero" class="d-none">
                            <small class="text-muted">N°: </small><span id="exitoFacturaNumeroTexto"></span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <a id="btnImprimirTicket" href="#" class="btn btn-outline-danger" target="_blank">
                            <i class="bi bi-printer me-1"></i>Imprimir Ticket
                        </a>
                        <button class="btn btn-miracle" onclick="nuevaVenta()">
                            <i class="bi bi-plus me-1"></i>Nueva Venta
                        </button>
                        <a href="{{ route('pdv.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SIIGO Invoice Modal --}}
    @if($siigoActivo)
        @include('pdv.venta.partials.modal-factura')
    @endif

    @push('scripts')
    <script>
        // State
        let items = [];
        let clienteSeleccionado = null;
        let pinCallback = null;
        let autorizadorDescuento = null;
        let autorizadorPrecio = null;
        const ivaPorcentaje = {{ $ivaPorcentaje }};
        const descuentoMaximo = {{ $descuentoMaximo }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const siigoActivo = {{ $siigoActivo ? 'true' : 'false' }};
        const siigoFacturarSiempre = {{ $siigoFacturarSiempre ? 'true' : 'false' }};
        const siigoModoTest = {{ ($siigoModoTest ?? false) ? 'true' : 'false' }};
        const listasPrecioPermitidas = @json($listasPrecioIdsPermitidas);
        let searchTimeout;
        let ultimaVentaResult = null; // Stores last sale result for SIIGO flow

        // Product Search
        const buscarInput = document.getElementById('buscarProducto');
        const resultadosDiv = document.getElementById('resultadosBusqueda');

        // Autofocus al cargar la vista: lector de códigos de barras listo para escribir
        if (buscarInput) {
            buscarInput.focus();
        }

        function renderResultadosProductos(filas) {
            if (filas.length === 0) {
                resultadosDiv.innerHTML = '<div class="p-3 text-center text-muted">No se encontraron productos</div>';
            } else {
                resultadosDiv.innerHTML = filas.map(f => {
                    const stockClass = f.stock_disponible > 5 ? 'stock-badge-ok' : (f.stock_disponible > 0 ? 'stock-badge-low' : 'stock-badge-zero');
                    const stockText  = f.controla_stock ? `Stock: ${f.stock_disponible}` : 'Sin control';
                    const subRef     = f.codigo_barras
                        ? `${f.referencia} · <code>${f.codigo_barras}</code>`
                        : f.referencia;
                    return `<div class="search-result-item p-2 border-bottom d-flex justify-content-between align-items-center"
                                onclick='agregarFilaDirecto(${JSON.stringify(f).replace(/'/g, "&#39;")})'>
                        <div>
                            <div class="fw-semibold">${f.nombre_completo}</div>
                            <small class="text-muted">${subRef}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">$${parseFloat(f.precio || 0).toFixed(2)}</div>
                            <small class="badge ${stockClass}">${stockText}</small>
                        </div>
                    </div>`;
                }).join('');
            }
            resultadosDiv.classList.remove('d-none');
        }

        function buscarProductosFetch(q) {
            const listaPrecioId = document.getElementById('listaPrecio').value;
            const ubicacionId = {{ $ubicacionIdDefault ?? 0 }};
            return fetch(`{{ route('pdv.ventas.buscar-productos') }}?q=${encodeURIComponent(q)}&lista_precio_id=${listaPrecioId}&ubicacion_id=${ubicacionId}`)
                .then(r => r.json());
        }

        // Devuelve la fila con match exacto por código de barras (o null).
        // El servicio ya prioriza variantes al tope cuando el término parece EAN.
        function encontrarMatchExactoCodigoBarras(filas, codigo) {
            const codigoTrim = String(codigo).trim();
            if (!codigoTrim) return null;
            return filas.find(f => f.codigo_barras && String(f.codigo_barras).trim() === codigoTrim) || null;
        }

        buscarInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            if (q.length < 2) { resultadosDiv.classList.add('d-none'); return; }

            // Si parece código de barras (numérico, 6+ dígitos) reducir el debounce —
            // el lector escribe rápido y no siempre manda Enter, así que tras una breve
            // pausa auto-agregamos si hay match exacto.
            const pareceCodigoBarras = /^\d{6,}$/.test(q);
            const delay = pareceCodigoBarras ? 120 : 300;

            searchTimeout = setTimeout(() => {
                buscarProductosFetch(q).then(filas => {
                    if (pareceCodigoBarras) {
                        const match = encontrarMatchExactoCodigoBarras(filas, q);
                        if (match) {
                            agregarItem(match);
                            buscarInput.value = '';
                            resultadosDiv.classList.add('d-none');
                            buscarInput.focus();
                            return;
                        }
                    }
                    renderResultadosProductos(filas);
                });
            }, delay);
        });

        // Enter: el lector de códigos de barras envía Enter al final. Si hay match exacto
        // por código de barras, agrega el producto/variante directo y limpia el input.
        // Si no hay match exacto, muestra los resultados como búsqueda manual.
        buscarInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                resultadosDiv.classList.add('d-none');
                return;
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                const q = this.value.trim();
                if (q.length < 2) return;

                buscarProductosFetch(q).then(filas => {
                    const match = encontrarMatchExactoCodigoBarras(filas, q);
                    if (match) {
                        // Agregar directo al carrito — ya no hay modal intermedio
                        agregarItem(match);
                        buscarInput.value = '';
                        resultadosDiv.classList.add('d-none');
                        buscarInput.focus();
                    } else {
                        renderResultadosProductos(filas);
                    }
                });
            }
        });

        document.addEventListener('click', function(e) {
            if (!buscarInput.contains(e.target) && !resultadosDiv.contains(e.target)) {
                resultadosDiv.classList.add('d-none');
            }
            if (!document.getElementById('buscarCliente').contains(e.target)) {
                document.getElementById('resultadosCliente').classList.add('d-none');
            }
        });

        // Agregar fila directa al carrito (desde clic en dropdown o match exacto de barras)
        function agregarFilaDirecto(fila) {
            resultadosDiv.classList.add('d-none');
            buscarInput.value = '';
            agregarItem(fila);
            buscarInput.focus();
        }

        // Add Item
        function agregarItem(fila) {
            const varianteId = fila.variante_producto_id || null;
            const existente = items.findIndex(i =>
                i.producto_id === fila.producto_id &&
                (i.variante_producto_id || null) === varianteId
            );

            if (existente >= 0) {
                items[existente].cantidad++;
                renderItems();
                return;
            }

            const precio = parseFloat(fila.precio) || 0;
            items.push({
                producto_id: fila.producto_id,
                variante_producto_id: varianteId,
                nombre: fila.nombre_producto,
                referencia: fila.referencia,
                variante_nombre: fila.nombre_variante || '-',
                cantidad: 1,
                precio_unitario: precio,
                precio_original: precio,
                stock_disponible: fila.stock_disponible,
                controla_stock: fila.controla_stock,
                iva: 0,
                siigo_product_code: fila.siigo_product_code || null,
            });

            renderItems();
            buscarInput.focus();
        }

        // Render Items
        function renderItems() {
            const tbody = document.getElementById('itemsBody');

            if (items.length === 0) {
                tbody.innerHTML = `<tr id="sinProductos"><td colspan="7" class="text-center text-muted py-5">
                    <i class="bi bi-cart display-4 d-block mb-2"></i>Busque y agregue productos a la venta</td></tr>`;
                actualizarTotales();
                return;
            }

            tbody.innerHTML = items.map((item, i) => {
                const subtotal = item.precio_unitario * item.cantidad;
                const stockWarning = item.controla_stock && item.cantidad > item.stock_disponible
                    ? '<i class="bi bi-exclamation-triangle text-danger ms-1" title="Stock insuficiente"></i>' : '';

                const homologado = !!item.siigo_product_code;
                const homologarBtn = homologado
                    ? `<span class="badge bg-success" title="SIIGO: ${item.siigo_product_code}"><i class="bi bi-link-45deg"></i></span>`
                    : `<button class="btn btn-sm btn-warning" title="Producto NO homologado con SIIGO. Click para homologar." onclick="homologarSiigo(${item.producto_id}, ${item.variante_producto_id || 'null'})"><i class="bi bi-exclamation-triangle"></i> Homologar SIIGO</button>`;

                return `<tr class="${homologado ? '' : 'table-warning'}">
                    <td><button class="btn btn-sm btn-outline-danger border-0" onclick="eliminarItem(${i})"><i class="bi bi-trash"></i></button></td>
                    <td>${item.nombre} ${homologarBtn}</td>
                    <td><small class="text-muted">${item.referencia}</small></td>
                    <td><small>${item.variante_nombre}</small></td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm qty-input" value="${item.cantidad}"
                               min="1" onchange="cambiarCantidad(${i}, this.value)">${stockWarning}
                    </td>
                    <td class="text-end">
                        <input type="number" class="form-control form-control-sm price-input" value="${item.precio_unitario.toFixed(2)}"
                               min="0" step="0.01" onchange="cambiarPrecio(${i}, this.value)">
                    </td>
                    <td class="text-end fw-semibold">$${subtotal.toFixed(2)}</td>
                </tr>`;
            }).join('');

            actualizarTotales();
        }

        function eliminarItem(index) {
            items.splice(index, 1);
            renderItems();
        }

        function cambiarCantidad(index, valor) {
            items[index].cantidad = Math.max(1, parseInt(valor) || 1);
            renderItems();
        }

        function cambiarPrecio(index, valor) {
            const nuevoPrecio = parseFloat(valor) || 0;
            const aplicar = () => {
                items[index].precio_unitario = nuevoPrecio;
                renderItems();
            };
            if (nuevoPrecio !== items[index].precio_original) {
                solicitarPin('Cambio de precio manual', function(autorizadorId) {
                    autorizadorPrecio = autorizadorId;
                    aplicar();
                });
                return;
            }
            aplicar();
        }

        // Update Totals
        function actualizarTotales() {
            let subtotal = 0;
            let totalItems = 0;

            items.forEach(item => {
                subtotal += item.precio_unitario * item.cantidad;
                totalItems += item.cantidad;
            });

            const descGlobalInput = parseFloat(document.getElementById('descuentoGlobal').value) || 0;
            const descGlobalTipo = document.getElementById('descuentoGlobalTipo').value;
            let descuentoGlobal = descGlobalTipo === '%' ? subtotal * (descGlobalInput / 100) : descGlobalInput;

            const baseIva = subtotal - descuentoGlobal;
            const iva = ivaPorcentaje > 0 ? baseIva * (ivaPorcentaje / 100) : 0;
            const total = baseIva + iva;

            document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);
            document.getElementById('itemsCountDisplay').textContent = `${totalItems} producto${totalItems !== 1 ? 's' : ''}`;

            if (document.getElementById('ivaDisplay')) {
                document.getElementById('ivaDisplay').textContent = '$' + iva.toFixed(2);
            }

            const itemsSinHomologar = items.filter(it => !it.siigo_product_code);
            const btnProc = document.getElementById('btnProcesar');
            if (items.length === 0) {
                btnProc.disabled = true;
                btnProc.title = '';
            } else if (itemsSinHomologar.length > 0) {
                btnProc.disabled = true;
                btnProc.title = 'Hay ' + itemsSinHomologar.length + ' producto(s) sin homologar con SIIGO. Homologue antes de procesar.';
            } else {
                btnProc.disabled = false;
                btnProc.title = '';
            }

            // Update change calculation
            calcularCambio();
        }

        // Descuento Global con validación de PIN
        let pinDescuentoGlobalAutorizado = {{ ($esAdmin ?? false) ? 'true' : 'false' }};
        const requierePinDescGlobal = {{ ($requierePinDescuento ?? true) ? 'true' : 'false' }};
        const descuentoMaxCajero = {{ $descuentoMaximo ?? 15 }};

        document.getElementById('descuentoGlobal').addEventListener('focus', function() {
            if (requierePinDescGlobal && !pinDescuentoGlobalAutorizado) {
                this.blur();
                solicitarPin('Se requiere PIN de administrador para aplicar descuento global', function(autorizadorId) {
                    pinDescuentoGlobalAutorizado = true;
                    document.getElementById('descuentoGlobal').focus();
                });
            }
        });
        document.getElementById('descuentoGlobal').addEventListener('input', function() {
            const tipo = document.getElementById('descuentoGlobalTipo').value;
            if (tipo === '%' && parseFloat(this.value) > descuentoMaxCajero && !pinDescuentoGlobalAutorizado) {
                this.value = descuentoMaxCajero;
                Swal.fire({ icon: 'warning', title: 'Límite alcanzado', text: `Descuento máximo sin autorización: ${descuentoMaxCajero}%`, timer: 2000, showConfirmButton: false });
            }
            actualizarTotales();
        });
        document.getElementById('descuentoGlobalTipo').addEventListener('change', actualizarTotales);

        // Payment Method Toggle
        document.querySelectorAll('input[name="metodoPago"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('seccionEfectivo').style.display = this.value === 'efectivo' ? 'block' : 'none';
                document.getElementById('seccionTransferencia').style.display = this.value === 'transferencia' ? 'block' : 'none';
                document.getElementById('seccionMixto').style.display = this.value === 'mixto' ? 'block' : 'none';
            });
        });

        // Change Calculation
        function calcularCambio() {
            const total = parseFloat(document.getElementById('totalDisplay').textContent.replace('$', '').replace(',', '')) || 0;
            const recibido = parseFloat(document.getElementById('montoRecibido').value) || 0;
            const cambio = recibido - total;
            const container = document.getElementById('cambioContainer');

            if (recibido > 0) {
                container.style.display = 'block';
                container.style.background = cambio >= 0 ? '#d4edda' : '#f8d7da';
                document.getElementById('cambioDisplay').textContent = '$' + Math.max(0, cambio).toFixed(2);
                document.getElementById('cambioDisplay').className = 'fs-4 fw-bold ' + (cambio >= 0 ? 'text-success' : 'text-danger');
            } else {
                container.style.display = 'none';
            }
        }
        document.getElementById('montoRecibido').addEventListener('input', calcularCambio);

        // Mixed Payment Calculation
        ['montoMixtoEfectivo', 'montoMixtoTransferencia'].forEach(id => {
            document.getElementById(id).addEventListener('input', function() {
                const total = parseFloat(document.getElementById('totalDisplay').textContent.replace('$', '').replace(',', '')) || 0;
                const efectivo = parseFloat(document.getElementById('montoMixtoEfectivo').value) || 0;
                const transferencia = parseFloat(document.getElementById('montoMixtoTransferencia').value) || 0;
                const cubierto = efectivo + transferencia;
                document.getElementById('totalCubierto').textContent = '$' + cubierto.toFixed(2);
                const faltante = total - cubierto;
                const faltanteEl = document.getElementById('faltanteMixto');
                if (faltante > 0.01) {
                    faltanteEl.textContent = 'Falta: $' + faltante.toFixed(2);
                    faltanteEl.classList.remove('d-none');
                } else {
                    faltanteEl.classList.add('d-none');
                }
            });
        });

        // Client Search
        const buscarClienteInput = document.getElementById('buscarCliente');
        let clienteSearchTimeout;

        buscarClienteInput.addEventListener('input', function() {
            clearTimeout(clienteSearchTimeout);
            const q = this.value.trim();
            if (q.length < 2) { document.getElementById('resultadosCliente').classList.add('d-none'); return; }

            clienteSearchTimeout = setTimeout(() => {
                fetch(`{{ route('pdv.ventas.buscar-clientes') }}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(clientes => {
                        const div = document.getElementById('resultadosCliente');
                        if (clientes.length === 0) {
                            div.innerHTML = '<div class="p-3 text-center text-muted">No se encontraron clientes</div>';
                        } else {
                            div.innerHTML = clientes.map(c => `
                                <div class="search-result-item p-2 border-bottom" onclick='asignarCliente(${JSON.stringify(c).replace(/'/g, "&#39;")})'>
                                    <div class="fw-semibold">${c.nombre}</div>
                                    <small class="text-muted">${c.documento || ''} ${c.telefono ? '| ' + c.telefono : ''}</small>
                                    <span class="badge bg-light text-dark float-end">${c.lista_precio_nombre}</span>
                                </div>
                            `).join('');
                        }
                        div.classList.remove('d-none');
                    });
            }, 300);
        });

        function asignarCliente(cliente) {
            clienteSeleccionado = cliente;
            document.getElementById('clienteInfo').classList.remove('d-none');
            document.getElementById('clienteNombre').textContent = cliente.nombre;
            document.getElementById('clienteDocumento').textContent = cliente.documento || '';
            document.getElementById('clienteTelefono').textContent = cliente.telefono || '';
            document.getElementById('clienteListaPrecio').textContent = cliente.lista_precio_nombre;
            document.getElementById('resultadosCliente').classList.add('d-none');
            buscarClienteInput.value = '';

            // Auto-set price list
            if (cliente.lista_precio_id) {
                const select = document.getElementById('listaPrecio');

                // Remover opciones temporales previas
                select.querySelectorAll('option[data-temporal="true"]').forEach(opt => opt.remove());

                // Si la lista del cliente no está entre las permitidas, agregarla temporalmente
                if (!listasPrecioPermitidas.includes(cliente.lista_precio_id)) {
                    const opt = document.createElement('option');
                    opt.value = cliente.lista_precio_id;
                    opt.textContent = cliente.lista_precio_nombre + ' (del cliente)';
                    opt.dataset.temporal = 'true';
                    select.appendChild(opt);
                }

                select.value = cliente.lista_precio_id;
            }
        }

        // Listener para remover opción temporal cuando el cajero cambia de lista
        document.getElementById('listaPrecio').addEventListener('change', function() {
            const temporales = this.querySelectorAll('option[data-temporal="true"]');
            temporales.forEach(opt => {
                if (opt.value !== this.value) {
                    opt.remove();
                }
            });
        });

        function quitarCliente() {
            clienteSeleccionado = null;
            document.getElementById('clienteInfo').classList.add('d-none');

            // Remover opciones temporales y resetear al default
            const select = document.getElementById('listaPrecio');
            select.querySelectorAll('option[data-temporal="true"]').forEach(opt => opt.remove());
            select.value = {{ $listaPrecioDefault }};
        }

        // New Client Modal
        function mostrarModalNuevoCliente() {
            new bootstrap.Modal(document.getElementById('modalNuevoCliente')).show();
        }

        function guardarNuevoCliente() {
            const nombre = document.getElementById('nuevoClienteNombre').value.trim();
            if (!nombre) { Swal.fire('Error', 'El nombre es obligatorio', 'error'); return; }

            fetch('{{ route("pdv.ventas.cliente-rapido") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({
                    nombre: nombre,
                    documento: document.getElementById('nuevoClienteDocumento').value,
                    telefono: document.getElementById('nuevoClienteTelefono').value,
                    email: document.getElementById('nuevoClienteEmail').value,
                    lista_precio_id: document.getElementById('listaPrecio').value,
                }),
            })
            .then(r => {
                if (!r.ok) return r.json().then(err => { throw new Error(err.message || 'Error al crear cliente'); });
                return r.json();
            })
            .then(cliente => {
                bootstrap.Modal.getInstance(document.getElementById('modalNuevoCliente')).hide();
                asignarCliente(cliente);
                Swal.fire('Listo', 'Cliente creado exitosamente', 'success');
            })
            .catch(err => Swal.fire('Error', err.message || 'No se pudo crear el cliente', 'error'));
        }

        // PIN Authorization — 4-digit OTP style
        const pinDigits = document.querySelectorAll('.pin-digit');
        const btnVerificarPin = document.getElementById('btnVerificarPin');

        function getFullPin() {
            return Array.from(pinDigits).map(d => d.value).join('');
        }

        function updateHiddenPin() {
            document.getElementById('inputPin').value = getFullPin();
            btnVerificarPin.disabled = getFullPin().length < 4;
        }

        pinDigits.forEach((input, idx) => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value && idx < 3) {
                    pinDigits[idx + 1].focus();
                }
                updateHiddenPin();
                // Auto-submit when all 4 digits entered
                if (getFullPin().length === 4) {
                    verificarPinSubmit();
                }
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && idx > 0) {
                    pinDigits[idx - 1].focus();
                    pinDigits[idx - 1].value = '';
                    updateHiddenPin();
                }
                if (e.key === 'Enter') verificarPinSubmit();
            });
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasted = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').slice(0, 4);
                pasted.split('').forEach((ch, i) => { if (pinDigits[i]) pinDigits[i].value = ch; });
                if (pasted.length > 0) pinDigits[Math.min(pasted.length, 3)].focus();
                updateHiddenPin();
                if (getFullPin().length === 4) verificarPinSubmit();
            });
        });

        function solicitarPin(motivo, callback) {
            pinCallback = callback;
            document.getElementById('pinMotivo').textContent = motivo;
            pinDigits.forEach(d => { d.value = ''; d.style.borderColor = '#dee2e6'; });
            document.getElementById('inputPin').value = '';
            document.getElementById('pinError').classList.add('d-none');
            btnVerificarPin.disabled = true;
            new bootstrap.Modal(document.getElementById('modalPin')).show();
            setTimeout(() => pinDigits[0].focus(), 300);
        }

        function verificarPinSubmit() {
            const pin = getFullPin();
            if (pin.length < 4) return;
            btnVerificarPin.disabled = true;

            fetch('{{ route("pdv.ventas.verificar-pin") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ pin: pin }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.exito) {
                    pinDigits.forEach(d => d.style.borderColor = '#28a745');
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('modalPin')).hide();
                        if (pinCallback) pinCallback(data.autorizador_id);
                    }, 400);
                } else {
                    pinDigits.forEach(d => { d.value = ''; d.style.borderColor = '#dc3545'; });
                    document.getElementById('inputPin').value = '';
                    document.getElementById('pinError').classList.remove('d-none');
                    btnVerificarPin.disabled = true;
                    setTimeout(() => pinDigits[0].focus(), 200);
                }
            })
            .catch(() => {
                document.getElementById('pinError').classList.remove('d-none');
                btnVerificarPin.disabled = false;
            });
        }

        // Process Sale
        function procesarVenta() {
            if (items.length === 0) return;

            const sinHomologar = items.filter(it => !it.siigo_product_code);
            if (sinHomologar.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Productos sin homologar',
                    html: 'Los siguientes productos no están homologados con SIIGO y no pueden facturarse electrónicamente:<br><ul class="text-start mt-2">' +
                        sinHomologar.map(it => '<li>' + (it.nombre || '') + ' (' + (it.referencia || '') + ')</li>').join('') +
                        '</ul>Homologue cada producto antes de procesar la venta.',
                });
                return;
            }

            const total = parseFloat(document.getElementById('totalDisplay').textContent.replace('$', '').replace(',', '')) || 0;
            const metodo = document.querySelector('input[name="metodoPago"]:checked').value;

            // Validate payment
            if (metodo === 'efectivo') {
                const recibido = parseFloat(document.getElementById('montoRecibido').value) || 0;
                if (recibido < total) {
                    Swal.fire('Error', 'El monto recibido es menor al total', 'warning');
                    return;
                }
            } else if (metodo === 'mixto') {
                const efec = parseFloat(document.getElementById('montoMixtoEfectivo').value) || 0;
                const trans = parseFloat(document.getElementById('montoMixtoTransferencia').value) || 0;
                if ((efec + trans) < total - 0.01) {
                    Swal.fire('Error', 'La suma de los pagos no cubre el total', 'warning');
                    return;
                }
            }

            document.getElementById('btnProcesar').disabled = true;
            document.getElementById('btnProcesar').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

            const descGlobalInput = parseFloat(document.getElementById('descuentoGlobal').value) || 0;
            const descGlobalTipo = document.getElementById('descuentoGlobalTipo').value;
            const subtotal = items.reduce((s, i) => s + (i.precio_unitario * i.cantidad), 0);
            const descuentoGlobal = descGlobalTipo === '%' ? subtotal * (descGlobalInput / 100) : descGlobalInput;

            const datos = {
                items: items.map(i => ({
                    producto_id: i.producto_id,
                    variante_producto_id: i.variante_producto_id,
                    cantidad: i.cantidad,
                    precio_unitario: i.precio_unitario,
                    precio_original: i.precio_original,
                    iva: i.iva,
                })),
                cliente_id: clienteSeleccionado ? clienteSeleccionado.id : null,
                nombre_cliente: clienteSeleccionado ? clienteSeleccionado.nombre : 'Consumidor Final',
                lista_precio_id: document.getElementById('listaPrecio').value,
                descuento_global: descuentoGlobal,
                metodo_pago: metodo,
                monto_efectivo: metodo === 'efectivo' ? (parseFloat(document.getElementById('montoRecibido').value) || total) : (metodo === 'mixto' ? (parseFloat(document.getElementById('montoMixtoEfectivo').value) || 0) : null),
                monto_transferencia: metodo === 'transferencia' ? total : (metodo === 'mixto' ? (parseFloat(document.getElementById('montoMixtoTransferencia').value) || 0) : null),
                monto_recibido: metodo === 'efectivo' ? (parseFloat(document.getElementById('montoRecibido').value) || 0) : null,
                cambio: metodo === 'efectivo' ? Math.max(0, (parseFloat(document.getElementById('montoRecibido').value) || 0) - total) : null,
                tipo_transferencia: metodo === 'transferencia' ? document.getElementById('tipoTransferencia').value : (metodo === 'mixto' ? document.getElementById('tipoTransferenciaMixto').value : null),
                notas: document.getElementById('notasVenta').value,
                descuento_autorizado_por: autorizadorDescuento,
                precio_autorizado_por: autorizadorPrecio,
            };

            const formData = new FormData();
            formData.append('items', JSON.stringify(datos.items));
            formData.append('cliente_id', datos.cliente_id || '');
            formData.append('nombre_cliente', datos.nombre_cliente || '');
            formData.append('lista_precio_id', datos.lista_precio_id);
            formData.append('descuento_global', datos.descuento_global);
            formData.append('metodo_pago', datos.metodo_pago);
            formData.append('monto_efectivo', datos.monto_efectivo || '');
            formData.append('monto_transferencia', datos.monto_transferencia || '');
            formData.append('tipo_transferencia', datos.tipo_transferencia || '');
            formData.append('monto_recibido', datos.monto_recibido || '');
            formData.append('cambio', datos.cambio || '');
            formData.append('notas', datos.notas || '');
            formData.append('descuento_autorizado_por', datos.descuento_autorizado_por || '');
            formData.append('precio_autorizado_por', datos.precio_autorizado_por || '');

            if (window.prefacturaId) {
                formData.append('prefactura_id', window.prefacturaId);
            }

            // Add file if exists
            const fileInput = metodo === 'transferencia'
                ? document.getElementById('archivoComprobante')
                : (metodo === 'mixto' ? document.getElementById('archivoComprobanteMixto') : null);
            if (fileInput && fileInput.files[0]) {
                formData.append('archivo_comprobante', fileInput.files[0]);
            }

            fetch('{{ route("pdv.ventas.procesar") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData,
            })
            .then(r => r.json())
            .then(result => {
                if (result.exito) {
                    ultimaVentaResult = result;
                    document.getElementById('exitoMensaje').textContent = result.mensaje;
                    document.getElementById('btnImprimirTicket').href = `/pdv/ventas/${result.venta.id}/ticket`;

                    if (siigoActivo) {
                        // Build client data for pre-filling the invoice form
                        const clienteData = clienteSeleccionado ? {
                            documento: clienteSeleccionado.documento || '',
                            nombre: clienteSeleccionado.nombre || '',
                            email: clienteSeleccionado.email || '',
                            telefono: clienteSeleccionado.telefono || '',
                        } : null;

                        if (siigoFacturarSiempre) {
                            // Auto-invoice: if client exists, use their data; otherwise consumidor final
                            ventaIdParaFactura = result.venta.id;
                            mostrarPasoResultado();
                            const modal = new bootstrap.Modal(document.getElementById('modalFactura'));
                            modal.show();

                            if (clienteSeleccionado && clienteSeleccionado.documento) {
                                enviarFactura({
                                    tipo_factura: 'con_cliente',
                                    tipo_documento: '13',
                                    numero_identificacion: clienteSeleccionado.documento,
                                    nombre_fiscal: clienteSeleccionado.nombre,
                                    email_factura: clienteSeleccionado.email || '',
                                });
                            } else {
                                enviarFactura({ tipo_factura: 'consumidor_final' });
                            }
                        } else {
                            // Show the invoice prompt modal
                            abrirModalFactura(result.venta.id, clienteData);
                        }
                    } else {
                        mostrarExitoVenta();
                    }
                } else {
                    Swal.fire('Error', result.mensaje, 'error');
                    document.getElementById('btnProcesar').disabled = false;
                    document.getElementById('btnProcesar').innerHTML = '<i class="bi bi-check-circle me-2"></i>Procesar Venta';
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Error de conexión', 'error');
                document.getElementById('btnProcesar').disabled = false;
                document.getElementById('btnProcesar').innerHTML = '<i class="bi bi-check-circle me-2"></i>Procesar Venta';
            });
        }

        function mostrarExitoVenta() {
            // Show factura info in success modal if available
            const facturaInfo = document.getElementById('exitoFacturaInfo');
            if (ultimaVentaResult && ultimaVentaResult.factura) {
                facturaInfo.classList.remove('d-none');
                const f = ultimaVentaResult.factura;
                const estados = {
                    aprobada: '<span class="badge bg-success">Aprobada</span>',
                    pendiente: '<span class="badge bg-warning text-dark">Pendiente</span>',
                    error: '<span class="badge bg-danger">Error</span>',
                    rechazada: '<span class="badge bg-danger">Rechazada</span>',
                };
                document.getElementById('exitoFacturaEstado').innerHTML = estados[f.estado_dian] || '';
                if (f.numero_factura) {
                    document.getElementById('exitoFacturaNumero').classList.remove('d-none');
                    document.getElementById('exitoFacturaNumeroTexto').textContent = f.numero_factura;
                }
            } else {
                facturaInfo.classList.add('d-none');
            }

            new bootstrap.Modal(document.getElementById('modalExito')).show();
        }

        function nuevaVenta() {
            bootstrap.Modal.getInstance(document.getElementById('modalExito')).hide();
            limpiarVenta();
        }

        function limpiarVenta() {
            items = [];
            clienteSeleccionado = null;
            autorizadorDescuento = null;
            autorizadorPrecio = null;
            document.getElementById('clienteInfo').classList.add('d-none');
            document.getElementById('descuentoGlobal').value = 0;
            document.getElementById('montoRecibido').value = '';
            document.getElementById('notasVenta').value = '';
            document.getElementById('cambioContainer').style.display = 'none';
            document.getElementById('btnProcesar').disabled = true;
            document.getElementById('btnProcesar').innerHTML = '<i class="bi bi-check-circle me-2"></i>Procesar Venta';
            renderItems();
            buscarInput.focus();
        }

        // Price list change - auto-update prices
        document.getElementById('listaPrecio').addEventListener('change', function() {
            if (items.length === 0) return;

            const listaPrecioId = this.value;
            const payload = items.map(i => ({
                producto_id: i.producto_id,
                variante_producto_id: i.variante_producto_id,
            }));

            fetch('{{ route("pdv.ventas.obtener-precios") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ lista_precio_id: listaPrecioId, items: payload }),
            })
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(precios => {
                precios.forEach(p => {
                    const idx = items.findIndex(i =>
                        i.producto_id == p.producto_id &&
                        (i.variante_producto_id == p.variante_producto_id)
                    );
                    if (idx >= 0) {
                        items[idx].precio_unitario = p.precio;
                        items[idx].precio_original = p.precio;
                    }
                });
                renderItems();
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Precios actualizados', showConfirmButton: false, timer: 1500 });
            })
            .catch(() => {
                Swal.fire('Error', 'No se pudieron actualizar los precios', 'error');
            });
        });

        // Poll prefacturas every 15s
        function pollPrefacturas() {
            fetch('{{ route("pdv.prefacturas.pendientes") }}', {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('prefacturasCount');
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            })
            .catch(() => {});
        }
        setInterval(pollPrefacturas, 15000);
        pollPrefacturas();

        // Pre-load prefactura data
        @if(isset($prefactura) && $prefactura)
            window.prefacturaId = {{ $prefactura->id }};

            // Pre-load items
            items = @json($prefacturaItems ?? []);

            @if($prefactura->cliente_id && $prefactura->cliente)
                // Pre-load client
                clienteSeleccionado = {
                    id: {{ $prefactura->cliente_id }},
                    nombre: @json($prefactura->cliente->razon_social ?: $prefactura->cliente->nombre_contacto ?? 'Consumidor Final'),
                    documento: @json($prefactura->cliente->numero_identificacion ?? ''),
                    telefono: @json($prefactura->cliente->telefono ?? ''),
                    lista_precio_id: {{ $prefactura->lista_precio_id ?? 'null' }},
                    lista_precio_nombre: @json($prefactura->listaPrecio->nombre ?? ''),
                };
                // Show client info in UI
                document.getElementById('clienteNombre').textContent = clienteSeleccionado.nombre;
                document.getElementById('clienteDocumento').textContent = clienteSeleccionado.documento || '';
                document.getElementById('clienteTelefono').textContent = clienteSeleccionado.telefono || '';
                document.getElementById('clienteListaPrecio').textContent = clienteSeleccionado.lista_precio_nombre || '';
                document.getElementById('clienteInfo').classList.remove('d-none');
                document.getElementById('buscarCliente').parentElement.classList.add('d-none');
            @endif

            // Set lista de precios
            @if($prefactura->lista_precio_id)
                document.getElementById('listaPrecio').value = '{{ $prefactura->lista_precio_id }}';
            @endif

            renderItems();
        @else
            window.prefacturaId = null;
        @endif

        // Focus search on load
        buscarInput.focus();
    </script>

    {{-- Select2 + Modal Homologación SIIGO --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    (function() {
        const URL_LISTAR_SIIGO = "{{ route('productos.siigo.listar') }}";
        const URL_HOMOLOGACION_BASE = "{{ url('productos') }}";
        const CSRF = $('meta[name="csrf-token"]').attr('content');

        let estadoSiigo = {
            productoId: null,
            varianteId: null,
            tieneVariantes: false,
            siigoCodeActual: null,
        };

        window.homologarSiigo = function(productoId, varianteId) {
            estadoSiigo.productoId = productoId;
            estadoSiigo.varianteId = (varianteId === null || varianteId === 'null') ? null : varianteId;

            const $info = $('#siigoHomologarInfo');
            const $msg = $('#siigoHomologarMensaje').addClass('d-none').removeClass('alert-success alert-danger').text('');
            const $btnQuitar = $('#btnSiigoQuitarHomologacion').addClass('d-none');
            $info.html('<div class="text-center py-2"><div class="spinner-border spinner-border-sm"></div> Cargando datos del producto...</div>');

            $('#modalSiigoHomologar').modal('show');

            $.get(URL_HOMOLOGACION_BASE + '/' + productoId + '/siigo/homologacion')
                .done(function(data) {
                    estadoSiigo.tieneVariantes = !!data.producto.tiene_variantes;

                    let infoHtml = '<div class="card"><div class="card-body py-2">';
                    infoHtml += '<div><strong>' + escapeHtml(data.producto.referencia) + '</strong> — ' + escapeHtml(data.producto.nombre) + '</div>';

                    if (estadoSiigo.tieneVariantes) {
                        const variante = (data.variantes || []).find(v => String(v.id) === String(estadoSiigo.varianteId));
                        if (!variante) {
                            infoHtml += '<div class="text-danger small mt-1">Variante no encontrada.</div>';
                            estadoSiigo.siigoCodeActual = null;
                        } else {
                            const detalle = [variante.referencia_variante, variante.color, variante.sku].filter(Boolean).join(' / ');
                            infoHtml += '<div class="text-muted small mt-1"><i class="bi bi-tag"></i> Variante: ' + escapeHtml(detalle || ('ID ' + variante.id)) + '</div>';
                            estadoSiigo.siigoCodeActual = variante.siigo_product_code || null;
                        }
                    } else {
                        estadoSiigo.siigoCodeActual = data.producto.siigo_product_code || null;
                    }

                    if (estadoSiigo.siigoCodeActual) {
                        infoHtml += '<div class="mt-2"><span class="badge bg-success">Homologado</span> Código SIIGO actual: <code>' + escapeHtml(estadoSiigo.siigoCodeActual) + '</code></div>';
                        $btnQuitar.removeClass('d-none');
                    } else {
                        infoHtml += '<div class="mt-2"><span class="badge bg-secondary">Sin homologar</span></div>';
                    }
                    infoHtml += '</div></div>';
                    $info.html(infoHtml);

                    inicializarSelectSiigo();
                })
                .fail(function(xhr) {
                    $info.html('<div class="alert alert-danger mb-0">No se pudo cargar el producto: ' + escapeHtml(xhr.responseJSON?.message || xhr.statusText) + '</div>');
                });
        };

        function inicializarSelectSiigo() {
            const $select = $('#selectSiigoProducto');
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.empty();

            $select.select2({
                dropdownParent: $('#modalSiigoHomologar'),
                theme: 'bootstrap-5',
                placeholder: 'Buscar por código, nombre o referencia...',
                allowClear: true,
                minimumInputLength: 0,
                ajax: {
                    url: URL_LISTAR_SIIGO,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1,
                            page_size: 25,
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        const results = (data.results || []).map(p => {
                            const partes = ['[' + (p.code || '') + ']', p.name || ''];
                            if (p.reference && p.reference !== p.code) partes.push('· ref: ' + p.reference);
                            return {
                                id: p.code,
                                text: partes.join(' '),
                                raw: p,
                            };
                        });
                        const totalCargado = ((params.page - 1) * (data.page_size || 25)) + results.length;
                        const totalDisponible = data.total_results || results.length;
                        return {
                            results: results,
                            pagination: { more: totalCargado < totalDisponible },
                        };
                    },
                    cache: false,
                },
            });

            if (estadoSiigo.siigoCodeActual) {
                const opt = new Option('[' + estadoSiigo.siigoCodeActual + '] (código actual)', estadoSiigo.siigoCodeActual, true, true);
                $select.append(opt).trigger('change');
            }
        }

        $('#btnSiigoGuardarHomologacion').on('click', function() {
            const codigo = $('#selectSiigoProducto').val();
            enviarHomologacion(codigo, false);
        });

        $('#btnSiigoQuitarHomologacion').on('click', function() {
            if (!confirm('¿Quitar la homologación SIIGO actual?')) return;
            enviarHomologacion(null, true);
        });

        function enviarHomologacion(codigo, esLimpiar) {
            const $msg = $('#siigoHomologarMensaje').addClass('d-none').removeClass('alert-success alert-danger').text('');
            const $btnGuardar = $('#btnSiigoGuardarHomologacion').prop('disabled', true);
            const $btnQuitar = $('#btnSiigoQuitarHomologacion').prop('disabled', true);

            const payload = { siigo_code: codigo || null };
            if (estadoSiigo.tieneVariantes && estadoSiigo.varianteId) {
                payload.variante_id = estadoSiigo.varianteId;
            }

            $.ajax({
                url: URL_HOMOLOGACION_BASE + '/' + estadoSiigo.productoId + '/siigo/homologar',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                data: payload,
            })
            .done(function(resp) {
                $msg.removeClass('d-none').addClass('alert-success').text(resp.message || 'Guardado correctamente.');

                // Actualizar items en el carrito que correspondan
                const nuevoCodigo = resp.siigo_product_code || null;
                items.forEach(it => {
                    const matchVariante = estadoSiigo.varianteId
                        ? String(it.variante_producto_id) === String(estadoSiigo.varianteId)
                        : (!it.variante_producto_id && String(it.producto_id) === String(estadoSiigo.productoId));
                    const matchProducto = !estadoSiigo.varianteId && String(it.producto_id) === String(estadoSiigo.productoId);
                    if (matchVariante || matchProducto) {
                        it.siigo_product_code = nuevoCodigo;
                    }
                });
                if (typeof renderItems === 'function') renderItems();

                setTimeout(function() {
                    $('#modalSiigoHomologar').modal('hide');
                }, 600);
            })
            .fail(function(xhr) {
                const m = xhr.responseJSON?.message || 'Error al guardar la homologación.';
                $msg.removeClass('d-none').addClass('alert-danger').text(m);
            })
            .always(function() {
                $btnGuardar.prop('disabled', false);
                $btnQuitar.prop('disabled', false);
            });
        }

        function escapeHtml(s) {
            if (s === null || s === undefined) return '';
            return String(s)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }
    })();
    </script>
    @endpush

    {{-- Modal Homologación SIIGO --}}
    <div class="modal fade" id="modalSiigoHomologar" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-link-45deg"></i> Homologar producto con SIIGO</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="siigoHomologarInfo" class="mb-3"></div>

            <div class="mb-3">
              <label class="form-label fw-bold">Producto en SIIGO</label>
              <select id="selectSiigoProducto" class="form-select" style="width:100%"></select>
              <small class="text-muted">
                Busca por código, nombre o referencia. La lista se carga del catálogo de SIIGO.
              </small>
            </div>

            <div id="siigoHomologarMensaje" class="alert d-none"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-danger me-auto d-none" id="btnSiigoQuitarHomologacion">
              <i class="bi bi-x-circle"></i> Quitar homologación
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnSiigoGuardarHomologacion">
              <i class="bi bi-save"></i> Guardar
            </button>
          </div>
        </div>
      </div>
    </div>
</x-app-layout>
