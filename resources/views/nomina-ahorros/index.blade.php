@extends('layouts.app')

@section('header', 'Ahorros de empleados')

@section('content')
<div x-data="{
        open: false, empleadoId: null, nombre: '', acumulado: 0,
        get acumuladoFmt() { return '$ ' + new Intl.NumberFormat('es-CO').format(this.acumulado); },
        abrir(d) {
            this.empleadoId = d.id; this.nombre = d.nombre; this.acumulado = parseInt(d.acumulado || 0, 10); this.open = true;
            this.$nextTick(() => {
                const inp = document.getElementById('ahorro_monto');
                if (inp) { inp.value = String(this.acumulado); inp.dispatchEvent(new Event('input', { bubbles: true })); }
            });
        }
    }"
    @abrir-entrega-ahorro.window="abrir($event.detail)"
>
    <x-page-header
        title="Ahorros de empleados"
        subtitle="Ahorro retenido en nómina y entregas realizadas"
        icon="piggy-bank"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('empleados.index')">Empleados</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-table-enhanced class="mb-5" search-placeholder="Buscar empleado...">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-cream-100 dark:bg-cream-900/40 text-left text-xs font-semibold uppercase tracking-wide text-cream-600 dark:text-cream-400">
                    <tr>
                        <x-th-sort :col="0" class="px-4 py-3">Empleado</x-th-sort>
                        <x-th-sort :col="1" align="right" class="px-4 py-3 text-right">Retenido</x-th-sort>
                        <x-th-sort :col="2" align="right" class="px-4 py-3 text-right">Entregado</x-th-sort>
                        <x-th-sort :col="3" align="right" class="px-4 py-3 text-right">Acumulado</x-th-sort>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody data-enhance class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($empleados as $e)
                        @php($acumulado = $e->ahorro_acumulado)
                        <tr data-row class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-3 font-medium text-cream-900 dark:text-cream-50">{{ $e->nombre }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-cream-700 dark:text-cream-300">$ {{ number_format((int) ($e->total_ahorrado ?? 0), 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-cream-700 dark:text-cream-300">$ {{ number_format((int) ($e->total_pagado_ahorro ?? 0), 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold {{ $acumulado > 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-cream-500' }}">{{ $e->ahorro_acumulado_formateado }}</td>
                            <td class="px-4 py-3 text-right">
                                <button type="button"
                                    data-id="{{ $e->id }}" data-nombre="{{ e($e->nombre) }}" data-acumulado="{{ $acumulado }}"
                                    onclick="window.dispatchEvent(new CustomEvent('abrir-entrega-ahorro', { detail: { id: this.dataset.id, nombre: this.dataset.nombre, acumulado: this.dataset.acumulado } }))"
                                    {{ $acumulado > 0 ? '' : 'disabled' }}
                                    class="inline-flex items-center gap-1 font-medium text-xs {{ $acumulado > 0 ? 'text-emerald-700 hover:text-emerald-900 dark:text-emerald-300 dark:hover:text-emerald-100' : 'text-cream-400 cursor-not-allowed' }}">
                                    <x-icon name="banknote" class="w-3.5 h-3.5" /> Entregar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-cream-600 dark:text-cream-400">No hay empleados activos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-table-enhanced>

    {{-- Entregas recientes --}}
    <x-table-enhanced search-placeholder="Buscar entrega...">
        <div class="px-4 py-3 border-b border-cream-200 dark:border-cream-800">
            <h3 class="font-semibold text-sm text-cream-800 dark:text-cream-200">Entregas recientes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-cream-50 dark:bg-cream-900/20 text-left text-xs font-semibold uppercase tracking-wide text-cream-500">
                    <tr>
                        <x-th-sort :col="0" class="px-4 py-2">Fecha</x-th-sort>
                        <x-th-sort :col="1" class="px-4 py-2">Empleado</x-th-sort>
                        <th class="px-4 py-2">Observación</th>
                        <x-th-sort :col="3" align="right" class="px-4 py-2 text-right">Monto</x-th-sort>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody data-enhance class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($pagos as $p)
                        <tr data-row>
                            <td class="px-4 py-2 tabular-nums text-cream-700 dark:text-cream-300">{{ $p->pagado_en->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 text-cream-900 dark:text-cream-50">{{ $p->empleado?->nombre ?? '—' }}</td>
                            <td class="px-4 py-2 text-cream-500">{{ $p->observacion ?? '—' }}</td>
                            <td class="px-4 py-2 text-right tabular-nums font-semibold text-emerald-700 dark:text-emerald-400">{{ $p->monto_formateado }}</td>
                            <td class="px-4 py-2 text-right">
                                <form action="{{ route('nomina-ahorros.destroy', $p) }}" method="POST" class="inline"
                                      onsubmit="return confirm('¿Eliminar esta entrega?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 dark:text-rose-400"><x-icon name="trash-2" class="w-4 h-4" /></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-cream-500">Aún no hay entregas de ahorro.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-table-enhanced>

    {{-- Modal: entregar ahorro --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-cream-950/60 backdrop-blur-sm"
         x-transition.opacity @keydown.escape.window="open = false">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-md bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg" @click.outside="open = false">
                <form method="POST" action="{{ route('nomina-ahorros.store') }}">
                    @csrf
                    <input type="hidden" name="empleado_id" :value="empleadoId">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-cream-200 dark:border-cream-800">
                        <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50">Entregar ahorro — <span x-text="nombre"></span></h3>
                        <button type="button" class="text-cream-500 hover:text-cream-800 dark:hover:text-cream-200" @click="open = false">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="rounded-xl bg-primary-50 dark:bg-primary-900/20 px-4 py-3">
                            <p class="text-[11px] uppercase tracking-wide text-primary-700 dark:text-primary-300 font-semibold">Ahorro acumulado disponible</p>
                            <p class="text-xl font-bold tabular-nums text-primary-800 dark:text-primary-100" x-text="acumuladoFmt"></p>
                        </div>
                        <x-input-currency label="Monto a entregar" name="monto" id="ahorro_monto" required
                                          hint="No puede superar el ahorro acumulado." />
                        <x-textarea label="Observación (opcional)" name="observacion" rows="2" placeholder="Ej. Entrega solicitada por el empleado" />
                    </div>
                    <div class="px-5 py-4 border-t border-cream-200 dark:border-cream-800 flex items-center justify-end gap-2">
                        <x-button type="button" variant="ghost" x-on:click="open = false">Cancelar</x-button>
                        <x-button type="submit" variant="primary" icon="banknote">Registrar entrega</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
