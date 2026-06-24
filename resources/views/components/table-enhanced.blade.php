@props([
    'searchable' => true,
    'paginate' => true,
    'perPage' => 5,
    'perPageOptions' => [5, 10, 25, 50, 100],
    'filters' => [],                       // [['col' => 2, 'label' => 'Estado']]  (col = indice base 0)
    'searchPlaceholder' => 'Buscar...',
])

{{--
    Envuelve una tabla Blade existente y le agrega busqueda / filtros / orden / paginacion en
    cliente SIN reescribir el controlador. Marca en la tabla del slot:
      - <tbody data-enhance>         el tbody que se pagina
      - <tr data-row>                cada fila de datos
      - <x-th-sort :col="N">         (opcional) en los <th> ordenables
    Los <tfoot> (totales) van fuera del tbody[data-enhance] y quedan intactos.
--}}
<div
    x-data="tableEnhanced({ perPage: {{ (int) $perPage }}, filters: @js(array_values($filters)) })"
    {{ $attributes->merge(['class' => 'surface-card !p-0 overflow-hidden']) }}
>
    @if ($searchable || count($filters))
        <div x-show="rowsMeta.length > 0" x-cloak class="px-4 py-3 border-b border-cream-200 dark:border-cream-800 flex flex-wrap items-center gap-3">
            @if ($searchable)
                <div class="relative flex-1 min-w-[12rem] max-w-xs">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-cream-500">
                        <x-icon name="search" class="w-4 h-4" />
                    </span>
                    <input x-model.debounce.200ms="search" type="search" placeholder="{{ $searchPlaceholder }}" class="w-full rounded-xl border-cream-300 bg-white pl-9 pr-3 py-2 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100" />
                </div>
            @endif

            @foreach ($filters as $f)
                <div class="min-w-[9rem]">
                    <select x-model="activeFilters[{{ (int) $f['col'] }}]" class="w-full rounded-xl border-cream-300 bg-white py-2 pl-3 pr-8 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                        <option value="">{{ $f['label'] }}: todos</option>
                        <template x-for="opt in filterOptions[{{ (int) $f['col'] }}]" :key="opt">
                            <option :value="opt" x-text="opt"></option>
                        </template>
                    </select>
                </div>
            @endforeach

            <span class="text-xs text-cream-600 dark:text-cream-400 ms-auto whitespace-nowrap" x-text="`${visibleRows.length} registros`"></span>
        </div>
    @endif

    {{ $slot }}

    <div x-show="rowsMeta.length > 0 && visibleRows.length === 0" x-cloak class="px-4 py-10 text-center text-sm text-cream-500">
        Sin resultados para la búsqueda o filtros aplicados.
    </div>

    @if ($paginate)
        <div x-show="rowsMeta.length > 0" x-cloak class="px-4 py-3 border-t border-cream-200 dark:border-cream-800 flex flex-wrap items-center justify-between gap-3 text-sm">
            <label class="flex items-center gap-2 text-xs text-cream-600 dark:text-cream-400">
                Filas por página
                <select x-model.number="perPage" class="rounded-lg border-cream-300 bg-white py-1 pl-2 pr-7 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                    @foreach ($perPageOptions as $opt)
                        <option :value="{{ (int) $opt }}">{{ $opt }}</option>
                    @endforeach
                    <option :value="0">Todas</option>
                </select>
            </label>
            <div class="flex items-center gap-3">
                <span class="text-xs text-cream-600 dark:text-cream-400" x-text="`Pagina ${page} de ${pages}`"></span>
                <div class="flex items-center gap-1.5">
                    <button type="button" @click="goTo(page - 1)" :disabled="page === 1" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-cream-300 text-cream-700 hover:bg-cream-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-cream-700 dark:text-cream-300 dark:hover:bg-cream-800">
                        <x-icon name="chevron-left" class="w-4 h-4" />
                    </button>
                    <button type="button" @click="goTo(page + 1)" :disabled="page === pages" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-cream-300 text-cream-700 hover:bg-cream-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-cream-700 dark:text-cream-300 dark:hover:bg-cream-800">
                        <x-icon name="chevron-right" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
