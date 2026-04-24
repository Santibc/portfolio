<?php

namespace Database\Seeders;

use App\Models\TipoDescuento;
use Illuminate\Database\Seeder;

class TipoDescuentoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Descuento Comercial', 'alcance' => 'linea', 'modalidad' => 'porcentaje', 'activo' => true],
            ['nombre' => 'Descuento por Volumen', 'alcance' => 'global', 'modalidad' => 'porcentaje', 'activo' => true],
            ['nombre' => 'Descuento Pronto Pago', 'alcance' => 'global', 'modalidad' => 'porcentaje', 'activo' => true],
            ['nombre' => 'Descuento Valor Fijo', 'alcance' => 'global', 'modalidad' => 'valor_fijo', 'activo' => true],
            ['nombre' => 'Descuento Línea Valor Fijo', 'alcance' => 'linea', 'modalidad' => 'valor_fijo', 'activo' => true],
        ];

        foreach ($tipos as $tipo) {
            TipoDescuento::updateOrCreate(['nombre' => $tipo['nombre']], $tipo);
        }
    }
}
