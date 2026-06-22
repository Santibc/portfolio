<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ConceptoGastoFijo;
use Illuminate\Database\Seeder;

class ConceptosGastoFijoSeeder extends Seeder
{
    public function run(): void
    {
        $conceptos = [
            ['nombre' => 'Arriendo',  'orden' => 1, 'activo' => true],
            ['nombre' => 'Energía',   'orden' => 2, 'activo' => true],
            ['nombre' => 'Agua',      'orden' => 3, 'activo' => true],
            ['nombre' => 'Gas',       'orden' => 4, 'activo' => true],
            ['nombre' => 'Internet',  'orden' => 5, 'activo' => true],
        ];

        foreach ($conceptos as $c) {
            ConceptoGastoFijo::updateOrCreate(['nombre' => $c['nombre']], $c);
        }
    }
}
