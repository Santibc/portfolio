@extends('layouts.app')

@section('header', 'Pago masivo de nómina')

@section('content')
    <x-page-header
        title="Pago masivo de nómina"
        subtitle="Registra el pago de varios empleados a la vez"
        icon="credit-card"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('nomina.index')">Volver</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($nominaFiltro)
        <div class="mb-4">
            <x-alert variant="info" title="Filtrado por período">
                Mostrando solo pendientes de <strong>{{ $nominaFiltro->descripcion }}</strong>.
                <a href="{{ route('nomina-pagos.masivo') }}" class="underline">Ver todos</a>
            </x-alert>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4">
            <x-alert variant="danger" title="Revisa los datos del pago" dismissible>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        </div>
    @endif

    @if ($detalles->isEmpty())
        <x-card>
            <x-empty-state
                icon="check-circle"
                title="No hay pagos pendientes"
                description="Todas las líneas de nómina están pagadas. Liquida un nuevo período para registrar más pagos."
            >
                <x-slot:actions>
                    <x-button variant="primary" icon="calculator" :href="route('nomina.create')">Liquidar nómina</x-button>
                </x-slot:actions>
            </x-empty-state>
        </x-card>
    @elseif (empty($metodosOptions))
        <x-card>
            <x-empty-state icon="credit-card" title="No hay métodos de pago"
                description="Configura al menos un método de pago activo para registrar los pagos." />
        </x-card>
    @else
        <form action="{{ route('nomina-pagos.masivo.store') }}" method="POST"
            x-data="{
                total: 0,
                digits(el) { return parseInt((el?.value || '').replace(/\D/g, '') || '0', 10); },
                recalcular() {
                    let t = 0;
                    this.$root.querySelectorAll('[data-row]').forEach((row) => {
                        const chk = row.querySelector('[data-pagar]');
                        if (!chk || !chk.checked) return;
                        t += this.digits(row.querySelector('[data-money=monto]'));
                    });
                    this.total = t;
                },
                toggleTodos(e) {
                    this.$root.querySelectorAll('[data-pagar]').forEach((c) => { c.checked = e.target.checked; });
                    this.recalcular();
                }
            }"
            x-init="$nextTick(() => recalcular())"
            @input="recalcular()"
            @change="recalcular()"
        >
            @csrf

            <x-card padding="p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-cream-100 dark:bg-cream-900/40 text-left text-xs font-semibold uppercase tracking-wide text-cream-600 dark:text-cream-400">
                                <th class="px-3 py-3">
                                    <label class="inline-flex items-center gap-2 normal-case">
                                        <input type="checkbox" checked @change="toggleTodos($event)"
                                               class="rounded border-cream-300 text-primary-600 focus:ring-primary-500/30 dark:border-cream-600 dark:bg-cream-900/40">
                                        <span>Pagar</span>
                                    </label>
                                </th>
                                <th class="px-3 py-3">Empleado</th>
                                <th class="px-3 py-3 text-right">Saldo</th>
                                <th class="px-3 py-3">Método de pago</th>
                                <th class="px-3 py-3 w-40">Monto</th>
                                <th class="px-3 py-3 w-40">Fecha</th>
                                <th class="px-3 py-3 min-w-[12rem]">Referencia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                            @foreach ($detalles as $i => $d)
                                <tr data-row class="align-top">
                                    <td class="px-3 py-4">
                                        <input type="checkbox" name="items[{{ $i }}][pagar]" value="1" data-pagar checked
                                               class="mt-2 rounded border-cream-300 text-primary-600 focus:ring-primary-500/30 dark:border-cream-600 dark:bg-cream-900/40">
                                    </td>
                                    <td class="px-3 py-4">
                                        <div class="font-medium text-cream-900 dark:text-cream-50 pt-1.5">{{ $d->empleado_nombre }}</div>
                                        <div class="text-xs text-cream-500">{{ $d->nomina?->descripcion }}</div>
                                        <input type="hidden" name="items[{{ $i }}][nomina_detalle_id]" value="{{ $d->id }}">
                                    </td>
                                    <td class="px-3 py-4 text-right tabular-nums font-semibold text-amber-700 dark:text-amber-400 pt-6">{{ $d->saldo_pendiente_formateado }}</td>
                                    <td class="px-3 py-4">
                                        <x-select name="items[{{ $i }}][metodo_pago_id]" :id="'metodo_'.$d->id" :options="$metodosOptions"
                                                  :value="old('items.'.$i.'.metodo_pago_id', $d->empleado?->metodo_pago_id ?? $metodoDefault)" />
                                    </td>
                                    <td class="px-3 py-4">
                                        <x-input-currency name="items[{{ $i }}][monto]" :id="'monto_'.$d->id"
                                                          :value="old('items.'.$i.'.monto', $d->saldo_pendiente)" data-money="monto" />
                                    </td>
                                    <td class="px-3 py-4">
                                        <x-input name="items[{{ $i }}][fecha_pago]" :id="'fecha_'.$d->id" type="date"
                                                 :value="old('items.'.$i.'.fecha_pago', now()->toDateString())" />
                                    </td>
                                    <td class="px-3 py-4">
                                        <x-input name="items[{{ $i }}][referencia]" :id="'ref_'.$d->id"
                                                 :value="old('items.'.$i.'.referencia')" placeholder="Opcional" maxlength="100" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-slot:footer>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-cream-700 dark:text-cream-300">
                            Total a pagar:
                            <strong class="tabular-nums text-cream-900 dark:text-cream-50"
                                x-text="'$ ' + new Intl.NumberFormat('es-CO').format(total)"></strong>
                        </div>
                        <div class="flex items-center justify-end gap-2">
                            <x-button variant="ghost" :href="route('nomina.index')">Cancelar</x-button>
                            <x-button type="submit" variant="primary" icon="check">Pagar seleccionados</x-button>
                        </div>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    @endif
@endsection
