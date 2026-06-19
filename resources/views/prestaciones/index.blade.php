@extends('layouts.app')

@section('header', 'Prestaciones sociales')

@section('content')
<div x-data="{
        open: false, pagarUrl: '', nombre: '', tipo: '', valor: '',
        abrir(d) { this.pagarUrl = d.url; this.nombre = d.nombre; this.tipo = d.tipo; this.valor = d.valor; this.open = true; }
    }"
    @abrir-pago-prestacion.window="abrir($event.detail)"
>
    <x-page-header
        title="Prestaciones sociales"
        subtitle="Prima, cesantías, intereses y vacaciones liquidadas por empleado"
        icon="receipt"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="gauge" :href="route('nomina-dashboard.index')">Dashboard</x-button>
            <x-button variant="primary" icon="plus" :href="route('prestaciones.create')">Liquidar prestación</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-cream-100 dark:bg-cream-900/40 text-left text-xs font-semibold uppercase tracking-wide text-cream-600 dark:text-cream-400">
                    <tr>
                        <th class="px-4 py-3">Empleado</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Período</th>
                        <th class="px-4 py-3 text-center">Días</th>
                        <th class="px-4 py-3 text-right">Base</th>
                        <th class="px-4 py-3 text-right">Valor</th>
                        <th class="px-4 py-3">Fondo</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($prestaciones as $p)
                        <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-3 font-medium text-cream-900 dark:text-cream-50">{{ $p->empleado?->nombre ?? '—' }}</td>
                            <td class="px-4 py-3"><x-badge :variant="$p->tipo->badge()" size="sm">{{ $p->tipo->label() }}</x-badge></td>
                            <td class="px-4 py-3 text-cream-700 dark:text-cream-300 tabular-nums text-xs">{{ $p->fecha_inicio->format('Y-m-d') }} → {{ $p->fecha_fin->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-cream-600 dark:text-cream-400">{{ $p->dias }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-cream-600 dark:text-cream-400">{{ $p->base_formateado }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold text-primary-700 dark:text-primary-300">{{ $p->valor_formateado }}</td>
                            <td class="px-4 py-3 text-cream-700 dark:text-cream-300">{{ $p->fondo ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <x-badge :variant="$p->estado->badge()" size="sm">{{ $p->estado->label() }}</x-badge>
                                @if ($p->estado === \App\Enums\EstadoPrestacion::Pagada && $p->fecha_pago)
                                    <div class="text-[10px] text-cream-500 mt-0.5">{{ $p->fecha_pago->format('Y-m-d') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    @if ($p->estado === \App\Enums\EstadoPrestacion::Pendiente)
                                        <button type="button"
                                            data-url="{{ route('prestaciones.pagar', $p) }}" data-nombre="{{ e($p->empleado?->nombre) }}" data-tipo="{{ $p->tipo->label() }}" data-valor="{{ $p->valor_formateado }}"
                                            onclick="window.dispatchEvent(new CustomEvent('abrir-pago-prestacion', { detail: { url: this.dataset.url, nombre: this.dataset.nombre, tipo: this.dataset.tipo, valor: this.dataset.valor } }))"
                                            class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-900 dark:text-emerald-300 dark:hover:text-emerald-100 font-medium text-xs">
                                            <x-icon name="check" class="w-3.5 h-3.5" /> Marcar pagada
                                        </button>
                                    @endif
                                    <form action="{{ route('prestaciones.destroy', $p) }}" method="POST" class="inline"
                                          onsubmit="return confirm('¿Eliminar esta prestación?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-rose-700 hover:text-rose-900 dark:text-rose-300 dark:hover:text-rose-100 font-medium text-xs">
                                            <x-icon name="trash-2" class="w-3.5 h-3.5" /> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <x-empty-state icon="receipt" title="Aún no hay prestaciones liquidadas"
                                    description="Liquida la prima, cesantías, intereses o vacaciones de un empleado.">
                                    <x-slot:actions>
                                        <x-button variant="primary" icon="plus" :href="route('prestaciones.create')">Liquidar prestación</x-button>
                                    </x-slot:actions>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Modal: marcar pagada --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-cream-950/60 backdrop-blur-sm"
         x-transition.opacity @keydown.escape.window="open = false">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-md bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg" @click.outside="open = false">
                <form method="POST" :action="pagarUrl">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center justify-between px-5 py-4 border-b border-cream-200 dark:border-cream-800">
                        <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50">Marcar pagada</h3>
                        <button type="button" class="text-cream-500 hover:text-cream-800 dark:hover:text-cream-200" @click="open = false">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="rounded-xl bg-primary-50 dark:bg-primary-900/20 px-4 py-3">
                            <p class="text-sm text-cream-700 dark:text-cream-300"><span x-text="tipo"></span> — <span class="font-semibold" x-text="nombre"></span></p>
                            <p class="text-xl font-bold tabular-nums text-primary-800 dark:text-primary-100" x-text="valor"></p>
                        </div>
                        <x-select label="Método de pago" name="metodo_pago_id" :options="$metodosOptions" required />
                        <x-input label="Fecha de pago" name="fecha_pago" type="date" :value="now()->toDateString()" required />
                    </div>
                    <div class="px-5 py-4 border-t border-cream-200 dark:border-cream-800 flex items-center justify-end gap-2">
                        <x-button type="button" variant="ghost" x-on:click="open = false">Cancelar</x-button>
                        <x-button type="submit" variant="primary" icon="check">Confirmar pago</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
