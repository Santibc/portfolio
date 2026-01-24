<?php

namespace App\Exports;

use App\Models\Fichaje;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class FichajesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $fichajes;

    public function __construct($fichajes)
    {
        $this->fichajes = $fichajes;
    }

    public function collection()
    {
        return $this->fichajes;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha',
            'Día',
            'Trabajador',
            'Obra',
            'Hora Entrada',
            'Hora Salida',
            'Horas Trabajadas',
            'Horas Extra',
            'Estado',
            'Validado Por',
            'Fecha Validación',
            'Notas',
        ];
    }

    public function map($fichaje): array
    {
        return [
            $fichaje->id,
            $fichaje->fecha->format('d/m/Y'),
            $fichaje->fecha->translatedFormat('l'),
            $fichaje->trabajador ? $fichaje->trabajador->nombre . ' ' . $fichaje->trabajador->apellidos : '-',
            $fichaje->obra ? $fichaje->obra->nombre : '-',
            $fichaje->hora_entrada ? Carbon::parse($fichaje->hora_entrada)->format('H:i') : '-',
            $fichaje->hora_salida ? Carbon::parse($fichaje->hora_salida)->format('H:i') : '-',
            $fichaje->horas_trabajadas ? number_format($fichaje->horas_trabajadas, 2) : '-',
            $fichaje->horas_extra ? number_format($fichaje->horas_extra, 2) : '0',
            $fichaje->validado ? 'Validado' : 'Pendiente',
            $fichaje->validadoPor ? $fichaje->validadoPor->name : '-',
            $fichaje->fecha_validacion ? $fichaje->fecha_validacion->format('d/m/Y H:i') : '-',
            $fichaje->notas ?? '-',
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
