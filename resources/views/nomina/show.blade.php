@extends('layouts.app')

@section('header', 'Nómina')

@section('content')
<div x-data="{
        open: false,
        detalleId: null, nombre: '', saldo: 0, metodo: '',
        get saldoFmt() { return '$ ' + new Intl.NumberFormat('es-CO').format(this.saldo); },
        abrirPago(d) {
            this.detalleId = d.id; this.nombre = d.nombre; this.saldo = parseInt(d.saldo || 0, 10); this.metodo = d.metodo || '';
            this.open = true;
            this.$nextTick(() => {
                const inp = document.getElementById('pago_monto');
                if (inp) { inp.value = String(this.saldo); inp.dispatchEvent(new Event('input', { bubbles: true })); }
            });
        }
    }"
    @abrir-pago-nomina.window="abrirPago($event.detail)"
>
    <x-page-header :title="$nomina->descripcion" icon="banknote"
        :subtitle="$nomina->tipo->label() . ' · ' . $nomina->dias . ' días · creada por ' . ($nomina->creadaPor?->name ?? '—')">
        <x-slot:actions>
            <x-badge :variant="$nomina->estado->badge()" size="lg">{{ $nomina->estado->label() }}</x-badge>
            <x-button variant="ghost" icon="arrow-left" :href="route('nomina.index')">Volver</x-button>
            @if ($nomina->estado !== \App\Enums\EstadoNomina::Pagada)
                <x-button variant="ghost" icon="edit" :href="route('nomina.edit', $nomina)">Editar</x-button>
            @endif
            @if ($nomina->estado === \App\Enums\EstadoNomina::Borrador)
                <form action="{{ route('nomina.aprobar', $nomina) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <x-button type="submit" variant="secondary" icon="check">Aprobar</x-button>
                </form>
            @endif
            @if ($nomina->total_pendiente > 0)
                <x-button variant="primary" icon="credit-card" :href="route('nomina-pagos.masivo', ['nomina' => $nomina->id])">Pagar</x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    {{-- Resumen --}}
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-5">
        <x-stat-card icon="trending-up"  color="primary" label="Devengado" :value="$nomina->total_devengado_formateado" />
        <x-stat-card icon="trending-down" color="rose"   label="Deducido"  :value="$nomina->total_deducido_formateado" />
        <x-stat-card icon="banknote"     color="emerald" label="Neto"      :value="$nomina->total_neto_formateado" />
        <x-stat-card icon="check-circle" color="emerald" label="Pagado"    :value="$nomina->total_pagado_formateado" />
        <x-stat-card icon="clock"        color="accent"  label="Pendiente" :value="$nomina->total_pendiente_formateado" />
        <x-stat-card icon="piggy-bank"   color="sky"     label="Ahorro"    :value="$nomina->total_ahorro_formateado" />
    </div>

    {{-- Detalle por empleado --}}
    <x-table-enhanced
        class="mb-5"
        :filters="[['col' => 11, 'label' => 'Pago']]"
        search-placeholder="Buscar empleado..."
    >
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-cream-100 dark:bg-cream-900/40 text-left text-xs font-semibold uppercase tracking-wide text-cream-600 dark:text-cream-400">
                    <tr>
                        <x-th-sort :col="0" class="px-3 py-3">Empleado</x-th-sort>
                        <th class="px-3 py-3 text-center">Días</th>
                        <th class="px-3 py-3 text-right">Básico</th>
                        <th class="px-3 py-3 text-right">Bono</th>
                        <th class="px-3 py-3 text-right">Auxilio</th>
                        <th class="px-3 py-3 text-right">Salud</th>
                        <th class="px-3 py-3 text-right">Pensión</th>
                        <x-th-sort :col="7" align="right" class="px-3 py-3 text-right">Neto</x-th-sort>
                        <th class="px-3 py-3 text-right">Ahorro</th>
                        <th class="px-3 py-3 text-right">Pagado</th>
                        <x-th-sort :col="10" align="right" class="px-3 py-3 text-right">Saldo</x-th-sort>
                        <th class="px-3 py-3 text-center">Pago</th>
                        <th class="px-3 py-3"></th>
                    </tr>
                </thead>
                <tbody data-enhance class="divide-y divide-cream-200 dark:divide-cream-800">
                    @foreach ($nomina->detalles as $d)
                        <tr data-row class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-3 py-3 font-medium text-cream-900 dark:text-cream-50">{{ $d->empleado_nombre }}</td>
                            <td class="px-3 py-3 text-center tabular-nums text-cream-600 dark:text-cream-400">{{ $d->dias }}</td>
                            <td class="px-3 py-3 text-right tabular-nums">{{ $d->basico_formateado }}</td>
                            <td class="px-3 py-3 text-right tabular-nums text-cream-600 dark:text-cream-400">{{ $d->bono_formateado }}</td>
                            <td class="px-3 py-3 text-right tabular-nums text-cream-600 dark:text-cream-400">{{ $d->auxilio_formateado }}</td>
                            <td class="px-3 py-3 text-right tabular-nums text-rose-700 dark:text-rose-400">{{ $d->salud_formateado }}</td>
                            <td class="px-3 py-3 text-right tabular-nums text-rose-700 dark:text-rose-400">{{ $d->pension_formateado }}</td>
                            <td class="px-3 py-3 text-right tabular-nums font-semibold text-primary-700 dark:text-primary-300">{{ $d->neto_formateado }}</td>
                            <td class="px-3 py-3 text-right tabular-nums text-sky-700 dark:text-sky-400">{{ $d->ahorro > 0 ? $d->ahorro_formateado : '—' }}</td>
                            <td class="px-3 py-3 text-right tabular-nums text-emerald-700 dark:text-emerald-400">{{ $d->total_pagado_formateado }}</td>
                            <td class="px-3 py-3 text-right tabular-nums {{ $d->saldo_pendiente > 0 ? 'text-amber-700 dark:text-amber-400 font-semibold' : 'text-cream-500' }}">{{ $d->saldo_pendiente_formateado }}</td>
                            <td class="px-3 py-3 text-center"><x-badge :variant="$d->estado_pago->badge()" size="sm">{{ $d->estado_pago->label() }}</x-badge></td>
                            <td class="px-3 py-3 text-right">
                                @if ($d->saldo_pendiente > 0)
                                    <button type="button"
                                        data-id="{{ $d->id }}" data-nombre="{{ e($d->empleado_nombre) }}" data-saldo="{{ $d->saldo_pendiente }}" data-metodo="{{ $d->empleado?->metodo_pago_id }}"
                                        onclick="window.dispatchEvent(new CustomEvent('abrir-pago-nomina', { detail: { id: this.dataset.id, nombre: this.dataset.nombre, saldo: this.dataset.saldo, metodo: this.dataset.metodo } }))"
                                        class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-900 dark:text-emerald-300 dark:hover:text-emerald-100 font-medium text-xs">
                                        <x-icon name="banknote" class="w-3.5 h-3.5" /> Pagar
                                    </button>
                                @else
                                    <span class="text-emerald-600 dark:text-emerald-400 text-xs inline-flex items-center gap-1"><x-icon name="check-circle" class="w-3.5 h-3.5" /> OK</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-table-enhanced>

    {{-- Pagos registrados --}}
    @php($pagos = $nomina->detalles->flatMap->pagos->sortByDesc('fecha_pago'))
    @if ($pagos->isNotEmpty())
        <x-table-enhanced
            :filters="[['col' => 2, 'label' => 'Método']]"
            search-placeholder="Buscar pago..."
        >
            <div class="px-4 py-3 border-b border-cream-200 dark:border-cream-800">
                <h3 class="font-semibold text-sm text-cream-800 dark:text-cream-200">Pagos registrados</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-cream-50 dark:bg-cream-900/20 text-left text-xs font-semibold uppercase tracking-wide text-cream-500">
                        <tr>
                            <x-th-sort :col="0" class="px-4 py-2">Fecha</x-th-sort>
                            <x-th-sort :col="1" class="px-4 py-2">Empleado</x-th-sort>
                            <th class="px-4 py-2">Método</th>
                            <th class="px-4 py-2">Referencia</th>
                            <x-th-sort :col="4" align="right" class="px-4 py-2 text-right">Monto</x-th-sort>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody data-enhance class="divide-y divide-cream-200 dark:divide-cream-800">
                        @foreach ($pagos as $p)
                            <tr data-row>
                                <td class="px-4 py-2 tabular-nums text-cream-700 dark:text-cream-300">{{ $p->fecha_pago->format('Y-m-d') }}</td>
                                <td class="px-4 py-2 text-cream-900 dark:text-cream-50">{{ $p->detalle?->empleado_nombre }}</td>
                                <td class="px-4 py-2 text-cream-700 dark:text-cream-300">{{ $p->metodoPago?->nombre ?? '—' }}</td>
                                <td class="px-4 py-2 text-cream-500">{{ $p->referencia ?? '—' }}</td>
                                <td class="px-4 py-2 text-right tabular-nums font-semibold text-emerald-700 dark:text-emerald-400">{{ $p->monto_formateado }}</td>
                                <td class="px-4 py-2 text-right">
                                    <form action="{{ route('nomina-pagos.destroy', $p) }}" method="POST" class="inline"
                                          onsubmit="return confirm('¿Eliminar este pago?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 dark:text-rose-400"><x-icon name="trash-2" class="w-4 h-4" /></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-table-enhanced>
    @endif

    {{-- Modal: registrar pago --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-cream-950/60 backdrop-blur-sm"
         x-transition.opacity @keydown.escape.window="open = false">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-md bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg" @click.outside="open = false">
                <form method="POST" action="{{ route('nomina-pagos.store') }}">
                    @csrf
                    <input type="hidden" name="nomina_detalle_id" :value="detalleId">

                    <div class="flex items-center justify-between px-5 py-4 border-b border-cream-200 dark:border-cream-800">
                        <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50">
                            Pagar — <span x-text="nombre"></span>
                        </h3>
                        <button type="button" class="text-cream-500 hover:text-cream-800 dark:hover:text-cream-200" @click="open = false">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 px-4 py-3">
                            <p class="text-[11px] uppercase tracking-wide text-amber-700 dark:text-amber-300 font-semibold">Saldo pendiente</p>
                            <p class="text-xl font-bold tabular-nums text-amber-800 dark:text-amber-100" x-text="saldoFmt"></p>
                        </div>

                        <x-input-currency label="Monto a pagar" name="monto" id="pago_monto" required
                                          hint="Puede ser parcial; no supera el saldo pendiente." />

                        <x-select label="Método de pago" name="metodo_pago_id" :options="$metodosOptions" x-model="metodo" required />

                        <x-input label="Fecha de pago" name="fecha_pago" type="date" :value="now()->toDateString()" required />

                        <x-input label="Referencia (opcional)" name="referencia" placeholder="Ej. Transferencia Bancolombia" maxlength="100" />
                    </div>

                    <div class="px-5 py-4 border-t border-cream-200 dark:border-cream-800 flex items-center justify-end gap-2">
                        <x-button type="button" variant="ghost" x-on:click="open = false">Cancelar</x-button>
                        <x-button type="submit" variant="primary" icon="banknote">Registrar pago</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
