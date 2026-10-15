{{-- Cifras de "Nuestra historia" (ej. "+40 productos horneados a diario") --}}
<div class="border-t border-cream-200 pt-4 dark:border-cream-800" x-data="{ filas: @js(array_values(old('cifras', $cifras))) }">
    <p class="text-sm font-medium text-cream-800 dark:text-cream-200">Cifras</p>
    <p class="mb-3 text-xs text-cream-600 dark:text-cream-400">El número sube animado al aparecer. Hasta 4.</p>

    @if ($errors->has('cifras.*'))
        <x-alert variant="danger" class="mb-3">Revisa las cifras: cada una necesita número y texto.</x-alert>
    @endif

    <div class="space-y-3">
        <template x-for="(fila, i) in filas" :key="i">
            <div class="rounded-xl border border-cream-200 p-3 dark:border-cream-800">
                <div class="grid grid-cols-[4.5rem_1fr_4.5rem] gap-2">
                    <x-input label="Antes" x-bind:name="`cifras[${i}][prefijo]`" x-model="fila.prefijo" placeholder="+" maxlength="5" />
                    <x-input label="Número" type="number" min="0" max="999999" x-bind:name="`cifras[${i}][valor]`" x-model="fila.valor" required />
                    <x-input label="Después" x-bind:name="`cifras[${i}][sufijo]`" x-model="fila.sufijo" placeholder="%" maxlength="10" />
                </div>
                <div class="mt-2 flex items-end gap-2">
                    <div class="flex-1">
                        <x-input label="Texto" x-bind:name="`cifras[${i}][texto]`" x-model="fila.texto" placeholder="productos horneados a diario" required maxlength="80" />
                    </div>
                    <x-button variant="ghost" icon="trash-2" x-on:click="filas.splice(i, 1)" aria-label="Quitar cifra" />
                </div>
            </div>
        </template>
    </div>

    <x-button variant="secondary" size="sm" icon="plus" class="mt-3" x-show="filas.length < 4"
              x-on:click="filas.push({ prefijo: '', valor: 0, sufijo: '', texto: '' })">
        Agregar cifra
    </x-button>
</div>
