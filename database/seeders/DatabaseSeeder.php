<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
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
            // Datos geográficos
            PaisSeeder::class,
            DepartamentoSeeder::class,
            CiudadSeeder::class,

            // Roles y permisos (DEBE ejecutarse antes de crear usuarios)
            RolesAndPermissionsSeeder::class,

            // Usuario administrador
            AdminUserSeeder::class,

            // Usuarios de prueba para todos los roles
            TestUsersSeeder::class,
        ]);

    }
}
