@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
    <div class="container-fluid py-4">
        <x-manzer.page-header
            title="Clientes"
            description="Nacionales e internacionales, para facturación DIAN y documentos comerciales."
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
                    href="{{ route('catalogos.clientes.create') }}"
                >
                    Nuevo cliente
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
            <form action="{{ route('catalogos.clientes.index') }}" method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
                <div class="md:col-span-7">
                    <label for="q" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <i class="bi bi-search mr-1"></i>Buscar
                    </label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        value="{{ $buscar ?? '' }}"
                        placeholder="Nombre, identificación o email…"
                        class="input"
                    >
                </div>
                <div class="md:col-span-3">
                    <label for="tipo" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <i class="bi bi-globe mr-1"></i>Tipo
                    </label>
                    <select id="tipo" name="tipo" class="input">
                        @foreach (['' => 'Todos', 'nacional' => 'Nacionales', 'internacional' => 'Internacionales'] as $v => $l)
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

        @if ($clientes->isEmpty())
            <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-12 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-950/40 dark:text-primary-400">
                    <i class="bi bi-people text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Aún no hay clientes</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Empieza registrando tu primer cliente nacional o internacional.</p>
                <div class="mt-4">
                    <x-manzer.button variant="primary" icon="plus-lg" href="{{ route('catalogos.clientes.create') }}">
                        Crear primer cliente
                    </x-manzer.button>
                </div>
            </div>
        @else
            <x-manzer.data-table :headers="['Tipo', 'Identificación', 'Nombre', 'País', 'Moneda', 'Email', 'Teléfono', 'Activo', 'Acciones']">
                @foreach ($clientes as $cliente)
                    <x-manzer.table-row>
                        <x-manzer.table-cell>
                            @if ($cliente->tipo === 'internacional')
                                <x-manzer.badge variant="info" text="Internacional" />
                            @else
                                <x-manzer.badge variant="secondary" text="Nacional" />
                            @endif
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            @if (!empty($cliente->identificacion))
                                <span class="font-mono">
                                    @if (!empty($cliente->tipo_identificacion))
                                        <span class="text-zinc-500 dark:text-zinc-400">{{ $cliente->tipo_identificacion }}</span>
                                    @endif
                                    {{ $cliente->identificacion }}
                                </span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            <div class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $cliente->nombre }}</div>
                            @if (!empty($cliente->nombre_comercial))
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $cliente->nombre_comercial }}</div>
                            @endif
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            @if (!empty($cliente->pais))
                                <div>{{ $cliente->pais }}</div>
                                @if (!empty($cliente->ciudad))
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $cliente->ciudad }}</div>
                                @endif
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            {{ $cliente->monedaPreferida?->codigo ?? '—' }}
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            {{ $cliente->email ?? '—' }}
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            {{ $cliente->telefono ?? '—' }}
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            @if ($cliente->activo)
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
                                    href="{{ route('catalogos.clientes.edit', $cliente) }}"
                                    aria-label="Editar"
                                >
                                    Editar
                                </x-manzer.button>

                                <form
                                    action="{{ route('catalogos.clientes.destroy', $cliente) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="event.preventDefault(); const f=this; window.Swal.fire({title:'¿Eliminar cliente?',text:'Esta acción no se puede deshacer.',icon:'warning',showCancelButton:true,confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)f.submit();});"
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
                {{ $clientes->appends(['q' => $buscar ?? '', 'tipo' => $tipo ?? ''])->links() }}
            </div>
        @endif
    </div>
@endsection
