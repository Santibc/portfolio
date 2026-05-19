@php
    /** @var \App\Models\Gasto|null $gasto */
    /** @var \App\Models\TurnoCaja|null $turnoActivo */
    $isEdit = isset($gasto) && $gasto !== null && $gasto->exists;
    $tipoActual = old('tipo', $isEdit ? $gasto->tipo->value : 'general');
    $trabajadorActual = old('trabajador_turno_id', $isEdit ? $gasto->trabajador_turno_id : '');
    $valorActual = old('valor', $isEdit ? $gasto->valor : '');
    $observacionActual = old('observacion', $isEdit ? $gasto->observacion : '');
@endphp

@if ($errors->any())
    <div class="mb-4">
        <x-alert variant="danger" title="Revisa los datos del gasto" dismissible>
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    </div>
@endif

<div
    x-data="{
        tipo: @js($tipoActual),
        valoresDefault: @js($valoresTurnoDefault ?? []),
        aplicarDefault(id) {
            if (!id) return;
            const v = this.valoresDefault[id] ?? 0;
            const inp = document.getElementById('valor');
            if (!inp) return;
            inp.value = String(v);
            inp.dispatchEvent(new Event('input', { bubbles: true }));
        },
    }"
>
    <x-card>
        <div class="space-y-5">
            <x-select
                label="Tipo de gasto"
                name="tipo"
                :options="['general' => 'Gasto general', 'turno' => 'Pago de turno']"
                :value="$tipoActual"
                x-model="tipo"
                required
            />

            <div x-show="tipo === 'turno'" x-transition x-cloak>
                <x-select
                    label="Trabajador"
                    name="trabajador_turno_id"
                    :options="$trabajadoresOptions"
                    :value="$trabajadorActual"
                    placeholder="Selecciona un trabajador..."
                    x-on:change="aplicarDefault($event.target.value)"
                    hint="Al elegir el trabajador, su valor por defecto se sugerirá en el campo de valor."
                />
                @if (empty($trabajadoresOptions))
                    <p class="mt-2 text-xs text-amber-700 dark:text-amber-300">
                        No hay trabajadores activos.
                        <a href="{{ route('trabajadores-turno.create') }}" class="underline">Crea uno aquí</a>.
                    </p>
                @endif
            </div>

            <x-input-currency
                label="Valor"
                name="valor"
                :value="$valorActual"
                hint="El valor del gasto no está limitado por el saldo de la caja."
                required
            />

            <x-textarea
                label="Observación"
                name="observacion"
                :value="$observacionActual"
                placeholder="Ej. Compra urgente de tenedores desechables"
                rows="3"
                x-bind:required="tipo === 'general'"
            />
        </div>

        <x-slot:footer>
            <div class="flex items-center justify-end gap-2">
                <x-button
                    variant="ghost"
                    :href="route('gastos.index')"
                >
                    Cancelar
                </x-button>
                <x-button type="submit" variant="primary" icon="save">
                    {{ $isEdit ? 'Guardar cambios' : 'Guardar' }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-card>
</div>
