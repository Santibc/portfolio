<?php

namespace App\Services\Catalogo;

use App\Models\Producto;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

/**
 * Lee un archivo Excel/CSV de productos y los crea o actualiza según la
 * referencia. Si la referencia ya existe se actualizan los campos enviados;
 * si no existe se crea un producto nuevo. Las filas inválidas no detienen el
 * proceso: se acumulan en la lista de errores que se devuelve al final.
 *
 * Columnas esperadas (en orden, con fila de encabezado):
 *   Referencia | Descripción | Color | Composición | Código PA | País origen |
 *   Precio unitario | Unidad medida | Es prenda | Activo
 */
class ProductoImportService
{
    /**
     * @return array{creados: int, actualizados: int, errores: list<array{fila: int, referencia: string, motivo: string}>}
     */
    public function procesar(string $rutaArchivo): array
    {
        $hoja = IOFactory::load($rutaArchivo)->getActiveSheet();
        /** @var array<int, array<int, mixed>> $filas */
        $filas = $hoja->toArray(null, true, false, false);

        // Mapa de productos existentes indexado por referencia normalizada.
        $existentes = Producto::all()
            ->keyBy(fn (Producto $p) => $this->normalizar((string) $p->referencia));

        $creados = 0;
        $actualizados = 0;
        $errores = [];

        foreach ($filas as $indice => $fila) {
            // La primera fila es el encabezado.
            if ($indice === 0) {
                continue;
            }

            $numeroFila = $indice + 1;
            $referencia = trim((string) ($fila[0] ?? ''));

            // Fila totalmente vacía: se ignora en silencio.
            if ($referencia === '' && $this->filaVacia($fila)) {
                continue;
            }

            if ($referencia === '') {
                $errores[] = ['fila' => $numeroFila, 'referencia' => '—', 'motivo' => 'Falta la referencia del producto.'];

                continue;
            }

            $refUpper = mb_strtoupper($referencia);
            if (mb_strlen($refUpper) > 40) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'La referencia supera los 40 caracteres.'];

                continue;
            }

            $producto = $existentes->get($this->normalizar($referencia));
            $esNuevo = $producto === null;

            // Lectura de celdas.
            $descripcion = trim((string) ($fila[1] ?? ''));
            $color = trim((string) ($fila[2] ?? ''));
            $composicion = trim((string) ($fila[3] ?? ''));
            $codigoPa = trim((string) ($fila[4] ?? ''));
            $paisOrigen = trim((string) ($fila[5] ?? ''));
            $precio = $this->numero($fila[6] ?? null);
            $unidad = trim((string) ($fila[7] ?? ''));
            $esPrenda = $this->booleano($fila[8] ?? null);
            $activo = $this->booleano($fila[9] ?? null);

            // Validaciones de campos requeridos al crear.
            if ($esNuevo && $descripcion === '') {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'La descripción es obligatoria para crear un producto.'];

                continue;
            }
            if ($esNuevo && $precio === null) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'El precio unitario es obligatorio para crear un producto.'];

                continue;
            }

            // Validaciones de formato / longitud (aplican si el campo viene informado).
            if ($descripcion !== '' && mb_strlen($descripcion) > 150) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'La descripción supera los 150 caracteres.'];

                continue;
            }
            if ($color !== '' && mb_strlen($color) > 60) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'El color supera los 60 caracteres.'];

                continue;
            }
            if ($composicion !== '' && mb_strlen($composicion) > 255) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'La composición supera los 255 caracteres.'];

                continue;
            }
            if ($codigoPa !== '' && preg_match('/^[0-9\.]+$/', $codigoPa) !== 1) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'El código PA solo admite números y puntos.'];

                continue;
            }
            if ($paisOrigen !== '' && mb_strlen($paisOrigen) > 60) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'El país de origen supera los 60 caracteres.'];

                continue;
            }
            if ($precio !== null && ($precio < 0 || $precio > 9999999999.99)) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'El precio unitario está fuera de rango.'];

                continue;
            }
            if ($unidad !== '' && mb_strlen($unidad) > 20) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'La unidad de medida supera los 20 caracteres.'];

                continue;
            }

            // Solo se asignan los campos que vienen informados, para no borrar
            // datos existentes al actualizar con celdas vacías.
            $data = [];
            if ($descripcion !== '') {
                $data['descripcion'] = $descripcion;
            }
            if ($color !== '') {
                $data['color'] = $color;
            }
            if ($composicion !== '') {
                $data['composicion'] = $composicion;
            }
            if ($codigoPa !== '') {
                $data['codigo_pa'] = $codigoPa;
            }
            if ($paisOrigen !== '') {
                $data['pais_origen'] = $paisOrigen;
            }
            if ($precio !== null) {
                $data['precio_unitario'] = $precio;
            }
            if ($unidad !== '') {
                $data['unidad_medida'] = $unidad;
            }
            if ($esPrenda !== null) {
                $data['es_prenda'] = $esPrenda;
            }
            if ($activo !== null) {
                $data['activo'] = $activo;
            }

            try {
                if ($esNuevo) {
                    $data['referencia'] = $refUpper;
                    $data['unidad_medida'] ??= 'Und';
                    $data['es_prenda'] ??= false;
                    $data['activo'] ??= true;

                    $nuevo = Producto::create($data);
                    // Se registra para detectar duplicados dentro del mismo archivo.
                    $existentes->put($this->normalizar($refUpper), $nuevo);
                    $creados++;
                } else {
                    if ($data !== []) {
                        $producto->update($data);
                    }
                    $actualizados++;
                }
            } catch (Throwable $e) {
                $errores[] = ['fila' => $numeroFila, 'referencia' => $referencia, 'motivo' => 'No se pudo guardar: '.$e->getMessage()];
            }
        }

        return ['creados' => $creados, 'actualizados' => $actualizados, 'errores' => $errores];
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
     * Interpreta un valor de celda como booleano. Devuelve null cuando la celda
     * está vacía, de modo que al actualizar se conserve el valor existente.
     *
     * @param  mixed  $valor
     */
    private function booleano($valor): ?bool
    {
        $texto = mb_strtolower(trim((string) ($valor ?? '')));

        if ($texto === '') {
            return null;
        }

        if (in_array($texto, ['si', 'sí', 's', '1', 'true', 'verdadero', 'x', 'yes'], true)) {
            return true;
        }

        if (in_array($texto, ['no', 'n', '0', 'false', 'falso'], true)) {
            return false;
        }

        return null;
    }
}
