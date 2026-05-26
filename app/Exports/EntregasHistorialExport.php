<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EntregasHistorialExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $entregasPiezas;

    public function __construct($entregasPiezas)
    {
        $this->entregasPiezas = $entregasPiezas;
    }

    public function collection()
    {
        return $this->entregasPiezas;
    }

    public function headings(): array
    {
        return [
            'Fecha Entrega',
            'Orden #',
            'Cliente',
            'Pieza',
            'Cantidad Entregada',
            'Cantidad Total Pieza',
            'Material',
            'Calibre',
            'Entregado Por',
        ];
    }

    public function map($ep): array
    {
        $orden = $ep->entrega->orden ?? null;
        $pieza = $ep->ordenPieza;

        return [
            $ep->created_at ? $ep->created_at->format('d/m/Y H:i') : '-',
            $orden->numero_orden ?? '-',
            $orden->cliente->nombre ?? '-',
            $pieza->nombre ?? '-',
            $ep->cantidad,
            $pieza->cantidad ?? '-',
            $pieza->material ?? '-',
            $pieza->calibre ?? '-',
            $ep->entrega->entregadaPorUsuario->name ?? '-',
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
