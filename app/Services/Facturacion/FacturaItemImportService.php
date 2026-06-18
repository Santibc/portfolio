<?php

namespace App\Services\Facturacion;

use App\Models\Producto;
use App\Models\Talla;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Lee un archivo Excel/CSV de líneas de factura y lo convierte en ítems listos
 * para el formulario. Solo acepta referencias de productos existentes y activos;
 * el resto de campos editables (cantidad, precio, descuento, IVA) se toman del
 * archivo con valores por defecto sensatos.
 *
 * Las columnas se identifican por ENCABEZADO (no por posición), porque hay una
 * columna por cada talla activa del catálogo. Encabezados esperados:
 *   Referencia | <Talla1> | <Talla2> | … | Cantidad | Precio unitario | Descuento | Tipo descuento | IVA %
 *
 * Para prendas, la cantidad de la línea es la suma de las cantidades por talla;
 * para productos sin tallas se usa la columna "Cantidad".
 */
class FacturaItemImportService
{
    /**
     * @return array{items: list<array<string, mixed>>, errores: list<array{fila: int, referencia: string, motivo: string}>}
     */
    public function procesar(string $rutaArchivo, float $ivaPorDefecto): array
    {
        $hoja = IOFactory::load($rutaArchivo)->getActiveSheet();
        /** @var array<int, array<int, mixed>> $filas */
        $filas = $hoja->toArray(null, true, false, false);

        if ($filas === []) {
            return ['items' => [], 'errores' => []];
        }

        // Catálogo de tallas activas indexado por nombre normalizado → nombre canónico.
        $tallasCanonicas = Talla::activas()->orderBy('orden')->orderBy('nombre')
            ->pluck('nombre')
            ->mapWithKeys(fn ($n) => [$this->normalizar((string) $n) => (string) $n])
            ->all();

        // Mapeo de columnas a partir de la fila de encabezado (índice 0).
        $colReferencia = $colCantidad = $colPrecio = $colDescuento = $colDescuentoTipo = $colIva = null;
        /** @var array<int, string> $colsTalla  índice de columna → nombre canónico de talla */
        $colsTalla = [];

        foreach ($filas[0] as $idx => $titulo) {
            $h = $this->normalizar((string) ($titulo ?? ''));
            if ($h === '') {
                continue;
            }
            if (isset($tallasCanonicas[$h])) {
                $colsTalla[$idx] = $tallasCanonicas[$h];
            } elseif (str_contains($h, 'REFERENCIA')) {
                $colReferencia = $idx;
            } elseif (str_contains($h, 'TIPO') && str_contains($h, 'DESCUENTO')) {
                $colDescuentoTipo = $idx;
            } elseif (str_contains($h, 'DESCUENTO')) {
                $colDescuento = $idx;
            } elseif (str_contains($h, 'PRECIO')) {
                $colPrecio = $idx;
            } elseif (str_contains($h, 'CANTIDAD')) {
                $colCantidad = $idx;
            } elseif (str_contains($h, 'IVA')) {
                $colIva = $idx;
            }
        }

        // Mapa de productos activos indexado por referencia normalizada.
        $porReferencia = Producto::where('activo', true)->get()
            ->keyBy(fn (Producto $p) => $this->normalizar((string) $p->referencia));

        $items = [];
        $errores = [];

        foreach ($filas as $indice => $fila) {
            // La primera fila es el encabezado.
            if ($indice === 0) {
                continue;
            }

            $numeroFila = $indice + 1;
            $referencia = trim((string) ($colReferencia !== null ? ($fila[$colReferencia] ?? '') : ''));

            // Fila totalmente vacía: se ignora en silencio.
            if ($referencia === '' && $this->filaVacia($fila)) {
                continue;
            }

            if ($referencia === '') {
                $errores[] = ['fila' => $numeroFila, 'referencia' => '—', 'motivo' => 'Falta la referencia del producto.'];

                continue;
            }

            $producto = $porReferencia->get($this->normalizar($referencia));
            if ($producto === null) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'No existe un producto activo con esa referencia.'];

                continue;
            }

            // Tallas: mapa {nombre: cantidad} con solo las que tienen cantidad > 0.
            $tallas = [];
            foreach ($colsTalla as $idx => $nombre) {
                $cant = $this->numero($fila[$idx] ?? null);
                if ($cant !== null && $cant > 0) {
                    $tallas[$nombre] = $cant;
                }
            }

            // Cantidad de la línea: suma de tallas si hay; si no, columna "Cantidad".
            $cantidad = $tallas !== []
                ? (float) array_sum($tallas)
                : ($this->numero($colCantidad !== null ? ($fila[$colCantidad] ?? null) : null) ?? 1.0);

            if ($cantidad <= 0) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'La cantidad debe ser mayor que 0.'];

                continue;
            }

            $precio = $this->numero($colPrecio !== null ? ($fila[$colPrecio] ?? null) : null) ?? (float) $producto->precio_unitario;
            $descuento = $this->numero($colDescuento !== null ? ($fila[$colDescuento] ?? null) : null) ?? 0.0;
            $descuentoTipo = $this->tipoDescuento($colDescuentoTipo !== null ? ($fila[$colDescuentoTipo] ?? null) : null);
            $iva = $this->numero($colIva !== null ? ($fila[$colIva] ?? null) : null) ?? $ivaPorDefecto;

            $items[] = [
                'producto_id' => $producto->id,
                'referencia' => (string) $producto->referencia,
                'descripcion' => (string) $producto->descripcion,
                'color' => (string) $producto->color,
                'composicion' => (string) $producto->composicion,
                'codigo_pa' => (string) $producto->codigo_pa,
                'cantidad' => $cantidad,
                'precio_unitario' => max($precio, 0.0),
                'descuento' => max($descuento, 0.0),
                'descuento_tipo' => $descuentoTipo,
                'impuesto_porcentaje' => max($iva, 0.0),
                'tallas' => (object) $tallas,
            ];
        }

        return ['items' => $items, 'errores' => $errores];
    }

    private function normalizar(string $referencia): string
    {
        return mb_strtoupper(trim($referencia));
    }

    /**
     * @param  array<int, mixed>  $fila
     */
    private function filaVacia(array $fila): bool
    {
        foreach ($fila as $celda) {
            if (trim((string) ($celda ?? '')) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Convierte un valor de celda a float aceptando formato es-CO ("1.234,56").
     *
     * @param  mixed  $valor
     */
    private function numero($valor): ?float
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        if (is_numeric($valor)) {
            return (float) $valor;
        }

        $texto = trim((string) $valor);
        if ($texto === '') {
            return null;
        }

        if (str_contains($texto, '.') && str_contains($texto, ',')) {
            // 1.234,56 → 1234.56
            $texto = str_replace(['.', ','], ['', '.'], $texto);
        } elseif (str_contains($texto, ',')) {
            $texto = str_replace(',', '.', $texto);
        }

        return is_numeric($texto) ? (float) $texto : null;
    }

    /**
     * @param  mixed  $valor
     */
    private function tipoDescuento($valor): string
    {
        $texto = mb_strtolower(trim((string) ($valor ?? '')));

        return in_array($texto, ['porcentaje', 'porcentual', '%', 'percent', 'pct'], true) ? 'porcentaje' : 'valor';
    }
}
