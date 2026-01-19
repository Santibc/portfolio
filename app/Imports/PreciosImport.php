<?php

namespace App\Imports;

use App\Models\Producto;
use App\Models\PrecioProducto;
use App\Models\ListaPrecio;
use App\Models\ActualizacionPrecio;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PreciosImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings, WithCalculatedFormulas
{
    protected $actualizacion;
    protected $mapeoListas;

    public function __construct(ActualizacionPrecio $actualizacion)
    {
        $this->actualizacion = $actualizacion;

        // Construir mapeo dinámico desde las listas de precios activas en la BD
        $this->mapeoListas = $this->construirMapeoListas();
    }

    /**
     * Construye el mapeo de columnas a IDs de listas de precios
     * basándose en las listas activas en la base de datos
     */
    protected function construirMapeoListas(): array
    {
        $mapeo = [];

        // Obtener todas las listas de precios activas
        $listas = ListaPrecio::activas()->get();

        foreach ($listas as $lista) {
            // Normalizar el nombre de la lista para usarlo como key
            $keyNormalizada = $this->normalizarKey($lista->nombre);
            $mapeo[$keyNormalizada] = $lista->id;

            // También agregar variaciones comunes
            $mapeo[strtolower($lista->nombre)] = $lista->id;
        }

        // Agregar mapeos de compatibilidad adicionales
        $mapeosCompatibilidad = [
            'costo' => 1,
            'precioventaoro' => 2,
            'precioventainstaladorespecial' => 3,
            'precioventainstalador' => 4,
            'precioventafinal' => 5,
            'export1' => 1,
            'export_1' => 1,
            'export2' => 2,
            'export_2' => 2,
            'local1' => 3,
            'local_1' => 3,
            'local2' => 4,
            'local_2' => 4,
            'local3' => 5,
            'local_3' => 5,
            'local4' => 6,
            'local_4' => 6,
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
        $key = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key) ?: $key;

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
            'delimiter' => ';',  // Cambiar a punto y coma
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
            $exitosas = 0;
            $fallidas = 0;
            $filaActual = 2; // Empezamos en 2 porque la 1 es el encabezado

            Log::info('Iniciando procesamiento de precios', [
                'total_filas' => $totalFilas,
                'actualizacion_id' => $this->actualizacion->id
            ]);

            foreach ($rows as $row) {
                // Convertir row a array y normalizar keys
                $rowArray = $row->toArray();
                $rowNormalized = [];

                foreach ($rowArray as $key => $value) {
                    // Limpiar y normalizar las keys
                    $cleanKey = $this->normalizarKey((string) $key);
                    // Limpiar valores
                    $rowNormalized[$cleanKey] = $this->limpiarValor($value);
                }

                // Verificar si la fila está completamente vacía
                $filaVacia = true;
                foreach ($rowNormalized as $value) {
                    if (!empty($value) && trim((string)$value) !== '') {
                        $filaVacia = false;
                        break;
                    }
                }

                // Saltar filas vacías sin registrarlas como error
                if ($filaVacia) {
                    $filaActual++;
                    continue;
                }

                // Buscar la referencia del producto (prioridad sobre nombre)
                $referencia = trim(
                    $rowNormalized['referencia'] ??
                    $rowNormalized['ref'] ??
                    $rowNormalized['codigo'] ??
                    $rowNormalized['sku'] ??
                    ''
                );

                // Buscar el nombre como alternativa
                $nombre = trim(
                    $rowNormalized['nombre'] ??
                    $rowNormalized['item'] ??
                    $rowNormalized['producto'] ??
                    ''
                );

                // Debe haber al menos referencia o nombre
                if (empty($referencia) && empty($nombre)) {
                    $this->actualizacion->agregarError($filaActual, '', 'Referencia y nombre vacíos - debe proporcionar al menos uno');
                    $fallidas++;
                    $filaActual++;
                    continue;
                }

                // Buscar el producto: primero por referencia, luego por nombre
                $producto = null;
                $identificador = '';

                if (!empty($referencia)) {
                    $producto = Producto::where('referencia', $referencia)
                                       ->where('eliminado', false)
                                       ->first();
                    $identificador = $referencia;
                }

                // Si no se encontró por referencia, buscar por nombre
                if (!$producto && !empty($nombre)) {
                    $producto = Producto::where('nombre', $nombre)
                                       ->where('eliminado', false)
                                       ->first();
                    $identificador = $nombre;
                }

                if (!$producto) {
                    $mensajeError = !empty($referencia)
                        ? "Producto con referencia '{$referencia}' no encontrado"
                        : "Producto con nombre '{$nombre}' no encontrado";
                    $this->actualizacion->agregarError($filaActual, $identificador, $mensajeError);
                    $fallidas++;
                    $filaActual++;
                    continue;
                }

                // Procesar cada lista de precios
                $alMenosUnPrecioActualizado = false;
                
                foreach ($this->mapeoListas as $columna => $listaId) {
                    // Buscar el precio en el array normalizado
                    $precio = $rowNormalized[$columna] ?? null;
                    
                    if ($precio !== null && $precio !== '' && is_numeric($precio)) {
                        $precio = floatval($precio);
                        
                        // Validar que el precio sea positivo
                        if ($precio < 0) {
                            $this->actualizacion->agregarError(
                                $filaActual,
                                $nombre,
                                "Precio negativo no permitido para lista {$columna}: {$precio}"
                            );
                            continue;
                        }

                        // Obtener precio anterior
                        $precioAnterior = PrecioProducto::where('producto_id', $producto->id)
                                                        ->where('lista_precio_id', $listaId)
                                                        ->first();

                        $valorAnterior = $precioAnterior ? $precioAnterior->precio : null;

                        // Actualizar o crear precio
                        PrecioProducto::updateOrCreate(
                            [
                                'producto_id' => $producto->id,
                                'lista_precio_id' => $listaId
                            ],
                            [
                                'precio' => $precio,
                                'activo' => true
                            ]
                        );

                        // Registrar en detalles procesados
                        $listaNombre = ListaPrecio::find($listaId)->nombre ?? $columna;
                        $this->actualizacion->agregarProcesado(
                            $filaActual,
                            $nombre,
                            $listaNombre,
                            $valorAnterior,
                            $precio
                        );

                        $alMenosUnPrecioActualizado = true;

                        Log::info('Precio actualizado', [
                            'producto' => $nombre,
                            'lista' => $listaNombre,
                            'precio_anterior' => $valorAnterior,
                            'precio_nuevo' => $precio
                        ]);
                    }
                }

                if ($alMenosUnPrecioActualizado) {
                    $exitosas++;
                } else {
                    $this->actualizacion->agregarError($filaActual, $nombre, 'No se encontraron precios válidos para actualizar');
                    $fallidas++;
                }
                
                $filaActual++;
            }

            // Actualizar estadísticas
            $this->actualizacion->update([
                'total_filas' => $totalFilas,
                'actualizaciones_exitosas' => $exitosas,
                'actualizaciones_fallidas' => $fallidas,
                'estado' => 'completado'
            ]);

            Log::info('Procesamiento completado', [
                'actualizacion_id' => $this->actualizacion->id,
                'exitosas' => $exitosas,
                'fallidas' => $fallidas
            ]);

        } catch (\Exception $e) {
            Log::error('Error en importación de precios', [
                'actualizacion_id' => $this->actualizacion->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->actualizacion->update([
                'estado' => 'error',
                'errores' => [[
                    'fila' => 0,
                    'nombre' => '',
                    'mensaje' => 'Error general: ' . $e->getMessage()
                ]]
            ]);
            
            throw $e;
        }
    }
}