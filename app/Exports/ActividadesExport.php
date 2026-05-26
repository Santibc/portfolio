<?php

namespace App\Exports;

use App\Models\RegistroActividad;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActividadesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $actividades;
    protected bool $global;

    public function __construct($actividades, bool $global = false)
    {
        $this->actividades = $actividades;
        $this->global = $global;
    }

    public function collection()
    {
        return $this->actividades;
    }

    public function headings(): array
    {
        if ($this->global) {
            return [
                'Fecha/Hora',
                'Usuario',
                'Rol',
                'Accion',
                'Orden #',
                'Descripcion',
            ];
        }
        return [
            'Fecha/Hora',
            'Accion',
            'Orden #',
            'Descripcion',
        ];
    }

    public function map($r): array
    {
        $accionTexto = RegistroActividad::TIPOS_ACCION[$r->accion] ?? $r->accion;
        $ordenNumero = $r->orden->numero_orden ?? ($r->orden_id ? '#' . $r->orden_id : '-');

        if ($this->global) {
            return [
                $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-',
                $r->usuario->name ?? '-',
                $r->usuario->roles->first()->name ?? '-',
                $accionTexto,
                $ordenNumero,
                $r->descripcion,
            ];
        }
        return [
            $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-',
            $accionTexto,
            $ordenNumero,
            $r->descripcion,
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
