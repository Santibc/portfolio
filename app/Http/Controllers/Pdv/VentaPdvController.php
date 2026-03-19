<?php

namespace App\Http\Controllers\Pdv;

use App\Http\Controllers\Controller;
use App\Models\VentaPdv;
use App\Models\Cliente;
use App\Models\ListaPrecio;
use App\Models\ConfiguracionPdv;
use App\Models\Producto;
use App\Models\PrecioVariante;
use App\Models\SesionCaja;
use App\Services\VentaPdvServiceV2;
use App\Services\AutorizacionPdvService;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaPdvController extends Controller
{
    protected VentaPdvServiceV2 $ventaService;
    protected AutorizacionPdvService $autorizacionService;
    protected CajaService $cajaService;

    public function __construct(
        VentaPdvServiceV2 $ventaService,
        AutorizacionPdvService $autorizacionService,
        CajaService $cajaService
    ) {
        $this->ventaService = $ventaService;
        $this->autorizacionService = $autorizacionService;
        $this->cajaService = $cajaService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = VentaPdv::with('usuario', 'cliente', 'caja');

            if (!auth()->user()->hasRole('admin')) {
                $sesion = $this->cajaService->obtenerSesionActivaDeUsuario(auth()->id());
                if ($sesion) {
                    $query->where('caja_id', $sesion->caja_id);
                }
            }

            if ($request->estado) {
                $query->where('estado', $request->estado);
            }
            if ($request->caja_id) {
                $query->where('caja_id', $request->caja_id);
            }
            if ($request->metodo_pago) {
                $query->where('metodo_pago', $request->metodo_pago);
            }
            if ($request->fecha_desde) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }
            if ($request->fecha_hasta) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }

            return DataTables::of($query)
                ->addColumn('cliente_display', function ($v) {
                    return $v->nombre_cliente ?: ($v->cliente ? ($v->cliente->razon_social ?: $v->cliente->nombre_contacto) : 'Consumidor Final');
                })
                ->addColumn('caja_nombre', fn($v) => $v->caja->nombre ?? '-')
                ->addColumn('usuario_nombre', fn($v) => $v->usuario->name ?? '-')
                ->addColumn('estado_badge', fn($v) => $v->estado === 'completada'
                    ? '<span class="badge bg-success">Completada</span>'
                    : '<span class="badge bg-danger">Anulada</span>')
                ->addColumn('metodo_badge', function ($v) {
                    $colors = ['efectivo' => 'success', 'transferencia' => 'info', 'mixto' => 'primary'];
                    return '<span class="badge bg-' . ($colors[$v->metodo_pago] ?? 'secondary') . '">' . ucfirst($v->metodo_pago) . '</span>';
                })
                ->addColumn('action', function ($v) {
                    $btn = '<button class="btn btn-sm btn-outline-info me-1" onclick="verDetalle(' . $v->id . ')" title="Detalle"><i class="bi bi-eye"></i></button>';
                    $btn .= '<a href="' . route('pdv.ventas.ticket', $v->id) . '" class="btn btn-sm btn-outline-danger me-1" title="Ticket" target="_blank"><i class="bi bi-printer"></i></a>';
                    if ($v->estado === 'completada' && auth()->user()->hasRole('admin')) {
                        $btn .= '<button class="btn btn-sm btn-outline-warning" onclick="anularVenta(' . $v->id . ')" title="Anular"><i class="bi bi-x-circle"></i></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['estado_badge', 'metodo_badge', 'action'])
                ->make(true);
        }

        $cajas = \App\Models\Caja::all();
        return view('pdv.venta.index', compact('cajas'));
    }

    public function crear()
    {
        $user = auth()->user();
        $sesion = $this->cajaService->obtenerSesionActivaDeUsuario($user->id);

        if (!$sesion && !$user->hasRole('admin')) {
            return redirect()->route('pdv.dashboard')
                ->with('error', 'Debe abrir una caja antes de realizar ventas');
        }

        $listasPrecios = ListaPrecio::where('activo', true)->get();
        $listaPrecioDefault = ConfiguracionPdv::obtener('lista_precio_consumidor_final', 1);
        $ivaPorcentaje = ConfiguracionPdv::obtenerNumero('iva_porcentaje', 0);
        $descuentoMaximo = ConfiguracionPdv::obtenerNumero('descuento_maximo_cajero', 15);

        return view('pdv.venta.crear', compact(
            'sesion', 'listasPrecios', 'listaPrecioDefault', 'ivaPorcentaje', 'descuentoMaximo'
        ));
    }

    public function procesarVenta(Request $request)
    {
        // Items may come as JSON string (FormData) or array (JSON request)
        $items = $request->input('items');
        if (is_string($items)) {
            $items = json_decode($items, true);
            $request->merge(['items' => $items]);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,transferencia,mixto',
        ]);

        $sesion = $this->cajaService->obtenerSesionActivaDeUsuario(auth()->id());
        $sesionId = $sesion ? $sesion->id : null;

        $datosVenta = [
            'ubicacion_id' => $sesion ? $sesion->caja->ubicacion_id : $request->ubicacion_id,
            'cliente_id' => $request->cliente_id,
            'nombre_cliente' => $request->nombre_cliente ?? 'Consumidor Final',
            'lista_precio_id' => $request->lista_precio_id,
            'descuento_global' => $request->descuento_global ?? 0,
            'metodo_pago' => $request->metodo_pago,
            'monto_efectivo' => $request->monto_efectivo,
            'monto_transferencia' => $request->monto_transferencia,
            'monto_recibido' => $request->monto_recibido,
            'cambio' => $request->cambio,
            'tipo_transferencia' => $request->tipo_transferencia,
            'comprobante_pago' => $request->comprobante_pago,
            'notas' => $request->notas,
            'descuento_autorizado_por' => $request->descuento_autorizado_por,
            'precio_autorizado_por' => $request->precio_autorizado_por,
        ];

        // Handle comprobante upload
        if ($request->hasFile('archivo_comprobante')) {
            $file = $request->file('archivo_comprobante');
            $nombre = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/comprobantes_pdv'), $nombre);
            $datosVenta['comprobante_pago'] = 'uploads/comprobantes_pdv/' . $nombre;
        }

        $resultado = $this->ventaService->crearVenta($datosVenta, $request->items, auth()->id(), $sesionId);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($resultado, $resultado['exito'] ? 200 : 422);
        }

        if (!$resultado['exito']) {
            return redirect()->back()->with('error', $resultado['mensaje'])->withInput();
        }

        return redirect()->route('pdv.ventas.crear')
            ->with('success', $resultado['mensaje'])
            ->with('venta_id', $resultado['venta']->id);
    }

    public function detalle($id)
    {
        $venta = VentaPdv::with('items.producto', 'items.variante', 'usuario', 'cliente', 'caja.ubicacion', 'listaPrecio', 'anulador', 'sesionCaja')
            ->findOrFail($id);

        return view('pdv.venta.partials.detalle-venta', compact('venta'));
    }

    public function anular(Request $request, $id)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string|min:10',
        ]);

        $venta = VentaPdv::findOrFail($id);
        $resultado = $this->ventaService->anularVenta($venta, auth()->id(), $request->motivo_anulacion);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($resultado, $resultado['exito'] ? 200 : 422);
        }

        return redirect()->route('pdv.ventas.index')
            ->with($resultado['exito'] ? 'success' : 'error', $resultado['mensaje']);
    }

    public function ticket($id)
    {
        $venta = VentaPdv::with('items.producto', 'items.variante', 'usuario', 'cliente', 'caja.ubicacion')
            ->findOrFail($id);

        $pdf = Pdf::loadView('pdv.pdf.ticket', compact('venta'))
            ->setPaper([0, 0, 226.77, 600], 'portrait');

        return $pdf->stream("ticket-{$venta->numero_venta}.pdf");
    }

    public function buscarProductos(Request $request)
    {
        $termino = $request->q ?? '';
        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $sesion = $this->cajaService->obtenerSesionActivaDeUsuario(auth()->id());
        $ubicacionId = $sesion ? $sesion->caja->ubicacion_id : ($request->ubicacion_id ?? 0);
        $listaPrecioId = $request->lista_precio_id;

        $productos = $this->ventaService->buscarProductos($termino, $ubicacionId, $listaPrecioId);

        return response()->json($productos);
    }

    public function buscarClientes(Request $request)
    {
        $termino = $request->q ?? '';
        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $clientes = Cliente::where('activo', true)
            ->where(function ($query) use ($termino) {
                $query->where('nombre_contacto', 'like', "%{$termino}%")
                    ->orWhere('razon_social', 'like', "%{$termino}%")
                    ->orWhere('numero_identificacion', 'like', "%{$termino}%")
                    ->orWhere('telefono', 'like', "%{$termino}%")
                    ->orWhere('email', 'like', "%{$termino}%");
            })
            ->with('listaPrecio')
            ->limit(10)
            ->get()
            ->map(function ($cliente) {
                $nombre = $cliente->razon_social ?: $cliente->nombre_contacto;
                return [
                    'id' => $cliente->id,
                    'nombre' => $nombre,
                    'documento' => $cliente->numero_identificacion,
                    'telefono' => $cliente->telefono,
                    'email' => $cliente->email,
                    'lista_precio_id' => $cliente->lista_precio_id,
                    'lista_precio_nombre' => $cliente->listaPrecio->nombre ?? '-',
                ];
            });

        return response()->json($clientes);
    }

    public function crearClienteRapido(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
        ]);

        $cliente = Cliente::create([
            'nombre_contacto' => $request->nombre,
            'numero_identificacion' => $request->documento,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'activo' => true,
        ]);

        $nombre = $cliente->razon_social ?: $cliente->nombre_contacto;
        return response()->json([
            'id' => $cliente->id,
            'nombre' => $nombre,
            'documento' => $cliente->numero_identificacion,
            'telefono' => $cliente->telefono,
            'email' => $cliente->email,
            'lista_precio_id' => $cliente->lista_precio_id,
            'lista_precio_nombre' => $cliente->listaPrecio->nombre ?? 'Sin lista',
        ]);
    }

    public function obtenerPrecios(Request $request)
    {
        $request->validate([
            'lista_precio_id' => 'required|exists:listas_precios,id',
            'items' => 'required|array',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.variante_producto_id' => 'nullable|exists:variantes_productos,id',
        ]);

        $listaPrecioId = $request->lista_precio_id;
        $precios = [];

        foreach ($request->items as $item) {
            $producto = Producto::find($item['producto_id']);
            $varianteId = $item['variante_producto_id'] ?? null;

            if ($varianteId) {
                $precioVariante = PrecioVariante::where('variante_producto_id', $varianteId)
                    ->where('lista_precio_id', $listaPrecioId)
                    ->first();
                $precio = $precioVariante ? $precioVariante->precio : ($producto->getPrecioPorLista($listaPrecioId) ?? 0);
            } else {
                $precio = $producto->getPrecioPorLista($listaPrecioId) ?? 0;
            }

            $precios[] = [
                'producto_id' => $item['producto_id'],
                'variante_producto_id' => $varianteId,
                'precio' => (float) $precio,
            ];
        }

        return response()->json($precios);
    }

    public function verificarPin(Request $request)
    {
        $request->validate(['pin' => 'required|string|min:4|max:6']);

        $admin = $this->autorizacionService->verificarPin($request->pin);

        if (!$admin) {
            return response()->json(['exito' => false, 'mensaje' => 'PIN incorrecto'], 401);
        }

        return response()->json([
            'exito' => true,
            'autorizador_id' => $admin->id,
            'autorizador_nombre' => $admin->name,
        ]);
    }

    public function verificarStock(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'ubicacion_id' => 'required|integer',
        ]);

        $resultado = $this->ventaService->verificarDisponibilidadItems($request->items, $request->ubicacion_id);

        return response()->json($resultado);
    }
}
