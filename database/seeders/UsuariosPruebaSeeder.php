<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuariosPruebaSeeder extends Seeder
{
    /**
     * Seeder para crear usuarios de prueba con diferentes roles.
     * Cada usuario tendrá la contraseña: password
     */
    public function run(): void
    {
        $usuarios = [
            ['name' => 'Admin Prueba', 'email' => 'admin@miracle.com', 'role' => 'admin'],
            ['name' => 'Vendedor Prueba', 'email' => 'vendedor@miracle.com', 'role' => 'vendedor'],
            ['name' => 'Inventarios Prueba', 'email' => 'inventarios@miracle.com', 'role' => 'inventarios'],
            ['name' => 'Facturación Prueba', 'email' => 'facturacion@miracle.com', 'role' => 'facturacion'],
            ['name' => 'PDV Prueba', 'email' => 'pdv@miracle.com', 'role' => 'punto_venta'],
            ['name' => 'Cliente Prueba', 'email' => 'cliente@miracle.com', 'role' => 'cliente'],
            ['name' => 'Técnico Prueba', 'email' => 'tecnico@miracle.com', 'role' => 'tecnico'],
            ['name' => 'Auxiliar Inventario Prueba', 'email' => 'auxiliar@miracle.com', 'role' => 'auxiliar_inventario'],
            ['name' => 'Centro Experiencia Prueba', 'email' => 'centro@miracle.com', 'role' => 'centro_experiencia', 'password' => '12345678'],
            ['name' => 'Auxiliar Admin Prueba', 'email' => 'auxiliar_admin@miracle.com', 'role' => 'auxiliar_administrativo', 'password' => '12345678'],
        ];

        foreach ($usuarios as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password'] ?? 'password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$data['role']]);

            $this->command->info("Usuario creado/actualizado: {$data['email']} con rol {$data['role']}");
        }

        $this->command->info('');
        $this->command->info('=== Usuarios de prueba creados exitosamente ===');
        $this->command->info('Contraseña para todos: password');
    }
}
