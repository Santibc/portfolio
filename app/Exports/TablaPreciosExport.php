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
            ->orderBy('calibre_mm')
            ->orderBy('largo_rango_min')
            ->orderBy('cantidad_rango_min');

        if ($this->tipoServicio) {
            $query->forServicio($this->tipoServicio);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Tipo Servicio',
            'Etiqueta',
            'Calibre',
            'Calibre (mm)',
            'Largo Min',
            'Largo Max',
            'Cantidad Min',
            'Cantidad Max',
            'Precio',
            'Precio Minimo',
        ];
    }

    public function map($row): array
    {
        return [
            $row->tipo_servicio,
            $row->etiqueta_servicio,
            $row->clave_calibre,
            $row->calibre_mm,
            $row->largo_rango_min,
            $row->largo_rango_max,
            $row->cantidad_rango_min,
            $row->cantidad_rango_max,
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
