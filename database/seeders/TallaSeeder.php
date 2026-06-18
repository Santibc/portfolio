<?php

namespace Database\Seeders;

use App\Models\Talla;
use Illuminate\Database\Seeder;

class TallaSeeder extends Seeder
{
    public function run(): void
    {
        $tallas = [
            ['nombre' => 'XS', 'orden' => 1, 'activo' => true],
            ['nombre' => 'S', 'orden' => 2, 'activo' => true],
            ['nombre' => 'M', 'orden' => 3, 'activo' => true],
            ['nombre' => 'L', 'orden' => 4, 'activo' => true],
            ['nombre' => 'XL', 'orden' => 5, 'activo' => true],
            ['nombre' => 'XXL', 'orden' => 6, 'activo' => true],
        ];

        foreach ($tallas as $talla) {
            Talla::updateOrCreate(['nombre' => $talla['nombre']], $talla);
        }
    }
}
