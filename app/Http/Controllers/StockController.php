<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\StockProducto;
use App\Models\MovimientoStock;
use App\Models\VarianteProducto;
use App\Models\Ubicacion;
use App\Models\ReservaStock;
use App\Models\CodigoBarrasLog;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockPorUbicacionExport;
use Illuminate\Support\Str;

class StockController extends Controller
{
    // Vista principal de gestión de stock
    public function index(Request $request)
    {
        // Si el usuario tiene una sede asignada, restringimos a esa ubicación
        $ubicacionUsuarioId = auth()->user()->ubicacion_id ?? null;

        if ($request->ajax()) {
            $query = StockProducto::select(
                    'stock_productos.producto_id',
                    'stock_productos.variante_producto_id',
                    DB::raw('MIN(stock_productos.id) as stock_id_representativo'),
                    DB::raw('SUM(stock_productos.cantidad_disponible) as total_disponible'),
                    DB::raw('SUM(stock_productos.cantidad_reservada) as total_reservado'),
                    DB::raw('SUM(stock_productos.cantidad_disponible - stock_productos.cantidad_reservada) as stock_total'),
                    DB::raw('MIN(stock_productos.stock_minimo) as stock_minimo_min'),
                    DB::raw('MAX(stock_productos.alerta_stock_bajo) as alerta_stock_bajo')
                )
                ->with(['producto', 'variante'])
                ->whereHas('producto', function($q) {
                    $q->where('eliminado', false);
                })
                // Si el producto tiene variantes, ocultar la fila agregada sin variante
                ->where(function($q) {
                    $q->whereNotNull('stock_productos.variante_producto_id')
                      ->orWhereHas('producto', function($sub) {
                          $sub->where('tiene_variantes', false);
                      });
                })
                ->groupBy('stock_productos.producto_id', 'stock_productos.variante_producto_id');

            // Restringir a la sede del usuario si tiene una asignada
            if ($ubicacionUsuarioId) {
                $query->where('stock_productos.ubicacion_id', $ubicacionUsuarioId);
            }

            // Filtrar por producto si se especifica
            if ($request->has('producto_id') && $request->producto_id) {
                $query->where('stock_productos.producto_id', $request->producto_id);
            }

            // Filtrar por ubicación (los usuarios con sede asignada ya están restringidos arriba)
            if (!$ubicacionUsuarioId && $request->filled('ubicacion_id')) {
                $query->where('stock_productos.ubicacion_id', $request->ubicacion_id);
            }

            // Filtrar por estado de stock (usando HAVING sobre los totales agregados)
            if ($request->has('estado') && $request->estado) {
                switch ($request->estado) {
                    case 'con_stock':
                        $query->havingRaw('SUM(stock_productos.cantidad_disponible - stock_productos.cantidad_reservada) > 0');
                        break;
                    case 'sin_stock':
                        $query->havingRaw('SUM(stock_productos.cantidad_disponible - stock_productos.cantidad_reservada) <= 0');
                        break;
                    case 'stock_bajo':
                        $query->havingRaw('SUM(stock_productos.cantidad_disponible - stock_productos.cantidad_reservada) <= MIN(stock_productos.stock_minimo) AND MAX(stock_productos.alerta_stock_bajo) = 1');
                        break;
                }
            }

            return DataTables::of($query)
                ->addColumn('producto_info', function($row) {
                    $info = '<strong>' . e($row->producto->referencia) . '</strong><br>';
                    $info .= e($row->producto->nombre);
                    if ($row->variante) {
                        $info .= '<br><small class="text-muted">' . e($row->variante->nombre_variante) . '</small>';
                    }
                    return $info;
                })
                ->addColumn('codigo_barras', function($row) {
                    $codigoBarras = $row->variante
                        ? $row->variante->codigo_barras
                        : $row->producto->codigo_barras;
                    if ($codigoBarras) {
                        return '<small class="text-dark"><i class="bi bi-upc-scan"></i> <code>' . e($codigoBarras) . '</code></small>';
                    }
                    return '<span class="text-muted">—</span>';
                })
                ->addColumn('stock_actual', function($row) {
                    $total = (int) $row->stock_total;
                    $minimo = (int) $row->stock_minimo_min;
                    $alerta = (int) $row->alerta_stock_bajo === 1;

                    if ($total <= 0) {
                        $badge = 'danger';
                    } elseif ($alerta && $total <= $minimo) {
                        $badge = 'warning';
                    } else {
                        $badge = 'success';
                    }
                    return '<span class="badge bg-'.$badge.'" style="font-size: 0.95rem;">' . $total . '</span>';
                })
                ->addColumn('disponible_reservado', function($row) {
                    return 'Disponible: ' . (int) $row->total_disponible . '<br>Reservado: ' . (int) $row->total_reservado;
                })
                ->addColumn('action', function($row) {
                    $productoId = $row->producto_id;
                    $varianteId = $row->variante_producto_id ?: 'null';
                    $stockIdRep = $row->stock_id_representativo;
                    $reservadoTotal = (int) $row->total_reservado;

                    $buttons = '<div class="btn-group btn-group-sm">';

                    // Botón principal: Ver ubicaciones (abre modal con operaciones por ubicación)
                    $buttons .= '<button type="button" class="btn btn-primary" onclick="verUbicaciones('.$productoId.', '.$varianteId.')" title="Ver ubicaciones y operar stock">
                                    <i class="bi bi-geo-alt"></i> Ubicaciones
                                </button>';

                    // Botón reservas (por producto/variante, agregado de todas las ubicaciones)
                    $badgeClass = $reservadoTotal < 0 ? 'bg-danger' : ($reservadoTotal > 0 ? 'bg-primary' : 'bg-secondary');
                    $btnClass = $reservadoTotal < 0 ? 'btn btn-outline-danger' : ($reservadoTotal > 0 ? 'btn btn-outline-primary' : 'btn btn-outline-secondary');
                    $buttons .= '<button type="button" class="'.$btnClass.'" onclick="verReservas('.$productoId.', '.$varianteId.')" title="Ver Reservas ('.$reservadoTotal.')">
                                    <i class="bi bi-bookmark-check"></i>
                                    <span class="badge '.$badgeClass.'">'.$reservadoTotal.'</span>
                                </button>';

                    // Botón historial movimientos (por producto/variante)
                    $buttons .= '<button type="button" class="btn btn-secondary" onclick="verHistorial('.$productoId.', '.$varianteId.')" title="Historial">
                                    <i class="bi bi-clock-history"></i>
                                </button>';

                    // Botón escanear código de barras (aplica al producto/variante, usamos stock_id representativo)
                    if (auth()->user()->hasAnyRole(['admin', 'auxiliar_administrativo', 'inventarios', 'auxiliar_inventario'])) {
                        $codigoBarrasActual = $row->variante
                            ? $row->variante->codigo_barras
                            : $row->producto->codigo_barras;
                        $codigoEscapado = e($codigoBarrasActual ?? '');
                        $buttons .= '<button type="button" class="btn btn-dark" data-stock-id="'.$stockIdRep.'" data-codigo-actual="'.$codigoEscapado.'" onclick="abrirModalCodigoBarras(parseInt(this.dataset.stockId, 10), this.dataset.codigoActual)" title="Escanear Código de Barras">
                                        <i class="bi bi-upc-scan"></i>
                                    </button>';

                        // Botón historial código de barras
                        $buttons .= '<button type="button" class="btn btn-outline-dark" onclick="verHistorialCodigoBarras('.$stockIdRep.')" title="Historial Código de Barras">
                                        <i class="bi bi-journal-text"></i>
                                    </button>';
                    }

                    // Botón homologar con SIIGO (admin/auxiliar/facturación)
                    if (auth()->user()->hasAnyRole(['admin', 'auxiliar_administrativo', 'facturacion'])) {
                        $siigoCodeActual = $row->variante
                            ? ($row->variante->siigo_product_code ?? null)
                            : ($row->producto->siigo_product_code ?? null);
                        $homologado = !empty($siigoCodeActual);
                        $btnSiigoClass = $homologado ? 'btn btn-success' : 'btn btn-outline-secondary';
                        $titleSiigo = $homologado
                            ? 'Homologado SIIGO: '.e($siigoCodeActual)
                            : 'Homologar con SIIGO';
                        $varianteIdSiigo = $row->variante_producto_id ?: 'null';
                        $buttons .= '<button type="button" class="'.$btnSiigoClass.'" onclick="homologarSiigo('.$productoId.', '.$varianteIdSiigo.')" title="'.$titleSiigo.'">
                                        <i class="bi bi-link-45deg"></i>
                                    </button>';
                    }

                    $buttons .= '</div>';
                    return $buttons;
                })
                ->addColumn('referencia', function($row) {
                    return $row->producto->referencia;
                })
                ->addColumn('nombre_producto', function($row) {
                    return $row->producto->nombre;
                })
                ->addColumn('variante_nombre', function($row) {
                    return $row->variante ? $row->variante->nombre_variante : '';
                })
                ->filterColumn('producto_id', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        // Texto del producto (referencia/nombre): aplica a todos los stocks del producto
                        $q->whereHas('producto', function($sub) use ($keyword) {
                            $sub->where('referencia', 'like', "%{$keyword}%")
                                ->orWhere('nombre', 'like', "%{$keyword}%");
                        })
                        // Datos de la variante: solo stocks con esa variante
                        ->orWhereHas('variante', function($sub) use ($keyword) {
                            $sub->where('referencia_variante', 'like', "%{$keyword}%")
                                ->orWhere('color', 'like', "%{$keyword}%")
                                ->orWhere('sku', 'like', "%{$keyword}%")
                                ->orWhere('codigo_barras', 'like', "%{$keyword}%");
                        })
                        // Código de barras del producto: solo stock sin variante
                        ->orWhere(function($sub) use ($keyword) {
                            $sub->whereNull('stock_productos.variante_producto_id')
                                ->whereHas('producto', function($p) use ($keyword) {
                                    $p->where('codigo_barras', 'like', "%{$keyword}%");
                                });
                        });
                    });
                })
                ->filterColumn('codigo_barras', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        // Stock sin variante → matchea contra producto.codigo_barras
                        $q->where(function($sub) use ($keyword) {
                            $sub->whereNull('stock_productos.variante_producto_id')
                                ->whereHas('producto', function($p) use ($keyword) {
                                    $p->where('codigo_barras', 'like', "%{$keyword}%");
                                });
                        })
                        // Stock con variante → matchea contra variante.codigo_barras
                        ->orWhere(function($sub) use ($keyword) {
                            $sub->whereNotNull('stock_productos.variante_producto_id')
                                ->whereHas('variante', function($v) use ($keyword) {
                                    $v->where('codigo_barras', 'like', "%{$keyword}%");
                                });
                        });
                    });
                })
                ->rawColumns(['producto_info', 'stock_actual', 'disponible_reservado', 'codigo_barras', 'action'])
                ->make(true);
        }

        // Las tarjetas de resumen respetan los filtros activos (sede del usuario o ubicación
        // seleccionada, y producto) para que cambien al filtrar.
        $ubicacionFiltro = $ubicacionUsuarioId ?: $request->ubicacion_id;
        $productoFiltroId = $request->filled('producto_id') ? $request->producto_id : null;

        $aplicarFiltrosResumen = function ($q) use ($ubicacionFiltro, $productoFiltroId) {
            return $q->when($ubicacionFiltro, fn($qq) => $qq->where('ubicacion_id', $ubicacionFiltro))
                     ->when($productoFiltroId, fn($qq) => $qq->where('producto_id', $productoFiltroId));
        };

        $productosConStock     = $aplicarFiltrosResumen(StockProducto::conStock())->count();
        $productosConStockBajo = $aplicarFiltrosResumen(StockProducto::conStockBajo())->count();
        $productosSinStock     = $aplicarFiltrosResumen(StockProducto::sinStock())->count();

        // Obtener información del producto si viene filtrado
        $productoFiltrado = null;
        if ($request->has('producto_id') && $request->producto_id) {
            $productoFiltrado = Producto::find($request->producto_id);
        }

        // Obtener ubicaciones activas para el filtro
        // Si el usuario tiene sede asignada, solo mostrar la suya
        $ubicaciones = Ubicacion::activas()
            ->when($ubicacionUsuarioId, fn($q) => $q->where('id', $ubicacionUsuarioId))
            ->orderBy('nombre')
            ->get();

        return view('stock.index', compact('productosConStock', 'productosConStockBajo', 'productosSinStock', 'productoFiltrado', 'ubicaciones'));
    }

    // AJAX: retorna el HTML con el desglose de stock por ubicación para un producto/variante
    public function ubicacionesAjax(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'variante_producto_id' => 'nullable|exists:variantes_productos,id',
        ]);

        $productoId = $request->producto_id;
        $varianteId = $request->variante_producto_id;
        $ubicacionUsuarioId = auth()->user()->ubicacion_id ?? null;

        $producto = Producto::findOrFail($productoId);
        $variante = $varianteId ? VarianteProducto::find($varianteId) : null;

        $stocks = StockProducto::with(['ubicacionRelacion'])
            ->where('producto_id', $productoId)
            ->when($varianteId, function($q) use ($varianteId) {
                $q->where('variante_producto_id', $varianteId);
            }, function($q) {
                $q->whereNull('variante_producto_id');
            })
            ->when($ubicacionUsuarioId, fn($q) => $q->where('ubicacion_id', $ubicacionUsuarioId))
            ->orderBy('ubicacion_id')
            ->get();

        // Ubicaciones activas donde aún no hay registro para este producto/variante
        // Si el usuario tiene una sede asignada, solo puede agregar registros en esa sede
        $ubicacionesExistentes = $stocks->pluck('ubicacion_id')->filter()->all();
        $ubicacionesDisponibles = Ubicacion::activas()
            ->whereNotIn('id', $ubicacionesExistentes)
            ->when($ubicacionUsuarioId, fn($q) => $q->where('id', $ubicacionUsuarioId))
            ->orderBy('nombre')
            ->get();

        $html = view('stock.partials._ubicaciones_modal', compact(
            'stocks', 'ubicacionesDisponibles', 'producto', 'variante'
        ))->render();

        return response()->json(['html' => $html]);
    }

    // Crear un registro de stock en una ubicación donde el producto/variante aún no existe
    public function agregarUbicacion(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'variante_producto_id' => 'nullable|exists:variantes_productos,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
        ]);

        // Si el usuario tiene una sede asignada, solo puede agregar en esa sede
        $ubicacionUsuarioId = auth()->user()->ubicacion_id ?? null;
        if ($ubicacionUsuarioId && (int) $request->ubicacion_id !== (int) $ubicacionUsuarioId) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para agregar stock en esa ubicación.'
            ], 403);
        }

        $existente = StockProducto::where('producto_id', $request->producto_id)
            ->where('ubicacion_id', $request->ubicacion_id)
            ->when($request->variante_producto_id, function($q) use ($request) {
                $q->where('variante_producto_id', $request->variante_producto_id);
            }, function($q) {
                $q->whereNull('variante_producto_id');
            })
            ->first();

        if ($existente) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un registro de stock para este producto en esa ubicación.'
            ], 422);
        }

        StockProducto::create([
            'producto_id' => $request->producto_id,
            'variante_producto_id' => $request->variante_producto_id,
            'ubicacion_id' => $request->ubicacion_id,
            'cantidad_disponible' => 0,
            'cantidad_reservada' => 0,
            'stock_minimo' => 0,
            'alerta_stock_bajo' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ubicación agregada. Ya puedes registrar entradas de stock en ella.'
        ]);
    }

    // Entrada de stock
    public function entrada(Request $request)
    {
        $request->validate([
            'stock_id' => 'required|exists:stock_productos,id',
            'cantidad' => 'required|integer|min:1',
            'referencia' => 'nullable|string|max:255',
            'motivo' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $stock = StockProducto::findOrFail($request->stock_id);
            $stock->entrada(
                $request->cantidad,
                'compra',
                $request->referencia,
                $request->motivo
            );

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Entrada de stock registrada correctamente',
                'stock_actual' => $stock->fresh()->stock_real
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar entrada: ' . $e->getMessage()
            ], 500);
        }
    }

    // Salida de stock
    public function salida(Request $request)
    {
        $request->validate([
            'stock_id' => 'required|exists:stock_productos,id',
            'cantidad' => 'required|integer|min:1',
            'referencia' => 'nullable|string|max:255',
            'motivo' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $stock = StockProducto::findOrFail($request->stock_id);
            
            if (!$stock->hayDisponibilidad($request->cantidad)) {
                throw new \Exception('Stock insuficiente. Disponible: ' . $stock->stock_real);
            }
            
            $resultado = $stock->salida(
                $request->cantidad,
                'venta',
                $request->referencia,
                $request->motivo
            );

            if (!$resultado) {
                throw new \Exception('No se pudo procesar la salida de stock');
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Salida de stock registrada correctamente',
                'stock_actual' => $stock->fresh()->stock_real
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Ajuste de inventario
    public function ajuste(Request $request)
    {
        $request->validate([
            'stock_id' => 'required|exists:stock_productos,id',
            'nueva_cantidad' => 'required|integer|min:0',
            'motivo' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $stock = StockProducto::findOrFail($request->stock_id);
            $stock->ajustar($request->nueva_cantidad, $request->motivo);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Ajuste de inventario realizado correctamente',
                'stock_actual' => $stock->fresh()->stock_real
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al ajustar inventario: ' . $e->getMessage()
            ], 500);
        }
    }

    // Configurar parámetros de stock (por ubicación específica - stock_id fijo)
    public function configurar(Request $request)
    {
        $request->validate([
            'stock_id' => 'required|exists:stock_productos,id',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'nullable|integer|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'alerta_stock_bajo' => 'boolean',
            'notas' => 'nullable|string'
        ]);

        try {
            $stock = StockProducto::findOrFail($request->stock_id);

            $stock->update([
                'stock_minimo' => $request->stock_minimo,
                'stock_maximo' => $request->stock_maximo,
                'ubicacion' => $request->ubicacion,
                'alerta_stock_bajo' => $request->alerta_stock_bajo ?? true,
                'notas' => $request->notas
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    // Ver historial de movimientos
    public function historial(Request $request)
    {
        $productoId = $request->producto_id;
        $varianteId = $request->variante_id;
        $ubicacionId = $request->ubicacion_id;

        $movimientos = MovimientoStock::with(['usuario', 'producto', 'variante', 'ubicacion'])
            ->where('producto_id', $productoId);

        if ($varianteId) {
            $movimientos->where('variante_producto_id', $varianteId);
        } else {
            $movimientos->whereNull('variante_producto_id');
        }

        // Filtro opcional por ubicación: muestra solo lo que entra y sale en esa ubicación
        if ($ubicacionId) {
            $movimientos->where('ubicacion_id', $ubicacionId);
        }

        $movimientos = $movimientos->orderBy('created_at', 'desc')
                                   ->limit(50)
                                   ->get();

        $ubicacion = $ubicacionId ? Ubicacion::find($ubicacionId) : null;

        $html = view('stock.historial', compact('movimientos', 'ubicacion'))->render();

        return response()->json(['html' => $html]);
    }

    // Ver reservas de stock (agregado de todas las ubicaciones del producto/variante)
    public function reservas(Request $request)
    {
        // Soporta llamada legacy por stock_id, pero prefiere producto_id + variante_producto_id
        if ($request->filled('producto_id')) {
            $productoId = $request->producto_id;
            $varianteId = $request->variante_producto_id;

            $stockIds = StockProducto::where('producto_id', $productoId)
                ->when($varianteId, function($q) use ($varianteId) {
                    $q->where('variante_producto_id', $varianteId);
                }, function($q) {
                    $q->whereNull('variante_producto_id');
                })
                ->pluck('id');
        } else {
            $stockIds = collect([$request->stock_id])->filter();
        }

        $reservas = ReservaStock::with(['solicitudCotizacion.cliente', 'itemSolicitud'])
            ->whereIn('stock_producto_id', $stockIds)
            ->orderByRaw("FIELD(estado, 'activa', 'aplicada', 'expirada', 'liberada_manual')")
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Agrupar reservas activas por cotizacion
        $reservasActivas = $reservas->where('estado', 'activa');
        $cotizacionesActivas = $reservasActivas->groupBy('solicitud_cotizacion_id')->map(function ($grupo) {
            $solicitud = $grupo->first()->solicitudCotizacion;
            return [
                'solicitud' => $solicitud,
                'total_reservado' => $grupo->sum('cantidad_reservada'),
                'reservas' => $grupo,
            ];
        });

        // Para compatibilidad con la vista (que usa $stockId para acciones)
        $stockId = $stockIds->first();

        $html = view('stock.reservas', compact('reservas', 'cotizacionesActivas', 'stockId'))->render();

        return response()->json(['html' => $html]);
    }

    // Obtener datos de stock para edición
    public function obtenerStock($id)
    {
        $stock = StockProducto::with(['producto', 'variante', 'ubicacionRelacion'])->findOrFail($id);

        return response()->json([
            'stock' => $stock,
            'producto_nombre' => $stock->producto->nombre,
            'variante_nombre' => $stock->variante ? $stock->variante->nombre_variante : null,
            'ubicacion_nombre' => $stock->ubicacionRelacion ? $stock->ubicacionRelacion->nombre : null,
        ]);
    }

    // Dashboard de stock
    public function dashboard()
    {
        // Estadísticas generales
        $totalProductos = Producto::where('controlar_stock', true)->where('eliminado', false)->count();
        $productosConStock = StockProducto::conStock()->count();
        $productosSinStock = StockProducto::sinStock()->count();
        $productosStockBajo = StockProducto::conStockBajo()->count();
        
        // Valor total del inventario (necesitaría precio de costo)
        $valorInventario = 0; // Implementar si se tiene precio de costo
        
        // Movimientos del mes
        $movimientosMes = MovimientoStock::delMes()->get();
        $entradasMes = $movimientosMes->where('tipo_movimiento', 'entrada')->sum('cantidad');
        $salidasMes = $movimientosMes->where('tipo_movimiento', 'salida')->sum('cantidad');
        
        // Productos con mayor rotación
        $productosTopRotacion = DB::table('movimientos_stock')
            ->select('producto_id', DB::raw('SUM(cantidad) as total_movimiento'))
            ->where('tipo_movimiento', 'salida')
            ->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()])
            ->groupBy('producto_id')
            ->orderBy('total_movimiento', 'desc')
            ->limit(10)
            ->get();
        
        // Productos críticos (stock bajo)
        $productosCriticos = StockProducto::with(['producto', 'variante'])
            ->conStockBajo()
            ->orderBy('cantidad_disponible', 'asc')
            ->limit(10)
            ->get();
        
        return view('stock.dashboard', compact(
            'totalProductos',
            'productosConStock',
            'productosSinStock',
            'productosStockBajo',
            'valorInventario',
            'entradasMes',
            'salidasMes',
            'productosTopRotacion',
            'productosCriticos'
        ));
    }

    // Importar stock desde Excel
    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls|max:10240'
        ]);

        // TODO: Implementar importación desde Excel
        // Formato esperado: Referencia | SKU_Variante | Cantidad | Stock_Minimo | Stock_Maximo | Ubicacion
        
        return back()->with('success', 'Stock importado correctamente.');
    }

    // Exportar inventario actual
    // Exportar inventario a Excel, respetando los filtros de ubicación, producto y estado
    public function exportar(Request $request)
    {
        // Si el usuario está restringido a una sede, se fuerza esa ubicación
        $ubicacionUsuarioId = auth()->user()->ubicacion_id ?? null;
        $ubicacionId = $ubicacionUsuarioId ?: $request->ubicacion_id;

        $filtros = [
            'ubicacion_id' => $ubicacionId,
            'producto_id'  => $request->producto_id,
            'estado'       => $request->estado,
        ];

        $sufijo = $ubicacionId
            ? Str::slug(optional(Ubicacion::find($ubicacionId))->nombre ?: 'ubicacion')
            : 'todas-ubicaciones';

        $nombre = 'stock_' . $sufijo . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new StockPorUbicacionExport($filtros), $nombre);
    }

    // Reporte de movimientos
    public function reporteMovimientos(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->subMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();
        
        $movimientos = MovimientoStock::with(['producto', 'variante', 'usuario'])
            ->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        
        if ($request->producto_id) {
            $movimientos->where('producto_id', $request->producto_id);
        }
        
        if ($request->tipo_movimiento) {
            $movimientos->where('tipo_movimiento', $request->tipo_movimiento);
        }
        
        $movimientos = $movimientos->orderBy('created_at', 'desc')->get();
        
        return view('stock.reporte_movimientos', compact('movimientos', 'fechaInicio', 'fechaFin'));
    }

    // Inicializar stock para todos los productos
    public function inicializarTodos()
    {
        DB::beginTransaction();
        try {
            $productos = Producto::where('controlar_stock', true)->where('eliminado', false)->get();
            
            foreach ($productos as $producto) {
                $producto->inicializarStock();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Stock inicializado para todos los productos'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al inicializar stock: ' . $e->getMessage()
            ], 500);
        }
    }

    // Obtener productos para selector (AJAX)
    public function productosJson(Request $request)
    {
        $query = Producto::where('controlar_stock', true)
                        ->where('eliminado', false);
        
        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('referencia', 'like', "%{$search}%");
            });
        }
        
        $productos = $query->orderBy('nombre')
                           ->limit(50)
                           ->get()
                           ->map(function($producto) {
                               return [
                                   'id' => $producto->id,
                                   'text' => $producto->referencia . ' - ' . $producto->nombre . 
                                            ($producto->tiene_variantes ? ' (Con variantes)' : ''),
                                   'tiene_variantes' => $producto->tiene_variantes,
                                   'stock_total' => $producto->stock_total
                               ];
                           });
        
        return response()->json([
            'results' => $productos
        ]);
    }

    // Generar PDF de nota de movimiento
    public function generarNotaPdf($id)
    {
        $movimiento = MovimientoStock::with(['producto', 'variante', 'usuario', 'ubicacion'])
            ->findOrFail($id);

        // Generar número de nota único
        $prefijo = match($movimiento->tipo_movimiento) {
            'entrada' => 'NE',
            'salida' => 'NS',
            'ajuste' => 'NA',
            default => 'NM'
        };
        $numero = $prefijo . '-' . str_pad($movimiento->id, 6, '0', STR_PAD_LEFT);
        $fecha = $movimiento->created_at->format('d/m/Y H:i');

        // Seleccionar vista según tipo de movimiento
        $vista = match($movimiento->tipo_movimiento) {
            'entrada' => 'pdf.nota-entrada',
            'salida' => 'pdf.nota-salida',
            'ajuste' => 'pdf.nota-ajuste',
            default => 'pdf.nota-entrada'
        };

        $pdf = Pdf::loadView($vista, compact('movimiento', 'numero', 'fecha'));

        $nombreArchivo = $prefijo . '_' . $movimiento->id . '_' . $movimiento->created_at->format('Ymd') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    // Ver nota de movimiento en pantalla (stream)
    public function verNotaPdf($id)
    {
        $movimiento = MovimientoStock::with(['producto', 'variante', 'usuario', 'ubicacion'])
            ->findOrFail($id);

        // Generar número de nota único
        $prefijo = match($movimiento->tipo_movimiento) {
            'entrada' => 'NE',
            'salida' => 'NS',
            'ajuste' => 'NA',
            default => 'NM'
        };
        $numero = $prefijo . '-' . str_pad($movimiento->id, 6, '0', STR_PAD_LEFT);
        $fecha = $movimiento->created_at->format('d/m/Y H:i');

        // Seleccionar vista según tipo de movimiento
        $vista = match($movimiento->tipo_movimiento) {
            'entrada' => 'pdf.nota-entrada',
            'salida' => 'pdf.nota-salida',
            'ajuste' => 'pdf.nota-ajuste',
            default => 'pdf.nota-entrada'
        };

        $pdf = Pdf::loadView($vista, compact('movimiento', 'numero', 'fecha'));

        return $pdf->stream();
    }

    /**
     * Guardar/actualizar el código de barras asociado a un registro de stock.
     * Si el stock tiene variante, el código se guarda en la variante; si no, en el producto.
     */
    public function guardarCodigoBarras(Request $request, $stockId)
    {
        $stock = StockProducto::with(['producto', 'variante'])->findOrFail($stockId);
        $variante = $stock->variante;
        $producto = $stock->producto;

        // Reglas: único globalmente entre productos y entre variantes, ignorando el registro actual
        try {
            $request->validate([
                'codigo_barras' => [
                    'required','string','max:50',
                    $variante
                        ? Rule::unique('variantes_productos','codigo_barras')->ignore($variante->id)
                        : Rule::unique('productos','codigo_barras')->ignore($producto->id),
                    // Cruzar unicidad con la otra tabla (no se debe repetir entre productos y variantes)
                    function ($attribute, $value, $fail) use ($variante, $producto) {
                        if ($variante) {
                            $existeEnProducto = Producto::where('codigo_barras', $value)->exists();
                            if ($existeEnProducto) {
                                $fail('Ya existe un producto con este código de barras.');
                            }
                        } else {
                            $existeEnVariante = VarianteProducto::where('codigo_barras', $value)
                                ->where('producto_id', '!=', $producto->id)
                                ->exists();
                            if ($existeEnVariante) {
                                $fail('Ya existe una variante con este código de barras.');
                            }
                        }
                    },
                ],
            ], [
                'codigo_barras.required' => 'Debe ingresar un código de barras.',
                'codigo_barras.max' => 'El código de barras no debe superar los 50 caracteres.',
                'codigo_barras.unique' => 'Ya existe otro registro con este código de barras.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors(),
            ], 422);
        }

        $nuevo = trim($request->input('codigo_barras'));

        if ($variante) {
            $anterior = $variante->codigo_barras;
            $variante->update(['codigo_barras' => $nuevo]);
            CodigoBarrasLog::registrar($producto, $anterior, $nuevo, 'modal_escaneo', $variante);
        } else {
            $anterior = $producto->codigo_barras;
            $producto->update(['codigo_barras' => $nuevo]);
            CodigoBarrasLog::registrar($producto, $anterior, $nuevo, 'modal_escaneo');
        }

        return response()->json([
            'success' => true,
            'codigo_barras' => $nuevo,
            'message' => 'Código de barras guardado correctamente.',
        ]);
    }

    /**
     * Eliminar el código de barras del producto o variante asociado al stock.
     * Si no hay código actual, retorna 422.
     */
    public function eliminarCodigoBarras($stockId)
    {
        $stock = StockProducto::with(['producto', 'variante'])->findOrFail($stockId);
        $variante = $stock->variante;
        $producto = $stock->producto;

        if ($variante) {
            $anterior = $variante->codigo_barras;
            if (!$anterior) {
                return response()->json([
                    'success' => false,
                    'message' => 'La variante no tiene código de barras asignado.',
                ], 422);
            }
            $variante->update(['codigo_barras' => null]);
            CodigoBarrasLog::registrar($producto, $anterior, null, 'modal_eliminacion', $variante);
        } else {
            $anterior = $producto->codigo_barras;
            if (!$anterior) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto no tiene código de barras asignado.',
                ], 422);
            }
            $producto->update(['codigo_barras' => null]);
            CodigoBarrasLog::registrar($producto, $anterior, null, 'modal_eliminacion');
        }

        return response()->json([
            'success' => true,
            'message' => 'Código de barras eliminado correctamente.',
        ]);
    }

    /**
     * Historial de cambios del código de barras para el registro de stock indicado.
     */
    public function historialCodigoBarras($stockId)
    {
        $stock = StockProducto::with(['producto', 'variante'])->findOrFail($stockId);

        if ($stock->variante) {
            $logs = $stock->variante->codigosBarrasLogs()->with('usuario')->get();
            $codigoActual = $stock->variante->codigo_barras;
            $titulo = $stock->producto->referencia . ' — ' . $stock->producto->nombre
                . ' / ' . $stock->variante->nombre_variante;
        } else {
            $logs = $stock->producto->codigosBarrasLogs()->with('usuario')->get();
            $codigoActual = $stock->producto->codigo_barras;
            $titulo = $stock->producto->referencia . ' — ' . $stock->producto->nombre;
        }

        return view('stock.partials._codigo_barras_historial', compact('logs', 'codigoActual', 'titulo'));
    }
}