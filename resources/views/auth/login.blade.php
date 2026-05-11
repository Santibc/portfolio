<x-guest-layout>
    <h2 class="text-xl font-bold text-cream-900 dark:text-cream-50 mb-1">Iniciar sesion</h2>
    <p class="text-sm text-cream-600 dark:text-cream-400 mb-6">Bienvenido de nuevo. Ingresa tus credenciales.</p>

    @if (session('status'))
        <x-alert variant="success" class="mb-4">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <x-input name="email" type="email" label="Correo" :value="old('email')" icon="mail" autofocus required autocomplete="username" />

        <div x-data="{ show: false }">
            <label class="block text-sm font-medium text-cream-800 dark:text-cream-200 mb-1.5">Contrasena</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-cream-500">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                </span>
                <input :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" class="block w-full rounded-xl border-cream-300 bg-white pl-10 pr-10 py-2.5 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-cream-500 hover:text-cream-700">
                    <i data-lucide="eye" class="w-4 h-4" x-show="!show"></i>
                    <i data-lucide="eye-off" class="w-4 h-4" x-show="show" x-cloak></i>
                </button>
            </div>
            @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between">
            <x-checkbox name="remember" label="Recordarme" />
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-primary-700 hover:text-primary-800 dark:text-primary-300">
                    Olvidaste la contrasena?
                </a>
            @endif
        </div>

        <x-button type="submit" variant="primary" iconRight="arrow-right" class="w-full">
            Ingresar
        </x-button>
    </form>
</x-guest-layout>
