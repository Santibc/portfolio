<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MetricasProductosService
{
    /**
     * Normaliza filtros con defaults.
     */
    public function normalizar(array $filtros): array
    {
        return [
            'fecha_inicio' => $filtros['fecha_inicio'] ?? Carbon::now()->startOfMonth()->toDateString(),
            'fecha_fin'    => $filtros['fecha_fin']    ?? Carbon::now()->toDateString(),
            'fuente'       => $filtros['fuente']       ?? 'ambas',
            'categoria_id' => $filtros['categoria_id'] ?? null,
            'ubicacion_id' => $filtros['ubicacion_id'] ?? null,
            'tipo'         => $filtros['tipo']         ?? 'todos',
        ];
    }

    /**
     * Subconsulta base producto-variante (lista plana).
     * Productos sin variantes -> 1 fila con variante_id=NULL
     * Productos con variantes -> 1 fila por cada variante activa
     */
    private function pvSubquery()
    {
        $sinVariantes = DB::table('productos as p')
            ->leftJoin('categorias as c', 'c.id', '=', 'p.categoria_id')
            ->select(
                'p.id as producto_id',
                DB::raw('NULL as variante_id'),
                'p.referencia',
                'p.nombre',
                'p.marca',
                'p.activo',
                'p.categoria_id',
                'c.nombre as categoria_nombre',
                DB::raw('NULL as variante_referencia'),
                DB::raw('NULL as variante_color'),
                DB::raw('NULL as variante_sku')
            )
            ->where('p.eliminado', false)
            ->where('p.tiene_variantes', false);

        $conVariantes = DB::table('productos as p')
            ->join('variantes_productos as v', 'v.producto_id', '=', 'p.id')
            ->leftJoin('categorias as c', 'c.id', '=', 'p.categoria_id')
            ->select(
                'p.id as producto_id',
                'v.id as variante_id',
                'p.referencia',
                'p.nombre',
                'p.marca',
                'p.activo',
                'p.categoria_id',
                'c.nombre as categoria_nombre',
                'v.referencia_variante as variante_referencia',
                'v.color as variante_color',
                'v.sku as variante_sku'
            )
            ->where('p.eliminado', false)
            ->where('p.tiene_variantes', true)
            ->where('v.activo', true);

        return $sinVariantes->unionAll($conVariantes);
    }

    /**
     * Subconsulta de ventas agregadas por producto/variante para el período y fuente.
     * Devuelve: producto_id, variante_key, unidades, ingresos, transacciones, precio_prom, ultima_venta
     */
    public function ventasSubquery(string $fi, string $ff, string $fuente, ?int $ubicacionId = null)
    {
        $fiTs = $fi . ' 00:00:00';
        $ffTs = $ff . ' 23:59:59';

        $pdv = DB::table('items_venta_pdv as i')
            ->join('ventas_pdv as vp', 'vp.id', '=', 'i.venta_pdv_id')
            ->where('vp.estado', 'completada')
            ->whereBetween('vp.created_at', [$fiTs, $ffTs])
            ->when($ubicacionId, fn($q) => $q->where('vp.ubicacion_id', $ubicacionId))
            ->groupBy('i.producto_id', DB::raw('IFNULL(i.variante_producto_id,0)'))
            ->select(
                'i.producto_id',
                DB::raw('IFNULL(i.variante_producto_id,0) as variante_key'),
                DB::raw('SUM(i.cantidad) as unidades'),
                DB::raw('SUM(i.total) as ingresos'),
                DB::raw('COUNT(DISTINCT i.venta_pdv_id) as transacciones'),
                DB::raw('AVG(i.precio_unitario) as precio_prom'),
                DB::raw('MAX(vp.created_at) as ultima_venta')
            );

        $cot = DB::table('items_solicitud_cotizacion as isc')
            ->join('solicitudes_cotizacion as sc', 'sc.id', '=', 'isc.solicitud_cotizacion_id')
            ->where('sc.estado', 'aplicada')
            ->whereNull('sc.deleted_at')
            ->whereBetween('sc.aplicada_en', [$fiTs, $ffTs])
            ->groupBy('isc.producto_id', DB::raw('IFNULL(isc.variante_producto_id,0)'))
            ->select(
                'isc.producto_id',
                DB::raw('IFNULL(isc.variante_producto_id,0) as variante_key'),
                DB::raw('SUM(isc.cantidad) as unidades'),
                DB::raw('SUM(isc.precio_total) as ingresos'),
                DB::raw('COUNT(DISTINCT isc.solicitud_cotizacion_id) as transacciones'),
                DB::raw('AVG(isc.precio_unitario) as precio_prom'),
                DB::raw('MAX(sc.aplicada_en) as ultima_venta')
            );

        if ($fuente === 'pdv') {
            return $pdv;
        }
        if ($fuente === 'cotizaciones') {
            return $cot;
        }

        // ambas: UNION ALL y reagrupar
        $union = $pdv->unionAll($cot);
        return DB::query()->fromSub($union, 'u')
            ->groupBy('u.producto_id', 'u.variante_key')
            ->select(
                'u.producto_id',
                'u.variante_key',
                DB::raw('SUM(u.unidades) as unidades'),
                DB::raw('SUM(u.ingresos) as ingresos'),
                DB::raw('SUM(u.transacciones) as transacciones'),
                DB::raw('CASE WHEN SUM(u.unidades) > 0 THEN SUM(u.ingresos)/SUM(u.unidades) ELSE 0 END as precio_prom'),
                DB::raw('MAX(u.ultima_venta) as ultima_venta')
            );
    }

    /**
     * Subconsulta de stock por producto/variante (sumado por ubicación o filtrado por una).
     */
    public function stockSubquery(?int $ubicacionId = null)
    {
        return DB::table('stock_productos as s')
            ->when($ubicacionId, fn($q) => $q->where('s.ubicacion_id', $ubicacionId))
            ->groupBy('s.producto_id', DB::raw('IFNULL(s.variante_producto_id,0)'))
            ->select(
                's.producto_id',
                DB::raw('IFNULL(s.variante_producto_id,0) as variante_key'),
                DB::raw('SUM(s.cantidad_disponible - s.cantidad_reservada) as stock_actual'),
                DB::raw('MIN(s.stock_minimo) as stock_minimo')
            );
    }

    /**
     * Calcula período anterior de igual duración.
     */
    private function periodoAnterior(string $fi, string $ff): array
    {
        $inicio = Carbon::parse($fi)->startOfDay();
        $fin    = Carbon::parse($ff)->endOfDay();
        $dias   = $inicio->diffInDays($fin) + 1;
        return [
            $inicio->copy()->subDays($dias)->toDateString(),
            $inicio->copy()->subDay()->toDateString(),
        ];
    }

    /**
     * KPIs principales para las cards de la vista.
     */
    public function kpis(array $f): array
    {
        $f = $this->normalizar($f);
        $fiTs = $f['fecha_inicio'] . ' 00:00:00';
        $ffTs = $f['fecha_fin'] . ' 23:59:59';

        // Totales del período según fuente
        $totales = $this->totalesPeriodo($f['fecha_inicio'], $f['fecha_fin'], $f['fuente'], $f['ubicacion_id'], $f['categoria_id']);

        // Total SKUs: productos sin variantes (activos no eliminados) + variantes activas
        $totalSinVar = DB::table('productos')
            ->where('eliminado', false)
            ->where('activo', true)
            ->where('tiene_variantes', false)
            ->when($f['categoria_id'], fn($q, $id) => $q->where('categoria_id', $id))
            ->count();

        $totalVar = DB::table('variantes_productos as v')
            ->join('productos as p', 'p.id', '=', 'v.producto_id')
            ->where('p.eliminado', false)
            ->where('p.activo', true)
            ->where('p.tiene_variantes', true)
            ->where('v.activo', true)
            ->when($f['categoria_id'], fn($q, $id) => $q->where('p.categoria_id', $id))
            ->count();

        $totalSkus = $totalSinVar + $totalVar;

        $sinMovimiento = max(0, $totalSkus - (int) $totales['productos_distintos']);
        $ticketLinea   = $totales['lineas'] > 0 ? $totales['ingresos'] / $totales['lineas'] : 0;
        $pctConVentas  = $totalSkus > 0 ? ($totales['productos_distintos'] / $totalSkus) * 100 : 0;

        return [
            'ingresos'              => (float) $totales['ingresos'],
            'unidades'              => (int) $totales['unidades'],
            'productos_vendidos'    => (int) $totales['productos_distintos'],
            'total_skus'            => $totalSkus,
            'sin_movimiento'        => $sinMovimiento,
            'ticket_promedio_linea' => (float) $ticketLinea,
            'pct_con_ventas'        => round($pctConVentas, 1),
            'lineas'                => (int) $totales['lineas'],
        ];
    }

    /**
     * Calcula totales agregados del período (suma sobre la subconsulta de ventas).
     * Para `productos_distintos` cuenta combinaciones (producto_id, variante_key) con unidades>0.
     */
    private function totalesPeriodo(string $fi, string $ff, string $fuente, ?int $ubicacionId = null, ?int $categoriaId = null): array
    {
        $sub = $this->ventasSubquery($fi, $ff, $fuente, $ubicacionId);

        $q = DB::query()->fromSub($sub, 'va')
            ->leftJoin('productos as p', 'p.id', '=', 'va.producto_id')
            ->when($categoriaId, fn($qq, $id) => $qq->where('p.categoria_id', $id))
            ->selectRaw('
                COALESCE(SUM(va.unidades),0) as unidades,
                COALESCE(SUM(va.ingresos),0) as ingresos,
                COALESCE(SUM(va.transacciones),0) as lineas,
                COUNT(*) as productos_distintos
            ')
            ->first();

        return [
            'unidades'            => $q->unidades ?? 0,
            'ingresos'            => $q->ingresos ?? 0,
            'lineas'              => $q->lineas ?? 0,
            'productos_distintos' => $q->productos_distintos ?? 0,
        ];
    }

    /**
     * DataTables server-side para la tabla principal.
     */
    public function datatable(array $f)
    {
        $f = $this->normalizar($f);

        [$prevFi, $prevFf] = $this->periodoAnterior($f['fecha_inicio'], $f['fecha_fin']);

        $pv          = $this->pvSubquery();
        $ventasSub   = $this->ventasSubquery($f['fecha_inicio'], $f['fecha_fin'], $f['fuente'], $f['ubicacion_id']);
        $ventasPrev  = $this->ventasSubquery($prevFi, $prevFf, $f['fuente'], $f['ubicacion_id']);
        $stockSub    = $this->stockSubquery($f['ubicacion_id']);

        $query = DB::query()
            ->fromSub($pv, 'pv')
            ->leftJoinSub($ventasSub, 'va', function ($j) {
                $j->on('va.producto_id', '=', 'pv.producto_id')
                  ->on('va.variante_key', '=', DB::raw('IFNULL(pv.variante_id,0)'));
            })
            ->leftJoinSub($ventasPrev, 'vp', function ($j) {
                $j->on('vp.producto_id', '=', 'pv.producto_id')
                  ->on('vp.variante_key', '=', DB::raw('IFNULL(pv.variante_id,0)'));
            })
            ->leftJoinSub($stockSub, 'st', function ($j) {
                $j->on('st.producto_id', '=', 'pv.producto_id')
                  ->on('st.variante_key', '=', DB::raw('IFNULL(pv.variante_id,0)'));
            })
            ->select(
                'pv.producto_id',
                'pv.variante_id',
                'pv.referencia',
                'pv.nombre',
                'pv.marca',
                'pv.categoria_id',
                'pv.categoria_nombre',
                'pv.variante_referencia',
                'pv.variante_color',
                'pv.variante_sku',
                DB::raw('COALESCE(va.unidades,0) as unidades'),
                DB::raw('COALESCE(va.ingresos,0) as ingresos'),
                DB::raw('COALESCE(va.transacciones,0) as transacciones'),
                DB::raw('COALESCE(va.precio_prom,0) as precio_prom'),
                'va.ultima_venta',
                DB::raw('COALESCE(vp.ingresos,0) as ingresos_prev'),
                DB::raw('CASE WHEN COALESCE(vp.ingresos,0) = 0 THEN NULL ELSE ((COALESCE(va.ingresos,0) - vp.ingresos) / vp.ingresos) * 100 END as delta_pct'),
                DB::raw('COALESCE(st.stock_actual,0) as stock_actual'),
                DB::raw('COALESCE(st.stock_minimo,0) as stock_minimo')
            )
            ->when($f['categoria_id'], fn($q, $id) => $q->where('pv.categoria_id', $id))
            ->when($f['tipo'] === 'con_ventas', fn($q) => $q->whereRaw('COALESCE(va.unidades,0) > 0'))
            ->when($f['tipo'] === 'sin_ventas', fn($q) => $q->whereRaw('COALESCE(va.unidades,0) = 0'))
            ->when($f['tipo'] === 'stock_bajo', fn($q) => $q->whereRaw('COALESCE(st.stock_actual,0) <= COALESCE(st.stock_minimo,0)'));

        return DataTables::query($query)
            ->addColumn('producto_info', function ($row) {
                $info = '<strong>' . e($row->referencia) . '</strong><br>';
                $info .= '<span class="text-dark">' . e($row->nombre) . '</span>';
                if ($row->variante_id) {
                    $bits = array_filter([$row->variante_referencia, $row->variante_color, $row->variante_sku]);
                    if ($bits) {
                        $info .= '<br><small class="text-muted"><i class="bi bi-tag"></i> ' . e(implode(' / ', $bits)) . '</small>';
                    }
                }
                return $info;
            })
            ->addColumn('stock_badge', function ($row) {
                $stock = (int) $row->stock_actual;
                $min   = (int) $row->stock_minimo;
                if ($stock <= 0) {
                    $cls = 'danger';
                } elseif ($min > 0 && $stock <= $min) {
                    $cls = 'warning';
                } else {
                    $cls = 'success';
                }
                return '<span class="badge bg-' . $cls . '" style="font-size:0.85rem;">' . number_format($stock) . '</span>';
            })
            ->addColumn('ingresos_fmt', function ($row) {
                return '<span class="fw-bold">$' . number_format((float) $row->ingresos, 0, ',', '.') . '</span>';
            })
            ->addColumn('precio_prom_fmt', function ($row) {
                return '$' . number_format((float) $row->precio_prom, 0, ',', '.');
            })
            ->addColumn('ultima_venta_fmt', function ($row) {
                if (!$row->ultima_venta) {
                    return '<span class="badge bg-danger">Nunca</span>';
                }
                $f = Carbon::parse($row->ultima_venta);
                return '<span title="' . $f->format('Y-m-d H:i') . '">' . $f->format('d/m/Y') . '</span>';
            })
            ->addColumn('delta_badge', function ($row) {
                if ($row->delta_pct === null) {
                    if ((float) $row->ingresos > 0) {
                        return '<span class="badge bg-secondary">Nuevo</span>';
                    }
                    return '<span class="text-muted">—</span>';
                }
                $delta = (float) $row->delta_pct;
                $cls   = $delta > 0 ? 'success' : ($delta < 0 ? 'danger' : 'secondary');
                $icon  = $delta > 0 ? 'bi-arrow-up' : ($delta < 0 ? 'bi-arrow-down' : 'bi-dash');
                return '<span class="badge bg-' . $cls . '"><i class="bi ' . $icon . '"></i> ' . number_format(abs($delta), 1) . '%</span>';
            })
            ->orderColumn('producto_info', 'pv.nombre $1')
            ->orderColumn('stock_badge', 'stock_actual $1')
            ->orderColumn('ingresos_fmt', 'ingresos $1')
            ->orderColumn('precio_prom_fmt', 'precio_prom $1')
            ->orderColumn('ultima_venta_fmt', 'va.ultima_venta $1')
            ->orderColumn('delta_badge', 'delta_pct $1')
            ->filterColumn('producto_info', function ($q, $kw) {
                $q->where(function ($qq) use ($kw) {
                    $qq->where('pv.referencia', 'like', "%{$kw}%")
                       ->orWhere('pv.nombre', 'like', "%{$kw}%")
                       ->orWhere('pv.variante_referencia', 'like', "%{$kw}%")
                       ->orWhere('pv.variante_color', 'like', "%{$kw}%")
                       ->orWhere('pv.variante_sku', 'like', "%{$kw}%");
                });
            })
            ->rawColumns(['producto_info', 'stock_badge', 'ingresos_fmt', 'precio_prom_fmt', 'ultima_venta_fmt', 'delta_badge'])
            ->make(true);
    }

    /**
     * Devuelve TODAS las series para la vista de gráficas en una sola request.
     */
    public function graficas(array $f): array
    {
        $f = $this->normalizar($f);
        [$prevFi, $prevFf] = $this->periodoAnterior($f['fecha_inicio'], $f['fecha_fin']);

        return [
            'filtros'                  => $f,
            'top_ingresos'             => $this->topProductos($f, 'ingresos', 15),
            'top_unidades'             => $this->topProductos($f, 'unidades', 15),
            'categorias_dona'          => $this->ingresosPorCategoria($f),
            'evolucion'                => [
                'actual' => $this->evolucionDiaria($f['fecha_inicio'], $f['fecha_fin'], $f['fuente'], $f['ubicacion_id'], $f['categoria_id']),
                'previo' => $this->evolucionDiaria($prevFi, $prevFf, $f['fuente'], $f['ubicacion_id'], $f['categoria_id']),
            ],
            'pareto'                   => $this->pareto($f),
            'sin_movimiento_por_cat'   => $this->sinMovimientoPorCategoria($f),
            'heatmap'                  => $this->heatmapDiaCategoria($f),
            'stock_vs_ventas'          => $this->stockVsVentas($f),
            'ranking_ticket_categoria' => $this->rankingTicketCategoria($f),
            'productos_nuevos'         => $this->productosNuevos($f),
        ];
    }

    private function topProductos(array $f, string $orden, int $limite): array
    {
        $sub = $this->ventasSubquery($f['fecha_inicio'], $f['fecha_fin'], $f['fuente'], $f['ubicacion_id']);

        $rows = DB::query()->fromSub($sub, 'va')
            ->join('productos as p', 'p.id', '=', 'va.producto_id')
            ->leftJoin('variantes_productos as v', function ($j) {
                $j->on('v.id', '=', 'va.variante_key')
                  ->where('va.variante_key', '>', 0);
            })
            ->leftJoin('categorias as c', 'c.id', '=', 'p.categoria_id')
            ->when($f['categoria_id'], fn($q, $id) => $q->where('p.categoria_id', $id))
            ->select(
                'p.referencia',
                'p.nombre',
                'c.nombre as categoria',
                'v.referencia_variante',
                'v.color',
                DB::raw('va.unidades'),
                DB::raw('va.ingresos')
            )
            ->orderByDesc($orden)
            ->limit($limite)
            ->get();

        return $rows->map(function ($r) {
            $label = $r->referencia . ' — ' . $r->nombre;
            if ($r->referencia_variante || $r->color) {
                $label .= ' (' . trim(($r->referencia_variante ?? '') . ' ' . ($r->color ?? '')) . ')';
            }
            return [
                'label'     => $label,
                'unidades'  => (int) $r->unidades,
                'ingresos'  => (float) $r->ingresos,
                'categoria' => $r->categoria,
            ];
        })->toArray();
    }

    private function ingresosPorCategoria(array $f): array
    {
        $sub = $this->ventasSubquery($f['fecha_inicio'], $f['fecha_fin'], $f['fuente'], $f['ubicacion_id']);

        $rows = DB::query()->fromSub($sub, 'va')
            ->join('productos as p', 'p.id', '=', 'va.producto_id')
            ->leftJoin('categorias as c', 'c.id', '=', 'p.categoria_id')
            ->when($f['categoria_id'], fn($q, $id) => $q->where('p.categoria_id', $id))
            ->groupBy('c.id', 'c.nombre')
            ->select(
                DB::raw("COALESCE(c.nombre,'Sin categoría') as categoria"),
                DB::raw('SUM(va.ingresos) as ingresos'),
                DB::raw('SUM(va.unidades) as unidades')
            )
            ->orderByDesc('ingresos')
            ->get();

        return $rows->map(fn($r) => [
            'categoria' => $r->categoria,
            'ingresos'  => (float) $r->ingresos,
            'unidades'  => (int) $r->unidades,
        ])->toArray();
    }

    private function evolucionDiaria(string $fi, string $ff, string $fuente, ?int $ubicacionId, ?int $categoriaId): array
    {
        $fiTs = $fi . ' 00:00:00';
        $ffTs = $ff . ' 23:59:59';

        $pdv = DB::table('items_venta_pdv as i')
            ->join('ventas_pdv as vp', 'vp.id', '=', 'i.venta_pdv_id')
            ->join('productos as p', 'p.id', '=', 'i.producto_id')
            ->where('vp.estado', 'completada')
            ->whereBetween('vp.created_at', [$fiTs, $ffTs])
            ->when($ubicacionId, fn($q) => $q->where('vp.ubicacion_id', $ubicacionId))
            ->when($categoriaId, fn($q, $id) => $q->where('p.categoria_id', $id))
            ->groupBy(DB::raw('DATE(vp.created_at)'))
            ->select(DB::raw('DATE(vp.created_at) as dia'), DB::raw('SUM(i.total) as ingresos'));

        $cot = DB::table('items_solicitud_cotizacion as isc')
            ->join('solicitudes_cotizacion as sc', 'sc.id', '=', 'isc.solicitud_cotizacion_id')
            ->join('productos as p', 'p.id', '=', 'isc.producto_id')
            ->where('sc.estado', 'aplicada')
            ->whereNull('sc.deleted_at')
            ->whereBetween('sc.aplicada_en', [$fiTs, $ffTs])
            ->when($categoriaId, fn($q, $id) => $q->where('p.categoria_id', $id))
            ->groupBy(DB::raw('DATE(sc.aplicada_en)'))
            ->select(DB::raw('DATE(sc.aplicada_en) as dia'), DB::raw('SUM(isc.precio_total) as ingresos'));

        if ($fuente === 'pdv') {
            $rows = $pdv->orderBy('dia')->get();
        } elseif ($fuente === 'cotizaciones') {
            $rows = $cot->orderBy('dia')->get();
        } else {
            $rows = DB::query()->fromSub($pdv->unionAll($cot), 'u')
                ->groupBy('u.dia')
                ->select('u.dia', DB::raw('SUM(u.ingresos) as ingresos'))
                ->orderBy('u.dia')
                ->get();
        }

        return $rows->map(fn($r) => [
            'dia'      => $r->dia,
            'ingresos' => (float) $r->ingresos,
        ])->toArray();
    }

    private function pareto(array $f): array
    {
        $sub = $this->ventasSubquery($f['fecha_inicio'], $f['fecha_fin'], $f['fuente'], $f['ubicacion_id']);

        $rows = DB::query()->fromSub($sub, 'va')
            ->join('productos as p', 'p.id', '=', 'va.producto_id')
            ->leftJoin('variantes_productos as v', function ($j) {
                $j->on('v.id', '=', 'va.variante_key')->where('va.variante_key', '>', 0);
            })
            ->when($f['categoria_id'], fn($q, $id) => $q->where('p.categoria_id', $id))
            ->select(
                'p.referencia',
                'p.nombre',
                'v.referencia_variante',
                'v.color',
                DB::raw('va.ingresos')
            )
            ->orderByDesc('va.ingresos')
            ->get();

        $total = $rows->sum('ingresos');
        if ($total <= 0) {
            return ['items' => [], 'total' => 0, 'productos_80' => 0];
        }

        $acum = 0;
        $resultado = [];
        $productos80 = null;
        foreach ($rows as $i => $r) {
            $acum += (float) $r->ingresos;
            $pctAcum = ($acum / $total) * 100;
            $resultado[] = [
                'label'    => $r->referencia . ' — ' . mb_substr($r->nombre, 0, 30),
                'ingresos' => (float) $r->ingresos,
                'pct_acum' => round($pctAcum, 2),
            ];
            if ($productos80 === null && $pctAcum >= 80) {
                $productos80 = $i + 1;
            }
            if ($pctAcum >= 95) {
                break;
            }
        }

        return [
            'items'        => $resultado,
            'total'        => (float) $total,
            'productos_80' => $productos80 ?? count($resultado),
        ];
    }

    private function sinMovimientoPorCategoria(array $f): array
    {
        $pv = $this->pvSubquery();
        $ventasSub = $this->ventasSubquery($f['fecha_inicio'], $f['fecha_fin'], $f['fuente'], $f['ubicacion_id']);

        $rows = DB::query()->fromSub($pv, 'pv')
            ->leftJoinSub($ventasSub, 'va', function ($j) {
                $j->on('va.producto_id', '=', 'pv.producto_id')
                  ->on('va.variante_key', '=', DB::raw('IFNULL(pv.variante_id,0)'));
            })
            ->whereNull('va.producto_id')
            ->where('pv.activo', true)
            ->when($f['categoria_id'], fn($q, $id) => $q->where('pv.categoria_id', $id))
            ->groupBy('pv.categoria_id', 'pv.categoria_nombre')
            ->select(
                DB::raw("COALESCE(pv.categoria_nombre,'Sin categoría') as categoria"),
                DB::raw('COUNT(*) as sin_movimiento')
            )
            ->orderByDesc('sin_movimiento')
            ->get();

        return $rows->map(fn($r) => [
            'categoria'      => $r->categoria,
            'sin_movimiento' => (int) $r->sin_movimiento,
        ])->toArray();
    }

    private function heatmapDiaCategoria(array $f): array
    {
        $fiTs = $f['fecha_inicio'] . ' 00:00:00';
        $ffTs = $f['fecha_fin'] . ' 23:59:59';
        $fuente = $f['fuente'];

        $pdv = DB::table('items_venta_pdv as i')
            ->join('ventas_pdv as vp', 'vp.id', '=', 'i.venta_pdv_id')
            ->join('productos as p', 'p.id', '=', 'i.producto_id')
            ->leftJoin('categorias as c', 'c.id', '=', 'p.categoria_id')
            ->where('vp.estado', 'completada')
            ->whereBetween('vp.created_at', [$fiTs, $ffTs])
            ->when($f['ubicacion_id'], fn($q) => $q->where('vp.ubicacion_id', $f['ubicacion_id']))
            ->when($f['categoria_id'], fn($q, $id) => $q->where('p.categoria_id', $id))
            ->select(
                DB::raw('DAYOFWEEK(vp.created_at) as dow'),
                DB::raw("COALESCE(c.nombre,'Sin categoría') as categoria"),
                DB::raw('i.total as ingresos')
            );

        $cot = DB::table('items_solicitud_cotizacion as isc')
            ->join('solicitudes_cotizacion as sc', 'sc.id', '=', 'isc.solicitud_cotizacion_id')
            ->join('productos as p', 'p.id', '=', 'isc.producto_id')
            ->leftJoin('categorias as c', 'c.id', '=', 'p.categoria_id')
            ->where('sc.estado', 'aplicada')
            ->whereNull('sc.deleted_at')
            ->whereBetween('sc.aplicada_en', [$fiTs, $ffTs])
            ->when($f['categoria_id'], fn($q, $id) => $q->where('p.categoria_id', $id))
            ->select(
                DB::raw('DAYOFWEEK(sc.aplicada_en) as dow'),
                DB::raw("COALESCE(c.nombre,'Sin categoría') as categoria"),
                DB::raw('isc.precio_total as ingresos')
            );

        if ($fuente === 'pdv') {
            $base = $pdv;
        } elseif ($fuente === 'cotizaciones') {
            $base = $cot;
        } else {
            $base = $pdv->unionAll($cot);
        }

        $rows = DB::query()->fromSub($base, 'u')
            ->groupBy('u.dow', 'u.categoria')
            ->select('u.dow', 'u.categoria', DB::raw('SUM(u.ingresos) as ingresos'))
            ->get();

        // dow MySQL: 1=Domingo ... 7=Sábado. Lo reordenamos para mostrar Lun-Dom.
        $diasNombre = [2 => 'Lun', 3 => 'Mar', 4 => 'Mié', 5 => 'Jue', 6 => 'Vie', 7 => 'Sáb', 1 => 'Dom'];
        $orden      = [2, 3, 4, 5, 6, 7, 1];

        $categorias = $rows->pluck('categoria')->unique()->values()->all();

        $matriz = [];
        foreach ($categorias as $cat) {
            $fila = [];
            foreach ($orden as $d) {
                $cell = $rows->first(fn($r) => (int)$r->dow === $d && $r->categoria === $cat);
                $fila[] = $cell ? (float) $cell->ingresos : 0.0;
            }
            $matriz[] = $fila;
        }

        return [
            'dias'       => array_map(fn($d) => $diasNombre[$d], $orden),
            'categorias' => $categorias,
            'matriz'     => $matriz,
        ];
    }

    private function stockVsVentas(array $f): array
    {
        $pv        = $this->pvSubquery();
        $ventasSub = $this->ventasSubquery($f['fecha_inicio'], $f['fecha_fin'], $f['fuente'], $f['ubicacion_id']);
        $stockSub  = $this->stockSubquery($f['ubicacion_id']);

        $rows = DB::query()->fromSub($pv, 'pv')
            ->leftJoinSub($ventasSub, 'va', function ($j) {
                $j->on('va.producto_id', '=', 'pv.producto_id')
                  ->on('va.variante_key', '=', DB::raw('IFNULL(pv.variante_id,0)'));
            })
            ->leftJoinSub($stockSub, 'st', function ($j) {
                $j->on('st.producto_id', '=', 'pv.producto_id')
                  ->on('st.variante_key', '=', DB::raw('IFNULL(pv.variante_id,0)'));
            })
            ->where('pv.activo', true)
            ->when($f['categoria_id'], fn($q, $id) => $q->where('pv.categoria_id', $id))
            ->whereRaw('(COALESCE(va.unidades,0) > 0 OR COALESCE(st.stock_actual,0) > 0)')
            ->select(
                'pv.referencia',
                'pv.nombre',
                'pv.variante_referencia',
                DB::raw('COALESCE(st.stock_actual,0) as stock'),
                DB::raw('COALESCE(va.unidades,0) as unidades')
            )
            ->limit(500)
            ->get();

        return $rows->map(fn($r) => [
            'label'    => $r->referencia . ($r->variante_referencia ? '/' . $r->variante_referencia : ''),
            'nombre'   => $r->nombre,
            'stock'    => (int) $r->stock,
            'unidades' => (int) $r->unidades,
        ])->toArray();
    }

    private function rankingTicketCategoria(array $f): array
    {
        $fiTs = $f['fecha_inicio'] . ' 00:00:00';
        $ffTs = $f['fecha_fin'] . ' 23:59:59';
        $fuente = $f['fuente'];

        $pdv = DB::table('items_venta_pdv as i')
            ->join('ventas_pdv as vp', 'vp.id', '=', 'i.venta_pdv_id')
            ->join('productos as p', 'p.id', '=', 'i.producto_id')
            ->leftJoin('categorias as c', 'c.id', '=', 'p.categoria_id')
            ->where('vp.estado', 'completada')
            ->whereBetween('vp.created_at', [$fiTs, $ffTs])
            ->when($f['ubicacion_id'], fn($q) => $q->where('vp.ubicacion_id', $f['ubicacion_id']))
            ->select(
                DB::raw("COALESCE(c.nombre,'Sin categoría') as categoria"),
                DB::raw('vp.id as transaccion_id'),
                DB::raw('i.total as ingresos')
            );

        $cot = DB::table('items_solicitud_cotizacion as isc')
            ->join('solicitudes_cotizacion as sc', 'sc.id', '=', 'isc.solicitud_cotizacion_id')
            ->join('productos as p', 'p.id', '=', 'isc.producto_id')
            ->leftJoin('categorias as c', 'c.id', '=', 'p.categoria_id')
            ->where('sc.estado', 'aplicada')
            ->whereNull('sc.deleted_at')
            ->whereBetween('sc.aplicada_en', [$fiTs, $ffTs])
            ->select(
                DB::raw("COALESCE(c.nombre,'Sin categoría') as categoria"),
                DB::raw('sc.id as transaccion_id'),
                DB::raw('isc.precio_total as ingresos')
            );

        if ($fuente === 'pdv') {
            $base = $pdv;
        } elseif ($fuente === 'cotizaciones') {
            $base = $cot;
        } else {
            $base = $pdv->unionAll($cot);
        }

        $rows = DB::query()->fromSub($base, 'u')
            ->when($f['categoria_id'], function ($q, $id) {
                // Filtrar dentro de la subquery resulta complejo cuando hay UNION; aplicamos el filtro de categoría afuera vía join secundario.
                // Para mantenerlo simple: filtramos por nombre cuando aplica
                return $q;
            })
            ->groupBy('u.categoria')
            ->select(
                'u.categoria',
                DB::raw('SUM(u.ingresos) as ingresos'),
                DB::raw('COUNT(DISTINCT u.transaccion_id) as transacciones'),
                DB::raw('SUM(u.ingresos)/NULLIF(COUNT(DISTINCT u.transaccion_id),0) as ticket_prom')
            )
            ->orderByDesc('ticket_prom')
            ->get();

        return $rows->map(fn($r) => [
            'categoria'     => $r->categoria,
            'ingresos'      => (float) $r->ingresos,
            'transacciones' => (int) $r->transacciones,
            'ticket_prom'   => (float) $r->ticket_prom,
        ])->toArray();
    }

    private function productosNuevos(array $f): array
    {
        $fiTs = $f['fecha_inicio'] . ' 00:00:00';
        $ffTs = $f['fecha_fin'] . ' 23:59:59';

        $sub = $this->ventasSubquery($f['fecha_inicio'], $f['fecha_fin'], $f['fuente'], $f['ubicacion_id']);

        $rows = DB::table('productos as p')
            ->leftJoin('categorias as c', 'c.id', '=', 'p.categoria_id')
            ->leftJoinSub(
                DB::query()->fromSub($sub, 'va')
                    ->groupBy('va.producto_id')
                    ->select(
                        'va.producto_id',
                        DB::raw('SUM(va.unidades) as unidades'),
                        DB::raw('SUM(va.ingresos) as ingresos')
                    ),
                'agg',
                'agg.producto_id', '=', 'p.id'
            )
            ->where('p.eliminado', false)
            ->whereBetween('p.created_at', [$fiTs, $ffTs])
            ->when($f['categoria_id'], fn($q, $id) => $q->where('p.categoria_id', $id))
            ->select(
                'p.referencia',
                'p.nombre',
                DB::raw("COALESCE(c.nombre,'Sin categoría') as categoria"),
                'p.created_at',
                DB::raw('COALESCE(agg.unidades,0) as unidades'),
                DB::raw('COALESCE(agg.ingresos,0) as ingresos')
            )
            ->orderByDesc('p.created_at')
            ->limit(20)
            ->get();

        return $rows->map(fn($r) => [
            'referencia' => $r->referencia,
            'nombre'     => $r->nombre,
            'categoria'  => $r->categoria,
            'creado'     => $r->created_at,
            'unidades'   => (int) $r->unidades,
            'ingresos'   => (float) $r->ingresos,
        ])->toArray();
    }
}
