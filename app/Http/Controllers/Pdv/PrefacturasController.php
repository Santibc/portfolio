<?php

namespace App\Http\Controllers\Pdv;

use App\Http\Controllers\Controller;
use App\Models\Prefactura;
use App\Models\ListaPrecio;
use App\Models\Ubicacion;
use App\Models\ConfiguracionPdv;
use App\Services\PrefacturaService;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PrefacturasController extends Controller
{
    protected PrefacturaService $prefacturaService;
    protected CajaService $cajaService;

    public function __construct(PrefacturaService $prefacturaService, CajaService $cajaService)
    {
        $this->prefacturaService = $prefacturaService;
        $this->cajaService = $cajaService;
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Prefactura::with('usuarioCreador', 'usuarioCajero', 'cliente');

            $user = auth()->user();
            if ($user->hasRole(['auxiliar_venta']) && !$user->hasRole(['admin', 'cajero_principal'])) {
                $query->where('usuario_creador_id', $user->id);
            }

            if ($request->estado) {
                $query->where('estado', $request->estado);
            }

            return DataTables::of($query->orderByDesc('created_at'))
                ->addColumn('creador_nombre', fn($p) => $p->usuarioCreador->name ?? '-')
                ->addColumn('cajero_nombre', fn($p) => $p->usuarioCajero->name ?? '-')
                ->addColumn('cliente_display', fn($p) => $p->nombre_cliente_display)
                ->addColumn('estado_badge', fn($p) => $p->estado_badge)
                ->addColumn('action', function ($p) {
                    $btn = '<button class="btn btn-sm btn-outline-info me-1" onclick="verDetalle(' . $p->id . ')" title="Ver"><i class="bi bi-eye"></i></button>';
                    if ($p->estado === 'pendiente' && auth()->user()->hasRole(['admin', 'cajero_principal'])) {
                        $btn .= '<button class="btn btn-sm btn-outline-success me-1" onclick="aceptarPrefactura(' . $p->id . ')" title="Aceptar"><i class="bi bi-check-circle"></i></button>';
                        $btn .= '<button class="btn btn-sm btn-outline-danger" onclick="anularPrefactura(' . $p->id . ')" title="Anular"><i class="bi bi-x-circle"></i></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }

        return view('pdv.prefacturas.index');
    }

    public function crear()
    {
        $listasPrecioPdvConfig = ConfiguracionPdv::obtener('listas_precio_pdv', '');
        $listasPrecioIds = array_filter(explode(',', $listasPrecioPdvConfig));

        if (!empty($listasPrecioIds)) {
            $listasPrecios = ListaPrecio::where('activo', true)->whereIn('id', $listasPrecioIds)->orderBy('orden')->get();
        } else {
            $listasPrecios = ListaPrecio::where('activo', true)->orderBy('orden')->get();
        }

        $ubicaciones = Ubicacion::activas()->tiendas()->get();
        $descuentoMaximo = (float) ConfiguracionPdv::obtener('descuento_maximo_cajero', 15);

        return view('pdv.prefacturas.crear', compact('listasPrecios', 'ubicaciones', 'descuentoMaximo'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'lista_precio_id' => 'required|exists:listas_precios,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $resultado = $this->prefacturaService->crear(
            $request->only(['cliente_id', 'nombre_cliente', 'lista_precio_id', 'ubicacion_id', 'descuento_global', 'iva', 'observaciones']),
            $request->items,
            auth()->id()
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($resultado, $resultado['exito'] ? 200 : 422);
        }

        if (!$resultado['exito']) {
            return redirect()->back()->with('error', $resultado['mensaje'])->withInput();
        }

        return redirect()->route('pdv.prefacturas.index')
            ->with('success', $resultado['mensaje']);
    }

    public function pendientes(Request $request)
    {
        $sesion = $this->cajaService->obtenerSesionActivaDeUsuario(auth()->id());

        $query = Prefactura::with([
                'usuarioCreador',
                'cliente',
                'items.producto:id,siigo_product_code',
                'items.variante:id,siigo_product_code',
            ])
            ->pendientes()
            ->orderByDesc('created_at');

        if ($sesion) {
            $query->where('ubicacion_id', $sesion->caja->ubicacion_id);
        }

        $prefacturas = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'count' => $prefacturas->count(),
                'prefacturas' => $prefacturas->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'numero' => $p->numero_prefactura,
                        'cliente' => $p->nombre_cliente_display,
                        'total' => $p->total,
                        'items_count' => $p->items->count(),
                        'creador' => $p->usuarioCreador->name ?? '-',
                        'creada' => $p->created_at->diffForHumans(),
                    ];
                }),
            ]);
        }

        return view('pdv.prefacturas.pendientes', compact('prefacturas'));
    }

    public function aceptar(Request $request, $id)
    {
        $request->validate([
            'metodo_pago' => 'required|in:efectivo,transferencia,mixto',
        ]);

        $sesion = $this->cajaService->obtenerSesionActivaDeUsuario(auth()->id());
        if (!$sesion) {
            return response()->json(['exito' => false, 'mensaje' => 'Debe tener una caja abierta'], 422);
        }

        $datosModificados = $request->only([
            'metodo_pago', 'monto_efectivo', 'monto_transferencia',
            'monto_recibido', 'cambio', 'tipo_transferencia',
            'descuento_global', 'items',
        ]);

        // Handle comprobante upload
        if ($request->hasFile('archivo_comprobante')) {
            $file = $request->file('archivo_comprobante');
            $nombre = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/comprobantes_pdv'), $nombre);
            $datosModificados['comprobante_pago'] = 'uploads/comprobantes_pdv/' . $nombre;
        }

        $resultado = $this->prefacturaService->aceptar($id, auth()->id(), $sesion->id, $datosModificados);

        return response()->json($resultado, $resultado['exito'] ? 200 : 422);
    }

    public function anular(Request $request, $id)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string|min:5',
        ]);

        $resultado = $this->prefacturaService->anular($id, auth()->id(), $request->motivo_anulacion);

        return response()->json($resultado, $resultado['exito'] ? 200 : 422);
    }

    public function detalle($id)
    {
        $prefactura = Prefactura::with('items.producto', 'items.variante', 'usuarioCreador', 'usuarioCajero', 'cliente')
            ->findOrFail($id);

        return view('pdv.prefacturas.partials.detalle', compact('prefactura'));
    }
}
