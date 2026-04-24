<?php

namespace Database\Seeders;

use App\Models\Puerto;
use Illuminate\Database\Seeder;

class PuertoSeeder extends Seeder
{
    public function run(): void
    {
        $puertos = [
            ['nombre' => 'Cali', 'pais' => 'Colombia', 'activo' => true],
            ['nombre' => 'Buenaventura', 'pais' => 'Colombia', 'activo' => true],
            ['nombre' => 'Cartagena', 'pais' => 'Colombia', 'activo' => true],
            ['nombre' => 'Barranquilla', 'pais' => 'Colombia', 'activo' => true],
            ['nombre' => 'Bogotá', 'pais' => 'Colombia', 'activo' => true],
            ['nombre' => 'Medellín', 'pais' => 'Colombia', 'activo' => true],
        ];

        foreach ($puertos as $puerto) {
            Puerto::updateOrCreate(['nombre' => $puerto['nombre']], $puerto);
        }
    }
}
