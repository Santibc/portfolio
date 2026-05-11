<x-guest-layout>
    <h2 class="text-xl font-bold text-cream-900 dark:text-cream-50 mb-1">Confirmar contrasena</h2>
    <p class="text-sm text-cream-600 dark:text-cream-400 mb-6">
        Esta es un area segura. Confirma tu contrasena para continuar.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf
        <x-input name="password" type="password" label="Contrasena" icon="lock" required autocomplete="current-password" />

        <x-button type="submit" variant="primary" class="w-full">
            Confirmar
        </x-button>
    </form>
</x-guest-layout>
