<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Plantilla descargable para importar líneas de factura desde Excel.
 * Incluye una fila de encabezado y, opcionalmente, ejemplos con referencias
 * reales del catálogo para guiar al usuario.
 */
class PlantillaImportacionFacturaExport implements FromArray, ShouldAutoSize, WithHeadings
{
    /**
     * @param  list<array<int, mixed>>  $ejemplos
     * @param  list<string>  $tallas  Nombres de las tallas activas (una columna por talla)
     */
    public function __construct(
        private readonly array $ejemplos = [],
        private readonly array $tallas = [],
    ) {}

    /**
     * @return list<array<int, mixed>>
     */
    public function array(): array
    {
        return $this->ejemplos;
    }

    /**
     * Encabezados: una columna por talla activa (cantidad por talla) entre la
     * referencia y la cantidad total. Para prendas, la cantidad se calcula como
     * la suma de las tallas; para no-prendas usa la columna "Cantidad".
     *
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'Referencia',
            ...array_values($this->tallas),
            'Cantidad',
            'Precio unitario',
            'Descuento',
            'Tipo descuento (valor/porcentaje)',
            'IVA %',
        ];
    }
}
