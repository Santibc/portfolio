<?php

namespace App\Http\Controllers;

use App\Models\TrasladoStock;
use App\Models\ItemTrasladoStock;
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
            $query = TrasladoStock::with(['ubicacionOrigen', 'ubicacionDestino', 'items.producto', 'items.varianteProducto', 'usuarioCreador']);

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
                    $user = auth()->user();
                    $puedeAprobarRechazar = $user->hasRole(['admin', 'auxiliar_administrativo', 'centro_experiencia']);
                    $btns = '<div class="d-flex gap-1">';

                    if ($row->puedeEnviar() && $user->hasRole(['admin', 'auxiliar_administrativo', 'inventarios'])) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-primary" onclick="enviarTraslado(' . $row->id . ')" title="Enviar"><i class="bi bi-send"></i></button>';
                    }

                    if ($row->puedeRecibir() && $puedeAprobarRechazar) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-success" onclick="recibirTraslado(' . $row->id . ')" title="Recibir"><i class="bi bi-check-lg"></i></button>';
                    }

                    if ($row->puedeCancelar() && $puedeAprobarRechazar) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelarTraslado(' . $row->id . ')" title="Cancelar"><i class="bi bi-x-lg"></i></button>';
                    }

                    $btns .= '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="verDetalleTraslado(' . $row->id . ')" title="Ver detalle"><i class="bi bi-eye"></i></button>';
                    $btns .= '</div>';
                    return $btns;
                })
                ->addColumn('producto_nombre', function ($row) {
                    return $row->producto_nombre;
                })
                ->addColumn('cantidad_total', function ($row) {
                    return $row->cantidad_total;
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
     * Obtener productos con stock disponible
     */
    public function getProductosPorUbicacion($ubicacionId)
    {
        $stockItems = StockProducto::where('cantidad_disponible', '>', 0)->get();

        // Obtener ítems en traslados en tránsito
        $itemsEnTransito = ItemTrasladoStock::whereHas('traslado', function ($q) {
            $q->where('estado', TrasladoStock::ESTADO_EN_TRANSITO);
        })->get();

        $productosIds = $stockItems->pluck('producto_id')->unique();

        $productos = Producto::whereIn('id', $productosIds)
            ->where('eliminado', false)
            ->where('activo', true)
            ->where('controlar_stock', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($producto) use ($stockItems, $itemsEnTransito) {
                $stockTotal = $stockItems->where('producto_id', $producto->id)->sum('cantidad_disponible');
                $enTransito = $itemsEnTransito->where('producto_id', $producto->id)->sum('cantidad');
                $stockEfectivo = $stockTotal - $enTransito;

                if ($stockEfectivo <= 0) {
                    return null;
                }

                $tieneVariantesConStock = $stockItems->where('producto_id', $producto->id)
                    ->whereNotNull('variante_producto_id')->count() > 0;

                return [
                    'id' => $producto->id,
                    'referencia' => $producto->referencia,
                    'nombre' => $producto->nombre,
                    'tiene_variantes' => $producto->tiene_variantes && $tieneVariantesConStock,
                    'stock_disponible' => $stockEfectivo,
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'productos' => $productos
        ]);
    }

    /**
     * Obtener variantes de un producto con stock
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

        $stockItems = StockProducto::where('producto_id', $productoId)
            ->whereNotNull('variante_producto_id')
            ->where('cantidad_disponible', '>', 0)
            ->get();

        $varianteIds = $stockItems->pluck('variante_producto_id')->unique();

        // Ítems en traslados en tránsito para este producto
        $itemsEnTransito = ItemTrasladoStock::where('producto_id', $productoId)
            ->whereNotNull('variante_producto_id')
            ->whereHas('traslado', function ($q) {
                $q->where('estado', TrasladoStock::ESTADO_EN_TRANSITO);
            })->get();

        $variantes = $producto->variantes()
            ->whereIn('id', $varianteIds)
            ->get()
            ->map(function ($variante) use ($stockItems, $itemsEnTransito) {
                $stockTotal = $stockItems->where('variante_producto_id', $variante->id)->sum('cantidad_disponible');
                $enTransito = $itemsEnTransito->where('variante_producto_id', $variante->id)->sum('cantidad');
                $stockEfectivo = $stockTotal - $enTransito;

                if ($stockEfectivo <= 0) {
                    return null;
                }

                return [
                    'id' => $variante->id,
                    'nombre_variante' => $variante->nombre_variante,
                    'stock_disponible' => $stockEfectivo,
                ];
            })
            ->filter()
            ->values();

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
            'tipo_operacion' => 'required|in:general,credito',
            'notas' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.variante_producto_id' => 'nullable|exists:variantes_productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Crear cabecera del traslado
            $traslado = TrasladoStock::create([
                'numero_traslado' => TrasladoStock::generarNumeroTraslado(),
                'ubicacion_origen_id' => $request->ubicacion_origen_id,
                'ubicacion_destino_id' => $request->ubicacion_destino_id,
                'estado' => TrasladoStock::ESTADO_EN_TRANSITO,
                'notas' => $request->notas,
                'tipo_operacion' => $request->tipo_operacion,
                'usuario_creador_id' => auth()->id(),
                'enviado_en' => now(),
            ]);

            $ubicacionDestinoNombre = Ubicacion::find($request->ubicacion_destino_id)->nombre;

            // Procesar cada ítem
            foreach ($request->items as $itemData) {
                $cantidad = (int) $itemData['cantidad'];
                $varianteId = $itemData['variante_producto_id'] ?? null;

                // Buscar registros de stock disponibles para este producto (cualquier ubicación)
                $stockQuery = StockProducto::where('producto_id', $itemData['producto_id'])
                    ->where('cantidad_disponible', '>', 0);

                if (!empty($varianteId)) {
                    $stockQuery->where('variante_producto_id', $varianteId);
                } else {
                    $stockQuery->whereNull('variante_producto_id');
                }

                // Priorizar la ubicación de origen, luego las demás
                $stockRecords = $stockQuery->orderByRaw("CASE WHEN ubicacion_id = ? THEN 0 ELSE 1 END", [$request->ubicacion_origen_id])
                    ->get();

                $stockTotal = $stockRecords->sum('cantidad_disponible');

                if ($stockTotal < $cantidad) {
                    $producto = Producto::find($itemData['producto_id']);
                    throw new \Exception("No hay suficiente stock para el producto: {$producto->referencia} - {$producto->nombre}");
                }

                // Crear ítem del traslado
                ItemTrasladoStock::create([
                    'traslado_stock_id' => $traslado->id,
                    'producto_id' => $itemData['producto_id'],
                    'variante_producto_id' => $varianteId,
                    'cantidad' => $cantidad,
                ]);

                // Descontar stock de los registros disponibles
                $restante = $cantidad;
                foreach ($stockRecords as $stockRecord) {
                    if ($restante <= 0) break;

                    $descontar = min($restante, $stockRecord->cantidad_disponible);
                    $stockAnterior = $stockRecord->cantidad_disponible;
                    $stockRecord->cantidad_disponible -= $descontar;
                    $stockRecord->save();

                    // Registrar movimiento de salida por cada ubicación afectada
                    MovimientoStock::create([
                        'producto_id' => $itemData['producto_id'],
                        'variante_producto_id' => $varianteId,
                        'ubicacion_id' => $stockRecord->ubicacion_id,
                        'tipo_movimiento' => 'salida',
                        'cantidad' => $descontar,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockRecord->cantidad_disponible,
                        'referencia_documento' => $traslado->numero_traslado,
                        'origen' => 'traslado',
                        'tipo_operacion' => $request->tipo_operacion,
                        'motivo' => 'Traslado a ' . $ubicacionDestinoNombre,
                        'usuario_id' => auth()->id(),
                    ]);

                    $restante -= $descontar;
                }
            }

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
        $traslado = TrasladoStock::with('items')->findOrFail($id);

        if (!$traslado->puedeEnviar()) {
            return response()->json([
                'success' => false,
                'message' => 'Este traslado no puede ser enviado.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($traslado->items as $item) {
                $stockQuery = StockProducto::where('producto_id', $item->producto_id)
                    ->where('cantidad_disponible', '>', 0);

                if ($item->variante_producto_id) {
                    $stockQuery->where('variante_producto_id', $item->variante_producto_id);
                } else {
                    $stockQuery->whereNull('variante_producto_id');
                }

                $stockRecords = $stockQuery->orderByRaw("CASE WHEN ubicacion_id = ? THEN 0 ELSE 1 END", [$traslado->ubicacion_origen_id])->get();
                $stockTotal = $stockRecords->sum('cantidad_disponible');

                if ($stockTotal < $item->cantidad) {
                    throw new \Exception('No hay suficiente stock para: ' . $item->producto->nombre);
                }

                $restante = $item->cantidad;
                foreach ($stockRecords as $stockRecord) {
                    if ($restante <= 0) break;
                    $descontar = min($restante, $stockRecord->cantidad_disponible);
                    $stockAnterior = $stockRecord->cantidad_disponible;
                    $stockRecord->cantidad_disponible -= $descontar;
                    $stockRecord->save();

                    MovimientoStock::create([
                        'producto_id' => $item->producto_id,
                        'variante_producto_id' => $item->variante_producto_id,
                        'ubicacion_id' => $stockRecord->ubicacion_id,
                        'tipo_movimiento' => 'salida',
                        'cantidad' => $descontar,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockRecord->cantidad_disponible,
                        'referencia_documento' => $traslado->numero_traslado,
                        'origen' => 'traslado',
                        'tipo_operacion' => $traslado->tipo_operacion ?? 'general',
                        'motivo' => 'Traslado a ' . $traslado->ubicacionDestino->nombre,
                        'usuario_id' => auth()->id(),
                    ]);
                    $restante -= $descontar;
                }
            }

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
        if (!auth()->user()->hasRole(['admin', 'auxiliar_administrativo', 'centro_experiencia'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para aprobar traslados.'
            ], 403);
        }

        $traslado = TrasladoStock::with('items')->findOrFail($id);

        if (!$traslado->puedeRecibir()) {
            return response()->json([
                'success' => false,
                'message' => 'Este traslado no puede ser recibido.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($traslado->items as $item) {
                // Buscar o crear stock en destino
                $stockDestino = StockProducto::firstOrCreate(
                    [
                        'producto_id' => $item->producto_id,
                        'variante_producto_id' => $item->variante_producto_id,
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
                $stockDestino->cantidad_disponible += $item->cantidad;
                $stockDestino->save();

                MovimientoStock::create([
                    'producto_id' => $item->producto_id,
                    'variante_producto_id' => $item->variante_producto_id,
                    'ubicacion_id' => $traslado->ubicacion_destino_id,
                    'tipo_movimiento' => 'entrada',
                    'cantidad' => $item->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockDestino->cantidad_disponible,
                    'referencia_documento' => $traslado->numero_traslado,
                    'origen' => 'traslado',
                    'tipo_operacion' => $traslado->tipo_operacion ?? 'general',
                    'motivo' => 'Traslado desde ' . $traslado->ubicacionOrigen->nombre,
                    'usuario_id' => auth()->id(),
                ]);
            }

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
        if (!auth()->user()->hasRole(['admin', 'auxiliar_administrativo', 'centro_experiencia'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para rechazar traslados.'
            ], 403);
        }

        $traslado = TrasladoStock::with('items')->findOrFail($id);

        if (!$traslado->puedeCancelar()) {
            return response()->json([
                'success' => false,
                'message' => 'Este traslado no puede ser cancelado.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Si ya estaba en tránsito, devolver el stock al origen para cada ítem
            if ($traslado->estado === TrasladoStock::ESTADO_EN_TRANSITO) {
                foreach ($traslado->items as $item) {
                    $stockOrigen = StockProducto::where('producto_id', $item->producto_id)
                        ->where('ubicacion_id', $traslado->ubicacion_origen_id);

                    if ($item->variante_producto_id) {
                        $stockOrigen->where('variante_producto_id', $item->variante_producto_id);
                    } else {
                        $stockOrigen->whereNull('variante_producto_id');
                    }

                    $stockOrigen = $stockOrigen->first();

                    if ($stockOrigen) {
                        $stockAnterior = $stockOrigen->cantidad_disponible;
                        $stockOrigen->cantidad_disponible += $item->cantidad;
                        $stockOrigen->save();

                        MovimientoStock::create([
                            'producto_id' => $item->producto_id,
                            'variante_producto_id' => $item->variante_producto_id,
                            'ubicacion_id' => $traslado->ubicacion_origen_id,
                            'tipo_movimiento' => 'entrada',
                            'cantidad' => $item->cantidad,
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
            'items.producto',
            'items.varianteProducto',
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
            'ubicacion_id' => 'nullable|exists:ubicaciones,id',
            'variante_producto_id' => 'nullable|exists:variantes_productos,id',
        ]);

        $query = StockProducto::where('producto_id', $request->producto_id);

        if ($request->variante_producto_id) {
            $query->where('variante_producto_id', $request->variante_producto_id);
        } else {
            $query->whereNull('variante_producto_id');
        }

        $stockTotal = $query->sum('cantidad_disponible');

        // Restar cantidades en traslados en tránsito (ahora desde items)
        $enTransito = ItemTrasladoStock::where('producto_id', $request->producto_id)
            ->whereHas('traslado', function ($q) {
                $q->where('estado', TrasladoStock::ESTADO_EN_TRANSITO);
            });

        if ($request->variante_producto_id) {
            $enTransito->where('variante_producto_id', $request->variante_producto_id);
        } else {
            $enTransito->whereNull('variante_producto_id');
        }

        $enTransitoTotal = $enTransito->sum('cantidad');
        $stockEfectivo = max(0, $stockTotal - $enTransitoTotal);

        return response()->json([
            'stock_disponible' => $stockEfectivo
        ]);
    }
}
