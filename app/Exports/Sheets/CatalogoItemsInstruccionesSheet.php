<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CatalogoItemsInstruccionesSheet implements FromArray, WithStyles, WithTitle, WithColumnWidths
{
    public function title(): string
    {
        return 'Instrucciones';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 60,
        ];
    }

    public function array(): array
    {
        return [
            ['INSTRUCCIONES DE IMPORTACION', ''],
            ['', ''],
            ['Columna', 'Descripcion'],
            ['codigo', 'Codigo unico del item (max 50 caracteres). Si ya existe, el item se actualizara.'],
            ['descripcion', 'Descripcion del item (obligatorio).'],
            ['precio_unitario', 'Precio unitario en pesos (numero, sin signo $, sin puntos de miles). Ej: 50000'],
            ['porcentaje_iva', 'Porcentaje de IVA (numero entre 0 y 100). Ej: 19'],
            ['categoria', 'Categoria del item. Valores validos: servicio, material, producto_terminado'],
            ['', ''],
            ['REGLAS IMPORTANTES', ''],
            ['Codigos existentes', 'Si el codigo ya existe en el catalogo, se actualizaran los datos del item (descripcion, precio, IVA, categoria).'],
            ['Codigos nuevos', 'Si el codigo no existe, se creara un nuevo item con estado ACTIVO.'],
            ['Filas con error', 'Las filas con errores se omiten sin afectar las demas. Revise el reporte al finalizar.'],
            ['Formato de precios', 'Use numeros simples sin formato. Correcto: 50000. Incorrecto: $50.000 o 50,000.'],
            ['Categorias', 'Puede escribir la categoria en mayusculas o minusculas: SERVICIO, Servicio, servicio, PRODUCTO TERMINADO, producto_terminado.'],
            ['Filas de ejemplo', 'Las filas de ejemplo en la hoja "Datos" estan en gris cursiva. Eliminelas antes de importar o reemplacelas con sus datos.'],
            ['', ''],
            ['CATEGORIAS VALIDAS', ''],
            ['servicio', 'SERVICIO - Servicios de la empresa'],
            ['material', 'MATERIAL - Materiales e insumos'],
            ['producto_terminado', 'PRODUCTO TERMINADO - Productos finales'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4A7C59'],
                ],
            ],
            3 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4A7C59'],
                ],
            ],
            10 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FF4A7C59']],
            ],
            18 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FF4A7C59']],
            ],
        ];
    }
}
