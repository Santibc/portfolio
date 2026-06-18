<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Plantilla descargable para importar/actualizar productos desde Excel.
 * El orden de columnas debe coincidir con el que espera ProductoImportService.
 * Incluye filas de ejemplo (referencias reales del catálogo) para guiar al usuario.
 */
class PlantillaProductosExport implements FromArray, ShouldAutoSize, WithHeadings
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
            'Descripción',
            'Color',
            'Composición',
            'Código PA',
            'País origen',
            'Precio unitario',
            'Unidad medida',
            'Es prenda (Sí/No)',
            'Activo (Sí/No)',
        ];
    }
}
