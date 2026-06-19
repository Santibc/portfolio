@extends('layouts.app')

@section('header', 'Pago masivo de turnos')

@section('content')
    <x-page-header
        title="Pago masivo de turnos"
        subtitle="Paga el turno de varios trabajadores activos a la vez"
        icon="layers"
    >
        <x-slot:actions>
            <x-button
                variant="ghost"
                icon="arrow-left"
                :href="route('gastos.index')"
            >
                Volver
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($turnoActivo === null)
        <x-card>
            <x-empty-state
                icon="lock"
                title="No hay caja abierta"
                description="Para registrar pagos de turno primero debes abrir un turno de caja."
            >
                <x-slot:actions>
                    <x-button variant="primary" icon="unlock" :href="route('caja.index')">
                        Abrir caja
                    </x-button>
                </x-slot:actions>
            </x-empty-state>
        </x-card>
    @elseif ($trabajadores->isEmpty())
        <x-card>
            <x-empty-state
                icon="users"
                title="No hay trabajadores activos"
                description="Activa o crea trabajadores de turno para poder pagarlos masivamente."
            >
                <x-slot:actions>
                    <x-button variant="primary" icon="plus" :href="route('trabajadores-turno.create')">
                        Crear trabajador
                    </x-button>
                </x-slot:actions>
            </x-empty-state>
        </x-card>
    @elseif (empty($metodosOptions))
        <x-card>
            <x-empty-state
                icon="credit-card"
                title="No hay métodos de pago"
                description="Configura al menos un método de pago activo para registrar los gastos."
            />
        </x-card>
    @else
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

        <div class="mb-4">
            <x-alert variant="info" title="Turno activo">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                    <span><strong>Turno #{{ $turnoActivo->id }}</strong></span>
                    <span>Abierto: {{ $turnoActivo->abierto_en->format('Y-m-d H:i') }}</span>
                    <span>Ventas: <strong class="tabular-nums">{{ $turnoActivo->total_ventas_formateado }}</strong></span>
                </div>
            </x-alert>
        </div>

        <form
            action="{{ route('gastos.pago-masivo.store') }}"
            method="POST"
            x-data="{
                total: 0,
                digits(el) { return parseInt((el?.value || '').replace(/\D/g, '') || '0', 10); },
                recalcular() {
                    let t = 0;
                    this.$root.querySelectorAll('[data-row]').forEach((row) => {
                        const chk = row.querySelector('[data-pagar]');
                        if (!chk || !chk.checked) return;
                        t += this.digits(row.querySelector('[data-money=valor]'))
                           + this.digits(row.querySelector('[data-money=ahorro]'));
                    });
                    this.total = t;
                },
                toggleTodos(e) {
                    this.$root.querySelectorAll('[data-pagar]').forEach((c) => { c.checked = e.target.checked; });
                    this.recalcular();
                },
            }"
            x-init="$nextTick(() => recalcular())"
            @input="recalcular()"
            @change="recalcular()"
        >
            @csrf

            <x-card>
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-cream-200 dark:border-cream-800 text-left text-xs font-semibold uppercase tracking-wide text-cream-600 dark:text-cream-400">
                                <th class="px-3 py-3">
                                    <label class="inline-flex items-center gap-2 normal-case">
                                        <input
                                            type="checkbox"
                                            checked
                                            @change="toggleTodos($event)"
                                            class="rounded border-cream-300 text-primary-600 focus:ring-primary-500/30 dark:border-cream-600 dark:bg-cream-900/40"
                                        >
                                        <span>Pagar</span>
                                    </label>
                                </th>
                                <th class="px-3 py-3">Trabajador</th>
                                <th class="px-3 py-3">Método de pago</th>
                                <th class="px-3 py-3 w-40">Valor</th>
                                <th class="px-3 py-3 w-40">Ahorro</th>
                                <th class="px-3 py-3 min-w-[14rem]">Observación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                            @foreach ($trabajadores as $i => $t)
                                <tr data-row class="align-top">
                                    <td class="px-3 py-4">
                                        <input
                                            type="checkbox"
                                            name="items[{{ $i }}][pagar]"
                                            value="1"
                                            data-pagar
                                            checked
                                            class="mt-2 rounded border-cream-300 text-primary-600 focus:ring-primary-500/30 dark:border-cream-600 dark:bg-cream-900/40"
                                        >
                                    </td>

                                    <td class="px-3 py-4">
                                        <div class="font-medium text-cream-900 dark:text-cream-50 pt-1.5">
                                            {{ $t->nombre }}
                                        </div>
                                        <input type="hidden" name="items[{{ $i }}][trabajador_turno_id]" value="{{ $t->id }}">
                                    </td>

                                    <td class="px-3 py-4">
                                        <x-select
                                            name="items[{{ $i }}][metodo_pago_id]"
                                            :id="'metodo_'.$t->id"
                                            :options="$metodosOptions"
                                            :value="old('items.'.$i.'.metodo_pago_id', $metodoDefault)"
                                        />
                                    </td>

                                    <td class="px-3 py-4">
                                        <x-input-currency
                                            name="items[{{ $i }}][valor]"
                                            :id="'valor_'.$t->id"
                                            :value="old('items.'.$i.'.valor', $t->valor_turno_default)"
                                            data-money="valor"
                                        />
                                    </td>

                                    <td class="px-3 py-4">
                                        <x-input-currency
                                            name="items[{{ $i }}][ahorro]"
                                            :id="'ahorro_'.$t->id"
                                            :value="old('items.'.$i.'.ahorro', $t->valor_ahorro_default)"
                                            data-money="ahorro"
                                        />
                                    </td>

                                    <td class="px-3 py-4">
                                        <x-input
                                            name="items[{{ $i }}][observacion]"
                                            :id="'obs_'.$t->id"
                                            :value="old('items.'.$i.'.observacion')"
                                            placeholder="Opcional"
                                            maxlength="500"
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-slot:footer>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-cream-700 dark:text-cream-300">
                            Total a descontar de la caja:
                            <strong class="tabular-nums text-cream-900 dark:text-cream-50"
                                x-text="'$ ' + new Intl.NumberFormat('es-CO').format(total)"></strong>
                        </div>
                        <div class="flex items-center justify-end gap-2">
                            <x-button variant="ghost" :href="route('gastos.index')">
                                Cancelar
                            </x-button>
                            <x-button type="submit" variant="primary" icon="check">
                                Pagar masivamente
                            </x-button>
                        </div>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    @endif
@endsection
