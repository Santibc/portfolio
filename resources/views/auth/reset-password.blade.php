<x-guest-layout>
    <h2 class="text-xl font-bold text-cream-900 dark:text-cream-50 mb-1">Restablecer contrasena</h2>
    <p class="text-sm text-cream-600 dark:text-cream-400 mb-6">Ingresa tu nueva contrasena.</p>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-input name="email" type="email" label="Correo" :value="old('email', $request->email)" icon="mail" required autofocus />
        <x-input name="password" type="password" label="Nueva contrasena" icon="lock" required autocomplete="new-password" />
        <x-input name="password_confirmation" type="password" label="Confirmar contrasena" icon="lock" required autocomplete="new-password" />

        <x-button type="submit" variant="primary" class="w-full">
            Restablecer contrasena
        </x-button>
    </form>
</x-guest-layout>
