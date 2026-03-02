<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\OrdenFoto;
use App\Models\OrdenPieza;
use App\Services\OrdenEstadoService;
use App\Traits\RegistraActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class EntregaController extends Controller
{
    use RegistraActividad;

    protected OrdenEstadoService $estadoService;

    public function __construct(OrdenEstadoService $estadoService)
    {
        $this->estadoService = $estadoService;
    }

    /**
     * GET /recepcion/entregas-pendientes - Listado de ordenes con piezas listas para entregar.
     */
    public function pendientes(Request $request)
    {
        if ($request->ajax()) {
            $query = Orden::whereHas('piezas', function ($q) {
                $q->where('porcentaje_avance', '>=', 100)->where('entregada', false);
            })
                ->whereNotIn('estado_trabajo', ['borrador', 'anulada'])
                ->with('cliente')
                ->select('ordenes.*');

            return DataTables::of($query)
                ->addColumn('cliente_nombre', function ($o) {
                    return $o->cliente->nombre ?? '-';
                })
                ->addColumn('piezas_listas', function ($o) {
                    $total = $o->piezas->count();
                    $listas = $o->piezas->where('porcentaje_avance', '>=', 100)->where('entregada', false)->count();
                    return '<span class="fw-semibold text-success">' . $listas . '</span> de ' . $total;
                })
                ->addColumn('estado_trabajo_badge', function ($o) {
                    return $this->badgeEstadoTrabajo($o->estado_trabajo);
                })
                ->addColumn('estado_entrega_badge', function ($o) {
                    return $this->badgeEstadoEntrega($o->estado_entrega);
                })
                ->addColumn('acciones', function ($o) {
                    $flujoUrl = route('recepcion.entregas.flujo', $o);
                    $html = '<div class="action-buttons justify-content-end">';
                    $html .= '<a href="' . $flujoUrl . '" class="action-btn view" title="Entregar" data-tooltip="Entregar"><i class="bi bi-box-arrow-right"></i></a>';
                    $html .= '<button type="button" class="action-btn btn-entrega-rapida" data-orden-id="' . $o->id . '" title="Entrega Rapida" data-tooltip="Entrega Rapida"><i class="bi bi-lightning"></i></button>';
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('numero_orden', function ($o) {
                    return '<span class="fw-semibold">' . ($o->numero_orden ?? '-') . '</span>';
                })
                ->editColumn('fecha_entrega', function ($o) {
                    if (!$o->fecha_entrega) return '<span class="text-muted">-</span>';
                    $hoy = now()->startOfDay();
                    $fecha = $o->fecha_entrega;
                    $class = '';
                    if ($fecha->lt($hoy)) {
                        $class = ' text-danger fw-semibold';
                    } elseif ($fecha->eq($hoy)) {
                        $class = ' text-warning fw-semibold';
                    }
                    return '<span class="' . $class . '">' . $fecha->format('d/m/Y') . '</span>';
                })
                ->rawColumns(['numero_orden', 'piezas_listas', 'estado_trabajo_badge', 'estado_entrega_badge', 'fecha_entrega', 'acciones'])
                ->make(true);
        }

        // Stats para cards
        $baseQuery = fn() => Orden::whereHas('piezas', function ($q) {
            $q->where('porcentaje_avance', '>=', 100)->where('entregada', false);
        })->whereNotIn('estado_trabajo', ['borrador', 'anulada']);

        $totalPendientes = $baseQuery()->count();

        $piezasListas = OrdenPieza::where('porcentaje_avance', '>=', 100)
            ->where('entregada', false)
            ->whereHas('orden', function ($q) {
                $q->whereNotIn('estado_trabajo', ['borrador', 'anulada']);
            })
            ->count();

        $entregasHoy = OrdenPieza::where('entregada', true)
            ->whereDate('entregada_en', today())
            ->count();

        $entregasVencidas = $baseQuery()->whereDate('fecha_entrega', '<', today())->count();

        return view('entregas.pendientes', compact(
            'totalPendientes', 'piezasListas', 'entregasHoy', 'entregasVencidas'
        ));
    }

    /**
     * GET /recepcion/entregas-pendientes/{orden}/flujo - Wizard de entrega.
     */
    public function flujo(Orden $orden)
    {
        if (in_array($orden->estado_trabajo, ['borrador', 'anulada'])) {
            return redirect()->route('recepcion.entregas-pendientes')
                ->with('error', 'Esta orden no permite entregas.');
        }

        $piezasEntregables = $orden->piezas()
            ->where('porcentaje_avance', '>=', 100)
            ->where('entregada', false)
            ->with('bosquejo')
            ->orderBy('orden_visual')
            ->get();

        if ($piezasEntregables->isEmpty()) {
            return redirect()->route('recepcion.entregas-pendientes')
                ->with('info', 'No hay piezas listas para entregar en esta orden.');
        }

        $orden->load('cliente');

        return view('entregas.flujo', compact('orden', 'piezasEntregables'));
    }

    /**
     * POST /recepcion/entregas-pendientes/{orden}/entregar - Marca piezas como entregadas.
     */
    public function entregarPiezas(Request $request, Orden $orden)
    {
        $request->validate([
            'pieza_ids' => 'required|array|min:1',
            'pieza_ids.*' => 'required|integer',
        ]);

        $user = $request->user();
        $entregadas = [];

        DB::beginTransaction();
        try {
            foreach ($request->input('pieza_ids') as $piezaId) {
                $pieza = OrdenPieza::where('id', $piezaId)
                    ->where('orden_id', $orden->id)
                    ->where('porcentaje_avance', '>=', 100)
                    ->where('entregada', false)
                    ->first();

                if (!$pieza) continue;

                $pieza->update([
                    'entregada' => true,
                    'entregada_en' => now(),
                    'entregada_por' => $user->id,
                    'estado' => 'entregada',
                ]);

                $entregadas[] = $pieza->nombre;

                $this->registrarActividad(
                    'pieza.entregada',
                    "Pieza '{$pieza->nombre}' entregada al cliente (Orden {$orden->numero_orden})",
                    $orden->id,
                    ['pieza_id' => $pieza->id, 'pieza_nombre' => $pieza->nombre]
                );
            }

            $orden->load('piezas');
            $this->estadoService->recalcularTodo($orden);

            DB::commit();

            $count = count($entregadas);
            return response()->json([
                'success' => true,
                'message' => $count . ' pieza(s) entregada(s) exitosamente.',
                'piezas_entregadas' => $count,
                'estado_entrega' => $orden->estado_entrega,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la entrega: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /recepcion/entregas-pendientes/{orden}/entrega-rapida - Entrega todas las piezas listas.
     */
    public function entregaRapida(Orden $orden)
    {
        if (in_array($orden->estado_trabajo, ['borrador', 'anulada'])) {
            return response()->json([
                'success' => false,
                'message' => 'Esta orden no permite entregas.',
            ], 422);
        }

        $piezasListas = $orden->piezas()
            ->where('porcentaje_avance', '>=', 100)
            ->where('entregada', false)
            ->get();

        if ($piezasListas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay piezas listas para entrega rapida.',
            ], 422);
        }

        $user = auth()->user();
        $entregadas = [];

        DB::beginTransaction();
        try {
            foreach ($piezasListas as $pieza) {
                $pieza->update([
                    'entregada' => true,
                    'entregada_en' => now(),
                    'entregada_por' => $user->id,
                    'estado' => 'entregada',
                ]);

                $entregadas[] = $pieza->nombre;

                $this->registrarActividad(
                    'pieza.entregada',
                    "Pieza '{$pieza->nombre}' entregada al cliente (Orden {$orden->numero_orden})",
                    $orden->id,
                    ['pieza_id' => $pieza->id, 'pieza_nombre' => $pieza->nombre]
                );
            }

            $orden->load('piezas');
            $this->estadoService->recalcularTodo($orden);

            DB::commit();

            $count = count($entregadas);
            return response()->json([
                'success' => true,
                'message' => $count . ' pieza(s) entregada(s) exitosamente.',
                'piezas_entregadas' => $count,
                'estado_entrega' => $orden->estado_entrega,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la entrega rapida: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /recepcion/entregas-pendientes/{orden}/foto-entrega - Sube foto de entrega.
     */
    public function subirFotoEntrega(Request $request, Orden $orden)
    {
        $request->validate([
            'foto' => 'required|image|max:5120',
        ]);

        $directorio = public_path("uploads/ordenes/{$orden->id}/fotos");
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $archivo = $request->file('foto');
        $nombreArchivo = 'entrega_' . time() . '_' . rand(100, 999) . '.' . $archivo->getClientOriginalExtension();
        $archivo->move($directorio, $nombreArchivo);

        $rutaRelativa = "uploads/ordenes/{$orden->id}/fotos/{$nombreArchivo}";

        $foto = OrdenFoto::create([
            'orden_id' => $orden->id,
            'orden_pieza_id' => null,
            'tipo_foto' => 'entrega',
            'ruta_archivo' => $rutaRelativa,
            'ruta_miniatura' => null,
            'subido_por' => auth()->id(),
            'aprobada' => false,
        ]);

        return response()->json([
            'success' => true,
            'foto' => [
                'id' => $foto->id,
                'url' => asset($rutaRelativa),
            ],
        ]);
    }

    /**
     * GET /recepcion/entregas-historial - Historial de piezas entregadas.
     */
    public function historial(Request $request)
    {
        if ($request->ajax()) {
            $query = OrdenPieza::where('entregada', true)
                ->with(['orden.cliente', 'entregadaPorUsuario'])
                ->select('orden_piezas.*');

            return DataTables::of($query)
                ->addColumn('fecha_entrega_formatted', function ($p) {
                    return $p->entregada_en ? $p->entregada_en->format('d/m/Y H:i') : '-';
                })
                ->addColumn('numero_orden', function ($p) {
                    $url = route('recepcion.ordenes.show', $p->orden_id);
                    return '<a href="' . $url . '" class="fw-semibold text-decoration-none">' . ($p->orden->numero_orden ?? '-') . '</a>';
                })
                ->addColumn('cliente_nombre', function ($p) {
                    return $p->orden->cliente->nombre ?? '-';
                })
                ->addColumn('entregado_por_nombre', function ($p) {
                    return $p->entregadaPorUsuario->name ?? '-';
                })
                ->editColumn('cantidad', function ($p) {
                    return '<span class="text-center d-block">' . $p->cantidad . '</span>';
                })
                ->filterColumn('numero_orden', function ($query, $keyword) {
                    $query->whereHas('orden', function ($q) use ($keyword) {
                        $q->where('numero_orden', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('cliente_nombre', function ($query, $keyword) {
                    $query->whereHas('orden.cliente', function ($q) use ($keyword) {
                        $q->where('nombre', 'like', "%{$keyword}%");
                    });
                })
                ->orderColumn('fecha_entrega_formatted', function ($query, $order) {
                    $query->orderBy('entregada_en', $order);
                })
                ->rawColumns(['numero_orden', 'cantidad'])
                ->make(true);
        }

        $totalEntregadas = OrdenPieza::where('entregada', true)->count();

        $entregadasHoy = OrdenPieza::where('entregada', true)
            ->whereDate('entregada_en', today())
            ->count();

        $entregadasSemana = OrdenPieza::where('entregada', true)
            ->where('entregada_en', '>=', now()->subDays(7))
            ->count();

        return view('entregas.historial', compact(
            'totalEntregadas', 'entregadasHoy', 'entregadasSemana'
        ));
    }

    // ---- Badge helpers (copiados de OrdenController) ----

    protected function badgeEstadoTrabajo(string $estado): string
    {
        $map = [
            'borrador' => ['secondary', 'BORRADOR'],
            'generada' => ['info', 'GENERADA'],
            'en_ejecucion' => ['warning', 'EN EJECUCION'],
            'ejecutada_parcialmente' => ['warning', 'EJEC. PARCIAL'],
            'ejecutada' => ['success', 'EJECUTADA'],
            'anulada' => ['danger', 'ANULADA'],
        ];
        $cfg = $map[$estado] ?? ['secondary', strtoupper($estado)];
        return '<span class="status-badge ' . $cfg[0] . '">' . $cfg[1] . '</span>';
    }

    protected function badgeEstadoEntrega(?string $estado): string
    {
        if (!$estado) return '<span class="text-muted">-</span>';
        $map = [
            'entregada_parcialmente' => ['info', 'ENTREGA PARCIAL'],
            'entregada' => ['success', 'ENTREGADA'],
        ];
        $cfg = $map[$estado] ?? ['secondary', strtoupper($estado)];
        return '<span class="status-badge ' . $cfg[0] . '">' . $cfg[1] . '</span>';
    }
}
