<?php

namespace Database\Seeders;

use App\Models\Impuesto;
use Illuminate\Database\Seeder;

class ImpuestoSeeder extends Seeder
{
    public function run(): void
    {
        $impuestos = [
            ['nombre' => 'IVA 19%', 'porcentaje' => 19.00, 'tipo' => 'iva', 'codigo_siigo' => null, 'activo' => true],
            ['nombre' => 'IVA 5%', 'porcentaje' => 5.00, 'tipo' => 'iva', 'codigo_siigo' => null, 'activo' => true],
            ['nombre' => 'IVA Exento (0%)', 'porcentaje' => 0.00, 'tipo' => 'iva', 'codigo_siigo' => null, 'activo' => true],
            ['nombre' => 'ReteIVA 15%', 'porcentaje' => 15.00, 'tipo' => 'retencion', 'codigo_siigo' => null, 'activo' => true],
            ['nombre' => 'ReteFuente Servicios 4%', 'porcentaje' => 4.00, 'tipo' => 'retencion', 'codigo_siigo' => null, 'activo' => true],
        ];

        foreach ($impuestos as $impuesto) {
            Impuesto::updateOrCreate(['nombre' => $impuesto['nombre']], $impuesto);
        }
    }
}
