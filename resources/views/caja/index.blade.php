@extends('layouts.app')

@section('header', 'Caja')

@section('content')
    @if ($errors->any())
        <div class="mb-4">
            <x-alert variant="danger" title="No se pudo procesar" dismissible>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </x-alert>
        </div>
    @endif

    @if ($turno === null)
        {{-- ===== TURNO CERRADO ===== --}}
        <div class="max-w-xl mx-auto mt-8">
            <x-card>
                <div class="flex flex-col items-center text-center py-4">
                    <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 mb-4">
                        <x-icon name="wallet" class="w-8 h-8" />
                    </span>
                    <h2 class="text-2xl font-bold text-cream-900 dark:text-cream-50">Caja cerrada</h2>
                    <p class="mt-2 text-cream-600 dark:text-cream-400">
                        Para empezar a registrar ventas, abre la caja con la base inicial en efectivo.
                    </p>
                </div>

                <form id="form-abrir-caja" action="{{ route('caja.turno.abrir') }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    <x-input-currency
                        label="Base inicial (efectivo en caja al abrir)"
                        name="base_inicial"
                        :value="old('base_inicial', 0)"
                        required
                    />
                    <x-textarea
                        label="Notas (opcional)"
                        name="notas"
                        :value="old('notas')"
                        placeholder="ej. Turno mañana — caja entregada por Juan"
                    />
                </form>

                <x-slot:footer>
                    <div class="flex items-center justify-end">
                        <button type="submit" form="form-abrir-caja"
                                class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-primary-500 hover:bg-primary-600 active:bg-primary-700 text-white text-base font-semibold shadow-soft transition-colors">
                            <x-icon name="play" class="w-5 h-5" />
                            Abrir caja
                        </button>
                    </div>
                </x-slot:footer>
            </x-card>
        </div>
    @else
        {{-- ===== TURNO ABIERTO — POS ===== --}}
        @php
            $totalVentas = (int) $turno->total_ventas;
            $totalEfvo   = (int) $turno->total_efectivo;
            $totalNoEfvo = (int) $turno->total_no_efectivo;
            $efvoEspera  = (int) $turno->efectivo_esperado;

            $menuPayload = $items->map(fn ($i) => [
                'id'        => $i->id,
                'nombre'    => $i->nombre,
                'precio'    => (int) $i->precio,
                'tipo_id'   => $i->tipo_id,
                'tipo'      => $i->tipo?->nombre,
                'imagen'    => $i->imagen_url ?: null,
            ])->values();

            $metodosPayload = $metodos->map(fn ($m) => [
                'id'          => $m->id,
                'nombre'      => $m->nombre,
                'es_efectivo' => (bool) $m->es_efectivo,
            ])->values();
            $tiposPayload = $tipos->values();
            $oldData = [
                'items'             => old('items', []),
                'pagos'             => old('pagos', []),
                'efectivo_recibido' => (int) (old('efectivo_recibido') ?? 0),
                'notas'             => old('notas', ''),
            ];
        @endphp

        <div x-data='pos(@json($menuPayload), @json($metodosPayload), @json($tiposPayload), @json($oldData))'>

            {{-- Header sticky (colapsable para ganar espacio vertical) --}}
            <div x-show="infoOpen" x-cloak
                 class="sticky top-16 z-30 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3 bg-white/95 dark:bg-surface-dark/95 backdrop-blur border-b border-cream-200 dark:border-cream-800 mb-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                            <x-icon name="unlock" class="w-5 h-5" />
                        </span>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-cream-600 dark:text-cream-400">Turno abierto</div>
                            <div class="text-sm font-semibold text-cream-900 dark:text-cream-50">
                                Desde {{ $turno->abierto_en->format('H:i') }} ·
                                Por {{ $turno->aperturadoPor?->name ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-[10px] uppercase tracking-wide text-cream-500">Base</div>
                            <div class="text-sm font-semibold text-cream-900 dark:text-cream-50">{{ $turno->base_inicial_formateada }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] uppercase tracking-wide text-cream-500">Ventas</div>
                            <div class="text-sm font-semibold text-primary-700 dark:text-primary-300">{{ $turno->total_ventas_formateado }}</div>
                        </div>
                        <x-button variant="ghost" size="sm" icon="lock"
                                  @click.prevent="document.getElementById('modal-cerrar-caja').classList.remove('hidden')">
                            Cerrar caja
                        </x-button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- ===== CATÁLOGO ===== --}}
                <div class="lg:col-span-2">
                    {{-- Tabs por tipo --}}
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <button type="button" class="px-3.5 py-2 rounded-full text-sm font-semibold border transition-all"
                                :class="tipoFiltro === null ? 'bg-primary-500 text-white border-primary-500 shadow-soft' : 'bg-white text-cream-800 border-cream-300 hover:bg-cream-100 dark:bg-cream-900/40 dark:text-cream-200 dark:border-cream-700'"
                                @click="tipoFiltro = null">
                            Todos
                        </button>
                        <template x-for="t in tipos" :key="t.id">
                            <button type="button" class="px-3.5 py-2 rounded-full text-sm font-semibold border transition-all"
                                    :class="tipoFiltro === t.id ? 'bg-primary-500 text-white border-primary-500 shadow-soft' : 'bg-white text-cream-800 border-cream-300 hover:bg-cream-100 dark:bg-cream-900/40 dark:text-cream-200 dark:border-cream-700'"
                                    @click="tipoFiltro = t.id" x-text="t.nombre"></button>
                        </template>

                        <div class="ml-auto flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex-1 sm:w-56">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-cream-500">
                                    <x-icon name="search" class="w-4 h-4" />
                                </span>
                                <input x-model="search" type="text" placeholder="Buscar..."
                                       class="block w-full rounded-xl border-cream-300 bg-white pl-8 pr-3 py-2 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                            </div>
                            {{-- Toggle de la info del turno --}}
                            <button type="button" @click="infoOpen = !infoOpen"
                                    :class="infoOpen ? 'bg-primary-500 text-white border-primary-500' : 'bg-white text-cream-700 border-cream-300 hover:bg-cream-100 dark:bg-cream-900/40 dark:text-cream-200 dark:border-cream-700'"
                                    class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border text-sm font-semibold transition-all"
                                    :title="infoOpen ? 'Ocultar info del turno' : 'Ver info del turno'">
                                <x-icon name="wallet" class="w-4 h-4" />
                                <span class="hidden sm:inline">Turno</span>
                                <span class="inline-flex transition-transform" :class="infoOpen && 'rotate-180'">
                                    <x-icon name="chevron-down" class="w-3.5 h-3.5" />
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Grid de items --}}
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                        <template x-for="item in itemsFiltrados" :key="item.id">
                            <button type="button" @click="addToCart(item)"
                                    class="group h-full bg-white dark:bg-cream-900/40 rounded-xl shadow-soft border border-cream-200 dark:border-cream-800 overflow-hidden flex flex-col text-left hover:border-primary-400 hover:shadow-soft-lg transition-all active:scale-[0.98]">
                                <div class="relative w-full pt-[100%] bg-cream-100 dark:bg-cream-800 overflow-hidden">
                                    <template x-if="item.imagen">
                                        <img :src="item.imagen" :alt="item.nombre" class="absolute inset-0 w-full h-full object-cover">
                                    </template>
                                    <template x-if="!item.imagen">
                                        <div class="absolute inset-0 flex items-center justify-center text-cream-400 dark:text-cream-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="m16 2-2.3 2.3a3 3 0 0 0 0 4.2l1.8 1.8a3 3 0 0 0 4.2 0L22 8"/><path d="M15 15 3.3 3.3a4.2 4.2 0 0 0 0 6l7.3 7.3c.7.7 2 .7 2.8 0L15 15Zm0 0 7 7"/><path d="m2.1 21.8 6.4-6.3"/><path d="m19 5-7 7"/></svg>
                                        </div>
                                    </template>
                                    <span class="absolute top-1.5 right-1.5 inline-flex items-center font-semibold rounded-full bg-primary-500/95 text-white text-[9px] px-1.5 py-0.5 shadow-soft" x-text="item.tipo"></span>
                                </div>
                                <div class="p-2 flex-1 flex flex-col">
                                    <h3 class="font-semibold text-xs text-cream-900 dark:text-cream-50 line-clamp-2 min-h-[2rem] leading-tight" x-text="item.nombre"></h3>
                                    <p class="mt-1 text-sm font-bold text-primary-700 dark:text-primary-300" x-text="fmt(item.precio)"></p>
                                </div>
                            </button>
                        </template>

                        <template x-if="itemsFiltrados.length === 0">
                            <div class="col-span-full text-center py-10 text-cream-600 dark:text-cream-400">
                                No hay items que coincidan.
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ===== CARRITO ===== --}}
                <div class="lg:col-span-1">
                    <div class="lg:sticky lg:top-[7.5rem] bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg border border-cream-200 dark:border-cream-800 overflow-hidden">
                        <div class="px-4 py-3 bg-cream-50 dark:bg-cream-900/40 border-b border-cream-200 dark:border-cream-800 flex items-center justify-between">
                            <h3 class="font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                                <x-icon name="shopping-cart" class="w-4 h-4" />
                                Carrito
                                <span x-show="cart.length > 0" class="text-xs font-normal text-cream-600 dark:text-cream-400" x-text="'(' + cart.reduce((s,c) => s+c.cantidad, 0) + ')'"></span>
                            </h3>
                            <button type="button" x-show="cart.length > 0" @click="cart = []" class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium">
                                Vaciar
                            </button>
                        </div>

                        {{-- Items en carrito --}}
                        <div class="max-h-72 overflow-y-auto divide-y divide-cream-200 dark:divide-cream-800">
                            <template x-if="cart.length === 0">
                                <p class="px-4 py-6 text-sm text-cream-600 dark:text-cream-400 text-center">
                                    Toca un item del catálogo para agregarlo.
                                </p>
                            </template>
                            <template x-for="(c, idx) in cart" :key="c.id">
                                <div class="px-4 py-3 flex items-center gap-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-cream-900 dark:text-cream-50 truncate" x-text="c.nombre"></p>
                                        <p class="text-xs text-cream-600 dark:text-cream-400" x-text="fmt(c.precio) + ' c/u'"></p>
                                    </div>
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button" @click="setQty(c.id, c.cantidad - 1)" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-cream-100 hover:bg-cream-200 text-cream-800 dark:bg-cream-800 dark:hover:bg-cream-700 dark:text-cream-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>
                                        </button>
                                        <span class="min-w-[2rem] text-center text-sm font-semibold tabular-nums" x-text="c.cantidad"></span>
                                        <button type="button" @click="setQty(c.id, c.cantidad + 1)" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-cream-100 hover:bg-cream-200 text-cream-800 dark:bg-cream-800 dark:hover:bg-cream-700 dark:text-cream-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                        </button>
                                    </div>
                                    <div class="text-right min-w-[5rem]">
                                        <p class="text-sm font-bold text-cream-900 dark:text-cream-50" x-text="fmt(c.precio * c.cantidad)"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Totales y pagos --}}
                        <div class="px-4 py-3 bg-cream-50 dark:bg-cream-900/40 border-t border-cream-200 dark:border-cream-800 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-cream-700 dark:text-cream-300">Total</span>
                                <span class="text-xl font-bold text-cream-900 dark:text-cream-50" x-text="fmt(total)"></span>
                            </div>
                        </div>

                        <div class="px-4 py-3 space-y-2 border-t border-cream-200 dark:border-cream-800">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-semibold uppercase tracking-wide text-cream-600 dark:text-cream-400">Pagos</h4>
                                <button type="button" @click="addPago()" class="inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100">
                                    <x-icon name="plus" class="w-3.5 h-3.5" /> Agregar método
                                </button>
                            </div>

                            <template x-for="(p, i) in pagos" :key="i">
                                <div class="rounded-xl border border-cream-200 dark:border-cream-800 p-2 space-y-2">
                                    {{-- Métodos como botones táctiles --}}
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-for="m in metodos" :key="m.id">
                                            <button type="button" @click="p.metodo_pago_id = m.id"
                                                    :class="p.metodo_pago_id == m.id
                                                        ? 'bg-primary-500 border-primary-500 text-white shadow-sm'
                                                        : 'bg-white border-cream-300 text-cream-700 hover:border-primary-400 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-200 dark:hover:border-primary-500'"
                                                    class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition-colors active:scale-95"
                                                    x-text="m.nombre">
                                            </button>
                                        </template>
                                    </div>

                                    {{-- Monto + quitar --}}
                                    <div class="flex items-center gap-1.5">
                                        <div class="flex-1 relative">
                                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2 text-cream-500 text-xs font-semibold">$</span>
                                            <input type="text" inputmode="numeric"
                                                   :value="p.monto > 0 ? p.monto.toLocaleString('es-CO') : ''"
                                                   @input="p.monto = parseInt(($event.target.value || '').replace(/\D/g,'') || '0', 10); $event.target.value = p.monto > 0 ? p.monto.toLocaleString('es-CO') : ''"
                                                   placeholder="0"
                                                   class="block w-full rounded-lg border-cream-300 bg-white pl-5 pr-2 py-1.5 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                                        </div>

                                        <button type="button" @click="removePago(i)" class="shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-100 dark:hover:bg-red-900/40">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <div x-show="pagos.length === 0" class="text-xs text-cream-600 dark:text-cream-400 italic">
                                Agrega al menos un método de pago.
                            </div>
                        </div>

                        {{-- Cambio (solo si la suma de pagos supera el total) --}}
                        <div class="px-4 py-3 border-t border-cream-200 dark:border-cream-800" x-show="cambio > 0">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Cambio a entregar</span>
                                <span class="text-2xl font-bold text-emerald-700 dark:text-emerald-400" x-text="fmt(cambio)"></span>
                            </div>
                        </div>
                        {{-- Faltante (cuando los pagos no cubren el total) --}}
                        <div class="px-4 py-3 border-t border-cream-200 dark:border-cream-800" x-show="faltante > 0">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-rose-700 dark:text-rose-400">Falta</span>
                                <span class="text-lg font-bold text-rose-700 dark:text-rose-400" x-text="fmt(faltante)"></span>
                            </div>
                        </div>

                        {{-- Notas --}}
                        <div class="px-4 py-3 border-t border-cream-200 dark:border-cream-800">
                            <input type="text" x-model="notas" placeholder="Notas (opcional)…"
                                   class="block w-full rounded-lg border-cream-300 bg-white px-2.5 py-1.5 text-xs focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                        </div>

                        {{-- Botón cobrar --}}
                        <div class="p-4 border-t border-cream-200 dark:border-cream-800">
                            <button type="button" @click="submit()"
                                    :disabled="!puedeSubmitir || loading"
                                    :class="puedeSubmitir && !loading ? 'bg-primary-500 hover:bg-primary-600 active:bg-primary-700 shadow-glow' : 'bg-cream-300 dark:bg-cream-800 cursor-not-allowed'"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-base font-semibold text-white transition-all">
                                <x-icon name="receipt" class="w-5 h-5" />
                                <span x-show="!loading">Cobrar <span x-text="total > 0 ? '(' + fmt(total) + ')' : ''"></span></span>
                                <span x-show="loading">Procesando…</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form oculto para POST --}}
            <form id="form-venta" action="{{ route('caja.venta.store') }}" method="POST" class="hidden">
                @csrf
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

            {{-- ===== MODAL CERRAR CAJA ===== --}}
            <div id="modal-cerrar-caja" class="hidden fixed inset-0 z-50 overflow-y-auto bg-cream-950/60 backdrop-blur-sm">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="w-full max-w-md bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg">
                        <form action="{{ route('caja.turno.cerrar', $turno) }}" method="POST">
                            @csrf
                            <div class="flex items-center justify-between px-5 py-4 border-b border-cream-200 dark:border-cream-800">
                                <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50">Cerrar caja</h3>
                                <button type="button" class="text-cream-500 hover:text-cream-800 dark:hover:text-cream-200"
                                        onclick="document.getElementById('modal-cerrar-caja').classList.add('hidden')">
                                    <x-icon name="x" class="w-5 h-5" />
                                </button>
                            </div>

                            <div class="p-5 space-y-4">
                                <dl class="text-sm divide-y divide-cream-200 dark:divide-cream-800">
                                    <div class="flex items-center justify-between py-2">
                                        <dt class="text-cream-600 dark:text-cream-400">Base inicial</dt>
                                        <dd class="font-semibold text-cream-900 dark:text-cream-50">{{ $turno->base_inicial_formateada }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between py-2">
                                        <dt class="text-cream-600 dark:text-cream-400">Total ventas</dt>
                                        <dd class="font-semibold text-cream-900 dark:text-cream-50">{{ $turno->total_ventas_formateado }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between py-2">
                                        <dt class="text-cream-600 dark:text-cream-400">Ventas en efectivo</dt>
                                        <dd class="font-semibold text-cream-900 dark:text-cream-50">{{ $turno->total_efectivo_formateado }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between py-2">
                                        <dt class="text-cream-700 dark:text-cream-300">Efectivo esperado</dt>
                                        <dd class="font-bold text-primary-700 dark:text-primary-300">{{ $turno->efectivo_esperado_formateado }}</dd>
                                    </div>
                                </dl>

                                <x-input-currency
                                    label="Total efectivo contado en caja"
                                    name="total_declarado"
                                    :value="old('total_declarado', $efvoEspera)"
                                    hint="Lo que efectivamente hay en el cajón al cerrar"
                                    required
                                />
                                <x-textarea
                                    label="Notas de cierre (opcional)"
                                    name="notas"
                                    :value="old('notas')"
                                />
                            </div>

                            <div class="px-5 py-4 border-t border-cream-200 dark:border-cream-800 flex items-center justify-end gap-2">
                                <x-button type="button" variant="ghost"
                                          onclick="document.getElementById('modal-cerrar-caja').classList.add('hidden')">
                                    Cancelar
                                </x-button>
                                <x-button type="submit" variant="danger" icon="lock">Cerrar caja</x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    function pos(menuItems, metodos, tipos, oldData) {
        return {
            menuItems,
            metodos,
            tipos,
            infoOpen: false,
            cart: [],
            pagos: [],
            notas: oldData.notas || '',
            tipoFiltro: null,
            search: '',
            loading: false,

            init() {
                // Restaurar carrito desde old() si hay errores de validación
                if (Array.isArray(oldData.items) && oldData.items.length) {
                    oldData.items.forEach(row => {
                        const mi = this.menuItems.find(m => m.id == row.menu_item_id);
                        if (mi) this.cart.push({ id: mi.id, nombre: mi.nombre, precio: mi.precio, cantidad: parseInt(row.cantidad) || 1 });
                    });
                }
                if (Array.isArray(oldData.pagos) && oldData.pagos.length) {
                    oldData.pagos.forEach(row => {
                        this.pagos.push({
                            metodo_pago_id: parseInt(row.metodo_pago_id) || null,
                            monto: parseInt(row.monto) || 0,
                            referencia: row.referencia || '',
                        });
                    });
                }
                // Pre-cargar el primer pago con el método de pago por defecto (efectivo si existe).
                if (this.pagos.length === 0 && this.metodos.length > 0) {
                    const def = this.metodos.find(m => m.es_efectivo) || this.metodos[0];
                    this.pagos.push({ metodo_pago_id: def.id, monto: 0, referencia: '' });
                }

                // Con un único método de pago, el monto sigue automáticamente al total
                // del carrito (al agregar/quitar items o al quedar un solo método).
                this.$watch('total', () => this.autoMontoPagoUnico());
                this.$watch('pagos.length', () => this.autoMontoPagoUnico());
                this.autoMontoPagoUnico();
            },

            // Si solo hay un método de pago, su monto = total del carrito.
            autoMontoPagoUnico() {
                if (this.pagos.length === 1) this.pagos[0].monto = this.total;
            },

            get itemsFiltrados() {
                const s = this.search.trim().toLowerCase();
                return this.menuItems.filter(i => {
                    if (this.tipoFiltro && i.tipo_id !== this.tipoFiltro) return false;
                    if (s && !i.nombre.toLowerCase().includes(s)) return false;
                    return true;
                });
            },

            addToCart(item) {
                const e = this.cart.find(c => c.id === item.id);
                if (e) e.cantidad++;
                else this.cart.push({ id: item.id, nombre: item.nombre, precio: item.precio, cantidad: 1 });
            },
            setQty(id, n) {
                if (n <= 0) { this.cart = this.cart.filter(c => c.id !== id); return; }
                if (n > 99) n = 99;
                const c = this.cart.find(c => c.id === id);
                if (c) c.cantidad = n;
            },
            addPago() {
                // Pre-llenar con el primer método activo si no hay. Se agrega arriba (unshift)
                // para que el método recién creado quede primero en la lista.
                const m = this.metodos[0];
                this.pagos.unshift({ metodo_pago_id: m ? m.id : null, monto: 0, referencia: '' });
                // Al pasar a múltiples métodos, limpiar los montos: el usuario debe
                // repartir el total manualmente entre los métodos.
                this.pagos.forEach(p => p.monto = 0);
            },
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
                this.$nextTick(() => {
                    document.getElementById('form-venta').submit();
                });
            },

            fmt(n) { return '$ ' + (parseInt(n) || 0).toLocaleString('es-CO'); },
        };
    }
</script>
@endpush
