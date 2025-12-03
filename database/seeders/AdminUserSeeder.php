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
        // Crear usuario administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador AGROMARKET',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'telefono' => '3001234567',
                'activo' => true,
                'documento_identidad' => '1234567890',
                'tipo_documento' => 'CC',
                'fecha_nacimiento' => '1990-01-01',
                'pais' => 'CO',
                'ciudad' => 'Bogotá',
                'direccion' => 'Calle Principal #123',
                'kyc_status' => 'aprobado',
                'kyc_aprobado_at' => now(),
            ]
        );

        // Asignar rol de Administrador (tiene todos los permisos)
        if (!$admin->hasRole('Administrador')) {
            $admin->assignRole('Administrador');
        }

        $this->command->info('✅ Usuario administrador creado exitosamente');
        $this->command->info('📧 Email: admin@admin.com');
        $this->command->info('🔑 Password: 12345678');
    }
}
