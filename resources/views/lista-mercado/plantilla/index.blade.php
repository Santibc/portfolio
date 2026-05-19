@extends('layouts.app')

@section('header', 'Plantilla — Lista Mercado')

@section('content')
    <x-page-header
        :title="$lista->nombre"
        subtitle="Productos que se compran semanalmente"
        icon="list-checks"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('lista-mercado.index')">
                Volver
            </x-button>
            @if ($puedeEditar && $productosDisponibles->isNotEmpty())
                <x-button variant="primary" icon="plus" data-hs-overlay="#modal-agregar-item">
                    Agregar producto
                </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if (! $puedeEditar)
        <x-alert variant="warning" class="mb-4" title="Mercado en progreso">
            Termina el mercado actual para editar la lista.
            <a href="{{ route('lista-mercado.index') }}" class="font-semibold underline">Ir al mercado</a>.
        </x-alert>
    @endif

    <x-card padding="p-0" clip>
        @if ($items->isEmpty())
            <div class="py-12">
                <x-empty-state
                    icon="list-checks"
                    title="La lista está vacía"
                    description="Agrega productos del catálogo para que aparezcan al iniciar un mercado."
                >
                    @if ($puedeEditar && $productosDisponibles->isNotEmpty())
                        <x-slot:actions>
                            <x-button variant="primary" icon="plus" data-hs-overlay="#modal-agregar-item">
                                Agregar producto
                            </x-button>
                        </x-slot:actions>
                    @endif
                </x-empty-state>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-cream-200 dark:border-cream-800 bg-cream-50/50 dark:bg-cream-900/40">
                            <th class="text-left text-xs uppercase tracking-wider font-semibold text-cream-700 dark:text-cream-300 px-4 py-3 whitespace-nowrap">Producto</th>
                            <th class="text-left text-xs uppercase tracking-wider font-semibold text-cream-700 dark:text-cream-300 px-4 py-3 whitespace-nowrap">Lugar</th>
                            <th class="text-left text-xs uppercase tracking-wider font-semibold text-cream-700 dark:text-cream-300 px-4 py-3 whitespace-nowrap">Unidad</th>
                            <th class="text-left text-xs uppercase tracking-wider font-semibold text-cream-700 dark:text-cream-300 px-4 py-3 whitespace-nowrap">Cantidad sugerida</th>
                            <th class="text-left text-xs uppercase tracking-wider font-semibold text-cream-700 dark:text-cream-300 px-4 py-3 whitespace-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                        @foreach ($items as $item)
                            @php $p = $item->producto; @endphp
                            <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30 transition-colors">
                                <td class="px-4 py-3 text-cream-800 dark:text-cream-200">
                                    <div class="inline-flex items-center gap-2">
                                        @if ($p && $p->hasImagen())
                                            <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}"
                                                 class="w-9 h-9 rounded-lg object-cover border border-cream-200 dark:border-cream-700">
                                        @else
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-cream-200 text-cream-500 dark:bg-cream-800 dark:text-cream-400">
                                                <x-icon name="image" class="w-4 h-4" />
                                            </span>
                                        @endif
                                        <span class="font-medium">{{ $p?->nombre ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($p && $p->tipo)
                                        <x-badge>{{ $p->tipo->nombre }}</x-badge>
                                    @else
                                        <span class="text-cream-500">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-cream-700 dark:text-cream-300">{{ $p?->unidad_empaque ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($puedeEditar)
                                        <form action="{{ route('lista-mercado.plantilla.items.update', $item) }}" method="POST"
                                              class="inline-flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="cantidad_sugerida"
                                                   value="{{ $item->cantidad_sugerida }}"
                                                   min="1" step="1" required
                                                   class="w-20 rounded-lg border-cream-300 bg-white px-2 py-1 text-sm dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                                            <button type="submit" class="text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100">
                                                <x-icon name="check" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @else
                                        <span class="font-semibold">{{ $item->cantidad_sugerida }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($puedeEditar)
                                        <form action="{{ route('lista-mercado.plantilla.items.destroy', $item) }}" method="POST"
                                              class="inline" onsubmit="event.preventDefault(); return swalConfirm(this, {title: '¿Quitar este producto?', text: 'Se eliminará de la lista. Puedes agregarlo de nuevo después.', icon: 'warning', confirmButtonText: 'Sí, quitar', confirmButtonColor: '#e11d48'});">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium text-xs">
                                                <x-icon name="trash-2" class="w-3.5 h-3.5" />
                                                Quitar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-cream-400 text-xs">Bloqueado</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    @if ($puedeEditar && $productosDisponibles->isNotEmpty())
        <x-modal id="modal-agregar-item" title="Agregar producto a la lista" size="md">
            <form action="{{ route('lista-mercado.plantilla.items.store') }}" method="POST" class="space-y-4">
                @csrf

                <x-select
                    label="Producto"
                    name="producto_mercado_id"
                    :options="$productosDisponibles->mapWithKeys(fn ($p) => [$p->id => $p->nombre . ($p->tipo ? ' — ' . $p->tipo->nombre : '')])"
                    placeholder="Selecciona un producto"
                    required
                />

                <x-input
                    label="Cantidad sugerida"
                    name="cantidad_sugerida"
                    type="number"
                    :value="old('cantidad_sugerida', 1)"
                    min="1" step="1" required
                    hint="Cuántas unidades se compran normalmente"
                />

                <div class="flex items-center justify-end gap-2 pt-2">
                    <x-button type="button" variant="ghost" data-hs-overlay="#modal-agregar-item">
                        Cancelar
                    </x-button>
                    <x-button type="submit" variant="primary" icon="plus">
                        Agregar
                    </x-button>
                </div>
            </form>
        </x-modal>
    @endif
@endsection
