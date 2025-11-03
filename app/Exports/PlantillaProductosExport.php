<?php

namespace App\Exports;

use App\Models\Categoria;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PlantillaProductosExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        $data = [];

        // Obtener categorías para ejemplos
        $categorias = Categoria::activas()->limit(3)->get();

        if ($categorias->count() > 0) {
            foreach ($categorias as $index => $categoria) {
                $data[] = [
                    'Producto Ejemplo ' . ($index + 1),
                    'Descripción del producto ' . ($index + 1),
                    $categoria->slug,
                    'Marca Ejemplo',
                    100.00,
                    150.00,
                    140.00,
                    145.00,
                    160.00
                ];
            }
        } else {
            // Ejemplos genéricos si no hay categorías
            $data[] = [
                'Producto 1',
                'Descripción del producto 1',
                'categoria-slug',
                'Marca A',
                100.00,
                150.00,
                140.00,
                145.00,
                160.00
            ];
            $data[] = [
                'Producto 2',
                'Descripción del producto 2',
                'categoria-slug',
                'Marca B',
                200.00,
                300.00,
                280.00,
                290.00,
                320.00
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'ITEM',
            'DESCRIPCION',
            'CATEGORIA',
            'MARCA',
            'COSTO',
            'PRECIO VENTA ORO',
            'PRECIO VENTA INSTALADOR ESPECIAL',
            'PRECIO VENTA INSTALADOR',
            'PRECIO VENTA FINAL'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para encabezados
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Bordes para todas las celdas con datos (aplicar a las primeras 1000 filas)
        $maxRows = 1000;
        $sheet->getStyle('A1:I' . $maxRows)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D0D0D0']
                ]
            ]
        ]);

        // Formato de moneda para columnas de precios (E a I) - Aplicar a las primeras 1000 filas
        // Esto asegura que cuando el usuario agregue nuevas filas, mantengan el formato de moneda
        $sheet->getStyle('E2:I' . $maxRows)->getNumberFormat()
            ->setFormatCode('$#,##0.00');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,  // ITEM
            'B' => 50,  // DESCRIPCION
            'C' => 25,  // CATEGORIA
            'D' => 20,  // MARCA
            'E' => 18,  // COSTO
            'F' => 25,  // PRECIO VENTA ORO
            'G' => 35,  // PRECIO VENTA INSTALADOR ESPECIAL
            'H' => 30,  // PRECIO VENTA INSTALADOR
            'I' => 25,  // PRECIO VENTA FINAL
        ];
    }
}
