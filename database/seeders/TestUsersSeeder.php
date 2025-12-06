<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    /**
     * Crea usuarios de prueba.
     * Password para todos: 12345678
     *
     * @return void
     */
    public function run()
    {
        // Verificar que el rol Administrador exista
        if (!Role::where('name', 'Administrador')->exists()) {
            $this->command->warn("Rol 'Administrador' no existe. Ejecuta RolesAndPermissionsSeeder primero.");
            return;
        }

        // Usuario Administrador de prueba
        $admin = User::firstOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Usuario de Prueba',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->hasRole('Administrador')) {
            $admin->assignRole('Administrador');
            $this->command->info('Usuario de prueba creado: test@test.com');
        }

        $this->command->info('');
        $this->command->info('USUARIOS DE PRUEBA:');
        $this->command->info('===============================================');
        $this->command->info('Email: admin@admin.com | Password: 12345678');
        $this->command->info('Email: test@test.com | Password: 12345678');
        $this->command->info('===============================================');
    }
}
