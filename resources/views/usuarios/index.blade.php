@extends('layouts.app')

@section('header', 'Usuarios')

@section('content')
    <x-page-header
        title="Usuarios"
        subtitle="Quién entra al sistema y con qué rol. El rol Ventas solo ve la caja."
        icon="user-cog"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="user-plus" :href="route('usuarios.create')">Nuevo usuario</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-table-enhanced :filters="[['col' => 2, 'label' => 'Rol']]" search-placeholder="Buscar usuario..." :per-page="25">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream-100 dark:bg-cream-900/40 text-cream-800 dark:text-cream-200">
                    <tr>
                        <x-th-sort :col="0" class="text-left px-4 py-3 font-semibold">Nombre</x-th-sort>
                        <x-th-sort :col="1" class="text-left px-4 py-3 font-semibold">Correo</x-th-sort>
                        <th class="text-left px-4 py-3 font-semibold">Rol</th>
                        <th class="text-left px-4 py-3 font-semibold">Creado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody data-enhance class="divide-y divide-cream-200 dark:divide-cream-800">
                    @foreach ($usuarios as $u)
                        @php $rol = $u->rolUsuario(); @endphp
                        <tr data-row class="hover:bg-cream-50 dark:hover:bg-cream-900/30">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar :src="$u->profile_photo_url ?: null" :name="$u->name" size="sm" />
                                    <span class="font-medium text-cream-900 dark:text-cream-50">{{ $u->name }}</span>
                                    @if ($u->is(auth()->user()))
                                        <x-badge variant="neutral" size="sm">Tú</x-badge>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-cream-700 dark:text-cream-300">{{ $u->email }}</td>
                            <td class="px-4 py-3">
                                @if ($rol)
                                    <x-badge :variant="$rol->badge()">{{ $rol->label() }}</x-badge>
                                @else
                                    <x-badge variant="neutral">Sin rol</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-cream-600 dark:text-cream-400">{{ $u->created_at?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('usuarios.edit', $u) }}" class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium text-xs">
                                        <x-icon name="edit" class="w-3.5 h-3.5" /> Editar
                                    </a>
                                    @unless ($u->is(auth()->user()))
                                        <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="inline"
                                              onsubmit="return confirm('¿Eliminar el usuario {{ addslashes($u->name) }}?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 text-rose-700 hover:text-rose-900 dark:text-rose-300 dark:hover:text-rose-100 font-medium text-xs">
                                                <x-icon name="trash-2" class="w-3.5 h-3.5" /> Eliminar
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-table-enhanced>
@endsection
