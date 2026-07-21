<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Meta;
use App\Models\SolicitudCotizacion;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $anio = (int) now()->year;
        $mes = (int) now()->month;
        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();

        if ($user->hasRole('admin')) {
            $almacenId = $request->filled('almacen_id') ? (int) $request->input('almacen_id') : null;
            return view('dashboard', array_merge(
                ['rol' => 'admin', 'anio' => $anio, 'mes' => $mes],
                $this->datosAdmin($anio, $mes, $inicioMes, $finMes, $almacenId)
            ));
        }

        return view('dashboard', array_merge(
            ['rol' => 'vendedor', 'anio' => $anio, 'mes' => $mes],
            $this->datosVendedor($user, $anio, $mes, $inicioMes, $finMes)
        ));
    }

    protected function datosVendedor(User $user, int $anio, int $mes, $inicioMes, $finMes): array
    {
        $ventasMes = (float) Venta::delVendedor($user->id)
            ->delMes($anio, $mes)
            ->sum('monto');

        $cotizadoMes = (float) SolicitudCotizacion::whereBetween('created_at', [$inicioMes, $finMes])
            ->where('estado', '!=', 'rechazada')
            ->whereHas('cliente', fn($q) => $q->where('vendedor_id', $user->id))
            ->sum('monto_total');

        $meta = Meta::where('user_id', $user->id)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->value('monto');
        $metaMes = (float) ($meta ?? 0);

        $pct = $metaMes > 0 ? min(999, ($ventasMes / $metaMes) * 100) : 0;

        $almacen = $user->almacen()->first();

        $metaSede = 0.0;
        $ventasSedeMes = 0.0;
        $ventasSinVendedor = 0.0;
        $companeros = collect();

        if ($almacen) {
            $vendedoresIds = $almacen->vendedores()->pluck('users.id');

            $metaSede = (float) Meta::whereIn('user_id', $vendedoresIds)
                ->where('anio', $anio)
                ->where('mes', $mes)
                ->sum('monto');

            $ventasSedeMes = (float) Venta::where('almacen_id', $almacen->id)
                ->delMes($anio, $mes)
                ->sum('monto');

            $ventasSinVendedor = (float) Venta::where('almacen_id', $almacen->id)
                ->delMes($anio, $mes)
                ->whereNull('user_id')
                ->sum('monto');

            $companeros = $almacen->vendedores()
                ->orderBy('name')
                ->get(['users.id', 'users.name'])
                ->map(function ($v) use ($anio, $mes, $user) {
                    $vendido = (float) Venta::delVendedor($v->id)->delMes($anio, $mes)->sum('monto');
                    $meta = (float) Meta::where('user_id', $v->id)
                        ->where('anio', $anio)
                        ->where('mes', $mes)
                        ->value('monto');
                    $faltante = max(0, $meta - $vendido);
                    $pct = $meta > 0 ? min(999, ($vendido / $meta) * 100) : 0;
                    return (object) [
                        'id' => $v->id,
                        'nombre' => $v->name,
                        'es_actual' => $v->id === $user->id,
                        'meta' => $meta,
                        'vendido' => $vendido,
                        'faltante' => $faltante,
                        'pct' => round($pct, 1),
                    ];
                });
        }

        $faltanteSede = max(0, $metaSede - $ventasSedeMes);
        $pctSede = $metaSede > 0 ? min(999, ($ventasSedeMes / $metaSede) * 100) : 0;

        return [
            'ventas_mes' => $ventasMes,
            'cotizado_mes' => $cotizadoMes,
            'meta_mes' => $metaMes,
            'pct_cumplimiento' => round($pct, 1),
            'almacen' => $almacen,
            'meta_sede' => $metaSede,
            'ventas_sede_mes' => $ventasSedeMes,
            'faltante_sede' => $faltanteSede,
            'pct_sede' => round($pctSede, 1),
            'ventas_sin_vendedor' => $ventasSinVendedor,
            'companeros' => $companeros,
        ];
    }

    protected function datosAdmin(int $anio, int $mes, $inicioMes, $finMes, ?int $almacenId = null): array
    {
        $almacenesActivos = Almacen::activos()->orderBy('nombre')->get(['id', 'codigo', 'nombre']);
        $almacenSeleccionado = $almacenId ? Almacen::find($almacenId) : null;

        $vendedoresBase = User::role('vendedor')
            ->when($almacenId, fn($q) => $q->where('almacen_id', $almacenId))
            ->orderBy('name')
            ->get(['id', 'name', 'almacen_id']);

        $vendedoresIds = $vendedoresBase->pluck('id');

        // ============ Resumen de cotizaciones ============
        $cotQuery = SolicitudCotizacion::whereBetween('created_at', [$inicioMes, $finMes]);
        if ($almacenId) {
            $cotQuery->whereHas('cliente', fn($q) => $q->whereIn('vendedor_id', $vendedoresIds));
        }
        $cotizacionesMes = (clone $cotQuery)
            ->selectRaw('estado, COUNT(*) as total, COALESCE(SUM(monto_total), 0) as monto')
            ->groupBy('estado')
            ->get()
            ->keyBy('estado');

        $cotResumen = [
            'total' => (int) $cotizacionesMes->sum('total'),
            'pendientes' => (int) ($cotizacionesMes['pendiente']->total ?? 0),
            'aplicadas' => (int) ($cotizacionesMes['aplicada']->total ?? 0),
            'rechazadas' => (int) ($cotizacionesMes['rechazada']->total ?? 0),
            'monto_total' => (float) $cotizacionesMes->sum('monto'),
        ];

        // ============ Cuadro de seguimiento ============
        $metaQuery = Meta::where('anio', $anio)->where('mes', $mes);
        if ($almacenId) {
            $metaQuery->whereIn('user_id', $vendedoresIds);
        }
        $metaTotal = (float) $metaQuery->sum('monto');

        $ventasQuery = Venta::delMes($anio, $mes);
        if ($almacenId) {
            $ventasQuery->where('almacen_id', $almacenId);
        }
        $ventasTotal = (float) $ventasQuery->sum('monto');

        $ventasSinVendedor = 0.0;
        if ($almacenId) {
            $ventasSinVendedor = (float) Venta::where('almacen_id', $almacenId)
                ->delMes($anio, $mes)
                ->whereNull('user_id')
                ->sum('monto');
        }

        $faltanteTotal = max(0, $metaTotal - $ventasTotal);
        $pctTotal = $metaTotal > 0 ? min(999, ($ventasTotal / $metaTotal) * 100) : 0;

        // ============ Cumplimiento por vendedor ============
        $cumplimiento = $vendedoresBase->map(function ($v) use ($anio, $mes, $inicioMes, $finMes) {
            $ventas = (float) Venta::delVendedor($v->id)->delMes($anio, $mes)->sum('monto');
            $meta = (float) Meta::where('user_id', $v->id)
                ->where('anio', $anio)
                ->where('mes', $mes)
                ->value('monto');
            $faltante = max(0, $meta - $ventas);
            $pct = $meta > 0 ? min(999, ($ventas / $meta) * 100) : 0;

            $cotizaciones = SolicitudCotizacion::whereBetween('created_at', [$inicioMes, $finMes])
                ->whereHas('cliente', fn($q) => $q->where('vendedor_id', $v->id))
                ->selectRaw('estado, COUNT(*) as total, COALESCE(SUM(monto_total), 0) as monto')
                ->groupBy('estado')
                ->get()
                ->keyBy('estado');

            return (object) [
                'id' => $v->id,
                'nombre' => $v->name,
                'ventas' => $ventas,
                'meta' => $meta,
                'faltante' => $faltante,
                'pct' => round($pct, 1),
                'cot_pendientes' => (int) ($cotizaciones['pendiente']->total ?? 0),
                'cot_aplicadas' => (int) ($cotizaciones['aplicada']->total ?? 0),
                'cot_total' => (int) $cotizaciones->sum('total'),
                'cot_monto' => (float) $cotizaciones->where('estado', '!=', 'rechazada')->sum('monto'),
            ];
        });

        // ============ Ventas por almacén ============
        $porAlmacen = $almacenesActivos
            ->when($almacenId, fn($c) => $c->where('id', $almacenId))
            ->map(function ($a) use ($anio, $mes) {
                $ventas = (float) Venta::where('almacen_id', $a->id)
                    ->delMes($anio, $mes)
                    ->sum('monto');
                return (object) [
                    'codigo' => $a->codigo,
                    'nombre' => $a->nombre,
                    'ventas' => $ventas,
                    'vendedores' => $a->vendedores()->count(),
                ];
            })
            ->values();

        $sinAlmacen = $almacenId ? 0.0 : (float) Venta::whereNull('almacen_id')->delMes($anio, $mes)->sum('monto');

        return [
            'almacenes_activos' => $almacenesActivos,
            'almacen_seleccionado' => $almacenSeleccionado,
            'cot_resumen' => $cotResumen,
            'meta_total' => $metaTotal,
            'ventas_total' => $ventasTotal,
            'faltante_total' => $faltanteTotal,
            'pct_total' => round($pctTotal, 1),
            'ventas_sin_vendedor' => $ventasSinVendedor,
            'cumplimiento' => $cumplimiento,
            'por_almacen' => $porAlmacen,
            'sin_almacen' => $sinAlmacen,
            'total_ventas_mes' => $ventasTotal,
        ];
    }
}
