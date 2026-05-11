<x-guest-layout>
    <h2 class="text-xl font-bold text-cream-900 dark:text-cream-50 mb-1">Recuperar contrasena</h2>
    <p class="text-sm text-cream-600 dark:text-cream-400 mb-6">
        Te enviaremos un enlace para restablecer tu contrasena.
    </p>

    @if (session('status'))
        <x-alert variant="success" class="mb-4">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <x-input name="email" type="email" label="Correo" :value="old('email')" icon="mail" required autofocus />

        <x-button type="submit" variant="primary" iconRight="send" class="w-full">
            Enviar enlace
        </x-button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-xs text-cream-600 hover:text-primary-700 dark:text-cream-400 dark:hover:text-primary-300">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" /> Volver al login
            </a>
        </div>
    </form>
</x-guest-layout>
