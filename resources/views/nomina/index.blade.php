@extends('layouts.app')

@section('header', 'Nómina')

@section('content')
    <x-page-header
        title="Nómina"
        subtitle="Períodos de pago de sueldos liquidados"
        icon="banknote"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="gauge" :href="route('nomina-dashboard.index')">Dashboard</x-button>
            <x-button variant="ghost" icon="credit-card" :href="route('nomina-pagos.masivo')">Pago masivo</x-button>
            <x-button variant="primary" icon="calculator" :href="route('nomina.create')">Liquidar nómina</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-100 dark:bg-cream-900/40 text-cream-800 dark:text-cream-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold">Período</th>
                        <th class="text-center px-4 py-3 font-semibold">Empl.</th>
                        <th class="text-right px-4 py-3 font-semibold">Devengado</th>
                        <th class="text-right px-4 py-3 font-semibold">Deducido</th>
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
                            <td class="px-4 py-3">
                                <div class="font-medium text-cream-900 dark:text-cream-50">{{ $n->descripcion }}</div>
                                <div class="text-xs text-cream-500">{{ $n->tipo->label() }} · {{ $n->dias }} días</div>
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums text-cream-700 dark:text-cream-300">{{ $n->detalles->count() }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-cream-700 dark:text-cream-300">{{ $n->total_devengado_formateado }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-rose-700 dark:text-rose-400">{{ $n->total_deducido_formateado }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold text-primary-700 dark:text-primary-300">{{ $n->total_neto_formateado }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-emerald-700 dark:text-emerald-400">{{ $n->total_pagado_formateado }}</td>
                            <td class="px-4 py-3 text-right tabular-nums {{ $n->total_pendiente > 0 ? 'text-amber-700 dark:text-amber-400 font-semibold' : 'text-cream-500' }}">{{ $n->total_pendiente_formateado }}</td>
                            <td class="px-4 py-3 text-center">
                                <x-badge :variant="$n->estado->badge()">{{ $n->estado->label() }}</x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('nomina.show', $n) }}" class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium text-xs">
                                        <x-icon name="eye" class="w-3.5 h-3.5" /> Ver
                                    </a>
                                    @if ($n->estado !== \App\Enums\EstadoNomina::Pagada)
                                        <a href="{{ route('nomina.edit', $n) }}" class="inline-flex items-center gap-1 text-sky-700 hover:text-sky-900 dark:text-sky-300 dark:hover:text-sky-100 font-medium text-xs">
                                            <x-icon name="edit" class="w-3.5 h-3.5" /> Editar
                                        </a>
                                    @endif
                                    <form action="{{ route('nomina.destroy', $n) }}" method="POST" class="inline"
                                          onsubmit="return confirm('¿Eliminar esta nómina y todos sus pagos? Esta acción no se puede deshacer.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-rose-700 hover:text-rose-900 dark:text-rose-300 dark:hover:text-rose-100 font-medium text-xs">
                                            <x-icon name="trash-2" class="w-3.5 h-3.5" /> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <x-empty-state
                                    icon="banknote"
                                    title="Aún no hay nóminas"
                                    description="Liquida tu primer período para generar los pagos de sueldo de los empleados."
                                >
                                    <x-slot:actions>
                                        <x-button variant="primary" icon="calculator" :href="route('nomina.create')">Liquidar nómina</x-button>
                                    </x-slot:actions>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
