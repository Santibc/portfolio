<x-guest-layout>
    @section('title', 'Confirmar contraseña')

    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">Zona segura</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Confirma tu contraseña para continuar.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium">Contraseña</label>
            <div class="relative">
                <i class="bi bi-shield-lock pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400"></i>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="input pl-9" autofocus>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <button type="submit" class="btn-primary w-full">
            Confirmar
            <i class="bi bi-check-lg"></i>
        </button>
    </form>
</x-guest-layout>
