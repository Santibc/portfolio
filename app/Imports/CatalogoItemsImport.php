<?php

namespace App\Imports;

use App\Models\CatalogoItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class CatalogoItemsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected int $creados = 0;
    protected int $actualizados = 0;
    protected int $errores = 0;
    protected array $detalleLog = [];

    protected const CATEGORIA_MAP = [
        'servicio' => 'servicio',
        'material' => 'material',
        'producto_terminado' => 'producto_terminado',
        'producto terminado' => 'producto_terminado',
    ];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $fila = $index + 2; // Row 1 is the header

            try {
                // Skip completely empty rows
                $valores = array_values(array_filter($row->toArray(), fn($v) => $v !== null && trim((string) $v) !== ''));
                if (empty($valores)) {
                    continue;
                }

                $codigo = trim($row['codigo'] ?? '');
                $descripcion = trim($row['descripcion'] ?? '');
                $categoria = $this->normalizarCategoria($row['categoria'] ?? '');
                $precioUnitario = $this->normalizarPrecio($row['precio_unitario'] ?? '');
                $porcentajeIva = $this->normalizarPrecio($row['porcentaje_iva'] ?? '');

                // Validate
                $validator = Validator::make([
                    'codigo' => $codigo,
                    'descripcion' => $descripcion,
                    'precio_unitario' => $precioUnitario,
                    'porcentaje_iva' => $porcentajeIva,
                    'categoria' => $categoria,
                ], [
                    'codigo' => 'required|string|max:50',
                    'descripcion' => 'required|string',
                    'precio_unitario' => 'required|numeric|min:0',
                    'porcentaje_iva' => 'required|numeric|min:0|max:100',
                    'categoria' => 'required|in:servicio,material,producto_terminado',
                ], [
                    'codigo.required' => 'El codigo es obligatorio.',
                    'codigo.max' => 'El codigo no puede exceder 50 caracteres.',
                    'descripcion.required' => 'La descripcion es obligatoria.',
                    'precio_unitario.required' => 'El precio unitario es obligatorio.',
                    'precio_unitario.numeric' => 'El precio unitario debe ser un numero.',
                    'precio_unitario.min' => 'El precio unitario no puede ser negativo.',
                    'porcentaje_iva.required' => 'El porcentaje de IVA es obligatorio.',
                    'porcentaje_iva.numeric' => 'El porcentaje de IVA debe ser un numero.',
                    'porcentaje_iva.min' => 'El porcentaje de IVA no puede ser negativo.',
                    'porcentaje_iva.max' => 'El porcentaje de IVA no puede exceder 100.',
                    'categoria.required' => 'La categoria es obligatoria.',
                    'categoria.in' => 'Categoria no valida. Use: servicio, material, producto_terminado.',
                ]);

                if ($validator->fails()) {
                    $this->errores++;
                    $this->detalleLog[] = [
                        'fila' => $fila,
                        'codigo' => $codigo ?: '(vacio)',
                        'accion' => 'error',
                        'mensaje' => implode(' ', $validator->errors()->all()),
                        'datos' => $row->toArray(),
                    ];
                    continue;
                }

                $datos = [
                    'descripcion' => $descripcion,
                    'precio_unitario' => $precioUnitario,
                    'porcentaje_iva' => $porcentajeIva,
                    'categoria' => $categoria,
                ];

                // Check if item exists by codigo
                $existing = CatalogoItem::where('codigo', $codigo)->first();

                if ($existing) {
                    $existing->update($datos);
                    $this->actualizados++;
                    $this->detalleLog[] = [
                        'fila' => $fila,
                        'codigo' => $codigo,
                        'accion' => 'actualizado',
                        'mensaje' => 'Item actualizado exitosamente.',
                        'datos' => $datos,
                    ];
                } else {
                    CatalogoItem::create(array_merge($datos, [
                        'codigo' => $codigo,
                        'activo' => true,
                    ]));
                    $this->creados++;
                    $this->detalleLog[] = [
                        'fila' => $fila,
                        'codigo' => $codigo,
                        'accion' => 'creado',
                        'mensaje' => 'Item creado exitosamente.',
                        'datos' => $datos,
                    ];
                }
            } catch (\Exception $e) {
                $this->errores++;
                $this->detalleLog[] = [
                    'fila' => $fila,
                    'codigo' => trim($row['codigo'] ?? '(vacio)'),
                    'accion' => 'error',
                    'mensaje' => 'Error inesperado: ' . $e->getMessage(),
                    'datos' => $row->toArray(),
                ];
            }
        }
    }

    protected function normalizarCategoria(?string $valor): ?string
    {
        if (!$valor) {
            return null;
        }

        $normalizado = strtolower(trim($valor));

        return self::CATEGORIA_MAP[$normalizado] ?? $normalizado;
    }

    protected function normalizarPrecio($valor)
    {
        if (is_numeric($valor)) {
            return $valor;
        }

        if (!is_string($valor)) {
            return $valor;
        }

        // Remove $ sign and spaces
        $valor = str_replace(['$', ' '], '', trim($valor));

        // If has comma as decimal separator (e.g. 1.500,50), normalize
        if (preg_match('/\d+\.\d{3}(,\d+)?$/', $valor)) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } elseif (str_contains($valor, ',')) {
            $valor = str_replace(',', '.', $valor);
        }

        return is_numeric($valor) ? (float) $valor : $valor;
    }

    public function getCreados(): int
    {
        return $this->creados;
    }

    public function getActualizados(): int
    {
        return $this->actualizados;
    }

    public function getErrores(): int
    {
        return $this->errores;
    }

    public function getDetalleLog(): array
    {
        return $this->detalleLog;
    }

    public function getTotalFilas(): int
    {
        return $this->creados + $this->actualizados + $this->errores;
    }
}
