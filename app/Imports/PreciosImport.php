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
        
        // Mapeo de nombres de columnas a IDs de listas de precios
        // Las columnas del Excel son: Nombre, COSTO, PRECIO VENTA ORO, PRECIO VENTA INSTALADOR ESPECIAL, PRECIO VENTA INSTALADOR, PRECIO VENTA FINAL
        // Las listas de precios son: 1=COSTO, 2=PRECIO VENTA ORO, 3=PRECIO VENTA INSTALADOR ESPECIAL, 4=PRECIO VENTA INSTALADOR, 5=PRECIO VENTA FINAL
        $this->mapeoListas = [
            // Nombres completos (como aparecen en el Excel)
            'costo' => 1,
            'precioventaoro' => 2,
            'precioventainstaladorespecial' => 3,
            'precioventainstalador' => 4,
            'precioventafinal' => 5,
            // Compatibilidad con formato anterior (Export1, Export2, Local1-3)
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
        ];
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
                    $cleanKey = strtolower(trim(str_replace([' ', '-', '_'], '', $key)));
                    $rowNormalized[$cleanKey] = $value;
                }
                
                // Buscar el nombre con diferentes posibles nombres de columna
                $nombre = trim(
                    $rowNormalized['nombre'] ??
                    $rowNormalized['item'] ??
                    $rowNormalized['producto'] ??
                    ''
                );

                if (empty($nombre)) {
                    $this->actualizacion->agregarError($filaActual, '', 'Nombre vacío');
                    $fallidas++;
                    $filaActual++;
                    continue;
                }

                // Buscar el producto por nombre (solo productos no eliminados)
                $producto = Producto::where('nombre', $nombre)
                                   ->where('eliminado', false)
                                   ->first();

                if (!$producto) {
                    $this->actualizacion->agregarError($filaActual, $nombre, "Producto con nombre '{$nombre}' no encontrado o está eliminado");
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