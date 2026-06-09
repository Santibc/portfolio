@extends('layouts.app')

@section('title', 'Tipos de pago')

@section('content')
    <x-manzer.page-header
        title="Tipos de pago"
        description="Modalidades y plazos de pago disponibles."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.index') }}">
                Volver
            </x-manzer.button>
            <x-manzer.button variant="primary" icon="plus-lg" href="{{ route('admin.tipos-pago.create') }}">
                Nuevo tipo
            </x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    {{-- Mensajes flash y errores de validación se renderizan globalmente vía <x-flash-messages /> en el layout. --}}

    <x-manzer.data-table :headers="['Nombre', 'Días crédito', 'Código Siigo', 'Activo', 'Acciones']">
        @forelse ($tipos as $tipo)
            <x-manzer.table-row>
                <x-manzer.table-cell class="font-medium">{{ $tipo->nombre }}</x-manzer.table-cell>
                <x-manzer.table-cell>{{ $tipo->dias_credito }}</x-manzer.table-cell>
                <x-manzer.table-cell class="font-mono text-xs">{{ $tipo->codigo_siigo ?: '—' }}</x-manzer.table-cell>
                <x-manzer.table-cell>
                    @if ($tipo->activo)
                        <x-manzer.badge variant="success" text="Activo" />
                    @else
                        <x-manzer.badge variant="secondary" text="Inactivo" />
                    @endif
                </x-manzer.table-cell>
                <x-manzer.table-cell>
                    <div class="flex items-center gap-2">
                        <x-manzer.button variant="outline" size="sm" icon="pencil" href="{{ route('admin.tipos-pago.edit', $tipo) }}">
                            Editar
                        </x-manzer.button>
                        <form action="{{ route('admin.tipos-pago.destroy', $tipo) }}" method="POST" onsubmit="event.preventDefault(); confirmarEliminar(this);">
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
                <x-manzer.table-cell class="text-center text-zinc-500 py-6" colspan="5">
                    No hay tipos de pago registrados todavía.
                </x-manzer.table-cell>
            </x-manzer.table-row>
        @endforelse
    </x-manzer.data-table>
@endsection

@push('scripts')
    <script>
        function confirmarEliminar(form) {
            window.Swal.fire({
                title: '¿Eliminar tipo de pago?',
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
