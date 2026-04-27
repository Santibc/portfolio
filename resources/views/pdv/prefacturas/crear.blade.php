<x-app-layout>
    @section('title', 'Crear Prefactura')

    @push('styles')
    <style>
        .search-results { position: absolute; z-index: 1050; width: 100%; max-height: 300px; overflow-y: auto; box-shadow: 0 4px 15px rgba(0,0,0,.15); }
        .search-result-item { cursor: pointer; }
        .search-result-item:hover { background: var(--miracle-pink-light) !important; }
    </style>
    @endpush

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('pdv.prefacturas.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Crear Prefactura</h4>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <select id="listaPrecio" class="form-select" required>
                            @foreach($listasPrecios as $lp)
                                <option value="{{ $lp->id }}">{{ $lp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="ubicacion" class="form-select" required>
                            @foreach($ubicaciones as $ub)
                                <option value="{{ $ub->id }}">{{ $ub->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="vendedoraPrefactura" class="form-select" required>
                            <option value="">Vendedora...</option>
                            @foreach($vendedorasPrefactura as $vendedora)
                                <option value="{{ $vendedora }}">{{ $vendedora }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 position-relative">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                            <input type="text" id="buscarCliente" class="form-control" placeholder="CC, NIT, Nombre, Tel, Email..." autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" onclick="mostrarModalNuevoCliente()" title="Nuevo cliente">
                                <i class="bi bi-person-plus"></i>
                            </button>
                        </div>
                        <div id="resultadosCliente" class="search-results bg-white rounded-bottom d-none"></div>
                    </div>
                </div>

                <div id="clienteInfo" class="mb-3 d-none">
                    <div class="card border-0 shadow-sm">
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

                <div class="position-relative mb-3">
                    <input type="text" id="buscarProducto" class="form-control form-control-lg" placeholder="Buscar producto por nombre o referencia..." autocomplete="off">
                    <div id="resultadosBusqueda" class="search-results bg-white rounded-bottom d-none"></div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0" id="tablaItems">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px;"></th>
                                    <th>Producto</th>
                                    <th>Variante</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr><td colspan="6" class="text-center text-muted py-4">Agregue productos a la prefactura</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body" style="background: #f8f9fa; border-radius: .5rem;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-semibold" id="subtotalDisplay">$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fs-5 fw-bold">TOTAL:</span>
                            <span class="fs-4 fw-bold" style="color: var(--miracle-pink);" id="totalDisplay">$0.00</span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <textarea id="observaciones" class="form-control" rows="3" placeholder="Observaciones (opcional)"></textarea>
                </div>

                <button class="btn w-100 py-3 text-white" style="background: var(--miracle-pink);" onclick="guardarPrefactura()" id="btnGuardar" disabled>
                    <i class="bi bi-check-circle me-2"></i>Crear Prefactura
                </button>
            </div>
        </div>
    </div>

    {{-- Modal: Nuevo Cliente --}}
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
                    <button type="button" class="btn text-white" style="background: var(--miracle-pink);" onclick="guardarNuevoCliente()">
                        <i class="bi bi-check me-1"></i>Guardar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let items = [];
        let clienteSeleccionado = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let searchTimeout;

        const buscarInput = document.getElementById('buscarProducto');

        // Autofocus al cargar: lector de códigos de barras listo para escribir
        if (buscarInput) {
            buscarInput.focus();
        }

        // Product search — endpoint devuelve filas atómicas (una por variante o producto sin variantes).
        // Auto-agrega si el input es un código de barras con match exacto (lector no siempre manda Enter).
        buscarInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            const div = document.getElementById('resultadosBusqueda');
            if (q.length < 2) { div.classList.add('d-none'); return; }

            const pareceCodigoBarras = /^\d{6,}$/.test(q);
            const delay = pareceCodigoBarras ? 120 : 300;

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('pdv.ajax.buscar-productos') }}?q=${encodeURIComponent(q)}&lista_precio_id=${document.getElementById('listaPrecio').value}&ubicacion_id=${document.getElementById('ubicacion').value}`)
                    .then(r => r.json())
                    .then(filas => {
                        if (pareceCodigoBarras) {
                            const match = filas.find(f => f.codigo_barras && String(f.codigo_barras).trim() === q);
                            if (match) {
                                addProduct(match);
                                return;
                            }
                        }
                        div.innerHTML = filas.length === 0
                            ? '<div class="p-3 text-center text-muted">No encontrado</div>'
                            : filas.map(f => {
                                const subRef = f.codigo_barras ? `${f.referencia} · <code>${f.codigo_barras}</code>` : f.referencia;
                                return `<div class="search-result-item p-2 border-bottom" onclick='addProduct(${JSON.stringify(f).replace(/'/g,"&#39;")})'>
                                    <strong>${f.nombre_completo}</strong> <small class="text-muted d-block">${subRef}</small>
                                    <span class="float-end fw-bold">$${parseFloat(f.precio||0).toFixed(2)}</span></div>`;
                            }).join('');
                        div.classList.remove('d-none');
                    });
            }, delay);
        });

        // Enter del lector de códigos de barras: si hay match exacto, agregar directo
        buscarInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('resultadosBusqueda').classList.add('d-none');
                return;
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                const q = this.value.trim();
                if (q.length < 2) return;

                fetch(`{{ route('pdv.ajax.buscar-productos') }}?q=${encodeURIComponent(q)}&lista_precio_id=${document.getElementById('listaPrecio').value}&ubicacion_id=${document.getElementById('ubicacion').value}`)
                    .then(r => r.json())
                    .then(filas => {
                        const match = filas.find(f => f.codigo_barras && String(f.codigo_barras).trim() === q);
                        if (match) {
                            addProduct(match);
                        }
                    });
            }
        });

        function addProduct(fila) {
            document.getElementById('resultadosBusqueda').classList.add('d-none');
            buscarInput.value = '';

            const varianteId = fila.variante_producto_id || null;
            const existente = items.findIndex(i =>
                i.producto_id === fila.producto_id &&
                (i.variante_producto_id || null) === varianteId
            );

            if (existente >= 0) {
                const max = items[existente].stock_disponible || 999999;
                if (items[existente].cantidad + 1 > max) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: `Stock disponible: ${max} unidades`, showConfirmButton: false, timer: 2000 });
                } else {
                    items[existente].cantidad++;
                }
                renderItems();
                buscarInput.focus();
                return;
            }

            const precio = parseFloat(fila.precio) || 0;
            items.push({
                producto_id: fila.producto_id,
                nombre: fila.nombre_producto,
                referencia: fila.referencia,
                variante_producto_id: varianteId,
                variante_nombre: fila.nombre_variante || '-',
                cantidad: 1,
                precio_unitario: precio,
                precio_original: precio,
                stock_disponible: fila.stock_disponible ?? null,
            });
            renderItems();
            buscarInput.focus();
        }

        // Cambio de lista de precios: actualiza precios de los productos ya agregados
        document.getElementById('listaPrecio').addEventListener('change', function() {
            if (items.length === 0) return;

            const listaPrecioId = this.value;
            const payload = items.map(i => ({
                producto_id: i.producto_id,
                variante_producto_id: i.variante_producto_id,
            }));

            fetch('{{ route("pdv.ajax.obtener-precios") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
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
                        (i.variante_producto_id || null) == (p.variante_producto_id || null)
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

        function cambiarCantidad(index, valor) {
            const max = items[index].stock_disponible || 999999;
            const cantidad = Math.min(max, Math.max(1, parseInt(valor) || 1));
            if (parseInt(valor) > max) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: `Stock disponible: ${max} unidades`, showConfirmButton: false, timer: 2000 });
            }
            items[index].cantidad = cantidad;
            renderItems();
        }

        function renderItems() {
            const tbody = document.getElementById('itemsBody');
            if (items.length === 0) { tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Agregue productos</td></tr>'; updateTotals(); return; }
            tbody.innerHTML = items.map((item, i) => {
                const sub = item.precio_unitario * item.cantidad;
                const maxStock = item.stock_disponible || 999999;
                return `<tr>
                    <td><button class="btn btn-sm btn-outline-danger border-0" onclick="items.splice(${i},1);renderItems();"><i class="bi bi-trash"></i></button></td>
                    <td>${item.nombre} <small class="text-muted d-block">${item.referencia}</small></td>
                    <td><small>${item.variante_nombre}</small></td>
                    <td class="text-center"><input type="number" class="form-control form-control-sm" style="width:70px;" value="${item.cantidad}" min="1" max="${maxStock}" onchange="cambiarCantidad(${i}, this.value)"> <small class="text-muted">Stock: ${item.stock_disponible || '?'}</small></td>
                    <td class="text-end">$${item.precio_unitario.toFixed(2)}</td>
                    <td class="text-end fw-semibold">$${sub.toFixed(2)}</td>
                </tr>`;
            }).join('');
            updateTotals();
        }

        function updateTotals() {
            const subtotal = items.reduce((s,i) => s + (i.precio_unitario * i.cantidad), 0);
            document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('totalDisplay').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('btnGuardar').disabled = items.length === 0;
        }

        // Client search
        const buscarClienteInput = document.getElementById('buscarCliente');
        let clienteSearchTimeout;

        buscarClienteInput.addEventListener('input', function() {
            clearTimeout(clienteSearchTimeout);
            const q = this.value.trim();
            const div = document.getElementById('resultadosCliente');
            if (q.length < 2) { div.classList.add('d-none'); return; }

            clienteSearchTimeout = setTimeout(() => {
                fetch(`{{ route('pdv.ajax.buscar-clientes') }}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(clientes => {
                        if (clientes.length === 0) {
                            div.innerHTML = '<div class="p-3 text-center text-muted">No se encontraron clientes</div>';
                        } else {
                            div.innerHTML = clientes.map(c => `
                                <div class="search-result-item p-2 border-bottom" onclick='asignarCliente(${JSON.stringify(c).replace(/'/g, "&#39;")})'>
                                    <strong>${c.nombre}</strong>
                                    <small class="text-muted d-block">${c.documento || ''} ${c.telefono ? '· ' + c.telefono : ''}</small>
                                    <small class="text-muted">${c.lista_precio_nombre || ''}</small>
                                </div>
                            `).join('');
                        }
                        div.classList.remove('d-none');
                    });
            }, 300);
        });

        // Cerrar resultados al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!buscarClienteInput.contains(e.target)) {
                document.getElementById('resultadosCliente').classList.add('d-none');
            }
        });

        function asignarCliente(cliente) {
            clienteSeleccionado = cliente;
            document.getElementById('clienteInfo').classList.remove('d-none');
            document.getElementById('clienteNombre').textContent = cliente.nombre;
            document.getElementById('clienteDocumento').textContent = cliente.documento || '';
            document.getElementById('clienteTelefono').textContent = cliente.telefono || '';
            document.getElementById('clienteListaPrecio').textContent = cliente.lista_precio_nombre || '';
            document.getElementById('resultadosCliente').classList.add('d-none');
            buscarClienteInput.value = '';

            if (cliente.lista_precio_id) {
                const select = document.getElementById('listaPrecio');
                const optionExists = Array.from(select.options).some(o => o.value == cliente.lista_precio_id);
                if (!optionExists) {
                    const opt = document.createElement('option');
                    opt.value = cliente.lista_precio_id;
                    opt.textContent = (cliente.lista_precio_nombre || 'Lista cliente') + ' (del cliente)';
                    select.appendChild(opt);
                }
                if (select.value != cliente.lista_precio_id) {
                    select.value = cliente.lista_precio_id;
                    select.dispatchEvent(new Event('change'));
                }
            }
        }

        function quitarCliente() {
            clienteSeleccionado = null;
            document.getElementById('clienteInfo').classList.add('d-none');
        }

        function mostrarModalNuevoCliente() {
            document.getElementById('nuevoClienteNombre').value = '';
            document.getElementById('nuevoClienteDocumento').value = '';
            document.getElementById('nuevoClienteTelefono').value = '';
            document.getElementById('nuevoClienteEmail').value = '';
            new bootstrap.Modal(document.getElementById('modalNuevoCliente')).show();
        }

        function guardarNuevoCliente() {
            const nombre = document.getElementById('nuevoClienteNombre').value.trim();
            if (!nombre) { Swal.fire('Error', 'El nombre es obligatorio', 'warning'); return; }

            fetch('{{ route("pdv.ajax.cliente-rapido") }}', {
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
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Cliente creado', showConfirmButton: false, timer: 1500 });
            })
            .catch(err => Swal.fire('Error', err.message || 'No se pudo crear el cliente', 'error'));
        }

        function guardarPrefactura() {
            if (items.length === 0) return;
            const vendedora = document.getElementById('vendedoraPrefactura').value;
            if (!vendedora) {
                Swal.fire('Falta vendedora', 'Seleccione la vendedora antes de crear la prefactura', 'warning');
                return;
            }
            document.getElementById('btnGuardar').disabled = true;
            fetch('{{ route("pdv.prefacturas.guardar") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({
                    lista_precio_id: document.getElementById('listaPrecio').value,
                    ubicacion_id: document.getElementById('ubicacion').value,
                    cliente_id: clienteSeleccionado?.id || null,
                    nombre_cliente: clienteSeleccionado?.nombre || null,
                    vendedora_prefactura: vendedora,
                    observaciones: document.getElementById('observaciones').value,
                    items: items.map(i => ({ producto_id: i.producto_id, variante_producto_id: i.variante_producto_id, cantidad: i.cantidad, precio_unitario: i.precio_unitario, precio_original: i.precio_original })),
                }),
            }).then(r => r.json()).then(data => {
                if (data.exito) {
                    Swal.fire('Creada', data.mensaje, 'success').then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.mensaje, 'error');
                    document.getElementById('btnGuardar').disabled = false;
                }
            }).catch(() => { Swal.fire('Error', 'Error de conexión', 'error'); document.getElementById('btnGuardar').disabled = false; });
        }
    </script>
    @endpush
</x-app-layout>
