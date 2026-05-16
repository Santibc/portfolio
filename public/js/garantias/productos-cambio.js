/**
 * Módulo reutilizable para "Productos de cambio" en liberación de garantías.
 * Uso: const instancia = window.GarantiaProductosCambio(prefix); instancia.init();
 *
 * Cada modal de liberación pasa un prefix distinto ('index', 'cotizacion', 'solicitud')
 * para soportar múltiples instancias coexistentes en una misma página.
 */
window.GarantiaProductosCambio = (function () {
    const instancias = {};

    function crearInstancia(prefix) {
        const ids = {
            container: 'garProdContainer_' + prefix,
            body: 'garProdBody_' + prefix,
            toggle: '[data-gar-prod-toggle="' + prefix + '"]',
            ubicacion: 'garProdUbicacion_' + prefix,
            producto: 'garProdProducto_' + prefix,
            varianteWrap: 'garProdVarianteWrap_' + prefix,
            variante: 'garProdVariante_' + prefix,
            cantidad: 'garProdCantidad_' + prefix,
            stockInfo: 'garProdStockInfo_' + prefix,
            agregar: 'garProdAgregar_' + prefix,
            tabla: 'garProdTabla_' + prefix,
        };

        const state = {
            items: [],
            ubicacionId: null,
            productos: [],
            productoActual: null,
            varianteActual: null,
            stockDisponibleSel: 0,
        };

        const $ = (id) => document.getElementById(id);

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function renderTabla() {
            const tbody = $(ids.tabla).querySelector('tbody');
            if (state.items.length === 0) {
                tbody.innerHTML = '<tr class="text-muted text-center" data-empty-row><td colspan="4" class="py-2">Sin productos agregados</td></tr>';
                return;
            }
            tbody.innerHTML = state.items.map((item, idx) => `
                <tr>
                    <td>${escapeHtml(item.producto_nombre)}</td>
                    <td>${escapeHtml(item.variante_nombre || '—')}</td>
                    <td class="text-center">${item.cantidad}</td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger" data-remove-idx="${idx}"><i class="bi bi-trash"></i></button></td>
                </tr>
            `).join('');
            tbody.querySelectorAll('[data-remove-idx]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const i = parseInt(this.dataset.removeIdx, 10);
                    state.items.splice(i, 1);
                    renderTabla();
                });
            });
        }

        function refrescarBotonAgregar() {
            const productoOk = !!state.productoActual;
            const necesitaVariante = state.productoActual && state.productoActual.tiene_variantes;
            const varianteOk = !necesitaVariante || !!state.varianteActual;
            const cantidad = parseInt($(ids.cantidad).value || '0', 10);
            const cantOk = cantidad >= 1 && cantidad <= state.stockDisponibleSel;
            $(ids.agregar).disabled = !(productoOk && varianteOk && cantOk);
        }

        function actualizarStockInfo() {
            const info = $(ids.stockInfo);
            if (state.productoActual) {
                info.textContent = 'Disponible: ' + state.stockDisponibleSel;
                $(ids.cantidad).max = state.stockDisponibleSel;
            } else {
                info.textContent = '';
                $(ids.cantidad).removeAttribute('max');
            }
        }

        async function cargarProductos(ubicacionId) {
            const selProd = $(ids.producto);
            selProd.innerHTML = '<option value="">Cargando...</option>';
            selProd.disabled = true;

            try {
                const res = await fetch('/garantias/productos-por-ubicacion/' + encodeURIComponent(ubicacionId), {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Error al cargar productos');
                const data = await res.json();
                state.productos = data.productos || [];

                if (state.productos.length === 0) {
                    selProd.innerHTML = '<option value="">Sin productos con stock en esta ubicación</option>';
                    selProd.disabled = true;
                    return;
                }
                selProd.innerHTML = '<option value="">Selecciona un producto...</option>' +
                    state.productos.map(p => `<option value="${p.id}" data-tiene-variantes="${p.tiene_variantes ? '1' : '0'}" data-stock="${p.stock_disponible}">${escapeHtml(p.nombre)} (${escapeHtml(p.referencia || '')}) — Stock: ${p.stock_disponible}</option>`).join('');
                selProd.disabled = false;
            } catch (err) {
                selProd.innerHTML = '<option value="">Error al cargar</option>';
                selProd.disabled = true;
            }
        }

        async function cargarVariantes(productoId, ubicacionId) {
            const selVar = $(ids.variante);
            const wrap = $(ids.varianteWrap);
            selVar.innerHTML = '<option value="">Cargando...</option>';
            wrap.style.display = 'block';
            try {
                const res = await fetch('/garantias/variantes-por-ubicacion/' + encodeURIComponent(productoId) + '/' + encodeURIComponent(ubicacionId), {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Error al cargar variantes');
                const data = await res.json();
                if (!data.tiene_variantes) {
                    wrap.style.display = 'none';
                    return;
                }
                if ((data.variantes || []).length === 0) {
                    selVar.innerHTML = '<option value="">Sin variantes con stock</option>';
                    return;
                }
                selVar.innerHTML = '<option value="">Selecciona una variante...</option>' +
                    data.variantes.map(v => `<option value="${v.id}" data-stock="${v.stock_disponible}">${escapeHtml(v.nombre_variante || '')} — Stock: ${v.stock_disponible}</option>`).join('');
            } catch (err) {
                selVar.innerHTML = '<option value="">Error al cargar</option>';
            }
        }

        function init() {
            const container = $(ids.container);
            if (!container) return;

            // Toggle mostrar/ocultar
            document.querySelectorAll(ids.toggle).forEach(btn => {
                btn.addEventListener('click', function () {
                    const body = $(ids.body);
                    const visible = body.style.display !== 'none';
                    body.style.display = visible ? 'none' : 'block';
                    this.innerHTML = visible
                        ? '<i class="bi bi-chevron-down"></i> Mostrar'
                        : '<i class="bi bi-chevron-up"></i> Ocultar';
                });
            });

            $(ids.ubicacion).addEventListener('change', async function () {
                state.ubicacionId = this.value || null;
                state.productoActual = null;
                state.varianteActual = null;
                state.stockDisponibleSel = 0;
                $(ids.varianteWrap).style.display = 'none';
                actualizarStockInfo();
                refrescarBotonAgregar();
                if (state.ubicacionId) {
                    await cargarProductos(state.ubicacionId);
                } else {
                    $(ids.producto).innerHTML = '<option value="">Selecciona ubicación primero</option>';
                    $(ids.producto).disabled = true;
                }
            });

            $(ids.producto).addEventListener('change', async function () {
                const opt = this.options[this.selectedIndex];
                if (!this.value) {
                    state.productoActual = null;
                    state.varianteActual = null;
                    state.stockDisponibleSel = 0;
                    $(ids.varianteWrap).style.display = 'none';
                } else {
                    state.productoActual = {
                        id: this.value,
                        nombre: opt.textContent,
                        tiene_variantes: opt.dataset.tieneVariantes === '1',
                        stock: parseInt(opt.dataset.stock, 10) || 0,
                    };
                    state.varianteActual = null;
                    if (state.productoActual.tiene_variantes) {
                        state.stockDisponibleSel = 0;
                        await cargarVariantes(state.productoActual.id, state.ubicacionId);
                    } else {
                        $(ids.varianteWrap).style.display = 'none';
                        state.stockDisponibleSel = state.productoActual.stock;
                    }
                }
                actualizarStockInfo();
                refrescarBotonAgregar();
            });

            $(ids.variante).addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                if (!this.value) {
                    state.varianteActual = null;
                    state.stockDisponibleSel = 0;
                } else {
                    state.varianteActual = {
                        id: this.value,
                        nombre: opt.textContent.split(' — Stock:')[0].trim(),
                        stock: parseInt(opt.dataset.stock, 10) || 0,
                    };
                    state.stockDisponibleSel = state.varianteActual.stock;
                }
                actualizarStockInfo();
                refrescarBotonAgregar();
            });

            $(ids.cantidad).addEventListener('input', refrescarBotonAgregar);

            $(ids.agregar).addEventListener('click', function () {
                if (this.disabled) return;
                const cantidad = parseInt($(ids.cantidad).value, 10);
                const item = {
                    producto_id: parseInt(state.productoActual.id, 10),
                    producto_nombre: state.productoActual.nombre.replace(/ \(.*$/, ''),
                    variante_producto_id: state.varianteActual ? parseInt(state.varianteActual.id, 10) : null,
                    variante_nombre: state.varianteActual ? state.varianteActual.nombre : null,
                    cantidad: cantidad,
                };
                state.items.push(item);
                renderTabla();

                // Reset selección
                $(ids.producto).value = '';
                state.productoActual = null;
                state.varianteActual = null;
                state.stockDisponibleSel = 0;
                $(ids.varianteWrap).style.display = 'none';
                $(ids.cantidad).value = 1;
                actualizarStockInfo();
                refrescarBotonAgregar();
            });
        }

        function getPayload() {
            if (state.items.length === 0) return {};
            return {
                ubicacion_id: parseInt(state.ubicacionId, 10),
                items: state.items.map(i => ({
                    producto_id: i.producto_id,
                    variante_producto_id: i.variante_producto_id,
                    cantidad: i.cantidad,
                })),
            };
        }

        function reset() {
            state.items = [];
            state.ubicacionId = null;
            state.productoActual = null;
            state.varianteActual = null;
            state.stockDisponibleSel = 0;
            const ub = $(ids.ubicacion);
            if (ub) ub.value = '';
            const prod = $(ids.producto);
            if (prod) {
                prod.innerHTML = '<option value="">Selecciona ubicación primero</option>';
                prod.disabled = true;
            }
            const wrap = $(ids.varianteWrap);
            if (wrap) wrap.style.display = 'none';
            const cant = $(ids.cantidad);
            if (cant) cant.value = 1;
            const info = $(ids.stockInfo);
            if (info) info.textContent = '';
            const body = $(ids.body);
            if (body) body.style.display = 'none';
            const toggle = document.querySelector(ids.toggle);
            if (toggle) toggle.innerHTML = '<i class="bi bi-chevron-down"></i> Mostrar';
            renderTabla();
        }

        function validate() {
            if (state.items.length === 0) {
                return { ok: true };
            }
            if (!state.ubicacionId) {
                return { ok: false, error: 'Selecciona una ubicación para los productos de cambio.' };
            }
            return { ok: true };
        }

        return { init, getPayload, reset, validate };
    }

    return function (prefix) {
        if (!instancias[prefix]) {
            instancias[prefix] = crearInstancia(prefix);
        }
        return instancias[prefix];
    };
})();
