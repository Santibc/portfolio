@extends('layouts.app')

@section('header', 'Editar nómina')

@section('content')
    <x-page-header
        :title="$nomina->descripcion"
        subtitle="Ajusta días, bono, auxilio y ahorro por empleado. El básico, deducciones y neto se recalculan solos."
        icon="calculator"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('nomina.show', $nomina)">Ver nómina</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <div class="mb-4">
            <x-alert variant="danger" title="Revisa los valores" dismissible>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        </div>
    @endif

    <form action="{{ route('nomina.update', $nomina) }}" method="POST"
        x-data="{
            totDev: '$ 0', totDed: '$ 0', totNeto: '$ 0',
            fmt(n) { return '$ ' + new Intl.NumberFormat('es-CO').format(Math.round(n)); },
            digits(el) { return parseInt((el?.value || '').replace(/\D/g, '') || '0', 10); },
            basico(sal, dias) { return Math.round(sal * dias / 30); },
            ded(basico, pct) { return Math.round(basico * pct / 100); },
            recalcAll() {
                let tDev = 0, tDed = 0, tNeto = 0;
                this.$root.querySelectorAll('[data-row]').forEach((row) => {
                    const sal = +row.dataset.salario, pS = +row.dataset.psalud, pP = +row.dataset.ppension;
                    const dias = this.digits(row.querySelector('[data-f=dias]'));
                    const bono = this.digits(row.querySelector('[data-money=bono]'));
                    const aux  = this.digits(row.querySelector('[data-money=auxilio]'));
                    const b = this.basico(sal, dias);
                    const ded = this.ded(b, pS) + this.ded(b, pP);
                    const dev = b + bono + aux, neto = dev - ded;
                    row.querySelector('[data-out=basico]').textContent = this.fmt(b);
                    row.querySelector('[data-out=devengado]').textContent = this.fmt(dev);
                    row.querySelector('[data-out=deducido]').textContent = this.fmt(ded);
                    row.querySelector('[data-out=neto]').textContent = this.fmt(neto);
                    tDev += dev; tDed += ded; tNeto += neto;
                });
                this.totDev = this.fmt(tDev); this.totDed = this.fmt(tDed); this.totNeto = this.fmt(tNeto);
            }
        }"
        x-init="$nextTick(() => recalcAll())"
        @input="recalcAll()"
        @change="recalcAll()"
    >
        @csrf
        @method('PUT')

        <x-card padding="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-cream-100 dark:bg-cream-900/40 text-left text-xs font-semibold uppercase tracking-wide text-cream-600 dark:text-cream-400">
                            <th class="px-3 py-3">Empleado</th>
                            <th class="px-3 py-3 w-20">Días</th>
                            <th class="px-3 py-3 text-right">Salario</th>
                            <th class="px-3 py-3 text-right">Básico</th>
                            <th class="px-3 py-3 w-36">Bono</th>
                            <th class="px-3 py-3 w-36">Auxilio</th>
                            <th class="px-3 py-3 text-right">Devengado</th>
                            <th class="px-3 py-3 text-right">Deducido</th>
                            <th class="px-3 py-3 text-right">Neto</th>
                            <th class="px-3 py-3 w-36">Ahorro</th>
                            <th class="px-3 py-3 min-w-[12rem]">Observación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                        @foreach ($nomina->detalles as $i => $d)
                            <tr data-row class="align-top"
                                data-salario="{{ $d->salario_base }}"
                                data-psalud="{{ $d->porcentaje_salud }}"
                                data-ppension="{{ $d->porcentaje_pension }}">
                                <td class="px-3 py-4">
                                    <div class="font-medium text-cream-900 dark:text-cream-50 pt-1.5">{{ $d->empleado_nombre }}</div>
                                    <input type="hidden" name="lineas[{{ $i }}][id]" value="{{ $d->id }}">
                                </td>
                                <td class="px-3 py-4">
                                    <input type="number" min="1" max="31" data-f="dias"
                                           name="lineas[{{ $i }}][dias]" value="{{ old('lineas.'.$i.'.dias', $d->dias) }}"
                                           class="block w-16 rounded-xl border-cream-300 bg-white px-2 py-2 text-sm text-cream-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                                </td>
                                <td class="px-3 py-4 text-right tabular-nums text-cream-600 dark:text-cream-400 pt-6">{{ $d->salario_base_formateado }}</td>
                                <td class="px-3 py-4 text-right tabular-nums font-medium text-cream-900 dark:text-cream-50 pt-6" data-out="basico">{{ $d->basico_formateado }}</td>
                                <td class="px-3 py-4">
                                    <x-input-currency name="lineas[{{ $i }}][bono]" :value="old('lineas.'.$i.'.bono', $d->bono)" data-money="bono" />
                                </td>
                                <td class="px-3 py-4">
                                    <x-input-currency name="lineas[{{ $i }}][auxilio]" :value="old('lineas.'.$i.'.auxilio', $d->auxilio)" data-money="auxilio" />
                                </td>
                                <td class="px-3 py-4 text-right tabular-nums font-semibold text-cream-900 dark:text-cream-50 pt-6" data-out="devengado">{{ $d->total_devengado_formateado }}</td>
                                <td class="px-3 py-4 text-right tabular-nums text-rose-700 dark:text-rose-400 pt-6" data-out="deducido">{{ $d->total_deducido_formateado }}</td>
                                <td class="px-3 py-4 text-right tabular-nums font-bold text-primary-700 dark:text-primary-300 pt-6" data-out="neto">{{ $d->neto_formateado }}</td>
                                <td class="px-3 py-4">
                                    <x-input-currency name="lineas[{{ $i }}][ahorro]" :value="old('lineas.'.$i.'.ahorro', $d->ahorro)" data-money="ahorro" />
                                </td>
                                <td class="px-3 py-4">
                                    <x-input name="lineas[{{ $i }}][observacion]" :value="old('lineas.'.$i.'.observacion', $d->observacion)" placeholder="Opcional" maxlength="500" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-cream-100 dark:bg-cream-900/40 font-semibold text-cream-900 dark:text-cream-50">
                            <td class="px-3 py-3" colspan="6">Totales</td>
                            <td class="px-3 py-3 text-right tabular-nums" x-text="totDev"></td>
                            <td class="px-3 py-3 text-right tabular-nums text-rose-700 dark:text-rose-400" x-text="totDed"></td>
                            <td class="px-3 py-3 text-right tabular-nums text-primary-700 dark:text-primary-300" x-text="totNeto"></td>
                            <td class="px-3 py-3" colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <x-slot:footer>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-cream-600 dark:text-cream-400">
                        Salud y pensión se calculan sobre el básico. El servidor recalcula al guardar.
                    </p>
                    <div class="flex items-center justify-end gap-2">
                        <x-button variant="ghost" :href="route('nomina.show', $nomina)">Cancelar</x-button>
                        <x-button type="submit" variant="primary" icon="save">Guardar cambios</x-button>
                    </div>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
@endsection
