@extends('layouts.app')

@section('title', 'Tallas')

@section('content')
    <x-manzer.page-header
        title="Tallas"
        description="Catálogo de tallas disponibles para desglosar las líneas de factura."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.index') }}">
                Volver
            </x-manzer.button>
            <x-manzer.button variant="primary" icon="plus-lg" href="{{ route('admin.tallas.create') }}">
                Nueva talla
            </x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    {{-- Mensajes flash y errores de validación se renderizan globalmente vía <x-flash-messages /> en el layout. --}}

    <x-manzer.data-table :headers="['Orden', 'Nombre', 'Activo', 'Acciones']">
        @forelse ($tallas as $talla)
            <x-manzer.table-row>
                <x-manzer.table-cell>{{ $talla->orden }}</x-manzer.table-cell>
                <x-manzer.table-cell class="font-medium">{{ $talla->nombre }}</x-manzer.table-cell>
                <x-manzer.table-cell>
                    @if ($talla->activo)
                        <x-manzer.badge variant="success" text="Activa" />
                    @else
                        <x-manzer.badge variant="secondary" text="Inactiva" />
                    @endif
                </x-manzer.table-cell>
                <x-manzer.table-cell>
                    <div class="flex items-center gap-2">
                        <x-manzer.button variant="outline" size="sm" icon="pencil" href="{{ route('admin.tallas.edit', $talla) }}">
                            Editar
                        </x-manzer.button>
                        <form action="{{ route('admin.tallas.destroy', $talla) }}" method="POST" onsubmit="event.preventDefault(); confirmarEliminar(this);">
                            @csrf
                            @method('DELETE')
                            <x-manzer.button type="submit" variant="danger" size="sm" icon="trash">
                                Eliminar
                            </x-manzer.button>
                        </form>
                    </div>
                </x-manzer.table-cell>
            </x-manzer.table-row>
        @empty
            <x-manzer.table-row>
                <x-manzer.table-cell class="text-center text-zinc-500 py-6" colspan="4">
                    No hay tallas registradas todavía.
                </x-manzer.table-cell>
            </x-manzer.table-row>
        @endforelse
    </x-manzer.data-table>
@endsection

@push('scripts')
    <script>
        function confirmarEliminar(form) {
            window.Swal.fire({
                title: '¿Eliminar talla?',
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
