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

            // Aislamiento por sede: cada usuario ve únicamente las prefacturas de su
            // tienda asignada (users.ubicacion_id). El admin ve todas las sedes.
            if (!$user->hasRole('admin') && $user->ubicacion_id) {
                $query->where('ubicacion_id', $user->ubicacion_id);
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

        // Aislamiento por sede: un usuario no-admin solo puede crear en su tienda asignada.
        $user = auth()->user();
        $ubicaciones = Ubicacion::activas()->tiendas()
            ->when(!$user->hasRole('admin') && $user->ubicacion_id,
                fn($q) => $q->where('id', $user->ubicacion_id))
            ->get();
        $descuentoMaximo = (float) ConfiguracionPdv::obtener('descuento_maximo_cajero', 15);
        // Vendedoras filtradas por la sede del usuario (las de su tienda + las globales).
        // El admin ve todas.
        $vendedorasPrefactura = ($user->hasRole('admin') || !$user->ubicacion_id)
            ? \App\Models\VendedoraPrefactura::nombresActivos()
            : \App\Models\VendedoraPrefactura::nombresActivosPorUbicacion($user->ubicacion_id);

        return view('pdv.prefacturas.crear', compact('listasPrecios', 'ubicaciones', 'descuentoMaximo', 'vendedorasPrefactura'));
    }

    public function guardar(Request $request)
    {
        $user = auth()->user();
        // Vendedoras permitidas según la sede del usuario (su tienda + globales); admin todas.
        $vendedorasPermitidas = ($user->hasRole('admin') || !$user->ubicacion_id)
            ? \App\Models\VendedoraPrefactura::nombresActivos()
            : \App\Models\VendedoraPrefactura::nombresActivosPorUbicacion($user->ubicacion_id);

        $request->validate([
            'lista_precio_id' => 'required|exists:listas_precios,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'vendedora_prefactura' => ['required', \Illuminate\Validation\Rule::in($vendedorasPermitidas)],
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $datos = $request->only(['cliente_id', 'nombre_cliente', 'lista_precio_id', 'ubicacion_id', 'descuento_global', 'iva', 'observaciones', 'vendedora_prefactura']);

        // Aislamiento por sede: un usuario no-admin siempre crea en su tienda asignada,
        // sin importar lo que llegue en el request.
        if (!$user->hasRole('admin') && $user->ubicacion_id) {
            $datos['ubicacion_id'] = $user->ubicacion_id;
        }

        $resultado = $this->prefacturaService->crear(
            $datos,
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
                        'vendedora' => $p->vendedora_prefactura,
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

        $this->verificarSede(Prefactura::findOrFail($id));

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

        $this->verificarSede(Prefactura::findOrFail($id));

        $resultado = $this->prefacturaService->anular($id, auth()->id(), $request->motivo_anulacion);

        return response()->json($resultado, $resultado['exito'] ? 200 : 422);
    }

    public function actualizar(Request $request, $id)
    {
        $items = $request->input('items');
        if (is_string($items)) {
            $items = json_decode($items, true);
            $request->merge(['items' => $items]);
        }

        $request->validate([
            'lista_precio_id' => 'required|exists:listas_precios,id',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $this->verificarSede(Prefactura::findOrFail($id));

        $datos = $request->only([
            'cliente_id', 'nombre_cliente', 'lista_precio_id',
            'descuento_global', 'observaciones',
        ]);

        $resultado = $this->prefacturaService->actualizar($id, $datos, $request->items);

        return response()->json($resultado, $resultado['exito'] ? 200 : 422);
    }

    public function detalle($id)
    {
        $prefactura = Prefactura::with('items.producto', 'items.variante', 'usuarioCreador', 'usuarioCajero', 'cliente')
            ->findOrFail($id);

        $this->verificarSede($prefactura);

        return view('pdv.prefacturas.partials.detalle', compact('prefactura'));
    }

    /**
     * Aislamiento por sede: un usuario no-admin solo puede ver/gestionar prefacturas
     * de su propia tienda asignada (users.ubicacion_id). El admin no tiene restricción.
     */
    private function verificarSede(Prefactura $prefactura): void
    {
        $user = auth()->user();
        if (!$user->hasRole('admin') && $user->ubicacion_id
            && (int) $prefactura->ubicacion_id !== (int) $user->ubicacion_id) {
            abort(403, 'No tiene acceso a prefacturas de otra sede.');
        }
    }
}
