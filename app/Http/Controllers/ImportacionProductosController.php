<?php

namespace App\Http\Controllers;

use App\Models\ImportacionProducto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductosImport;
use App\Exports\PlantillaProductosExport;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportacionProductosController extends Controller
{
    public function historial(Request $request)
    {
        if ($request->ajax()) {
            $query = ImportacionProducto::with('usuario')
                ->select('importaciones_productos.*')
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addColumn('usuario', fn($i) => $i->usuario->name)
                ->addColumn('fecha', fn($i) => $i->created_at->format('d/m/Y H:i'))
                ->addColumn('estado_badge', function($i) {
                    $badges = [
                        'procesando' => 'warning',
                        'completado' => 'success',
                        'error' => 'danger'
                    ];
                    $badge = $badges[$i->estado] ?? 'secondary';
                    return '<span class="badge bg-'.$badge.'">'.$i->estado.'</span>';
                })
                ->addColumn('resultados', function($i) {
                    return "Creados: {$i->productos_creados} / Fallidos: {$i->productos_fallidos}";
                })
                ->addColumn('action', function($i) {
                    $buttons = '<div class="btn-group">';
                    $buttons .= '<button onclick="verDetalles('.$i->id.')" class="btn btn-sm btn-info" title="Ver detalles">
                                   <i class="bi bi-eye"></i>
                                 </button>';
                    if ($i->ruta_archivo && file_exists(public_path($i->ruta_archivo))) {
                        $buttons .= '<a href="'.asset($i->ruta_archivo).'" download class="btn btn-sm btn-secondary" title="Descargar archivo">
                                       <i class="bi bi-download"></i>
                                     </a>';
                    }
                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }

        return view('productos.historial_importaciones');
    }

    public function verDetalle($id)
    {
        $importacion = ImportacionProducto::with('usuario')->findOrFail($id);

        return response()->json([
            'importacion' => $importacion,
            'errores' => $importacion->errores ?? [],
            'procesados' => $importacion->detalles_procesados ?? []
        ]);
    }

    public function importarProductos(Request $request)
    {
        // 1. Validación
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240'
        ], [
            'archivo.required' => 'Debe seleccionar un archivo',
            'archivo.mimes'    => 'El archivo debe ser Excel (.xlsx, .xls) o CSV',
            'archivo.max'      => 'El archivo no debe superar los 10MB'
        ]);

        DB::beginTransaction();

        try {
            // 2. Obtener archivo y nombres
            $archivo        = $request->file('archivo');
            $nombreOriginal = $archivo->getClientOriginalName();
            $nombreArchivo  = time() . '_' . $nombreOriginal;

            // 3. Directorio en public
            $rutaPublic = public_path('uploads/importaciones_productos');
            if (!File::exists($rutaPublic)) {
                File::makeDirectory($rutaPublic, 0755, true);
            }

            // 4. Mover archivo y construir ruta relativa
            $archivo->move($rutaPublic, $nombreArchivo);
            $rutaArchivo = 'uploads/importaciones_productos/' . $nombreArchivo;

            // 5. Registrar en base de datos
            $importacion = ImportacionProducto::create([
                'usuario_id'          => auth()->id(),
                'estado'              => 'procesando',
                'nombre_archivo'      => $nombreOriginal,
                'ruta_archivo'        => $rutaArchivo,
                'total_filas'         => 0,
                'productos_creados'   => 0,
                'productos_fallidos'  => 0,
                'errores'             => [],
                'detalles_procesados' => []
            ]);

            // 6. Log de inicio
            Log::info('Iniciando importación de productos', [
                'usuario'        => auth()->user()->name,
                'archivo'        => $nombreArchivo,
                'importacion_id' => $importacion->id,
            ]);

            // 7. Importar usando la ruta en public
            $pathImport = public_path($rutaArchivo);
            Excel::import(new ProductosImport($importacion), $pathImport);

            DB::commit();

            // 8. Preparar mensaje de resultado
            $importacion->refresh();
            if ($importacion->productos_fallidos === 0 && $importacion->productos_creados > 0) {
                return back()->with('success', "Importación completada: {$importacion->productos_creados} productos creados.");
            } elseif ($importacion->productos_creados > 0) {
                return back()->with('warning', "Importación parcial: {$importacion->productos_creados} creados, {$importacion->productos_fallidos} con errores.");
            } else {
                return back()->with('error', 'No se pudo crear ningún producto. Revisa el reporte de errores.');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en importación de productos', [
                'mensaje' => $e->getMessage()
            ]);
            return back()->with('error', 'Ocurrió un error procesando el archivo: ' . $e->getMessage());
        }
    }

    // Descargar plantilla CSV con punto y coma
    public function descargarPlantillaCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_productos.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Agregar BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados con punto y coma
            fputcsv($file, [
                'ITEM',
                'DESCRIPCION',
                'CATEGORIA',
                'MARCA',
                'COSTO',
                'PRECIO VENTA ORO',
                'PRECIO VENTA INSTALADOR ESPECIAL',
                'PRECIO VENTA INSTALADOR',
                'PRECIO VENTA FINAL'
            ], ';');

            // Obtener categorías para ejemplos
            $categorias = Categoria::activas()->limit(3)->get();

            if ($categorias->count() > 0) {
                foreach ($categorias as $index => $categoria) {
                    fputcsv($file, [
                        'Producto Ejemplo ' . ($index + 1),
                        'Descripción del producto ' . ($index + 1),
                        $categoria->slug,
                        'Marca Ejemplo',
                        '100.00',
                        '150.00',
                        '140.00',
                        '145.00',
                        '160.00'
                    ], ';');
                }
            } else {
                // Ejemplos genéricos si no hay categorías
                fputcsv($file, [
                    'Producto 1',
                    'Descripción del producto 1',
                    'categoria-slug',
                    'Marca A',
                    '100.00',
                    '150.00',
                    '140.00',
                    '145.00',
                    '160.00'
                ], ';');
                fputcsv($file, [
                    'Producto 2',
                    'Descripción del producto 2',
                    'categoria-slug',
                    'Marca B',
                    '200.00',
                    '300.00',
                    '280.00',
                    '290.00',
                    '320.00'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Descargar plantilla Excel
    public function descargarPlantillaExcel()
    {
        return Excel::download(new PlantillaProductosExport, 'plantilla_productos.xlsx');
    }

    public function descargarArchivoImportacion($id)
    {
        $importacion = ImportacionProducto::findOrFail($id);

        if (!$importacion->ruta_archivo) {
            return back()->with('error', 'No hay archivo asociado a esta importación.');
        }

        $fullPath = public_path($importacion->ruta_archivo);
        if (!file_exists($fullPath)) {
            return back()->with('error', 'Archivo no encontrado en el servidor.');
        }

        return response()->download($fullPath, $importacion->nombre_archivo);
    }
}
