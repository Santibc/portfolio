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
     */
    public function __construct(private readonly array $ejemplos = []) {}

    /**
     * @return list<array<int, mixed>>
     */
    public function array(): array
    {
        return $this->ejemplos;
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'Referencia',
            'Tallas (ej: S, M, L)',
            'Cantidad',
            'Precio unitario',
            'Descuento',
            'Tipo descuento (valor/porcentaje)',
            'IVA %',
        ];
    }
}
