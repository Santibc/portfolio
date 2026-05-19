@extends('layouts.app')

@section('header', 'Editar venta')

@section('content')
    @if ($errors->any())
        <div class="mb-4">
            <x-alert variant="danger" title="No se pudo guardar" dismissible>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </x-alert>
        </div>
    @endif

    <x-page-header
        :title="'Editar venta #' . $venta->id"
        :subtitle="'Turno ' . $venta->turno->abierto_en->format('Y-m-d H:i')"
        icon="edit"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('caja-dashboard.show', $venta->turno_caja_id)">Volver</x-button>
        </x-slot:actions>
    </x-page-header>

    @php
        $menuPayload = $items->map(fn ($i) => [
            'id'      => $i->id,
            'nombre'  => $i->nombre,
            'precio'  => (int) $i->precio,
            'tipo_id' => $i->tipo_id,
            'tipo'    => $i->tipo?->nombre,
            'imagen'  => $i->imagen_url ?: null,
        ])->values();

        $metodosPayload = $metodos->map(fn ($m) => [
            'id'          => $m->id,
            'nombre'      => $m->nombre,
            'es_efectivo' => (bool) $m->es_efectivo,
        ])->values();

        $itemsInicial = old('items', $venta->items->map(fn ($it) => [
            'menu_item_id' => $it->menu_item_id,
            'cantidad'     => $it->cantidad,
            'nombre'       => $it->nombre_snapshot,
            'precio'       => (int) $it->precio_unitario,
        ])->all());

        $pagosInicial = old('pagos', $venta->pagos->map(fn ($p) => [
            'metodo_pago_id' => $p->metodo_pago_id,
            'monto'          => (int) $p->monto,
            'referencia'     => $p->referencia,
        ])->all());

        $tiposPayload = $tipos->values();
        $oldData = [
            'items'             => $itemsInicial,
            'pagos'             => $pagosInicial,
            'efectivo_recibido' => (int) old('efectivo_recibido', $venta->efectivo_recibido),
            'notas'             => old('notas', $venta->notas),
        ];
    @endphp

    <div x-data='ventaEditor(@json($menuPayload), @json($metodosPayload), @json($tiposPayload), @json($oldData))'>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Catálogo --}}
            <div class="lg:col-span-2">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <button type="button" class="px-3.5 py-1.5 rounded-full text-sm font-semibold border"
                            :class="tipoFiltro === null ? 'bg-primary-500 text-white border-primary-500' : 'bg-white text-cream-800 border-cream-300 dark:bg-cream-900/40 dark:text-cream-200 dark:border-cream-700'"
                            @click="tipoFiltro = null">Todos</button>
                    <template x-for="t in tipos" :key="t.id">
                        <button type="button" class="px-3.5 py-1.5 rounded-full text-sm font-semibold border"
                                :class="tipoFiltro === t.id ? 'bg-primary-500 text-white border-primary-500' : 'bg-white text-cream-800 border-cream-300 dark:bg-cream-900/40 dark:text-cream-200 dark:border-cream-700'"
                                @click="tipoFiltro = t.id" x-text="t.nombre"></button>
                    </template>
                </div>

                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                    <template x-for="item in itemsFiltrados" :key="item.id">
                        <button type="button" @click="addToCart(item)"
                                class="h-full bg-white dark:bg-cream-900/40 rounded-xl shadow-soft border border-cream-200 dark:border-cream-800 overflow-hidden flex flex-col text-left hover:border-primary-400 transition-all">
                            <div class="relative w-full pt-[100%] bg-cream-100 dark:bg-cream-800 overflow-hidden">
                                <template x-if="item.imagen"><img :src="item.imagen" class="absolute inset-0 w-full h-full object-cover" :alt="item.nombre"></template>
                                <template x-if="!item.imagen">
                                    <div class="absolute inset-0 flex items-center justify-center text-cream-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="m16 2-2.3 2.3a3 3 0 0 0 0 4.2l1.8 1.8a3 3 0 0 0 4.2 0L22 8"/><path d="M15 15 3.3 3.3a4.2 4.2 0 0 0 0 6l7.3 7.3c.7.7 2 .7 2.8 0L15 15Zm0 0 7 7"/><path d="m2.1 21.8 6.4-6.3"/><path d="m19 5-7 7"/></svg>
                                    </div>
                                </template>
                            </div>
                            <div class="p-1.5 flex-1 flex flex-col">
                                <h3 class="font-semibold text-[11px] text-cream-900 dark:text-cream-50 line-clamp-2 min-h-[1.75rem] leading-tight" x-text="item.nombre"></h3>
                                <p class="mt-0.5 text-xs font-bold text-primary-700 dark:text-primary-300" x-text="fmt(item.precio)"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Carrito --}}
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-[5rem] bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg border border-cream-200 dark:border-cream-800 overflow-hidden">
                    <div class="px-4 py-3 bg-cream-50 dark:bg-cream-900/40 border-b border-cream-200 dark:border-cream-800">
                        <h3 class="font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2"><x-icon name="shopping-cart" class="w-4 h-4" /> Carrito</h3>
                    </div>

                    <div class="max-h-64 overflow-y-auto divide-y divide-cream-200 dark:divide-cream-800">
                        <template x-for="c in cart" :key="c.id">
                            <div class="px-4 py-2.5 flex items-center gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-cream-900 dark:text-cream-50 truncate" x-text="c.nombre"></p>
                                    <p class="text-xs text-cream-600 dark:text-cream-400" x-text="fmt(c.precio)"></p>
                                </div>
                                <div class="inline-flex items-center gap-1">
                                    <button type="button" @click="setQty(c.id, c.cantidad - 1)" class="w-6 h-6 rounded bg-cream-100 dark:bg-cream-800"><x-icon name="minus" class="w-3 h-3 mx-auto" /></button>
                                    <span class="min-w-[1.5rem] text-center text-sm font-semibold" x-text="c.cantidad"></span>
                                    <button type="button" @click="setQty(c.id, c.cantidad + 1)" class="w-6 h-6 rounded bg-cream-100 dark:bg-cream-800"><x-icon name="plus" class="w-3 h-3 mx-auto" /></button>
                                </div>
                                <span class="text-sm font-bold w-20 text-right tabular-nums" x-text="fmt(c.precio * c.cantidad)"></span>
                            </div>
                        </template>
                    </div>

                    <div class="px-4 py-3 bg-cream-50 dark:bg-cream-900/40 border-t flex items-center justify-between">
                        <span class="text-sm">Total</span>
                        <span class="text-xl font-bold" x-text="fmt(total)"></span>
                    </div>

                    <div class="px-4 py-3 space-y-2 border-t border-cream-200 dark:border-cream-800">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-cream-600">Pagos</h4>
                            <button type="button" @click="addPago()" class="text-xs text-primary-700 font-medium">+ Agregar</button>
                        </div>
                        <template x-for="(p, i) in pagos" :key="i">
                            <div class="grid grid-cols-12 gap-1.5 items-center">
                                <select x-model.number="p.metodo_pago_id" class="col-span-5 rounded-lg border-cream-300 px-2 py-1.5 text-xs dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                                    <option :value="null">Seleccionar…</option>
                                    <template x-for="m in metodos" :key="m.id">
                                        <option :value="m.id" x-text="m.nombre"></option>
                                    </template>
                                </select>
                                <div class="col-span-6 relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-cream-500 text-xs">$</span>
                                    <input type="text" inputmode="numeric"
                                           :value="p.monto > 0 ? p.monto.toLocaleString('es-CO') : ''"
                                           @input="p.monto = parseInt(($event.target.value||'').replace(/\D/g,'') || '0', 10); $event.target.value = p.monto > 0 ? p.monto.toLocaleString('es-CO') : ''"
                                           class="block w-full rounded-lg border-cream-300 pl-5 pr-2 py-1.5 text-xs dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                                </div>
                                <button type="button" @click="removePago(i)" class="col-span-1 w-6 h-6 inline-flex items-center justify-center text-red-600 rounded">
                                    <x-icon name="x" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </template>
                    </div>

                    <div class="px-4 py-3 border-t border-cream-200 dark:border-cream-800" x-show="cambio > 0">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Cambio</span>
                            <span class="text-lg font-bold text-emerald-700 dark:text-emerald-400" x-text="fmt(cambio)"></span>
                        </div>
                    </div>
                    <div class="px-4 py-3 border-t border-cream-200 dark:border-cream-800" x-show="faltante > 0">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-rose-700 dark:text-rose-400">Falta</span>
                            <span class="font-bold text-rose-700 dark:text-rose-400" x-text="fmt(faltante)"></span>
                        </div>
                    </div>

                    <div class="px-4 py-3 border-t border-cream-200 dark:border-cream-800">
                        <input type="text" x-model="notas" placeholder="Notas…" class="block w-full rounded-lg border-cream-300 px-2.5 py-1.5 text-xs dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                    </div>

                    <div class="p-4 border-t border-cream-200 dark:border-cream-800">
                        <button type="button" @click="submit()"
                                :disabled="!puedeSubmitir || loading"
                                :class="puedeSubmitir && !loading ? 'bg-primary-500 hover:bg-primary-600' : 'bg-cream-300 dark:bg-cream-800 cursor-not-allowed'"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-base font-semibold text-white transition-all">
                            <x-icon name="save" class="w-5 h-5" />
                            <span x-show="!loading">Guardar cambios</span>
                            <span x-show="loading">Guardando…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form id="form-venta-edit" action="{{ route('caja.venta.update', $venta) }}" method="POST" class="hidden">
            @csrf
            @method('PATCH')
            <template x-for="(c, i) in cart" :key="'i'+i">
                <div>
                    <input type="hidden" :name="'items[' + i + '][menu_item_id]'" :value="c.id">
                    <input type="hidden" :name="'items[' + i + '][cantidad]'" :value="c.cantidad">
                </div>
            </template>
            <template x-for="(p, i) in pagos" :key="'p'+i">
                <div>
                    <input type="hidden" :name="'pagos[' + i + '][metodo_pago_id]'" :value="p.metodo_pago_id">
                    <input type="hidden" :name="'pagos[' + i + '][monto]'" :value="p.monto">
                </div>
            </template>
            <input type="hidden" name="notas" :value="notas">
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function ventaEditor(menuItems, metodos, tipos, oldData) {
        return {
            menuItems, metodos, tipos,
            cart: [],
            pagos: [],
            notas: oldData.notas || '',
            tipoFiltro: null,
            loading: false,

            init() {
                if (Array.isArray(oldData.items)) {
                    oldData.items.forEach(row => {
                        const mi = this.menuItems.find(m => m.id == row.menu_item_id);
                        const nombre = (row.nombre) || (mi ? mi.nombre : 'Item');
                        const precio = (row.precio !== undefined) ? parseInt(row.precio) : (mi ? mi.precio : 0);
                        this.cart.push({ id: parseInt(row.menu_item_id), nombre, precio, cantidad: parseInt(row.cantidad) || 1 });
                    });
                }
                if (Array.isArray(oldData.pagos)) {
                    oldData.pagos.forEach(row => {
                        this.pagos.push({
                            metodo_pago_id: parseInt(row.metodo_pago_id) || null,
                            monto: parseInt(row.monto) || 0,
                            referencia: row.referencia || '',
                        });
                    });
                }
            },

            get itemsFiltrados() {
                return this.tipoFiltro
                    ? this.menuItems.filter(i => i.tipo_id === this.tipoFiltro)
                    : this.menuItems;
            },
            addToCart(item) {
                const e = this.cart.find(c => c.id === item.id);
                if (e) e.cantidad++;
                else this.cart.push({ id: item.id, nombre: item.nombre, precio: item.precio, cantidad: 1 });
            },
            setQty(id, n) {
                if (n <= 0) { this.cart = this.cart.filter(c => c.id !== id); return; }
                const c = this.cart.find(c => c.id === id);
                if (c) c.cantidad = Math.min(99, n);
            },
            addPago() { const m = this.metodos[0]; this.pagos.push({ metodo_pago_id: m ? m.id : null, monto: 0, referencia: '' }); },
            removePago(i) { this.pagos.splice(i, 1); },

            get total() { return this.cart.reduce((s, c) => s + c.precio * c.cantidad, 0); },
            get sumNoEfectivo() {
                return this.pagos.reduce((s, p) => {
                    const m = this.metodos.find(x => x.id == p.metodo_pago_id);
                    if (m && !m.es_efectivo) return s + (parseInt(p.monto) || 0);
                    return s;
                }, 0);
            },
            get sumEfectivoPago() {
                return this.pagos.reduce((s, p) => {
                    const m = this.metodos.find(x => x.id == p.metodo_pago_id);
                    if (m && m.es_efectivo) return s + (parseInt(p.monto) || 0);
                    return s;
                }, 0);
            },
            get efectivoRequerido() { return Math.max(0, this.total - this.sumNoEfectivo); },
            get cambio() { return Math.max(0, this.sumEfectivoPago - this.efectivoRequerido); },
            get faltante() { return Math.max(0, this.efectivoRequerido - this.sumEfectivoPago); },
            get puedeSubmitir() {
                if (this.cart.length === 0 || this.total <= 0) return false;
                if (this.pagos.length === 0) return false;
                if (!this.pagos.every(p => p.metodo_pago_id && p.monto > 0)) return false;
                if (this.sumNoEfectivo > this.total) return false;
                if (this.sumEfectivoPago < this.efectivoRequerido) return false;
                return true;
            },
            submit() {
                if (!this.puedeSubmitir || this.loading) return;
                this.loading = true;
                this.$nextTick(() => document.getElementById('form-venta-edit').submit());
            },
            fmt(n) { return '$ ' + (parseInt(n) || 0).toLocaleString('es-CO'); },
        };
    }
</script>
@endpush
