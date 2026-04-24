<x-guest-layout>
    @section('title', 'Recuperar contraseña')

    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">Recuperar contraseña</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            Indícanos tu correo y te enviaremos un enlace para restablecer tu contraseña.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium">Correo</label>
            <div class="relative">
                <i class="bi bi-envelope pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="tu@email.com" class="input pl-9">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <button type="submit" class="btn-primary w-full">
            Enviar enlace de recuperación
            <i class="bi bi-send"></i>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1 font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">
            <i class="bi bi-arrow-left"></i> Volver al login
        </a>
    </p>
</x-guest-layout>
