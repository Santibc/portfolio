@extends('layouts.app')

@section('header', 'Trabajadores de turno')

@section('content')
<div
    x-data="{
        pagoOpen: false,
        pagoTrabId: null,
        pagoNombre: '',
        pagoAcumulado: 0,
        get pagoAcumuladoFmt() { return '$ ' + new Intl.NumberFormat('es-CO').format(this.pagoAcumulado); },
        abrirPago(d) {
            this.pagoTrabId = d.id;
            this.pagoNombre = d.nombre;
            this.pagoAcumulado = parseInt(d.acumulado || 0, 10);
            this.pagoOpen = true;
            this.$nextTick(() => {
                const inp = document.getElementById('monto');
                if (inp) { inp.value = String(this.pagoAcumulado); inp.dispatchEvent(new Event('input', { bubbles: true })); }
            });
        },
        histOpen: false,
        histNombre: '',
        histLoading: false,
        histHtml: '',
        async abrirHistorial(d) {
            this.histNombre = d.nombre;
            this.histHtml = '';
            this.histLoading = true;
            this.histOpen = true;
            try {
                const res = await fetch(`{{ url('trabajadores-turno') }}/${d.id}/historial-ahorro`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                this.histHtml = await res.text();
            } catch (e) {
                this.histHtml = '<p class=\'text-rose-600 dark:text-rose-400 text-sm\'>No se pudo cargar el historial.</p>';
            } finally {
                this.histLoading = false;
            }
        },
    }"
    @abrir-pago-ahorro.window="abrirPago($event.detail)"
    @abrir-historial-ahorro.window="abrirHistorial($event.detail)"
>
    <x-page-header
        title="Trabajadores de turno"
        subtitle="Personas que reciben pago diario por turno desde la caja"
        icon="users"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="piggy-bank" :href="route('pagos-ahorros.index')">
                Pagos ahorros
            </x-button>
            <x-button variant="ghost" icon="arrow-left" :href="route('gastos.index')">
                Volver a gastos
            </x-button>
            <x-button variant="primary" icon="plus" :href="route('trabajadores-turno.create')">
                Nuevo trabajador
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-data-table
        :columns="$columns"
        :rows="$rows"
        :searchable="true"
        :paginate="true"
        :perPage="15"
        empty="Aún no hay trabajadores de turno. Crea el primero con el botón “Nuevo trabajador”."
    />

    {{-- ============ MODAL: PAGAR AHORRO ============ --}}
    <div x-show="pagoOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-cream-950/60 backdrop-blur-sm"
         x-transition.opacity @keydown.escape.window="pagoOpen = false">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-md bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg" @click.outside="pagoOpen = false">
                <form method="POST" action="{{ route('pagos-ahorros.store') }}">
                    @csrf
                    <input type="hidden" name="trabajador_turno_id" :value="pagoTrabId">

                    <div class="flex items-center justify-between px-5 py-4 border-b border-cream-200 dark:border-cream-800">
                        <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50">
                            Pagar ahorro — <span x-text="pagoNombre"></span>
                        </h3>
                        <button type="button" class="text-cream-500 hover:text-cream-800 dark:hover:text-cream-200"
                                @click="pagoOpen = false">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="rounded-xl bg-primary-50 dark:bg-primary-900/20 px-4 py-3">
                            <p class="text-[11px] uppercase tracking-wide text-primary-700 dark:text-primary-300 font-semibold">Ahorro acumulado disponible</p>
                            <p class="text-xl font-bold tabular-nums text-primary-800 dark:text-primary-100" x-text="pagoAcumuladoFmt"></p>
                        </div>

                        <x-input-currency
                            label="Monto a pagar"
                            name="monto"
                            id="monto"
                            hint="No puede superar el ahorro acumulado disponible."
                            required
                        />

                        <x-textarea
                            label="Observación (opcional)"
                            name="observacion"
                            placeholder="Ej. Pago parcial solicitado por el trabajador"
                            rows="2"
                        />
                    </div>

                    <div class="px-5 py-4 border-t border-cream-200 dark:border-cream-800 flex items-center justify-end gap-2">
                        <x-button type="button" variant="ghost" x-on:click="pagoOpen = false">Cancelar</x-button>
                        <x-button type="submit" variant="primary" icon="banknote">Registrar pago</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ MODAL: HISTORIAL ============ --}}
    <div x-show="histOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-cream-950/60 backdrop-blur-sm"
         x-transition.opacity @keydown.escape.window="histOpen = false">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-2xl bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg" @click.outside="histOpen = false">
                <div class="flex items-center justify-between px-5 py-4 border-b border-cream-200 dark:border-cream-800">
                    <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50">
                        Historial de ahorro — <span x-text="histNombre"></span>
                    </h3>
                    <button type="button" class="text-cream-500 hover:text-cream-800 dark:hover:text-cream-200"
                            @click="histOpen = false">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5">
                    <div x-show="histLoading" class="py-10 flex items-center justify-center text-cream-500">
                        <x-icon name="loader-circle" class="w-6 h-6 animate-spin" />
                        <span class="ml-2 text-sm">Cargando historial...</span>
                    </div>
                    <div x-show="!histLoading" x-html="histHtml"></div>
                </div>

                <div class="px-5 py-4 border-t border-cream-200 dark:border-cream-800 flex items-center justify-end">
                    <x-button type="button" variant="ghost" x-on:click="histOpen = false">Cerrar</x-button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
