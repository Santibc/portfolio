@extends('layouts.app')

@section('header', 'Mercado completado')

@section('content')
    @php
        $items        = $mercado->items;
        $registros    = $mercado->registros;
        $registrados  = $items->where('estado.value', 'registrado')->count();
        $saltados     = $items->where('estado.value', 'saltado')->count();
        $totalItems   = $items->count();
        $totalGastado = (int) $registros->sum('valor');
        $duracion     = $mercado->finalizado_en
            ? $mercado->iniciado_en->diffForHumans($mercado->finalizado_en, ['parts' => 2, 'short' => true, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE])
            : '—';
    @endphp

    <x-page-header
        title="¡Mercado completado!"
        :subtitle="'Finalizado ' . $mercado->finalizado_en?->isoFormat('D MMM, HH:mm')"
        icon="check-circle"
    >
        <x-slot:actions>
            <x-button variant="secondary" icon="bar-chart-3" :href="route('mercado-dashboard.index')">
                Ver dashboard
            </x-button>
            <x-button variant="primary" icon="clipboard-list" :href="route('lista-mercado.index')">
                Volver a Lista
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            icon="wallet"
            label="Total gastado"
            :value="'$ ' . number_format($totalGastado, 0, ',', '.')"
            color="primary"
        />
        <x-stat-card
            icon="check"
            label="Registrados"
            :value="$registrados . ' / ' . $totalItems"
            color="emerald"
        />
        <x-stat-card
            icon="skip-forward"
            label="Saltados"
            :value="(string) $saltados"
            color="accent"
        />
        <x-stat-card
            icon="clock"
            label="Duración"
            :value="$duracion"
            color="sky"
        />
    </div>

    <x-card>
        <h3 class="text-lg font-bold text-cream-900 dark:text-cream-50 mb-4">Resumen por lugar</h3>

        @php
            $porTipo = $items->groupBy('tipo_producto_mercado_id');
        @endphp

        <div class="space-y-3">
            @foreach ($porTipo as $tipoId => $grupo)
                @php
                    $tipo = $grupo->first()->tipo;
                    $reg  = $grupo->where('estado.value', 'registrado');
                    $sal  = $grupo->where('estado.value', 'saltado');
                    $totalTipo = (int) $reg->sum(fn ($i) => $i->registro?->valor ?? 0);
                @endphp
                <div class="flex items-center justify-between flex-wrap gap-2 py-2 border-b border-cream-200 dark:border-cream-800 last:border-0">
                    <div class="flex items-center gap-3">
                        <x-icon name="map-pin" class="w-4 h-4 text-primary-500" />
                        <span class="font-semibold text-cream-900 dark:text-cream-50">{{ $tipo?->nombre ?? '—' }}</span>
                        <div class="flex flex-wrap gap-1.5">
                            @if ($reg->count() > 0)
                                <x-badge variant="success" size="sm">{{ $reg->count() }} registrados</x-badge>
                            @endif
                            @if ($sal->count() > 0)
                                <x-badge variant="neutral" size="sm">{{ $sal->count() }} saltados</x-badge>
                            @endif
                        </div>
                    </div>
                    <span class="font-semibold text-cream-900 dark:text-cream-50">
                        $ {{ number_format($totalTipo, 0, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>
    </x-card>
@endsection
