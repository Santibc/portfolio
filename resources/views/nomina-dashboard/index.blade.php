@extends('layouts.app')

@section('header', 'Dashboard Nómina')

@section('content')
    <x-page-header
        title="Dashboard Nómina"
        subtitle="Consolidado de sueldos, pagos, ahorros y prestaciones"
        icon="gauge"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="users" :href="route('empleados.index')">Empleados</x-button>
            <x-button variant="primary" icon="calculator" :href="route('nomina.create')">Liquidar nómina</x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Filtro de rango --}}
    <form method="GET" action="{{ route('nomina-dashboard.index') }}" class="mb-5">
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
        </div>
    </form>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
        <x-stat-card icon="trending-up"   color="primary" label="Total devengado" :value="'$ ' . number_format($totalDevengado, 0, ',', '.')" />
        <x-stat-card icon="trending-down" color="rose"    label="Total deducido"  :value="'$ ' . number_format($totalDeducido, 0, ',', '.')" />
        <x-stat-card icon="banknote"      color="emerald" label="Total neto"      :value="'$ ' . number_format($totalNeto, 0, ',', '.')" />
        <x-stat-card icon="check-circle"  color="emerald" label="Pagado"          :value="'$ ' . number_format($totalPagado, 0, ',', '.')" />
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <x-stat-card icon="clock"      color="accent"  label="Pendiente por pagar" :value="'$ ' . number_format($totalPendiente, 0, ',', '.')" />
        <x-stat-card icon="piggy-bank" color="sky"     label="Ahorros acumulados"  :value="'$ ' . number_format($ahorroAcumulado, 0, ',', '.')" />
        <x-stat-card icon="receipt"    color="primary" label="Prestaciones (rango)" :value="'$ ' . number_format($totalPrestaciones, 0, ',', '.')" />
        <x-stat-card icon="alert-circle" color="rose"  label="Prestaciones pendientes" :value="(string) $prestacionesPendientes" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
        {{-- Neto por período --}}
        <x-card class="lg:col-span-2">
            <h3 class="font-semibold text-sm text-cream-800 dark:text-cream-200 mb-3">Neto por período</h3>
            @if ($netoData->isNotEmpty())
                <x-chart
                    type="bar"
                    :series="[['name' => 'Neto', 'data' => $netoData->all()]]"
                    :options="['xaxis' => ['categories' => $netoLabels->all()], 'plotOptions' => ['bar' => ['borderRadius' => 6, 'columnWidth' => '55%']]]"
                    :height="300"
                />
            @else
                <p class="text-sm text-cream-500 py-10 text-center">No hay nóminas en este rango.</p>
            @endif
        </x-card>

        {{-- Pagos por método --}}
        <x-card>
            <h3 class="font-semibold text-sm text-cream-800 dark:text-cream-200 mb-3">Pagos por método</h3>
            @if ($desglosePorMetodo->isNotEmpty())
                <x-chart
                    type="donut"
                    :series="$desglosePorMetodo->pluck('monto')->all()"
                    :options="['labels' => $desglosePorMetodo->pluck('nombre')->all(), 'legend' => ['position' => 'bottom']]"
                    :height="300"
                />
            @else
                <p class="text-sm text-cream-500 py-10 text-center">No hay pagos registrados en este rango.</p>
            @endif
        </x-card>
    </div>

    {{-- Tabla de períodos --}}
    <x-card padding="p-0">
        <div class="px-4 py-3 border-b border-cream-200 dark:border-cream-800">
            <h3 class="font-semibold text-sm text-cream-800 dark:text-cream-200">Períodos en el rango</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-50 dark:bg-cream-900/20 text-cream-800 dark:text-cream-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold">Período</th>
                        <th class="text-right px-4 py-3 font-semibold">Neto</th>
                        <th class="text-right px-4 py-3 font-semibold">Pagado</th>
                        <th class="text-right px-4 py-3 font-semibold">Pendiente</th>
                        <th class="text-center px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($nominas as $n)
                        <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-3 font-medium text-cream-900 dark:text-cream-50">{{ $n->descripcion }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold text-primary-700 dark:text-primary-300">{{ $n->total_neto_formateado }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-emerald-700 dark:text-emerald-400">{{ $n->total_pagado_formateado }}</td>
                            <td class="px-4 py-3 text-right tabular-nums {{ $n->total_pendiente > 0 ? 'text-amber-700 dark:text-amber-400' : 'text-cream-500' }}">{{ $n->total_pendiente_formateado }}</td>
                            <td class="px-4 py-3 text-center"><x-badge :variant="$n->estado->badge()" size="sm">{{ $n->estado->label() }}</x-badge></td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('nomina.show', $n) }}" class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium text-xs">
                                    <x-icon name="eye" class="w-3.5 h-3.5" /> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-cream-600 dark:text-cream-400">No hay nóminas en este rango.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
