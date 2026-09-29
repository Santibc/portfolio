<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Solo datos base (rol admin + tablas de referencia). Los datos demo
     * viven en DemoSeeder: php artisan db:seed --class=DemoSeeder
     */
    public function run()
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            DiasSemanaSeeder::class,
            TiposMenuItemSeeder::class,
            MetodosPagoSeeder::class,
            ConceptosGastoFijoSeeder::class,
        ]);
    }
}
