<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CatalogoItemsDatosSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Datos';
    }

    public function headings(): array
    {
        return [
            'codigo',
            'descripcion',
            'precio_unitario',
            'porcentaje_iva',
            'categoria',
        ];
    }

    public function array(): array
    {
        return [
            ['SRV-001', 'Servicio de corte laser ejemplo', 50000, 19, 'servicio'],
            ['MAT-001', 'Lamina de acero inoxidable ejemplo', 25000, 19, 'material'],
            ['PT-001', 'Pieza terminada ejemplo', 100000, 19, 'producto_terminado'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4A7C59'],
                ],
            ],
            // Example rows in italic gray
            2 => ['font' => ['italic' => true, 'color' => ['argb' => 'FF999999']]],
            3 => ['font' => ['italic' => true, 'color' => ['argb' => 'FF999999']]],
            4 => ['font' => ['italic' => true, 'color' => ['argb' => 'FF999999']]],
        ];
    }
}
