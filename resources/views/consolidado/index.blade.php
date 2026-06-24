@extends('layouts.app')

@section('header', 'Consolidado general')

@section('content')
    <x-page-header
        title="Consolidado general"
        subtitle="Ingresos y egresos de todos los módulos, discriminados por método de pago"
        icon="pie-chart"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="wallet" :href="route('caja-dashboard.index')">
                Dashboard caja
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Filtro de rango (por defecto, el mes en curso) --}}
    <form method="GET" action="{{ route('consolidado.index') }}" class="mb-5">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-cream-700 dark:text-cream-300 mb-1">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}"
                       class="rounded-xl border-cream-300 bg-white px-3 py-2 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-cream-700 dark:text-cream-300 mb-1">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}"
                       class="rounded-xl border-cream-300 bg-white px-3 py-2 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
            </div>
            <x-button type="submit" variant="secondary" icon="search" size="sm">Filtrar</x-button>
            <x-button variant="ghost" icon="calendar" size="sm" :href="route('consolidado.index')">Mes actual</x-button>
        </div>
        <p class="mt-2 text-xs text-cream-500">
            Período: {{ \Carbon\Carbon::parse($desde)->translatedFormat('d M Y') }}
            — {{ \Carbon\Carbon::parse($hasta)->translatedFormat('d M Y') }}
        </p>
    </form>

    {{-- Totales --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
        <x-stat-card icon="trending-up"   color="emerald" label="Total ingresos" :value="'$ ' . number_format($totalIngresos, 0, ',', '.')" />
        <x-stat-card icon="trending-down" color="rose"    label="Total egresos"  :value="'$ ' . number_format($totalEgresos, 0, ',', '.')" />
        <x-stat-card :icon="$neto >= 0 ? 'trending-up' : 'trending-down'" :color="$neto >= 0 ? 'emerald' : 'rose'"
                     label="Neto (ingresos − egresos)"
                     :value="($neto < 0 ? '-' : '') . '$ ' . number_format(abs($neto), 0, ',', '.')" />
    </div>

    {{-- Tabla por método de pago (pieza central) --}}
    <x-table-enhanced class="mb-5" search-placeholder="Buscar método...">
        <div class="px-6 py-4 border-b border-cream-200 dark:border-cream-800">
            <h3 class="text-base font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                <x-icon name="credit-card" class="w-4 h-4 text-primary-600 dark:text-primary-300" />
                Por método de pago
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-100 dark:bg-cream-900/40 text-cream-800 dark:text-cream-200">
                    <tr>
                        <x-th-sort :col="0" class="text-left px-4 py-3 font-semibold">Método</x-th-sort>
                        <x-th-sort :col="1" align="right" class="text-right px-4 py-3 font-semibold">Ingresos</x-th-sort>
                        <x-th-sort :col="2" align="right" class="text-right px-4 py-3 font-semibold">Egresos</x-th-sort>
                        <x-th-sort :col="3" align="right" class="text-right px-4 py-3 font-semibold">Neto</x-th-sort>
                    </tr>
                </thead>
                <tbody data-enhance class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($porMetodo as $row)
                        <tr data-row class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-3 font-medium text-cream-900 dark:text-cream-50">
                                <span class="inline-flex items-center gap-2">
                                    <x-icon :name="$row['es_efectivo'] ? 'banknote' : 'credit-card'" class="w-4 h-4 text-cream-500" />
                                    {{ $row['nombre'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-emerald-700 dark:text-emerald-400">
                                {{ $row['ingresos'] > 0 ? '$ ' . number_format($row['ingresos'], 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-rose-700 dark:text-rose-400">
                                {{ $row['egresos'] > 0 ? '$ ' . number_format($row['egresos'], 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold {{ $row['neto'] >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }}">
                                {{ ($row['neto'] < 0 ? '-' : '') . '$ ' . number_format(abs($row['neto']), 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-cream-600 dark:text-cream-400">
                                No hay movimientos en este rango de fechas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($porMetodo->isNotEmpty())
                    <tfoot class="bg-cream-100 dark:bg-cream-900/40 font-semibold text-cream-900 dark:text-cream-50">
                        <tr>
                            <td class="px-4 py-3">Total</td>
                            <td class="px-4 py-3 text-right tabular-nums text-emerald-700 dark:text-emerald-400">$ {{ number_format($totalIngresos, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-rose-700 dark:text-rose-400">$ {{ number_format($totalEgresos, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums {{ $neto >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }}">
                                {{ ($neto < 0 ? '-' : '') . '$ ' . number_format(abs($neto), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </x-table-enhanced>

    {{-- Egresos por módulo --}}
    <x-card padding="p-4" class="mb-5">
        <h3 class="font-semibold text-sm text-cream-800 dark:text-cream-200 mb-3">Egresos por módulo</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
            @php
                $modulos = [
                    ['label' => 'Caja',         'icon' => 'wallet',          'key' => 'caja'],
                    ['label' => 'Mercado',      'icon' => 'shopping-basket', 'key' => 'mercado'],
                    ['label' => 'Nómina',       'icon' => 'banknote',        'key' => 'nomina'],
                    ['label' => 'Gastos fijos', 'icon' => 'receipt',         'key' => 'fijos'],
                ];
            @endphp
            @foreach ($modulos as $m)
                <div class="rounded-xl bg-cream-50 dark:bg-cream-900/40 border border-cream-200 dark:border-cream-800 px-3 py-2">
                    <p class="text-[11px] uppercase tracking-wide text-cream-500 flex items-center gap-1">
                        <x-icon :name="$m['icon']" class="w-3.5 h-3.5" /> {{ $m['label'] }}
                    </p>
                    <p class="text-sm font-bold tabular-nums text-rose-700 dark:text-rose-400">
                        $ {{ number_format($egresosPorModulo[$m['key']] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            @endforeach
        </div>
    </x-card>

    {{-- Gráfica: ingresos vs egresos por método --}}
    @if ($porMetodo->isNotEmpty())
        @php
            $cats          = $porMetodo->pluck('nombre')->values()->all();
            $serieIngresos = $porMetodo->pluck('ingresos')->map(fn ($v) => (int) $v)->values()->all();
            $serieEgresos  = $porMetodo->pluck('egresos')->map(fn ($v) => (int) $v)->values()->all();
        @endphp
        <x-card padding="p-4">
            <h3 class="font-semibold text-sm text-cream-800 dark:text-cream-200 mb-3">Ingresos vs egresos por método</h3>
            <x-chart
                id="chart-consolidado-metodo"
                type="bar"
                :series="[
                    ['name' => 'Ingresos', 'data' => $serieIngresos],
                    ['name' => 'Egresos',  'data' => $serieEgresos],
                ]"
                :options="[
                    'colors' => ['#10b981', '#f43f5e'],
                    'plotOptions' => ['bar' => ['borderRadius' => 6, 'columnWidth' => '55%']],
                    'stroke' => ['show' => true, 'width' => 2, 'colors' => ['transparent']],
                    'xaxis' => ['categories' => $cats],
                    'legend' => ['show' => true, 'position' => 'top'],
                ]"
                height="320"
            />
        </x-card>
    @endif
@endsection
