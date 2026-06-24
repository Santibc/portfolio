@extends('layouts.app')

@section('header', 'Pagos de ahorros')

@section('content')
    <x-page-header
        title="Pagos de ahorros"
        subtitle="Ahorros acumulados de trabajadores y pagos realizados"
        icon="piggy-bank"
    >
        <x-slot:actions>
            <x-button variant="secondary" icon="users" :href="route('trabajadores-turno.index')">
                Trabajadores
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('pagos-ahorros.index') }}" class="mb-5">
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
            <div>
                <label class="block text-xs font-medium text-cream-700 dark:text-cream-300 mb-1">Trabajador</label>
                <select name="trabajador_turno_id"
                        class="rounded-xl border-cream-300 bg-white px-3 py-2 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                    <option value="">Todos</option>
                    @foreach ($trabajadoresOptions as $id => $nombre)
                        <option value="{{ $id }}" @selected($trabajadorId === (int) $id)>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-cream-700 dark:text-cream-300 mb-1">Movimiento</label>
                <select name="tipo"
                        class="rounded-xl border-cream-300 bg-white px-3 py-2 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                    <option value="" @selected($tipo === '')>Todos</option>
                    <option value="ahorro" @selected($tipo === 'ahorro')>Ahorros</option>
                    <option value="pago" @selected($tipo === 'pago')>Pagos</option>
                </select>
            </div>
            <x-button type="submit" variant="secondary" icon="search" size="sm">Filtrar</x-button>
        </div>
    </form>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <x-stat-card icon="arrow-down"  color="emerald" label="Ahorrado (rango)"   :value="'$ ' . number_format($totalAhorrado, 0, ',', '.')" />
        <x-stat-card icon="arrow-up"    color="rose"    label="Pagado (rango)"     :value="'$ ' . number_format($totalPagado, 0, ',', '.')" />
        <x-stat-card icon="piggy-bank"  color="primary" label="Saldo acumulado"    :value="'$ ' . number_format($saldoAcumulado, 0, ',', '.')" />
        <x-stat-card icon="users"       color="accent"  label="Trabajadores con ahorro" :value="(string) $trabajadoresConAhorro" />
    </div>

    {{-- Tabla de movimientos (aportes + pagos) --}}
    <x-table-enhanced
        :filters="[['col' => 1, 'label' => 'Movimiento']]"
        search-placeholder="Buscar movimiento..."
    >
        <div class="px-4 py-3 border-b border-cream-200 dark:border-cream-800 flex items-center justify-between gap-3">
            <h2 class="font-semibold text-cream-900 dark:text-cream-50">Movimientos de ahorro en el rango</h2>
            <span class="text-xs text-cream-600 dark:text-cream-400">{{ $movimientos->count() }} registros</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-100 dark:bg-cream-900/40 text-cream-800 dark:text-cream-200">
                    <tr>
                        <x-th-sort :col="0" class="text-left px-4 py-3 font-semibold">Fecha</x-th-sort>
                        <th class="text-left px-4 py-3 font-semibold">Movimiento</th>
                        <th class="text-left px-4 py-3 font-semibold">Trabajador</th>
                        <x-th-sort :col="3" align="right" class="text-right px-4 py-3 font-semibold">Monto</x-th-sort>
                        <th class="text-left px-4 py-3 font-semibold">Detalle</th>
                        <th class="text-left px-4 py-3 font-semibold">Registrado por</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody data-enhance class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($movimientos as $m)
                        @php $esAhorro = $m['tipo'] === 'ahorro'; @endphp
                        <tr data-row class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-3 text-cream-700 dark:text-cream-300 whitespace-nowrap">{{ $m['fecha']?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($esAhorro)
                                    <span class="inline-flex items-center gap-1 font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200 text-xs px-2.5 py-1">
                                        <x-icon name="arrow-down" class="w-3 h-3" /> Ahorro
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200 text-xs px-2.5 py-1">
                                        <x-icon name="arrow-up" class="w-3 h-3" /> Pago
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-cream-900 dark:text-cream-50">{{ $m['trabajador'] ?? '—' }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold {{ $esAhorro ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-400' }}">
                                {{ $esAhorro ? '+' : '−' }}$ {{ number_format($m['monto'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-cream-700 dark:text-cream-300">{{ $m['detalle'] ?: '—' }}</td>
                            <td class="px-4 py-3 text-cream-700 dark:text-cream-300">{{ $m['usuario'] ?? '—' }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if ($esAhorro)
                                    @if ($m['turno_id'])
                                        <a href="{{ route('caja-dashboard.show', $m['turno_id']) }}"
                                           class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium text-xs">
                                            <x-icon name="eye" class="w-3.5 h-3.5" /> Ver turno
                                        </a>
                                    @endif
                                @else
                                    <form action="{{ route('pagos-ahorros.destroy', $m['pago_id']) }}" method="POST" class="inline"
                                          onsubmit="return swalConfirm(this, { title: '¿Eliminar este pago?', text: 'El monto volverá al ahorro acumulado del trabajador.', icon: 'warning', confirmButtonText: 'Sí, eliminar' });">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium text-xs">
                                            <x-icon name="trash-2" class="w-3.5 h-3.5" /> Eliminar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-cream-600 dark:text-cream-400">
                                No hay movimientos de ahorro en este rango.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-table-enhanced>
@endsection
