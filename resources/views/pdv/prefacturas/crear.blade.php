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
                    <div class="col-md-4">
                        <select id="listaPrecio" class="form-select" required>
                            @foreach($listasPrecios as $lp)
                                <option value="{{ $lp->id }}">{{ $lp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="ubicacion" class="form-select" required>
                            @foreach($ubicaciones as $ub)
                                <option value="{{ $ub->id }}">{{ $ub->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 position-relative">
                        <input type="text" id="buscarCliente" class="form-control" placeholder="Buscar cliente (opcional)">
                        <div id="resultadosCliente" class="search-results bg-white rounded-bottom d-none"></div>
                    </div>
                </div>

                <div id="clienteInfo" class="mb-3 d-none">
                    <div class="alert alert-info py-2 d-flex justify-content-between align-items-center mb-0">
                        <span><strong id="clienteNombre"></strong> <small class="text-muted" id="clienteDoc"></small></span>
                        <button class="btn btn-sm btn-outline-danger" onclick="quitarCliente()"><i class="bi bi-x"></i></button>
                    </div>
                </div>

                <div class="position-relative mb-3">
                    <input type="text" id="buscarProducto" class="form-control form-control-lg" placeholder="Buscar producto por nombre o referencia...">
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
                                    <th class="text-center">Desc %</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr><td colspan="7" class="text-center text-muted py-4">Agregue productos a la prefactura</td></tr>
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

    @push('scripts')
    <script>
        let items = [];
        let clienteSeleccionado = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const descuentoMaximo = {{ $descuentoMaximo ?? 15 }};
        let searchTimeout;

        // Product search — endpoint devuelve filas atómicas (una por variante o producto sin variantes).
        // Auto-agrega si el input es un código de barras con match exacto (lector no siempre manda Enter).
        document.getElementById('buscarProducto').addEventListener('input', function() {
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

        function addProduct(fila) {
            document.getElementById('resultadosBusqueda').classList.add('d-none');
            document.getElementById('buscarProducto').value = '';
            const precio = parseFloat(fila.precio) || 0;
            items.push({
                producto_id: fila.producto_id,
                nombre: fila.nombre_producto,
                referencia: fila.referencia,
                variante_producto_id: fila.variante_producto_id || null,
                variante_nombre: fila.nombre_variante || '-',
                cantidad: 1,
                precio_unitario: precio,
                precio_original: precio,
                descuento_porcentaje: 0,
                descuento_valor: 0,
                stock_disponible: fila.stock_disponible ?? null,
            });
            renderItems();
        }

        function cambiarCantidad(index, valor) {
            const max = items[index].stock_disponible || 999999;
            const cantidad = Math.min(max, Math.max(1, parseInt(valor) || 1));
            if (parseInt(valor) > max) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: `Stock disponible: ${max} unidades`, showConfirmButton: false, timer: 2000 });
            }
            items[index].cantidad = cantidad;
            renderItems();
        }

        function cambiarDescuento(index, valor) {
            const porcentaje = Math.min(descuentoMaximo, Math.max(0, parseFloat(valor) || 0));
            if (parseFloat(valor) > descuentoMaximo) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: `Descuento máximo permitido: ${descuentoMaximo}%`, showConfirmButton: false, timer: 2000 });
            }
            items[index].descuento_porcentaje = porcentaje;
            items[index].descuento_valor = (items[index].precio_unitario * items[index].cantidad) * (porcentaje / 100);
            renderItems();
        }

        function renderItems() {
            const tbody = document.getElementById('itemsBody');
            if (items.length === 0) { tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Agregue productos</td></tr>'; updateTotals(); return; }
            tbody.innerHTML = items.map((item, i) => {
                const sub = (item.precio_unitario * item.cantidad) - item.descuento_valor;
                const maxStock = item.stock_disponible || 999999;
                return `<tr>
                    <td><button class="btn btn-sm btn-outline-danger border-0" onclick="items.splice(${i},1);renderItems();"><i class="bi bi-trash"></i></button></td>
                    <td>${item.nombre} <small class="text-muted d-block">${item.referencia}</small></td>
                    <td><small>${item.variante_nombre}</small></td>
                    <td class="text-center"><input type="number" class="form-control form-control-sm" style="width:70px;" value="${item.cantidad}" min="1" max="${maxStock}" onchange="cambiarCantidad(${i}, this.value)"> <small class="text-muted">Stock: ${item.stock_disponible || '?'}</small></td>
                    <td class="text-end">$${item.precio_unitario.toFixed(2)}</td>
                    <td class="text-center"><input type="number" class="form-control form-control-sm" style="width:70px;" value="${item.descuento_porcentaje}" min="0" max="${descuentoMaximo}" onchange="cambiarDescuento(${i}, this.value)"></td>
                    <td class="text-end fw-semibold">$${sub.toFixed(2)}</td>
                </tr>`;
            }).join('');
            updateTotals();
        }

        function updateTotals() {
            const subtotal = items.reduce((s,i) => s + (i.precio_unitario * i.cantidad) - i.descuento_valor, 0);
            document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('totalDisplay').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('btnGuardar').disabled = items.length === 0;
        }

        // Client search
        document.getElementById('buscarCliente').addEventListener('input', function() {
            const q = this.value.trim(); const div = document.getElementById('resultadosCliente');
            if (q.length < 2) { div.classList.add('d-none'); return; }
            fetch(`{{ route('pdv.ajax.buscar-clientes') }}?q=${encodeURIComponent(q)}`).then(r => r.json()).then(clientes => {
                div.innerHTML = clientes.map(c => `<div class="search-result-item p-2 border-bottom" onclick="selCliente(${c.id},'${c.nombre.replace(/'/g,"")}','${c.documento||""}',${c.lista_precio_id||'null'})">${c.nombre} <small class="text-muted">${c.documento||''}</small></div>`).join('');
                div.classList.remove('d-none');
            });
        });

        window.selCliente = function(id, nombre, doc, lpId) {
            clienteSeleccionado = { id, nombre, documento: doc, lista_precio_id: lpId };
            document.getElementById('clienteInfo').classList.remove('d-none');
            document.getElementById('clienteNombre').textContent = nombre;
            document.getElementById('clienteDoc').textContent = doc;
            document.getElementById('resultadosCliente').classList.add('d-none');
            document.getElementById('buscarCliente').value = '';
            if (lpId) document.getElementById('listaPrecio').value = lpId;
        };

        function quitarCliente() { clienteSeleccionado = null; document.getElementById('clienteInfo').classList.add('d-none'); }

        function guardarPrefactura() {
            if (items.length === 0) return;
            document.getElementById('btnGuardar').disabled = true;
            fetch('{{ route("pdv.prefacturas.guardar") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({
                    lista_precio_id: document.getElementById('listaPrecio').value,
                    ubicacion_id: document.getElementById('ubicacion').value,
                    cliente_id: clienteSeleccionado?.id || null,
                    nombre_cliente: clienteSeleccionado?.nombre || null,
                    observaciones: document.getElementById('observaciones').value,
                    items: items.map(i => ({ producto_id: i.producto_id, variante_producto_id: i.variante_producto_id, cantidad: i.cantidad, precio_unitario: i.precio_unitario, precio_original: i.precio_original, descuento_porcentaje: i.descuento_porcentaje, descuento_valor: i.descuento_valor })),
                }),
            }).then(r => r.json()).then(data => {
                if (data.exito) {
                    Swal.fire('Creada', data.mensaje, 'success').then(() => window.location.href = '{{ route("pdv.prefacturas.index") }}');
                } else {
                    Swal.fire('Error', data.mensaje, 'error');
                    document.getElementById('btnGuardar').disabled = false;
                }
            }).catch(() => { Swal.fire('Error', 'Error de conexión', 'error'); document.getElementById('btnGuardar').disabled = false; });
        }
    </script>
    @endpush
</x-app-layout>
