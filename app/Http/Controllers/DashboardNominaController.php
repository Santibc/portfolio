<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use App\Models\Nomina;
use App\Models\NominaDetalle;
use App\Models\PagoAhorroNomina;
use App\Models\PrestacionSocial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardNominaController extends Controller
{
    public function index(Request $request): View
    {
        $desde = $request->filled('desde')
            ? Carbon::parse((string) $request->input('desde'))->startOfDay()
            : Carbon::now()->subMonths(6)->startOfDay();
        $hasta = $request->filled('hasta')
            ? Carbon::parse((string) $request->input('hasta'))->endOfDay()
            : Carbon::now()->endOfDay();

        $nominas = Nomina::with([
            'detalles' => fn ($q) => $q->withSum('pagos', 'monto'),
            'detalles.pagos.metodoPago',
        ])
            ->whereBetween('fecha_inicio', [$desde, $hasta])
            ->orderByDesc('fecha_inicio')
            ->get();

        $totalDevengado = (int) $nominas->sum->total_devengado;
        $totalDeducido = (int) $nominas->sum->total_deducido;
        $totalNeto = (int) $nominas->sum->total_neto;
        $totalPagado = (int) $nominas->sum->total_pagado;
        $totalPendiente = (int) $nominas->sum->total_pendiente;
        $totalAhorro = (int) $nominas->sum->total_ahorro;

        // Saldo global de ahorros de empleados (todo el histórico).
        $ahorroAcumulado = (int) NominaDetalle::sum('ahorro') - (int) PagoAhorroNomina::sum('monto');

        // Prestaciones cuyo período se solapa con el rango (inicio <= hasta && fin >= desde).
        // Usar solape (en vez de filtrar solo por fecha_fin) evita perder una prima/cesantía
        // cuyo período se extiende más allá del "hasta" seleccionado.
        $prestaciones = PrestacionSocial::where('fecha_inicio', '<=', $hasta)
            ->where('fecha_fin', '>=', $desde)
            ->get();
        $totalPrestaciones = (int) $prestaciones->sum('valor');
        $prestacionesPendientes = $prestaciones->where('estado', \App\Enums\EstadoPrestacion::Pendiente)->count();

        $desglosePorMetodo = $this->desglosePorMetodo($nominas);

        // Datos para gráficas.
        $netoLabels = $nominas->sortBy('fecha_inicio')->pluck('descripcion')->map(
            fn ($d) => str_replace('PERIODO DEL ', '', (string) $d)
        )->values();
        $netoData = $nominas->sortBy('fecha_inicio')->map(fn ($n) => $n->total_neto)->values();

        return view('nomina-dashboard.index', [
            'desde' => $desde->toDateString(),
            'hasta' => $hasta->toDateString(),
            'nominas' => $nominas,
            'totalDevengado' => $totalDevengado,
            'totalDeducido' => $totalDeducido,
            'totalNeto' => $totalNeto,
            'totalPagado' => $totalPagado,
            'totalPendiente' => $totalPendiente,
            'totalAhorro' => $totalAhorro,
            'ahorroAcumulado' => $ahorroAcumulado,
            'totalPrestaciones' => $totalPrestaciones,
            'prestacionesPendientes' => $prestacionesPendientes,
            'desglosePorMetodo' => $desglosePorMetodo,
            'netoLabels' => $netoLabels,
            'netoData' => $netoData,
        ]);
    }

    /**
     * Total pagado por método de pago en el rango.
     *
     * @param  \Illuminate\Support\Collection<int, Nomina>  $nominas
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function desglosePorMetodo($nominas)
    {
        $pagos = $nominas->flatMap(fn (Nomina $n) => $n->detalles)
            ->flatMap(fn (NominaDetalle $d) => $d->pagos);

        return MetodoPago::orderBy('orden')->get()->map(function (MetodoPago $m) use ($pagos) {
            $monto = (int) $pagos->where('metodo_pago_id', $m->id)->sum('monto');

            return [
                'nombre' => $m->nombre,
                'es_efectivo' => (bool) $m->es_efectivo,
                'monto' => $monto,
            ];
        })->filter(fn ($r) => $r['monto'] > 0)->values();
    }
}
