<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Garantia;
use App\Models\GarantiaDocumento;
use App\Models\GarantiaItem;
use App\Models\GarantiaProductoLiberacion;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\StockProducto;
use App\Models\Ubicacion;
use App\Models\VarianteProducto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class GarantiaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Garantia::with([
                'cliente',
                'producto',
                'variante',
                'items.producto',
                'items.variante',
                'solicitud',
                'usuarioCreador',
                'usuarioLiberador',
                'documentos',
            ])->orderBy('created_at', 'desc');

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('cliente_id')) {
                $query->where('cliente_id', $request->cliente_id);
            }

            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }

            $puedeLiberar = auth()->user()->hasAnyRole(['admin', 'garantias']);

            return DataTables::of($query)
                ->addColumn('id_badge', function ($row) {
                    return '<span class="badge bg-secondary">#' . $row->id . '</span>';
                })
                ->addColumn('cliente_nombre', function ($row) {
                    return $row->cliente->nombre_completo ?? 'N/A';
                })
                ->addColumn('producto_nombre', function ($row) {
                    return $row->itemsResumen();
                })
                ->addColumn('tipo_badge', function ($row) {
                    $tipos = Garantia::tiposDisponibles();
                    $colores = [
                        Garantia::TIPO_CAMBIO_PRODUCTO => 'primary',
                        Garantia::TIPO_DESCUENTO => 'info',
                        Garantia::TIPO_NOTA_CREDITO => 'warning',
                        Garantia::TIPO_OTRO => 'secondary',
                    ];
                    $color = $colores[$row->tipo] ?? 'secondary';
                    $label = $tipos[$row->tipo] ?? $row->tipo;
                    return '<span class="badge bg-' . $color . '">' . e($label) . '</span>';
                })
                ->addColumn('estado_badge', function ($row) {
                    if ($row->estado === Garantia::ESTADO_PENDIENTE) {
                        return '<span class="badge bg-warning text-dark">Pendiente</span>';
                    }
                    return '<span class="badge bg-success">Liberado</span>';
                })
                ->addColumn('cotizacion_vinculada', function ($row) {
                    return $row->solicitud?->numero_solicitud ?? '—';
                })
                ->addColumn('fecha_creacion', function ($row) {
                    return $row->created_at?->format('d/m/Y H:i');
                })
                ->addColumn('action', function ($row) use ($puedeLiberar) {
                    $btns = '<div class="d-flex gap-1 flex-nowrap justify-content-center" style="white-space:nowrap;">';
                    $btns .= '<button type="button" class="btn btn-sm btn-info text-white flex-shrink-0" onclick="verGarantia(' . $row->id . ')" title="Ver"><i class="bi bi-eye"></i></button>';
                    $btns .= '<a href="' . route('garantias.pdf', $row->id) . '" class="btn btn-sm btn-danger flex-shrink-0" title="Descargar PDF" target="_blank"><i class="bi bi-file-earmark-pdf"></i></a>';
                    if ($puedeLiberar && $row->estaPendiente()) {
                        $btns .= '<button type="button" class="btn btn-sm btn-success flex-shrink-0" onclick="liberarGarantia(' . $row->id . ')" title="Liberar"><i class="bi bi-unlock"></i></button>';
                    }
                    $btns .= '</div>';
                    return $btns;
                })
                ->rawColumns(['action', 'id_badge', 'tipo_badge', 'estado_badge'])
                ->make(true);
        }

        $clientes = Cliente::activos()->orderBy('nombre_contacto')->get(['id', 'nombre_contacto', 'razon_social', 'tipo_cliente']);
        $tipos = Garantia::tiposDisponibles();
        $ubicaciones = Ubicacion::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'tipo']);

        return view('garantias.index', compact('clientes', 'tipos', 'ubicaciones'));
    }

    public function create()
    {
        $clientes = Cliente::activos()->orderBy('nombre_contacto')->get(['id', 'nombre_contacto', 'razon_social', 'tipo_cliente', 'numero_identificacion']);
        $tipos = Garantia::tiposDisponibles();

        return view('garantias.form', compact('clientes', 'tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            // El producto es OPCIONAL: la garantía puede registrarse sin productos.
            // Si se agregan, cada uno debe tener producto y cantidad (>= 1).
            'items' => 'nullable|array',
            'items.*.producto_id' => 'required|integer|exists:productos,id',
            'items.*.variante_producto_id' => 'nullable|integer|exists:variantes_productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'tipo' => 'required|in:cambio_producto,descuento,nota_credito,otro',
            'tipo_otro_descripcion' => 'required_if:tipo,otro|nullable|max:500',
            'observacion_creacion' => 'nullable|string|max:1000',
            'documentos' => 'nullable|array',
            'documentos.*' => 'file|max:10240',
        ], [
            'documentos.*.max' => 'Cada documento no puede superar los 10MB.',
            'tipo_otro_descripcion.required_if' => 'Debes especificar el tipo cuando seleccionas "Otro".',
            'items.*.producto_id.required' => 'Cada producto agregado debe estar seleccionado.',
            'items.*.cantidad.required' => 'Cada producto agregado debe tener una cantidad.',
            'items.*.cantidad.min' => 'La cantidad de cada producto debe ser al menos 1.',
        ]);

        $items = $request->input('items', []) ?: [];

        // Cada variante debe pertenecer a su producto.
        foreach ($items as $idx => $item) {
            if (!empty($item['variante_producto_id'])) {
                $pertenece = VarianteProducto::where('id', $item['variante_producto_id'])
                    ->where('producto_id', $item['producto_id'])
                    ->exists();
                if (!$pertenece) {
                    return back()->withInput()->with('error', 'La variante del producto #' . ($idx + 1) . ' no pertenece al producto seleccionado.');
                }
            }
        }

        DB::beginTransaction();
        try {
            $garantia = Garantia::create([
                'cliente_id' => $request->cliente_id,
                // Los productos reclamados viven ahora en garantia_items.
                'producto_id' => null,
                'variante_producto_id' => null,
                'tipo' => $request->tipo,
                'tipo_otro_descripcion' => $request->tipo === Garantia::TIPO_OTRO ? $request->tipo_otro_descripcion : null,
                'observacion_creacion' => $request->filled('observacion_creacion') ? trim($request->observacion_creacion) : null,
                'estado' => Garantia::ESTADO_PENDIENTE,
                'usuario_creador_id' => auth()->id(),
            ]);

            foreach ($items as $item) {
                GarantiaItem::create([
                    'garantia_id' => $garantia->id,
                    'producto_id' => $item['producto_id'],
                    'variante_producto_id' => $item['variante_producto_id'] ?? null,
                    'cantidad' => (int) ($item['cantidad'] ?? 1),
                ]);
            }

            // Los documentos son opcionales: solo se procesan si se adjuntó alguno.
            if ($request->hasFile('documentos')) {
                $directorio = public_path('uploads/garantias/documentos/' . $garantia->id);
                if (!File::exists($directorio)) {
                    File::makeDirectory($directorio, 0755, true);
                }

                foreach ($request->file('documentos') as $archivo) {
                    if (!$archivo || !$archivo->isValid()) {
                        throw new \Exception('Archivo inválido o subida fallida: ' . ($archivo?->getClientOriginalName() ?? 'desconocido'));
                    }

                    $nombreOriginal = $archivo->getClientOriginalName();
                    $mimeType = $archivo->getClientMimeType() ?: 'application/octet-stream';
                    $tamano = $archivo->getSize();
                    $nombreSeguro = preg_replace('/[^A-Za-z0-9._-]/', '_', $nombreOriginal);
                    $nombre = time() . '_' . uniqid() . '_' . $nombreSeguro;

                    $rutaTemp = $archivo->getPathname();
                    $rutaDestino = $directorio . DIRECTORY_SEPARATOR . $nombre;

                    if (!file_exists($rutaTemp) || !is_readable($rutaTemp)) {
                        throw new \Exception('El archivo temporal no se encuentra disponible: ' . $rutaTemp);
                    }

                    if (!@copy($rutaTemp, $rutaDestino)) {
                        throw new \Exception('No se pudo guardar el archivo: ' . $nombreOriginal);
                    }
                    @unlink($rutaTemp);

                    GarantiaDocumento::create([
                        'garantia_id' => $garantia->id,
                        'nombre_original' => $nombreOriginal,
                        'nombre_archivo' => $nombre,
                        'ruta_relativa' => 'uploads/garantias/documentos/' . $garantia->id . '/' . $nombre,
                        'mime_type' => $mimeType,
                        'tamano' => $tamano,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('garantias.index')->with('success', 'Garantía registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error al registrar garantía', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['documentos']),
            ]);
            return back()->withInput()->with('error', 'Error al registrar la garantía: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $garantia = Garantia::with([
            'cliente',
            'producto',
            'variante',
            'items.producto',
            'items.variante',
            'solicitud',
            'usuarioCreador',
            'usuarioLiberador',
            'documentos',
            'productosLiberacion.producto',
            'productosLiberacion.variante',
            'productosLiberacion.ubicacionRelacion',
        ])->findOrFail($id);

        return response()->json([
            'id' => $garantia->id,
            'cliente' => $garantia->cliente?->nombre_completo,
            'producto' => $garantia->producto?->nombre,
            'variante' => $garantia->variante?->nombre_variante,
            'items' => $garantia->items->map(function ($it) {
                return [
                    'producto' => $it->producto?->nombre,
                    'variante' => $it->variante?->nombre_variante,
                    'cantidad' => (int) $it->cantidad,
                ];
            }),
            'tipo' => $garantia->tipo,
            'tipo_legible' => $garantia->tipoLegible(),
            'tipo_otro_descripcion' => $garantia->tipo_otro_descripcion,
            'observacion_creacion' => $garantia->observacion_creacion,
            'estado' => $garantia->estado,
            'observacion_liberacion' => $garantia->observacion_liberacion,
            'usuario_creador' => $garantia->usuarioCreador?->name,
            'usuario_liberador' => $garantia->usuarioLiberador?->name,
            'liberado_en' => $garantia->liberado_en?->format('d/m/Y H:i'),
            'created_at' => $garantia->created_at?->format('d/m/Y H:i'),
            'cotizacion' => $garantia->solicitud ? [
                'id' => $garantia->solicitud->id,
                'numero' => $garantia->solicitud->numero_solicitud,
            ] : null,
            'documentos' => $garantia->documentos->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'nombre_original' => $doc->nombre_original,
                    'tamano' => $doc->tamano,
                    'mime_type' => $doc->mime_type,
                    'url_descarga' => route('garantias.documentos.descargar', $doc->id),
                ];
            }),
            'productos_liberacion' => $garantia->productosLiberacion->map(function ($p) {
                return [
                    'producto' => $p->producto?->nombre,
                    'variante' => $p->variante?->nombre_variante,
                    'ubicacion' => $p->ubicacionRelacion?->nombre,
                    'cantidad' => (int) $p->cantidad,
                ];
            }),
        ]);
    }

    public function liberar(Request $request, $id)
    {
        $request->validate([
            'observacion_liberacion' => 'required|string|min:5|max:1000',
            'solicitud_cotizacion_id' => 'nullable|exists:solicitudes_cotizacion,id',
            'items' => 'nullable|array',
            'ubicacion_id' => 'required_with:items|exists:ubicaciones,id',
            'items.*.producto_id' => 'required|integer|exists:productos,id',
            'items.*.variante_producto_id' => [
                'nullable',
                'integer',
                'exists:variantes_productos,id',
            ],
            'items.*.cantidad' => 'required|integer|min:1',
        ], [
            'observacion_liberacion.required' => 'La observación es obligatoria para liberar la garantía.',
            'observacion_liberacion.min' => 'La observación debe tener al menos 5 caracteres.',
            'ubicacion_id.required_with' => 'Debes seleccionar una ubicación cuando agregas productos de cambio.',
        ]);

        $items = $request->input('items', []) ?: [];
        $ubicacionId = $request->input('ubicacion_id');

        foreach ($items as $idx => $item) {
            if (!empty($item['variante_producto_id'])) {
                $pertenece = VarianteProducto::where('id', $item['variante_producto_id'])
                    ->where('producto_id', $item['producto_id'])
                    ->exists();
                if (!$pertenece) {
                    return response()->json([
                        'error' => "La variante seleccionada no pertenece al producto (item " . ($idx + 1) . ").",
                    ], 422);
                }
            }
        }

        try {
            DB::transaction(function () use ($id, $request, $items, $ubicacionId) {
                $garantia = Garantia::lockForUpdate()->findOrFail($id);

                if ($garantia->estaLiberada()) {
                    throw new \RuntimeException('La garantía ya está liberada.');
                }

                $stocks = [];
                foreach ($items as $idx => $item) {
                    $stock = StockProducto::where('producto_id', $item['producto_id'])
                        ->where('ubicacion_id', $ubicacionId)
                        ->where(function ($q) use ($item) {
                            if (!empty($item['variante_producto_id'])) {
                                $q->where('variante_producto_id', $item['variante_producto_id']);
                            } else {
                                $q->whereNull('variante_producto_id');
                            }
                        })
                        ->lockForUpdate()
                        ->first();

                    $disponibleReal = $stock ? ($stock->cantidad_disponible - $stock->cantidad_reservada) : 0;
                    if (!$stock || $disponibleReal < (int) $item['cantidad']) {
                        $producto = Producto::find($item['producto_id']);
                        $nombre = $producto?->nombre ?? ('ID ' . $item['producto_id']);
                        throw new \RuntimeException("Stock insuficiente para '{$nombre}'. Disponible: {$disponibleReal}, solicitado: {$item['cantidad']}.");
                    }

                    $stocks[$idx] = $stock;
                }

                $garantia->update([
                    'estado' => Garantia::ESTADO_LIBERADO,
                    'observacion_liberacion' => $request->observacion_liberacion,
                    'usuario_liberador_id' => auth()->id(),
                    'liberado_en' => now(),
                    'solicitud_cotizacion_id' => $request->solicitud_cotizacion_id ?: $garantia->solicitud_cotizacion_id,
                ]);

                foreach ($items as $idx => $item) {
                    $stock = $stocks[$idx];
                    $cantidad = (int) $item['cantidad'];
                    $stockAnterior = $stock->cantidad_disponible;
                    $stock->cantidad_disponible = $stockAnterior - $cantidad;
                    $stock->save();

                    $movimiento = MovimientoStock::create([
                        'producto_id' => $stock->producto_id,
                        'variante_producto_id' => $stock->variante_producto_id,
                        'ubicacion_id' => $ubicacionId,
                        'tipo_movimiento' => 'salida',
                        'cantidad' => $cantidad,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stock->cantidad_disponible,
                        'referencia_documento' => 'GAR-' . $garantia->id,
                        'origen' => 'garantia',
                        'motivo' => 'Cambio por garantía #' . $garantia->id,
                        'usuario_id' => auth()->id() ?? 1,
                    ]);

                    GarantiaProductoLiberacion::create([
                        'garantia_id' => $garantia->id,
                        'producto_id' => $stock->producto_id,
                        'variante_producto_id' => $stock->variante_producto_id,
                        'ubicacion_id' => $ubicacionId,
                        'cantidad' => $cantidad,
                        'movimiento_stock_id' => $movimiento->id,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            \Log::warning('Error al liberar garantía', [
                'garantia_id' => $id,
                'mensaje' => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Garantía liberada correctamente.',
        ]);
    }

    public function descargarPdf($id)
    {
        $garantia = Garantia::with([
            'cliente',
            'items.producto',
            'items.variante',
            'solicitud',
            'usuarioCreador',
            'usuarioLiberador',
            'documentos',
            'productosLiberacion.producto',
            'productosLiberacion.variante',
            'productosLiberacion.ubicacionRelacion',
        ])->findOrFail($id);

        $logoPath = public_path('images/logo.png');
        $logo = File::exists($logoPath) ? $logoPath : null;

        $pdf = Pdf::loadView('garantias.pdf', compact('garantia', 'logo'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('garantia-' . $garantia->id . '.pdf');
    }

    public function productosPorUbicacion($ubicacionId)
    {
        $stockItems = StockProducto::where('ubicacion_id', $ubicacionId)
            ->whereRaw('(cantidad_disponible - cantidad_reservada) > 0')
            ->get();

        $productosIds = $stockItems->pluck('producto_id')->unique();

        $productos = Producto::whereIn('id', $productosIds)
            ->where('eliminado', false)
            ->where('activo', true)
            ->where('controlar_stock', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($producto) use ($stockItems) {
                $delProducto = $stockItems->where('producto_id', $producto->id);
                $stockReal = $delProducto->sum('cantidad_disponible') - $delProducto->sum('cantidad_reservada');

                if ($stockReal <= 0) {
                    return null;
                }

                $tieneVariantesConStock = $delProducto->whereNotNull('variante_producto_id')->count() > 0;

                return [
                    'id' => $producto->id,
                    'referencia' => $producto->referencia,
                    'nombre' => $producto->nombre,
                    'tiene_variantes' => (bool) $producto->tiene_variantes && $tieneVariantesConStock,
                    'stock_disponible' => (int) $stockReal,
                ];
            })
            ->filter()
            ->values();

        return response()->json(['productos' => $productos]);
    }

    public function variantesPorProductoYUbicacion($productoId, $ubicacionId)
    {
        $producto = Producto::with('variantes')->findOrFail($productoId);

        if (!$producto->tiene_variantes) {
            return response()->json([
                'tiene_variantes' => false,
                'variantes' => [],
            ]);
        }

        $stockItems = StockProducto::where('producto_id', $productoId)
            ->where('ubicacion_id', $ubicacionId)
            ->whereNotNull('variante_producto_id')
            ->whereRaw('(cantidad_disponible - cantidad_reservada) > 0')
            ->get();

        $varianteIds = $stockItems->pluck('variante_producto_id')->unique();

        $variantes = $producto->variantes()
            ->whereIn('id', $varianteIds)
            ->get()
            ->map(function ($variante) use ($stockItems) {
                $delVariante = $stockItems->where('variante_producto_id', $variante->id);
                $stockReal = $delVariante->sum('cantidad_disponible') - $delVariante->sum('cantidad_reservada');

                if ($stockReal <= 0) {
                    return null;
                }

                return [
                    'id' => $variante->id,
                    'nombre_variante' => $variante->nombre_variante,
                    'stock_disponible' => (int) $stockReal,
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'tiene_variantes' => true,
            'variantes' => $variantes,
        ]);
    }

    public function descargarDocumento($id)
    {
        $documento = GarantiaDocumento::findOrFail($id);
        $rutaCompleta = public_path($documento->ruta_relativa);

        if (!File::exists($rutaCompleta)) {
            abort(404, 'El documento no existe.');
        }

        return response()->download($rutaCompleta, $documento->nombre_original);
    }

    public function buscarProductos(Request $request)
    {
        $termino = trim($request->get('q', ''));

        if (mb_strlen($termino) < 2) {
            return response()->json(['data' => []]);
        }

        $productos = Producto::activos()
            ->where(function ($query) use ($termino) {
                $query->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('referencia', 'like', "%{$termino}%")
                    ->orWhere('codigo_barras', 'like', "%{$termino}%")
                    ->orWhereHas('variantes', function ($q) use ($termino) {
                        $q->where('codigo_barras', 'like', "%{$termino}%")
                          ->orWhere('sku', 'like', "%{$termino}%")
                          ->orWhere('referencia_variante', 'like', "%{$termino}%")
                          ->orWhere('color', 'like', "%{$termino}%");
                    });
            })
            ->with(['variantes'])
            ->limit(40)
            ->get();

        $filas = [];
        foreach ($productos as $producto) {
            if ($producto->tiene_variantes) {
                if ($producto->variantes->isEmpty()) {
                    $producto->load('variantes');
                }
                foreach ($producto->variantes as $variante) {
                    $filas[] = [
                        'producto_id' => $producto->id,
                        'variante_producto_id' => $variante->id,
                        'referencia' => $variante->referencia_variante ?: $producto->referencia,
                        'nombre_completo' => $producto->nombre . ($variante->nombre_variante ? ' — ' . $variante->nombre_variante : ''),
                        'sku' => $variante->sku,
                        'codigo_barras' => $variante->codigo_barras ?: $producto->codigo_barras,
                        'tiene_variante' => true,
                    ];
                }
            } else {
                $filas[] = [
                    'producto_id' => $producto->id,
                    'variante_producto_id' => null,
                    'referencia' => $producto->referencia,
                    'nombre_completo' => $producto->nombre,
                    'sku' => null,
                    'codigo_barras' => $producto->codigo_barras,
                    'tiene_variante' => false,
                ];
            }
        }

        return response()->json(['data' => array_slice($filas, 0, 30)]);
    }

    public function garantiasPendientesCliente($clienteId)
    {
        $cliente = Cliente::findOrFail($clienteId);

        $garantias = $cliente->garantiasPendientes()
            ->with(['producto', 'variante', 'items.producto', 'items.variante', 'documentos', 'usuarioCreador'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $garantias->map(function ($g) {
                return [
                    'id' => $g->id,
                    'producto' => $g->itemsResumen(),
                    'variante' => null,
                    'items' => $g->items->map(fn($it) => [
                        'producto' => $it->producto?->nombre,
                        'variante' => $it->variante?->nombre_variante,
                        'cantidad' => (int) $it->cantidad,
                    ]),
                    'tipo' => $g->tipo,
                    'tipo_legible' => $g->tipoLegible(),
                    'observacion_creacion' => $g->observacion_creacion,
                    'fecha' => $g->created_at?->format('d/m/Y H:i'),
                    'usuario_creador' => $g->usuarioCreador?->name,
                    'documentos' => $g->documentos->map(fn($d) => [
                        'id' => $d->id,
                        'nombre_original' => $d->nombre_original,
                        'url_descarga' => route('garantias.documentos.descargar', $d->id),
                    ]),
                ];
            }),
        ]);
    }
}
