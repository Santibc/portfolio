<x-guest-layout>
    @section('title', 'Restablecer contraseña')

    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">Restablecer contraseña</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Elige una nueva contraseña para tu cuenta.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium">Correo</label>
            <div class="relative">
                <i class="bi bi-envelope pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400"></i>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="input pl-9">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium">Nueva contraseña</label>
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
            Restablecer contraseña
            <i class="bi bi-check-lg"></i>
        </button>
    </form>
</x-guest-layout>
