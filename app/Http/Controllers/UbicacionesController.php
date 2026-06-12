<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use App\Models\StockProducto;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UbicacionesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Ubicacion::query();

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $btns = '<div class="d-flex gap-1">';
                    $btns .= '<a href="' . route('ubicaciones.form', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>';
                    if (auth()->user()->hasAnyRole(['admin', 'auxiliar_administrativo'])) {
                        $nombreJs = htmlspecialchars(json_encode($row->nombre), ENT_QUOTES);
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-warning" onclick="reiniciarInventario(' . $row->id . ', ' . $nombreJs . ')" title="Reiniciar inventario a 0 (conteo)"><i class="bi bi-arrow-counterclockwise"></i></button>';
                    }
                    if (!$row->es_principal) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarUbicacion(' . $row->id . ')"><i class="bi bi-trash"></i></button>';
                    }
                    $btns .= '</div>';
                    return $btns;
                })
                ->addColumn('tipo_nombre', function ($row) {
                    $colores = [
                        'bodega' => 'primary',
                        'tienda' => 'success',
                        'otro' => 'secondary',
                    ];
                    $color = $colores[$row->tipo] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . $row->tipo_nombre . '</span>';
                })
                ->addColumn('activo', function ($row) {
                    $color = $row->activo ? 'success' : 'secondary';
                    $texto = $row->activo ? 'Activo' : 'Inactivo';
                    return '<span class="badge bg-' . $color . '">' . $texto . '</span>';
                })
                ->addColumn('es_principal', function ($row) {
                    return $row->es_principal ? '<i class="bi bi-star-fill text-warning"></i>' : '';
                })
                ->rawColumns(['action', 'tipo_nombre', 'activo', 'es_principal'])
                ->make(true);
        }

        return view('ubicaciones.index');
    }

    public function form($id = null)
    {
        $ubicacion = $id ? Ubicacion::findOrFail($id) : new Ubicacion();
        $tipos = Ubicacion::tipos();

        return view('ubicaciones.form', compact('ubicacion', 'tipos'));
    }

    public function guardar(Request $request)
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:50|unique:ubicaciones,codigo,' . $request->id,
            'tipo' => 'required|in:bodega,tienda,otro',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'responsable' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ];

        $request->validate($rules);

        $data = $request->only(['nombre', 'codigo', 'tipo', 'direccion', 'telefono', 'responsable']);
        $data['activo'] = $request->has('activo');

        if ($request->id) {
            $ubicacion = Ubicacion::findOrFail($request->id);
            $ubicacion->update($data);
            $mensaje = 'Ubicación actualizada correctamente';
        } else {
            $ubicacion = Ubicacion::create($data);
            $mensaje = 'Ubicación creada correctamente';
        }

        // Si se marca como principal
        if ($request->has('es_principal') && $request->es_principal) {
            $ubicacion->marcarComoPrincipal();
        }

        return redirect()->route('ubicaciones')
            ->with('success', $mensaje);
    }

    public function eliminar($id)
    {
        $ubicacion = Ubicacion::findOrFail($id);

        // Verificar que no es la principal
        if ($ubicacion->es_principal) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la ubicación principal.'
            ], 422);
        }

        // Verificar que no tiene stock asociado
        if ($ubicacion->stockProductos()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la ubicación porque tiene productos en stock.'
            ], 422);
        }

        // Verificar que no tiene traslados pendientes
        $trasladosPendientes = $ubicacion->trasladosOrigen()
            ->whereIn('estado', ['pendiente', 'en_transito'])
            ->count();
        $trasladosPendientes += $ubicacion->trasladosDestino()
            ->whereIn('estado', ['pendiente', 'en_transito'])
            ->count();

        if ($trasladosPendientes > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la ubicación porque tiene traslados pendientes.'
            ], 422);
        }

        $ubicacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ubicación eliminada correctamente.'
        ]);
    }

    public function toggleEstado(Ubicacion $ubicacion)
    {
        // No permitir desactivar la ubicación principal
        if ($ubicacion->es_principal && $ubicacion->activo) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede desactivar la ubicación principal.'
            ], 422);
        }

        $ubicacion->activo = !$ubicacion->activo;
        $ubicacion->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente.',
            'activo' => $ubicacion->activo
        ]);
    }

    public function marcarPrincipal(Ubicacion $ubicacion)
    {
        if (!$ubicacion->activo) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede marcar como principal una ubicación inactiva.'
            ], 422);
        }

        $ubicacion->marcarComoPrincipal();

        return response()->json([
            'success' => true,
            'message' => 'Ubicación marcada como principal correctamente.'
        ]);
    }

    /**
     * Reinicia el inventario de una ubicación: pone la cantidad disponible en 0 para un
     * conteo físico. Conserva las reservas activas. Registra un movimiento de ajuste por
     * cada producto afectado y una entrada de auditoría en el log.
     */
    public function reiniciarInventario(Ubicacion $ubicacion)
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['admin', 'auxiliar_administrativo'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para reiniciar el inventario.'
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Solo los registros con disponible distinto de 0 generan movimiento
            $stocks = StockProducto::where('ubicacion_id', $ubicacion->id)
                ->where('cantidad_disponible', '!=', 0)
                ->lockForUpdate()
                ->get();

            $registros = 0;
            $unidades = 0;

            foreach ($stocks as $stock) {
                $unidades += (int) $stock->cantidad_disponible;
                // ajustar(0) pone disponible en 0, conserva reservada y crea el movimiento
                // de ajuste con su ubicacion_id (queda en el historial de stock).
                $stock->ajustar(0, 'Reinicio de inventario para conteo físico - ' . $ubicacion->nombre);
                $registros++;
            }

            // Registro de auditoría en el log general
            Log::create([
                'id_tabla'    => $ubicacion->id,
                'tabla'       => 'ubicaciones',
                'tipo_log'    => 'reinicio_inventario',
                'detalle'     => "Reinicio de inventario en ubicación '{$ubicacion->nombre}' (#{$ubicacion->id}): "
                                 . "{$registros} registros puestos en 0, {$unidades} unidades retiradas. Reservas conservadas.",
                'valor_viejo' => (string) $unidades,
                'valor_nuevo' => '0',
                'id_usuario'  => $user->id,
                'estado'      => 1,
            ]);

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => "Inventario de '{$ubicacion->nombre}' reiniciado a 0: {$registros} productos ({$unidades} unidades). Ya puedes ingresar el conteo.",
                'registros' => $registros,
                'unidades'  => $unidades,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al reiniciar el inventario: ' . $e->getMessage()
            ], 500);
        }
    }
}
