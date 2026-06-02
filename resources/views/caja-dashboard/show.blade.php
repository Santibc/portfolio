@extends('layouts.app')

@section('header', 'Turno de caja')

@section('content')
    <x-page-header
        :title="'Turno · ' . $turno->abierto_en->format('Y-m-d')"
        :subtitle="'Apertura ' . $turno->abierto_en->format('H:i') . ' · por ' . ($turno->aperturadoPor?->name ?? '—')"
        icon="receipt"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('caja-dashboard.index')">Volver</x-button>
            @if ($turno->cerrado_en === null)
                <x-button variant="primary" icon="shopping-cart" :href="route('caja.index')">Ir a la caja</x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    {{-- Estado y stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
        <x-stat-card icon="wallet"      color="primary" label="Total ventas"        :value="$turno->total_ventas_formateado" />
        <x-stat-card icon="dollar-sign" color="accent"  label="Base inicial"        :value="$turno->base_inicial_formateada" />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <x-stat-card icon="trending-down" color="rose" label="Total gastos" :value="$turno->total_gastos_formateado" />
        <x-stat-card icon="piggy-bank"    color="primary" label="Ahorros descontados" :value="$turno->total_ahorros_formateado" />
        <x-stat-card icon="trending-up"   :color="$turno->neto >= 0 ? 'emerald' : 'rose'" label="Neto (ventas − gastos − ahorros)" :value="$turno->neto_formateado" />
    </div>

    @if ($turno->cerrado_en !== null)
        <x-card class="mb-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-xs uppercase tracking-wide text-cream-500">Cerrada</p>
                    <p class="font-semibold text-cream-900 dark:text-cream-50">{{ $turno->cerrado_en->format('Y-m-d H:i') }}</p>
                    <p class="text-xs text-cream-600 dark:text-cream-400">por {{ $turno->cerradoPor?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-cream-500">Total declarado</p>
                    <p class="font-semibold text-cream-900 dark:text-cream-50">{{ $turno->total_declarado_formateado }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-cream-500">Diferencia</p>
                    @php
                        $diferencia = $turno->diferencia_cierre;
                        $colorDif   = $diferencia === null
                            ? 'neutral'
                            : ($diferencia > 0 ? 'success' : ($diferencia < 0 ? 'danger' : 'neutral'));
                    @endphp
                    <x-badge :variant="$colorDif" size="lg">{{ $turno->diferencia_cierre_formateada }}</x-badge>
                </div>
            </div>
        </x-card>
    @endif

    {{-- Desglose por método --}}
    @if ($desglosePorMetodo->isNotEmpty())
        <x-card class="mb-5" padding="p-4">
            <h3 class="font-semibold text-sm text-cream-800 dark:text-cream-200 mb-3">Desglose por método de pago</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
                @foreach ($desglosePorMetodo as $d)
                    <div class="rounded-xl bg-cream-50 dark:bg-cream-900/40 border border-cream-200 dark:border-cream-800 px-3 py-2">
                        <p class="text-[10px] uppercase tracking-wide text-cream-500 flex items-center gap-1">
                            <x-icon :name="$d['es_efectivo'] ? 'banknote' : 'credit-card'" class="w-3 h-3" />
                            {{ $d['nombre'] }}
                        </p>
                        @if ($d['gastos'] > 0)
                            <p class="text-[11px] text-cream-500 tabular-nums leading-tight">
                                $ {{ number_format($d['ventas'], 0, ',', '.') }}
                                <span class="text-rose-600 dark:text-rose-400">− $ {{ number_format($d['gastos'], 0, ',', '.') }}</span>
                            </p>
                        @endif
                        <p class="text-sm font-bold tabular-nums {{ $d['monto'] < 0 ? 'text-rose-700 dark:text-rose-400' : 'text-cream-900 dark:text-cream-50' }}">$ {{ number_format($d['monto'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    {{-- Tabla de items vendidos --}}
    <x-card padding="p-0" class="mb-5">
        <div x-data="{
            rows: @js($desglosePorItem),
            sortKey: 'total', sortAsc: false,
            page: 1, perPage: 25,
            sort(k) { this.sortKey === k ? (this.sortAsc = !this.sortAsc) : (this.sortKey = k, this.sortAsc = true); this.page = 1; },
            get sorted() {
                const k = this.sortKey, dir = this.sortAsc ? 1 : -1;
                return [...this.rows].sort((a, b) => {
                    const x = a[k], y = b[k];
                    return (typeof x === 'number' && typeof y === 'number')
                        ? (x - y) * dir
                        : String(x).localeCompare(String(y), 'es', { numeric: true }) * dir;
                });
            },
            get effPerPage() { return this.perPage === 0 ? Math.max(this.sorted.length, 1) : this.perPage; },
            get pages() { return Math.max(1, Math.ceil(this.sorted.length / this.effPerPage)); },
            get pageRows() { const s = (this.page - 1) * this.effPerPage; return this.sorted.slice(s, s + this.effPerPage); },
            get totalCantidad() { return this.rows.reduce((a, r) => a + r.cantidad, 0); },
            get totalTotal() { return this.rows.reduce((a, r) => a + r.total, 0); },
            fmt(n) { return new Intl.NumberFormat('es-CO').format(n); },
        }">
        <div class="px-4 py-3 border-b border-cream-200 dark:border-cream-800">
            <h3 class="font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                <x-icon name="utensils-crossed" class="w-4 h-4" />
                Items vendidos
                <span class="text-xs font-normal text-cream-600 dark:text-cream-400" x-text="`(${rows.length})`"></span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-50 dark:bg-cream-900/30 text-cream-700 dark:text-cream-300">
                    <tr>
                        <th class="text-left px-4 py-2 font-semibold whitespace-nowrap">
                            <button type="button" @click="sort('nombre')" class="inline-flex items-center gap-1 hover:text-primary-700 dark:hover:text-primary-300">
                                Item
                                <span class="inline-flex" x-show="sortKey === 'nombre'" x-cloak>
                                    <x-icon name="arrow-up" class="w-3 h-3" x-show="sortAsc" />
                                    <x-icon name="arrow-down" class="w-3 h-3" x-show="!sortAsc" />
                                </span>
                            </button>
                        </th>
                        <th class="text-right px-4 py-2 font-semibold whitespace-nowrap">
                            <button type="button" @click="sort('cantidad')" class="inline-flex items-center gap-1 hover:text-primary-700 dark:hover:text-primary-300">
                                Cantidad
                                <span class="inline-flex" x-show="sortKey === 'cantidad'" x-cloak>
                                    <x-icon name="arrow-up" class="w-3 h-3" x-show="sortAsc" />
                                    <x-icon name="arrow-down" class="w-3 h-3" x-show="!sortAsc" />
                                </span>
                            </button>
                        </th>
                        <th class="text-right px-4 py-2 font-semibold whitespace-nowrap">
                            <button type="button" @click="sort('total')" class="inline-flex items-center gap-1 hover:text-primary-700 dark:hover:text-primary-300">
                                Total vendido
                                <span class="inline-flex" x-show="sortKey === 'total'" x-cloak>
                                    <x-icon name="arrow-up" class="w-3 h-3" x-show="sortAsc" />
                                    <x-icon name="arrow-down" class="w-3 h-3" x-show="!sortAsc" />
                                </span>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                    <template x-for="item in pageRows" :key="item.nombre">
                        <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-2.5 text-cream-800 dark:text-cream-100" x-text="item.nombre"></td>
                            <td class="px-4 py-2.5 text-right tabular-nums text-cream-700 dark:text-cream-300" x-text="fmt(item.cantidad)"></td>
                            <td class="px-4 py-2.5 text-right tabular-nums font-semibold text-cream-900 dark:text-cream-50" x-text="'$ ' + fmt(item.total)"></td>
                        </tr>
                    </template>
                    <tr x-show="rows.length === 0">
                        <td colspan="3" class="px-4 py-10 text-center text-cream-600 dark:text-cream-400">
                            No se vendieron items en este turno todavía.
                        </td>
                    </tr>
                </tbody>
                <tfoot x-show="rows.length > 0" class="border-t-2 border-cream-200 dark:border-cream-800 bg-cream-50 dark:bg-cream-900/30">
                    <tr class="font-semibold text-cream-900 dark:text-cream-50">
                        <td class="px-4 py-2.5 whitespace-nowrap">Total</td>
                        <td class="px-4 py-2.5 text-right tabular-nums" x-text="fmt(totalCantidad)"></td>
                        <td class="px-4 py-2.5 text-right tabular-nums" x-text="'$ ' + fmt(totalTotal)"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Controles: filas por página + paginación --}}
        <div x-show="rows.length > 0" class="px-4 py-3 border-t border-cream-200 dark:border-cream-800 flex flex-wrap items-center justify-between gap-3">
            <label class="flex items-center gap-2 text-xs text-cream-600 dark:text-cream-400">
                Filas por página
                <select x-model.number="perPage" @change="page = 1" class="rounded-lg border-cream-300 bg-white py-1 pl-2 pr-7 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                    <option :value="100">100</option>
                    <option :value="0">Todas</option>
                </select>
            </label>
            <div class="flex items-center gap-3">
                <span class="text-xs text-cream-600 dark:text-cream-400" x-text="`Página ${page} de ${pages}`"></span>
                <div class="flex items-center gap-1.5">
                    <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page === 1" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-cream-300 text-cream-700 hover:bg-cream-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-cream-700 dark:text-cream-300 dark:hover:bg-cream-800">
                        <x-icon name="chevron-left" class="w-4 h-4" />
                    </button>
                    <button type="button" @click="page = Math.min(pages, page + 1)" :disabled="page === pages" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-cream-300 text-cream-700 hover:bg-cream-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-cream-700 dark:text-cream-300 dark:hover:bg-cream-800">
                        <x-icon name="chevron-right" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
        </div>
    </x-card>

    {{-- Tabla de ventas --}}
    <x-card padding="p-0">
        <div x-data="{
            ventas: @js($ventasData),
            csrf: '{{ csrf_token() }}',
            open: null,
            sortKey: 'ts', sortAsc: false,
            page: 1, perPage: 25,
            sort(k) { this.sortKey === k ? (this.sortAsc = !this.sortAsc) : (this.sortKey = k, this.sortAsc = true); this.page = 1; },
            get sorted() {
                const k = this.sortKey, dir = this.sortAsc ? 1 : -1;
                return [...this.ventas].sort((a, b) => {
                    const x = a[k], y = b[k];
                    return (typeof x === 'number' && typeof y === 'number')
                        ? (x - y) * dir
                        : String(x).localeCompare(String(y), 'es', { numeric: true }) * dir;
                });
            },
            get effPerPage() { return this.perPage === 0 ? Math.max(this.sorted.length, 1) : this.perPage; },
            get pages() { return Math.max(1, Math.ceil(this.sorted.length / this.effPerPage)); },
            get pageRows() { const s = (this.page - 1) * this.effPerPage; return this.sorted.slice(s, s + this.effPerPage); },
            fmt(n) { return new Intl.NumberFormat('es-CO').format(n); },
            confirmDelete(e) { if (!confirm('¿Eliminar esta venta?')) e.preventDefault(); },
        }">
        <div class="px-4 py-3 border-b border-cream-200 dark:border-cream-800">
            <h3 class="font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                <x-icon name="list" class="w-4 h-4" />
                Ventas del turno
                <span class="text-xs font-normal text-cream-600 dark:text-cream-400" x-text="`(${ventas.length})`"></span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-50 dark:bg-cream-900/30 text-cream-700 dark:text-cream-300">
                    <tr>
                        <th class="text-left px-4 py-2 font-semibold whitespace-nowrap">
                            <button type="button" @click="sort('ts')" class="inline-flex items-center gap-1 hover:text-primary-700 dark:hover:text-primary-300">
                                Hora
                                <span class="inline-flex" x-show="sortKey === 'ts'" x-cloak>
                                    <x-icon name="arrow-up" class="w-3 h-3" x-show="sortAsc" />
                                    <x-icon name="arrow-down" class="w-3 h-3" x-show="!sortAsc" />
                                </span>
                            </button>
                        </th>
                        <th class="text-left px-4 py-2 font-semibold whitespace-nowrap">
                            <button type="button" @click="sort('cajero')" class="inline-flex items-center gap-1 hover:text-primary-700 dark:hover:text-primary-300">
                                Cajero
                                <span class="inline-flex" x-show="sortKey === 'cajero'" x-cloak>
                                    <x-icon name="arrow-up" class="w-3 h-3" x-show="sortAsc" />
                                    <x-icon name="arrow-down" class="w-3 h-3" x-show="!sortAsc" />
                                </span>
                            </button>
                        </th>
                        <th class="text-right px-4 py-2 font-semibold whitespace-nowrap">
                            <button type="button" @click="sort('items_count')" class="inline-flex items-center gap-1 hover:text-primary-700 dark:hover:text-primary-300">
                                Items
                                <span class="inline-flex" x-show="sortKey === 'items_count'" x-cloak>
                                    <x-icon name="arrow-up" class="w-3 h-3" x-show="sortAsc" />
                                    <x-icon name="arrow-down" class="w-3 h-3" x-show="!sortAsc" />
                                </span>
                            </button>
                        </th>
                        <th class="text-right px-4 py-2 font-semibold whitespace-nowrap">
                            <button type="button" @click="sort('total')" class="inline-flex items-center gap-1 hover:text-primary-700 dark:hover:text-primary-300">
                                Total
                                <span class="inline-flex" x-show="sortKey === 'total'" x-cloak>
                                    <x-icon name="arrow-up" class="w-3 h-3" x-show="sortAsc" />
                                    <x-icon name="arrow-down" class="w-3 h-3" x-show="!sortAsc" />
                                </span>
                            </button>
                        </th>
                        <th class="text-right px-4 py-2 font-semibold whitespace-nowrap">
                            <button type="button" @click="sort('cambio')" class="inline-flex items-center gap-1 hover:text-primary-700 dark:hover:text-primary-300">
                                Cambio
                                <span class="inline-flex" x-show="sortKey === 'cambio'" x-cloak>
                                    <x-icon name="arrow-up" class="w-3 h-3" x-show="sortAsc" />
                                    <x-icon name="arrow-down" class="w-3 h-3" x-show="!sortAsc" />
                                </span>
                            </button>
                        </th>
                        <th class="text-left px-4 py-2 font-semibold whitespace-nowrap">Métodos</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <template x-for="v in pageRows" :key="v.id">
                    <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                        <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-2.5 font-mono text-xs text-cream-700 dark:text-cream-300 whitespace-nowrap" x-text="v.hora"></td>
                            <td class="px-4 py-2.5 text-cream-700 dark:text-cream-300 whitespace-nowrap" x-text="v.cajero"></td>
                            <td class="px-4 py-2.5 text-right tabular-nums" x-text="v.items_count"></td>
                            <td class="px-4 py-2.5 text-right tabular-nums font-semibold text-cream-900 dark:text-cream-50 whitespace-nowrap" x-text="v.total_fmt"></td>
                            <td class="px-4 py-2.5 text-right tabular-nums text-emerald-700 dark:text-emerald-400 whitespace-nowrap" x-text="v.cambio_fmt"></td>
                            <td class="px-4 py-2.5">
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="(p, i) in v.pagos" :key="i">
                                        <span class="inline-flex items-center font-medium rounded-full bg-cream-100 dark:bg-cream-800 text-cream-800 dark:text-cream-200 text-[10px] px-2 py-0.5 whitespace-nowrap">
                                            <span x-text="p.nombre"></span> <span class="ml-1 font-bold" x-text="'$ ' + fmt(p.monto)"></span>
                                        </span>
                                    </template>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    {{-- Iconos inline (sin data-lucide) para que paginar/ordenar no dispare el re-render global de Lucide --}}
                                    <button type="button" @click="open === v.id ? open = null : open = v.id"
                                            class="inline-flex items-center gap-1 text-cream-700 hover:text-cream-900 dark:text-cream-300 dark:hover:text-cream-100 text-xs font-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span x-text="open === v.id ? 'Ocultar' : 'Ver'"></span>
                                    </button>
                                    <a :href="v.edit_url"
                                       class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 text-xs font-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg> Editar
                                    </a>
                                    <form :action="v.destroy_url" method="POST" @submit="confirmDelete($event)">
                                        <input type="hidden" name="_token" :value="csrf">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 text-xs font-medium">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <template x-if="open === v.id">
                        <tr>
                            <td colspan="7" class="bg-cream-50 dark:bg-cream-900/40 px-6 py-3">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-[10px] uppercase tracking-wide text-cream-500 mb-1.5">Items</h4>
                                        <ul class="text-sm divide-y divide-cream-200 dark:divide-cream-800">
                                            <template x-for="(it, i) in v.items" :key="i">
                                                <li class="py-1.5 flex items-center justify-between gap-2">
                                                    <span class="text-cream-800 dark:text-cream-100" x-text="it.label"></span>
                                                    <span class="font-semibold tabular-nums" x-text="it.subtotal_fmt"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] uppercase tracking-wide text-cream-500 mb-1.5">Pagos</h4>
                                        <ul class="text-sm divide-y divide-cream-200 dark:divide-cream-800">
                                            <template x-for="(p, i) in v.pagos" :key="i">
                                                <li class="py-1.5 flex items-center justify-between gap-2">
                                                    <span class="text-cream-800 dark:text-cream-100">
                                                        <span x-text="p.nombre"></span><template x-if="p.referencia"><span class="text-xs text-cream-500"> · <span x-text="p.referencia"></span></span></template>
                                                    </span>
                                                    <span class="font-semibold tabular-nums" x-text="'$ ' + fmt(p.monto)"></span>
                                                </li>
                                            </template>
                                            <template x-if="v.efectivo_recibido > 0">
                                                <li class="py-1.5 flex items-center justify-between gap-2 text-cream-600 dark:text-cream-400 text-xs">
                                                    <span>Efectivo recibido extra</span>
                                                    <span class="tabular-nums" x-text="v.efectivo_recibido_fmt"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                                <template x-if="v.notas">
                                    <p class="mt-3 text-xs text-cream-600 dark:text-cream-400 italic" x-text="v.notas"></p>
                                </template>
                            </td>
                        </tr>
                        </template>
                    </tbody>
                </template>
                <tbody x-show="ventas.length === 0">
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-cream-600 dark:text-cream-400">
                            No hay ventas en este turno todavía.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Controles: filas por página + paginación --}}
        <div x-show="ventas.length > 0" class="px-4 py-3 border-t border-cream-200 dark:border-cream-800 flex flex-wrap items-center justify-between gap-3">
            <label class="flex items-center gap-2 text-xs text-cream-600 dark:text-cream-400">
                Filas por página
                <select x-model.number="perPage" @change="page = 1" class="rounded-lg border-cream-300 bg-white py-1 pl-2 pr-7 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                    <option :value="100">100</option>
                    <option :value="0">Todas</option>
                </select>
            </label>
            <div class="flex items-center gap-3">
                <span class="text-xs text-cream-600 dark:text-cream-400" x-text="`Página ${page} de ${pages}`"></span>
                <div class="flex items-center gap-1.5">
                    <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page === 1" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-cream-300 text-cream-700 hover:bg-cream-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-cream-700 dark:text-cream-300 dark:hover:bg-cream-800">
                        <x-icon name="chevron-left" class="w-4 h-4" />
                    </button>
                    <button type="button" @click="page = Math.min(pages, page + 1)" :disabled="page === pages" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-cream-300 text-cream-700 hover:bg-cream-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-cream-700 dark:text-cream-300 dark:hover:bg-cream-800">
                        <x-icon name="chevron-right" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
        </div>
    </x-card>

    {{-- Tabla de gastos del turno --}}
    <x-card padding="p-0" class="mt-5">
        <div class="px-4 py-3 border-b border-cream-200 dark:border-cream-800 flex items-center justify-between gap-3">
            <h3 class="font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                <x-icon name="wallet" class="w-4 h-4" />
                Gastos del turno
                <span class="text-xs font-normal text-cream-600 dark:text-cream-400">({{ $turno->gastos->count() }})</span>
            </h3>
            <div class="flex items-center gap-2 text-xs text-cream-600 dark:text-cream-400">
                <span>Generales: <strong class="text-cream-900 dark:text-cream-50">$ {{ number_format($totalGastosGeneral, 0, ',', '.') }}</strong></span>
                <span class="text-cream-400">·</span>
                <span>Turnos: <strong class="text-cream-900 dark:text-cream-50">$ {{ number_format($totalGastosTurno, 0, ',', '.') }}</strong></span>
                @if ($turno->cerrado_en === null)
                    <x-button variant="primary" size="xs" icon="plus" :href="route('gastos.create')">Nuevo</x-button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-50 dark:bg-cream-900/30 text-cream-700 dark:text-cream-300">
                    <tr>
                        <th class="text-left px-4 py-2 font-semibold">Hora</th>
                        <th class="text-left px-4 py-2 font-semibold">Tipo</th>
                        <th class="text-left px-4 py-2 font-semibold">Concepto / Trabajador</th>
                        <th class="text-right px-4 py-2 font-semibold">Valor</th>
                        <th class="text-right px-4 py-2 font-semibold">Ahorro</th>
                        <th class="text-left px-4 py-2 font-semibold">Método</th>
                        <th class="text-left px-4 py-2 font-semibold">Cajero</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($turno->gastos as $g)
                        <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-2.5 font-mono text-xs text-cream-700 dark:text-cream-300">{{ $g->created_at->format('H:i:s') }}</td>
                            <td class="px-4 py-2.5">
                                @if ($g->tipo === \App\Enums\TipoGasto::Turno)
                                    <span class="inline-flex items-center font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200 text-xs px-2.5 py-1">Pago de turno</span>
                                @else
                                    <span class="inline-flex items-center font-semibold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200 text-xs px-2.5 py-1">Gasto general</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-cream-800 dark:text-cream-200">
                                @if ($g->tipo === \App\Enums\TipoGasto::Turno)
                                    <span class="font-medium">{{ $g->trabajadorTurno?->nombre ?? '—' }}</span>
                                    @if ($g->observacion)
                                        <span class="block text-xs text-cream-500">{{ $g->observacion }}</span>
                                    @endif
                                @else
                                    {{ $g->observacion ?? '—' }}
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right tabular-nums font-semibold text-rose-700 dark:text-rose-400">{{ $g->valor_formateado }}</td>
                            <td class="px-4 py-2.5 text-right tabular-nums {{ $g->ahorro > 0 ? 'text-primary-700 dark:text-primary-300 font-semibold' : 'text-cream-400' }}">{{ $g->ahorro > 0 ? $g->ahorro_formateado : '—' }}</td>
                            <td class="px-4 py-2.5">
                                @if ($g->metodoPago)
                                    <span class="inline-flex items-center gap-1 font-medium rounded-full bg-cream-100 dark:bg-cream-800 text-cream-800 dark:text-cream-200 text-xs px-2.5 py-1">
                                        <x-icon :name="$g->metodoPago->es_efectivo ? 'banknote' : 'credit-card'" class="w-3 h-3" />
                                        {{ $g->metodoPago->nombre }}
                                    </span>
                                @else
                                    <span class="text-cream-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-cream-700 dark:text-cream-300">{{ $g->user?->name ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('gastos.edit', $g) }}"
                                       class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 text-xs font-medium">
                                        <x-icon name="edit" class="w-3.5 h-3.5" /> Editar
                                    </a>
                                    <form action="{{ route('gastos.destroy', $g) }}" method="POST" onsubmit="return confirm('¿Eliminar este gasto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 text-xs font-medium">
                                            <x-icon name="trash-2" class="w-3.5 h-3.5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-cream-600 dark:text-cream-400">
                                No hay gastos en este turno.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
