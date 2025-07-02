<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserCreationService
{
    /**
     * Crea un nuevo usuario.
     */
    public function create(array $userData): User
    {
        return User::create([
            'name' => $userData['name'] ?? $userData['email'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password'] ?? '12345678'),
            'uuid' => $userData['uuid'] ?? null,
            'avatar_url' => $userData['avatar_url'] ?? null,
            'locale' => $userData['locale'] ?? null,
            'time_notation' => $userData['time_notation'] ?? null,
            'timezone' => $userData['timezone'] ?? null,
            'slug' => $userData['slug'] ?? null,
            'scheduling_url' => $userData['scheduling_url'] ?? null,
            'calendly_uri' => $userData['calendly_uri'] ?? null,
        ]);
    }

    /**
     * Actualiza un usuario existente.
     */
    public function update(User $user, array $userData): User
    {
        $user->name = $userData['name'] ?? $user->name;
        $user->email = $userData['email'] ?? $user->email;

        if (!empty($userData['password'])) {
            $user->password = Hash::make($userData['password']);
        }

        if (!$user->uuid && !empty($userData['uuid'])) {
            $user->uuid = $userData['uuid'];
        }

        $user->avatar_url = $userData['avatar_url'] ?? $user->avatar_url;
        $user->locale = $userData['locale'] ?? $user->locale;
        $user->time_notation = $userData['time_notation'] ?? $user->time_notation;
        $user->timezone = $userData['timezone'] ?? $user->timezone;
        $user->slug = $userData['slug'] ?? $user->slug;
        $user->scheduling_url = $userData['scheduling_url'] ?? $user->scheduling_url;
        $user->calendly_uri = $userData['calendly_uri'] ?? $user->calendly_uri;

        $user->save();

        return $user;
    }
}
