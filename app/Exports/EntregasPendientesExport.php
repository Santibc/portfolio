<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EntregasPendientesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $ordenes;

    public function __construct($ordenes)
    {
        $this->ordenes = $ordenes;
    }

    public function collection()
    {
        return $this->ordenes;
    }

    public function headings(): array
    {
        return [
            'Orden #',
            'Cliente',
            'Fecha Entrega',
            'Total Unidades',
            'Unidades Entregadas',
            'Unidades Pendientes',
            'Porcentaje Entregado',
            'Estado Trabajo',
            'Estado Entrega',
        ];
    }

    public function map($orden): array
    {
        $totalUnidades = (int) $orden->piezas->sum('cantidad');
        $unidadesEntregadas = (int) $orden->piezas->sum('cantidad_entregada');
        $unidadesPendientes = $totalUnidades - $unidadesEntregadas;
        $porcentaje = $totalUnidades > 0 ? round(($unidadesEntregadas / $totalUnidades) * 100) : 0;

        return [
            $orden->numero_orden ?? '-',
            $orden->cliente->nombre ?? '-',
            $orden->fecha_entrega ? $orden->fecha_entrega->format('d/m/Y') : '-',
            $totalUnidades,
            $unidadesEntregadas,
            $unidadesPendientes,
            $porcentaje . '%',
            strtoupper(str_replace('_', ' ', $orden->estado_trabajo)),
            $orden->estado_entrega ? strtoupper(str_replace('_', ' ', $orden->estado_entrega)) : '-',
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
