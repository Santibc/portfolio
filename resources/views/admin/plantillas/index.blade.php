@extends('layouts.app')

@section('title', 'Plantillas de factura')

@section('content')
    <x-manzer.page-header
        title="Plantillas de factura"
        description="Diseña el HTML de la factura que reciben tus clientes. Una plantilla debe ser predeterminada."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.index') }}">
                Volver
            </x-manzer.button>
            <x-manzer.button variant="primary" icon="plus-lg" href="{{ route('admin.plantillas.create') }}">
                Nueva plantilla
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

    @if ($plantillas->isEmpty())
        <div class="card flex flex-col items-center justify-center gap-3 py-12 text-center">
            <i class="bi bi-file-earmark-code text-4xl text-zinc-400"></i>
            <h3 class="text-lg font-semibold">No hay plantillas creadas</h3>
            <p class="max-w-md text-sm text-zinc-500 dark:text-zinc-400">
                Crea tu primera plantilla HTML para personalizar el PDF de factura que envías a tus clientes.
            </p>
            <x-manzer.button variant="primary" icon="plus-lg" href="{{ route('admin.plantillas.create') }}">
                Crear plantilla
            </x-manzer.button>
        </div>
    @else
        <x-manzer.data-table :headers="['Nombre', 'Descripción', 'Predeterminada', 'Activa', 'Acciones']">
            @foreach ($plantillas as $plantilla)
                <x-manzer.table-row>
                    <x-manzer.table-cell class="font-semibold">
                        {{ $plantilla->nombre }}
                    </x-manzer.table-cell>
                    <x-manzer.table-cell class="whitespace-normal text-zinc-500 dark:text-zinc-400">
                        {{ $plantilla->descripcion ?: '—' }}
                    </x-manzer.table-cell>
                    <x-manzer.table-cell>
                        @if ($plantilla->es_default)
                            <x-manzer.badge variant="success" text="Default" />
                        @endif
                    </x-manzer.table-cell>
                    <x-manzer.table-cell>
                        @if ($plantilla->activo)
                            <x-manzer.badge variant="success" text="Activa" />
                        @else
                            <x-manzer.badge variant="danger" text="Inactiva" />
                        @endif
                    </x-manzer.table-cell>
                    <x-manzer.table-cell>
                        <div class="flex items-center gap-2">
                            <x-manzer.button variant="outline" size="sm" icon="pencil" href="{{ route('admin.plantillas.edit', $plantilla) }}">
                                Editar
                            </x-manzer.button>
                            @if (! $plantilla->es_default)
                                <form action="{{ route('admin.plantillas.destroy', $plantilla) }}" method="POST" onsubmit="event.preventDefault(); confirmarEliminar(this);">
                                    @csrf
                                    @method('DELETE')
                                    <x-manzer.button type="submit" variant="danger" size="sm" icon="trash">
                                        Eliminar
                                    </x-manzer.button>
                                </form>
                            @endif
                        </div>
                    </x-manzer.table-cell>
                </x-manzer.table-row>
            @endforeach
        </x-manzer.data-table>
    @endif
@endsection

@push('scripts')
    <script>
        function confirmarEliminar(form) {
            window.Swal.fire({
                title: '¿Eliminar plantilla?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endpush
