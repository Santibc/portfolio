@extends('layouts.app')

@section('header', 'Liquidar prestación')

@php
    $empleadosData = $empleados->mapWithKeys(fn ($e) => [
        $e->id => ['salario' => (int) $e->salario_base, 'auxilio' => $e->tiene_auxilio ? (int) $e->auxilio_transporte : 0],
    ]);
    $empleadosOptions = $empleados->pluck('nombre', 'id');
@endphp

@section('content')
    <x-page-header
        title="Liquidar prestación social"
        subtitle="Prima, cesantías, intereses o vacaciones por empleado"
        icon="receipt"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('prestaciones.index')">Volver</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <div class="mb-4 max-w-2xl">
            <x-alert variant="danger" title="Revisa los datos" dismissible>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        </div>
    @endif

    @if ($empleados->isEmpty())
        <x-card>
            <x-empty-state icon="users" title="No hay empleados activos"
                description="Registra al menos un empleado para liquidar prestaciones.">
                <x-slot:actions>
                    <x-button variant="primary" icon="plus" :href="route('empleados.create')">Nuevo empleado</x-button>
                </x-slot:actions>
            </x-empty-state>
        </x-card>
    @else
        <form action="{{ route('prestaciones.store') }}" method="POST" class="max-w-2xl"
            x-data="{
                empleados: @js($empleadosData),
                factor: {{ $factorInteres }},
                empleadoId: @js(old('empleado_id', '')),
                tipo: @js(old('tipo', 'prima')),
                dias: @js((int) old('dias', 180)),
                fmt(n) { return '$ ' + new Intl.NumberFormat('es-CO').format(Math.round(n)); },
                get emp() { return this.empleados[this.empleadoId] || null; },
                get preview() {
                    if (!this.emp) return null;
                    const sal = this.emp.salario, aux = this.emp.auxilio, d = this.dias;
                    if (this.tipo === 'vacaciones') return Math.round(sal * d / 720);
                    if (this.tipo === 'intereses') {
                        const ces = Math.round((sal + aux) * d / 360);
                        return Math.round(ces * this.factor * d / 360);
                    }
                    return Math.round((sal + aux) * d / 360); // prima / cesantias
                }
            }"
        >
            @csrf

            <x-card>
                <div class="space-y-5">
                    <x-select label="Empleado" name="empleado_id" :options="$empleadosOptions" :value="old('empleado_id')"
                              x-model="empleadoId" placeholder="— Selecciona un empleado —" required />

                    <x-select label="Tipo de prestación" name="tipo" :value="old('tipo', 'prima')" x-model="tipo" required
                        :options="[
                            'prima' => 'Prima de servicios',
                            'cesantias' => 'Cesantías',
                            'intereses' => 'Intereses sobre cesantías',
                            'vacaciones' => 'Vacaciones',
                        ]" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input label="Fecha inicio del período" name="fecha_inicio" type="date" :value="old('fecha_inicio')" required />
                        <x-input label="Fecha fin del período" name="fecha_fin" type="date" :value="old('fecha_fin')" required />
                    </div>

                    <x-input label="Días del período" name="dias" type="number" min="1" max="360" :value="old('dias', 180)" x-model.number="dias"
                             hint="Semestre = 180 días · año = 360 días." required />

                    <x-input label="Fondo (opcional)" name="fondo" :value="old('fondo')" placeholder="Ej. Protección / Porvenir"
                             hint="Para cesantías e intereses." />

                    <x-textarea label="Observación (opcional)" name="observacion" rows="2" placeholder="Notas">{{ old('observacion') }}</x-textarea>

                    <div class="rounded-xl bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 px-4 py-3"
                         x-show="preview !== null" x-cloak>
                        <p class="text-[11px] uppercase tracking-wide text-primary-700 dark:text-primary-300 font-semibold mb-1">Valor estimado</p>
                        <p class="text-2xl font-bold tabular-nums text-primary-800 dark:text-primary-100" x-text="fmt(preview)"></p>
                    </div>
                </div>

                <x-slot:footer>
                    <div class="flex items-center justify-end gap-2">
                        <x-button variant="ghost" :href="route('prestaciones.index')">Cancelar</x-button>
                        <x-button type="submit" variant="primary" icon="receipt">Liquidar prestación</x-button>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    @endif
@endsection
