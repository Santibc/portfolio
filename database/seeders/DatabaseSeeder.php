<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Instalación limpia: solo el admin y las tablas de referencia.
     * Los seeders *Demo* siguen disponibles vía `db:seed --class=...`.
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
