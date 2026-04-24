<x-guest-layout>
    @section('title', 'Crear cuenta')

    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">Crear cuenta</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Regístrate para empezar.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium">Nombre</label>
            <div class="relative">
                <i class="bi bi-person pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400"></i>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Tu nombre" class="input pl-9">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium">Correo</label>
            <div class="relative">
                <i class="bi bi-envelope pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="tu@email.com" class="input pl-9">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium">Contraseña</label>
            <div class="relative">
                <i class="bi bi-lock pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400"></i>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" class="input pl-9">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Confirmar contraseña</label>
            <div class="relative">
                <i class="bi bi-lock-fill pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400"></i>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" class="input pl-9">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <button type="submit" class="btn-primary w-full">
            Crear cuenta
            <i class="bi bi-arrow-right"></i>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">Inicia sesión</a>
    </p>
</x-guest-layout>
