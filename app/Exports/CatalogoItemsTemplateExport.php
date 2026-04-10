<?php

namespace App\Exports;

use App\Exports\Sheets\CatalogoItemsDatosSheet;
use App\Exports\Sheets\CatalogoItemsInstruccionesSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CatalogoItemsTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new CatalogoItemsDatosSheet(),
            new CatalogoItemsInstruccionesSheet(),
        ];
    }
}
