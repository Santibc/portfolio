@extends('layouts.app')

@section('header', 'Kardex · '.$producto->nombre)

@section('content')
    @php
        use App\Enums\TipoMovimientoInventario;
        use App\Models\ProductoMercado;
        $esAdmin = auth()->user()->isAdmin();
    @endphp

    <x-page-header
        :title="$producto->nombre"
        :subtitle="'Kardex de inventario · '.$producto->unidad_empaque"
        icon="package"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('inventario.index')">Inventario</x-button>
            @if ($esAdmin && $producto->controla_inventario)
                <x-button variant="primary" icon="settings" data-hs-overlay="#modal-ajuste">Ajustar stock</x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <x-alert variant="danger" class="mb-4">
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    @unless ($producto->controla_inventario)
        <x-alert variant="warning" class="mb-4">
            Este producto ya no controla inventario. Se muestra su historial, pero las compras nuevas no suman stock.
        </x-alert>
    @endunless

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-stat-card icon="package" label="Stock actual"
                     :value="ProductoMercado::formatearCantidad($stock).' '.$producto->unidad_empaque"
                     :color="$stock > 0 ? 'primary' : 'rose'" />
        <x-stat-card icon="archive" label="Saldo al {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}"
                     :value="ProductoMercado::formatearCantidad($saldoInicial)" color="sky" />
        <x-stat-card icon="list" label="Movimientos en el rango" :value="$movimientos->count()" color="accent" />
    </div>

    <form action="{{ route('inventario.show', $producto) }}" method="GET" class="mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[140px]">
                <x-input type="date" label="Desde" name="desde" :value="$desde" x-data x-on:change="$el.form.submit()" />
            </div>
            <div class="flex-1 min-w-[140px]">
                <x-input type="date" label="Hasta" name="hasta" :value="$hasta" x-data x-on:change="$el.form.submit()" />
            </div>
        </div>
    </form>

    <x-table-enhanced :filters="[['col' => 1, 'label' => 'Tipo']]" search-placeholder="Buscar movimiento..." :per-page="25">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-100 dark:bg-cream-900/40 text-cream-800 dark:text-cream-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold">Fecha</th>
                        <th class="text-left px-4 py-3 font-semibold">Tipo</th>
                        <th class="text-left px-4 py-3 font-semibold">Detalle</th>
                        <th class="text-left px-4 py-3 font-semibold">Usuario</th>
                        <th class="text-right px-4 py-3 font-semibold">Cantidad</th>
                        <th class="text-right px-4 py-3 font-semibold">Saldo</th>
                    </tr>
                </thead>
                <tbody data-enhance class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($movimientos as $m)
                        <tr data-row class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-3 whitespace-nowrap text-cream-700 dark:text-cream-300">{{ $m->created_at->format('d/m/Y h:i a') }}</td>
                            <td class="px-4 py-3"><x-badge :variant="$m->tipo->badge()">{{ $m->tipo->label() }}</x-badge></td>
                            <td class="px-4 py-3 text-cream-700 dark:text-cream-300">
                                @switch($m->tipo)
                                    @case(TipoMovimientoInventario::Compra)
                                        Compra de mercado
                                        @if ($m->registroMercado)
                                            <span class="text-xs text-cream-500">· {{ $m->registroMercado->valor_formateado }}</span>
                                        @endif
                                        @break
                                    @case(TipoMovimientoInventario::Venta)
                                        {{ $m->ventaItem?->nombre_snapshot ?? 'Venta' }}
                                        @if ($m->ventaItem)
                                            <span class="text-xs text-cream-500">· Venta #{{ $m->ventaItem->venta_id }} × {{ $m->ventaItem->cantidad }}</span>
                                        @endif
                                        @break
                                    @default
                                        {{ $m->motivo }}
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-cream-600 dark:text-cream-400 text-xs">{{ $m->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold {{ (float) $m->cantidad >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }}">
                                {{ $m->cantidad_formateada }}
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold text-cream-900 dark:text-cream-50">
                                {{ ProductoMercado::formatearCantidad($m->saldo) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <x-empty-state icon="list" title="Sin movimientos en este rango"
                                               description="Las compras de mercado, ventas de caja y ajustes de este producto aparecerán aquí." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-table-enhanced>

    @if ($esAdmin && $producto->controla_inventario)
        <x-modal id="modal-ajuste" title="Ajustar stock de {{ $producto->nombre }}">
            <form action="{{ route('inventario.ajustes.store', $producto) }}" method="POST" class="space-y-4">
                @csrf
                <p class="text-sm text-cream-600 dark:text-cream-400">
                    Stock actual: <strong class="text-cream-900 dark:text-cream-50">{{ ProductoMercado::formatearCantidad($stock) }} {{ $producto->unidad_empaque }}</strong>.
                    Usa una entrada para cargar el stock inicial o corregir un conteo, y una salida para daños o pérdidas.
                </p>
                <x-select
                    label="Tipo de ajuste"
                    name="tipo"
                    :options="['entrada' => 'Entrada (suma al stock)', 'salida' => 'Salida (resta del stock)']"
                    :value="old('tipo', 'entrada')"
                    required
                />
                <x-input label="Cantidad ({{ $producto->unidad_empaque }})" name="cantidad" type="number" step="0.01" min="0.01"
                         :value="old('cantidad')" required />
                <x-input label="Motivo" name="motivo" :value="old('motivo')" maxlength="255"
                         placeholder="Ej. Stock inicial, conteo físico, producto dañado" required />
                <div class="flex justify-end gap-2 pt-2">
                    <x-button type="button" variant="ghost" data-hs-overlay="#modal-ajuste">Cancelar</x-button>
                    <x-button type="submit" variant="primary" icon="save">Registrar ajuste</x-button>
                </div>
            </form>
        </x-modal>
    @endif
@endsection
