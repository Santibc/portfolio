@extends('layouts.app')

@section('header', 'Dashboard Caja')

@section('content')
    <x-page-header
        title="Dashboard Caja"
        subtitle="Historial de turnos y ventas"
        icon="gauge"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="shopping-cart" :href="route('caja.index')">
                Ir a la caja
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Filtro de rango --}}
    <form method="GET" action="{{ route('caja-dashboard.index') }}" class="mb-5">
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
        <x-stat-card icon="wallet"      color="primary" label="Total ventas"     :value="'$ ' . number_format($totalVentas, 0, ',', '.')" />
        <x-stat-card icon="banknote"    color="emerald" label="Efectivo"         :value="'$ ' . number_format($totalEfectivo, 0, ',', '.')" />
        <x-stat-card icon="credit-card" color="sky"     label="Transferencias"   :value="'$ ' . number_format($totalNoEfvo, 0, ',', '.')" />
        <x-stat-card icon="unlock"      color="accent"  label="Turnos abiertos"  :value="(string) $turnosAbiertos" />
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <x-stat-card icon="trending-down" color="rose" label="Total gastos" :value="'$ ' . number_format($totalGastos, 0, ',', '.')" />
        <x-stat-card icon="piggy-bank"    color="primary" label="Ahorros descontados" :value="'$ ' . number_format($totalAhorros, 0, ',', '.')" />
        <x-stat-card icon="trending-up"   :color="$neto >= 0 ? 'emerald' : 'rose'" label="Neto (ventas − gastos − ahorros)" :value="($neto < 0 ? '-' : '') . '$ ' . number_format(abs($neto), 0, ',', '.')" />
    </div>

    {{-- Tabla de turnos --}}
    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-100 dark:bg-cream-900/40 text-cream-800 dark:text-cream-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold">Fecha</th>
                        <th class="text-left px-4 py-3 font-semibold">Apertura</th>
                        <th class="text-left px-4 py-3 font-semibold">Cierre</th>
                        <th class="text-right px-4 py-3 font-semibold">Base</th>
                        <th class="text-right px-4 py-3 font-semibold">Ventas</th>
                        <th class="text-right px-4 py-3 font-semibold">Gastos</th>
                        <th class="text-right px-4 py-3 font-semibold">Neto</th>
                        <th class="text-right px-4 py-3 font-semibold">Declarado</th>
                        <th class="text-right px-4 py-3 font-semibold">Diferencia</th>
                        <th class="text-center px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($turnos as $t)
                        @php
                            $abierto    = $t->cerrado_en === null;
                            $diferencia = $t->diferencia_cierre;
                            $colorDif   = $diferencia === null
                                ? 'neutral'
                                : ($diferencia > 0 ? 'success' : ($diferencia < 0 ? 'danger' : 'neutral'));
                        @endphp
                        <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-3 font-medium text-cream-900 dark:text-cream-50">{{ $t->abierto_en->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-cream-700 dark:text-cream-300">
                                {{ $t->abierto_en->format('H:i') }}
                                <span class="text-xs text-cream-500 block">por {{ $t->aperturadoPor?->name ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-cream-700 dark:text-cream-300">
                                @if ($abierto)
                                    <span class="italic text-cream-500">—</span>
                                @else
                                    {{ $t->cerrado_en->format('H:i') }}
                                    <span class="text-xs text-cream-500 block">por {{ $t->cerradoPor?->name ?? '—' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-cream-700 dark:text-cream-300">{{ $t->base_inicial_formateada }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold text-primary-700 dark:text-primary-300">{{ $t->total_ventas_formateado }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-rose-700 dark:text-rose-400">{{ $t->total_gastos > 0 ? $t->total_gastos_formateado : '—' }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold {{ $t->neto >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }}">{{ $t->neto_formateado }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-cream-700 dark:text-cream-300">{{ $t->total_declarado_formateado }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">
                                @if ($diferencia === null)
                                    <span class="text-cream-500">—</span>
                                @else
                                    <x-badge :variant="$colorDif">{{ $t->diferencia_cierre_formateada }}</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($abierto)
                                    <x-badge variant="success" icon="unlock">Abierto</x-badge>
                                @else
                                    <x-badge variant="neutral" icon="lock">Cerrado</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('caja-dashboard.show', $t) }}"
                                   class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium text-xs">
                                    <x-icon name="eye" class="w-3.5 h-3.5" /> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-10 text-center text-cream-600 dark:text-cream-400">
                                No hay turnos registrados en este rango.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
