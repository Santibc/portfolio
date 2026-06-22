@php
    $conceptoValue = old('concepto_gasto_fijo_id', $gastoFijo->concepto_gasto_fijo_id ?? null);
    $metodoValue   = old('metodo_pago_id', $gastoFijo->metodo_pago_id ?? null);
    $fechaValue    = old('fecha', isset($gastoFijo) ? $gastoFijo->fecha->format('Y-m-d') : now()->toDateString());
@endphp

<x-card>
    <div class="space-y-5">
        <x-select
            label="Concepto"
            name="concepto_gasto_fijo_id"
            :value="$conceptoValue"
            :options="$conceptosOptions"
            placeholder="Selecciona un concepto..."
            required
        />

        <x-input-currency
            label="Valor (COP)"
            name="valor"
            :value="old('valor', $gastoFijo->valor ?? null)"
            placeholder="0"
            required
        />

        <x-select
            label="Método de pago"
            name="metodo_pago_id"
            :value="$metodoValue"
            :options="$metodosOptions"
            placeholder="Selecciona un método..."
            required
        />

        <x-input
            label="Fecha de pago"
            name="fecha"
            type="date"
            :value="$fechaValue"
            required
        />

        <x-textarea
            label="Observación"
            name="observacion"
            :value="old('observacion', $gastoFijo->observacion ?? null)"
            placeholder="Opcional. Ej. arriendo de junio, factura #1234..."
            rows="2"
        />
    </div>
</x-card>

<div class="sticky bottom-0 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3 mt-4
            bg-cream-50/95 dark:bg-surface-dark/95 backdrop-blur
            border-t border-cream-200 dark:border-cream-800">
    <x-button type="submit" variant="primary" size="lg" icon="check" class="w-full justify-center">
        {{ isset($gastoFijo) ? 'Guardar cambios' : 'Registrar gasto fijo' }}
    </x-button>
</div>
