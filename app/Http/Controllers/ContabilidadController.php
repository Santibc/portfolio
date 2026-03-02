<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Pago;
use App\Services\OrdenEstadoService;
use App\Traits\RegistraActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ContabilidadController extends Controller
{
    use RegistraActividad;

    protected OrdenEstadoService $estadoService;

    public function __construct(OrdenEstadoService $estadoService)
    {
        $this->estadoService = $estadoService;
    }

    /**
     * GET /contabilidad/panel - Dashboard financiero.
     */
    public function panel()
    {
        $baseOrden = fn() => Orden::where('estado_pago', 'saldo_pendiente')
            ->whereNotIn('estado_trabajo', ['borrador', 'anulada']);

        $ordenesConSaldo = $baseOrden()->count();
        $abonosPorAprobar = Pago::where('aprobado', false)->count();
        $totalPendiente = $baseOrden()->sum('saldo');

        $recaudadoHoy = Pago::where('aprobado', true)
            ->whereDate('created_at', today())
            ->sum('monto');

        $recaudadoSemana = Pago::where('aprobado', true)
            ->where('created_at', '>=', now()->startOfWeek())
            ->sum('monto');

        // Recaudo por metodo de pago (hoy)
        $porMetodoPago = Pago::where('aprobado', true)
            ->whereDate('created_at', today())
            ->selectRaw('metodo_pago, SUM(monto) as total')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago')
            ->toArray();

        // Ultimos 10 pagos aprobados recientes
        $ultimosPagos = Pago::where('aprobado', true)
            ->with(['orden.cliente', 'registradoPorUsuario'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('contabilidad.panel', compact(
            'ordenesConSaldo',
            'abonosPorAprobar',
            'totalPendiente',
            'recaudadoHoy',
            'recaudadoSemana',
            'porMetodoPago',
            'ultimosPagos'
        ));
    }

    /**
     * GET /contabilidad/ordenes-pendientes - Ordenes con saldo pendiente.
     */
    public function ordenesPendientes(Request $request)
    {
        if ($request->ajax()) {
            $query = Orden::where('estado_pago', 'saldo_pendiente')
                ->whereNotIn('estado_trabajo', ['borrador', 'anulada'])
                ->with(['cliente', 'pagos'])
                ->select('ordenes.*');

            // Filtros
            if ($request->filled('numero_orden')) {
                $query->where('numero_orden', 'like', '%' . $request->input('numero_orden') . '%');
            }
            if ($request->filled('cliente')) {
                $query->whereHas('cliente', function ($q) use ($request) {
                    $q->where('nombre', 'like', '%' . $request->input('cliente') . '%');
                });
            }
            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->input('fecha_desde'));
            }
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->input('fecha_hasta'));
            }

            return DataTables::of($query)
                ->addColumn('cliente_nombre', function ($o) {
                    return $o->cliente->nombre ?? '-';
                })
                ->addColumn('total_formatted', function ($o) {
                    return '$' . number_format($o->total, 0, ',', '.');
                })
                ->addColumn('pagado_formatted', function ($o) {
                    return '<span class="text-success">$' . number_format($o->total_pagado, 0, ',', '.') . '</span>';
                })
                ->addColumn('saldo_formatted', function ($o) {
                    $class = $o->saldo > 0 ? 'text-danger fw-bold' : 'text-success';
                    return '<span class="' . $class . '" style="font-size:1rem">$' . number_format($o->saldo, 0, ',', '.') . '</span>';
                })
                ->addColumn('porcentaje_pagado', function ($o) {
                    $pct = $o->total > 0 ? round(($o->total_pagado / $o->total) * 100) : 0;
                    return '<div class="progress" style="height:6px;min-width:60px">'
                        . '<div class="progress-bar bg-success" style="width:' . $pct . '%"></div>'
                        . '</div>'
                        . '<small class="text-muted">' . $pct . '%</small>';
                })
                ->addColumn('estado_trabajo_badge', function ($o) {
                    return $this->badgeEstadoTrabajo($o->estado_trabajo);
                })
                ->addColumn('pagos_pendientes', function ($o) {
                    $count = $o->pagos->where('aprobado', false)->count();
                    if ($count > 0) {
                        return '<span class="badge bg-warning text-dark">' . $count . ' pend.</span>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('acciones', function ($o) {
                    $verUrl = route('contabilidad.ordenes.show', $o);
                    $html = '<div class="action-buttons justify-content-end">';
                    $html .= '<a href="' . $verUrl . '" class="action-btn view" title="Ver Orden" data-tooltip="Ver"><i class="bi bi-eye"></i></a>';
                    $html .= '<button type="button" class="action-btn edit btn-agregar-pago" '
                        . 'data-orden-id="' . $o->id . '" '
                        . 'data-orden-numero="' . ($o->numero_orden ?? 'ID:' . $o->id) . '" '
                        . 'data-orden-cliente="' . ($o->cliente->nombre ?? '-') . '" '
                        . 'data-orden-saldo="' . number_format($o->saldo, 0, ',', '.') . '" '
                        . 'title="Agregar Pago" data-tooltip="Agregar Pago"><i class="bi bi-plus-circle"></i></button>';
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('numero_orden', function ($o) {
                    $url = route('contabilidad.ordenes.show', $o);
                    return '<a href="' . $url . '" class="fw-semibold text-decoration-none">' . ($o->numero_orden ?? '-') . '</a>';
                })
                ->rawColumns(['numero_orden', 'pagado_formatted', 'saldo_formatted', 'porcentaje_pagado', 'estado_trabajo_badge', 'pagos_pendientes', 'acciones'])
                ->make(true);
        }

        // Stats
        $baseQuery = fn() => Orden::where('estado_pago', 'saldo_pendiente')
            ->whereNotIn('estado_trabajo', ['borrador', 'anulada']);

        $totalOrdenes = $baseQuery()->count();
        $totalPendiente = $baseQuery()->sum('saldo');
        $abonosSinAprobar = Pago::where('aprobado', false)->count();
        $recaudadoHoy = Pago::where('aprobado', true)
            ->whereDate('created_at', today())
            ->sum('monto');

        return view('contabilidad.ordenes-pendientes', compact(
            'totalOrdenes', 'totalPendiente', 'abonosSinAprobar', 'recaudadoHoy'
        ));
    }

    /**
     * GET /contabilidad/pagos-pendientes - Pagos sin aprobar.
     */
    public function pagosPendientes(Request $request)
    {
        if ($request->ajax()) {
            $query = Pago::where('aprobado', false)
                ->with(['orden.cliente', 'registradoPorUsuario'])
                ->select('pagos.*');

            return DataTables::of($query)
                ->addColumn('fecha_formatted', function ($p) {
                    return $p->created_at->format('d/m/Y H:i');
                })
                ->addColumn('numero_orden', function ($p) {
                    $url = route('contabilidad.ordenes.show', $p->orden_id);
                    return '<a href="' . $url . '" class="fw-semibold text-decoration-none">' . ($p->orden->numero_orden ?? '-') . '</a>';
                })
                ->addColumn('cliente_nombre', function ($p) {
                    return $p->orden->cliente->nombre ?? '-';
                })
                ->addColumn('monto_formatted', function ($p) {
                    return '<span class="fw-bold" style="font-size:1rem">$' . number_format($p->monto, 0, ',', '.') . '</span>';
                })
                ->addColumn('metodo_badge', function ($p) {
                    return $this->badgeMetodoPago($p->metodo_pago);
                })
                ->addColumn('registrado_por_nombre', function ($p) {
                    return $p->registradoPorUsuario->name ?? '-';
                })
                ->addColumn('acciones', function ($p) {
                    $html = '<div class="action-buttons justify-content-end">';
                    $html .= '<button type="button" class="action-btn edit btn-aprobar-pago" '
                        . 'data-pago-id="' . $p->id . '" '
                        . 'data-pago-monto="' . number_format($p->monto, 0, ',', '.') . '" '
                        . 'data-pago-metodo="' . ucfirst($p->metodo_pago) . '" '
                        . 'data-orden-numero="' . ($p->orden->numero_orden ?? '-') . '" '
                        . 'title="Aprobar" data-tooltip="Aprobar" style="width:36px;height:36px">'
                        . '<i class="bi bi-check-lg"></i></button>';
                    $html .= '<button type="button" class="action-btn delete btn-rechazar-pago" '
                        . 'data-pago-id="' . $p->id . '" '
                        . 'data-pago-monto="' . number_format($p->monto, 0, ',', '.') . '" '
                        . 'data-orden-numero="' . ($p->orden->numero_orden ?? '-') . '" '
                        . 'title="Rechazar" data-tooltip="Rechazar" style="width:36px;height:36px">'
                        . '<i class="bi bi-x-lg"></i></button>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('checkbox', function ($p) {
                    return '<input type="checkbox" class="form-check-input pago-checkbox" value="' . $p->id . '" data-monto="' . $p->monto . '" style="width:20px;height:20px;cursor:pointer">';
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
                ->orderColumn('fecha_formatted', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['checkbox', 'numero_orden', 'monto_formatted', 'metodo_badge', 'acciones'])
                ->make(true);
        }

        // Stats
        $porAprobar = Pago::where('aprobado', false)->count();
        $montoPendiente = Pago::where('aprobado', false)->sum('monto');
        $aprobadosHoy = Pago::where('aprobado', true)
            ->whereDate('updated_at', today())
            ->count();

        return view('contabilidad.pagos-pendientes', compact(
            'porAprobar', 'montoPendiente', 'aprobadosHoy'
        ));
    }

    /**
     * POST /contabilidad/pagos/{pago}/aprobar - Aprobar pago individual.
     */
    public function aprobarPago(Pago $pago)
    {
        if ($pago->aprobado) {
            return response()->json(['success' => false, 'message' => 'Este pago ya esta aprobado.'], 422);
        }

        $pago->update([
            'aprobado' => true,
            'aprobado_por' => auth()->id(),
        ]);

        $this->estadoService->recalcularTodo($pago->orden);

        $this->registrarActividad(
            'pago.aprobado',
            'Pago de $' . number_format($pago->monto, 0, ',', '.') . ' aprobado (Orden ' . ($pago->orden->numero_orden ?? 'ID:' . $pago->orden_id) . ')',
            $pago->orden_id,
            ['pago_id' => $pago->id, 'monto' => $pago->monto, 'metodo_pago' => $pago->metodo_pago]
        );

        $ordenFresh = $pago->orden->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Pago aprobado exitosamente.',
            'orden' => [
                'saldo' => '$' . number_format($ordenFresh->saldo, 0, ',', '.'),
                'total_pagado' => '$' . number_format($ordenFresh->total_pagado, 0, ',', '.'),
                'estado_pago' => $ordenFresh->estado_pago,
            ],
        ]);
    }

    /**
     * POST /contabilidad/pagos/aprobar-masivo - Aprobar multiples pagos.
     */
    public function aprobarPagosMasivo(Request $request)
    {
        $request->validate([
            'pago_ids' => 'required|array|min:1',
            'pago_ids.*' => 'required|integer|exists:pagos,id',
        ]);

        $pagos = Pago::where('aprobado', false)
            ->whereIn('id', $request->input('pago_ids'))
            ->get();

        if ($pagos->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No hay pagos pendientes para aprobar.'], 422);
        }

        $aprobados = 0;
        $montoTotal = 0;
        $ordenesAfectadas = collect();

        DB::beginTransaction();
        try {
            foreach ($pagos as $pago) {
                $pago->update([
                    'aprobado' => true,
                    'aprobado_por' => auth()->id(),
                ]);

                $aprobados++;
                $montoTotal += $pago->monto;
                $ordenesAfectadas->push($pago->orden_id);

                $this->registrarActividad(
                    'pago.aprobado',
                    'Pago de $' . number_format($pago->monto, 0, ',', '.') . ' aprobado (Orden ' . ($pago->orden->numero_orden ?? 'ID:' . $pago->orden_id) . ')',
                    $pago->orden_id,
                    ['pago_id' => $pago->id, 'monto' => $pago->monto, 'metodo_pago' => $pago->metodo_pago, 'masivo' => true]
                );
            }

            // Recalcular cada orden afectada
            $ordenesAfectadas->unique()->each(function ($ordenId) {
                $orden = Orden::find($ordenId);
                if ($orden) {
                    $this->estadoService->recalcularTodo($orden);
                }
            });

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $aprobados . ' pago(s) aprobado(s) por un total de $' . number_format($montoTotal, 0, ',', '.'),
                'aprobados' => $aprobados,
                'monto_total' => '$' . number_format($montoTotal, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar pagos: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /contabilidad/ordenes/{orden}/pagos - Agregar pago (auto-aprobado).
     */
    public function agregarPago(Request $request, Orden $orden)
    {
        if (in_array($orden->estado_trabajo, ['anulada', 'borrador'])) {
            return response()->json(['success' => false, 'message' => 'No se puede agregar pago a esta orden.'], 422);
        }

        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,nequi,transferencia,tarjeta,otro',
            'referencia_pago' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        $pago = Pago::create([
            'orden_id' => $orden->id,
            'monto' => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'referencia_pago' => $request->referencia_pago,
            'registrado_por' => $user->id,
            'aprobado' => true,
            'aprobado_por' => $user->id,
        ]);

        $this->estadoService->recalcularTodo($orden);

        $this->registrarActividad(
            'pago.registrado',
            'Pago de $' . number_format($request->monto, 0, ',', '.') . ' registrado y aprobado en orden ' . ($orden->numero_orden ?? 'ID:' . $orden->id),
            $orden->id,
            ['monto' => $request->monto, 'metodo_pago' => $request->metodo_pago, 'aprobado' => true]
        );

        $ordenFresh = $orden->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Pago registrado y aprobado.',
            'pago' => [
                'id' => $pago->id,
                'monto' => '$' . number_format($pago->monto, 0, ',', '.'),
                'metodo_pago' => $pago->metodo_pago,
                'fecha' => $pago->created_at->format('d/m/Y H:i'),
            ],
            'nuevo_saldo' => '$' . number_format($ordenFresh->saldo, 0, ',', '.'),
            'nuevo_total_pagado' => '$' . number_format($ordenFresh->total_pagado, 0, ',', '.'),
            'estado_pago' => $ordenFresh->estado_pago,
        ]);
    }

    /**
     * DELETE /contabilidad/pagos/{pago}/rechazar - Rechazar pago pendiente.
     */
    public function rechazarPago(Pago $pago)
    {
        if ($pago->aprobado) {
            return response()->json(['success' => false, 'message' => 'No se puede rechazar un pago ya aprobado.'], 422);
        }

        $ordenId = $pago->orden_id;
        $monto = $pago->monto;
        $metodo = $pago->metodo_pago;
        $ordenNumero = $pago->orden->numero_orden ?? 'ID:' . $ordenId;

        $pago->delete();

        $orden = Orden::find($ordenId);
        if ($orden) {
            $this->estadoService->recalcularTodo($orden);
        }

        $this->registrarActividad(
            'pago.rechazado',
            'Pago de $' . number_format($monto, 0, ',', '.') . ' rechazado (Orden ' . $ordenNumero . ')',
            $ordenId,
            ['monto' => $monto, 'metodo_pago' => $metodo]
        );

        return response()->json([
            'success' => true,
            'message' => 'Pago rechazado y eliminado.',
        ]);
    }

    /**
     * GET /contabilidad/historial-financiero - Todas las ordenes con resumen financiero.
     */
    public function historialFinanciero(Request $request)
    {
        if ($request->ajax()) {
            $query = Orden::whereNotIn('estado_trabajo', ['borrador', 'anulada'])
                ->with(['cliente', 'pagos'])
                ->select('ordenes.*');

            // Filtros
            if ($request->filled('numero_orden')) {
                $query->where('numero_orden', 'like', '%' . $request->input('numero_orden') . '%');
            }
            if ($request->filled('cliente')) {
                $query->whereHas('cliente', function ($q) use ($request) {
                    $q->where('nombre', 'like', '%' . $request->input('cliente') . '%');
                });
            }
            if ($request->filled('estado_pago') && $request->input('estado_pago') !== 'todos') {
                $filtro = $request->input('estado_pago');
                if ($filtro === 'sin_pagos') {
                    $query->where('total_pagado', 0);
                } elseif ($filtro === 'pagada') {
                    $query->where('total_pagado', '>', 0)->where('saldo', '<=', 0);
                } elseif ($filtro === 'saldo_pendiente') {
                    $query->where('saldo', '>', 0)->where('total_pagado', '>', 0);
                }
            }
            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->input('fecha_desde'));
            }
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->input('fecha_hasta'));
            }

            return DataTables::of($query)
                ->addColumn('cliente_nombre', fn($o) => $o->cliente->nombre ?? '-')
                ->addColumn('fecha_creacion', fn($o) => $o->created_at->format('d/m/Y'))
                ->addColumn('total_formatted', fn($o) => '$' . number_format($o->total, 0, ',', '.'))
                ->addColumn('pagado_formatted', function ($o) {
                    return '<span class="text-success">$' . number_format($o->total_pagado, 0, ',', '.') . '</span>';
                })
                ->addColumn('saldo_formatted', function ($o) {
                    if ($o->saldo > 0) {
                        return '<span class="text-danger fw-bold">$' . number_format($o->saldo, 0, ',', '.') . '</span>';
                    }
                    return '<span class="text-success">$0</span>';
                })
                ->addColumn('porcentaje_pagado', function ($o) {
                    $pct = $o->total > 0 ? round(($o->total_pagado / $o->total) * 100) : 0;
                    $color = $pct >= 100 ? 'bg-success' : ($pct > 0 ? 'bg-warning' : 'bg-secondary');
                    return '<div class="progress" style="height:6px;min-width:60px">'
                        . '<div class="progress-bar ' . $color . '" style="width:' . $pct . '%"></div>'
                        . '</div>'
                        . '<small class="text-muted">' . $pct . '%</small>';
                })
                ->addColumn('estado_pago_badge', function ($o) {
                    if ($o->saldo <= 0 && $o->total_pagado > 0) {
                        return '<span class="status-badge success">PAGADA</span>';
                    }
                    if ($o->total_pagado > 0) {
                        return '<span class="status-badge danger">SALDO PEND.</span>';
                    }
                    return '<span class="status-badge secondary">SIN PAGOS</span>';
                })
                ->addColumn('num_pagos', function ($o) {
                    $count = $o->pagos->count();
                    if ($count > 0) {
                        return '<span class="badge bg-primary bg-opacity-10 text-primary border">' . $count . '</span>';
                    }
                    return '<span class="text-muted">0</span>';
                })
                ->addColumn('acciones', function ($o) {
                    $verUrl = route('contabilidad.ordenes.show', $o);
                    $html = '<div class="action-buttons justify-content-end">';
                    $html .= '<a href="' . $verUrl . '" class="action-btn view" title="Ver Orden" data-tooltip="Ver"><i class="bi bi-eye"></i></a>';
                    $html .= '<button type="button" class="action-btn edit btn-ver-pagos" '
                        . 'data-orden-id="' . $o->id . '" '
                        . 'data-orden-numero="' . ($o->numero_orden ?? 'ID:' . $o->id) . '" '
                        . 'title="Ver Pagos" data-tooltip="Ver Pagos"><i class="bi bi-receipt"></i></button>';
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('numero_orden', function ($o) {
                    $url = route('contabilidad.ordenes.show', $o);
                    return '<a href="' . $url . '" class="fw-semibold text-decoration-none">' . ($o->numero_orden ?? '-') . '</a>';
                })
                ->rawColumns(['numero_orden', 'pagado_formatted', 'saldo_formatted', 'porcentaje_pagado', 'estado_pago_badge', 'num_pagos', 'acciones'])
                ->make(true);
        }

        // Stats
        $baseQuery = fn() => Orden::whereNotIn('estado_trabajo', ['borrador', 'anulada']);

        $totalOrdenes = $baseQuery()->count();
        $ordenesPagadas = $baseQuery()->where('total_pagado', '>', 0)->where('saldo', '<=', 0)->count();
        $totalRecaudado = Pago::where('aprobado', true)->sum('monto');
        $totalPorCobrar = $baseQuery()->sum('saldo');

        return view('contabilidad.historial-financiero', compact(
            'totalOrdenes', 'ordenesPagadas', 'totalRecaudado', 'totalPorCobrar'
        ));
    }

    /**
     * GET /contabilidad/ordenes/{orden}/pagos - Pagos de una orden (JSON para modal).
     */
    public function pagosOrden(Orden $orden)
    {
        $pagos = $orden->pagos()
            ->with(['registradoPorUsuario', 'aprobadoPorUsuario'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'fecha' => $p->created_at->format('d/m/Y H:i'),
                    'monto' => '$' . number_format($p->monto, 0, ',', '.'),
                    'monto_raw' => $p->monto,
                    'metodo_pago' => ucfirst($p->metodo_pago),
                    'metodo_badge' => $this->badgeMetodoPago($p->metodo_pago),
                    'referencia_pago' => $p->referencia_pago ?: '-',
                    'registrado_por' => $p->registradoPorUsuario->name ?? '-',
                    'aprobado' => $p->aprobado,
                    'aprobado_por' => $p->aprobadoPorUsuario->name ?? null,
                ];
            });

        return response()->json([
            'success' => true,
            'orden' => [
                'numero' => $orden->numero_orden ?? 'ID:' . $orden->id,
                'cliente' => $orden->cliente->nombre ?? '-',
                'total' => '$' . number_format($orden->total, 0, ',', '.'),
                'total_pagado' => '$' . number_format($orden->total_pagado, 0, ',', '.'),
                'saldo' => '$' . number_format($orden->saldo, 0, ',', '.'),
                'estado_pago' => $orden->estado_pago,
            ],
            'pagos' => $pagos,
        ]);
    }

    // ---- Badge helpers ----

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

    protected function badgeEstadoPago(?string $estado): string
    {
        if (!$estado) return '<span class="text-muted">-</span>';
        $map = [
            'saldo_pendiente' => ['danger', 'SALDO PEND.'],
            'pagado' => ['success', 'PAGADO'],
        ];
        $cfg = $map[$estado] ?? ['secondary', strtoupper($estado)];
        return '<span class="status-badge ' . $cfg[0] . '">' . $cfg[1] . '</span>';
    }

    protected function badgeMetodoPago(string $metodo): string
    {
        $map = [
            'efectivo' => ['success', 'bi-cash'],
            'nequi' => ['purple', 'bi-phone'],
            'transferencia' => ['info', 'bi-bank'],
            'tarjeta' => ['warning', 'bi-credit-card'],
            'otro' => ['secondary', 'bi-three-dots'],
        ];
        $cfg = $map[$metodo] ?? ['secondary', 'bi-three-dots'];
        $bgClass = $cfg[0] === 'purple' ? 'bg-purple' : 'bg-' . $cfg[0];
        return '<span class="badge ' . $bgClass . ' bg-opacity-10 text-dark border"><i class="bi ' . $cfg[1] . ' me-1"></i>' . ucfirst($metodo) . '</span>';
    }
}
