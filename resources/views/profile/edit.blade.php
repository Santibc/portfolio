@extends('layouts.app')
@section('title', 'Mi perfil')

@section('content')
<x-page-header
    title="Mi perfil"
    subtitle="Gestiona tu informacion personal y preferencias"
    icon="user-cog"
/>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Foto de perfil --}}
    <div class="lg:col-span-1" data-reveal>
        <x-card padding="p-6">
            <x-slot:header>
                <div class="flex items-center gap-2 text-cream-900 dark:text-cream-50">
                    <x-icon name="camera" class="w-4 h-4" />
                    <h3 class="font-semibold">Foto de perfil</h3>
                </div>
            </x-slot:header>

            <div class="flex flex-col items-center text-center">
                <x-avatar
                    :src="$user->hasProfilePhoto() ? $user->profile_photo_url : null"
                    :name="$user->name"
                    size="xl"
                    ring
                    class="mb-4"
                />

                <h4 class="font-semibold text-cream-900 dark:text-cream-50">{{ $user->name }}</h4>
                <p class="text-sm text-cream-600 dark:text-cream-400">{{ $user->email }}</p>
                <x-badge variant="primary" class="mt-2">{{ $user->roles->first()->name ?? 'Usuario' }}</x-badge>

                <div class="divider my-5"></div>

                <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="w-full space-y-3">
                    @csrf
                    <x-input
                        type="file"
                        name="profile_photo"
                        label="Cambiar foto"
                        accept="image/*"
                        class="!py-1.5 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-primary-100 file:text-primary-800 dark:file:bg-primary-900/40 dark:file:text-primary-200"
                    />
                    <x-button type="submit" variant="primary" icon="upload" class="w-full">
                        Subir foto
                    </x-button>
                </form>

                @if ($user->hasProfilePhoto())
                    <form action="{{ route('profile.photo.destroy') }}" method="POST" class="w-full mt-2">
                        @csrf @method('DELETE')
                        <x-button type="submit" variant="ghost" icon="trash-2" class="w-full text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20"
                            onclick="return confirm('Eliminar foto de perfil?')">
                            Eliminar foto
                        </x-button>
                    </form>
                @endif
            </div>
        </x-card>
    </div>

    {{-- Datos + contrasena --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Datos personales --}}
        <div data-reveal>
            <x-card padding="p-6">
                <x-slot:header>
                    <div class="flex items-center gap-2 text-cream-900 dark:text-cream-50">
                        <x-icon name="user" class="w-4 h-4" />
                        <h3 class="font-semibold">Informacion del perfil</h3>
                    </div>
                </x-slot:header>

                <p class="text-sm text-cream-600 dark:text-cream-400 mb-5">
                    Actualiza tu nombre y correo.
                </p>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf @method('patch')

                    <x-input name="name" label="Nombre" :value="old('name', $user->name)" icon="user" required />
                    <x-input name="email" type="email" label="Correo electronico" :value="old('email', $user->email)" icon="mail" required />

                    <div class="pt-2">
                        <x-button type="submit" variant="primary" icon="save">
                            Guardar cambios
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Cambiar contrasena --}}
        <div data-reveal>
            <x-card padding="p-6">
                <x-slot:header>
                    <div class="flex items-center gap-2 text-cream-900 dark:text-cream-50">
                        <x-icon name="lock" class="w-4 h-4" />
                        <h3 class="font-semibold">Actualizar contrasena</h3>
                    </div>
                </x-slot:header>

                <p class="text-sm text-cream-600 dark:text-cream-400 mb-5">
                    Asegurate de usar una contrasena larga y unica.
                </p>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4" x-data="{ show1: false, show2: false, show3: false }">
                    @csrf @method('put')

                    <div>
                        <label class="block text-sm font-medium text-cream-800 dark:text-cream-200 mb-1.5">Contrasena actual</label>
                        <div class="relative">
                            <input :type="show1 ? 'text' : 'password'" name="current_password" autocomplete="current-password" class="block w-full rounded-xl border-cream-300 bg-white px-3 py-2.5 pr-10 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                            <button type="button" @click="show1 = !show1" class="absolute inset-y-0 right-0 flex items-center pr-3 text-cream-500 hover:text-cream-700">
                                <x-icon name="eye" class="w-4 h-4" x-show="!show1" />
                                <x-icon name="eye-off" class="w-4 h-4" x-show="show1" x-cloak />
                            </button>
                        </div>
                        @error('current_password', 'updatePassword')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-cream-800 dark:text-cream-200 mb-1.5">Nueva contrasena</label>
                        <div class="relative">
                            <input :type="show2 ? 'text' : 'password'" name="password" autocomplete="new-password" class="block w-full rounded-xl border-cream-300 bg-white px-3 py-2.5 pr-10 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                            <button type="button" @click="show2 = !show2" class="absolute inset-y-0 right-0 flex items-center pr-3 text-cream-500 hover:text-cream-700">
                                <x-icon name="eye" class="w-4 h-4" x-show="!show2" />
                                <x-icon name="eye-off" class="w-4 h-4" x-show="show2" x-cloak />
                            </button>
                        </div>
                        @error('password', 'updatePassword')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-cream-800 dark:text-cream-200 mb-1.5">Confirmar contrasena</label>
                        <div class="relative">
                            <input :type="show3 ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password" class="block w-full rounded-xl border-cream-300 bg-white px-3 py-2.5 pr-10 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                            <button type="button" @click="show3 = !show3" class="absolute inset-y-0 right-0 flex items-center pr-3 text-cream-500 hover:text-cream-700">
                                <x-icon name="eye" class="w-4 h-4" x-show="!show3" />
                                <x-icon name="eye-off" class="w-4 h-4" x-show="show3" x-cloak />
                            </button>
                        </div>
                    </div>

                    <div class="pt-2">
                        <x-button type="submit" variant="primary" icon="key">
                            Cambiar contrasena
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

    </div>
</div>
@endsection
