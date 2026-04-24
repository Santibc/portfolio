@extends('layouts.app')

@section('title', 'Impuestos')

@section('content')
    <x-manzer.page-header
        title="Impuestos"
        description="Administra los impuestos y retenciones aplicables."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.index') }}">
                Volver
            </x-manzer.button>
            <x-manzer.button variant="primary" icon="plus-lg" href="{{ route('admin.impuestos.create') }}">
                Nuevo impuesto
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

    <x-manzer.data-table :headers="['Nombre', 'Porcentaje', 'Tipo', 'Código Siigo', 'Activo', 'Acciones']">
        @forelse ($impuestos as $impuesto)
            <x-manzer.table-row>
                <x-manzer.table-cell class="font-medium">{{ $impuesto->nombre }}</x-manzer.table-cell>
                <x-manzer.table-cell>{{ number_format((float) $impuesto->porcentaje, 2) }}%</x-manzer.table-cell>
                <x-manzer.table-cell>
                    @php
                        $tipoVariant = match ($impuesto->tipo) {
                            'iva' => 'primary',
                            'retencion' => 'warning',
                            default => 'secondary',
                        };
                        $tipoTexto = match ($impuesto->tipo) {
                            'iva' => 'IVA',
                            'retencion' => 'Retención',
                            default => 'Otro',
                        };
                    @endphp
                    <x-manzer.badge :variant="$tipoVariant" :text="$tipoTexto" />
                </x-manzer.table-cell>
                <x-manzer.table-cell class="font-mono text-xs">{{ $impuesto->codigo_siigo ?: '—' }}</x-manzer.table-cell>
                <x-manzer.table-cell>
                    @if ($impuesto->activo)
                        <x-manzer.badge variant="success" text="Activo" />
                    @else
                        <x-manzer.badge variant="secondary" text="Inactivo" />
                    @endif
                </x-manzer.table-cell>
                <x-manzer.table-cell>
                    <div class="flex items-center gap-2">
                        <x-manzer.button variant="outline" size="sm" icon="pencil" href="{{ route('admin.impuestos.edit', $impuesto) }}">
                            Editar
                        </x-manzer.button>
                        <form action="{{ route('admin.impuestos.destroy', $impuesto) }}" method="POST" onsubmit="event.preventDefault(); confirmarEliminar(this);">
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
                    No hay impuestos registrados todavía.
                </x-manzer.table-cell>
            </x-manzer.table-row>
        @endforelse
    </x-manzer.data-table>
@endsection

@push('scripts')
    <script>
        function confirmarEliminar(form) {
            window.Swal.fire({
                title: '¿Eliminar impuesto?',
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
