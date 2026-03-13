<?php

namespace App\Exports;

use App\Models\Producto;
use App\Models\ListaPrecio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PlantillaPreciosExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    private $listas;

    public function __construct()
    {
        $this->listas = ListaPrecio::activas()->get();
    }

    public function collection()
    {
        $productos = Producto::where('activo', true)
            ->where('eliminado', false)
            ->orderBy('nombre')
            ->get();

        $data = collect();

        foreach ($productos as $producto) {
            $row = ['nombre' => $producto->nombre];

            foreach ($this->listas as $lista) {
                $precio = $producto->precios()->where('lista_precio_id', $lista->id)->first();
                $row[$lista->nombre] = $precio ? $precio->precio : null;
            }

            $data->push($row);
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = ['Nombre'];

        foreach ($this->listas as $lista) {
            $headings[] = strtoupper($lista->nombre);
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Estilo para encabezados
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        // Bordes en datos
        $sheet->getStyle("A2:{$lastCol}{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9D9D9'],
                ],
            ],
        ]);

        // Formato moneda en columnas de precios (B en adelante)
        $sheet->getStyle("B2:{$lastCol}{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        return [];
    }

    public function title(): string
    {
        return 'Plantilla Precios';
    }
}
