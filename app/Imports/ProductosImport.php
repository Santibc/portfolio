<?php

namespace App\Imports;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\PrecioProducto;
use App\Models\ImportacionProducto;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductosImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings, WithCalculatedFormulas
{
    protected $importacion;
    protected $mapeoListasPrecios;

    public function __construct(ImportacionProducto $importacion)
    {
        $this->importacion = $importacion;

        // Mapeo de columnas del Excel a IDs de listas de precios
        $this->mapeoListasPrecios = [
            'costo' => 1,
            'precioventaoro' => 2,
            'precioventainstaladorespecial' => 3,
            'precioventainstalador' => 4,
            'precioventafinal' => 5,
        ];
    }

    // Configuración personalizada para CSV
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
        try {
            $totalFilas = $rows->count();
            $creados = 0;
            $fallidos = 0;
            $filaActual = 2; // Empezamos en 2 porque la 1 es el encabezado

            Log::info('Iniciando importación de productos', [
                'total_filas' => $totalFilas,
                'importacion_id' => $this->importacion->id
            ]);

            foreach ($rows as $row) {
                try {
                    // Convertir row a array y normalizar keys
                    $rowArray = $row->toArray();

                    // Verificar si la fila está completamente vacía (ignorar filas vacías)
                    $filaVacia = true;
                    foreach ($rowArray as $value) {
                        if (!empty($value) && trim($value) !== '') {
                            $filaVacia = false;
                            break;
                        }
                    }

                    // Saltar filas vacías sin registrarlas como error
                    if ($filaVacia) {
                        $filaActual++;
                        continue;
                    }

                    $rowNormalized = [];

                    foreach ($rowArray as $key => $value) {
                        // Limpiar y normalizar las keys (sin espacios, sin guiones, sin guiones bajos, lowercase)
                        $cleanKey = strtolower(trim(str_replace([' ', '-', '_'], '', $key)));
                        $rowNormalized[$cleanKey] = $value;
                    }

                    // Obtener los campos del Excel
                    $item = trim($rowNormalized['item'] ?? $rowNormalized['nombre'] ?? '');
                    $descripcion = trim($rowNormalized['descripcion'] ?? '');
                    $categoriaSlug = trim($rowNormalized['categoria'] ?? '');
                    $marca = trim($rowNormalized['marca'] ?? '');

                    // Validar que el item no esté vacío
                    if (empty($item)) {
                        $this->importacion->agregarError($filaActual, '', 'El campo ITEM es obligatorio');
                        $fallidos++;
                        $filaActual++;
                        continue;
                    }

                    // Verificar si ya existe un producto con el mismo nombre (solo productos no eliminados)
                    $productoExistente = Producto::where('nombre', $item)
                                                   ->where('eliminado', false)
                                                   ->first();
                    if ($productoExistente) {
                        $this->importacion->agregarError($filaActual, $item, "Ya existe un producto con este nombre (Ref: {$productoExistente->referencia})");
                        $fallidos++;
                        $filaActual++;
                        continue;
                    }

                    // Buscar categoría por slug
                    $categoria = Categoria::where('slug', $categoriaSlug)
                                         ->where('activo', true)
                                         ->first();

                    if (!$categoria) {
                        $this->importacion->agregarError($filaActual, $item, "Categoría con slug '{$categoriaSlug}' no encontrada");
                        $fallidos++;
                        $filaActual++;
                        continue;
                    }

                    // Generar referencia aleatoria única
                    $referencia = $this->generarReferenciaUnica();

                    // Crear el producto
                    $producto = Producto::create([
                        'referencia' => $referencia,
                        'nombre' => $item,
                        'descripcion' => $descripcion,
                        'marca' => $marca,
                        'unidad_venta' => 'Caja',
                        'unidad_empaque' => 'Caja',
                        'extension' => null,
                        'categoria_id' => $categoria->id,
                        'activo' => true,
                        'tiene_variantes' => false,
                        'controlar_stock' => false,
                        'permitir_venta_sin_stock' => true
                    ]);

                    // Procesar precios
                    $preciosProcesados = 0;
                    foreach ($this->mapeoListasPrecios as $columna => $listaId) {
                        $precio = $rowNormalized[$columna] ?? null;

                        if ($precio !== null && $precio !== '' && is_numeric($precio)) {
                            $precio = floatval($precio);

                            if ($precio >= 0) {
                                PrecioProducto::create([
                                    'producto_id' => $producto->id,
                                    'lista_precio_id' => $listaId,
                                    'precio' => $precio,
                                    'activo' => true
                                ]);
                                $preciosProcesados++;
                            }
                        }
                    }

                    // Registrar en detalles procesados
                    $this->importacion->agregarProcesado(
                        $filaActual,
                        $item,
                        $referencia,
                        $categoria->nombre
                    );

                    $creados++;

                    Log::info('Producto creado', [
                        'producto' => $item,
                        'referencia' => $referencia,
                        'categoria' => $categoria->nombre,
                        'precios_procesados' => $preciosProcesados
                    ]);

                } catch (\Exception $e) {
                    $this->importacion->agregarError(
                        $filaActual,
                        $item ?? 'Desconocido',
                        'Error al procesar: ' . $e->getMessage()
                    );
                    $fallidos++;

                    Log::error('Error procesando fila', [
                        'fila' => $filaActual,
                        'error' => $e->getMessage()
                    ]);
                }

                $filaActual++;
            }

            // Actualizar estadísticas
            // El total de filas procesadas es la suma de creados + fallidos (ignorando filas vacías)
            $totalProcesadas = $creados + $fallidos;

            $this->importacion->update([
                'total_filas' => $totalProcesadas,
                'productos_creados' => $creados,
                'productos_fallidos' => $fallidos,
                'estado' => 'completado'
            ]);

            Log::info('Importación completada', [
                'importacion_id' => $this->importacion->id,
                'creados' => $creados,
                'fallidos' => $fallidos
            ]);

        } catch (\Exception $e) {
            Log::error('Error en importación de productos', [
                'importacion_id' => $this->importacion->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->importacion->update([
                'estado' => 'error',
                'errores' => [[
                    'fila' => 0,
                    'item' => '',
                    'mensaje' => 'Error general: ' . $e->getMessage()
                ]]
            ]);

            throw $e;
        }
    }

    /**
     * Genera una referencia única aleatoria para el producto
     */
    private function generarReferenciaUnica()
    {
        do {
            // Generar referencia con formato: PROD-XXXXXXXX (8 caracteres alfanuméricos)
            $referencia = 'PROD-' . strtoupper(Str::random(8));

            // Verificar que no exista
            $existe = Producto::where('referencia', $referencia)->exists();

        } while ($existe);

        return $referencia;
    }
}
