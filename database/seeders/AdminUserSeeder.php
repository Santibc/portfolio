<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crear usuario administrador con acceso completo
     */
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->hasRole('Administrador')) {
            $admin->assignRole('Administrador');
        }

        $this->command->info('Usuario administrador creado exitosamente');
        $this->command->info('Email: admin@admin.com');
        $this->command->info('Password: 12345678');
    }
}
