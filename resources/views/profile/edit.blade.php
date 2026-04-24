@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
<div x-data="{ tab: 'info' }" class="space-y-6">
    <x-manzer.page-header
        title="Mi perfil"
        description="Gestiona tu información personal y preferencias."
    />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Sidebar perfil --}}
        <div class="space-y-4 lg:col-span-1">
            <div class="card text-center">
                @if ($user->hasProfilePhoto())
                    <img src="{{ $user->profile_photo_url }}" alt="" class="mx-auto h-28 w-28 rounded-full object-cover ring-4 ring-white dark:ring-zinc-800">
                @else
                    <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-primary-400 to-primary-600 text-3xl font-bold text-white ring-4 ring-white dark:ring-zinc-800">
                        {{ $user->initials }}
                    </div>
                @endif
                <h2 class="mt-4 text-lg font-semibold">{{ $user->name }}</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                <x-manzer.badge variant="primary" class="mt-2">
                    {{ $user->roles->first()->name ?? 'Usuario' }}
                </x-manzer.badge>
            </div>

            {{-- Tabs nav --}}
            <nav class="card space-y-1 p-2">
                <button type="button" @click="tab = 'info'" :class="tab === 'info' ? 'nav-item-active' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium transition">
                    <i class="bi bi-person"></i> Información
                </button>
                <button type="button" @click="tab = 'photo'" :class="tab === 'photo' ? 'nav-item-active' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium transition">
                    <i class="bi bi-camera"></i> Foto de perfil
                </button>
                <button type="button" @click="tab = 'password'" :class="tab === 'password' ? 'nav-item-active' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium transition">
                    <i class="bi bi-lock"></i> Contraseña
                </button>
                <button type="button" @click="tab = 'danger'" :class="tab === 'danger' ? 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-400' : 'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950'" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium transition">
                    <i class="bi bi-exclamation-triangle"></i> Zona de peligro
                </button>
            </nav>
        </div>

        {{-- Content --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Info --}}
            <div x-show="tab === 'info'" class="card">
                <h3 class="mb-1 text-lg font-semibold tracking-tight">Información del perfil</h3>
                <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">Actualiza tu nombre y correo electrónico.</p>

                <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-medium">Nombre</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus class="input">
                        @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="input">
                        @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2 rounded-lg bg-amber-50 p-3 text-xs dark:bg-amber-950">
                                <p class="text-amber-800 dark:text-amber-300">
                                    Tu correo no ha sido verificado.
                                    <button form="send-verification" class="font-medium underline hover:no-underline">
                                        Reenviar correo de verificación
                                    </button>
                                </p>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-1 text-green-700 dark:text-green-400">Se envió un nuevo enlace de verificación.</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar cambios
                        </button>
                        @if (session('status') === 'profile-updated')
                            <span x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 dark:text-green-400">
                                <i class="bi bi-check-circle"></i> Guardado
                            </span>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Foto --}}
            <div x-show="tab === 'photo'" class="card" style="display: none;">
                <h3 class="mb-1 text-lg font-semibold tracking-tight">Foto de perfil</h3>
                <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">Sube una imagen de perfil personalizada.</p>

                <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="profile_photo" class="mb-1.5 block text-sm font-medium">Seleccionar imagen</label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="block w-full text-sm text-zinc-700 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200 dark:text-zinc-300 dark:file:bg-zinc-800 dark:file:text-zinc-200">
                        @error('profile_photo')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-upload"></i> Subir foto
                    </button>
                </form>

                @if ($user->hasProfilePhoto())
                    <form action="{{ route('profile.photo.destroy') }}" method="POST" class="mt-3" onsubmit="return confirm('¿Eliminar foto de perfil?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn bg-white text-red-600 ring-1 ring-inset ring-red-200 hover:bg-red-50 dark:bg-transparent dark:ring-red-900 dark:hover:bg-red-950">
                            <i class="bi bi-trash"></i> Eliminar foto
                        </button>
                    </form>
                @endif
            </div>

            {{-- Password --}}
            <div x-show="tab === 'password'" class="card" style="display: none;">
                <h3 class="mb-1 text-lg font-semibold tracking-tight">Actualizar contraseña</h3>
                <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">Usa una contraseña larga y única para mantener tu cuenta segura.</p>

                <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf @method('put')

                    <div>
                        <label for="current_password" class="mb-1.5 block text-sm font-medium">Contraseña actual</label>
                        <input type="password" id="current_password" name="current_password" autocomplete="current-password" class="input">
                        @error('current_password', 'updatePassword')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium">Nueva contraseña</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" class="input">
                        @error('password', 'updatePassword')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Confirmar contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="input">
                        @error('password_confirmation', 'updatePassword')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="btn-primary">
                            <i class="bi bi-key"></i> Cambiar contraseña
                        </button>
                        @if (session('status') === 'password-updated')
                            <span x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 dark:text-green-400">
                                <i class="bi bi-check-circle"></i> Actualizada
                            </span>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Danger --}}
            <div x-show="tab === 'danger'" x-data="{ confirmOpen: false }" class="card border border-red-200 dark:border-red-900" style="display: none;">
                <h3 class="mb-1 text-lg font-semibold tracking-tight text-red-700 dark:text-red-400">Eliminar cuenta</h3>
                <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">Una vez eliminada tu cuenta, todos los datos se perderán de forma permanente.</p>

                <button type="button" @click="confirmOpen = true" class="btn-danger">
                    <i class="bi bi-trash"></i> Eliminar mi cuenta
                </button>

                {{-- Modal de confirmación --}}
                <div x-show="confirmOpen" x-cloak x-transition.opacity @click.self="confirmOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/60 backdrop-blur-sm p-4" style="display: none;">
                    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-800">
                        <h3 class="text-lg font-semibold tracking-tight">¿Eliminar tu cuenta?</h3>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Esta acción es irreversible. Ingresa tu contraseña para confirmar.</p>

                        <form method="post" action="{{ route('profile.destroy') }}" class="mt-4 space-y-4">
                            @csrf @method('delete')
                            <div>
                                <label for="delete_password" class="mb-1.5 block text-sm font-medium">Contraseña</label>
                                <input type="password" id="delete_password" name="password" autofocus placeholder="Tu contraseña" class="input">
                                @error('password', 'userDeletion')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="confirmOpen = false" class="btn-secondary">Cancelar</button>
                                <button type="submit" class="btn-danger">Eliminar cuenta</button>
                            </div>
                        </form>
                    </div>
                </div>

                @if ($errors->userDeletion->isNotEmpty())
                    <script>document.addEventListener('DOMContentLoaded', () => { window.dispatchEvent(new CustomEvent('open-delete')); });</script>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
