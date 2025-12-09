<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // Roles y permisos
            RolesAndPermissionsSeeder::class,

            // Usuario administrador
            AdminUserSeeder::class,

            // Usuarios de prueba
            TestUsersSeeder::class,

            // Datos de demostración GVA (categorías, cursos, videos)
            GVADemoSeeder::class,
        ]);
    }
}
