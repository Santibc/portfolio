<x-guest-layout>
    <h2 class="text-xl font-bold text-cream-900 dark:text-cream-50 mb-1">Verifica tu correo</h2>
    <p class="text-sm text-cream-600 dark:text-cream-400 mb-6">
        Te enviamos un enlace de verificacion a tu correo. Si no llego, podemos enviarlo de nuevo.
    </p>

    @if (session('status') == 'verification-link-sent')
        <x-alert variant="success" class="mb-4">
            Hemos enviado un nuevo enlace a tu correo.
        </x-alert>
    @endif

    <div class="flex items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button type="submit" variant="primary" icon="send">
                Reenviar enlace
            </x-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-button type="submit" variant="ghost">
                Cerrar sesion
            </x-button>
        </form>
    </div>
</x-guest-layout>
