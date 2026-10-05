@php
    $editando = isset($usuario);
    $rolActual = old('rol', $editando ? $usuario->rolUsuario()?->value : \App\Enums\RolUsuario::Ventas->value);
@endphp

<x-card>
    <div class="space-y-5">
        <x-input label="Nombre" name="name" :value="old('name', $usuario->name ?? null)" placeholder="Ej. Laura Gómez" required />

        <x-input label="Correo (usuario para entrar)" name="email" type="email" :value="old('email', $usuario->email ?? null)"
                 placeholder="Ej. caja@dorilokos.com" autocomplete="off" required />

        <div>
            <p class="block text-sm font-medium text-cream-800 dark:text-cream-200 mb-2">Rol <span class="text-red-500">*</span></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach (\App\Enums\RolUsuario::cases() as $rol)
                    <div class="rounded-xl border border-cream-200 dark:border-cream-700 bg-white dark:bg-cream-900/40 p-3 has-[:checked]:border-primary-500 has-[:checked]:ring-2 has-[:checked]:ring-primary-500/30 dark:has-[:checked]:border-primary-400">
                        <x-radio
                            name="rol"
                            :value="$rol->value"
                            :label="$rol->label()"
                            :description="$rol->descripcion()"
                            :checked="$rolActual === $rol->value"
                        />
                    </div>
                @endforeach
            </div>
            @error('rol')
                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                    <x-icon name="alert-circle" class="w-3.5 h-3.5" /> {{ $message }}
                </p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <x-input :label="$editando ? 'Nueva contraseña' : 'Contraseña'" name="password" type="password" autocomplete="new-password"
                     :hint="$editando ? 'Déjala vacía para no cambiarla.' : 'Mínimo 8 caracteres.'" :required="! $editando" />
            <x-input label="Confirmar contraseña" name="password_confirmation" type="password" autocomplete="new-password"
                     :required="! $editando" />
        </div>
    </div>
</x-card>

<div class="sticky bottom-0 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3 mt-4
            bg-cream-50/95 dark:bg-surface-dark/95 backdrop-blur
            border-t border-cream-200 dark:border-cream-800">
    <x-button type="submit" variant="primary" size="lg" icon="check" class="w-full justify-center">
        {{ $editando ? 'Guardar cambios' : 'Crear usuario' }}
    </x-button>
</div>
