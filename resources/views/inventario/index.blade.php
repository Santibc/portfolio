@extends('layouts.app')

@section('header', 'Inventario')

@section('content')
    @php use App\Models\ProductoMercado; @endphp

    <x-page-header
        title="Inventario"
        subtitle="Stock de los productos de mercado que controlan inventario. Las compras suman y las ventas de caja descuentan."
        icon="package"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="shopping-basket" :href="route('productos-mercado.index')">Productos</x-button>
            <x-button variant="primary" icon="shopping-cart" :href="route('registro-mercado.index')">Registrar compra</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <x-stat-card icon="package" label="Productos con inventario" :value="$productos->count()" color="primary" />
        <x-stat-card icon="alert-triangle" label="Agotados" :value="$agotados" :color="$agotados > 0 ? 'rose' : 'emerald'" />
    </div>

    <x-table-enhanced
        :filters="[['col' => 1, 'label' => 'Tipo'], ['col' => 4, 'label' => 'Estado']]"
        search-placeholder="Buscar producto..."
        :per-page="25"
    >
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-100 dark:bg-cream-900/40 text-cream-800 dark:text-cream-200">
                    <tr>
                        <x-th-sort :col="0" class="text-left px-4 py-3 font-semibold">Producto</x-th-sort>
                        <th class="text-left px-4 py-3 font-semibold">Tipo</th>
                        <x-th-sort :col="2" align="right" class="text-right px-4 py-3 font-semibold">Stock</x-th-sort>
                        <th class="text-left px-4 py-3 font-semibold">Último movimiento</th>
                        <th class="text-center px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody data-enhance class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($productos as $p)
                        @php $s = $stock[$p->id] ?? 0; @endphp
                        <tr data-row class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($p->hasImagen())
                                        <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}" class="w-9 h-9 rounded-lg object-cover border border-cream-200 dark:border-cream-700">
                                    @else
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-cream-200 text-cream-500 dark:bg-cream-800 dark:text-cream-400">
                                            <x-icon name="package" class="w-4 h-4" />
                                        </span>
                                    @endif
                                    <span class="font-medium text-cream-900 dark:text-cream-50">{{ $p->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-cream-700 dark:text-cream-300">{{ $p->tipo?->nombre ?? '—' }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">
                                <span class="font-bold {{ $s > 0 ? 'text-cream-900 dark:text-cream-50' : 'text-rose-700 dark:text-rose-400' }}">{{ ProductoMercado::formatearCantidad($s) }}</span>
                                <span class="text-xs text-cream-500">{{ $p->unidad_empaque }}</span>
                            </td>
                            <td class="px-4 py-3 text-cream-600 dark:text-cream-400 text-xs">
                                {{ isset($ultimos[$p->id]) ? $ultimos[$p->id]->format('d/m/Y h:i a') : 'Sin movimientos' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($s > 0)
                                    <x-badge variant="success">Disponible</x-badge>
                                @else
                                    <x-badge variant="danger">Agotado</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('inventario.show', $p) }}" class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium text-xs">
                                    <x-icon name="list" class="w-3.5 h-3.5" /> Kardex
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <x-empty-state
                                    icon="package"
                                    title="Ningún producto controla inventario"
                                    description="Edita un producto de mercado y activa “Controla inventario” para empezar a llevar su stock."
                                >
                                    <x-slot:actions>
                                        <x-button variant="primary" icon="shopping-basket" :href="route('productos-mercado.index')">Ir a productos</x-button>
                                    </x-slot:actions>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-table-enhanced>
@endsection
