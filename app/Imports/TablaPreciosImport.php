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
                ->where('largo_rango_min', $row['largo_min'])
                ->where(function ($q) use ($row) {
                    if ($row['largo_max'] === null || $row['largo_max'] === '') {
                        $q->whereNull('largo_rango_max');
                    } else {
                        $q->where('largo_rango_max', $row['largo_max']);
                    }
                })
                ->where('cantidad_rango_min', $row['cantidad_min'])
                ->where(function ($q) use ($row) {
                    if ($row['cantidad_max'] === null || $row['cantidad_max'] === '') {
                        $q->whereNull('cantidad_rango_max');
                    } else {
                        $q->where('cantidad_rango_max', $row['cantidad_max']);
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
            'largo_min' => 'required|numeric',
            'cantidad_min' => 'required|numeric',
            'precio' => 'required|numeric|min:0',
        ];
    }

    public function getActualizados(): int
    {
        return $this->actualizados;
    }
}
