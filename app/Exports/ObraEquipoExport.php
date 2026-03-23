<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ObraEquipoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $trabajadores;
    protected $obraNombre;
    protected $periodo;

    public function __construct($trabajadores, string $obraNombre = '', string $periodo = '')
    {
        $this->trabajadores = $trabajadores;
        $this->obraNombre = $obraNombre;
        $this->periodo = $periodo;
    }

    public function collection()
    {
        return $this->trabajadores;
    }

    public function headings(): array
    {
        return [
            'DNI',
            'Nombre Completo',
            'Categoría',
            'Asignación',
            'Rol',
            'Fecha Inicio',
            'Fecha Fin',
            'Días en Obra',
            'Horas Trabajadas',
            'Horas Extra',
            'Total Fichajes',
        ];
    }

    public function map($row): array
    {
        return [
            $row['dni'] ?? '-',
            $row['nombre_completo'] ?? '-',
            $row['categoria'] ?? '-',
            $row['asignacion'] ?? '-',
            $row['rol'] ?? 'Operario',
            $row['fecha_inicio'] ?? '-',
            $row['fecha_fin'] ?? 'Activo',
            $row['dias_en_obra'] ?? 0,
            $row['horas_trabajadas'] ? number_format($row['horas_trabajadas'], 2) : '0',
            $row['horas_extra'] ? number_format($row['horas_extra'], 2) : '0',
            $row['total_fichajes'] ?? 0,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E8F0']
                ]
            ],
        ];
    }
}
