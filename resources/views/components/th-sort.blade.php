@props([
    'col',                 // indice de columna (base 0) que ordena este encabezado
    'align' => 'left',     // left | right | center  -> solo coloca el indicador; el text-align va en class
])

{{-- Encabezado ordenable para usar DENTRO de <x-table-enhanced>. Llama sortBy(col) del scope.
     Pasa las MISMAS clases que tenia el <th> original via class="..." para conservar el estilo. --}}
<th {{ $attributes }}>
    <button type="button" @click="sortBy({{ (int) $col }})"
            class="inline-flex items-center gap-1 hover:text-primary-700 dark:hover:text-primary-300 {{ $align === 'right' ? 'flex-row-reverse' : '' }}">
        {{ $slot }}
        <span class="inline-flex" x-show="sortIdx === {{ (int) $col }}" x-cloak>
            <x-icon name="arrow-up" class="w-3 h-3" x-show="sortAsc" />
            <x-icon name="arrow-down" class="w-3 h-3" x-show="!sortAsc" />
        </span>
    </button>
</th>
