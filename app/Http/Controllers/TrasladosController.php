<?php

namespace App\Http\Controllers;

use App\Models\TrasladoStock;
use App\Models\Ubicacion;
use App\Models\Producto;
use App\Models\StockProducto;
use App\Models\MovimientoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TrasladosController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = TrasladoStock::with(['ubicacionOrigen', 'ubicacionDestino', 'producto', 'varianteProducto', 'usuarioCreador']);

            // Filtro por estado
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            // Filtro por tipo de operación
            if ($request->filled('tipo_operacion')) {
                $query->where('tipo_operacion', $request->tipo_operacion);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $btns = '<div class="d-flex gap-1">';

                    if ($row->puedeEnviar()) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-primary" onclick="enviarTraslado(' . $row->id . ')" title="Enviar"><i class="bi bi-send"></i></button>';
                    }

                    if ($row->puedeRecibir() && auth()->id() !== $row->usuario_creador_id) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-success" onclick="recibirTraslado(' . $row->id . ')" title="Recibir"><i class="bi bi-check-lg"></i></button>';
                    }

                    if ($row->puedeCancelar()) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelarTraslado(' . $row->id . ')" title="Cancelar"><i class="bi bi-x-lg"></i></button>';
                    }

                    $btns .= '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="verDetalleTraslado(' . $row->id . ')" title="Ver detalle"><i class="bi bi-eye"></i></button>';
                    $btns .= '</div>';
                    return $btns;
                })
                ->addColumn('producto_nombre', function ($row) {
                    return $row->producto_nombre;
                })
                ->addColumn('ruta', function ($row) {
                    return $row->ubicacionOrigen->nombre . ' → ' . $row->ubicacionDestino->nombre;
                })
                ->addColumn('tipo_operacion_badge', function ($row) {
                    $color = $row->tipo_operacion === 'credito' ? 'info' : 'secondary';
                    return '<span class="badge bg-' . $color . '">' . $row->tipo_operacion_nombre . '</span>';
                })
                ->addColumn('estado_badge', function ($row) {
                    $colores = [
                        'pendiente' => 'warning',
                        'en_transito' => 'info',
                        'completado' => 'success',
                        'cancelado' => 'secondary',
                    ];
                    $color = $colores[$row->estado] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . $row->estado_nombre . '</span>';
                })
                ->addColumn('creador', function ($row) {
                    return $row->usuarioCreador->name ?? 'N/A';
                })
                ->rawColumns(['action', 'tipo_operacion_badge', 'estado_badge'])
                ->make(true);
        }

        return view('traslados.index');
    }

    public function form($id = null)
    {
        $traslado = $id ? TrasladoStock::findOrFail($id) : new TrasladoStock();

        // Para rol inventarios, origen solo muestra bodegas
        if (auth()->user()->hasRole('inventarios')) {
            $ubicacionesOrigen = Ubicacion::activas()->bodegas()->get();
        } else {
            $ubicacionesOrigen = Ubicacion::activas()->get();
        }

        $ubicacionesDestino = Ubicacion::activas()->get();

        return view('traslados.form', compact('traslado', 'ubicacionesOrigen', 'ubicacionesDestino'));
    }

    /**
     * Obtener productos con stock disponible en una ubicación específica
     */
    public function getProductosPorUbicacion($ubicacionId)
    {
        // Obtener IDs de productos que tienen stock en esta ubicación
        $stockItems = StockProducto::where('ubicacion_id', $ubicacionId)
            ->where('cantidad_disponible', '>', 0)
            ->get();

        $productosIds = $stockItems->pluck('producto_id')->unique();

        $productos = Producto::whereIn('id', $productosIds)
            ->where('eliminado', false)
            ->where('activo', true)
            ->where('controlar_stock', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($producto) use ($stockItems, $ubicacionId) {
                // Verificar si tiene variantes con stock en esta ubicación
                $stockEnUbicacion = $stockItems->where('producto_id', $producto->id);
                $tieneVariantesConStock = $stockEnUbicacion->whereNotNull('variante_producto_id')->count() > 0;

                return [
                    'id' => $producto->id,
                    'referencia' => $producto->referencia,
                    'nombre' => $producto->nombre,
                    'tiene_variantes' => $producto->tiene_variantes && $tieneVariantesConStock,
                ];
            });

        return response()->json([
            'productos' => $productos
        ]);
    }

    /**
     * Obtener variantes de un producto con stock en una ubicación específica
     */
    public function getVariantesPorProductoYUbicacion($productoId, $ubicacionId)
    {
        $producto = Producto::with('variantes')->findOrFail($productoId);

        if (!$producto->tiene_variantes) {
            return response()->json([
                'tiene_variantes' => false,
                'variantes' => []
            ]);
        }

        // Obtener solo variantes que tienen stock en esta ubicación
        $stockItems = StockProducto::where('producto_id', $productoId)
            ->where('ubicacion_id', $ubicacionId)
            ->whereNotNull('variante_producto_id')
            ->where('cantidad_disponible', '>', 0)
            ->pluck('variante_producto_id');

        $variantes = $producto->variantes()
            ->whereIn('id', $stockItems)
            ->get()
            ->map(function ($variante) {
                return [
                    'id' => $variante->id,
                    'nombre_variante' => $variante->nombre_variante,
                ];
            });

        return response()->json([
            'tiene_variantes' => true,
            'variantes' => $variantes
        ]);
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'ubicacion_origen_id' => 'required|exists:ubicaciones,id',
            'ubicacion_destino_id' => 'required|exists:ubicaciones,id|different:ubicacion_origen_id',
            'producto_id' => 'required|exists:productos,id',
            'variante_producto_id' => 'nullable|exists:variantes_productos,id',
            'cantidad' => 'required|integer|min:1',
            'tipo_operacion' => 'required|in:general,credito',
            'notas' => 'nullable|string|max:500',
        ]);

        // Verificar stock disponible en origen
        $stockOrigen = StockProducto::where('producto_id', $request->producto_id)
            ->where('ubicacion_id', $request->ubicacion_origen_id);

        if ($request->variante_producto_id) {
            $stockOrigen->where('variante_producto_id', $request->variante_producto_id);
        } else {
            $stockOrigen->whereNull('variante_producto_id');
        }

        $stockOrigen = $stockOrigen->first();

        if (!$stockOrigen || $stockOrigen->stock_real < $request->cantidad) {
            return back()->withErrors(['cantidad' => 'No hay suficiente stock disponible en la ubicación de origen.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Crear el traslado directamente en estado "en_transito"
            $traslado = TrasladoStock::create([
                'numero_traslado' => TrasladoStock::generarNumeroTraslado(),
                'ubicacion_origen_id' => $request->ubicacion_origen_id,
                'ubicacion_destino_id' => $request->ubicacion_destino_id,
                'producto_id' => $request->producto_id,
                'variante_producto_id' => $request->variante_producto_id,
                'cantidad' => $request->cantidad,
                'estado' => TrasladoStock::ESTADO_EN_TRANSITO,
                'notas' => $request->notas,
                'tipo_operacion' => $request->tipo_operacion,
                'usuario_creador_id' => auth()->id(),
                'enviado_en' => now(),
            ]);

            // Descontar del stock de origen automáticamente
            $stockAnterior = $stockOrigen->cantidad_disponible;
            $stockOrigen->cantidad_disponible -= $request->cantidad;
            $stockOrigen->save();

            // Registrar movimiento de salida
            MovimientoStock::create([
                'producto_id' => $request->producto_id,
                'variante_producto_id' => $request->variante_producto_id,
                'ubicacion_id' => $request->ubicacion_origen_id,
                'tipo_movimiento' => 'salida',
                'cantidad' => $request->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockOrigen->cantidad_disponible,
                'referencia_documento' => $traslado->numero_traslado,
                'origen' => 'traslado',
                'tipo_operacion' => $request->tipo_operacion,
                'motivo' => 'Traslado a ' . Ubicacion::find($request->ubicacion_destino_id)->nombre,
                'usuario_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('traslados')
                ->with('success', 'Traslado creado y enviado correctamente. Número: ' . $traslado->numero_traslado);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el traslado: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function enviar($id)
    {
        $traslado = TrasladoStock::findOrFail($id);

        if (!$traslado->puedeEnviar()) {
            return response()->json([
                'success' => false,
                'message' => 'Este traslado no puede ser enviado.'
            ], 422);
        }

        // Verificar stock disponible
        $stockOrigen = StockProducto::where('producto_id', $traslado->producto_id)
            ->where('ubicacion_id', $traslado->ubicacion_origen_id);

        if ($traslado->variante_producto_id) {
            $stockOrigen->where('variante_producto_id', $traslado->variante_producto_id);
        } else {
            $stockOrigen->whereNull('variante_producto_id');
        }

        $stockOrigen = $stockOrigen->first();

        if (!$stockOrigen || $stockOrigen->stock_real < $traslado->cantidad) {
            return response()->json([
                'success' => false,
                'message' => 'No hay suficiente stock disponible para enviar.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Descontar del stock de origen
            $stockAnterior = $stockOrigen->cantidad_disponible;
            $stockOrigen->cantidad_disponible -= $traslado->cantidad;
            $stockOrigen->save();

            // Registrar movimiento de salida
            MovimientoStock::create([
                'producto_id' => $traslado->producto_id,
                'variante_producto_id' => $traslado->variante_producto_id,
                'ubicacion_id' => $traslado->ubicacion_origen_id,
                'tipo_movimiento' => 'salida',
                'cantidad' => $traslado->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockOrigen->cantidad_disponible,
                'referencia_documento' => $traslado->numero_traslado,
                'origen' => 'traslado',
                'tipo_operacion' => $traslado->tipo_operacion ?? 'general',
                'motivo' => 'Traslado a ' . $traslado->ubicacionDestino->nombre,
                'usuario_id' => auth()->id(),
            ]);

            // Actualizar estado del traslado
            $traslado->enviar();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Traslado enviado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el traslado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function recibir($id)
    {
        $traslado = TrasladoStock::findOrFail($id);

        if (!$traslado->puedeRecibir()) {
            return response()->json([
                'success' => false,
                'message' => 'Este traslado no puede ser recibido.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Buscar o crear stock en destino
            $stockDestino = StockProducto::firstOrCreate(
                [
                    'producto_id' => $traslado->producto_id,
                    'variante_producto_id' => $traslado->variante_producto_id,
                    'ubicacion_id' => $traslado->ubicacion_destino_id,
                ],
                [
                    'cantidad_disponible' => 0,
                    'cantidad_reservada' => 0,
                    'stock_minimo' => 0,
                    'stock_maximo' => 0,
                    'alerta_stock_bajo' => false,
                ]
            );

            $stockAnterior = $stockDestino->cantidad_disponible;
            $stockDestino->cantidad_disponible += $traslado->cantidad;
            $stockDestino->save();

            // Registrar movimiento de entrada
            MovimientoStock::create([
                'producto_id' => $traslado->producto_id,
                'variante_producto_id' => $traslado->variante_producto_id,
                'ubicacion_id' => $traslado->ubicacion_destino_id,
                'tipo_movimiento' => 'entrada',
                'cantidad' => $traslado->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockDestino->cantidad_disponible,
                'referencia_documento' => $traslado->numero_traslado,
                'origen' => 'traslado',
                'tipo_operacion' => $traslado->tipo_operacion ?? 'general',
                'motivo' => 'Traslado desde ' . $traslado->ubicacionOrigen->nombre,
                'usuario_id' => auth()->id(),
            ]);

            // Completar el traslado
            $traslado->completar(auth()->id());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Traslado recibido correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al recibir el traslado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelar($id)
    {
        $traslado = TrasladoStock::findOrFail($id);

        if (!$traslado->puedeCancelar()) {
            return response()->json([
                'success' => false,
                'message' => 'Este traslado no puede ser cancelado.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Si ya estaba en tránsito, devolver el stock al origen
            if ($traslado->estado === TrasladoStock::ESTADO_EN_TRANSITO) {
                $stockOrigen = StockProducto::where('producto_id', $traslado->producto_id)
                    ->where('ubicacion_id', $traslado->ubicacion_origen_id);

                if ($traslado->variante_producto_id) {
                    $stockOrigen->where('variante_producto_id', $traslado->variante_producto_id);
                } else {
                    $stockOrigen->whereNull('variante_producto_id');
                }

                $stockOrigen = $stockOrigen->first();

                if ($stockOrigen) {
                    $stockAnterior = $stockOrigen->cantidad_disponible;
                    $stockOrigen->cantidad_disponible += $traslado->cantidad;
                    $stockOrigen->save();

                    // Registrar movimiento de devolución
                    MovimientoStock::create([
                        'producto_id' => $traslado->producto_id,
                        'variante_producto_id' => $traslado->variante_producto_id,
                        'ubicacion_id' => $traslado->ubicacion_origen_id,
                        'tipo_movimiento' => 'entrada',
                        'cantidad' => $traslado->cantidad,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockOrigen->cantidad_disponible,
                        'referencia_documento' => $traslado->numero_traslado,
                        'origen' => 'traslado',
                        'tipo_operacion' => $traslado->tipo_operacion ?? 'general',
                        'motivo' => 'Cancelación de traslado',
                        'usuario_id' => auth()->id(),
                    ]);
                }
            }

            $traslado->cancelar();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Traslado cancelado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar el traslado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detalle($id)
    {
        $traslado = TrasladoStock::with([
            'ubicacionOrigen',
            'ubicacionDestino',
            'producto',
            'varianteProducto',
            'usuarioCreador',
            'usuarioReceptor'
        ])->findOrFail($id);

        return response()->json($traslado);
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
}
