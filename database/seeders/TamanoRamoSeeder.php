<?php

namespace Database\Seeders;

use App\Models\TamanoRamo;
use Illuminate\Database\Seeder;

class TamanoRamoSeeder extends Seeder
{
    public function run()
    {
        $tamanos = [
            [
                'nombre' => 'Pequeño',
                'cantidad_flores_min' => 6,
                'cantidad_flores_max' => 12,
                'precio_base' => 15000,
                'descripcion' => 'Perfecto para regalar o decorar espacios pequeños. Incluye envoltura básica y tarjeta.',
                'activo' => true,
                'orden' => 1,
            ],
            [
                'nombre' => 'Mediano',
                'cantidad_flores_min' => 12,
                'cantidad_flores_max' => 24,
                'precio_base' => 25000,
                'descripcion' => 'Ideal para ocasiones especiales. Incluye envoltura premium y tarjeta personalizada.',
                'activo' => true,
                'orden' => 2,
            ],
            [
                'nombre' => 'Grande',
                'cantidad_flores_min' => 24,
                'cantidad_flores_max' => 50,
                'precio_base' => 40000,
                'descripcion' => 'Impresionante arreglo para momentos importantes. Incluye envoltura de lujo y tarjeta especial.',
                'activo' => true,
                'orden' => 3,
            ],
        ];

        foreach ($tamanos as $tamano) {
            TamanoRamo::firstOrCreate(
                ['nombre' => $tamano['nombre']],
                $tamano
            );
        }

        $this->command->info('✅ Tamaños de ramo creados exitosamente');
    }
}
