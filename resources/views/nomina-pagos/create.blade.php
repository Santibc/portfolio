@extends('layouts.app')

@section('header', 'Registrar pago de nómina')

@section('content')
    <x-page-header
        title="Registrar pago"
        :subtitle="$detalle->empleado_nombre . ' · ' . $detalle->nomina?->descripcion"
        icon="banknote"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('nomina.show', $detalle->nomina_id)">Volver</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <div class="mb-4 max-w-xl">
            <x-alert variant="danger" title="No se pudo registrar el pago" dismissible>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        </div>
    @endif

    <form action="{{ route('nomina-pagos.store') }}" method="POST" class="max-w-xl">
        @csrf
        <input type="hidden" name="nomina_detalle_id" value="{{ $detalle->id }}">

        <x-card>
            <div class="space-y-5">
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-primary-50 dark:bg-primary-900/20 px-4 py-3">
                        <p class="text-[11px] uppercase tracking-wide text-primary-700 dark:text-primary-300 font-semibold">Neto</p>
                        <p class="text-lg font-bold tabular-nums text-primary-800 dark:text-primary-100">{{ $detalle->neto_formateado }}</p>
                    </div>
                    <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 px-4 py-3">
                        <p class="text-[11px] uppercase tracking-wide text-amber-700 dark:text-amber-300 font-semibold">Saldo pendiente</p>
                        <p class="text-lg font-bold tabular-nums text-amber-800 dark:text-amber-100">{{ $detalle->saldo_pendiente_formateado }}</p>
                    </div>
                </div>

                <x-input-currency label="Monto a pagar" name="monto" :value="old('monto', $detalle->saldo_pendiente)" required
                                  hint="Puede ser parcial; no supera el saldo pendiente." />

                <x-select label="Método de pago" name="metodo_pago_id" :options="$metodosOptions"
                          :value="old('metodo_pago_id', $detalle->empleado?->metodo_pago_id)" required />

                <x-input label="Fecha de pago" name="fecha_pago" type="date" :value="old('fecha_pago', now()->toDateString())" required />

                <x-input label="Referencia (opcional)" name="referencia" :value="old('referencia')" placeholder="Ej. Transferencia Bancolombia" maxlength="100" />
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-2">
                    <x-button variant="ghost" :href="route('nomina.show', $detalle->nomina_id)">Cancelar</x-button>
                    <x-button type="submit" variant="primary" icon="banknote">Registrar pago</x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
@endsection
