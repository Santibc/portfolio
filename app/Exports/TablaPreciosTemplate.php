<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Plantilla vacia con los encabezados esperados por el importador.
 * Util para guiar al usuario y garantizar que las columnas tengan los nombres correctos.
 */
class TablaPreciosTemplate implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['corte_inox', 'CORTE INOX', '#22', 0.76, 1, 5, 1, 60, 2280, 6839],
            ['corte_inox', 'CORTE INOX', '#22', 0.76, 1, 5, 61, 120, 2400, 6839],
        ];
    }

    public function headings(): array
    {
        return [
            'tipo_servicio',
            'etiqueta',
            'calibre',
            'calibre_mm',
            'cantidad_servicios_min',
            'cantidad_servicios_max',
            'largo_mm_min',
            'largo_mm_max',
            'precio',
            'precio_minimo',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4A7C59'],
                ],
            ],
        ];
    }
}
