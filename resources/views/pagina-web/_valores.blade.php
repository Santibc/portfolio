{{-- Tarjetas de "Por qué elegirnos" --}}
<div class="border-t border-cream-200 pt-4 dark:border-cream-800" x-data="{ filas: @js(array_values(old('valores', $valores))) }">
    <p class="text-sm font-medium text-cream-800 dark:text-cream-200">Tarjetas</p>
    <p class="mb-3 text-xs text-cream-600 dark:text-cream-400">Cada tarjeta lleva un icono, un título corto y una frase. Hasta 8.</p>

    @if ($errors->has('valores.*'))
        <x-alert variant="danger" class="mb-3">Revisa las tarjetas: cada una necesita icono, título y texto.</x-alert>
    @endif

    <div class="space-y-3">
        <template x-for="(fila, i) in filas" :key="i">
            <div class="rounded-xl border border-cream-200 p-3 dark:border-cream-800">
                <div class="grid gap-2 sm:grid-cols-2">
                    <x-select label="Icono" :options="$iconos" x-bind:name="`valores[${i}][icono]`" x-model="fila.icono" required />
                    <x-input label="Título" x-bind:name="`valores[${i}][titulo]`" x-model="fila.titulo" placeholder="Calidad" required maxlength="40" />
                </div>
                <div class="mt-2 flex items-end gap-2">
                    <div class="flex-1">
                        <x-input label="Texto" x-bind:name="`valores[${i}][texto]`" x-model="fila.texto" required maxlength="200" />
                    </div>
                    <x-button variant="ghost" icon="trash-2" x-on:click="filas.splice(i, 1)" aria-label="Quitar tarjeta" />
                </div>
            </div>
        </template>
    </div>

    <x-button variant="secondary" size="sm" icon="plus" class="mt-3" x-show="filas.length < 8"
              x-on:click="filas.push({ icono: 'award', titulo: '', texto: '' })">
        Agregar tarjeta
    </x-button>
</div>
