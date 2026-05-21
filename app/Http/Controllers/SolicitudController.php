<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudCotizacion;
use App\Models\ItemSolicitudCotizacion;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\PrecioProducto;
use App\Models\PrecioVariante;
use App\Models\StockProducto;
use App\Models\MovimientoStock;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SolicitudAplicada;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SolicitudesExport;
use Illuminate\Support\Facades\Log;

class SolicitudController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        // Verificar que el usuario sea admin o vendedor
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('vendedor')) {
            abort(403, 'No tienes permisos para acceder a este módulo.');
        }

        if ($request->ajax()) {
            $query = SolicitudCotizacion::with(['cliente', 'cliente.vendedor', 'createdBy', 'items'])
                                       ->select('solicitudes_cotizacion.*');

            // Si es vendedor (y NO es admin), filtrar solo solicitudes de sus clientes asignados
            if ($user->hasRole('vendedor') && !$user->hasRole('admin')) {
                $query->whereHas('cliente', function($subQ) use ($user) {
                    $subQ->where('vendedor_id', $user->id);
                });
            }

            // Filtrar por vendedor si se proporciona (solo admin puede usar este filtro)
            if ($request->filled('vendedor_id') && $user->hasRole('admin')) {
                $vendedorId = $request->vendedor_id;
                $query->where(function($q) use ($vendedorId) {
                    $q->where('created_by', $vendedorId)
                      ->orWhereHas('cliente', function($subQ) use ($vendedorId) {
                          $subQ->where('vendedor_id', $vendedorId);
                      });
                });
            }

            // Filtro por cliente
            if ($request->filled('cliente_id')) {
                $query->where('cliente_id', $request->cliente_id);
            }

            // Filtro por rango de fechas (sobre created_at)
            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }

            // Admin ve todas las solicitudes (si no hay filtro), vendedor ve solo las suyas

            return DataTables::of($query)
                ->addColumn('cliente_nombre', function($s) {
                    return $s->cliente->nombre_contacto;
                })
                ->addColumn('vendedor', function($s) {
                    // Mostrar quién creó la solicitud, o el vendedor del cliente si no hay creador
                    if ($s->createdBy) {
                        return $s->createdBy->name;
                    } elseif ($s->cliente->vendedor) {
                        return $s->cliente->vendedor->name . ' (cliente)';
                    }
                    return 'Sin asignar';
                })
                ->addColumn('fecha', function($s) {
                    return $s->created_at->format('d/m/Y H:i');
                })
                ->addColumn('total_items', function($s) {
                    return $s->total_items;
                })
                ->addColumn('monto_formateado', function($s) {
                    return '$' . number_format($s->monto_total, 2);
                })
                ->addColumn('estado_badge', function($s) {
                    if ($s->estado === 'pendiente') {
                        $class = 'warning';
                        $text = 'Pendiente';
                    } elseif ($s->estado === 'aplicada') {
                        $class = 'success';
                        $text = 'Aplicada';
                    } else {
                        $class = 'danger';
                        $text = 'Rechazada';
                    }
                    return '<span class="badge bg-'.$class.'">'.$text.'</span>';
                })
                ->addColumn('action', function($s) use ($user) {
                    $buttons = '<div class="d-flex justify-content-center gap-1">';

                    // Botón ver detalle (desde aquí se puede aprobar o rechazar)
                    $buttons .= '<button type="button" class="btn btn-outline-info btn-sm"
                                        title="Ver Detalle" onclick="verDetalle('.$s->id.')">
                                   <i class="bi bi-eye"></i>
                                </button>';

                    // Botón editar (solo pendientes y según permisos)
                    if ($this->puedeEditar($s, $user)) {
                        $buttons .= '<button type="button" class="btn btn-outline-warning btn-sm"
                                            title="Editar Cotización" onclick="editarCotizacion('.$s->id.')">
                                       <i class="bi bi-pencil-square"></i>
                                    </button>';
                    }

                    // Botón descargar PDF
                    $buttons .= '<a href="'.route('solicitudes.pdf', $s->id).'" class="btn btn-outline-danger btn-sm"
                                    title="Descargar PDF" target="_blank">
                                   <i class="bi bi-file-earmark-pdf"></i>
                                </a>';

                    // Botón eliminar (solo admin)
                    if ($user->hasRole('admin')) {
                        $buttons .= '<button type="button" class="btn btn-outline-dark btn-sm"
                                            title="Eliminar Cotización" onclick="eliminarCotizacion('.$s->id.')">
                                       <i class="bi bi-trash"></i>
                                    </button>';
                    }

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->filterColumn('cliente_nombre', function($query, $keyword) {
                    $query->whereHas('cliente', function($q) use ($keyword) {
                        $q->where('nombre_contacto', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('vendedor', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        // Buscar en el usuario que creó la solicitud (createdBy)
                        $q->whereHas('createdBy', function($subQ) use ($keyword) {
                            $subQ->where('name', $keyword);
                        })
                        // O buscar en el vendedor del cliente
                        ->orWhereHas('cliente.vendedor', function($subQ) use ($keyword) {
                            $subQ->where('name', $keyword);
                        });
                    });
                })
                ->setRowClass(function($s) {
                    // Verificar si es pendiente y tiene más de 3 días
                    if ($s->estado === 'pendiente') {
                        $diasDiferencia = now()->diffInDays($s->created_at);
                        if ($diasDiferencia > 3) {
                            return 'solicitud-antigua';
                        }
                    }
                    return '';
                })
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }

        // Contar solicitudes pendientes con más de 3 días
        $queryAntiguas = SolicitudCotizacion::where('estado', 'pendiente')
            ->where('created_at', '<', now()->subDays(3));

        // Si es vendedor (y NO es admin), filtrar solo solicitudes de sus clientes asignados
        if ($user->hasRole('vendedor') && !$user->hasRole('admin')) {
            $queryAntiguas->whereHas('cliente', function($subQ) use ($user) {
                $subQ->where('vendedor_id', $user->id);
            });
        }

        $totalAntiguas = $queryAntiguas->count();

        // Obtener lista de vendedores únicos (usuarios que han creado solicitudes o son vendedores de clientes)
        $vendedores = \App\Models\User::whereIn('id', function($query) {
            $query->select('created_by')
                  ->from('solicitudes_cotizacion')
                  ->whereNotNull('created_by')
                  ->union(
                      \DB::table('clientes')
                          ->select('vendedor_id')
                          ->whereNotNull('vendedor_id')
                  );
        })->orderBy('name')->get();

        return view('solicitudes.solicitudes_index', compact('totalAntiguas', 'vendedores'));
    }
    
    public function detalle(SolicitudCotizacion $solicitud)
    {
        $user = Auth::user();

        // Verificar que sea admin o vendedor
        if (!$user->hasRole('admin') && !$user->hasRole('vendedor')) {
            return response()->json(['error' => 'No tiene permisos para ver esta solicitud'], 403);
        }

        // Si es vendedor (y NO es admin), verificar que la solicitud sea de uno de sus clientes asignados
        if ($user->hasRole('vendedor') && !$user->hasRole('admin')) {
            if ($solicitud->cliente->vendedor_id != $user->id) {
                return response()->json(['error' => 'No tiene permisos para ver esta solicitud'], 403);
            }
        }

        $solicitud->load(['cliente', 'cliente.listaPrecio', 'items.producto', 'items.varianteProducto', 'enlaceAcceso']);
        
        $html = '<div class="row">';

        // Información del cliente
        $html .= '<div class="col-md-6">';
        $html .= '<h6>Información del Cliente</h6>';
        $html .= '<table class="table table-sm">';
        $html .= '<tr><td><strong>Cliente:</strong></td><td>' . $solicitud->cliente->nombre_contacto . '</td></tr>';
        $html .= '<tr><td><strong>Email:</strong></td><td>' . $solicitud->cliente->email . '</td></tr>';
        $html .= '<tr><td><strong>Teléfono:</strong></td><td>' . $solicitud->cliente->telefono . '</td></tr>';
        $html .= '<tr><td><strong>Lista de Precios:</strong></td><td>' . ($solicitud->cliente->listaPrecio?->nombre ?? 'Sin lista') . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';

        // Información de la cotización
        $html .= '<div class="col-md-6">';
        $html .= '<h6>Información de la Cotización</h6>';
        $html .= '<table class="table table-sm">';
        $html .= '<tr><td><strong>Número:</strong></td><td><code>' . $solicitud->numero_solicitud . '</code></td></tr>';
        $html .= '<tr><td><strong>Fecha:</strong></td><td>' . $solicitud->created_at->format('d/m/Y H:i') . '</td></tr>';
        $html .= '<tr><td><strong>Estado:</strong></td><td>';
        if ($solicitud->estado === 'pendiente') {
            $html .= '<span class="badge bg-warning">Pendiente</span>';
        } elseif ($solicitud->estado === 'aplicada') {
            $html .= '<span class="badge bg-success">Aplicada</span>';
        } else {
            $html .= '<span class="badge bg-danger">Rechazada</span>';
        }
        $html .= '</td></tr>';
        
        if ($solicitud->enlaceAcceso) {
            $html .= '<tr><td><strong>Origen:</strong></td><td>Enlace de Acceso</td></tr>';
        } else {
            $html .= '<tr><td><strong>Origen:</strong></td><td>Tienda a Tienda</td></tr>';
        }
        
        if ($solicitud->estado === 'aplicada') {
            $html .= '<tr><td><strong>Aplicada por:</strong></td><td>' . $solicitud->aplicadaPor?->name . '</td></tr>';
            $html .= '<tr><td><strong>Fecha aplicación:</strong></td><td>' . $solicitud->aplicada_en->format('d/m/Y H:i') . '</td></tr>';
        }

        if ($solicitud->estado === 'rechazada') {
            $html .= '<tr><td><strong>Rechazada por:</strong></td><td>' . $solicitud->rechazadaPor?->name . '</td></tr>';
            $html .= '<tr><td><strong>Fecha rechazo:</strong></td><td>' . $solicitud->rechazada_en->format('d/m/Y H:i') . '</td></tr>';
        }

        $html .= '</table>';
        $html .= '</div>';
        
        // Notas del cliente
        if ($solicitud->notas_cliente) {
            $html .= '<div class="col-12 mb-3">';
            $html .= '<h6>Notas del Cliente</h6>';
            $html .= '<div class="alert alert-info">' . nl2br(e($solicitud->notas_cliente)) . '</div>';
            $html .= '</div>';
        }
        
        // Items de la cotización
        $html .= '<div class="col-12">';
        $html .= '<h6>Productos Cotizados</h6>';
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-striped">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Referencia</th>';
        $html .= '<th>Producto</th>';
        $html .= '<th>Variante</th>';
        $html .= '<th>Cantidad</th>';
        $html .= '<th>Precio Unit.</th>';
        $html .= '<th>Subtotal</th>';
        if ($solicitud->estado === 'pendiente') {
            $html .= '<th>Stock Disponible</th>';
        }
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($solicitud->items as $item) {
            $html .= '<tr>';
            $html .= '<td><code>' . $item->referencia_producto . '</code></td>';
            $html .= '<td>' . $item->nombre_producto . '</td>';
            $html .= '<td>' . ($item->info_variante ?: '-') . '</td>';
            $html .= '<td>' . $item->cantidad . '</td>';
            $html .= '<td>$' . number_format($item->precio_unitario, 2) . '</td>';
            $html .= '<td>$' . number_format($item->precio_total, 2) . '</td>';
            
            // Mostrar stock disponible solo si está pendiente
            if ($solicitud->estado === 'pendiente') {
                $stockInfo = $this->obtenerStockItem($item);
                $html .= '<td>' . $stockInfo . '</td>';
            }
            
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '<tfoot>';
        $html .= '<tr>';
        $colspanTotal = ($solicitud->estado === 'pendiente') ? 6 : 5;
        $html .= '<th colspan="' . $colspanTotal . '" class="text-end">Total:</th>';
        $html .= '<th>$' . number_format($solicitud->monto_total, 2) . '</th>';
        $html .= '</tr>';
        $html .= '</tfoot>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Observaciones del admin (si está aplicada)
        if ($solicitud->estado === 'aplicada' && $solicitud->observaciones_admin) {
            $html .= '<div class="col-12 mt-3">';
            $html .= '<h6>Observaciones del Administrador</h6>';
            $html .= '<div class="alert alert-secondary">' . nl2br(e($solicitud->observaciones_admin)) . '</div>';
            $html .= '</div>';
        }

        // Motivo de rechazo (si está rechazada)
        if ($solicitud->estado === 'rechazada' && $solicitud->motivo_rechazo) {
            $html .= '<div class="col-12 mt-3">';
            $html .= '<h6>Motivo del Rechazo</h6>';
            $html .= '<div class="alert alert-warning">' . nl2br(e($solicitud->motivo_rechazo)) . '</div>';
            $html .= '</div>';
        }

        // Campo de observaciones y botones si está pendiente (para admin y vendedor)
        if ($solicitud->estado === 'pendiente' && ($user->hasRole('admin') || $user->hasRole('vendedor'))) {
            $html .= '<div class="col-12 mt-3">';
            $html .= '<hr>';
            $html .= '<div class="mb-3">';
            $html .= '<label class="form-label">Observaciones / Motivo de Rechazo</label>';
            $html .= '<textarea class="form-control" id="observacionesAdmin" rows="3"
                              placeholder="Ingrese observaciones si va a aprobar, o motivo detallado si va a rechazar..."></textarea>';
            $html .= '<small class="text-muted">Este campo se usará como observaciones si aprueba, o como motivo de rechazo si rechaza.</small>';
            $html .= '</div>';
            $html .= '<div class="mb-3">';
            $html .= '<div class="form-check">';
            $html .= '<input class="form-check-input" type="checkbox" id="procesarStock" checked>';
            $html .= '<label class="form-check-label" for="procesarStock">';
            $html .= '<strong>Procesar Stock:</strong> Descontar automáticamente del inventario (solo al aprobar)';
            $html .= '</label>';
            $html .= '</div>';
            $html .= '<small class="text-muted">Si está marcado, se descontará el stock de los productos que lo controlen.</small>';
            $html .= '</div>';
            $html .= '<div class="row g-2">';
            $html .= '<div class="col-md-6">';
            $html .= '<button type="button" class="btn btn-success w-100" onclick="confirmarAplicar(' . $solicitud->id . ')">
                        <i class="bi bi-check-circle"></i> Marcar como Aplicada
                      </button>';
            $html .= '</div>';
            $html .= '<div class="col-md-6">';
            $html .= '<button type="button" class="btn btn-danger w-100" onclick="confirmarRechazo(' . $solicitud->id . ')">
                        <i class="bi bi-x-circle"></i> Rechazar Cotización
                      </button>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return response($html);
    }

    /**
     * Valida que un precio nuevo no esté por debajo del precio_minimo_venta del producto.
     * Devuelve string con el mensaje de error, o null si pasa.
     */
    private function validarPrecioMinimo(Producto $producto, ?float $precio): ?string
    {
        if (is_null($precio) || is_null($producto->precio_minimo_venta)) {
            return null;
        }
        if ((float) $precio < (float) $producto->precio_minimo_venta) {
            return 'El producto "' . $producto->nombre . '" tiene un precio mínimo de venta de $'
                . number_format($producto->precio_minimo_venta, 2)
                . ' y se intentó asignar $' . number_format($precio, 2) . '.';
        }
        return null;
    }

    /**
     * ¿El usuario puede editar esta cotización?
     * - Solo cotizaciones en estado "pendiente"
     * - admin: cualquier cotización
     * - vendedor: solo cotizaciones de sus clientes asignados
     */
    private function puedeEditar(SolicitudCotizacion $solicitud, $user): bool
    {
        if ($solicitud->estado !== 'pendiente') {
            return false;
        }
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($user->hasRole('vendedor')) {
            return $solicitud->cliente->vendedor_id == $user->id;
        }
        return false;
    }

    /**
     * ¿El usuario puede editar precios?
     * Admin y vendedor pueden hacerlo. La validación de precio_minimo_venta
     * (configurada por el admin en el producto) se sigue aplicando a ambos
     * en actualizar() y agregarItem(), asi que el vendedor no puede bajar
     * de lo permitido.
     */
    private function puedeEditarPrecios($user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('vendedor');
    }

    /**
     * Vista de edición (HTML para modal)
     */
    public function editar(SolicitudCotizacion $solicitud)
    {
        $user = Auth::user();

        if (!$this->puedeEditar($solicitud, $user)) {
            return response()->json([
                'error' => 'No tiene permisos para editar esta cotización o ya no está pendiente.'
            ], 403);
        }

        $solicitud->load(['cliente.listaPrecio', 'items.producto', 'items.varianteProducto']);
        $puedeEditarPrecios = $this->puedeEditarPrecios($user);

        return view('solicitudes._editar_modal', compact('solicitud', 'puedeEditarPrecios'));
    }

    /**
     * Actualizar cotización (notas + items existentes en bloque)
     */
    public function actualizar(Request $request, SolicitudCotizacion $solicitud)
    {
        $user = Auth::user();

        if (!$this->puedeEditar($solicitud, $user)) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No tiene permisos para editar esta cotización.'
            ], 403);
        }

        $puedeEditarPrecios = $this->puedeEditarPrecios($user);

        $request->validate([
            'notas_cliente'           => 'nullable|string|max:2000',
            'observaciones_admin'     => 'nullable|string|max:2000',
            'items'                   => 'nullable|array',
            'items.*.id'              => 'required|integer|exists:items_solicitud_cotizacion,id',
            'items.*.cantidad'        => 'required|integer|min:1',
            'items.*.precio_unitario' => 'nullable|numeric|min:0',
        ]);

        // Pre-validar precio mínimo en bloque (antes de tocar nada)
        if ($puedeEditarPrecios) {
            foreach ($request->input('items', []) as $itemData) {
                if (!isset($itemData['precio_unitario'])) {
                    continue;
                }
                $item = ItemSolicitudCotizacion::with('producto')
                    ->where('solicitud_cotizacion_id', $solicitud->id)
                    ->where('id', $itemData['id'])
                    ->first();
                if (!$item || !$item->producto) {
                    continue;
                }
                $err = $this->validarPrecioMinimo($item->producto, (float) $itemData['precio_unitario']);
                if ($err) {
                    return response()->json(['success' => false, 'mensaje' => $err], 422);
                }
            }
        }

        DB::beginTransaction();
        try {
            $solicitud->update([
                'notas_cliente'       => $request->input('notas_cliente', $solicitud->notas_cliente),
                'observaciones_admin' => $user->hasRole('admin')
                    ? $request->input('observaciones_admin', $solicitud->observaciones_admin)
                    : $solicitud->observaciones_admin,
            ]);

            foreach ($request->input('items', []) as $itemData) {
                $item = ItemSolicitudCotizacion::where('solicitud_cotizacion_id', $solicitud->id)
                    ->where('id', $itemData['id'])
                    ->first();
                if (!$item) {
                    continue;
                }

                $cantidadNueva = (int) $itemData['cantidad'];
                $payload = ['cantidad' => $cantidadNueva];

                if ($puedeEditarPrecios && isset($itemData['precio_unitario'])) {
                    $precioNuevo = (float) $itemData['precio_unitario'];
                    if ((float) $item->precio_unitario !== $precioNuevo) {
                        $payload['precio_unitario']            = $precioNuevo;
                        $payload['precio_editado_manualmente'] = true;
                        if (is_null($item->precio_original)) {
                            $payload['precio_original'] = $item->precio_unitario;
                        }
                    }
                }

                $item->update($payload);
            }

            $solicitud->refresh();
            $solicitud->calcularMontoTotal();

            DB::commit();

            return response()->json([
                'success'      => true,
                'mensaje'      => 'Cotización actualizada correctamente.',
                'monto_total'  => $solicitud->monto_total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando cotización: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al actualizar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar un item de la cotización
     */
    public function eliminarItem(SolicitudCotizacion $solicitud, ItemSolicitudCotizacion $item)
    {
        $user = Auth::user();

        if (!$this->puedeEditar($solicitud, $user)) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No tiene permisos para editar esta cotización.'
            ], 403);
        }

        if ($item->solicitud_cotizacion_id !== $solicitud->id) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El item no pertenece a esta cotización.'
            ], 422);
        }

        if ($solicitud->items()->count() <= 1) {
            return response()->json([
                'success' => false,
                'mensaje' => 'La cotización debe tener al menos un producto.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $item->delete();
            $solicitud->refresh();
            $solicitud->calcularMontoTotal();

            DB::commit();

            return response()->json([
                'success'     => true,
                'mensaje'     => 'Producto eliminado de la cotización.',
                'monto_total' => $solicitud->monto_total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al eliminar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Agregar un nuevo item (producto / variante) a la cotización
     */
    public function agregarItem(Request $request, SolicitudCotizacion $solicitud)
    {
        $user = Auth::user();

        if (!$this->puedeEditar($solicitud, $user)) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No tiene permisos para editar esta cotización.'
            ], 403);
        }

        $request->validate([
            'producto_id'          => 'required|integer|exists:productos,id',
            'variante_producto_id' => 'nullable|integer|exists:variantes_producto,id',
            'cantidad'             => 'required|integer|min:1',
            'precio_unitario'      => 'nullable|numeric|min:0',
        ]);

        $producto = Producto::with('imagenPrincipal')->findOrFail($request->producto_id);
        $variante = $request->variante_producto_id
            ? VarianteProducto::find($request->variante_producto_id)
            : null;

        // Resolver precio: si admin lo manda, úsalo; si no, usar lista de precio del cliente
        $precioUnit = null;
        $editadoManual = false;

        if ($this->puedeEditarPrecios($user) && $request->filled('precio_unitario')) {
            $precioUnit = (float) $request->precio_unitario;
            $editadoManual = true;
        } else {
            $listaId = $solicitud->cliente->lista_precio_id;
            if ($listaId) {
                $precioBase = PrecioProducto::where('producto_id', $producto->id)
                    ->where('lista_precio_id', $listaId)
                    ->where('activo', true)
                    ->value('precio');

                if ($precioBase !== null) {
                    $precioUnit = (float) $precioBase;
                    if ($variante) {
                        $ajuste = PrecioVariante::where('variante_producto_id', $variante->id)
                            ->where('lista_precio_id', $listaId)
                            ->where('activo', true)
                            ->value('ajuste_precio');
                        if ($ajuste !== null) {
                            $precioUnit += (float) $ajuste;
                        }
                    }
                }
            }
        }

        if (is_null($precioUnit)) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se pudo determinar el precio del producto para la lista del cliente. Asigne uno manualmente.'
            ], 422);
        }

        $errMin = $this->validarPrecioMinimo($producto, $precioUnit);
        if ($errMin) {
            return response()->json(['success' => false, 'mensaje' => $errMin], 422);
        }

        DB::beginTransaction();
        try {
            $infoVariante = null;
            if ($variante) {
                $infoVariante = trim(($variante->talla ? 'Talla: ' . $variante->talla : '')
                    . ($variante->talla && $variante->color ? ' - ' : '')
                    . ($variante->color ? 'Color: ' . $variante->color : ''));
            }

            ItemSolicitudCotizacion::create([
                'solicitud_cotizacion_id'   => $solicitud->id,
                'producto_id'               => $producto->id,
                'variante_producto_id'      => $variante?->id,
                'cantidad'                  => (int) $request->cantidad,
                'precio_unitario'           => $precioUnit,
                'precio_editado_manualmente'=> $editadoManual,
                'precio_original'           => $editadoManual ? null : $precioUnit,
                'referencia_producto'       => $variante?->sku ?? $producto->referencia,
                'nombre_producto'           => $producto->nombre,
                'marca_producto'            => $producto->marca ?? null,
                'info_variante'             => $infoVariante,
            ]);

            $solicitud->refresh();
            $solicitud->calcularMontoTotal();

            DB::commit();

            return response()->json([
                'success'     => true,
                'mensaje'     => 'Producto agregado a la cotización.',
                'monto_total' => $solicitud->monto_total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al agregar producto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buscar productos para autocompletar al agregar items.
     * Si se pasa solicitud_id, devuelve también el precio calculado
     * según la lista de precios del cliente.
     */
    public function buscarProductos(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('vendedor')) {
            abort(403);
        }

        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $listaId = null;
        if ($request->filled('solicitud_id')) {
            $solicitud = SolicitudCotizacion::with('cliente')->find($request->solicitud_id);
            if ($solicitud) {
                $listaId = $solicitud->cliente->lista_precio_id;
            }
        }

        $productos = Producto::with(['variantes' => function($qb) {
                $qb->where('activo', true);
            }])
            ->where('activo', true)
            ->where(function($qb) use ($q) {
                $qb->where('referencia', 'like', "%{$q}%")
                   ->orWhere('nombre', 'like', "%{$q}%");
            })
            ->orderBy('nombre')
            ->limit(20)
            ->get();

        // Pre-cargar precios base por producto en una sola consulta
        $preciosBase = [];
        if ($listaId && $productos->isNotEmpty()) {
            $preciosBase = PrecioProducto::whereIn('producto_id', $productos->pluck('id'))
                ->where('lista_precio_id', $listaId)
                ->where('activo', true)
                ->pluck('precio', 'producto_id')
                ->toArray();
        }

        // Pre-cargar ajustes de variante en una sola consulta
        $ajustesVariante = [];
        if ($listaId) {
            $varianteIds = $productos->flatMap->variantes->pluck('id');
            if ($varianteIds->isNotEmpty()) {
                $ajustesVariante = PrecioVariante::whereIn('variante_producto_id', $varianteIds)
                    ->where('lista_precio_id', $listaId)
                    ->where('activo', true)
                    ->pluck('ajuste_precio', 'variante_producto_id')
                    ->toArray();
            }
        }

        $out = [];
        foreach ($productos as $p) {
            $precioBase = $preciosBase[$p->id] ?? null;

            if ($p->variantes && $p->variantes->count() > 0) {
                foreach ($p->variantes as $v) {
                    $info = trim(($v->talla ? 'Talla: ' . $v->talla : '')
                        . ($v->talla && $v->color ? ' - ' : '')
                        . ($v->color ? 'Color: ' . $v->color : ''));

                    $precio = null;
                    if (!is_null($precioBase)) {
                        $precio = (float) $precioBase + (float) ($ajustesVariante[$v->id] ?? 0);
                    }

                    $sufijo = $info ? " ({$info})" : '';
                    $sufijoPrecio = !is_null($precio) ? ' — $' . number_format($precio, 2) : '';

                    $out[] = [
                        'id'                   => $p->id,
                        'variante_producto_id' => $v->id,
                        'referencia'           => $v->sku ?? $p->referencia,
                        'nombre'               => $p->nombre,
                        'info_variante'        => $info ?: null,
                        'precio'               => $precio,
                        'label'                => $p->referencia . ' — ' . $p->nombre . $sufijo . $sufijoPrecio,
                    ];
                }
            } else {
                $precio = !is_null($precioBase) ? (float) $precioBase : null;
                $sufijoPrecio = !is_null($precio) ? ' — $' . number_format($precio, 2) : '';

                $out[] = [
                    'id'                   => $p->id,
                    'variante_producto_id' => null,
                    'referencia'           => $p->referencia,
                    'nombre'               => $p->nombre,
                    'info_variante'        => null,
                    'precio'               => $precio,
                    'label'                => $p->referencia . ' — ' . $p->nombre . $sufijoPrecio,
                ];
            }
        }

        return response()->json($out);
    }

    /**
     * Obtener información de stock para un item
     */
    private function obtenerStockItem($item)
    {
        $producto = $item->producto;
        
        // Si no controla stock
        if (!$producto->controlar_stock) {
            return '<span class="badge bg-success">Stock ilimitado</span>';
        }
        
        if ($item->variante_producto_id) {
            // Producto con variante
            $stock = StockProducto::where('producto_id', $producto->id)
                                  ->where('variante_producto_id', $item->variante_producto_id)
                                  ->first();
        } else {
            // Producto sin variante
            $stock = StockProducto::where('producto_id', $producto->id)
                                  ->whereNull('variante_producto_id')
                                  ->first();
        }
        
        if (!$stock) {
            if ($producto->permitir_venta_sin_stock) {
                return '<span class="badge bg-warning">Sin stock (se permite)</span>';
            } else {
                return '<span class="badge bg-danger">Sin stock</span>';
            }
        }
        
        $disponible = $stock->cantidad_disponible - $stock->cantidad_reservada;
        $solicitado = $item->cantidad;
        
        if ($disponible >= $solicitado) {
            return '<span class="badge bg-success">' . $disponible . ' disponibles</span>';
        } elseif ($producto->permitir_venta_sin_stock) {
            return '<span class="badge bg-warning">' . $disponible . ' disponibles (se permite déficit)</span>';
        } else {
            return '<span class="badge bg-danger">Insuficiente (' . $disponible . ' de ' . $solicitado . ')</span>';
        }
    }
    
    public function aplicar(Request $request, SolicitudCotizacion $solicitud)
    {
        $user = Auth::user();

        // Verificar que sea admin o vendedor
        if (!$user->hasRole('admin') && !$user->hasRole('vendedor')) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No tiene permisos para aplicar esta solicitud'
            ], 403);
        }

        // Si es vendedor (y NO es admin), verificar que la solicitud sea de uno de sus clientes asignados
        if ($user->hasRole('vendedor') && !$user->hasRole('admin')) {
            if ($solicitud->cliente->vendedor_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No tiene permisos para aplicar esta solicitud'
                ], 403);
            }
        }

        // Verificar que esté pendiente
        if ($solicitud->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'mensaje' => 'Esta solicitud ya fue aplicada'
            ], 400);
        }

        $request->validate([
            'observaciones' => 'nullable|string|max:1000',
            'procesar_stock' => 'boolean'
        ]);

        DB::beginTransaction();

        try {
            $procesarStock = $request->boolean('procesar_stock', true);
            $stockProcesado = [];
            $stockInsuficiente = [];
            
            // Procesar stock si se solicita
            if ($procesarStock) {
                foreach ($solicitud->items as $item) {
                    $resultado = $this->procesarStockItem($item, $user->id, $solicitud->id);
                    
                    if ($resultado['procesado']) {
                        $stockProcesado[] = $resultado['mensaje'];
                    } elseif ($resultado['error']) {
                        $stockInsuficiente[] = $resultado['mensaje'];
                    }
                }
                
                // Si hay stock insuficiente y no se permite venta sin stock, fallar
                if (!empty($stockInsuficiente)) {
                    $errorMsg = "No se puede procesar la solicitud por stock insuficiente:\n" . implode("\n", $stockInsuficiente);
                    throw new \Exception($errorMsg);
                }
            }
            
            // Marcar como aplicada
            $observaciones = $request->observaciones;
            if ($procesarStock && !empty($stockProcesado)) {
                $observaciones .= "\n\nMovimientos de stock procesados:\n" . implode("\n", $stockProcesado);
            }
            
            $solicitud->marcarComoAplicada($user->id, $observaciones);
            
            // Cargar relaciones necesarias para el PDF
            $solicitud->load([
                'cliente',
                'cliente.listaPrecio',
                'cliente.vendedor',
                'cliente.ciudad',
                'cliente.ciudad.departamento',
                'cliente.pais',
                'items.producto.imagenPrincipal',
                'items.producto.categoria',
                'aplicadaPor'
            ]);

            // Generar PDF con nuevo formato
            $pdf = PDF::loadView('pdf.cotizacion-excel-format', compact('solicitud'));
            $pdf->setPaper('letter', 'portrait');
            
            // Envío de email deshabilitado (solo se envía al crear la solicitud)
            // try {
            //     Mail::to($solicitud->cliente->email)
            //         ->send(new SolicitudAplicada($solicitud, $pdf));
            //
            //     $mensajeEmail = ' Se ha enviado el PDF por correo electrónico al cliente.';
            // } catch (\Exception $e) {
            //     // Log del error pero no fallar la aplicación
            //     Log::error('Error al enviar email de solicitud aplicada: ' . $e->getMessage());
            //     $mensajeEmail = ' (No se pudo enviar el correo: ' . $e->getMessage() . ')';
            // }
            $mensajeEmail = '';
            
            DB::commit();
            
            $mensaje = 'Solicitud marcada como aplicada exitosamente.';
            if ($procesarStock) {
                $mensaje .= ' Stock procesado correctamente.';
            }
            $mensaje .= $mensajeEmail;
            
            return response()->json([
                'success' => true,
                'mensaje' => $mensaje
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al aplicar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechazar solicitud de cotización
     */
    public function rechazar(Request $request, SolicitudCotizacion $solicitud)
    {
        $user = Auth::user();

        // Verificar que sea admin o vendedor
        if (!$user->hasRole('admin') && !$user->hasRole('vendedor')) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No tiene permisos para rechazar esta solicitud'
            ], 403);
        }

        // Si es vendedor (y NO es admin), verificar que la solicitud sea de uno de sus clientes asignados
        if ($user->hasRole('vendedor') && !$user->hasRole('admin')) {
            if ($solicitud->cliente->vendedor_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No tiene permisos para rechazar esta solicitud'
                ], 403);
            }
        }

        // Verificar que esté pendiente
        if ($solicitud->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'mensaje' => 'Esta solicitud ya fue procesada'
            ], 400);
        }

        $request->validate([
            'motivo_rechazo' => 'required|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            // Marcar como rechazada
            $solicitud->marcarComoRechazada($user->id, $request->motivo_rechazo);

            // Cargar relaciones necesarias para el email y PDF
            $solicitud->load([
                'cliente',
                'cliente.vendedor',
                'cliente.ciudad',
                'cliente.ciudad.departamento',
                'items.producto.imagenPrincipal',
                'items.producto.categoria',
                'rechazadaPor'
            ]);

            // Envío de email deshabilitado (solo se envía al crear la solicitud)
            // try {
            //     // Generar PDF
            //     $pdf = PDF::loadView('pdf.cotizacion-excel-format', compact('solicitud'));
            //     $pdf->setPaper('letter', 'portrait');
            //
            //     Mail::to($solicitud->cliente->email)
            //         ->send(new \App\Mail\SolicitudRechazada($solicitud, $pdf));
            //
            //     $mensajeEmail = ' Se ha enviado notificación por correo electrónico al cliente.';
            // } catch (\Exception $e) {
            //     // Log del error pero no fallar el rechazo
            //     Log::error('Error al enviar email de solicitud rechazada: ' . $e->getMessage());
            //     $mensajeEmail = ' (No se pudo enviar el correo: ' . $e->getMessage() . ')';
            // }
            $mensajeEmail = '';

            DB::commit();

            return response()->json([
                'success' => true,
                'mensaje' => 'Solicitud rechazada exitosamente.' . $mensajeEmail
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'mensaje' => 'Error al rechazar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar stock de un item individual
     */
    private function procesarStockItem($item, $usuarioId, $solicitudId)
    {
        $producto = $item->producto;
        
        // Si no controla stock, no hacer nada
        if (!$producto->controlar_stock) {
            return [
                'procesado' => false,
                'error' => false,
                'mensaje' => $producto->nombre . ' - No controla stock'
            ];
        }
        
        // Buscar registro de stock
        if ($item->variante_producto_id) {
            $stock = StockProducto::where('producto_id', $producto->id)
                                  ->where('variante_producto_id', $item->variante_producto_id)
                                  ->first();
            $descripcion = $producto->nombre . ' - ' . $item->info_variante;
        } else {
            $stock = StockProducto::where('producto_id', $producto->id)
                                  ->whereNull('variante_producto_id')
                                  ->first();
            $descripcion = $producto->nombre;
        }
        
        if (!$stock) {
            // Si no existe registro de stock, crearlo
            $stock = StockProducto::create([
                'producto_id' => $producto->id,
                'variante_producto_id' => $item->variante_producto_id,
                'cantidad_disponible' => 0,
                'cantidad_reservada' => 0,
                'stock_minimo' => 0,
                'alerta_stock_bajo' => true
            ]);
        }
        
        $stockAnterior = $stock->cantidad_disponible;
        $cantidadSolicitada = $item->cantidad;
        $stockResultante = $stockAnterior - $cantidadSolicitada;
        
        // Verificar si se puede procesar
        if ($stockResultante < 0 && !$producto->permitir_venta_sin_stock) {
            return [
                'procesado' => false,
                'error' => true,
                'mensaje' => $descripcion . ' - Stock insuficiente (disponible: ' . $stockAnterior . ', solicitado: ' . $cantidadSolicitada . ')'
            ];
        }
        
        // Procesar la salida
        $resultado = $stock->salida(
            $cantidadSolicitada,
            'venta',
            $item->solicitudCotizacion->numero_solicitud,
            'Venta aplicada desde solicitud de cotización'
        );
        
        if (!$resultado && !$producto->permitir_venta_sin_stock) {
            return [
                'procesado' => false,
                'error' => true,
                'mensaje' => $descripcion . ' - Error al procesar salida de stock'
            ];
        }
        
        // Si permite venta sin stock y falló la salida normal, hacer ajuste manual
        if (!$resultado && $producto->permitir_venta_sin_stock) {
            $stock->update(['cantidad_disponible' => $stockResultante]);
            
            // Crear movimiento manual
            MovimientoStock::create([
                'producto_id' => $producto->id,
                'variante_producto_id' => $item->variante_producto_id,
                'tipo_movimiento' => 'salida',
                'cantidad' => $cantidadSolicitada,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockResultante,
                'referencia_documento' => $item->solicitudCotizacion->numero_solicitud,
                'origen' => 'venta',
                'motivo' => 'Venta aplicada desde solicitud de cotización (permite stock negativo)',
                'usuario_id' => $usuarioId,
                'solicitud_cotizacion_id' => $solicitudId
            ]);
        }
        
        return [
            'procesado' => true,
            'error' => false,
            'mensaje' => $descripcion . ' - Descontado: ' . $cantidadSolicitada . ' unidades (stock resultante: ' . $stockResultante . ')'
        ];
    }
    
    /**
     * Eliminar (soft delete) una cotización. Solo admin.
     */
    public function eliminar(SolicitudCotizacion $solicitud)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No tiene permisos para eliminar cotizaciones.'
            ], 403);
        }

        try {
            $numero = $solicitud->numero_solicitud;
            $solicitud->delete(); // soft delete porque el modelo usa SoftDeletes

            return response()->json([
                'success' => true,
                'mensaje' => "Cotización {$numero} eliminada correctamente."
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar cotización: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar PDF de solicitud
     */
    public function descargarPdf(SolicitudCotizacion $solicitud)
    {
        $user = Auth::user();

        // Verificar que sea admin o vendedor
        if (!$user->hasRole('admin') && !$user->hasRole('vendedor')) {
            abort(403, 'No tiene permisos para descargar este PDF');
        }

        // Si es vendedor (y NO es admin), verificar que la solicitud sea de uno de sus clientes asignados
        if ($user->hasRole('vendedor') && !$user->hasRole('admin')) {
            if ($solicitud->cliente->vendedor_id != $user->id) {
                abort(403, 'No tiene permisos para descargar este PDF');
            }
        }

        // Cargar relaciones necesarias
        $solicitud->load([
            'cliente',
            'cliente.listaPrecio',
            'cliente.vendedor',
            'cliente.ciudad',
            'cliente.ciudad.departamento',
            'cliente.pais',
            'items.producto.imagenPrincipal',
                'items.producto.categoria',
            'aplicadaPor'
        ]);

        $pdf = PDF::loadView('pdf.cotizacion-excel-format', compact('solicitud'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('cotizacion-' . $solicitud->numero_solicitud . '.pdf');
    }
    
    /**
     * Exportar solicitudes a Excel
     */
    public function exportarExcel(Request $request)
    {
        // Verificar que sea admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'No tiene permisos para exportar solicitudes');
        }

        // Aplicar filtros
        $query = SolicitudCotizacion::with([
            'cliente',
            'cliente.vendedor',
            'items.producto',
            'items.varianteProducto',
            'aplicadaPor'
        ]);
        
        // Filtros opcionales
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        
        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }
        
        $solicitudes = $query->orderBy('created_at', 'desc')->get();
        
        return Excel::download(
            new SolicitudesExport($solicitudes), 
            'solicitudes-cotizacion-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }
}