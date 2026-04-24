@extends('layouts.app')

@section('title', 'Monedas')

@section('content')
    <x-manzer.page-header
        title="Monedas"
        description="Administra las monedas disponibles para facturación."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.index') }}">
                Volver
            </x-manzer.button>
            <x-manzer.button variant="primary" icon="plus-lg" href="{{ route('admin.monedas.create') }}">
                Nueva moneda
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

    <x-manzer.data-table :headers="['Código', 'Nombre', 'Símbolo', 'Predeterminada', 'Activa', 'Acciones']">
        @forelse ($monedas as $moneda)
            <x-manzer.table-row>
                <x-manzer.table-cell class="font-mono font-semibold">{{ $moneda->codigo }}</x-manzer.table-cell>
                <x-manzer.table-cell>{{ $moneda->nombre }}</x-manzer.table-cell>
                <x-manzer.table-cell>{{ $moneda->simbolo }}</x-manzer.table-cell>
                <x-manzer.table-cell>
                    @if ($moneda->es_predeterminada)
                        <x-manzer.badge variant="success" text="Sí" />
                    @else
                        <x-manzer.badge variant="secondary" text="No" />
                    @endif
                </x-manzer.table-cell>
                <x-manzer.table-cell>
                    @if ($moneda->activa)
                        <x-manzer.badge variant="success" text="Activa" />
                    @else
                        <x-manzer.badge variant="secondary" text="Inactiva" />
                    @endif
                </x-manzer.table-cell>
                <x-manzer.table-cell>
                    <div class="flex items-center gap-2">
                        <x-manzer.button variant="outline" size="sm" icon="pencil" href="{{ route('admin.monedas.edit', $moneda) }}">
                            Editar
                        </x-manzer.button>
                        <form action="{{ route('admin.monedas.destroy', $moneda) }}" method="POST" onsubmit="event.preventDefault(); confirmarEliminar(this);">
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
                <x-manzer.table-cell class="text-center text-zinc-500 py-6" colspan="6">
                    No hay monedas registradas todavía.
                </x-manzer.table-cell>
            </x-manzer.table-row>
        @endforelse
    </x-manzer.data-table>
@endsection

@push('scripts')
    <script>
        function confirmarEliminar(form) {
            window.Swal.fire({
                title: '¿Eliminar moneda?',
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
