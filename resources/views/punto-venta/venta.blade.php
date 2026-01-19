<x-app-layout>
    <x-slot name="header">
        Nueva Venta
        <span class="badge bg-primary ms-2">{{ $ubicacion->nombre }}</span>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            <div class="row">
                {{-- Panel izquierdo: Búsqueda y productos --}}
                <div class="col-lg-7">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Buscar Producto</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" id="buscarProducto" class="form-control"
                                               placeholder="Código, nombre o referencia..."
                                               autofocus autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Lista de Precios</label>
                                    <select id="listaPrecio" class="form-select form-select-lg">
                                        @foreach($listasPrecios as $lista)
                                            <option value="{{ $lista->id }}"
                                                {{ $listaPrecio && $listaPrecio->id == $lista->id ? 'selected' : '' }}>
                                                {{ $lista->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Resultados de búsqueda --}}
                            <div id="resultadosBusqueda" class="mt-3" style="display: none;">
                                <div class="list-group" id="listaProductos"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de items en la venta --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-cart3"></i> Productos en la Venta</h5>
                            <button type="button" class="btn btn-outline-light btn-sm" onclick="limpiarCarrito()"
                                    title="Limpiar todo">
                                <i class="bi bi-trash"></i> Limpiar
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="tablaCarrito">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40%;">Producto</th>
                                            <th class="text-center" style="width: 15%;">Precio</th>
                                            <th class="text-center" style="width: 15%;">Cantidad</th>
                                            <th class="text-center" style="width: 10%;">Desc.</th>
                                            <th class="text-end" style="width: 15%;">Subtotal</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsCarrito">
                                        <tr id="carritoVacio">
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="bi bi-cart-x fs-1"></i>
                                                <p class="mb-0">Busque y agregue productos</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panel derecho: Resumen y pago --}}
                <div class="col-lg-5">
                    {{-- Cliente (opcional) --}}
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-person"></i> Cliente (Opcional)</h6>
                        </div>
                        <div class="card-body">
                            <select id="clienteSelect" class="form-select">
                                <option value="">-- Cliente General --</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" data-lista="{{ $cliente->lista_precio_id }}">
                                        {{ $cliente->nombre_completo }} {{ $cliente->telefono ? '- '.$cliente->telefono : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-2" id="nombreClienteContainer" style="display: none;">
                                <input type="text" id="nombreCliente" class="form-control"
                                       placeholder="Nombre del cliente (opcional)">
                            </div>
                        </div>
                    </div>

                    {{-- Resumen --}}
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-receipt"></i> Resumen</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotalDisplay" class="fw-bold">$0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 align-items-center">
                                <span>Descuento:</span>
                                <div class="input-group" style="width: 150px;">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="descuentoGlobal" class="form-control text-end"
                                           value="0" min="0" step="100">
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="h4 mb-0">TOTAL:</span>
                                <span id="totalDisplay" class="h4 mb-0 text-success fw-bold">$0</span>
                            </div>
                        </div>
                    </div>

                    {{-- Método de pago --}}
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-credit-card"></i> Método de Pago</h6>
                        </div>
                        <div class="card-body">
                            <div class="btn-group w-100 mb-3" role="group">
                                <input type="radio" class="btn-check" name="metodoPago" id="pagoEfectivo"
                                       value="efectivo" checked>
                                <label class="btn btn-outline-success" for="pagoEfectivo">
                                    <i class="bi bi-cash"></i> Efectivo
                                </label>

                                <input type="radio" class="btn-check" name="metodoPago" id="pagoTarjeta"
                                       value="tarjeta">
                                <label class="btn btn-outline-primary" for="pagoTarjeta">
                                    <i class="bi bi-credit-card"></i> Tarjeta
                                </label>

                                <input type="radio" class="btn-check" name="metodoPago" id="pagoTransferencia"
                                       value="transferencia">
                                <label class="btn btn-outline-info" for="pagoTransferencia">
                                    <i class="bi bi-bank"></i> Transferencia
                                </label>

                                <input type="radio" class="btn-check" name="metodoPago" id="pagoMixto"
                                       value="mixto">
                                <label class="btn btn-outline-warning" for="pagoMixto">
                                    <i class="bi bi-wallet2"></i> Mixto
                                </label>
                            </div>

                            {{-- Montos para pago mixto --}}
                            <div id="montosMixto" style="display: none;">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <label class="form-label small">Efectivo</label>
                                        <input type="number" id="montoEfectivo" class="form-control"
                                               value="0" min="0" step="100">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small">Tarjeta</label>
                                        <input type="number" id="montoTarjeta" class="form-control"
                                               value="0" min="0" step="100">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small">Transf.</label>
                                        <input type="number" id="montoTransferencia" class="form-control"
                                               value="0" min="0" step="100">
                                    </div>
                                </div>
                                <div class="mt-2 text-end">
                                    <small>Total ingresado: <strong id="totalMixto">$0</strong></small>
                                </div>
                            </div>

                            {{-- Monto recibido (efectivo) --}}
                            <div id="calculoCambio" class="mt-3">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <label class="form-label">Recibido:</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="montoRecibido" class="form-control form-control-lg"
                                                   value="0" min="0" step="100">
                                        </div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <label class="form-label">Cambio:</label>
                                        <p id="cambioDisplay" class="h3 mb-0 text-primary">$0</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Notas --}}
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <textarea id="notasVenta" class="form-control" rows="2"
                                      placeholder="Notas de la venta (opcional)"></textarea>
                        </div>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="d-grid gap-2">
                        <button type="button" id="btnProcesarVenta" class="btn btn-success btn-lg" disabled>
                            <i class="bi bi-check-circle"></i> PROCESAR VENTA
                        </button>
                        <a href="{{ route('punto-venta.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal selección de variante --}}
    <div class="modal fade" id="modalVariantes" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Seleccionar Variante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="listaVariantes"></div>
            </div>
        </div>
    </div>

    {{-- Modal venta exitosa --}}
    <div class="modal fade" id="modalVentaExitosa" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    <h3 class="mt-3">Venta Exitosa</h3>
                    <p class="lead" id="mensajeVentaExitosa"></p>
                    <div class="d-grid gap-2 mt-4">
                        <a href="#" id="btnImprimirTicket" class="btn btn-primary btn-lg" target="_blank">
                            <i class="bi bi-printer"></i> Imprimir Ticket
                        </a>
                        <button type="button" class="btn btn-success btn-lg" onclick="nuevaVenta()">
                            <i class="bi bi-cart-plus"></i> Nueva Venta
                        </button>
                        <a href="{{ route('punto-venta.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Variables globales
        let carrito = [];
        let ubicacionId = {{ $ubicacionId }};
        let listaPrecioId = {{ $listaPrecio ? $listaPrecio->id : 'null' }};
        let timeoutBusqueda = null;

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            // Evento búsqueda de productos
            document.getElementById('buscarProducto').addEventListener('input', function() {
                clearTimeout(timeoutBusqueda);
                const termino = this.value.trim();

                if (termino.length < 2) {
                    document.getElementById('resultadosBusqueda').style.display = 'none';
                    return;
                }

                timeoutBusqueda = setTimeout(() => buscarProductos(termino), 300);
            });

            // Cambio de lista de precios
            document.getElementById('listaPrecio').addEventListener('change', function() {
                listaPrecioId = this.value;
                // Actualizar precios del carrito
                actualizarPreciosCarrito();
            });

            // Cambio de cliente
            document.getElementById('clienteSelect').addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                const listaCliente = option.dataset.lista;

                if (listaCliente) {
                    document.getElementById('listaPrecio').value = listaCliente;
                    listaPrecioId = listaCliente;
                    actualizarPreciosCarrito();
                }

                // Mostrar/ocultar campo nombre cliente
                document.getElementById('nombreClienteContainer').style.display =
                    this.value ? 'none' : 'block';
            });

            // Método de pago
            document.querySelectorAll('input[name="metodoPago"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const esMixto = this.value === 'mixto';
                    const esEfectivo = this.value === 'efectivo';

                    document.getElementById('montosMixto').style.display = esMixto ? 'block' : 'none';
                    document.getElementById('calculoCambio').style.display = esEfectivo ? 'block' : 'none';
                });
            });

            // Cálculo de cambio
            document.getElementById('montoRecibido').addEventListener('input', calcularCambio);
            document.getElementById('descuentoGlobal').addEventListener('input', actualizarTotales);

            // Montos mixtos
            ['montoEfectivo', 'montoTarjeta', 'montoTransferencia'].forEach(id => {
                document.getElementById(id).addEventListener('input', function() {
                    const total = parseFloat(document.getElementById('montoEfectivo').value || 0) +
                                  parseFloat(document.getElementById('montoTarjeta').value || 0) +
                                  parseFloat(document.getElementById('montoTransferencia').value || 0);
                    document.getElementById('totalMixto').textContent = '$' + formatNumber(total);
                });
            });

            // Procesar venta
            document.getElementById('btnProcesarVenta').addEventListener('click', procesarVenta);

            // Atajos de teclado
            document.addEventListener('keydown', function(e) {
                // F2 para enfocar búsqueda
                if (e.key === 'F2') {
                    e.preventDefault();
                    document.getElementById('buscarProducto').focus();
                }
                // F12 para procesar venta
                if (e.key === 'F12' && !document.getElementById('btnProcesarVenta').disabled) {
                    e.preventDefault();
                    procesarVenta();
                }
            });
        });

        // Buscar productos
        function buscarProductos(termino) {
            fetch(`{{ route('punto-venta.buscar-productos') }}?q=${encodeURIComponent(termino)}&lista_precio_id=${listaPrecioId}`)
                .then(response => response.json())
                .then(productos => {
                    const container = document.getElementById('listaProductos');
                    container.innerHTML = '';

                    if (productos.length === 0) {
                        container.innerHTML = '<div class="list-group-item text-muted">No se encontraron productos</div>';
                    } else {
                        productos.forEach(producto => {
                            const stockBadge = producto.controla_stock
                                ? `<span class="badge ${producto.stock_disponible > 0 ? 'bg-success' : 'bg-danger'}">${producto.stock_disponible} disp.</span>`
                                : '<span class="badge bg-secondary">Sin control</span>';

                            const item = document.createElement('a');
                            item.href = '#';
                            item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                            item.innerHTML = `
                                <div>
                                    <strong>${producto.referencia}</strong> - ${producto.nombre}
                                    ${producto.tiene_variantes ? '<span class="badge bg-info ms-1">Variantes</span>' : ''}
                                </div>
                                <div>
                                    ${stockBadge}
                                    <span class="ms-2 fw-bold">$${formatNumber(producto.precio)}</span>
                                </div>
                            `;
                            item.onclick = (e) => {
                                e.preventDefault();
                                if (producto.tiene_variantes) {
                                    mostrarVariantes(producto);
                                } else {
                                    agregarAlCarrito(producto);
                                }
                            };
                            container.appendChild(item);
                        });
                    }

                    document.getElementById('resultadosBusqueda').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error buscando productos:', error);
                });
        }

        // Mostrar variantes
        function mostrarVariantes(producto) {
            const container = document.getElementById('listaVariantes');
            container.innerHTML = '';

            producto.variantes.forEach(variante => {
                const stockBadge = `<span class="badge ${variante.stock_disponible > 0 ? 'bg-success' : 'bg-danger'}">${variante.stock_disponible} disp.</span>`;
                const precio = variante.precio || producto.precio;

                const btn = document.createElement('button');
                btn.className = 'btn btn-outline-primary w-100 mb-2 d-flex justify-content-between align-items-center';
                btn.innerHTML = `
                    <span>${variante.referencia_variante} ${variante.color ? '- ' + variante.color : ''}</span>
                    <span>${stockBadge} <strong>$${formatNumber(precio)}</strong></span>
                `;
                btn.onclick = () => {
                    agregarAlCarrito(producto, variante);
                    bootstrap.Modal.getInstance(document.getElementById('modalVariantes')).hide();
                };
                container.appendChild(btn);
            });

            new bootstrap.Modal(document.getElementById('modalVariantes')).show();
        }

        // Agregar al carrito
        function agregarAlCarrito(producto, variante = null) {
            const itemId = variante ? `${producto.id}-${variante.id}` : producto.id.toString();
            const existente = carrito.find(item => item.id === itemId);

            if (existente) {
                existente.cantidad++;
                renderizarCarrito();
                return;
            }

            const precio = variante ? (variante.precio || producto.precio) : producto.precio;
            const stockDisponible = variante ? variante.stock_disponible : producto.stock_disponible;

            carrito.push({
                id: itemId,
                producto_id: producto.id,
                variante_producto_id: variante ? variante.id : null,
                referencia: producto.referencia,
                nombre: producto.nombre + (variante ? ` - ${variante.referencia_variante}` : ''),
                precio_unitario: precio,
                cantidad: 1,
                descuento: 0,
                stock_disponible: stockDisponible,
                controla_stock: producto.controla_stock,
                permite_sin_stock: producto.permite_sin_stock
            });

            renderizarCarrito();
            document.getElementById('resultadosBusqueda').style.display = 'none';
            document.getElementById('buscarProducto').value = '';
            document.getElementById('buscarProducto').focus();
        }

        // Renderizar carrito
        function renderizarCarrito() {
            const tbody = document.getElementById('itemsCarrito');
            const carritoVacio = document.getElementById('carritoVacio');

            if (carrito.length === 0) {
                carritoVacio.style.display = '';
                document.getElementById('btnProcesarVenta').disabled = true;
                actualizarTotales();
                return;
            }

            carritoVacio.style.display = 'none';
            document.getElementById('btnProcesarVenta').disabled = false;

            // Limpiar filas excepto carritoVacio
            Array.from(tbody.querySelectorAll('tr:not(#carritoVacio)')).forEach(tr => tr.remove());

            carrito.forEach((item, index) => {
                const subtotal = (item.precio_unitario * item.cantidad) - item.descuento;
                const stockWarning = item.controla_stock && item.cantidad > item.stock_disponible && !item.permite_sin_stock;

                const tr = document.createElement('tr');
                tr.className = stockWarning ? 'table-warning' : '';
                tr.innerHTML = `
                    <td>
                        <strong>${item.referencia}</strong><br>
                        <small>${item.nombre}</small>
                        ${stockWarning ? '<br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Stock insuficiente</small>' : ''}
                    </td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm text-end"
                               value="${item.precio_unitario}" min="0" step="100"
                               onchange="actualizarPrecio(${index}, this.value)">
                    </td>
                    <td class="text-center">
                        <div class="input-group input-group-sm">
                            <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad(${index}, -1)">-</button>
                            <input type="number" class="form-control text-center" value="${item.cantidad}" min="1"
                                   onchange="setCantidad(${index}, this.value)" style="width: 60px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm text-end"
                               value="${item.descuento}" min="0" step="100"
                               onchange="actualizarDescuento(${index}, this.value)">
                    </td>
                    <td class="text-end fw-bold">$${formatNumber(subtotal)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarItem(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            actualizarTotales();
        }

        // Funciones del carrito
        function cambiarCantidad(index, delta) {
            carrito[index].cantidad = Math.max(1, carrito[index].cantidad + delta);
            renderizarCarrito();
        }

        function setCantidad(index, cantidad) {
            carrito[index].cantidad = Math.max(1, parseInt(cantidad) || 1);
            renderizarCarrito();
        }

        function actualizarPrecio(index, precio) {
            carrito[index].precio_unitario = parseFloat(precio) || 0;
            renderizarCarrito();
        }

        function actualizarDescuento(index, descuento) {
            carrito[index].descuento = parseFloat(descuento) || 0;
            renderizarCarrito();
        }

        function eliminarItem(index) {
            carrito.splice(index, 1);
            renderizarCarrito();
        }

        function limpiarCarrito() {
            if (carrito.length === 0) return;

            Swal.fire({
                title: '¿Limpiar carrito?',
                text: 'Se eliminarán todos los productos',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    carrito = [];
                    renderizarCarrito();
                }
            });
        }

        // Actualizar totales
        function actualizarTotales() {
            let subtotal = 0;
            carrito.forEach(item => {
                subtotal += (item.precio_unitario * item.cantidad) - item.descuento;
            });

            const descuentoGlobal = parseFloat(document.getElementById('descuentoGlobal').value) || 0;
            const total = subtotal - descuentoGlobal;

            document.getElementById('subtotalDisplay').textContent = '$' + formatNumber(subtotal);
            document.getElementById('totalDisplay').textContent = '$' + formatNumber(Math.max(0, total));

            calcularCambio();
        }

        // Calcular cambio
        function calcularCambio() {
            const total = parseFloat(document.getElementById('totalDisplay').textContent.replace(/[^0-9.-]/g, '')) || 0;
            const recibido = parseFloat(document.getElementById('montoRecibido').value) || 0;
            const cambio = recibido - total;

            document.getElementById('cambioDisplay').textContent = '$' + formatNumber(Math.max(0, cambio));
            document.getElementById('cambioDisplay').className = cambio >= 0 ? 'h3 mb-0 text-success' : 'h3 mb-0 text-danger';
        }

        // Actualizar precios del carrito
        function actualizarPreciosCarrito() {
            // Esto requeriría consultar los precios nuevamente - por simplicidad se mantienen
            // En una implementación completa, se consultarían los nuevos precios
        }

        // Procesar venta
        function procesarVenta() {
            if (carrito.length === 0) {
                Swal.fire('Error', 'Agregue productos al carrito', 'warning');
                return;
            }

            const metodoPago = document.querySelector('input[name="metodoPago"]:checked').value;
            const total = parseFloat(document.getElementById('totalDisplay').textContent.replace(/[^0-9.-]/g, '')) || 0;

            // Validar pago mixto
            if (metodoPago === 'mixto') {
                const totalMixto = parseFloat(document.getElementById('montoEfectivo').value || 0) +
                                   parseFloat(document.getElementById('montoTarjeta').value || 0) +
                                   parseFloat(document.getElementById('montoTransferencia').value || 0);

                if (totalMixto < total) {
                    Swal.fire('Error', 'El monto ingresado es menor al total', 'warning');
                    return;
                }
            }

            // Validar efectivo
            if (metodoPago === 'efectivo') {
                const recibido = parseFloat(document.getElementById('montoRecibido').value) || 0;
                if (recibido < total) {
                    Swal.fire('Error', 'El monto recibido es menor al total', 'warning');
                    return;
                }
            }

            const datos = {
                ubicacion_id: ubicacionId,
                cliente_id: document.getElementById('clienteSelect').value || null,
                nombre_cliente: document.getElementById('nombreCliente').value || null,
                descuento: parseFloat(document.getElementById('descuentoGlobal').value) || 0,
                metodo_pago: metodoPago,
                monto_efectivo: metodoPago === 'mixto' ? parseFloat(document.getElementById('montoEfectivo').value) || 0 :
                               (metodoPago === 'efectivo' ? total : null),
                monto_tarjeta: metodoPago === 'mixto' ? parseFloat(document.getElementById('montoTarjeta').value) || 0 :
                              (metodoPago === 'tarjeta' ? total : null),
                monto_transferencia: metodoPago === 'mixto' ? parseFloat(document.getElementById('montoTransferencia').value) || 0 :
                                    (metodoPago === 'transferencia' ? total : null),
                notas: document.getElementById('notasVenta').value || null,
                items: carrito.map(item => ({
                    producto_id: item.producto_id,
                    variante_producto_id: item.variante_producto_id,
                    cantidad: item.cantidad,
                    precio_unitario: item.precio_unitario,
                    descuento: item.descuento
                }))
            };

            document.getElementById('btnProcesarVenta').disabled = true;
            document.getElementById('btnProcesarVenta').innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';

            fetch('{{ route("punto-venta.procesar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(datos)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('mensajeVentaExitosa').textContent = data.message;
                    document.getElementById('btnImprimirTicket').href = `{{ url('punto-venta') }}/${data.venta_id}/ticket`;
                    new bootstrap.Modal(document.getElementById('modalVentaExitosa')).show();
                } else {
                    Swal.fire('Error', data.message, 'error');
                    document.getElementById('btnProcesarVenta').disabled = false;
                    document.getElementById('btnProcesarVenta').innerHTML = '<i class="bi bi-check-circle"></i> PROCESAR VENTA';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Error al procesar la venta', 'error');
                document.getElementById('btnProcesarVenta').disabled = false;
                document.getElementById('btnProcesarVenta').innerHTML = '<i class="bi bi-check-circle"></i> PROCESAR VENTA';
            });
        }

        // Nueva venta
        function nuevaVenta() {
            carrito = [];
            renderizarCarrito();
            document.getElementById('clienteSelect').value = '';
            document.getElementById('nombreCliente').value = '';
            document.getElementById('descuentoGlobal').value = 0;
            document.getElementById('notasVenta').value = '';
            document.getElementById('montoRecibido').value = 0;
            document.getElementById('pagoEfectivo').checked = true;
            document.getElementById('montosMixto').style.display = 'none';
            document.getElementById('calculoCambio').style.display = 'block';
            document.getElementById('btnProcesarVenta').disabled = true;
            document.getElementById('btnProcesarVenta').innerHTML = '<i class="bi bi-check-circle"></i> PROCESAR VENTA';

            bootstrap.Modal.getInstance(document.getElementById('modalVentaExitosa')).hide();
            document.getElementById('buscarProducto').focus();
        }

        // Formatear números
        function formatNumber(num) {
            return new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(num);
        }
    </script>
    @endpush
</x-app-layout>
