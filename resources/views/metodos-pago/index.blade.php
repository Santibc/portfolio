@extends('layouts.app')

@section('header', 'Métodos de pago')

@section('content')
<div x-data="{
    nuevoOpen: false,
    editarOpen: false,
    edit: { id: null, codigo: '', nombre: '', orden: 0, es_efectivo: false, activo: false },
}">
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
                                            @click="edit = { id: {{ $m->id }}, codigo: @js($m->codigo), nombre: @js($m->nombre), orden: {{ (int) $m->orden }}, es_efectivo: {{ $m->es_efectivo ? 'true' : 'false' }}, activo: {{ $m->activo ? 'true' : 'false' }} }; editarOpen = true">
                                        <x-icon name="edit" class="w-3.5 h-3.5" /> Editar
                                    </button>
                                    <form action="{{ route('metodos-pago.destroy', $m) }}" method="POST"
                                          onsubmit="return confirm('¿Deshabilitar este método de pago? No aparecerá en la caja, pero las ventas ya registradas lo conservan.');">
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
                <form :action="'{{ url('metodos-pago') }}/' + edit.id" method="POST">
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
                        <x-input label="Código" name="codigo" required x-model="edit.codigo" />
                        <x-input label="Nombre" name="nombre" required x-model="edit.nombre" />
                        <x-input label="Orden" name="orden" type="number" x-model.number="edit.orden" />

                        <input type="hidden" name="es_efectivo" value="0">
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <span class="relative inline-flex items-center">
                                <input type="checkbox" name="es_efectivo" value="1" x-model="edit.es_efectivo" class="sr-only peer">
                                <span class="w-11 h-6 rounded-full bg-cream-300 peer-checked:bg-primary-500 transition-colors duration-200 dark:bg-cream-700"></span>
                                <span class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform duration-200 peer-checked:translate-x-5"></span>
                            </span>
                            <span class="text-sm">
                                <span class="block font-medium text-cream-800 dark:text-cream-200">Es efectivo</span>
                                <span class="block text-xs text-cream-600 dark:text-cream-400">Marca solo si este método representa pago en dinero físico</span>
                            </span>
                        </label>

                        <input type="hidden" name="activo" value="0">
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <span class="relative inline-flex items-center">
                                <input type="checkbox" name="activo" value="1" x-model="edit.activo" class="sr-only peer">
                                <span class="w-11 h-6 rounded-full bg-cream-300 peer-checked:bg-primary-500 transition-colors duration-200 dark:bg-cream-700"></span>
                                <span class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform duration-200 peer-checked:translate-x-5"></span>
                            </span>
                            <span class="text-sm">
                                <span class="block font-medium text-cream-800 dark:text-cream-200">Activo</span>
                            </span>
                        </label>
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

