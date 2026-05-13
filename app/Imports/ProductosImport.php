<?php

namespace App\Imports;

use App\Models\ActualizacionPrecio;
use App\Models\Categoria;
use App\Models\ListaPrecio;
use App\Models\PrecioProducto;
use App\Models\Producto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Import unificado:
 *   - Crea/actualiza productos por su referencia.
 *   - Actualiza precios (Export1, Export2, Local1..Local4) en la misma plantilla.
 *
 * Encabezados esperados (cualquier orden, case-insensitive):
 *   referencia, nombre, descripcion, unidad_venta, unidad_empaque,
 *   categoria, tiene_extension, controlar_stock,
 *   export1, export2, local1, local2, local3, local4
 */
class ProductosImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
    protected ActualizacionPrecio $actualizacion;
    protected array $mapeoListas;

    public function __construct(ActualizacionPrecio $actualizacion)
    {
        $this->actualizacion = $actualizacion;

        $this->mapeoListas = [
            'export1' => 1, 'export_1' => 1,
            'export2' => 2, 'export_2' => 2,
            'local1'  => 3, 'local_1'  => 3,
            'local2'  => 4, 'local_2'  => 4,
            'local3'  => 5, 'local_3'  => 5,
            'local4'  => 6, 'local_4'  => 6,
        ];
    }

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
        $total = $rows->count();
        $exito = 0;
        $fallo = 0;
        $fila  = 2;

        foreach ($rows as $row) {
            $r = $this->normalizar($row->toArray());

            $referencia = trim($r['referencia'] ?? $r['ref'] ?? $r['codigo'] ?? $r['sku'] ?? '');
            if (empty($referencia)) {
                $this->actualizacion->agregarError($fila, '', 'Referencia vacía');
                $fallo++; $fila++;
                continue;
            }

            try {
                $producto = $this->upsertProducto($referencia, $r);
                $actualizoAlgo = $this->upsertPrecios($producto, $r, $fila, $referencia);

                if ($producto->wasRecentlyCreated || $actualizoAlgo) {
                    $exito++;
                } else {
                    $this->actualizacion->agregarError($fila, $referencia, 'Sin cambios aplicables (ningún precio nuevo y producto ya existía)');
                    $fallo++;
                }
            } catch (\Throwable $e) {
                Log::error('Error procesando fila import productos', [
                    'fila' => $fila, 'ref' => $referencia, 'msg' => $e->getMessage()
                ]);
                $this->actualizacion->agregarError($fila, $referencia, $e->getMessage());
                $fallo++;
            }

            $fila++;
        }

        $this->actualizacion->update([
            'total_filas' => $total,
            'actualizaciones_exitosas' => $exito,
            'actualizaciones_fallidas' => $fallo,
            'estado' => 'completado',
        ]);
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

    private function upsertProducto(string $referencia, array $r): Producto
    {
        $producto = Producto::where('referencia', $referencia)->first();

        // Solo crear si trae al menos nombre + categoría
        if (!$producto) {
            $nombre = $r['nombre'] ?? null;
            $categoriaNombre = $r['categoria'] ?? null;
            if (!$nombre || !$categoriaNombre) {
                throw new \RuntimeException("Producto nuevo requiere 'nombre' y 'categoria'.");
            }
            $categoria = Categoria::firstOrCreate(['nombre' => $categoriaNombre], ['activo' => true]);

            $producto = Producto::create([
                'referencia'      => $referencia,
                'nombre'          => $nombre,
                'descripcion'     => $r['descripcion'] ?? null,
                'unidad_venta'    => $r['unidadventa'] ?? $r['unidad_venta'] ?? 'UND',
                'unidad_empaque'  => $r['unidadempaque'] ?? $r['unidad_empaque'] ?? 'UND',
                'tiene_extension' => $this->boolValor($r['tieneextension'] ?? $r['tiene_extension'] ?? 0),
                'tiene_variantes' => $this->boolValor($r['tienevariantes'] ?? $r['tiene_variantes'] ?? 0),
                'controlar_stock' => $this->boolValor($r['controlarstock'] ?? $r['controlar_stock'] ?? 1),
                'categoria_id'    => $categoria->id,
                'activo'          => true,
            ]);
            return $producto;
        }

        // Actualizar campos si vienen en la fila
        $cambios = [];
        foreach (['nombre', 'descripcion'] as $f) {
            if (!empty($r[$f])) $cambios[$f] = $r[$f];
        }
        if (!empty($r['unidadventa'] ?? $r['unidad_venta'] ?? null)) {
            $cambios['unidad_venta'] = $r['unidadventa'] ?? $r['unidad_venta'];
        }
        if (!empty($r['unidadempaque'] ?? $r['unidad_empaque'] ?? null)) {
            $cambios['unidad_empaque'] = $r['unidadempaque'] ?? $r['unidad_empaque'];
        }
        if (isset($r['tieneextension']) || isset($r['tiene_extension'])) {
            $cambios['tiene_extension'] = $this->boolValor($r['tieneextension'] ?? $r['tiene_extension']);
        }
        if (!empty($r['categoria'])) {
            $categoria = Categoria::firstOrCreate(['nombre' => $r['categoria']], ['activo' => true]);
            $cambios['categoria_id'] = $categoria->id;
        }

        if (!empty($cambios)) $producto->update($cambios);

        return $producto;
    }

    private function upsertPrecios(Producto $producto, array $r, int $fila, string $referencia): bool
    {
        $algo = false;
        foreach ($this->mapeoListas as $col => $listaId) {
            $val = $r[$col] ?? null;
            if ($val === null || $val === '' || !is_numeric($val)) continue;
            $precio = (float) $val;
            if ($precio < 0) {
                $this->actualizacion->agregarError($fila, $referencia, "Precio negativo en {$col}: {$precio}");
                continue;
            }

            $anterior = PrecioProducto::where('producto_id', $producto->id)
                ->where('lista_precio_id', $listaId)->value('precio');

            PrecioProducto::updateOrCreate(
                ['producto_id' => $producto->id, 'lista_precio_id' => $listaId],
                ['precio' => $precio, 'activo' => true]
            );

            $listaNombre = ListaPrecio::find($listaId)->nombre ?? $col;
            $this->actualizacion->agregarProcesado($fila, $referencia, $listaNombre, $anterior, $precio);
            $algo = true;
        }
        return $algo;
    }

    private function boolValor($v): bool
    {
        if (is_bool($v)) return $v;
        $s = strtolower(trim((string) $v));
        return in_array($s, ['1', 'si', 'sí', 'yes', 'true', 'x'], true);
    }
}
