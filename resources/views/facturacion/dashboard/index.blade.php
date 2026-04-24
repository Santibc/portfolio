@extends('layouts.app')

@section('title', 'Panel de facturación')

@section('content')
    <x-manzer.page-header
        title="Panel de facturación"
        description="Resumen de actividad del mes en curso.">
        <x-slot name="actions">
            <x-manzer.button variant="primary" icon="plus-lg" href="{{ route('facturacion.facturas.create') }}">
                Nueva factura
            </x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    <div class="grid gap-4 md:grid-cols-3">
        <x-manzer.stat-card icon="receipt" :value="$stats['emitidas_mes']" label="Emitidas este mes" variant="success" />
        <x-manzer.stat-card icon="hourglass-split" :value="$stats['pendientes']" label="Borradores pendientes" variant="warning" />
        <x-manzer.stat-card icon="archive" :value="$stats['total_facturas']" label="Total histórico" variant="info" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Últimas 10 facturas</h3>
            @if ($ultimas->isEmpty())
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Aún no has creado facturas.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                            <tr>
                                <th class="px-3 py-2">#</th>
                                <th class="px-3 py-2">Fecha</th>
                                <th class="px-3 py-2">Cliente</th>
                                <th class="px-3 py-2">Total</th>
                                <th class="px-3 py-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach ($ultimas as $f)
                                <tr>
                                    <td class="px-3 py-2 font-mono font-semibold"><a href="{{ route('facturacion.facturas.edit', $f) }}" class="text-primary-600 hover:underline">{{ $f->numero_siigo ?? $f->numero_interno }}</a></td>
                                    <td class="px-3 py-2">{{ $f->fecha?->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2">{{ $f->cliente?->nombre }}</td>
                                    <td class="px-3 py-2">{{ $f->moneda?->simbolo }} {{ number_format($f->total, 2, ',', '.') }}</td>
                                    <td class="px-3 py-2">
                                        @php
                                            $map = ['borrador' => 'secondary', 'emitida' => 'info', 'enviada' => 'primary', 'pagada' => 'success', 'anulada' => 'danger'];
                                        @endphp
                                        <x-manzer.badge :variant="$map[$f->estado] ?? 'secondary'" :text="ucfirst($f->estado)" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Facturado por moneda (mes)</h3>
            @if ($porMoneda->isEmpty())
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Sin movimientos este mes.</p>
            @else
                <ul class="space-y-3">
                    @foreach ($porMoneda as $grupo)
                        <li class="flex items-center justify-between border-b border-zinc-100 pb-2 last:border-0 dark:border-zinc-800">
                            <span class="text-sm font-medium">{{ $grupo->moneda?->codigo }}</span>
                            <span class="text-base font-bold">{{ $grupo->moneda?->simbolo }} {{ number_format((float) $grupo->total_moneda, 2, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
