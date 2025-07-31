<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudCotizacion;
use App\Models\Cliente;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class SolicitudController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            
            $query = SolicitudCotizacion::with(['cliente', 'cliente.vendedor', 'items'])
                                       ->select('solicitudes_cotizacion.*');
            
            // Filtrar por rol
            if ($user->hasRole('vendedor')) {
                // Vendedor solo ve solicitudes de sus clientes
                $query->whereHas('cliente', function($q) use ($user) {
                    $q->where('vendedor_id', $user->id);
                });
            }
            // Admin ve todas las solicitudes (no se aplica filtro adicional)
            
            return DataTables::of($query)
                ->addColumn('cliente_nombre', function($s) {
                    return $s->cliente->nombre_contacto;
                })
                ->addColumn('vendedor', function($s) {
                    return $s->cliente->vendedor?->name ?? 'Sin vendedor';
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
                    $class = $s->estado === 'pendiente' ? 'warning' : 'success';
                    $text = $s->estado === 'pendiente' ? 'Pendiente' : 'Aplicada';
                    return '<span class="badge bg-'.$class.'">'.$text.'</span>';
                })
                ->addColumn('action', function($s) {
                    $buttons = '<div class="d-flex justify-content-center gap-1">';
                    
                    // Botón ver detalle
                    $buttons .= '<button type="button" class="btn btn-outline-info btn-sm" 
                                        title="Ver Detalle" onclick="verDetalle('.$s->id.')">
                                   <i class="bi bi-eye"></i>
                                </button>';
                    
                    // Botón aplicar (solo si está pendiente)
                    if ($s->estado === 'pendiente') {
                        $buttons .= '<button type="button" class="btn btn-outline-success btn-sm" 
                                            title="Marcar como Aplicada" onclick="marcarAplicada('.$s->id.')">
                                       <i class="bi bi-check-circle"></i>
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
                    $query->whereHas('cliente.vendedor', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }
        
        return view('solicitudes.solicitudes_index');
    }
    
    public function detalle(SolicitudCotizacion $solicitud)
    {
        // Verificar permisos
        $user = Auth::user();
        if ($user->hasRole('vendedor') && $solicitud->cliente->vendedor_id !== $user->id) {
            return response()->json(['error' => 'No tiene permisos para ver esta solicitud'], 403);
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
        
        // Información de la solicitud
        $html .= '<div class="col-md-6">';
        $html .= '<h6>Información de la Solicitud</h6>';
        $html .= '<table class="table table-sm">';
        $html .= '<tr><td><strong>Número:</strong></td><td><code>' . $solicitud->numero_solicitud . '</code></td></tr>';
        $html .= '<tr><td><strong>Fecha:</strong></td><td>' . $solicitud->created_at->format('d/m/Y H:i') . '</td></tr>';
        $html .= '<tr><td><strong>Estado:</strong></td><td>';
        if ($solicitud->estado === 'pendiente') {
            $html .= '<span class="badge bg-warning">Pendiente</span>';
        } else {
            $html .= '<span class="badge bg-success">Aplicada</span>';
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
        
        $html .= '</table>';
        $html .= '</div>';
        
        // Notas del cliente
        if ($solicitud->notas_cliente) {
            $html .= '<div class="col-12 mb-3">';
            $html .= '<h6>Notas del Cliente</h6>';
            $html .= '<div class="alert alert-info">' . nl2br(e($solicitud->notas_cliente)) . '</div>';
            $html .= '</div>';
        }
        
        // Items de la solicitud
        $html .= '<div class="col-12">';
        $html .= '<h6>Productos Solicitados</h6>';
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
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '<tfoot>';
        $html .= '<tr>';
        $html .= '<th colspan="5" class="text-end">Total:</th>';
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
        
        // Campo de observaciones si está pendiente
        if ($solicitud->estado === 'pendiente') {
            $html .= '<div class="col-12 mt-3">';
            $html .= '<hr>';
            $html .= '<div class="mb-3">';
            $html .= '<label class="form-label">Observaciones del Administrador</label>';
            $html .= '<textarea class="form-control" id="observacionesAdmin" rows="3" 
                              placeholder="Ingrese cualquier observación sobre esta solicitud..."></textarea>';
            $html .= '</div>';
            $html .= '<button type="button" class="btn btn-success w-100" onclick="confirmarAplicar(' . $solicitud->id . ')">
                        <i class="bi bi-check-circle"></i> Marcar como Aplicada
                      </button>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return response($html);
    }
    
    public function aplicar(Request $request, SolicitudCotizacion $solicitud)
    {
        // Verificar permisos
        $user = Auth::user();
        if ($user->hasRole('vendedor') && $solicitud->cliente->vendedor_id !== $user->id) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No tiene permisos para aplicar esta solicitud'
            ], 403);
        }
        
        // Verificar que esté pendiente
        if ($solicitud->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'mensaje' => 'Esta solicitud ya fue aplicada'
            ], 400);
        }
        
        $request->validate([
            'observaciones' => 'nullable|string|max:1000'
        ]);
        
        try {
            $solicitud->marcarComoAplicada($user->id, $request->observaciones);
            
            return response()->json([
                'success' => true,
                'mensaje' => 'Solicitud marcada como aplicada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al aplicar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
}