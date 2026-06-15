@extends('layouts.app')

@section('header', 'Liquidar nómina')

@section('content')
    <x-page-header
        title="Liquidar nómina"
        subtitle="Crea el período y genera automáticamente el pago de todos los empleados activos"
        icon="calculator"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('nomina.index')">Volver</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <div class="mb-4 max-w-2xl">
            <x-alert variant="danger" title="No se pudo liquidar" dismissible>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        </div>
    @endif

    <form action="{{ route('nomina.store') }}" method="POST" class="max-w-2xl"
        x-data="{
            tipo: @js(old('tipo', 'quincenal')),
            inicio: @js(old('fecha_inicio', '')),
            fin: @js(old('fecha_fin', '')),
            dias: @js((int) old('dias', 15)),
            meses: ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'],
            aplicarTipo() { this.dias = this.tipo === 'mensual' ? 30 : 15; },
            get descripcion() {
                if (!this.inicio || !this.fin) return '— Selecciona las fechas del período —';
                const a = this.inicio.split('-'), b = this.fin.split('-');
                const dIni = parseInt(a[2], 10), dFin = parseInt(b[2], 10);
                const mes = this.meses[parseInt(a[1], 10) - 1] || '';
                return `PERIODO DEL ${dIni} AL ${dFin} DE ${mes} DE ${b[0]}`;
            }
        }"
    >
        @csrf

        <x-card>
            <div class="space-y-5">
                <x-select label="Tipo de período" name="tipo"
                          :options="['quincenal' => 'Quincenal (15 días)', 'mensual' => 'Mensual (30 días)']"
                          :value="old('tipo', 'quincenal')"
                          x-model="tipo" x-on:change="aplicarTipo()" required />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input label="Fecha inicio" name="fecha_inicio" type="date" :value="old('fecha_inicio')" x-model="inicio" required />
                    <x-input label="Fecha fin" name="fecha_fin" type="date" :value="old('fecha_fin')" x-model="fin" required />
                </div>

                <x-input label="Días del período" name="dias" type="number" min="1" max="31" :value="old('dias', 15)" x-model.number="dias"
                         hint="Días trabajados en el período. Define el básico (salario × días / 30)." required />

                <div class="rounded-xl bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 px-4 py-3">
                    <p class="text-[11px] uppercase tracking-wide text-primary-700 dark:text-primary-300 font-semibold mb-1">Vista previa del período</p>
                    <p class="text-sm font-bold text-primary-800 dark:text-primary-100" x-text="descripcion"></p>
                </div>

                <x-alert variant="info" title="¿Qué pasa al liquidar?">
                    Se crea el período y se genera una línea por cada empleado activo, con su básico, auxilio,
                    deducciones (salud y pensión sobre el básico) y neto calculados automáticamente. Podrás ajustar
                    bono, auxilio, ahorro y días por empleado antes de pagar.
                </x-alert>
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-2">
                    <x-button variant="ghost" :href="route('nomina.index')">Cancelar</x-button>
                    <x-button type="submit" variant="primary" icon="calculator">Liquidar período</x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
@endsection
