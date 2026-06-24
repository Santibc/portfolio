@props([
    'columns' => [],         // [['key' => 'name', 'label' => 'Nombre', 'sortable' => true]]
    'rows' => [],            // [['name' => 'Foo', ...], ...]
    'searchable' => true,
    'paginate' => true,
    'perPage' => 5,
    'empty' => 'Sin datos',
    'filters' => [],         // [['key' => 'tipo', 'label' => 'Tipo']] -> dropdown por columna
    'perPageOptions' => [5, 10, 25, 50, 100],
])

<div
    x-data="{
        rows: @js($rows),
        cols: @js(array_values($columns)),
        filterDefs: @js(array_values($filters)),
        search: '',
        sortKey: null,
        sortAsc: true,
        page: 1,
        perPage: {{ (int) $perPage }},
        activeFilters: {},
        index: [],
        init() {
            // Indice de texto plano por fila: extrae el textContent de cada celda (descarta
            // tags HTML y decodifica entidades) para que la busqueda/filtros/orden trabajen
            // sobre el texto VISIBLE, no sobre el markup. Se computa una sola vez aqui.
            const tmp = document.createElement('div');
            const strip = (html) => {
                if (html == null) return '';
                const s = String(html);
                if (s.indexOf('<') === -1 && s.indexOf('&') === -1) return s.trim();
                tmp.innerHTML = s;
                return (tmp.textContent || '').replace(/\s+/g, ' ').trim();
            };
            this.index = this.rows.map((row) => {
                const cellsRaw = {};
                const parts = [];
                for (const c of this.cols) {
                    const t = strip(row[c.key]);
                    cellsRaw[c.key] = t;
                    parts.push(t.toLowerCase());
                }
                return { all: parts.join('  '), cellsRaw };
            });
            for (const f of this.filterDefs) {
                if (!(f.key in this.activeFilters)) this.activeFilters[f.key] = '';
            }
            this.$watch('search', () => { this.page = 1; });
            this.$watch('perPage', () => { this.page = 1; });
            // Clamp reactivo: si al filtrar/cambiar perPage la pagina queda fuera de rango,
            // ajustarla (evita el bug de quedarse en 'Pagina 3 de 2' con tabla vacia).
            this.$watch('pages', (p) => { if (this.page > p) this.page = p; });
            // Re-renderizar iconos Lucide cuando cambian las filas visibles (paginar/buscar/
            // ordenar/filtrar). renderToken deriva solo de estado, sin tocar el DOM: sin loops.
            this.$watch('renderToken', () => { this.$nextTick(() => window.renderIcons?.()); });
        },
        get filterOptions() {
            const out = {};
            for (const f of this.filterDefs) {
                const set = new Set();
                this.index.forEach((r) => { const v = r.cellsRaw[f.key]; if (v) set.add(v); });
                out[f.key] = [...set].sort((a, b) => a.localeCompare(b, 'es', { numeric: true }));
            }
            return out;
        },
        get filtered() {
            let idxs = this.rows.map((_, i) => i);
            if (this.search) {
                const q = this.search.toLowerCase();
                idxs = idxs.filter((i) => this.index[i].all.includes(q));
            }
            for (const k in this.activeFilters) {
                const want = this.activeFilters[k];
                if (want) idxs = idxs.filter((i) => this.index[i].cellsRaw[k] === want);
            }
            if (this.sortKey) {
                const k = this.sortKey, asc = this.sortAsc ? 1 : -1;
                idxs = [...idxs].sort((ia, ib) => {
                    const va = this.index[ia].cellsRaw[k] ?? '', vb = this.index[ib].cellsRaw[k] ?? '';
                    const na = this._num(va), nb = this._num(vb);
                    if (!isNaN(na) && !isNaN(nb)) return (na - nb) * asc;
                    return String(va).localeCompare(String(vb), 'es', { numeric: true }) * asc;
                });
            }
            return idxs.map((i) => this.rows[i]);
        },
        // Parsea numeros en formato es-CO ('.' miles, ',' decimal) tolerando '$' y unidades
        // (ej. '$ 1.500' -> 1500, '2 kg' -> 2). Devuelve NaN para fechas/texto -> orden alfabetico.
        _num(s) {
            const t = String(s).trim();
            if (!/\d/.test(t) || !/^-?\s*\$?\s*[\d.,]+\s*[a-zA-Z%º°]{0,5}\.?$/.test(t)) return NaN;
            const cleaned = t.replace(/[^\d.,-]/g, '').replace(/\./g, '').replace(',', '.');
            return (cleaned === '' || cleaned === '-') ? NaN : parseFloat(cleaned);
        },
        get effPerPage() { return this.perPage === 0 ? Math.max(this.filtered.length, 1) : this.perPage; },
        get pages() { return Math.max(1, Math.ceil(this.filtered.length / this.effPerPage)); },
        get pageRows() {
            const start = (this.page - 1) * this.effPerPage;
            return this.filtered.slice(start, start + this.effPerPage);
        },
        get renderToken() {
            return [this.page, this.perPage, this.search, this.sortKey, this.sortAsc,
                    Object.values(this.activeFilters).join('~')].join('|');
        },
        sort(k) {
            if (this.sortKey === k) this.sortAsc = !this.sortAsc;
            else { this.sortKey = k; this.sortAsc = true; }
            this.page = 1;
        },
    }"
    {{ $attributes->merge(['class' => 'surface-card !p-0 overflow-hidden']) }}
>
    @if ($searchable || count($filters))
        <div class="px-4 py-3 border-b border-cream-200 dark:border-cream-800 flex flex-wrap items-center gap-3">
            @if ($searchable)
                <div class="relative flex-1 min-w-[12rem] max-w-xs">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-cream-500">
                        <x-icon name="search" class="w-4 h-4" />
                    </span>
                    <input x-model.debounce.200ms="search" type="search" placeholder="Buscar..." class="w-full rounded-xl border-cream-300 bg-white pl-9 pr-3 py-2 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100" />
                </div>
            @endif

            @foreach ($filters as $f)
                <div class="min-w-[9rem]">
                    <select x-model="activeFilters['{{ $f['key'] }}']" @change="page = 1" class="w-full rounded-xl border-cream-300 bg-white py-2 pl-3 pr-8 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                        <option value="">{{ $f['label'] }}: todos</option>
                        <template x-for="opt in filterOptions['{{ $f['key'] }}']" :key="opt">
                            <option :value="opt" x-text="opt"></option>
                        </template>
                    </select>
                </div>
            @endforeach

            <span class="text-xs text-cream-600 dark:text-cream-400 ms-auto whitespace-nowrap" x-text="`${filtered.length} registros`"></span>
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
                                    <span class="inline-flex" x-show="sortKey === '{{ $col['key'] }}'" x-cloak>
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
                <template x-for="(row, idx) in pageRows" :key="`${page}-${idx}`">
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
        <div class="px-4 py-3 border-t border-cream-200 dark:border-cream-800 flex flex-wrap items-center justify-between gap-3 text-sm">
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
                    <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page === 1" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-cream-300 text-cream-700 hover:bg-cream-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-cream-700 dark:text-cream-300 dark:hover:bg-cream-800">
                        <x-icon name="chevron-left" class="w-4 h-4" />
                    </button>
                    <button type="button" @click="page = Math.min(pages, page + 1)" :disabled="page === pages" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-cream-300 text-cream-700 hover:bg-cream-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-cream-700 dark:text-cream-300 dark:hover:bg-cream-800">
                        <x-icon name="chevron-right" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
