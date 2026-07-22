<?php

namespace App\Exports;

use App\Exports\Sheets\FeriaInventarioResumenSheet;
use App\Exports\Sheets\FeriaMovimientosSheet;
use App\Models\Feria;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Excel del inventario de la feria, con 2 hojas:
 *  - Resumen: cargado / vendido / devuelto / stock actual por producto-tono (cuadre antes/después).
 *  - Movimientos: cada movimiento con su FECHA Y HORA exacta.
 */
class FeriaInventarioExport implements WithMultipleSheets
{
    private Feria $feria;

    public function __construct(Feria $feria)
    {
        $this->feria = $feria;
    }

    public function sheets(): array
    {
        return [
            new FeriaInventarioResumenSheet($this->feria),
            new FeriaMovimientosSheet($this->feria),
        ];
    }
}
