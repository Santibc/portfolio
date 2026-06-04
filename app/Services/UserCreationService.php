<?php

namespace App\Services;

use App\Mail\UsuarioBienvenida;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserCreationService
{
    /**
     * Crea un nuevo usuario y envía correo de bienvenida.
     */
    public function create(array $userData): User
    {
        $plainPassword = $userData['password'] ?? '12345678';

        // Si el correo pertenece a un usuario eliminado (soft-delete), lo restauramos
        // y sobrescribimos con los datos nuevos. Insertar de cero chocaría con el
        // índice UNIQUE de la columna email en BD.
        $user = User::withTrashed()->where('email', $userData['email'])->first();

        if ($user && $user->trashed()) {
            $user->restore();
            $user->fill([
                'name'     => $userData['name'] ?? $userData['email'],
                'password' => Hash::make($plainPassword),
            ]);
            if (array_key_exists('activo', $userData)) {
                $user->activo = (bool) $userData['activo'];
            }
            $user->save();
        } else {
            $user = User::create([
                'name'     => $userData['name'] ?? $userData['email'],
                'email'    => $userData['email'],
                'password' => Hash::make($plainPassword),
            ]);
        }

        try {
            Mail::to($user->email)->send(new UsuarioBienvenida($user, $plainPassword));
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar correo de bienvenida: '.$e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }

        return $user;
    }

    /**
     * Actualiza un usuario existente.
     */
    public function update(User $user, array $userData): User
    {
        $user->name = $userData['name'] ?? $user->name;
        $user->email = $userData['email'] ?? $user->email;

        if (array_key_exists('activo', $userData)) {
            $user->activo = (bool) $userData['activo'];
        }

        if (!empty($userData['password'])) {
            $user->password = Hash::make($userData['password']);
        }
        $user->save();

        return $user;
    }
}
