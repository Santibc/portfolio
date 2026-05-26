<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OperarioGarantiasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $garantias;

    public function __construct($garantias)
    {
        $this->garantias = $garantias;
    }

    public function collection()
    {
        return $this->garantias;
    }

    public function headings(): array
    {
        return [
            'Orden #',
            'Cliente',
            'Pieza',
            'Cantidad Devuelta',
            'Motivo',
            'Fecha Registro',
        ];
    }

    public function map($g): array
    {
        return [
            $g->orden->numero_orden ?? '-',
            $g->orden->cliente->nombre ?? '-',
            $g->pieza->nombre ?? '-',
            $g->cantidad_devuelta,
            $g->motivo,
            $g->created_at ? $g->created_at->format('d/m/Y H:i') : '-',
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
