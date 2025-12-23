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
            // 1. Roles y permisos (incluye usuarios base)
            RolesAndPermissionsSeeder::class,

            // 2. Catálogos (tipos, categorías)
            CatalogosSeeder::class,

            // 3. Datos de ejemplo (clientes, trabajadores, obras)
            DatosEjemploSeeder::class,
        ]);
    }
}
