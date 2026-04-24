<x-guest-layout>
    @section('title', 'Iniciar sesión')

    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">Iniciar sesión</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Accede a tu cuenta para continuar.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium">Correo</label>
            <div class="relative">
                <i class="bi bi-envelope pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="tu@email.com" class="input pl-9">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="text-sm font-medium">Contraseña</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">¿Olvidaste tu contraseña?</a>
                @endif
            </div>
            <div class="relative" x-data="{ show: false }">
                <i class="bi bi-lock pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400"></i>
                <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="••••••••" class="input pl-9 pr-10">
                <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300" aria-label="Mostrar contraseña">
                    <i x-show="!show" class="bi bi-eye"></i>
                    <i x-show="show" class="bi bi-eye-slash" style="display: none;"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <label class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-zinc-300 text-primary-600 focus:ring-primary-500 dark:border-zinc-600 dark:bg-zinc-800">
            Recordarme
        </label>

        <button type="submit" class="btn-primary w-full">
            Ingresar
            <i class="bi bi-arrow-right"></i>
        </button>
    </form>

    @if (Route::has('register'))
        <p class="mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">Regístrate</a>
        </p>
    @endif
</x-guest-layout>
