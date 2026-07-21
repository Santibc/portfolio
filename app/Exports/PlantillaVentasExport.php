<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlantillaVentasExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['vendedor_email', 'fecha', 'monto', 'descripcion', 'almacen_codigo'];
    }

    public function array(): array
    {
        return [
            ['vendedor@ejemplo.com', now()->format('Y-m-d'), 1500000, 'Venta ejemplo', 'BOG-01'],
        ];
    }
}
