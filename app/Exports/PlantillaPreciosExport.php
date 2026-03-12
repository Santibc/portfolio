<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PlantillaPreciosExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        $productos = Producto::where('activo', true)
            ->where('eliminado', false)
            ->orderBy('nombre')
            ->get();

        $data = collect();

        foreach ($productos as $producto) {
            $row = [
                'nombre' => $producto->nombre,
            ];

            $nombresListas = ['COSTO', 'PRECIO VENTA ORO', 'PRECIO VENTA INSTALADOR ESPECIAL', 'PRECIO VENTA INSTALADOR', 'PRECIO VENTA FINAL'];
            for ($i = 1; $i <= 5; $i++) {
                $precio = $producto->precios()->where('lista_precio_id', $i)->first();
                $row[$nombresListas[$i - 1]] = $precio ? $precio->precio : null;
            }

            $data->push($row);
        }

        return $data;
    }
    
    public function headings(): array
    {
        return [
            'Nombre',
            'COSTO',
            'PRECIO VENTA ORO',
            'PRECIO VENTA INSTALADOR ESPECIAL',
            'PRECIO VENTA INSTALADOR',
            'PRECIO VENTA FINAL'
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        // Estilo para los encabezados (6 columnas: A-F)
        $sheet->getStyle('A1:F1')->applyFromArray([
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
        
        // Altura de la fila de encabezados
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Aplicar bordes a todas las celdas con datos
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A2:F{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9D9D9'],
                ],
            ],
        ]);

        // Formato de moneda para las columnas de precios
        $sheet->getStyle("B2:F{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');
        
        // Centrar la columna de referencia
        $sheet->getStyle("A2:A{$highestRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        
        return [];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 50,  // Nombre
            'B' => 18,  // COSTO
            'C' => 22,  // PRECIO VENTA ORO
            'D' => 35,  // PRECIO VENTA INSTALADOR ESPECIAL
            'E' => 28,  // PRECIO VENTA INSTALADOR
            'F' => 25,  // PRECIO VENTA FINAL
        ];
    }
    
    public function title(): string
    {
        return 'Plantilla Precios';
    }
}