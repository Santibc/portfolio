<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            TiposProductoMercadoSeeder::class,
            ProductosMercadoDemoSeeder::class,
            ListaMercadoSeeder::class,
            TiposMenuItemSeeder::class,
            MetodosPagoSeeder::class,
            MenuItemsSeeder::class,
            TrabajadoresTurnoSeeder::class,
        ]);
    }
}
