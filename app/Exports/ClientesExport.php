<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $clientes;

    public function __construct($clientes)
    {
        $this->clientes = $clientes;
    }

    public function collection()
    {
        return $this->clientes;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Cedula',
            'Correo',
            'Celular Principal',
            'Celular Secundario',
            'Direccion',
            'Estado',
            'Fecha Registro',
        ];
    }

    public function map($cliente): array
    {
        return [
            $cliente->id,
            $cliente->nombre,
            $cliente->cedula ?? '-',
            $cliente->correo ?? '-',
            $cliente->celular_1 ?? '-',
            $cliente->celular_2 ?? '-',
            $cliente->direccion ?? '-',
            $cliente->activo ? 'Activo' : 'Inactivo',
            $cliente->created_at ? $cliente->created_at->format('d/m/Y') : '-',
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
