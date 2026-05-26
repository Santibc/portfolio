<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OperarioComplementarExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $piezas;

    public function __construct($piezas)
    {
        $this->piezas = $piezas;
    }

    public function collection()
    {
        return $this->piezas;
    }

    public function headings(): array
    {
        return [
            'Orden #',
            'Pieza',
            'Especificacion',
            'Progreso',
            'Ultimo Operario',
            'Cliente',
            'Fecha Entrega',
        ];
    }

    public function map($pieza): array
    {
        $ultimaAsignacion = $pieza->asignaciones()
            ->where('activa', false)
            ->latest()
            ->with('asignadoA')
            ->first();

        return [
            $pieza->orden->numero_orden ?? '-',
            $pieza->nombre,
            $pieza->especificacion ?? '-',
            intval($pieza->porcentaje_avance) . '%',
            $ultimaAsignacion->asignadoA->name ?? '-',
            $pieza->orden->cliente->nombre ?? '-',
            $pieza->orden->fecha_entrega ? $pieza->orden->fecha_entrega->format('d/m/Y') : '-',
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
