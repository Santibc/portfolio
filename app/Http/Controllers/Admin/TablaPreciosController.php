<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionSistema;
use App\Models\TablaPrecioServicio;
use App\Traits\RegistraActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TablaPreciosExport;
use App\Imports\TablaPreciosImport;

class TablaPreciosController extends Controller
{
    use RegistraActividad;

    /**
     * Vista principal + AJAX para cargar grid de precios.
     */
    public function index(Request $request)
    {
        if ($request->ajax() && $request->filled('tipo_servicio')) {
            return $this->cargarGrid($request);
        }

        $servicios = TablaPrecioServicio::getDistinctServicios();
        $largoRangos = TablaPrecioServicio::getDistinctLargoRangos();
        $totalServicios = $servicios->count();
        $totalRegistros = TablaPrecioServicio::count();
        $ultimaActualizacion = TablaPrecioServicio::max('updated_at');

        return view('admin.tabla-precios.index', compact(
            'servicios', 'largoRangos', 'totalServicios', 'totalRegistros', 'ultimaActualizacion'
        ));
    }

    /**
     * Carga grid de precios (AJAX).
     */
    private function cargarGrid(Request $request)
    {
        $tipoServicio = $request->tipo_servicio;
        $largoMin = $request->largo_min;
        $largoMax = $request->largo_max;

        $precios = TablaPrecioServicio::forServicio($tipoServicio)
            ->forLargoRange((int)$largoMin, $largoMax === '' || $largoMax === null ? null : (int)$largoMax)
            ->orderBy('calibre_mm')
            ->orderBy('cantidad_rango_min')
            ->get();

        $calibres = TablaPrecioServicio::getDistinctCalibres();
        $cantidadRangos = TablaPrecioServicio::getDistinctCantidadRangos();

        $servicio = $precios->first();

        return response()->json([
            'calibres' => $calibres,
            'cantidad_rangos' => $cantidadRangos,
            'precios' => $precios,
            'servicio_etiqueta' => $servicio?->etiqueta_servicio ?? '',
            'precio_minimo' => $servicio?->precio_minimo ?? 0,
        ]);
    }

    /**
     * Actualizar precios masivamente (AJAX).
     */
    public function updatePrecios(Request $request)
    {
        $request->validate([
            'precios' => 'required|array|min:1',
            'precios.*.id' => 'required|integer|exists:tabla_precios_servicios,id',
            'precios.*.precio' => 'required|numeric|min:0',
        ]);

        $cambios = [];

        foreach ($request->precios as $item) {
            $registro = TablaPrecioServicio::find($item['id']);
            $precioAnterior = $registro->precio;
            $precioNuevo = $item['precio'];

            if ((float)$precioAnterior !== (float)$precioNuevo) {
                $registro->update(['precio' => $precioNuevo]);
                $cambios[] = [
                    'calibre' => $registro->clave_calibre,
                    'cantidad_rango' => $registro->cantidad_rango_min . '-' . ($registro->cantidad_rango_max ?? '∞'),
                    'anterior' => $precioAnterior,
                    'nuevo' => $precioNuevo,
                ];
            }
        }

        if (count($cambios) > 0) {
            $servicio = TablaPrecioServicio::find($request->precios[0]['id']);
            $this->registrarActividad(
                'tabla_precios.precios_actualizados',
                'Actualizados ' . count($cambios) . ' precios de ' . ($servicio->etiqueta_servicio ?? ''),
                null,
                ['cambios' => $cambios]
            );
        }

        return response()->json([
            'success' => true,
            'message' => count($cambios) . ' precio(s) actualizado(s).',
        ]);
    }

    /**
     * Lista de tipos de servicio (AJAX).
     */
    public function servicios()
    {
        $servicios = TablaPrecioServicio::getDistinctServicios();
        $servicios->each(function ($s) {
            $s->total_registros = TablaPrecioServicio::forServicio($s->tipo_servicio)->count();
        });

        return response()->json($servicios);
    }

