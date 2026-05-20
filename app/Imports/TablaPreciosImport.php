<?php

namespace App\Imports;

use App\Models\TablaPrecioServicio;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TablaPreciosImport implements ToCollection, WithHeadingRow
{
    protected int $actualizados = 0;
    protected int $sinCambio = 0;
    protected int $noEncontradas = 0;
    protected int $invalidas = 0;
    protected int $vacias = 0;
    protected array $errores = [];

    public function collection(Collection $rows)
    {
        $numeroFila = 1;

        foreach ($rows as $row) {
            $numeroFila++;
            $data = $this->extractRow($row->toArray());

            if ($this->isEmpty($data)) {
                $this->vacias++;
                continue;
            }

            $missing = $this->validar($data);
            if ($missing !== null) {
                $this->invalidas++;
                if (count($this->errores) < 10) {
                    $this->errores[] = "Fila {$numeroFila}: falta {$missing}";
                }
                continue;
            }

            $registro = TablaPrecioServicio::where('tipo_servicio', $data['tipo_servicio'])
                ->where('clave_calibre', $data['calibre'])
                ->where('cantidad_servicios_min', $data['cantidad_servicios_min'])
                ->where('largo_mm_min', $data['largo_mm_min'])
                ->first();

            if (!$registro) {
                $this->noEncontradas++;
                if (count($this->errores) < 10) {
                    $this->errores[] = "Fila {$numeroFila}: no existe el registro ({$data['tipo_servicio']} / {$data['calibre']} / cant>={$data['cantidad_servicios_min']} / largo>={$data['largo_mm_min']})";
                }
                continue;
            }

            $precioNuevo = (float) $data['precio'];
            if ((float) $registro->precio === $precioNuevo) {
                $this->sinCambio++;
                continue;
            }

            $registro->update(['precio' => $precioNuevo]);
            $this->actualizados++;
        }
    }

    /**
     * Acepta tanto los encabezados nuevos (cantidad_servicios_min, largo_mm_min)
     * como los antiguos (largo_rango_min, cantidad_rango_min) por compatibilidad
     * con archivos exportados antes de la migracion de renombrado.
     */
    private function extractRow(array $row): array
    {
        return [
            'tipo_servicio' => $row['tipo_servicio'] ?? null,
            'calibre' => $row['calibre'] ?? ($row['clave_calibre'] ?? null),
            'cantidad_servicios_min' => $row['cantidad_servicios_min'] ?? ($row['largo_rango_min'] ?? null),
            'largo_mm_min' => $row['largo_mm_min'] ?? ($row['cantidad_rango_min'] ?? null),
            'precio' => $row['precio'] ?? null,
        ];
    }

    private function isEmpty(array $data): bool
    {
        foreach ($data as $v) {
            if ($v !== null && $v !== '') {
                return false;
            }
        }
        return true;
    }

    private function validar(array $data): ?string
    {
        if (empty($data['tipo_servicio'])) return 'tipo_servicio';
        if (empty($data['calibre'])) return 'calibre';
        if ($data['cantidad_servicios_min'] === null || $data['cantidad_servicios_min'] === '' || !is_numeric($data['cantidad_servicios_min'])) {
            return 'cantidad_servicios_min';
        }
        if ($data['largo_mm_min'] === null || $data['largo_mm_min'] === '' || !is_numeric($data['largo_mm_min'])) {
            return 'largo_mm_min';
        }
        if ($data['precio'] === null || $data['precio'] === '' || !is_numeric($data['precio'])) {
            return 'precio';
        }
        return null;
    }

    public function getActualizados(): int { return $this->actualizados; }
    public function getSinCambio(): int { return $this->sinCambio; }
    public function getNoEncontradas(): int { return $this->noEncontradas; }
    public function getInvalidas(): int { return $this->invalidas; }
    public function getVacias(): int { return $this->vacias; }
    public function getErrores(): array { return $this->errores; }
}
