@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div class="container-fluid py-4">
        <x-manzer.page-header
            title="Productos"
            description="Catálogo de prendas y accesorios para facturación."
        >
            <x-slot name="actions">
                <x-manzer.button
                    variant="secondary"
                    icon="arrow-left"
                    href="{{ route('dashboard') }}"
                >
                    Volver
                </x-manzer.button>
                <x-manzer.button
                    variant="primary"
                    icon="plus-lg"
                    href="{{ route('catalogos.productos.create') }}"
                >
                    Nuevo producto
                </x-manzer.button>
            </x-slot>
        </x-manzer.page-header>

        @if (session('success'))
            <div class="mb-4">
                <x-manzer.alert type="success" :message="session('success')" dismissible />
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4">
                <x-manzer.alert type="error" :message="session('error')" dismissible />
            </div>
        @endif

        <div class="card mb-4 p-4">
            <form action="{{ route('catalogos.productos.index') }}" method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
                <div class="md:col-span-7">
                    <label for="q" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <i class="bi bi-search mr-1"></i>Buscar
                    </label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        value="{{ $buscar ?? '' }}"
                        placeholder="Referencia, descripción o color…"
                        class="input"
                    >
                </div>
                <div class="md:col-span-3">
                    <label for="tipo" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <i class="bi bi-tag mr-1"></i>Tipo
                    </label>
                    <select id="tipo" name="tipo" class="input">
                        @foreach (['' => 'Todos', 'prenda' => 'Prendas', 'accesorio' => 'Accesorios'] as $v => $l)
                            <option value="{{ $v }}" @selected(($tipo ?? '') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <x-manzer.button type="submit" variant="primary" icon="funnel" class="w-full">
                        Filtrar
                    </x-manzer.button>
                </div>
            </form>
        </div>

        @if ($productos->isEmpty())
            <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-12 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-950/40 dark:text-primary-400">
                    <i class="bi bi-box-seam text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Aún no hay productos</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Empieza creando tu primer producto del catálogo.</p>
                <div class="mt-4">
                    <x-manzer.button variant="primary" icon="plus-lg" href="{{ route('catalogos.productos.create') }}">
                        Crear primer producto
                    </x-manzer.button>
                </div>
            </div>
        @else
            <x-manzer.data-table :headers="['Imagen', 'Referencia', 'Descripción', 'Color', 'Precio', 'Moneda', 'IVA', 'Tipo', 'Activo', 'Acciones']">
                @foreach ($productos as $producto)
                    <x-manzer.table-row>
                        <x-manzer.table-cell>
                            @if ($producto->imagen_url)
                                <img
                                    src="{{ $producto->imagen_url }}"
                                    alt="{{ $producto->referencia }}"
                                    class="h-10 w-10 rounded object-cover"
                                >
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded bg-zinc-100 dark:bg-zinc-800">
                                    <i class="bi bi-image text-zinc-400 dark:text-zinc-500"></i>
                                </div>
                            @endif
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            <span class="font-mono font-semibold">{{ $producto->referencia }}</span>
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            <div class="font-medium text-zinc-800 dark:text-zinc-200">{{ $producto->descripcion }}</div>
                            @if (!empty($producto->composicion))
                                <div class="text-xs text-zinc-500">{{ \Illuminate\Support\Str::limit($producto->composicion, 60) }}</div>
                            @endif
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            @if (!empty($producto->color))
                                <x-manzer.badge variant="secondary" :text="$producto->color" />
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </x-manzer.table-cell>

                        <x-manzer.table-cell class="font-semibold">
                            {{ number_format((float) $producto->precio_unitario, 2, ',', '.') }}
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            {{ $producto->moneda?->codigo ?? '—' }}
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            {{ $producto->impuesto?->nombre ?? '—' }}
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            @if ($producto->es_prenda)
                                <x-manzer.badge variant="info" text="Prenda" />
                            @else
                                <x-manzer.badge variant="secondary" text="Accesorio" />
                            @endif
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            @if ($producto->activo)
                                <x-manzer.badge variant="success" text="Activo" />
                            @else
                                <x-manzer.badge variant="danger" text="Inactivo" />
                            @endif
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            <div class="flex items-center gap-2">
                                <x-manzer.button
                                    variant="outline"
                                    size="sm"
                                    icon="pencil"
                                    href="{{ route('catalogos.productos.edit', $producto) }}"
                                    aria-label="Editar"
                                >
                                    Editar
                                </x-manzer.button>

                                <form
                                    action="{{ route('catalogos.productos.destroy', $producto) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="event.preventDefault(); const f=this; window.Swal.fire({title:'¿Eliminar producto?',text:'Esta acción no se puede deshacer.',icon:'warning',showCancelButton:true,confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)f.submit();});"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <x-manzer.button
                                        type="submit"
                                        variant="danger"
                                        size="sm"
                                        icon="trash"
                                        aria-label="Eliminar"
                                    >
                                        Eliminar
                                    </x-manzer.button>
                                </form>
                            </div>
                        </x-manzer.table-cell>
                    </x-manzer.table-row>
                @endforeach
            </x-manzer.data-table>

            <div class="mt-4">
                {{ $productos->appends(['q' => $buscar ?? '', 'tipo' => $tipo ?? ''])->links() }}
            </div>
        @endif
    </div>
@endsection
