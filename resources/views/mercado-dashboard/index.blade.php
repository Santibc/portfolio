@extends('layouts.app')

@section('header', 'Dashboard Mercado')

@section('content')
    <x-page-header
        title="Dashboard Mercado"
        subtitle="Auditoría de compras registradas"
        icon="gauge"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="bar-chart-3" :href="route('mercado-dashboard.graficas')">
                Gráficas
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <x-stat-card
            icon="dollar-sign"
            label="Total gastado"
            :value="'$ ' . number_format($totalGastado, 0, ',', '.')"
            color="primary"
        />
        <x-stat-card
            icon="list"
            label="Registros"
            :value="number_format(count($rows), 0, ',', '.')"
            color="accent"
        />
        <x-stat-card
            icon="shopping-basket"
            label="Productos distintos"
            :value="$productosDistintos"
            color="sky"
        />
        <x-stat-card
            icon="trending-up"
            label="Promedio / registro"
            :value="'$ ' . number_format($promedioPorRegistro, 0, ',', '.')"
            color="emerald"
        />
    </div>

    @if ($totalesPorMetodo->isNotEmpty())
        @php $coloresMetodo = ['primary', 'accent', 'sky', 'emerald', 'rose']; @endphp
        <div class="mb-4">
            <p class="text-xs uppercase tracking-wider font-semibold text-cream-600 dark:text-cream-400 mb-2">
                Total por método de pago
            </p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach ($totalesPorMetodo as $i => $metodo)
                    <x-stat-card
                        icon="credit-card"
                        :label="$metodo['nombre'] . ' · ' . number_format($metodo['count'], 0, ',', '.') . ' reg.'"
                        :value="'$ ' . number_format($metodo['total'], 0, ',', '.')"
                        :color="$coloresMetodo[$i % count($coloresMetodo)]"
                    />
                @endforeach
            </div>
        </div>
    @endif

    <form action="{{ route('mercado-dashboard.index') }}" method="GET"
          class="sticky top-16 z-30 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3
                 bg-cream-50/95 dark:bg-surface-dark/95 backdrop-blur
                 border-y border-cream-200 dark:border-cream-800 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-medium text-cream-700 dark:text-cream-300 mb-1">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}"
                       x-data x-on:change="$el.form.submit()"
                       class="block w-full rounded-xl border-cream-300 bg-white px-3 py-2 text-sm text-cream-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100" />
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-medium text-cream-700 dark:text-cream-300 mb-1">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}"
                       x-data x-on:change="$el.form.submit()"
                       class="block w-full rounded-xl border-cream-300 bg-white px-3 py-2 text-sm text-cream-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100" />
            </div>
            <x-button variant="ghost" icon="calendar" :href="route('mercado-dashboard.index')">
                Hoy
            </x-button>
        </div>
    </form>

    @if (count($rows) === 0)
        <x-empty-state
            icon="list"
            title="Sin registros en el período"
            description="No se encontraron registros para el rango de fechas seleccionado. Cambia las fechas o registra una nueva compra."
        />
    @else
        <x-data-table
            :columns="$columns"
            :rows="$rows"
            :searchable="true"
            :paginate="true"
            :perPage="5"
            :filters="[['key' => 'tipo', 'label' => 'Tipo']]"
            empty="Sin registros."
        />
    @endif
@endsection
