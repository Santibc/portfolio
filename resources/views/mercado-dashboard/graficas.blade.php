@extends('layouts.app')

@section('header', 'Gráficas')

@section('content')
    <x-page-header
        title="Gráficas"
        subtitle="Análisis de compras del período"
        icon="bar-chart-3"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('mercado-dashboard.index')">
                Volver al dashboard
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('mercado-dashboard.graficas') }}" method="GET"
          class="sticky top-16 z-30 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3
                 bg-cream-50/95 dark:bg-surface-dark/95 backdrop-blur
                 border-y border-cream-200 dark:border-cream-800 mb-4">
        <div class="flex items-end gap-3">
            <div class="flex-1 max-w-xs">
                <label class="block text-xs font-medium text-cream-700 dark:text-cream-300 mb-1">Período</label>
                <select name="periodo"
                        x-data x-on:change="$el.form.submit()"
                        class="block w-full rounded-xl border-cream-300 bg-white px-3 py-2 text-sm text-cream-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                    @foreach ([7 => 'Últimos 7 días', 14 => 'Últimos 14 días', 30 => 'Últimos 30 días', 60 => 'Últimos 60 días', 90 => 'Últimos 90 días'] as $val => $label)
                        <option value="{{ $val }}" @selected($periodo == $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if ($productoId)
                <input type="hidden" name="producto" value="{{ $productoId }}">
            @endif
        </div>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <x-card>
            <x-slot:header>
                <h2 class="text-base font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                    <x-icon name="trending-up" class="w-4 h-4 text-primary-600 dark:text-primary-300" />
                    Gasto diario
                </h2>
            </x-slot:header>

            @if (array_sum($diariasData) === 0)
                <p class="text-sm text-cream-500 text-center py-8">Sin datos en este período.</p>
            @else
                <x-chart
                    type="area"
                    :series="[['name' => 'Gasto', 'data' => $diariasData]]"
                    :options="[
                        'xaxis' => ['categories' => $diariasCategorias],
                        'fill' => ['type' => 'gradient', 'gradient' => ['shadeIntensity' => 1, 'opacityFrom' => 0.5, 'opacityTo' => 0.05]],
                        'dataLabels' => ['enabled' => false],
                    ]"
                    :height="280"
                />
            @endif
        </x-card>

        <x-card>
            <x-slot:header>
                <h2 class="text-base font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                    <x-icon name="pie-chart" class="w-4 h-4 text-primary-600 dark:text-primary-300" />
                    Gasto por tipo de producto
                </h2>
            </x-slot:header>

            @if (count($porTipoSeries) === 0)
                <p class="text-sm text-cream-500 text-center py-8">Sin datos en este período.</p>
            @else
                <x-chart
                    type="donut"
                    :series="$porTipoSeries"
                    :options="[
                        'labels' => $porTipoLabels,
                        'legend' => ['position' => 'bottom', 'fontSize' => '12px'],
                        'plotOptions' => ['pie' => ['donut' => ['size' => '60%']]],
                    ]"
                    :height="280"
                />
            @endif
        </x-card>

        <x-card>
            <x-slot:header>
                <h2 class="text-base font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                    <x-icon name="bar-chart-3" class="w-4 h-4 text-primary-600 dark:text-primary-300" />
                    Top 10 productos por valor invertido
                </h2>
            </x-slot:header>

            @if (count($topData) === 0)
                <p class="text-sm text-cream-500 text-center py-8">Sin datos en este período.</p>
            @else
                <x-chart
                    type="bar"
                    :series="[['name' => 'Total invertido', 'data' => $topData]]"
                    :options="[
                        'plotOptions' => ['bar' => ['horizontal' => true, 'borderRadius' => 6, 'barHeight' => '70%']],
                        'xaxis' => ['categories' => $topLabels],
                        'dataLabels' => ['enabled' => false],
                    ]"
                    :height="350"
                />
            @endif
        </x-card>

        <x-card>
            <x-slot:header>
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-base font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                        <x-icon name="trending-up" class="w-4 h-4 text-primary-600 dark:text-primary-300" />
                        Variación de precio unitario
                    </h2>
                    <form action="{{ route('mercado-dashboard.graficas') }}" method="GET" class="shrink-0">
                        <input type="hidden" name="periodo" value="{{ $periodo }}">
                        <select name="producto"
                                x-data x-on:change="$el.form.submit()"
                                class="rounded-lg border-cream-300 bg-white px-2 py-1 text-xs text-cream-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                            @foreach ($productos as $p)
                                <option value="{{ $p->id }}" @selected($productoId == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </x-slot:header>

            @if (count($variacionData) === 0)
                <p class="text-sm text-cream-500 text-center py-8">Sin datos para este producto en el período.</p>
            @else
                <x-chart
                    type="line"
                    :series="[['name' => 'Unitario', 'data' => $variacionData]]"
                    :options="[
                        'xaxis' => ['categories' => $variacionCategorias],
                        'stroke' => ['curve' => 'smooth', 'width' => 3],
                        'markers' => ['size' => 4],
                        'dataLabels' => ['enabled' => false],
                    ]"
                    :height="280"
                />
            @endif
        </x-card>

    </div>
@endsection
