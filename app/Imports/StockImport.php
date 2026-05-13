<?php

namespace App\Imports;

use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\StockProducto;
use App\Models\VarianteProducto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Importa cantidades de stock desde Excel.
 *
 * Columnas reconocidas:
 *   referencia        (obligatoria) — referencia del producto o SKU de la variante.
 *   cantidad          (obligatoria) — número entero de unidades disponibles.
 *   stock_minimo      (opcional)
 *   stock_maximo      (opcional)
 *   ubicacion         (opcional)
 *   modo              (opcional) — 'set' (default) reemplaza, 'sumar' suma a lo existente, 'restar' resta.
 */
class StockImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
    public int $exito = 0;
    public int $fallo = 0;
    public array $errores = [];

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'enclosure' => '"',
            'escape_character' => '\\',
            'contiguous' => false,
            'input_encoding' => 'UTF-8',
        ];
    }

    public function collection(Collection $rows)
    {
        $fila = 2;
        foreach ($rows as $row) {
            $r = $this->normalizar($row->toArray());
            $ref = trim($r['referencia'] ?? $r['ref'] ?? $r['sku'] ?? '');
            $cantidadRaw = $r['cantidad'] ?? null;

            if ($ref === '') {
                $this->fallar($fila, '', 'Referencia vacía');
                $fila++; continue;
            }
            if ($cantidadRaw === null || $cantidadRaw === '' || !is_numeric($cantidadRaw)) {
                $this->fallar($fila, $ref, 'Cantidad no válida');
                $fila++; continue;
            }
            $cantidad = (int) $cantidadRaw;
            if ($cantidad < 0) {
                $this->fallar($fila, $ref, 'Cantidad negativa no permitida');
                $fila++; continue;
            }

            try {
                $this->aplicar($ref, $cantidad, $r);
                $this->exito++;
            } catch (\Throwable $e) {
                Log::error('Error import stock', ['fila' => $fila, 'ref' => $ref, 'msg' => $e->getMessage()]);
                $this->fallar($fila, $ref, $e->getMessage());
            }

            $fila++;
        }
    }

    private function aplicar(string $referencia, int $cantidad, array $r): void
    {
        // Intentar primero como variante (SKU)
        $variante = VarianteProducto::where('sku', $referencia)->first();
        if ($variante) {
            $producto = $variante->producto;
            $varianteId = $variante->id;
        } else {
            $producto = Producto::where('referencia', $referencia)->first();
            if (!$producto) {
                throw new \RuntimeException("Producto/SKU '{$referencia}' no encontrado.");
            }
            $varianteId = null;
        }

        $stock = StockProducto::firstOrNew([
            'producto_id'          => $producto->id,
            'variante_producto_id' => $varianteId,
        ]);

        $stockAnterior = (int) ($stock->cantidad_disponible ?? 0);
        $modo = strtolower(trim($r['modo'] ?? 'set'));
        $stockNuevo = match ($modo) {
            'sumar'  => $stockAnterior + $cantidad,
            'restar' => max(0, $stockAnterior - $cantidad),
            default  => $cantidad,
        };

        $stock->fill([
            'cantidad_disponible' => $stockNuevo,
            'cantidad_reservada'  => $stock->cantidad_reservada ?? 0,
            'stock_minimo'        => $r['stockminimo'] ?? $r['stock_minimo'] ?? $stock->stock_minimo ?? 0,
            'stock_maximo'        => $r['stockmaximo'] ?? $r['stock_maximo'] ?? $stock->stock_maximo,
            'ubicacion'           => $r['ubicacion'] ?? $stock->ubicacion,
            'alerta_stock_bajo'   => $stock->alerta_stock_bajo ?? true,
        ])->save();

        $diferencia = $stockNuevo - $stockAnterior;
        if ($diferencia !== 0) {
            MovimientoStock::create([
                'producto_id'          => $producto->id,
                'variante_producto_id' => $varianteId,
                'tipo_movimiento'      => $diferencia > 0 ? 'entrada' : 'salida',
                'cantidad'             => abs($diferencia),
                'stock_anterior'       => $stockAnterior,
                'stock_nuevo'          => $stockNuevo,
                'origen'               => 'importacion_excel',
                'motivo'               => 'Importación desde Excel (modo: ' . $modo . ')',
                'usuario_id'           => auth()->id() ?? 1,
            ]);
        }
    }

    private function normalizar(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            $clean = strtolower(trim(str_replace([' ', '-', '_'], '', $k)));
            $out[$clean] = is_string($v) ? trim($v) : $v;
        }
        return $out;
    }

    private function fallar(int $fila, string $ref, string $msg): void
    {
        $this->fallo++;
        $this->errores[] = ['fila' => $fila, 'referencia' => $ref, 'mensaje' => $msg];
    }
}
