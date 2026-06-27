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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Caja;
use App\Models\LogTraslado;
use Yajra\DataTables\Facades\DataTables;

class TrasladosController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = TrasladoStock::with(['ubicacionOrigen', 'ubicacionDestino', 'items.producto', 'items.varianteProducto', 'usuarioCreador']);

            $user = auth()->user();
            $esCajeroPrincipal = $user->hasRole('cajero_principal') && !$user->hasRole('admin');
            $ubicacionCajeroId = null;
            if ($esCajeroPrincipal) {
                $ubicacionCajeroId = Caja::where('cajero_asignado_id', $user->id)->value('ubicacion_id');
            }

            // Filtro por estado
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            // Filtro por tipo de operación
            if ($request->filled('tipo_operacion')) {
                $query->where('tipo_operacion', $request->tipo_operacion);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) use ($esCajeroPrincipal, $ubicacionCajeroId) {
                    $user = auth()->user();
                    $puedeAprobarRechazar = $user->hasRole(['admin', 'auxiliar_administrativo', 'centro_experiencia']);
                    $btns = '<div class="d-flex gap-1">';

                    $esCreador = $row->usuario_creador_id === $user->id;

                    // Edit button - roles internos o el cajero sobre los traslados que él creó
                    if (in_array($row->estado, [TrasladoStock::ESTADO_PENDIENTE, TrasladoStock::ESTADO_EN_TRANSITO])
                        && ($user->hasRole(['admin', 'auxiliar_administrativo', 'inventarios'])
                            || ($esCajeroPrincipal && $esCreador))) {
                        $btns .= '<a href="/traslados/form/' . $row->id . '" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';
                    }
                    // Send button - roles internos o el cajero sobre los traslados que él creó
                    if ($row->puedeEnviar()
                        && ($user->hasRole(['admin', 'auxiliar_administrativo', 'inventarios'])
                            || ($esCajeroPrincipal && $esCreador))) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-primary" onclick="enviarTraslado(' . $row->id . ')" title="Enviar"><i class="bi bi-send"></i></button>';
                    }
                    // Cancel button - solo roles aprobadores
                    if ($row->puedeCancelar() && $puedeAprobarRechazar && !$esCajeroPrincipal) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelarTraslado(' . $row->id . ')" title="Cancelar"><i class="bi bi-x-lg"></i></button>';
                    }

                    // Receive button - depende del TIPO del destino
                    $destinoTipo = optional($row->ubicacionDestino)->tipo;
                    $destinoEsBodega = $destinoTipo === Ubicacion::TIPO_BODEGA;
                    $destinoEsTienda = $destinoTipo === Ubicacion::TIPO_TIENDA;

                    $puedeRecibir = false;
                    if ($destinoEsBodega && $user->hasRole(['admin', 'auxiliar_administrativo', 'inventarios'])) {
                        $puedeRecibir = true;
                    }
                    if ($destinoEsTienda && $user->hasRole(['admin', 'auxiliar_administrativo', 'centro_experiencia'])) {
                        $puedeRecibir = true;
                    }
                    // Cajero solo recibe en SU tienda (destino tienda); nunca en bodega
                    if ($esCajeroPrincipal && $ubicacionCajeroId
                        && $row->ubicacion_destino_id == $ubicacionCajeroId
                        && $destinoEsTienda) {
                        $puedeRecibir = true;
                    }
                    if ($esCajeroPrincipal && $destinoEsBodega) {
                        $puedeRecibir = false;
                    }

                    if ($row->puedeRecibir() && $puedeRecibir) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-success" onclick="recibirTraslado(' . $row->id . ')" title="Recibir"><i class="bi bi-check-lg"></i></button>';
                    }

                    // View, PDF, Logs - always visible
                    $btns .= '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="verDetalleTraslado(' . $row->id . ')" title="Ver detalle"><i class="bi bi-eye"></i></button>';
                    $btns .= '<a href="/traslados/' . $row->id . '/pdf" target="_blank" class="btn btn-sm btn-outline-info" title="PDF"><i class="bi bi-file-earmark-pdf"></i></a>';
                    $btns .= '<button type="button" class="btn btn-sm btn-outline-dark" onclick="verLogsTraslado(' . $row->id . ')" title="Historial"><i class="bi bi-clock-history"></i></button>';
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
                ->addColumn('tiene_observacion', function ($row) {
                    return $row->observacion_recepcion ? '1' : '0';
                })
                ->rawColumns(['action', 'tipo_operacion_badge', 'estado_badge'])
                ->make(true);
        }

        return view('traslados.index');
    }

    public function form($id = null)
    {
        $traslado = $id ? TrasladoStock::findOrFail($id) : new TrasladoStock();
        $ubicacionCajeroId = null;

        if (auth()->user()->hasRole('cajero_principal') && !auth()->user()->hasRole('admin')) {
            $caja = Caja::where('cajero_asignado_id', auth()->id())->first();

            if (!$caja || !$caja->ubicacion_id) {
                return redirect()->route('traslados')
                    ->with('error', 'No tiene una caja asignada con ubicación configurada.');
            }

            $ubicacionCajeroId = $caja->ubicacion_id;

            // Universo permitido: la tienda del cajero + todas las bodegas activas.
            // La vista filtra dinámicamente el destino según el origen seleccionado.
            $bodegas = Ubicacion::activas()->bodegas()->get();
            $tiendaCajero = Ubicacion::activas()->where('id', $ubicacionCajeroId)->get();

            $ubicacionesOrigen = $tiendaCajero->concat($bodegas)->unique('id')->values();
            $ubicacionesDestino = $ubicacionesOrigen;
        } elseif (auth()->user()->hasRole('inventarios')) {
            $ubicacionesOrigen = Ubicacion::activas()->bodegas()->get();
            $ubicacionesDestino = Ubicacion::activas()->get();
        } else {
            $ubicacionesOrigen = Ubicacion::activas()->get();
            $ubicacionesDestino = Ubicacion::activas()->get();
        }

        $items = $id ? $traslado->load('items.producto', 'items.varianteProducto')->items : collect();

        return view('traslados.form', compact('traslado', 'ubicacionesOrigen', 'ubicacionesDestino', 'items', 'ubicacionCajeroId'));
    }

    /**
     * Obtener productos con stock disponible
     */
    public function getProductosPorUbicacion($ubicacionId)
    {
        $stockItems = StockProducto::where('cantidad_disponible', '>', 0)
            ->where(function($q) use ($ubicacionId) {
                $q->where('ubicacion_id', $ubicacionId)
                  ->orWhereNull('ubicacion_id');
            })
            ->get();

        // Obtener ítems en traslados en tránsito desde esta ubicación
        $itemsEnTransito = ItemTrasladoStock::whereHas('traslado', function ($q) use ($ubicacionId) {
            $q->where('estado', TrasladoStock::ESTADO_EN_TRANSITO)
              ->where('ubicacion_origen_id', $ubicacionId);
        })->get();

        $productosIds = $stockItems->pluck('producto_id')->unique();

        $productos = Producto::whereIn('id', $productosIds)
            ->where('eliminado', false)
            ->where('activo', true)
            ->where('controlar_stock', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($producto) use ($stockItems, $itemsEnTransito) {
                $stockTotal = $stockItems->where('producto_id', $producto->id)->sum('cantidad_disponible')
                            - $stockItems->where('producto_id', $producto->id)->sum('cantidad_reservada');
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
            ->where(function($q) use ($ubicacionId) {
                $q->where('ubicacion_id', $ubicacionId)
                  ->orWhereNull('ubicacion_id');
            })
            ->get();

        $varianteIds = $stockItems->pluck('variante_producto_id')->unique();

        // Ítems en traslados en tránsito para este producto desde esta ubicación
        $itemsEnTransito = ItemTrasladoStock::where('producto_id', $productoId)
            ->whereNotNull('variante_producto_id')
            ->whereHas('traslado', function ($q) use ($ubicacionId) {
                $q->where('estado', TrasladoStock::ESTADO_EN_TRANSITO)
                  ->where('ubicacion_origen_id', $ubicacionId);
            })->get();

        $variantes = $producto->variantes()
            ->whereIn('id', $varianteIds)
            ->get()
            ->map(function ($variante) use ($stockItems, $itemsEnTransito) {
                $stockTotal = $stockItems->where('variante_producto_id', $variante->id)->sum('cantidad_disponible')
                           - $stockItems->where('variante_producto_id', $variante->id)->sum('cantidad_reservada');
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

        // Validar stock disponible agrupando por producto+variante
        $itemsAgrupados = [];
        foreach ($request->items as $itemData) {
            $productoId = (int) $itemData['producto_id'];
            $varianteId = !empty($itemData['variante_producto_id']) ? (int) $itemData['variante_producto_id'] : null;
            $clave = $productoId . '_' . ($varianteId ?: 'null');

            if (!isset($itemsAgrupados[$clave])) {
                $itemsAgrupados[$clave] = [
                    'producto_id' => $productoId,
                    'variante_producto_id' => $varianteId,
                    'cantidad' => 0
                ];
            }
            $itemsAgrupados[$clave]['cantidad'] += (int) $itemData['cantidad'];
        }

        $ubicacionOrigenId = $request->ubicacion_origen_id;

        foreach ($itemsAgrupados as $clave => $item) {
            $stockQuery = StockProducto::where('producto_id', $item['producto_id'])
                ->where(function($q) use ($ubicacionOrigenId) {
                    $q->where('ubicacion_id', $ubicacionOrigenId)
                      ->orWhereNull('ubicacion_id');
                });

            if ($item['variante_producto_id']) {
                $stockQuery->where('variante_producto_id', $item['variante_producto_id']);
            } else {
                $stockQuery->whereNull('variante_producto_id');
            }

            $stockRecord = $stockQuery->first();
            $stockDisponible = $stockRecord ? $stockRecord->cantidad_disponible : 0;
            $stockReservado = $stockRecord ? $stockRecord->cantidad_reservada : 0;
            $stockReal = $stockDisponible - $stockReservado;

            if ($stockReal < $item['cantidad']) {
                $producto = Producto::find($item['producto_id']);
                return back()->withErrors([
                    'error' => "Stock insuficiente para {$producto->referencia} - {$producto->nombre}. " .
                              "Disponible: {$stockReal}, Solicitado: {$item['cantidad']}."
                ])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Crear cabecera del traslado en estado pendiente
            $traslado = TrasladoStock::create([
                'numero_traslado' => TrasladoStock::generarNumeroTraslado(),
                'ubicacion_origen_id' => $request->ubicacion_origen_id,
                'ubicacion_destino_id' => $request->ubicacion_destino_id,
                'estado' => TrasladoStock::ESTADO_PENDIENTE,
                'notas' => $request->notas,
                'tipo_operacion' => $request->tipo_operacion,
                'usuario_creador_id' => auth()->id(),
            ]);

            // Crear ítems (no descuenta disponible todavía; eso ocurre al enviar/despachar)
            foreach ($request->items as $itemData) {
                ItemTrasladoStock::create([
                    'traslado_stock_id' => $traslado->id,
                    'producto_id' => $itemData['producto_id'],
                    'variante_producto_id' => $itemData['variante_producto_id'] ?? null,
                    'cantidad' => (int) $itemData['cantidad'],
                ]);
            }

            // RESERVA DE TRASLADO: apartar la cantidad en la bodega ORIGEN al solicitar el traslado.
            // El stock físico sigue ahí (cantidad_disponible no cambia) pero queda comprometido
            // (cantidad_reservada +=), de modo que una cotización no pueda venderlo antes de que el
            // traslado se despache. Al despachar se consume; al cancelar (pendiente) se libera.
            $nombreDestino = Ubicacion::find($request->ubicacion_destino_id)->nombre;
            foreach ($itemsAgrupados as $item) {
                $stockOrigen = StockProducto::where('producto_id', $item['producto_id'])
                    ->where('ubicacion_id', $ubicacionOrigenId)
                    ->when($item['variante_producto_id'],
                        fn($q) => $q->where('variante_producto_id', $item['variante_producto_id']),
                        fn($q) => $q->whereNull('variante_producto_id'))
                    ->lockForUpdate()
                    ->first();

                if ($stockOrigen) {
                    $stockOrigen->increment('cantidad_reservada', $item['cantidad']);

                    MovimientoStock::create([
                        'producto_id' => $item['producto_id'],
                        'variante_producto_id' => $item['variante_producto_id'],
                        'ubicacion_id' => $ubicacionOrigenId,
                        'tipo_movimiento' => 'reserva',
                        'cantidad' => $item['cantidad'],
                        'stock_anterior' => $stockOrigen->cantidad_disponible,
                        'stock_nuevo' => $stockOrigen->cantidad_disponible,
                        'referencia_documento' => $traslado->numero_traslado,
                        'origen' => 'traslado',
                        'tipo_operacion' => $traslado->tipo_operacion ?? 'general',
                        'motivo' => 'Reserva por traslado a ' . $nombreDestino,
                        'usuario_id' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            LogTraslado::registrar($traslado->id, LogTraslado::ACCION_CREACION, [
                'creado_por' => auth()->user()->name,
                'numero' => $traslado->numero_traslado,
                'origen' => Ubicacion::find($request->ubicacion_origen_id)->nombre,
                'destino' => Ubicacion::find($request->ubicacion_destino_id)->nombre,
                'tipo_operacion' => $request->tipo_operacion,
                'cantidad_items' => count($request->items),
                'items' => collect($request->items)->map(function($item) {
                    $prod = Producto::find($item['producto_id']);
                    return [
                        'producto' => $prod->referencia . ' - ' . $prod->nombre,
                        'cantidad' => $item['cantidad'],
                    ];
                })->values()->toArray(),
            ]);

            return redirect()->route('traslados')
                ->with('success', 'Traslado creado correctamente. Número: ' . $traslado->numero_traslado);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el traslado: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function actualizar(Request $request, $id)
    {
        $traslado = TrasladoStock::with('items')->findOrFail($id);

        if (!in_array($traslado->estado, [TrasladoStock::ESTADO_PENDIENTE, TrasladoStock::ESTADO_EN_TRANSITO])) {
            return back()->withErrors(['error' => 'Solo se pueden editar traslados en estado Pendiente o En Tránsito.']);
        }

        $request->validate([
            'ubicacion_origen_id' => 'required|exists:ubicaciones,id',
            'ubicacion_destino_id' => 'required|exists:ubicaciones,id|different:ubicacion_origen_id',
            'tipo_operacion' => 'required|in:general,credito',
            'notas' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.variante_producto_id' => 'nullable|exists:variantes_productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.item_original_id' => 'nullable|integer|exists:items_traslado_stock,id',
        ]);

        // ========================================
        // NUEVA LÓGICA: Validar solo items que REALMENTE cambiaron
        // Agrupa items por (producto_id + variante_id) y compara cantidades totales
        // ========================================

        // PASO 1: Agrupar items originales por clave
        $itemsOriginalesAgrupados = [];
        foreach ($traslado->items as $itemOrig) {
            $clave = $itemOrig->producto_id . '_' . ($itemOrig->variante_producto_id ?: 'null');
            if (!isset($itemsOriginalesAgrupados[$clave])) {
                $itemsOriginalesAgrupados[$clave] = [
                    'producto_id' => $itemOrig->producto_id,
                    'variante_producto_id' => $itemOrig->variante_producto_id,
                    'cantidad' => 0,
                    'referencia' => $itemOrig->producto->referencia ?? '?'
                ];
            }
            $itemsOriginalesAgrupados[$clave]['cantidad'] += $itemOrig->cantidad;
        }

        // PASO 2: Agrupar items editados por clave
        $itemsEditadosAgrupados = [];
        foreach ($request->items as $itemData) {
            $productoId = (int) $itemData['producto_id'];
            $varianteId = !empty($itemData['variante_producto_id']) ? (int) $itemData['variante_producto_id'] : null;
            $clave = $productoId . '_' . ($varianteId ?: 'null');

            if (!isset($itemsEditadosAgrupados[$clave])) {
                $itemsEditadosAgrupados[$clave] = [
                    'producto_id' => $productoId,
                    'variante_producto_id' => $varianteId,
                    'cantidad' => 0
                ];
            }
            $itemsEditadosAgrupados[$clave]['cantidad'] += (int) $itemData['cantidad'];
        }

        // PASO 3: Validar SOLO lo que cambió
        $ubicacionOrigenId = $request->ubicacion_origen_id;
        $ubicacionCambio = ($traslado->ubicacion_origen_id != $ubicacionOrigenId);

        \Log::info("=== VALIDACIÓN TRASLADO #{$traslado->id} (ENFOQUE DIFF) ===");
        \Log::info("Ubicación cambio: " . ($ubicacionCambio ? 'SI' : 'NO'));
        \Log::info("Items agrupados originales: " . count($itemsOriginalesAgrupados));
        \Log::info("Items agrupados editados: " . count($itemsEditadosAgrupados));

        foreach ($itemsEditadosAgrupados as $clave => $itemEditado) {
            $cantidadEditada = $itemEditado['cantidad'];
            $cantidadOriginal = $itemsOriginalesAgrupados[$clave]['cantidad'] ?? 0;
            $referenciaProducto = $itemsOriginalesAgrupados[$clave]['referencia'] ??
                                 (Producto::find($itemEditado['producto_id'])->referencia ?? '?');

            // SKIP si no cambió ubicación Y cantidad es igual
            if (!$ubicacionCambio && $cantidadEditada === $cantidadOriginal) {
                \Log::info("SKIP: {$referenciaProducto} ({$clave}) - cantidad sin cambios ({$cantidadOriginal})");
                continue;
            }

            // VALIDAR stock disponible
            $productoId = $itemEditado['producto_id'];
            $varianteId = $itemEditado['variante_producto_id'];

            $stockQuery = StockProducto::where('producto_id', $productoId)
                ->where(function($q) use ($ubicacionOrigenId) {
                    $q->where('ubicacion_id', $ubicacionOrigenId)
                      ->orWhereNull('ubicacion_id');
                });

            if ($varianteId) {
                $stockQuery->where('variante_producto_id', $varianteId);
            } else {
                $stockQuery->whereNull('variante_producto_id');
            }

            $stockRecord = $stockQuery->first();
            $stockDisponible = $stockRecord ? $stockRecord->cantidad_disponible : 0;
            $stockReservado = $stockRecord ? $stockRecord->cantidad_reservada : 0;
            $stockReal = $stockDisponible - $stockReservado;

            // Si EN_TRANSITO y ubicación NO cambió, sumar cantidad original
            if ($traslado->estado === TrasladoStock::ESTADO_EN_TRANSITO && !$ubicacionCambio) {
                $stockReal += $cantidadOriginal;
            }

            if ($stockReal < $cantidadEditada) {
                $producto = Producto::find($productoId);
                $cambio = $cantidadEditada - $cantidadOriginal;
                $accion = $cantidadOriginal == 0 ? 'NUEVO' : ($cambio > 0 ? "AUMENTO +{$cambio}" : "REDUCCIÓN {$cambio}");

                return back()->withErrors([
                    'error' => "Stock insuficiente para {$producto->referencia} - {$producto->nombre}. " .
                              "Disponible: {$stockReal}, Solicitado: {$cantidadEditada}. " .
                              "Cambio: {$accion} (original: {$cantidadOriginal})"
                ])->withInput();
            }

            \Log::info("VALIDADO: {$referenciaProducto} ({$clave}) - {$cantidadOriginal} → {$cantidadEditada}");
        }

        // Capture old state for logging
        $estadoAnterior = [
            'origen' => $traslado->ubicacionOrigen->nombre,
            'destino' => $traslado->ubicacionDestino->nombre,
            'tipo_operacion' => $traslado->tipo_operacion,
            'notas' => $traslado->notas,
            'items' => $traslado->items->map(fn($i) => [
                'producto' => ($i->producto->referencia ?? '') . ' - ' . ($i->producto->nombre ?? ''),
                'variante' => $i->varianteProducto->nombre_variante ?? null,
                'cantidad' => $i->cantidad,
            ])->toArray(),
        ];

        DB::beginTransaction();
        try {
            // Si estaba en tránsito, devolver el stock de los ítems actuales al origen
            if ($traslado->estado === TrasladoStock::ESTADO_EN_TRANSITO) {
                \Log::info("=== REVERSIÓN DE STOCK (traslado EN_TRANSITO) ===");
                foreach ($traslado->items as $item) {
                    $producto = Producto::find($item->producto_id);
                    $referencia = $producto->referencia ?? '?';

                    $stockQuery = StockProducto::where('producto_id', $item->producto_id)
                        ->where(function($q) use ($traslado) {
                            $q->where('ubicacion_id', $traslado->ubicacion_origen_id)
                              ->orWhereNull('ubicacion_id');
                        });

                    if ($item->variante_producto_id) {
                        $stockQuery->where('variante_producto_id', $item->variante_producto_id);
                    } else {
                        $stockQuery->whereNull('variante_producto_id');
                    }

                    $stockOrigen = $stockQuery->first();
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
                            'tipo_operacion' => $traslado->tipo_operacion,
                            'motivo' => 'Edición de traslado - reversión',
                            'usuario_id' => auth()->id(),
                        ]);

                        \Log::info("REVERSIÓN: {$referencia} +{$item->cantidad} (stock: {$stockAnterior} → {$stockOrigen->cantidad_disponible})");
                    } else {
                        \Log::warning("REVERSIÓN FALLIDA: {$referencia} - no se encontró stock en origen");
                    }
                }
            }

            $traslado->update([
                'ubicacion_origen_id' => $request->ubicacion_origen_id,
                'ubicacion_destino_id' => $request->ubicacion_destino_id,
                'tipo_operacion' => $request->tipo_operacion,
                'notas' => $request->notas,
            ]);

            $traslado->items()->delete();

            $ubicacionDestinoNombre = Ubicacion::find($request->ubicacion_destino_id)->nombre;

            foreach ($request->items as $itemData) {
                $cantidad = (int) $itemData['cantidad'];
                $varianteId = $itemData['variante_producto_id'] ?? null;

                ItemTrasladoStock::create([
                    'traslado_stock_id' => $traslado->id,
                    'producto_id' => $itemData['producto_id'],
                    'variante_producto_id' => $varianteId,
                    'cantidad' => $cantidad,
                ]);

                // Si estaba en tránsito, descontar stock de los nuevos ítems
                if ($traslado->estado === TrasladoStock::ESTADO_EN_TRANSITO) {
                    $producto = Producto::find($itemData['producto_id']);
                    $referencia = $producto->referencia ?? '?';

                    // CRÍTICO: Solo descontar de la ubicación ORIGEN (o sin ubicación)
                    $stockQuery = StockProducto::where('producto_id', $itemData['producto_id'])
                        ->where(function($q) use ($request) {
                            $q->where('ubicacion_id', $request->ubicacion_origen_id)
                              ->orWhereNull('ubicacion_id');
                        });

                    if (!empty($varianteId)) {
                        $stockQuery->where('variante_producto_id', $varianteId);
                    } else {
                        $stockQuery->whereNull('variante_producto_id');
                    }

                    $stockOrigen = $stockQuery->first();

                    if ($stockOrigen) {
                        $stockAnterior = $stockOrigen->cantidad_disponible;
                        $stockOrigen->cantidad_disponible -= $cantidad;
                        $stockOrigen->save();

                        MovimientoStock::create([
                            'producto_id' => $itemData['producto_id'],
                            'variante_producto_id' => $varianteId,
                            'ubicacion_id' => $request->ubicacion_origen_id,
                            'tipo_movimiento' => 'salida',
                            'cantidad' => $cantidad,
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo' => $stockOrigen->cantidad_disponible,
                            'referencia_documento' => $traslado->numero_traslado,
                            'origen' => 'traslado',
                            'tipo_operacion' => $request->tipo_operacion,
                            'motivo' => 'Edición de traslado a ' . $ubicacionDestinoNombre,
                            'usuario_id' => auth()->id(),
                        ]);

                        \Log::info("DEDUCCIÓN: {$referencia} -{$cantidad} (stock: {$stockAnterior} → {$stockOrigen->cantidad_disponible})");
                    } else {
                        \Log::warning("DEDUCCIÓN FALLIDA: {$referencia} - no se encontró stock en origen");
                    }
                }
            }

            DB::commit();

            $estadoNuevo = [
                'origen' => Ubicacion::find($request->ubicacion_origen_id)->nombre,
                'destino' => Ubicacion::find($request->ubicacion_destino_id)->nombre,
                'tipo_operacion' => $request->tipo_operacion,
                'notas' => $request->notas,
                'items' => collect($request->items)->map(function($item) {
                    $prod = Producto::find($item['producto_id']);
                    return [
                        'producto' => $prod->referencia . ' - ' . $prod->nombre,
                        'cantidad' => $item['cantidad'],
                    ];
                })->values()->toArray(),
            ];

            LogTraslado::registrar($traslado->id, LogTraslado::ACCION_EDICION, [
                'editado_por' => auth()->user()->name,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
            ]);

            return redirect()->route('traslados')
                ->with('success', 'Traslado actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el traslado: ' . $e->getMessage()])
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

                // Liberar la reserva de traslado en el origen: al despachar, lo apartado se consume
                // (ya bajó el disponible arriba), por lo que la reserva debe soltarse.
                $stockOrigenRes = StockProducto::where('producto_id', $item->producto_id)
                    ->where('ubicacion_id', $traslado->ubicacion_origen_id)
                    ->when($item->variante_producto_id,
                        fn($q) => $q->where('variante_producto_id', $item->variante_producto_id),
                        fn($q) => $q->whereNull('variante_producto_id'))
                    ->lockForUpdate()
                    ->first();
                if ($stockOrigenRes && $stockOrigenRes->cantidad_reservada > 0) {
                    $stockOrigenRes->decrement('cantidad_reservada', min($item->cantidad, $stockOrigenRes->cantidad_reservada));
                }
            }

            $traslado->enviar();
            DB::commit();

            LogTraslado::registrar($traslado->id, LogTraslado::ACCION_ENVIO, [
                'enviado_por' => auth()->user()->name,
                'origen' => $traslado->ubicacionOrigen->nombre,
                'destino' => $traslado->ubicacionDestino->nombre,
                'items_enviados' => $traslado->items->count(),
            ]);

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

    public function recibir(Request $request, $id)
    {
        $user = auth()->user();
        $traslado = TrasladoStock::with(['items', 'ubicacionDestino'])->findOrFail($id);

        $destino = $traslado->ubicacionDestino;
        $destinoTipo = optional($destino)->tipo;
        $destinoEsBodega = $destinoTipo === Ubicacion::TIPO_BODEGA;
        $destinoEsTienda = $destinoTipo === Ubicacion::TIPO_TIENDA;

        $puedeRecibir = false;

        // Recepción en bodega: admin / auxiliar_administrativo / inventarios
        if ($destinoEsBodega && $user->hasRole(['admin', 'auxiliar_administrativo', 'inventarios'])) {
            $puedeRecibir = true;
        }
        // Recepción en tienda: admin / auxiliar_administrativo / centro_experiencia
        if ($destinoEsTienda && $user->hasRole(['admin', 'auxiliar_administrativo', 'centro_experiencia'])) {
            $puedeRecibir = true;
        }
        // Cajero solo puede recibir si la caja está en el destino y el destino es tienda
        if ($user->hasRole('cajero_principal') && $destinoEsTienda) {
            $cajaEnDestino = Caja::where('ubicacion_id', $traslado->ubicacion_destino_id)
                ->where('cajero_asignado_id', $user->id)
                ->exists();
            if ($cajaEnDestino) {
                $puedeRecibir = true;
            }
        }

        if (!$puedeRecibir) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para recibir este traslado.'
            ], 403);
        }

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

            if ($request->filled('observacion_recepcion')) {
                $traslado->observacion_recepcion = $request->observacion_recepcion;
                $traslado->save();
            }

            $traslado->completar(auth()->id());
            DB::commit();

            $logDetalle = [
                'recibido_por' => $user->name,
                'ubicacion_destino' => $traslado->ubicacionDestino->nombre,
                'items_recibidos' => $traslado->items->count(),
            ];
            if ($traslado->observacion_recepcion) {
                $logDetalle['observacion'] = $traslado->observacion_recepcion;
            }
            LogTraslado::registrar($traslado->id, LogTraslado::ACCION_RECEPCION, $logDetalle);

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

        $estabEnTransito = $traslado->estado === TrasladoStock::ESTADO_EN_TRANSITO;

        DB::beginTransaction();
        try {
            // Si ya estaba en tránsito, devolver el stock al origen para cada ítem
            if ($traslado->estado === TrasladoStock::ESTADO_EN_TRANSITO) {
                foreach ($traslado->items as $item) {
                    $stockOrigen = StockProducto::where('producto_id', $item->producto_id)
                        ->where(function($q) use ($traslado) {
                            $q->where('ubicacion_id', $traslado->ubicacion_origen_id)
                              ->orWhereNull('ubicacion_id');
                        });

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
            } else {
                // Estaba PENDIENTE: liberar la reserva de traslado del origen
                // (lo apartado vuelve a quedar disponible para venta).
                foreach ($traslado->items as $item) {
                    $stockOrigen = StockProducto::where('producto_id', $item->producto_id)
                        ->where('ubicacion_id', $traslado->ubicacion_origen_id)
                        ->when($item->variante_producto_id,
                            fn($q) => $q->where('variante_producto_id', $item->variante_producto_id),
                            fn($q) => $q->whereNull('variante_producto_id'))
                        ->lockForUpdate()
                        ->first();
                    if ($stockOrigen && $stockOrigen->cantidad_reservada > 0) {
                        $liberar = min($item->cantidad, $stockOrigen->cantidad_reservada);
                        $stockOrigen->decrement('cantidad_reservada', $liberar);

                        MovimientoStock::create([
                            'producto_id' => $item->producto_id,
                            'variante_producto_id' => $item->variante_producto_id,
                            'ubicacion_id' => $traslado->ubicacion_origen_id,
                            'tipo_movimiento' => 'liberacion',
                            'cantidad' => $liberar,
                            'stock_anterior' => $stockOrigen->cantidad_disponible,
                            'stock_nuevo' => $stockOrigen->cantidad_disponible,
                            'referencia_documento' => $traslado->numero_traslado,
                            'origen' => 'traslado',
                            'tipo_operacion' => $traslado->tipo_operacion ?? 'general',
                            'motivo' => 'Liberación de reserva por cancelación de traslado',
                            'usuario_id' => auth()->id(),
                        ]);
                    }
                }
            }

            $traslado->cancelar();
            DB::commit();

            LogTraslado::registrar($traslado->id, LogTraslado::ACCION_CANCELACION, [
                'cancelado_por' => auth()->user()->name,
                'estaba_en_transito' => $estabEnTransito,
                'stock_devuelto' => $estabEnTransito,
            ]);

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
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'variante_producto_id' => 'nullable|exists:variantes_productos,id',
            'traslado_id' => 'nullable|exists:traslados_stock,id',
        ]);

        // Filtrar por ubicación Y producto (incluir stock sin ubicación asignada)
        $query = StockProducto::where('producto_id', $request->producto_id)
            ->where(function($q) use ($request) {
                $q->where('ubicacion_id', $request->ubicacion_id)
                  ->orWhereNull('ubicacion_id');
            });

        if ($request->variante_producto_id) {
            $query->where('variante_producto_id', $request->variante_producto_id);
        } else {
            $query->whereNull('variante_producto_id');
        }

        // Stock real = disponible - reservado
        $stockDisponible = $query->sum('cantidad_disponible');
        $stockReservado = $query->sum('cantidad_reservada');
        $stockReal = $stockDisponible - $stockReservado;

        // Restar items en tránsito (excluyendo traslado actual si es edición)
        $enTransitoQuery = ItemTrasladoStock::where('producto_id', $request->producto_id)
            ->whereHas('traslado', function ($q) use ($request) {
                $q->where('estado', TrasladoStock::ESTADO_EN_TRANSITO)
                  ->where('ubicacion_origen_id', $request->ubicacion_id);
                if ($request->traslado_id) {
                    $q->where('id', '!=', $request->traslado_id);
                }
            });

        if ($request->variante_producto_id) {
            $enTransitoQuery->where('variante_producto_id', $request->variante_producto_id);
        } else {
            $enTransitoQuery->whereNull('variante_producto_id');
        }

        $enTransitoTotal = $enTransitoQuery->sum('cantidad');
        $stockEfectivo = $stockReal - $enTransitoTotal;

        // Si estamos editando un traslado en_transito, sumar de vuelta sus cantidades
        if ($request->traslado_id) {
            $traslado = TrasladoStock::find($request->traslado_id);
            if ($traslado && $traslado->estado === TrasladoStock::ESTADO_EN_TRANSITO) {
                $cantidadEnTraslado = $traslado->items()
                    ->where('producto_id', $request->producto_id);

                if ($request->variante_producto_id) {
                    $cantidadEnTraslado->where('variante_producto_id', $request->variante_producto_id);
                } else {
                    $cantidadEnTraslado->whereNull('variante_producto_id');
                }

                $cantidadEnTraslado = $cantidadEnTraslado->sum('cantidad');
                $stockEfectivo += $cantidadEnTraslado;
            }
        }

        $stockEfectivo = max(0, $stockEfectivo);

        return response()->json([
            'stock_disponible' => $stockEfectivo
        ]);
    }

    public function generarPdf($id)
    {
        $traslado = TrasladoStock::with([
            'ubicacionOrigen',
            'ubicacionDestino',
            'items.producto',
            'items.varianteProducto',
            'usuarioCreador',
            'usuarioReceptor'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.traslado', compact('traslado'));

        $nombreArchivo = 'Traslado_' . $traslado->numero_traslado . '.pdf';

        return $pdf->stream($nombreArchivo);
    }

    public function logs($id)
    {
        $logs = LogTraslado::where('traslado_stock_id', $id)
            ->with('usuario')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'accion' => $log->accion,
                    'accion_label' => $log->accion_label,
                    'accion_color' => $log->accion_color,
                    'accion_icon' => $log->accion_icon,
                    'usuario' => $log->usuario->name ?? 'Sistema',
                    'detalle' => $log->detalle,
                    'fecha' => $log->created_at->format('d/m/Y h:i:s A'),
                    'fecha_relativa' => $log->created_at->diffForHumans(),
                ];
            });

        return response()->json($logs);
    }
}