    /**
     * Crear nuevo tipo de servicio con 312 registros.
     */
    public function storeServicio(Request $request)
    {
        $request->validate([
            'tipo_servicio' => 'required|string|max:100',
            'etiqueta_servicio' => 'required|string|max:255',
            'precio_minimo' => 'required|numeric|min:0',
        ]);

        $clave = Str::slug($request->tipo_servicio, '_');

        // Verificar unicidad
        if (TablaPrecioServicio::where('tipo_servicio', $clave)->exists()) {
            return response()->json(['message' => 'Ya existe un servicio con esa clave.'], 422);
        }

        $calibres = ConfiguracionSistema::get('calibres_disponibles', []);
        $largos = [
            ['min' => 0, 'max' => 50],
            ['min' => 51, 'max' => 100],
            ['min' => 101, 'max' => 200],
            ['min' => 201, 'max' => null],
        ];
        $cantidades = [
            ['min' => 1, 'max' => 10],
            ['min' => 11, 'max' => 50],
            ['min' => 51, 'max' => 100],
            ['min' => 101, 'max' => 500],
            ['min' => 501, 'max' => 1000],
            ['min' => 1001, 'max' => null],
        ];

        $records = [];
        $now = now();

        foreach ($calibres as $calibre) {
            foreach ($largos as $largo) {
                foreach ($cantidades as $cantidad) {
                    $records[] = [
                        'tipo_servicio' => $clave,
                        'etiqueta_servicio' => $request->etiqueta_servicio,
                        'clave_calibre' => $calibre['clave'],
                        'calibre_mm' => $calibre['mm'],
                        'largo_rango_min' => $largo['min'],
                        'largo_rango_max' => $largo['max'],
                        'cantidad_rango_min' => $cantidad['min'],
                        'cantidad_rango_max' => $cantidad['max'],
                        'precio' => $request->precio_minimo,
                        'precio_minimo' => $request->precio_minimo,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        foreach (array_chunk($records, 500) as $chunk) {
            TablaPrecioServicio::insert($chunk);
        }

        $this->registrarActividad(
            'tabla_precios.servicio_creado',
            'Creado tipo de servicio: ' . $request->etiqueta_servicio . ' (' . count($records) . ' registros)',
            null,
            ['clave' => $clave, 'etiqueta' => $request->etiqueta_servicio, 'precio_minimo' => $request->precio_minimo]
        );

        return response()->json([
            'success' => true,
            'message' => 'Servicio creado con ' . count($records) . ' registros de precios.',
        ]);
    }

    /**
     * Actualizar tipo de servicio.
     */
    public function updateServicio(Request $request, string $tipo_servicio)
    {
        $request->validate([
            'etiqueta_servicio' => 'required|string|max:255',
            'precio_minimo' => 'required|numeric|min:0',
        ]);

        $actualizados = TablaPrecioServicio::forServicio($tipo_servicio)->update([
            'etiqueta_servicio' => $request->etiqueta_servicio,
            'precio_minimo' => $request->precio_minimo,
        ]);

        if ($actualizados === 0) {
            return response()->json(['message' => 'Servicio no encontrado.'], 404);
        }

        $this->registrarActividad(
            'tabla_precios.servicio_actualizado',
            'Actualizado servicio: ' . $request->etiqueta_servicio,
            null,
            ['clave' => $tipo_servicio, 'etiqueta' => $request->etiqueta_servicio, 'precio_minimo' => $request->precio_minimo]
        );

        return response()->json(['success' => true, 'message' => 'Servicio actualizado.']);
    }

    /**
     * Eliminar tipo de servicio y todos sus registros.
     */
    public function destroyServicio(string $tipo_servicio)
    {
        $servicio = TablaPrecioServicio::forServicio($tipo_servicio)->first();

        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado.'], 404);
        }

        $etiqueta = $servicio->etiqueta_servicio;
        $eliminados = TablaPrecioServicio::forServicio($tipo_servicio)->delete();

        $this->registrarActividad(
            'tabla_precios.servicio_eliminado',
            'Eliminado servicio: ' . $etiqueta . ' (' . $eliminados . ' registros)',
            null,
            ['clave' => $tipo_servicio, 'etiqueta' => $etiqueta, 'registros_eliminados' => $eliminados]
        );

        return response()->json(['success' => true, 'message' => 'Servicio eliminado (' . $eliminados . ' registros).']);
    }

    /**
     * Exportar precios a Excel.
     */
    public function exportExcel(Request $request)
    {
        $tipoServicio = $request->tipo_servicio;
        $nombre = 'tabla-precios' . ($tipoServicio ? '-' . $tipoServicio : '') . '.xlsx';

        return Excel::download(new TablaPreciosExport($tipoServicio), $nombre);
    }

    /**
     * Importar precios desde Excel.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $import = new TablaPreciosImport();
        Excel::import($import, $request->file('archivo'));

        $actualizados = $import->getActualizados();

        $this->registrarActividad(
            'tabla_precios.importacion',
            'Importacion de precios: ' . $actualizados . ' registros actualizados',
            null,
            ['registros_actualizados' => $actualizados]
        );

        return response()->json([
            'success' => true,
            'message' => $actualizados . ' registro(s) de precios actualizados.',
        ]);
    }
}
