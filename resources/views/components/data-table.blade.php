@props([
    'columns' => [],         // [['key' => 'name', 'label' => 'Nombre', 'sortable' => true]]
    'rows' => [],            // [['name' => 'Foo', ...], ...]
    'searchable' => true,
    'paginate' => true,
    'perPage' => 10,
    'empty' => 'Sin datos',
])

@php
    $stateName = 'dt' . uniqid();
@endphp

<div
    x-data="{
        rows: @js($rows),
        cols: @js(array_values($columns)),
        search: '',
        sortKey: null,
        sortAsc: true,
        page: 1,
        perPage: {{ (int) $perPage }},
        get filtered() {
            let r = this.rows;
            if (this.search) {
                const q = this.search.toLowerCase();
                r = r.filter(row => Object.values(row).some(v => String(v ?? '').toLowerCase().includes(q)));
            }
            if (this.sortKey) {
                const k = this.sortKey, asc = this.sortAsc ? 1 : -1;
                r = [...r].sort((a, b) => {
                    const va = a[k] ?? '', vb = b[k] ?? '';
                    if (typeof va === 'number' && typeof vb === 'number') return (va - vb) * asc;
                    return String(va).localeCompare(String(vb), 'es', { numeric: true }) * asc;
                });
            }
            return r;
        },
        get pages() { return Math.max(1, Math.ceil(this.filtered.length / this.perPage)); },
        get pageRows() {
            const start = (this.page - 1) * this.perPage;
            return this.filtered.slice(start, start + this.perPage);
        },
        sort(k) {
            if (this.sortKey === k) this.sortAsc = !this.sortAsc;
            else { this.sortKey = k; this.sortAsc = true; }
        },
    }"
    {{ $attributes->merge(['class' => 'surface-card !p-0 overflow-hidden']) }}
>
    @if ($searchable)
        <div class="px-4 py-3 border-b border-cream-200 dark:border-cream-800 flex items-center gap-3">
            <div class="relative flex-1 max-w-xs">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-cream-500">
                    <x-icon name="search" class="w-4 h-4" />
                </span>
                <input x-model.debounce.200ms="search" type="search" placeholder="Buscar..." class="w-full rounded-xl border-cream-300 bg-white pl-9 pr-3 py-2 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100" />
            </div>
            <span class="text-xs text-cream-600 dark:text-cream-400 ms-auto" x-text="`${filtered.length} registros`"></span>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-cream-200 dark:border-cream-800 bg-cream-50/50 dark:bg-cream-900/40">
                    @foreach ($columns as $col)
                        <th class="text-left text-xs uppercase tracking-wider font-semibold text-cream-700 dark:text-cream-300 px-4 py-3 whitespace-nowrap">
                            @if (!empty($col['sortable']))
                                <button type="button" @click="sort('{{ $col['key'] }}')" class="inline-flex items-center gap-1 hover:text-primary-700 dark:hover:text-primary-300">
                                    {{ $col['label'] }}
                                    <span class="inline-flex" x-show="sortKey === '{{ $col['key'] }}'">
                                        <x-icon name="arrow-up" class="w-3 h-3" x-show="sortAsc" />
                                        <x-icon name="arrow-down" class="w-3 h-3" x-show="!sortAsc" />
                                    </span>
                                </button>
                            @else
                                {{ $col['label'] }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                <template x-for="(row, idx) in pageRows" :key="idx">
                    <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30 transition-colors">
                        @foreach ($columns as $col)
                            <td class="px-4 py-3 text-cream-800 dark:text-cream-200 whitespace-nowrap" x-html="row['{{ $col['key'] }}'] ?? ''"></td>
                        @endforeach
                    </tr>
                </template>
                <tr x-show="filtered.length === 0">
                    <td colspan="{{ count($columns) }}" class="px-4 py-12 text-center text-sm text-cream-500">
                        {{ $empty }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if ($paginate)
        <div class="px-4 py-3 border-t border-cream-200 dark:border-cream-800 flex items-center justify-between text-sm">
            <span class="text-xs text-cream-600 dark:text-cream-400" x-text="`Pagina ${page} de ${pages}`"></span>
            <div class="flex items-center gap-1.5">
                <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page === 1" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-cream-300 text-cream-700 hover:bg-cream-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-cream-700 dark:text-cream-300 dark:hover:bg-cream-800">
                    <x-icon name="chevron-left" class="w-4 h-4" />
                </button>
                <button type="button" @click="page = Math.min(pages, page + 1)" :disabled="page === pages" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-cream-300 text-cream-700 hover:bg-cream-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-cream-700 dark:text-cream-300 dark:hover:bg-cream-800">
                    <x-icon name="chevron-right" class="w-4 h-4" />
                </button>
            </div>
        </div>
    @endif
</div>
