<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VentaPdv;
use App\Models\ItemVentaPdv;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Ubicacion;
use App\Models\ListaPrecio;
use App\Services\PuntoVentaService;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PuntoVentaController extends Controller
{
    protected PuntoVentaService $puntoVentaService;

    public function __construct(PuntoVentaService $puntoVentaService)
    {
        $this->puntoVentaService = $puntoVentaService;
    }

    /**
     * Dashboard del Punto de Venta
     */
    public function dashboard(Request $request)
    {
        $ubicacionId = $request->ubicacion_id ?? session('pdv_ubicacion_id');

        // Si no hay ubicación seleccionada, redirigir a selección
        if (!$ubicacionId) {
            $ubicaciones = Ubicacion::where('activo', true)
                ->where('tipo', 'tienda')
                ->get();

            if ($ubicaciones->count() === 1) {
                $ubicacionId = $ubicaciones->first()->id;
                session(['pdv_ubicacion_id' => $ubicacionId]);
            } elseif ($ubicaciones->isEmpty()) {
                // Si no hay tiendas, mostrar todas las ubicaciones
                $ubicaciones = Ubicacion::where('activo', true)->get();
            }

            if (!$ubicacionId && $ubicaciones->count() > 1) {
                return view('punto-venta.seleccionar-ubicacion', compact('ubicaciones'));
            }
        }

        $ubicacion = Ubicacion::find($ubicacionId);

        // Métricas del día
        $metricasDia = $this->puntoVentaService->obtenerMetricasDelDia($ubicacionId);

        // Métricas del mes
        $metricasMes = $this->puntoVentaService->obtenerMetricasDelMes($ubicacionId);

        // Productos más vendidos del mes
        $productosTop = $this->puntoVentaService->obtenerProductosMasVendidos($ubicacionId, 'mes', 5);

        // Últimas ventas del día
        $ultimasVentas = VentaPdv::with(['usuario', 'items'])
            ->porUbicacion($ubicacionId)
            ->delDia()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Ubicaciones para selector
        $ubicaciones = Ubicacion::where('activo', true)->get();

        return view('punto-venta.dashboard', compact(
            'ubicacion',
            'ubicacionId',
            'metricasDia',
            'metricasMes',
            'productosTop',
            'ultimasVentas',
            'ubicaciones'
        ));
    }

    /**
     * Cambiar ubicación del PdV
     */
    public function cambiarUbicacion(Request $request)
    {
        $request->validate([
            'ubicacion_id' => 'required|exists:ubicaciones,id',
        ]);

        session(['pdv_ubicacion_id' => $request->ubicacion_id]);

        return redirect()->route('punto-venta.dashboard')
            ->with('success', 'Ubicación cambiada correctamente');
    }

    /**
     * Interfaz de nueva venta
     */
    public function nuevaVenta(Request $request)
    {
        $ubicacionId = session('pdv_ubicacion_id');

        if (!$ubicacionId) {
            return redirect()->route('punto-venta.dashboard')
                ->with('warning', 'Primero seleccione una ubicación');
        }

        $ubicacion = Ubicacion::findOrFail($ubicacionId);

        // Clientes para selector
        $clientes = Cliente::where('activo', true)
            ->orderBy('nombre_contacto')
            ->get(['id', 'razon_social', 'nombre_contacto', 'tipo_cliente', 'telefono', 'lista_precio_id']);

        // Lista de precios por defecto (local1 o la primera activa)
        $listaPrecio = ListaPrecio::where('activo', true)
            ->orderBy('nombre')
            ->first();

        // Listas de precios disponibles
        $listasPrecios = ListaPrecio::where('activo', true)->get();

        return view('punto-venta.venta', compact(
            'ubicacion',
            'ubicacionId',
            'clientes',
            'listaPrecio',
            'listasPrecios'
        ));
    }

    /**
     * Buscar productos (AJAX)
     */
    public function buscarProductos(Request $request)
    {
        $ubicacionId = session('pdv_ubicacion_id');
        $listaPrecioId = $request->lista_precio_id;
        $termino = $request->q ?? '';

        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $productos = $this->puntoVentaService->buscarProductos(
            $termino,
            $ubicacionId,
            $listaPrecioId,
            20
        );

        return response()->json($productos);
    }

    /**
     * Obtener datos de un producto específico (AJAX)
     */
    public function obtenerProducto(Request $request)
    {
        $ubicacionId = session('pdv_ubicacion_id');
        $productoId = $request->producto_id;
        $varianteId = $request->variante_id;
        $listaPrecioId = $request->lista_precio_id;

        $producto = Producto::with(['variantes', 'precios', 'stock' => function ($q) use ($ubicacionId) {
            $q->where('ubicacion_id', $ubicacionId);
        }])->find($productoId);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $precio = $producto->getPrecioPorLista($listaPrecioId);
        $stock = $producto->stock->first();
        $stockDisponible = $stock ? ($stock->cantidad_disponible - $stock->cantidad_reservada) : 0;

        $data = [
            'id' => $producto->id,
            'referencia' => $producto->referencia,
            'nombre' => $producto->nombre,
            'precio' => $precio ?? 0,
            'stock_disponible' => $stockDisponible,
            'controla_stock' => $producto->controlar_stock,
            'permite_sin_stock' => $producto->permitir_venta_sin_stock,
            'imagen_url' => $producto->url_imagen_principal,
        ];

        if ($varianteId) {
            $variante = $producto->variantes->find($varianteId);
            if ($variante) {
                $precioVariante = $variante->precios()->where('lista_precio_id', $listaPrecioId)->first();
                $stockVariante = $producto->stock
                    ->where('variante_producto_id', $varianteId)
                    ->first();

                $data['variante'] = [
                    'id' => $variante->id,
                    'sku' => $variante->sku,
                    'referencia_variante' => $variante->referencia_variante,
                    'color' => $variante->color,
                    'precio' => $precioVariante ? $precioVariante->precio : $precio,
                    'stock_disponible' => $stockVariante
                        ? ($stockVariante->cantidad_disponible - $stockVariante->cantidad_reservada)
                        : 0,
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Procesar venta (AJAX)
     */
    public function procesarVenta(Request $request)
    {
        $request->validate([
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,mixto',
        ]);

        $datosVenta = [
            'ubicacion_id' => $request->ubicacion_id,
            'cliente_id' => $request->cliente_id,
            'nombre_cliente' => $request->nombre_cliente,
            'descuento' => $request->descuento ?? 0,
            'metodo_pago' => $request->metodo_pago,
            'monto_efectivo' => $request->monto_efectivo,
            'monto_tarjeta' => $request->monto_tarjeta,
            'monto_transferencia' => $request->monto_transferencia,
            'notas' => $request->notas,
        ];

        $items = collect($request->items)->map(function ($item) {
            return [
                'producto_id' => $item['producto_id'],
                'variante_producto_id' => $item['variante_producto_id'] ?? null,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'descuento' => $item['descuento'] ?? 0,
                'iva' => $item['iva'] ?? 0,
            ];
        })->toArray();

        $resultado = $this->puntoVentaService->crearVenta(
            $datosVenta,
            $items,
            auth()->id()
        );

        if ($resultado['exito']) {
            return response()->json([
                'success' => true,
                'message' => $resultado['mensaje'],
                'venta' => $resultado['venta'],
                'venta_id' => $resultado['venta']->id,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $resultado['mensaje'],
        ], 422);
    }

    /**
     * Listado de ventas
     */
    public function index(Request $request)
    {
        $ubicacionId = $request->ubicacion_id ?? session('pdv_ubicacion_id');

        if ($request->ajax()) {
            $query = VentaPdv::with(['ubicacion', 'cliente', 'usuario', 'items'])
                ->select('ventas_pdv.*');

            if ($ubicacionId) {
                $query->where('ubicacion_id', $ubicacionId);
            }

            // Filtro por estado
            if ($request->estado) {
                $query->where('estado', $request->estado);
            }

            // Filtro por fecha
            if ($request->fecha_desde) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }
            if ($request->fecha_hasta) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }

            // Filtro por método de pago
            if ($request->metodo_pago) {
                $query->where('metodo_pago', $request->metodo_pago);
            }

            return DataTables::of($query)
                ->addColumn('fecha', function ($venta) {
                    return $venta->created_at->format('d/m/Y H:i');
                })
                ->addColumn('cliente_nombre', function ($venta) {
                    return $venta->nombre_cliente_display;
                })
                ->addColumn('ubicacion_nombre', function ($venta) {
                    return $venta->ubicacion->nombre ?? '-';
                })
                ->addColumn('total_formateado', function ($venta) {
                    return '$' . number_format($venta->total, 0, ',', '.');
                })
                ->addColumn('estado_badge', function ($venta) {
                    $badge = $venta->estado === 'completada' ? 'success' : 'danger';
                    $texto = $venta->estado === 'completada' ? 'Completada' : 'Anulada';
                    return '<span class="badge bg-' . $badge . '">' . $texto . '</span>';
                })
                ->addColumn('metodo_pago_badge', function ($venta) {
                    $colores = [
                        'efectivo' => 'success',
                        'tarjeta' => 'primary',
                        'transferencia' => 'info',
                        'mixto' => 'warning',
                    ];
                    $color = $colores[$venta->metodo_pago] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($venta->metodo_pago) . '</span>';
                })
                ->addColumn('items_count', function ($venta) {
                    return $venta->items->sum('cantidad') . ' items';
                })
                ->addColumn('vendedor', function ($venta) {
                    return $venta->usuario->name ?? '-';
                })
                ->addColumn('action', function ($venta) {
                    $buttons = '<div class="btn-group btn-group-sm">';

                    // Ver detalle
                    $buttons .= '<button type="button" class="btn btn-info" onclick="verDetalle(' . $venta->id . ')" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </button>';

                    // Imprimir ticket
                    $buttons .= '<a href="' . route('punto-venta.ticket', $venta->id) . '" class="btn btn-secondary" target="_blank" title="Ticket">
                                    <i class="bi bi-printer"></i>
                                </a>';

                    // Anular (solo si está completada)
                    if ($venta->estado === 'completada') {
                        $buttons .= '<button type="button" class="btn btn-danger" onclick="anularVenta(' . $venta->id . ')" title="Anular">
                                        <i class="bi bi-x-circle"></i>
                                    </button>';
                    }

                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['estado_badge', 'metodo_pago_badge', 'action'])
                ->orderColumn('created_at', 'created_at $1')
                ->make(true);
        }

        $ubicaciones = Ubicacion::where('activo', true)->get();
        $ubicacionSeleccionada = $ubicacionId ? Ubicacion::find($ubicacionId) : null;

        return view('punto-venta.index', compact('ubicaciones', 'ubicacionSeleccionada', 'ubicacionId'));
    }

    /**
     * Detalle de una venta (AJAX)
     */
    public function detalle($id)
    {
        $venta = VentaPdv::with([
            'ubicacion',
            'cliente',
            'usuario',
            'anulador',
            'items.producto',
            'items.variante',
        ])->findOrFail($id);

        return response()->json([
            'venta' => $venta,
            'html' => view('punto-venta.partials.detalle-venta', compact('venta'))->render(),
        ]);
    }

    /**
     * Anular venta (AJAX)
     */
    public function anular(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string|min:10',
        ]);

        $venta = VentaPdv::findOrFail($id);

        $resultado = $this->puntoVentaService->anularVenta(
            $venta,
            auth()->id(),
            $request->motivo
        );

        if ($resultado['exito']) {
            return response()->json([
                'success' => true,
                'message' => $resultado['mensaje'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $resultado['mensaje'],
        ], 422);
    }

    /**
     * Generar ticket de venta (PDF)
     */
    public function ticket($id)
    {
        $venta = VentaPdv::with([
            'ubicacion',
            'cliente',
            'usuario',
            'items.producto',
            'items.variante',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('punto-venta.ticket', compact('venta'));

        // Configurar tamaño ticket (80mm de ancho)
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait'); // 80mm = 226.77 puntos

        return $pdf->stream('ticket_' . $venta->numero_venta . '.pdf');
    }

    /**
     * Exportar ventas a Excel
     */
    public function exportar(Request $request)
    {
        $fechaDesde = $request->fecha_desde ?? now()->startOfMonth()->toDateString();
        $fechaHasta = $request->fecha_hasta ?? now()->toDateString();
        $ubicacionId = $request->ubicacion_id;

        $query = VentaPdv::with(['ubicacion', 'cliente', 'usuario', 'items.producto'])
            ->whereDate('created_at', '>=', $fechaDesde)
            ->whereDate('created_at', '<=', $fechaHasta);

        if ($ubicacionId) {
            $query->where('ubicacion_id', $ubicacionId);
        }

        $ventas = $query->orderBy('created_at', 'desc')->get();

        // Por ahora retornar JSON, implementar Excel después
        return response()->json([
            'total_ventas' => $ventas->count(),
            'monto_total' => $ventas->where('estado', 'completada')->sum('total'),
            'ventas' => $ventas,
        ]);
    }

    /**
     * Reporte de ventas por período
     */
    public function reporte(Request $request)
    {
        $ubicacionId = $request->ubicacion_id ?? session('pdv_ubicacion_id');
        $fechaDesde = $request->fecha_desde ?? now()->startOfMonth()->toDateString();
        $fechaHasta = $request->fecha_hasta ?? now()->toDateString();

        $query = VentaPdv::completadas()
            ->whereDate('created_at', '>=', $fechaDesde)
            ->whereDate('created_at', '<=', $fechaHasta);

        if ($ubicacionId) {
            $query->where('ubicacion_id', $ubicacionId);
        }

        $ventas = $query->get();

        // Resumen general
        $resumen = [
            'total_ventas' => $ventas->count(),
            'monto_total' => $ventas->sum('total'),
            'monto_promedio' => $ventas->count() > 0 ? $ventas->avg('total') : 0,
            'total_items' => $ventas->flatMap->items->sum('cantidad'),
        ];

        // Por método de pago
        $porMetodoPago = $ventas->groupBy('metodo_pago')->map(function ($grupo) {
            return [
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('total'),
            ];
        });

        // Por día
        $porDia = $ventas->groupBy(function ($venta) {
            return $venta->created_at->format('Y-m-d');
        })->map(function ($grupo) {
            return [
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('total'),
            ];
        })->sortKeys();

        // Ventas anuladas en el período
        $anuladas = VentaPdv::anuladas()
            ->whereDate('anulada_en', '>=', $fechaDesde)
            ->whereDate('anulada_en', '<=', $fechaHasta);

        if ($ubicacionId) {
            $anuladas->where('ubicacion_id', $ubicacionId);
        }

        $totalAnuladas = $anuladas->count();
        $montoAnulado = $anuladas->sum('total');

        $ubicaciones = Ubicacion::where('activo', true)->get();
        $ubicacionSeleccionada = $ubicacionId ? Ubicacion::find($ubicacionId) : null;

        return view('punto-venta.reporte', compact(
            'resumen',
            'porMetodoPago',
            'porDia',
            'totalAnuladas',
            'montoAnulado',
            'fechaDesde',
            'fechaHasta',
            'ubicaciones',
            'ubicacionSeleccionada',
            'ubicacionId'
        ));
    }

    /**
     * Verificar stock en tiempo real (AJAX)
     */
    public function verificarStock(Request $request)
    {
        $ubicacionId = session('pdv_ubicacion_id');

        $resultado = $this->puntoVentaService->verificarDisponibilidadItems(
            $request->items ?? [],
            $ubicacionId
        );

        return response()->json($resultado);
    }
}
