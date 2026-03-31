<?php

namespace App\Http\Controllers;

use App\Models\NovedadStock;
use App\Models\Ubicacion;
use App\Models\Producto;
use App\Models\Caja;
use App\Models\StockProducto;
use App\Models\MovimientoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NovedadesStockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = NovedadStock::with(['producto', 'varianteProducto', 'ubicacion', 'usuario']);

            // Cajero principal: solo ve novedades de su ubicación
            $user = auth()->user();
            if ($user->hasRole('cajero_principal') && !$user->hasRole('admin')) {
                $caja = Caja::where('cajero_asignado_id', $user->id)->first();
                if ($caja) {
                    $query->where('ubicacion_id', $caja->ubicacion_id);
                }
            }

            if ($request->tipo) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->estado) {
                $query->where('estado', $request->estado);
            }

            if ($request->producto_id) {
                $query->where('producto_id', $request->producto_id);
            }

            if ($request->fecha_desde) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }

            if ($request->fecha_hasta) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $btns = '<div class="d-flex gap-1">';

                    if ($row->puedeCerrar()) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-success" onclick="cerrarNovedad(' . $row->id . ')" title="Cerrar"><i class="bi bi-check-lg"></i></button>';
                    }

                    $btns .= '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="verDetalleNovedad(' . $row->id . ')" title="Ver detalle"><i class="bi bi-eye"></i></button>';
                    $btns .= '</div>';
                    return $btns;
                })
                ->addColumn('producto_nombre', function ($row) {
                    return $row->producto_nombre;
                })
                ->addColumn('tipo_badge', function ($row) {
                    $colores = [
                        'garantia' => 'primary',
                        'saldo' => 'info',
                        'perdida' => 'danger',
                        'dano' => 'warning',
                    ];
                    $color = $colores[$row->tipo] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . $row->tipo_nombre . '</span>';
                })
                ->addColumn('estado_badge', function ($row) {
                    $colores = [
                        'pendiente' => 'warning',
                        'procesado' => 'info',
                        'recuperado' => 'success',
                        'dado_de_baja' => 'secondary',
                    ];
                    $color = $colores[$row->estado] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . $row->estado_nombre . '</span>';
                })
                ->addColumn('valor_formateado', function ($row) {
                    return '$' . number_format($row->valor_original, 2);
                })
                ->addColumn('ubicacion_nombre', function ($row) {
                    return $row->ubicacion->nombre ?? 'N/A';
                })
                ->rawColumns(['action', 'tipo_badge', 'estado_badge'])
                ->make(true);
        }

        $tipos = NovedadStock::tipos();
        $estados = NovedadStock::estados();

        return view('novedades-stock.index', compact('tipos', 'estados'));
    }

    public function form($id = null)
    {
        $novedad = $id ? NovedadStock::findOrFail($id) : new NovedadStock();
        $ubicaciones = Ubicacion::activas()->get();
        $productos = Producto::where('eliminado', false)
            ->where('activo', true)
            ->where('controlar_stock', true)
            ->orderBy('nombre')
            ->get();
        $tipos = NovedadStock::tipos();

        // Cajero: preseleccionar ubicación de su caja
        $ubicacionCajeroId = null;
        $user = auth()->user();
        if ($user->hasRole('cajero_principal') && !$user->hasRole('admin')) {
            $caja = Caja::where('cajero_asignado_id', $user->id)->first();
            if ($caja) {
                $ubicacionCajeroId = $caja->ubicacion_id;
            }
        }

        return view('novedades-stock.form', compact('novedad', 'ubicaciones', 'productos', 'tipos', 'ubicacionCajeroId'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'variante_producto_id' => 'nullable|exists:variantes_productos,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'tipo' => 'required|in:garantia,saldo,perdida,dano',
            'cantidad' => 'required|integer|min:1',
            'valor_original' => 'required|numeric|min:0',
            'valor_saldo' => 'nullable|numeric|min:0',
            'descripcion' => 'required|string|max:1000',
            'numero_garantia' => 'nullable|string|max:100',
            'fecha_vencimiento_garantia' => 'nullable|date|after:today',
        ]);

        // Verificar stock disponible
        $stockActual = StockProducto::where('producto_id', $request->producto_id)
            ->where('ubicacion_id', $request->ubicacion_id);

        if ($request->variante_producto_id) {
            $stockActual->where('variante_producto_id', $request->variante_producto_id);
        } else {
            $stockActual->whereNull('variante_producto_id');
        }

        $stockActual = $stockActual->first();

        if (!$stockActual || $stockActual->stock_real < $request->cantidad) {
            return back()->withErrors(['cantidad' => 'No hay suficiente stock disponible para registrar esta novedad.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Crear la novedad
            $novedad = NovedadStock::create([
                'producto_id' => $request->producto_id,
                'variante_producto_id' => $request->variante_producto_id,
                'ubicacion_id' => $request->ubicacion_id,
                'tipo' => $request->tipo,
                'cantidad' => $request->cantidad,
                'valor_original' => $request->valor_original,
                'valor_saldo' => $request->tipo === 'saldo' ? $request->valor_saldo : null,
                'descripcion' => $request->descripcion,
                'estado' => NovedadStock::ESTADO_PENDIENTE,
                'numero_garantia' => $request->tipo === 'garantia' ? $request->numero_garantia : null,
                'fecha_vencimiento_garantia' => $request->tipo === 'garantia' ? $request->fecha_vencimiento_garantia : null,
                'usuario_id' => auth()->id(),
            ]);

            // Descontar del stock disponible
            $stockAnterior = $stockActual->cantidad_disponible;
            $stockActual->cantidad_disponible -= $request->cantidad;
            $stockActual->save();

            // Registrar movimiento de salida
            MovimientoStock::create([
                'producto_id' => $request->producto_id,
                'variante_producto_id' => $request->variante_producto_id,
                'ubicacion_id' => $request->ubicacion_id,
                'tipo_movimiento' => 'salida',
                'cantidad' => $request->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockActual->cantidad_disponible,
                'referencia_documento' => 'NOV-' . $novedad->id,
                'origen' => 'ajuste_inventario',
                'tipo_operacion' => 'general',
                'motivo' => 'Novedad: ' . $novedad->tipo_nombre . ' - ' . $request->descripcion,
                'usuario_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('novedades-stock')
                ->with('success', 'Novedad registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar la novedad: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function cerrar(Request $request, $id)
    {
        $novedad = NovedadStock::findOrFail($id);

        if (!$novedad->puedeCerrar()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta novedad ya no puede ser cerrada.'
            ], 422);
        }

        $request->validate([
            'estado' => 'required|in:procesado,recuperado,dado_de_baja',
            'notas_cierre' => 'nullable|string|max:500',
            'recuperar_stock' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Si se recupera el stock (por ejemplo, garantía procesada exitosamente)
            if ($request->recuperar_stock && in_array($request->estado, ['recuperado', 'procesado'])) {
                $stockActual = StockProducto::where('producto_id', $novedad->producto_id)
                    ->where('ubicacion_id', $novedad->ubicacion_id);

                if ($novedad->variante_producto_id) {
                    $stockActual->where('variante_producto_id', $novedad->variante_producto_id);
                } else {
                    $stockActual->whereNull('variante_producto_id');
                }

                $stockActual = $stockActual->first();

                if ($stockActual) {
                    $stockAnterior = $stockActual->cantidad_disponible;
                    $stockActual->cantidad_disponible += $novedad->cantidad;
                    $stockActual->save();

                    // Registrar movimiento de entrada
                    MovimientoStock::create([
                        'producto_id' => $novedad->producto_id,
                        'variante_producto_id' => $novedad->variante_producto_id,
                        'ubicacion_id' => $novedad->ubicacion_id,
                        'tipo_movimiento' => 'entrada',
                        'cantidad' => $novedad->cantidad,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockActual->cantidad_disponible,
                        'referencia_documento' => 'NOV-' . $novedad->id . '-REC',
                        'origen' => 'ajuste_inventario',
                        'tipo_operacion' => 'general',
                        'motivo' => 'Recuperación de novedad: ' . $novedad->tipo_nombre,
                        'usuario_id' => auth()->id(),
                    ]);
                }
            }

            // Cerrar la novedad
            $novedad->cerrar(auth()->id(), $request->estado, $request->notas_cierre);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Novedad cerrada correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar la novedad: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detalle($id)
    {
        $novedad = NovedadStock::with([
            'producto',
            'varianteProducto',
            'ubicacion',
            'usuario',
            'usuarioCierre'
        ])->findOrFail($id);

        return response()->json($novedad);
    }

    public function getVariantesPorProducto($productoId)
    {
        $producto = Producto::with('variantes')->findOrFail($productoId);

        return response()->json([
            'tiene_variantes' => $producto->tiene_variantes,
            'variantes' => $producto->variantes
        ]);
    }

    public function getStockDisponible(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'variante_producto_id' => 'nullable|exists:variantes_productos,id',
        ]);

        $query = StockProducto::where('producto_id', $request->producto_id)
            ->where('ubicacion_id', $request->ubicacion_id);

        if ($request->variante_producto_id) {
            $query->where('variante_producto_id', $request->variante_producto_id);
        } else {
            $query->whereNull('variante_producto_id');
        }

        $stock = $query->first();

        return response()->json([
            'stock_disponible' => $stock ? $stock->stock_real : 0
        ]);
    }

    public function dashboard()
    {
        $pendientes = NovedadStock::pendientes()->count();
        $garantiasVigentes = NovedadStock::garantiasVigentes()->count();
        $garantiasVencidas = NovedadStock::garantiasVencidas()->count();

        $porTipo = NovedadStock::selectRaw('tipo, count(*) as total, sum(valor_original) as valor_total')
            ->where('estado', 'pendiente')
            ->groupBy('tipo')
            ->get();

        $ultimasNovedades = NovedadStock::with(['producto', 'ubicacion'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('novedades-stock.dashboard', compact(
            'pendientes',
            'garantiasVigentes',
            'garantiasVencidas',
            'porTipo',
            'ultimasNovedades'
        ));
    }
}
