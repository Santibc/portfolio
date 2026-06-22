<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            // Mercado / Productos
            TiposProductoMercadoSeeder::class,
            ProductosMercadoDemoSeeder::class,
            ListaMercadoSeeder::class,
            MercadosDemoSeeder::class,
            // Caja / Menú
            DiasSemanaSeeder::class,
            TiposMenuItemSeeder::class,
            MetodosPagoSeeder::class,
            MenuItemsSeeder::class,
            TrabajadoresTurnoSeeder::class,
            TurnosCajaVentasGastosSeeder::class,
            PagosAhorroSeeder::class,
            // Gastos fijos mensuales
            ConceptosGastoFijoSeeder::class,
            // Nómina
            EmpleadoSeeder::class,
            NominaDemoSeeder::class,
            // Reintento con títulos alternativos para imágenes que Wikipedia no resolvió en el primer paso
            BackfillImagenesFaltantesSeeder::class,
        ]);
    }
}
