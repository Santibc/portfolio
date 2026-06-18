<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            MonedaSeeder::class,
            ImpuestoSeeder::class,
            TipoDescuentoSeeder::class,
            IncotermSeeder::class,
            PaisesSeeder::class,
            PuertoSeeder::class,
            TipoPagoSeeder::class,
            TallaSeeder::class,
            ConfiguracionSeeder::class,
            PlantillaFacturaSeeder::class,
        ]);
    }
}
