<?php

namespace App\Imports;

use App\Models\TablaPrecioServicio;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TablaPreciosImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected int $actualizados = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $updated = TablaPrecioServicio::where('tipo_servicio', $row['tipo_servicio'])
                ->where('clave_calibre', $row['calibre'])
                ->where('cantidad_servicios_min', $row['cantidad_servicios_min'])
                ->where(function ($q) use ($row) {
                    if ($row['cantidad_servicios_max'] === null || $row['cantidad_servicios_max'] === '') {
                        $q->whereNull('cantidad_servicios_max');
                    } else {
                        $q->where('cantidad_servicios_max', $row['cantidad_servicios_max']);
                    }
                })
                ->where('largo_mm_min', $row['largo_mm_min'])
                ->where(function ($q) use ($row) {
                    if ($row['largo_mm_max'] === null || $row['largo_mm_max'] === '') {
                        $q->whereNull('largo_mm_max');
                    } else {
                        $q->where('largo_mm_max', $row['largo_mm_max']);
                    }
                })
                ->update(['precio' => $row['precio']]);

            $this->actualizados += $updated;
        }
    }

    public function rules(): array
    {
        return [
            'tipo_servicio' => 'required|string',
            'calibre' => 'required|string',
            'cantidad_servicios_min' => 'required|numeric',
            'largo_mm_min' => 'required|numeric',
            'precio' => 'required|numeric|min:0',
        ];
    }

    public function getActualizados(): int
    {
        return $this->actualizados;
    }
}
