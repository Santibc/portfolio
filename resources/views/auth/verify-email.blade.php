<x-guest-layout>
    @section('title', 'Verificar correo')

    <div class="mb-8">
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
            <i class="bi bi-envelope-check text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold tracking-tight">Verifica tu correo</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            Te enviamos un enlace de verificación. Si no lo recibiste, podemos reenviártelo.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div role="alert" class="mb-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
            <i class="bi bi-check-circle mr-1.5"></i>
            Se envió un nuevo enlace de verificación a tu correo.
        </div>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary w-full">
                <i class="bi bi-arrow-clockwise"></i>
                Reenviar correo de verificación
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-secondary w-full">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>
