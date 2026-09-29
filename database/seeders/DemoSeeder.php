<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DatabaseSeeder::class,
            // Mercado / Productos
            TiposProductoMercadoSeeder::class,
            ProductosMercadoDemoSeeder::class,
            ListaMercadoSeeder::class,
            MercadosDemoSeeder::class,
            // Caja / Menú
            MenuItemsSeeder::class,
            TrabajadoresTurnoSeeder::class,
            TurnosCajaVentasGastosSeeder::class,
            PagosAhorroSeeder::class,
            // Nómina
            EmpleadoSeeder::class,
            NominaDemoSeeder::class,
            // Reintento con títulos alternativos para imágenes que Wikipedia no resolvió en el primer paso
            BackfillImagenesFaltantesSeeder::class,
        ]);
    }
}
