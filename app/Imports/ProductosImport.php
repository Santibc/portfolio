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

        // Construir mapeo dinámico desde las listas de precios activas en la BD
        $this->mapeoListasPrecios = $this->construirMapeoListas();
    }

    /**
     * Construye el mapeo de columnas a IDs de listas de precios
     * basándose en las listas activas en la base de datos
     */
    protected function construirMapeoListas(): array
    {
        $mapeo = [];

        // Obtener todas las listas de precios activas
        $listas = \App\Models\ListaPrecio::activas()->get();

        foreach ($listas as $lista) {
            // Normalizar el nombre de la lista para usarlo como key
            $keyNormalizada = $this->normalizarKey($lista->nombre);
            $mapeo[$keyNormalizada] = $lista->id;

            // También agregar por código
            if ($lista->codigo) {
                $mapeo[$this->normalizarKey($lista->codigo)] = $lista->id;
            }
        }

        // Agregar mapeos de compatibilidad adicionales
        $mapeosCompatibilidad = [
            'costo' => 1,
            'precioventaoro' => 2,
            'precioventainstaladorespecial' => 3,
            'precioventainstalador' => 4,
            'precioventafinal' => 5,
            'export1' => 1,
            'export2' => 2,
            'local1' => 3,
            'local2' => 4,
            'local3' => 5,
            'local4' => 6,
        ];

        return array_merge($mapeosCompatibilidad, $mapeo);
    }

    /**
     * Normaliza una key removiendo espacios, guiones, caracteres especiales y acentos
     */
    protected function normalizarKey(string $key): string
    {
        // Remover BOM y caracteres invisibles
        $key = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/', '', $key);

        // Convertir a minúsculas
        $key = mb_strtolower(trim($key), 'UTF-8');

        // Remover acentos y caracteres especiales
        $key = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key);

        // Remover espacios, guiones, guiones bajos y puntos
        $key = str_replace([' ', '-', '_', '.'], '', $key);

        return $key;
    }

    /**
     * Limpia un valor de texto de caracteres no deseados
     */
    protected function limpiarValor($value): string
    {
        if ($value === null) {
            return '';
        }

        // Convertir a string
        $value = (string) $value;

        // Remover BOM y caracteres invisibles
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        return trim($value);
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
                        // Usar el método de normalización consistente para keys
                        $cleanKey = $this->normalizarKey((string) $key);
                        // Limpiar valores de caracteres no deseados
                        $rowNormalized[$cleanKey] = $this->limpiarValor($value);
                    }

                    // Obtener los campos del Excel (soportar múltiples nombres de columna)
                    $item = $rowNormalized['item'] ?? $rowNormalized['nombre'] ?? $rowNormalized['producto'] ?? '';
                    $descripcion = $rowNormalized['descripcion'] ?? $rowNormalized['desc'] ?? '';
                    $categoriaSlug = $rowNormalized['categoria'] ?? $rowNormalized['cat'] ?? '';
                    $marca = $rowNormalized['marca'] ?? $rowNormalized['brand'] ?? '';

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

                    // Buscar categoría por slug o por nombre
                    $categoria = Categoria::where(function($query) use ($categoriaSlug) {
                        $query->where('slug', $categoriaSlug)
                              ->orWhere('nombre', $categoriaSlug)
                              ->orWhere('nombre', 'LIKE', $categoriaSlug);
                    })
                    ->where('activo', true)
                    ->first();

                    if (!$categoria) {
                        // Listar categorías disponibles para ayudar al usuario
                        $categoriasDisponibles = Categoria::activas()->pluck('slug')->take(5)->implode(', ');
                        $this->importacion->agregarError(
                            $filaActual,
                            $item,
                            "Categoría '{$categoriaSlug}' no encontrada. Categorías disponibles: {$categoriasDisponibles}"
                        );
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
