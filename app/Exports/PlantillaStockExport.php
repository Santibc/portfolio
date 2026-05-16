<?php

namespace App\Exports;

use App\Models\Producto;
use App\Models\VarianteProducto;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PlantillaStockExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new PlantillaStockHoja(),
            new PlantillaStockInstrucciones(),
        ];
    }
}

class PlantillaStockHoja implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'referencia',
            'cantidad',
            'modo',
            'stock_minimo',
            'stock_maximo',
            'ubicacion',
        ];
    }

    public function array(): array
    {
        $rows = [];

        $variante = VarianteProducto::whereNotNull('sku')->where('sku', '!=', '')->first();
        if ($variante) {
            $rows[] = [$variante->sku, 25, 'set', 5, 100, 'Bodega A'];
        }

        $producto = Producto::where('tiene_variantes', false)
            ->orWhereDoesntHave('variantes')
            ->first();
        if ($producto) {
            $rows[] = [$producto->referencia, 10, 'sumar', 2, null, 'Estante 3'];
        }

        if (empty($rows)) {
            $rows[] = ['SKU-001', 25, 'set', 5, 100, 'Bodega A'];
            $rows[] = ['REF-001', 10, 'sumar', 2, null, 'Estante 3'];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Stock';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, // referencia
            'B' => 12, // cantidad
            'C' => 12, // modo
            'D' => 14, // stock_minimo
            'E' => 14, // stock_maximo
            'F' => 22, // ubicacion
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Encabezado general
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 12,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Resaltar columnas obligatorias en rojo (referencia, cantidad)
        $sheet->getStyle('A1:B1')->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C00000'],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        $highestRow = max(2, $sheet->getHighestRow());
        $sheet->getStyle("A2:F{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'D9D9D9'],
                ],
            ],
        ]);

        return [];
    }
}

class PlantillaStockInstrucciones implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function headings(): array
    {
        return ['Columna', 'Obligatorio', 'Descripción'];
    }

    public function array(): array
    {
        return [
            ['referencia',   'Sí', 'Referencia del producto o SKU de la variante.'],
            ['cantidad',     'Sí', 'Entero positivo. Se interpreta según la columna "modo".'],
            ['modo',         'No', 'set (reemplaza el stock actual, valor por defecto), sumar (entrada) o restar (salida).'],
            ['stock_minimo', 'No', 'Umbral para la alerta de stock bajo.'],
            ['stock_maximo', 'No', 'Capacidad máxima de referencia.'],
            ['ubicacion',    'No', 'Ubicación física (bodega, estante, etc).'],
            ['',             '',   ''],
            ['Notas',        '',   'Encabezados en la fila 1, exactamente como en la hoja "Stock". Una fila por referencia.'],
        ];
    }

    public function title(): string
    {
        return 'Instrucciones';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 14,
            'C' => 80,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Filas obligatorias resaltadas
        $sheet->getStyle('A2:C3')->applyFromArray([
            'font' => ['color' => ['rgb' => 'C00000'], 'bold' => true],
        ]);

        $sheet->getStyle('A1:C9')->applyFromArray([
            'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_TOP],
        ]);

        return [];
    }
}
