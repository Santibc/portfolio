<?php

namespace Database\Seeders;

use App\Models\TipoPago;
use Illuminate\Database\Seeder;

class TipoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Contado', 'dias_credito' => 0, 'codigo_siigo' => null, 'activo' => true],
            ['nombre' => 'Crédito 14 días', 'dias_credito' => 14, 'codigo_siigo' => null, 'activo' => true],
            ['nombre' => 'Crédito 30 días', 'dias_credito' => 30, 'codigo_siigo' => null, 'activo' => true],
            ['nombre' => 'Crédito 60 días', 'dias_credito' => 60, 'codigo_siigo' => null, 'activo' => true],
            ['nombre' => 'Transferencia ACH', 'dias_credito' => 0, 'codigo_siigo' => null, 'activo' => true],
            ['nombre' => 'Crédito ACH', 'dias_credito' => 14, 'codigo_siigo' => null, 'activo' => true],
        ];

        foreach ($tipos as $tipo) {
            TipoPago::updateOrCreate(['nombre' => $tipo['nombre']], $tipo);
        }
    }
}
