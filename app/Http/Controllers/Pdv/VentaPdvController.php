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
use App\Models\FacturaSiigo;
use App\Services\VentaPdvServiceV2;
use App\Services\AutorizacionPdvService;
use App\Services\CajaService;
use App\Services\Siigo\SiigoApiClient;
use App\Services\Siigo\SiigoFacturacionService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Prefactura;

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
            $query = VentaPdv::with('usuario', 'cliente', 'caja', 'facturaSiigo');

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
                ->addColumn('factura_badge', function ($v) {
                    if (!$v->facturaSiigo) {
                        return '<span class="badge bg-secondary">Sin factura</span>';
                    }
                    $f = $v->facturaSiigo;
                    $badge = $f->estado_badge;
                    if ($f->numero_factura) {
                        $badge .= '<br><small class="text-muted">' . $f->numero_factura . '</small>';
                    }
                    return $badge;
                })
                ->addColumn('action', function ($v) {
                    $btn = '<button class="btn btn-sm btn-outline-info me-1" onclick="verDetalle(' . $v->id . ')" title="Detalle"><i class="bi bi-eye"></i></button>';
                    $btn .= '<a href="' . route('pdv.ventas.ticket', $v->id) . '" class="btn btn-sm btn-outline-danger me-1" title="Ticket" target="_blank"><i class="bi bi-printer"></i></a>';
                    if ($v->estado === 'completada' && auth()->user()->hasRole('admin')) {
                        $btn .= '<button class="btn btn-sm btn-outline-warning me-1" onclick="devolucionParcial(' . $v->id . ')" title="Devolución Parcial"><i class="bi bi-arrow-return-left"></i></button>';
                        $btn .= '<button class="btn btn-sm btn-outline-danger" onclick="anularVenta(' . $v->id . ')" title="Anular"><i class="bi bi-x-circle"></i></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['estado_badge', 'metodo_badge', 'factura_badge', 'action'])
                ->make(true);
        }

        $cajas = \App\Models\Caja::all();
        return view('pdv.venta.index', compact('cajas'));
    }

    public function crear(Request $request)
    {
        $user = auth()->user();
        $sesion = $this->cajaService->obtenerSesionActivaDeUsuario($user->id);

        if (!$sesion) {
            return redirect()->route('pdv.dashboard')
                ->with('error', 'Debe abrir una caja antes de realizar ventas');
        }

        $ubicacionIdDefault = $sesion->caja->ubicacion_id;

        $listasPrecioPdvConfig = ConfiguracionPdv::obtener('listas_precio_pdv', '');
        $listasPrecioIds = array_filter(explode(',', $listasPrecioPdvConfig));

        if (!empty($listasPrecioIds)) {
            $listasPrecios = ListaPrecio::where('activo', true)->whereIn('id', $listasPrecioIds)->orderBy('orden')->get();
        } else {
            $listasPrecios = ListaPrecio::where('activo', true)->orderBy('orden')->get();
        }
        $listasPrecioIdsPermitidas = $listasPrecios->pluck('id')->toArray();

        $listaPrecioDefault = ConfiguracionPdv::obtener('lista_precio_consumidor_final', 1);
        $ivaPorcentaje = ConfiguracionPdv::obtenerNumero('iva_porcentaje', 0);
        $descuentoMaximo = ConfiguracionPdv::obtenerNumero('descuento_maximo_cajero', 15);
        $requierePinDescuento = ConfiguracionPdv::obtener('requiere_pin_descuento_global', 'true') === 'true';
        $requierePinPrecio = ConfiguracionPdv::obtener('requiere_pin_precio', 'true') === 'true';
        $esAdmin = $user->hasRole('admin');

        // Load prefactura if editing
        $prefactura = null;
        $prefacturaItems = [];
        if ($request->has('prefactura_id')) {
            $prefactura = Prefactura::with(['items.producto', 'items.variante', 'cliente', 'usuarioCreador'])
                ->findOrFail($request->prefactura_id);

            if ($prefactura->estado !== 'pendiente') {
                return redirect()->route('pdv.prefacturas.pendientes')
                    ->with('error', 'Esta prefactura ya fue procesada');
            }

            $prefacturaItems = $prefactura->items->map(function ($i) use ($ubicacionIdDefault) {
                // Try to get stock for this product at the location
                $stockDisponible = null;
                if ($ubicacionIdDefault) {
                    $stockQuery = \App\Models\StockProducto::where('producto_id', $i->producto_id)
                        ->where('ubicacion_id', $ubicacionIdDefault);
                    if ($i->variante_producto_id) {
                        $stockQuery->where('variante_producto_id', $i->variante_producto_id);
                    } else {
                        $stockQuery->whereNull('variante_producto_id');
                    }
                    $stock = $stockQuery->first();
                    $stockDisponible = $stock ? ($stock->cantidad_disponible - $stock->cantidad_reservada) : 0;
                }

                return [
                    'producto_id' => $i->producto_id,
                    'variante_producto_id' => $i->variante_producto_id,
                    'nombre' => $i->producto->nombre ?? '',
                    'referencia' => $i->producto->referencia ?? '',
                    'variante_nombre' => $i->variante->nombre_variante ?? '-',
                    'cantidad' => $i->cantidad,
                    'precio_unitario' => (float) $i->precio_unitario,
                    'precio_original' => (float) $i->precio_original,
                    'descuento_porcentaje' => (float) $i->descuento_porcentaje,
                    'descuento_valor' => (float) $i->descuento_valor,
                    'stock_disponible' => $stockDisponible,
                    'controla_stock' => $i->producto->controlar_stock ?? false,
                    'iva' => (float) ($i->iva ?? 0),
                ];
            })->toArray();
        }

        $siigoActivo = ConfiguracionPdv::obtenerBoolean('siigo_activo', false);
        $siigoFacturarSiempre = ConfiguracionPdv::obtenerBoolean('siigo_facturar_siempre', false);
        $siigoModoTest = ConfiguracionPdv::obtener('siigo_modo', 'test') === 'test';

        return view('pdv.venta.crear', compact(
            'sesion', 'listasPrecios', 'listaPrecioDefault', 'ivaPorcentaje', 'descuentoMaximo',
            'requierePinDescuento', 'requierePinPrecio', 'esAdmin', 'ubicacionIdDefault',
            'prefactura', 'prefacturaItems',
            'siigoActivo', 'siigoFacturarSiempre', 'siigoModoTest',
            'listasPrecioIdsPermitidas'
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
            'prefactura_id' => $request->input('prefactura_id'),
        ];

        // Handle comprobante upload
        if ($request->hasFile('archivo_comprobante')) {
            $file = $request->file('archivo_comprobante');
            $nombre = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/comprobantes_pdv'), $nombre);
            $datosVenta['comprobante_pago'] = 'uploads/comprobantes_pdv/' . $nombre;
        }

        $resultado = $this->ventaService->crearVenta($datosVenta, $request->items, auth()->id(), $sesionId);

        // Mark prefactura as accepted if applicable
        if ($resultado['exito'] && $request->input('prefactura_id')) {
            $prefactura = Prefactura::find($request->input('prefactura_id'));
            if ($prefactura && $prefactura->estado === 'pendiente') {
                $prefactura->update([
                    'estado' => 'aceptada',
                    'usuario_cajero_id' => auth()->id(),
                    'aceptada_en' => now(),
                    'venta_pdv_id' => $resultado['venta']->id,
                ]);
            }
        }

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
        $venta = VentaPdv::with('items.producto', 'items.variante', 'items.devolucionesItems', 'usuario', 'cliente', 'caja.ubicacion', 'listaPrecio', 'anulador', 'sesionCaja', 'facturaSiigo.notasCredito', 'devolucionesParciales.items.producto', 'devolucionesParciales.items.variante', 'devolucionesParciales.usuario', 'devolucionesParciales.facturaSiigo')
            ->findOrFail($id);

        // Load SIIGO logs for this sale's invoices
        $siigoLogs = collect();
        if ($venta->facturaSiigo) {
            $facturaIds = collect([$venta->facturaSiigo->id]);
            $facturaIds = $facturaIds->merge($venta->facturaSiigo->notasCredito->pluck('id'));
            $siigoLogs = \App\Models\SiigoLog::whereIn('factura_siigo_id', $facturaIds)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('pdv.venta.partials.detalle-venta', compact('venta', 'siigoLogs'));
    }

    public function anular(Request $request, $id)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string|min:10',
        ]);

        $venta = VentaPdv::with('facturaSiigo')->findOrFail($id);
        $resultado = $this->ventaService->anularVenta($venta, auth()->id(), $request->motivo_anulacion);

        // Generate credit note if sale had an approved SIIGO invoice
        if ($resultado['exito'] && $venta->facturaSiigo && $venta->facturaSiigo->estaAprobada()) {
            try {
                $siigoService = app(SiigoFacturacionService::class);
                $notaCredito = $siigoService->crearNotaCredito($venta->facturaSiigo, $request->motivo_anulacion);
                $resultado['nota_credito'] = [
                    'estado' => $notaCredito->estado_dian,
                    'numero' => $notaCredito->numero_factura,
                ];
            } catch (\Exception $e) {
                $resultado['nota_credito_error'] = 'No se pudo generar la nota crédito: ' . $e->getMessage();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($resultado, $resultado['exito'] ? 200 : 422);
        }

        return redirect()->route('pdv.ventas.index')
            ->with($resultado['exito'] ? 'success' : 'error', $resultado['mensaje']);
    }

    public function itemsParaDevolucion($id)
    {
        $venta = VentaPdv::with('items.producto', 'items.variante', 'items.devolucionesItems')
            ->findOrFail($id);

        $items = $venta->items->map(function ($item) {
            $cantidadDevuelta = $item->cantidad_devuelta;
            $cantidadDisponible = $item->cantidad - $cantidadDevuelta;

            return [
                'item_venta_pdv_id' => $item->id,
                'producto_nombre' => $item->producto->nombre ?? 'Producto',
                'variante_nombre' => $item->variante
                    ? ($item->variante->referencia_variante ?? $item->variante->sku ?? '')
                    : null,
                'cantidad_original' => $item->cantidad,
                'cantidad_devuelta' => $cantidadDevuelta,
                'cantidad_disponible' => $cantidadDisponible,
                'precio_unitario' => (float) $item->precio_unitario,
                'descuento_porcentaje' => (float) ($item->descuento_porcentaje ?? 0),
                'iva_unitario' => $item->cantidad > 0 ? round((float) $item->iva / $item->cantidad, 2) : 0,
            ];
        })->filter(fn($item) => $item['cantidad_disponible'] > 0)->values();

        return response()->json(['items' => $items, 'numero_venta' => $venta->numero_venta]);
    }

    public function devolverParcial(Request $request, $id)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string|min:10',
            'items' => 'required|array|min:1',
            'items.*.item_venta_pdv_id' => 'required|integer',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        $venta = VentaPdv::with('facturaSiigo', 'items.devolucionesItems')->findOrFail($id);
        $resultado = $this->ventaService->devolverParcial(
            $venta,
            auth()->id(),
            $request->motivo_anulacion,
            $request->items
        );

        // Generate partial credit note if sale had an approved SIIGO invoice
        if ($resultado['exito'] && $venta->facturaSiigo && $venta->facturaSiigo->estaAprobada()) {
            try {
                $siigoService = app(SiigoFacturacionService::class);
                $notaCredito = $siigoService->crearNotaCreditoParcial(
                    $venta->facturaSiigo,
                    $resultado['devolucion'],
                    $request->motivo_anulacion
                );
                $resultado['nota_credito'] = [
                    'estado' => $notaCredito->estado_dian,
                    'numero' => $notaCredito->numero_factura,
                ];
            } catch (\Exception $e) {
                $resultado['nota_credito_error'] = 'No se pudo generar la nota crédito parcial: ' . $e->getMessage();
            }
        }

        return response()->json($resultado, $resultado['exito'] ? 200 : 422);
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
            'lista_precio_id' => 'nullable|exists:listas_precios,id',
        ]);

        $listaPrecioDefault = $request->lista_precio_id ?: ConfiguracionPdv::obtener('lista_precio_consumidor_final', 1);

        // Generate a unique identifier if none provided
        $documento = $request->documento;
        if (empty($documento)) {
            $documento = 'PDV-' . time() . '-' . rand(100, 999);
        }

        // Get default location (pais/ciudad) from an existing client or use fallbacks
        $defaults = Cliente::select('pais_id', 'ciudad_id')->first();

        $cliente = Cliente::create([
            'nombre_contacto' => $request->nombre,
            'numero_identificacion' => $documento,
            'telefono' => $request->telefono,
            'email' => $request->email ?: 'sin-email@pdv.local',
            'pais_id' => $defaults->pais_id ?? 1,
            'ciudad_id' => $defaults->ciudad_id ?? 1,
            'lista_precio_id' => $listaPrecioDefault,
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

    // =========================================
    // Facturación Electrónica SIIGO
    // =========================================

    public function generarFactura(Request $request, $id)
    {
        $venta = VentaPdv::with('items.producto', 'items.variante', 'cliente')->findOrFail($id);

        if ($venta->facturaSiigo && $venta->facturaSiigo->estaAprobada()) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Esta venta ya tiene una factura aprobada.',
            ], 422);
        }

        $siigoService = app(SiigoFacturacionService::class);
        $esPrueba = app(SiigoApiClient::class)->esModoTest();
        $prefijo = $esPrueba ? '(PRUEBA) ' : '';

        try {
            $tipo = $request->input('tipo_factura', 'con_cliente'); // 'con_cliente' or 'consumidor_final'

            if ($tipo === 'consumidor_final') {
                $factura = $siigoService->crearFacturaConsumidorFinal($venta);
            } else {
                $datosFiscales = [
                    'tipo_documento' => $request->input('tipo_documento', '13'),
                    'numero_identificacion' => $request->input('numero_identificacion'),
                    'nombre' => $request->input('nombre_fiscal'),
                    'razon_social' => $request->input('razon_social'),
                    'email' => $request->input('email_factura'),
                    'telefono' => $request->input('telefono'),
                ];

                $factura = $siigoService->crearFactura($venta, $datosFiscales, !empty($datosFiscales['email']));
            }

            return response()->json([
                'exito' => true,
                'modo_prueba' => $esPrueba,
                'factura' => [
                    'id' => $factura->id,
                    'estado_dian' => $factura->estado_dian,
                    'cufe' => $factura->cufe,
                    'numero_factura' => $factura->numero_factura,
                    'errores' => $factura->errores,
                ],
                'mensaje' => $factura->estado_dian === 'aprobada'
                    ? $prefijo . 'Factura electrónica generada exitosamente.'
                    : ($factura->estado_dian === 'error'
                        ? $prefijo . 'Error al generar la factura: ' . $factura->errores
                        : $prefijo . 'Factura enviada a SIIGO, estado: ' . $factura->estado_dian),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al generar factura: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reintentarFactura($id)
    {
        $venta = VentaPdv::with('facturaSiigo')->findOrFail($id);

        if (!$venta->facturaSiigo) {
            return response()->json(['exito' => false, 'mensaje' => 'Esta venta no tiene factura registrada.'], 404);
        }

        $siigoService = app(SiigoFacturacionService::class);

        try {
            $factura = $siigoService->reintentarFactura($venta->facturaSiigo);

            return response()->json([
                'exito' => true,
                'factura' => [
                    'id' => $factura->id,
                    'estado_dian' => $factura->estado_dian,
                    'cufe' => $factura->cufe,
                    'numero_factura' => $factura->numero_factura,
                    'errores' => $factura->errores,
                    'intentos' => $factura->intentos,
                ],
                'mensaje' => 'Reintento procesado.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al reintentar: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function descargarFacturaPdf($id)
    {
        $venta = VentaPdv::with('facturaSiigo')->findOrFail($id);

        if (!$venta->facturaSiigo || !$venta->facturaSiigo->siigo_invoice_id) {
            return response()->json(['exito' => false, 'mensaje' => 'No hay factura disponible para descarga.'], 404);
        }

        $siigoService = app(SiigoFacturacionService::class);

        try {
            $response = $siigoService->obtenerPdf($venta->facturaSiigo);

            if ($response->successful()) {
                $body = $response->json();
                $filename = "factura-{$venta->facturaSiigo->numero_factura}.pdf";

                // SIIGO returns PDF as base64 encoded in JSON: { id, cufe, base64 }
                if (isset($body['base64'])) {
                    $pdfContent = base64_decode($body['base64']);
                    return response($pdfContent, 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
                }

                // Fallback: direct PDF response
                return response($response->body(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
            }

            return response()->json(['exito' => false, 'mensaje' => 'No se pudo obtener el PDF.'], 500);
        } catch (\Exception $e) {
            return response()->json(['exito' => false, 'mensaje' => $e->getMessage()], 500);
        }
    }

    public function reenviarFacturaEmail($id)
    {
        $venta = VentaPdv::with('facturaSiigo')->findOrFail($id);

        if (!$venta->facturaSiigo || !$venta->facturaSiigo->siigo_invoice_id) {
            return response()->json(['exito' => false, 'mensaje' => 'No hay factura para reenviar.'], 404);
        }

        $siigoService = app(SiigoFacturacionService::class);

        try {
            $siigoService->reenviarEmail($venta->facturaSiigo);
            return response()->json(['exito' => true, 'mensaje' => 'Email reenviado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['exito' => false, 'mensaje' => $e->getMessage()], 500);
        }
    }

    public function consultarEstadoFactura($id)
    {
        $venta = VentaPdv::with('facturaSiigo')->findOrFail($id);

        if (!$venta->facturaSiigo) {
            return response()->json(['exito' => false, 'mensaje' => 'No hay factura registrada.'], 404);
        }

        $siigoService = app(SiigoFacturacionService::class);
        $resultado = $siigoService->consultarEstado($venta->facturaSiigo);

        return response()->json([
            'exito' => true,
            'factura' => [
                'estado_dian' => $resultado['estado'],
                'cufe' => $resultado['cufe'] ?? null,
                'numero_factura' => $resultado['numero'] ?? null,
            ],
            'mensaje' => $resultado['mensaje'],
        ]);
    }

    public function reintentarNotaCredito($id, $notaCreditoId)
    {
        $venta = VentaPdv::findOrFail($id);
        $notaCredito = FacturaSiigo::where('id', $notaCreditoId)
            ->where('tipo_documento', 'nota_credito')
            ->firstOrFail();

        if (!$notaCredito->puedeReintentar()) {
            return response()->json(['exito' => false, 'mensaje' => 'No se puede reintentar esta nota crédito.'], 422);
        }

        $siigoService = app(SiigoFacturacionService::class);

        try {
            $resultado = $siigoService->reintentarFactura($notaCredito);
            return response()->json([
                'exito' => true,
                'mensaje' => 'Reintento de nota crédito procesado. Estado: ' . $resultado->estado_dian,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al reintentar nota crédito: ' . $e->getMessage(),
            ], 500);
        }
    }
}
