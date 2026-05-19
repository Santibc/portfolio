@extends('layouts.app')

@section('header', 'Métodos de pago')

@section('content')
<div x-data="{ nuevoOpen: false, editarOpen: false }">
    <x-page-header
        title="Métodos de pago"
        subtitle="Administra los métodos disponibles en la caja"
        icon="credit-card"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" @click="nuevoOpen = true">
                Nuevo método
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-100 dark:bg-cream-900/40 text-cream-800 dark:text-cream-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold">Código</th>
                        <th class="text-left px-4 py-3 font-semibold">Nombre</th>
                        <th class="text-left px-4 py-3 font-semibold">Es efectivo</th>
                        <th class="text-left px-4 py-3 font-semibold">Orden</th>
                        <th class="text-left px-4 py-3 font-semibold">Estado</th>
                        <th class="text-right px-4 py-3 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200 dark:divide-cream-800">
                    @forelse ($metodos as $m)
                        <tr class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-3 font-mono text-xs text-cream-700 dark:text-cream-300">{{ $m->codigo }}</td>
                            <td class="px-4 py-3 font-medium text-cream-900 dark:text-cream-50">{{ $m->nombre }}</td>
                            <td class="px-4 py-3">
                                @if ($m->es_efectivo)
                                    <x-badge variant="success" icon="banknote">Efectivo</x-badge>
                                @else
                                    <x-badge variant="sky" icon="credit-card">Transferencia</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-cream-700 dark:text-cream-300">{{ $m->orden }}</td>
                            <td class="px-4 py-3">
                                @if ($m->activo)
                                    <x-badge variant="success">Activo</x-badge>
                                @else
                                    <x-badge variant="neutral">Inactivo</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button"
                                            class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium text-xs"
                                            @click="abrirEditarMetodo({{ $m->id }}, '{{ e($m->codigo) }}', '{{ e($m->nombre) }}', {{ $m->es_efectivo ? 'true' : 'false' }}, {{ $m->activo ? 'true' : 'false' }}, {{ (int) $m->orden }}); editarOpen = true">
                                        <x-icon name="edit" class="w-3.5 h-3.5" /> Editar
                                    </button>
                                    <form action="{{ route('metodos-pago.destroy', $m) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar este método de pago?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium text-xs">
                                            <x-icon name="trash-2" class="w-3.5 h-3.5" /> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-cream-600 dark:text-cream-400">
                                Aún no hay métodos de pago.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Modal nuevo --}}
    <div x-show="nuevoOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-cream-950/60 backdrop-blur-sm"
         @keydown.escape.window="nuevoOpen = false"
         @click.self="nuevoOpen = false">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-md bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg">
                <form action="{{ route('metodos-pago.store') }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-between px-5 py-4 border-b border-cream-200 dark:border-cream-800">
                        <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50">Nuevo método de pago</h3>
                        <button type="button" class="text-cream-500 hover:text-cream-800 dark:hover:text-cream-200"
                                @click="nuevoOpen = false">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-5 space-y-4">
                        <x-input label="Código" name="codigo" required placeholder="ej. tarjeta_credito" hint="Identificador estable. Solo minúsculas, números y guiones bajos." />
                        <x-input label="Nombre" name="nombre" required placeholder="ej. Tarjeta de crédito" />
                        <x-input label="Orden" name="orden" type="number" value="0" />
                        <input type="hidden" name="es_efectivo" value="0">
                        <x-toggle name="es_efectivo" label="Es efectivo" description="Marca solo si este método representa pago en dinero físico" />
                        <input type="hidden" name="activo" value="0">
                        <x-toggle name="activo" label="Activo" :checked="true" />
                    </div>

                    <div class="px-5 py-4 border-t border-cream-200 dark:border-cream-800 flex items-center justify-end gap-2">
                        <x-button type="button" variant="ghost" @click="nuevoOpen = false">
                            Cancelar
                        </x-button>
                        <x-button type="submit" variant="primary" icon="save">Crear</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal editar --}}
    <div x-show="editarOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-cream-950/60 backdrop-blur-sm"
         @keydown.escape.window="editarOpen = false"
         @click.self="editarOpen = false">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-md bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg">
                <form id="form-metodo-editar" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center justify-between px-5 py-4 border-b border-cream-200 dark:border-cream-800">
                        <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50">Editar método de pago</h3>
                        <button type="button" class="text-cream-500 hover:text-cream-800 dark:hover:text-cream-200"
                                @click="editarOpen = false">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-5 space-y-4">
                        <x-input label="Código" name="codigo" id="edit-codigo" required />
                        <x-input label="Nombre" name="nombre" id="edit-nombre" required />
                        <x-input label="Orden" name="orden" id="edit-orden" type="number" />
                        <input type="hidden" name="es_efectivo" value="0">
                        <x-toggle name="es_efectivo" id="edit-es-efectivo" label="Es efectivo" />
                        <input type="hidden" name="activo" value="0">
                        <x-toggle name="activo" id="edit-activo" label="Activo" />
                    </div>

                    <div class="px-5 py-4 border-t border-cream-200 dark:border-cream-800 flex items-center justify-end gap-2">
                        <x-button type="button" variant="ghost" @click="editarOpen = false">
                            Cancelar
                        </x-button>
                        <x-button type="submit" variant="primary" icon="save">Actualizar</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function abrirEditarMetodo(id, codigo, nombre, esEfectivo, activo, orden) {
        const form = document.getElementById('form-metodo-editar');
        form.action = '{{ url('metodos-pago') }}/' + id;
        document.getElementById('edit-codigo').value = codigo;
        document.getElementById('edit-nombre').value = nombre;
        document.getElementById('edit-orden').value = orden;
        document.getElementById('edit-es-efectivo').checked = esEfectivo;
        document.getElementById('edit-activo').checked = activo;
        document.getElementById('modal-metodo-editar').classList.remove('hidden');
    }
</script>
@endpush
