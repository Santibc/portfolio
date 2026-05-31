@php
    /** @var \App\Models\TrabajadorTurno $trabajador */
    /** @var \Illuminate\Support\Collection $movimientos */
    $fmt = fn ($v) => '$ ' . number_format((int) $v, 0, ',', '.');
@endphp

<div class="mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3">
        <p class="text-[11px] uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">Total aportado</p>
        <p class="text-lg font-bold tabular-nums text-emerald-800 dark:text-emerald-200">
            {{ $fmt($movimientos->where('tipo', 'aporte')->sum('monto')) }}
        </p>
    </div>
    <div class="rounded-xl bg-rose-50 dark:bg-rose-900/20 px-4 py-3">
        <p class="text-[11px] uppercase tracking-wide text-rose-700 dark:text-rose-300 font-semibold">Total pagado</p>
        <p class="text-lg font-bold tabular-nums text-rose-800 dark:text-rose-200">
            {{ $fmt($movimientos->where('tipo', 'pago')->sum('monto')) }}
        </p>
    </div>
    <div class="rounded-xl bg-primary-50 dark:bg-primary-900/20 px-4 py-3">
        <p class="text-[11px] uppercase tracking-wide text-primary-700 dark:text-primary-300 font-semibold">Saldo acumulado</p>
        <p class="text-lg font-bold tabular-nums text-primary-800 dark:text-primary-100">
            {{ $trabajador->ahorro_acumulado_formateado }}
        </p>
    </div>
</div>

<div class="overflow-x-auto rounded-xl border border-cream-200 dark:border-cream-800">
    <table class="w-full text-sm">
        <thead class="bg-cream-100 dark:bg-cream-900/40 text-cream-800 dark:text-cream-200">
            <tr>
                <th class="text-left px-4 py-2.5 font-semibold">Fecha</th>
                <th class="text-left px-4 py-2.5 font-semibold">Movimiento</th>
                <th class="text-left px-4 py-2.5 font-semibold">Detalle</th>
                <th class="text-right px-4 py-2.5 font-semibold">Monto</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
            @forelse ($movimientos as $m)
                <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                    <td class="px-4 py-2.5 text-cream-700 dark:text-cream-300 whitespace-nowrap">
                        {{ $m['fecha']?->format('Y-m-d H:i') ?? '—' }}
                    </td>
                    <td class="px-4 py-2.5">
                        @if ($m['tipo'] === 'aporte')
                            <span class="inline-flex items-center gap-1 font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200 text-xs px-2.5 py-1">
                                <i data-lucide="arrow-down" class="w-3 h-3"></i> Ahorro
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200 text-xs px-2.5 py-1">
                                <i data-lucide="arrow-up" class="w-3 h-3"></i> Pago de ahorro
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-cream-700 dark:text-cream-300">{{ $m['detalle'] }}</td>
                    <td class="px-4 py-2.5 text-right tabular-nums font-semibold {{ $m['tipo'] === 'aporte' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">
                        {{ $m['tipo'] === 'aporte' ? '+' : '−' }}{{ $fmt($m['monto']) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-cream-500">
                        Este trabajador aún no tiene movimientos de ahorro.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
