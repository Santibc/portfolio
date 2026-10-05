{{--
    Lógica compartida del POS (caja y edición de venta) para inventario:
    - Cada línea del carrito = item + opciones elegidas (ej. "Dorilokos de pollo" + "Doritos picantes").
    - Items con un componente de varias opciones abren el selector antes de agregarse.
    - Topes de cantidad según el stock restante (el servidor valida de nuevo al cobrar).
    Uso: Object.assign(componente, posInventario(stock)); el componente debe tener `menuItems` y `cart`.
--}}
@push('scripts')
<script>
    window.posInventario = function (stock) {
        const EPS = 0.0001;

        return {
            stock: stock || {},
            picker: { open: false, item: null, sel: {} },

            itemPorId(id) { return this.menuItems.find(m => m.id == id) || null; },
            elegibles(item) { return ((item && item.componentes) || []).filter(c => c.opciones.length > 1); },
            normalizarOpciones(opciones) {
                const out = {};
                Object.entries(opciones || {}).forEach(([k, v]) => { if (parseInt(v)) out[k] = parseInt(v); });
                return out;
            },
            lineKey(id, opciones) {
                return id + '|' + Object.keys(opciones || {}).sort().map(k => k + ':' + opciones[k]).join(',');
            },
            etiqueta(item, opciones) {
                return this.elegibles(item)
                    .map(c => (c.opciones.find(o => o.id == (opciones || {})[c.id]) || {}).nombre)
                    .filter(Boolean)
                    .join(', ');
            },
            fmtCant(n) { return (Math.round((parseFloat(n) || 0) * 100) / 100).toLocaleString('es-CO'); },

            // Productos que descuenta una línea: { producto_id: cantidad }
            consumoLinea(item, opciones, cantidad) {
                const out = {};
                if (!item) return out;
                for (const c of item.componentes || []) {
                    const pid = c.opciones.length === 1 ? c.opciones[0].id : parseInt((opciones || {})[c.id]);
                    if (!pid) continue;
                    out[pid] = (out[pid] || 0) + c.cantidad * cantidad;
                }
                return out;
            },
            consumoCarrito() {
                const out = {};
                for (const l of this.cart) {
                    const cons = this.consumoLinea(this.itemPorId(l.id), l.opciones, l.cantidad);
                    for (const pid in cons) out[pid] = (out[pid] || 0) + cons[pid];
                }
                return out;
            },
            disponible(pid, carrito = null) {
                const usado = (carrito || this.consumoCarrito())[pid] || 0;
                return (parseFloat(this.stock[pid]) || 0) - usado;
            },
            nombreProducto(item, pid) {
                for (const c of (item && item.componentes) || []) {
                    const o = c.opciones.find(o => o.id == pid);
                    if (o) return o.nombre;
                }
                return 'producto';
            },
            // Nombre del primer producto sin stock suficiente para agregar n unidades, o null.
            faltanteDe(item, opciones, n) {
                const cons = this.consumoLinea(item, opciones, n);
                const carrito = this.consumoCarrito();
                for (const pid in cons) {
                    const disp = this.disponible(pid, carrito);
                    if (disp + EPS < cons[pid]) {
                        return this.nombreProducto(item, pid) + ' (quedan ' + this.fmtCant(Math.max(0, disp)) + ')';
                    }
                }
                return null;
            },
            opcionAgotada(comp, pid) { return this.disponible(pid) + EPS < comp.cantidad; },
            itemAgotado(item) {
                const carrito = this.consumoCarrito();
                return (item.componentes || []).some(c => c.opciones.every(o => this.disponible(o.id, carrito) + EPS < c.cantidad));
            },
            // Unidades del item que aún se pueden vender (null si no descuenta inventario).
            unidadesDisponibles(item) {
                if (!item.componentes || item.componentes.length === 0) return null;
                const carrito = this.consumoCarrito();
                let min = Infinity;
                for (const c of item.componentes) {
                    const total = c.opciones.reduce((s, o) => s + Math.max(0, this.disponible(o.id, carrito)), 0);
                    min = Math.min(min, Math.floor((total + EPS) / c.cantidad));
                }
                return min;
            },

            addToCart(item) {
                if (this.elegibles(item).length) { this.abrirPicker(item); return; }
                this.agregarLinea(item, {});
            },
            agregarLinea(item, opciones) {
                opciones = this.normalizarOpciones(opciones);
                const falta = this.faltanteDe(item, opciones, 1);
                if (falta) { this.avisarSinStock(falta); return false; }
                const key = this.lineKey(item.id, opciones);
                const e = this.cart.find(c => c.key === key);
                if (e) { e.cantidad++; return true; }
                this.cart.push({
                    key, id: item.id, nombre: item.nombre, etiqueta: this.etiqueta(item, opciones), opciones,
                    precio: item.precio, precioOrig: item.precio, cantidad: 1,
                });
                return true;
            },
            // Reconstruye una línea guardada (old() o venta existente).
            restaurarLinea(row, precio, precioOrig, nombreFallback) {
                const opciones = this.normalizarOpciones(row.opciones);
                const mi = this.itemPorId(row.menu_item_id);
                let key = this.lineKey(parseInt(row.menu_item_id), opciones);
                if (this.cart.some(c => c.key === key)) key += '#' + this.cart.length;
                this.cart.push({
                    key, id: parseInt(row.menu_item_id),
                    nombre: mi ? mi.nombre : (nombreFallback || 'Item'),
                    etiqueta: mi ? this.etiqueta(mi, opciones) : '',
                    opciones, precio, precioOrig, cantidad: parseInt(row.cantidad) || 1,
                });
            },
            setQty(key, n) {
                const c = this.cart.find(c => c.key === key);
                if (!c) return;
                if (n <= 0) { this.cart = this.cart.filter(x => x.key !== key); return; }
                n = Math.min(99, n);
                if (n > c.cantidad) {
                    const falta = this.faltanteDe(this.itemPorId(c.id), c.opciones, n - c.cantidad);
                    if (falta) { this.avisarSinStock(falta); return; }
                }
                c.cantidad = n;
            },

            abrirPicker(item) { this.picker = { open: true, item, sel: {} }; },
            cerrarPicker() { this.picker = { open: false, item: null, sel: {} }; },
            elegirOpcion(comp, pid) {
                if (this.opcionAgotada(comp, pid)) return;
                this.picker.sel[comp.id] = pid;
                const pendientes = this.elegibles(this.picker.item).filter(c => !this.picker.sel[c.id]);
                if (pendientes.length === 0 && this.agregarLinea(this.picker.item, this.picker.sel)) {
                    this.cerrarPicker();
                }
            },
            avisarSinStock(msg) {
                if (typeof window.showToast === 'function') window.showToast('warning', 'Sin stock: ' + msg);
                else alert('Sin stock: ' + msg);
            },
        };
    };
</script>
@endpush
