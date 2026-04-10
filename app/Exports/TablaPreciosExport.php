<?php

namespace App\Exports;

use App\Models\TablaPrecioServicio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TablaPreciosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected ?string $tipoServicio;

    public function __construct(?string $tipoServicio = null)
    {
        $this->tipoServicio = $tipoServicio;
    }

    public function collection()
    {
        $query = TablaPrecioServicio::query()
            ->orderBy('tipo_servicio')
            ->orderBy('cantidad_servicios_min')
            ->orderBy('largo_mm_min')
            ->orderBy('calibre_mm');

        if ($this->tipoServicio) {
            $query->forServicio($this->tipoServicio);
        }

        return $query->get();
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

    public function map($row): array
    {
        return [
            $row->tipo_servicio,
            $row->etiqueta_servicio,
            $row->clave_calibre,
            $row->calibre_mm,
            $row->cantidad_servicios_min,
            $row->cantidad_servicios_max,
            $row->largo_mm_min,
            $row->largo_mm_max,
            $row->precio,
            $row->precio_minimo,
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
