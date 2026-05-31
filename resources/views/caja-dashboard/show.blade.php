@extends('layouts.app')

@section('header', 'Turno de caja')

@section('content')
    <x-page-header
        :title="'Turno · ' . $turno->abierto_en->format('Y-m-d')"
        :subtitle="'Apertura ' . $turno->abierto_en->format('H:i') . ' · por ' . ($turno->aperturadoPor?->name ?? '—')"
        icon="receipt"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('caja-dashboard.index')">Volver</x-button>
            @if ($turno->cerrado_en === null)
                <x-button variant="primary" icon="shopping-cart" :href="route('caja.index')">Ir a la caja</x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    {{-- Estado y stats --}}
    <div class="grid grid-cols-2 gap-3 mb-3">
        <x-stat-card icon="wallet"      color="primary" label="Total ventas"        :value="$turno->total_ventas_formateado" />
        <x-stat-card icon="dollar-sign" color="accent"  label="Base inicial"        :value="$turno->base_inicial_formateada" />
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <x-stat-card icon="trending-down" color="rose" label="Total gastos" :value="$turno->total_gastos_formateado" />
        <x-stat-card icon="piggy-bank"    color="primary" label="Ahorros descontados" :value="$turno->total_ahorros_formateado" />
        <x-stat-card icon="trending-up"   :color="$turno->neto >= 0 ? 'emerald' : 'rose'" label="Neto (ventas − gastos − ahorros)" :value="$turno->neto_formateado" />
    </div>

    @if ($turno->cerrado_en !== null)
        <x-card class="mb-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-xs uppercase tracking-wide text-cream-500">Cerrada</p>
                    <p class="font-semibold text-cream-900 dark:text-cream-50">{{ $turno->cerrado_en->format('Y-m-d H:i') }}</p>
                    <p class="text-xs text-cream-600 dark:text-cream-400">por {{ $turno->cerradoPor?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-cream-500">Total declarado</p>
                    <p class="font-semibold text-cream-900 dark:text-cream-50">{{ $turno->total_declarado_formateado }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-cream-500">Diferencia</p>
                    @php
                        $diferencia = $turno->diferencia_cierre;
                        $colorDif   = $diferencia === null
                            ? 'neutral'
                            : ($diferencia > 0 ? 'success' : ($diferencia < 0 ? 'danger' : 'neutral'));
                    @endphp
                    <x-badge :variant="$colorDif" size="lg">{{ $turno->diferencia_cierre_formateada }}</x-badge>
                </div>
            </div>
        </x-card>
    @endif

    {{-- Desglose por método --}}
    @if ($desglosePorMetodo->isNotEmpty())
        <x-card class="mb-5" padding="p-4">
            <h3 class="font-semibold text-sm text-cream-800 dark:text-cream-200 mb-3">Desglose por método de pago</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
                @foreach ($desglosePorMetodo as $d)
                    <div class="rounded-xl bg-cream-50 dark:bg-cream-900/40 border border-cream-200 dark:border-cream-800 px-3 py-2">
                        <p class="text-[10px] uppercase tracking-wide text-cream-500 flex items-center gap-1">
                            <x-icon :name="$d['es_efectivo'] ? 'banknote' : 'credit-card'" class="w-3 h-3" />
                            {{ $d['nombre'] }}
                        </p>
                        @if ($d['gastos'] > 0)
                            <p class="text-[11px] text-cream-500 tabular-nums leading-tight">
                                $ {{ number_format($d['ventas'], 0, ',', '.') }}
                                <span class="text-rose-600 dark:text-rose-400">− $ {{ number_format($d['gastos'], 0, ',', '.') }}</span>
                            </p>
                        @endif
                        <p class="text-sm font-bold tabular-nums {{ $d['monto'] < 0 ? 'text-rose-700 dark:text-rose-400' : 'text-cream-900 dark:text-cream-50' }}">$ {{ number_format($d['monto'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    {{-- Tabla de ventas --}}
    <x-card padding="p-0">
        <div class="px-4 py-3 border-b border-cream-200 dark:border-cream-800">
            <h3 class="font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                <x-icon name="list" class="w-4 h-4" />
                Ventas del turno
                <span class="text-xs font-normal text-cream-600 dark:text-cream-400">({{ $turno->ventas->count() }})</span>
            </h3>
        </div>

        <div x-data="{ open: null }">
            <table class="w-full text-sm">
                <thead class="bg-cream-50 dark:bg-cream-900/30 text-cream-700 dark:text-cream-300">
                    <tr>
                        <th class="text-left px-4 py-2 font-semibold">Hora</th>
                        <th class="text-left px-4 py-2 font-semibold">Cajero</th>
                        <th class="text-right px-4 py-2 font-semibold">Items</th>
                        <th class="text-right px-4 py-2 font-semibold">Total</th>
                        <th class="text-right px-4 py-2 font-semibold">Cambio</th>
                        <th class="text-left px-4 py-2 font-semibold">Métodos</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($turno->ventas as $v)
                        <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-2.5 font-mono text-xs text-cream-700 dark:text-cream-300">{{ $v->created_at->format('H:i:s') }}</td>
                            <td class="px-4 py-2.5 text-cream-700 dark:text-cream-300">{{ $v->user?->name ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-right tabular-nums">{{ $v->items->sum('cantidad') }}</td>
                            <td class="px-4 py-2.5 text-right tabular-nums font-semibold text-cream-900 dark:text-cream-50">{{ $v->total_formateado }}</td>
                            <td class="px-4 py-2.5 text-right tabular-nums text-emerald-700 dark:text-emerald-400">{{ $v->cambio_formateado }}</td>
                            <td class="px-4 py-2.5">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($v->pagos as $p)
                                        <span class="inline-flex items-center font-medium rounded-full bg-cream-100 dark:bg-cream-800 text-cream-800 dark:text-cream-200 text-[10px] px-2 py-0.5">
                                            {{ $p->metodo?->nombre }} <span class="ml-1 font-bold">$ {{ number_format((int) $p->monto, 0, ',', '.') }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" @click="open === {{ $v->id }} ? open = null : open = {{ $v->id }}"
                                            class="inline-flex items-center gap-1 text-cream-700 hover:text-cream-900 dark:text-cream-300 dark:hover:text-cream-100 text-xs font-medium">
                                        <x-icon name="eye" class="w-3.5 h-3.5" />
                                        <span x-text="open === {{ $v->id }} ? 'Ocultar' : 'Ver'"></span>
                                    </button>
                                    <a href="{{ route('caja.venta.edit', $v) }}"
                                       class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 text-xs font-medium">
                                        <x-icon name="edit" class="w-3.5 h-3.5" /> Editar
                                    </a>
                                    <form action="{{ route('caja.venta.destroy', $v) }}" method="POST" onsubmit="return confirm('¿Eliminar esta venta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 text-xs font-medium">
                                            <x-icon name="trash-2" class="w-3.5 h-3.5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr x-show="open === {{ $v->id }}" x-cloak>
                            <td colspan="7" class="bg-cream-50 dark:bg-cream-900/40 px-6 py-3">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-[10px] uppercase tracking-wide text-cream-500 mb-1.5">Items</h4>
                                        <ul class="text-sm divide-y divide-cream-200 dark:divide-cream-800">
                                            @foreach ($v->items as $it)
                                                <li class="py-1.5 flex items-center justify-between gap-2">
                                                    <span class="text-cream-800 dark:text-cream-100">{{ $it->cantidad }} × {{ $it->nombre_snapshot }}</span>
                                                    <span class="font-semibold tabular-nums">{{ $it->subtotal_formateado }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] uppercase tracking-wide text-cream-500 mb-1.5">Pagos</h4>
                                        <ul class="text-sm divide-y divide-cream-200 dark:divide-cream-800">
                                            @foreach ($v->pagos as $p)
                                                <li class="py-1.5 flex items-center justify-between gap-2">
                                                    <span class="text-cream-800 dark:text-cream-100">{{ $p->metodo?->nombre ?? '—' }}@if ($p->referencia) <span class="text-xs text-cream-500">· {{ $p->referencia }}</span>@endif</span>
                                                    <span class="font-semibold tabular-nums">{{ $p->monto_formateado }}</span>
                                                </li>
                                            @endforeach
                                            @if ($v->efectivo_recibido > 0)
                                                <li class="py-1.5 flex items-center justify-between gap-2 text-cream-600 dark:text-cream-400 text-xs">
                                                    <span>Efectivo recibido extra</span>
                                                    <span class="tabular-nums">{{ $v->efectivo_recibido_formateado }}</span>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                @if ($v->notas)
                                    <p class="mt-3 text-xs text-cream-600 dark:text-cream-400 italic">{{ $v->notas }}</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-cream-600 dark:text-cream-400">
                                No hay ventas en este turno todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Tabla de gastos del turno --}}
    <x-card padding="p-0" class="mt-5">
        <div class="px-4 py-3 border-b border-cream-200 dark:border-cream-800 flex items-center justify-between gap-3">
            <h3 class="font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                <x-icon name="wallet" class="w-4 h-4" />
                Gastos del turno
                <span class="text-xs font-normal text-cream-600 dark:text-cream-400">({{ $turno->gastos->count() }})</span>
            </h3>
            <div class="flex items-center gap-2 text-xs text-cream-600 dark:text-cream-400">
                <span>Generales: <strong class="text-cream-900 dark:text-cream-50">$ {{ number_format($totalGastosGeneral, 0, ',', '.') }}</strong></span>
                <span class="text-cream-400">·</span>
                <span>Turnos: <strong class="text-cream-900 dark:text-cream-50">$ {{ number_format($totalGastosTurno, 0, ',', '.') }}</strong></span>
                @if ($turno->cerrado_en === null)
                    <x-button variant="primary" size="xs" icon="plus" :href="route('gastos.create')">Nuevo</x-button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-50 dark:bg-cream-900/30 text-cream-700 dark:text-cream-300">
                    <tr>
                        <th class="text-left px-4 py-2 font-semibold">Hora</th>
                        <th class="text-left px-4 py-2 font-semibold">Tipo</th>
                        <th class="text-left px-4 py-2 font-semibold">Concepto / Trabajador</th>
                        <th class="text-right px-4 py-2 font-semibold">Valor</th>
                        <th class="text-right px-4 py-2 font-semibold">Ahorro</th>
                        <th class="text-left px-4 py-2 font-semibold">Método</th>
                        <th class="text-left px-4 py-2 font-semibold">Cajero</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($turno->gastos as $g)
                        <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-2.5 font-mono text-xs text-cream-700 dark:text-cream-300">{{ $g->created_at->format('H:i:s') }}</td>
                            <td class="px-4 py-2.5">
                                @if ($g->tipo === \App\Enums\TipoGasto::Turno)
                                    <span class="inline-flex items-center font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200 text-xs px-2.5 py-1">Pago de turno</span>
                                @else
                                    <span class="inline-flex items-center font-semibold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200 text-xs px-2.5 py-1">Gasto general</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-cream-800 dark:text-cream-200">
                                @if ($g->tipo === \App\Enums\TipoGasto::Turno)
                                    <span class="font-medium">{{ $g->trabajadorTurno?->nombre ?? '—' }}</span>
                                    @if ($g->observacion)
                                        <span class="block text-xs text-cream-500">{{ $g->observacion }}</span>
                                    @endif
                                @else
                                    {{ $g->observacion ?? '—' }}
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right tabular-nums font-semibold text-rose-700 dark:text-rose-400">{{ $g->valor_formateado }}</td>
                            <td class="px-4 py-2.5 text-right tabular-nums {{ $g->ahorro > 0 ? 'text-primary-700 dark:text-primary-300 font-semibold' : 'text-cream-400' }}">{{ $g->ahorro > 0 ? $g->ahorro_formateado : '—' }}</td>
                            <td class="px-4 py-2.5">
                                @if ($g->metodoPago)
                                    <span class="inline-flex items-center gap-1 font-medium rounded-full bg-cream-100 dark:bg-cream-800 text-cream-800 dark:text-cream-200 text-xs px-2.5 py-1">
                                        <x-icon :name="$g->metodoPago->es_efectivo ? 'banknote' : 'credit-card'" class="w-3 h-3" />
                                        {{ $g->metodoPago->nombre }}
                                    </span>
                                @else
                                    <span class="text-cream-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-cream-700 dark:text-cream-300">{{ $g->user?->name ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('gastos.edit', $g) }}"
                                       class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 text-xs font-medium">
                                        <x-icon name="edit" class="w-3.5 h-3.5" /> Editar
                                    </a>
                                    <form action="{{ route('gastos.destroy', $g) }}" method="POST" onsubmit="return confirm('¿Eliminar este gasto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 text-xs font-medium">
                                            <x-icon name="trash-2" class="w-3.5 h-3.5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-cream-600 dark:text-cream-400">
                                No hay gastos en este turno.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
