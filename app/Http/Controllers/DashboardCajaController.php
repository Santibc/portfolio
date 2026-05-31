<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use App\Models\TurnoCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardCajaController extends Controller
{
    public function index(Request $request): View
    {
        $desde = $request->filled('desde')
            ? Carbon::parse((string) $request->input('desde'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $hasta = $request->filled('hasta')
            ? Carbon::parse((string) $request->input('hasta'))->endOfDay()
            : Carbon::now()->endOfDay();

        $turnos = TurnoCaja::with(['aperturadoPor', 'cerradoPor', 'ventas.pagos.metodo', 'gastos'])
            ->whereBetween('abierto_en', [$desde, $hasta])
            ->orderByDesc('abierto_en')
            ->get();

        $totalVentas    = (int) $turnos->sum->total_ventas;
        $totalEfectivo  = (int) $turnos->sum->total_efectivo;
        $totalNoEfvo    = (int) $turnos->sum->total_no_efectivo;
        $totalGastos    = (int) $turnos->sum->total_gastos;
        $totalAhorros   = (int) $turnos->sum->total_ahorros;
        $neto           = $totalVentas - $totalGastos - $totalAhorros;
        $turnosAbiertos = $turnos->where('cerrado_en', null)->count();

        return view('caja-dashboard.index', [
            'turnos'         => $turnos,
            'desde'          => $desde->toDateString(),
            'hasta'          => $hasta->toDateString(),
            'totalVentas'    => $totalVentas,
            'totalEfectivo'  => $totalEfectivo,
            'totalNoEfvo'    => $totalNoEfvo,
            'totalGastos'    => $totalGastos,
            'totalAhorros'   => $totalAhorros,
            'neto'           => $neto,
            'turnosAbiertos' => $turnosAbiertos,
        ]);
    }

    public function show(TurnoCaja $turno): View
    {
        $turno->load([
            'aperturadoPor',
            'cerradoPor',
            'ventas' => fn ($q) => $q->orderByDesc('created_at'),
            'ventas.items',
            'ventas.pagos.metodo',
            'ventas.user',
            'gastos' => fn ($q) => $q->orderByDesc('created_at'),
            'gastos.trabajadorTurno',
            'gastos.metodoPago',
            'gastos.user',
        ]);

        $desglosePorMetodo = MetodoPago::orderBy('orden')->get()->map(function (MetodoPago $m) use ($turno) {
            $ventas = (int) $turno->ventas->sum(function ($venta) use ($m) {
                $montoMetodo = (int) $venta->pagos->where('metodo_pago_id', $m->id)->sum('monto');

                // El cambio siempre sale del efectivo: se descuenta del monto en
                // efectivo para mostrar el neto que realmente quedó en caja.
                if ($m->es_efectivo && $montoMetodo > 0 && (int) $venta->cambio > 0) {
                    $efectivoVenta = (int) $venta->pagos
                        ->filter(fn ($p) => optional($p->metodo)->es_efectivo)
                        ->sum('monto');
                    $montoMetodo -= $efectivoVenta > 0
                        ? (int) round((int) $venta->cambio * $montoMetodo / $efectivoVenta)
                        : 0;
                }

                return max(0, $montoMetodo);
            });

            // Gastos (valor + ahorro) registrados con este método se descuentan
            // del saldo que quedó en caja para ese método de pago.
            $gastos = (int) $turno->gastos
                ->where('metodo_pago_id', $m->id)
                ->sum(fn ($g) => (int) $g->valor + (int) $g->ahorro);

            return [
                'codigo'      => $m->codigo,
                'nombre'      => $m->nombre,
                'es_efectivo' => $m->es_efectivo,
                'ventas'      => $ventas,
                'gastos'      => $gastos,
                'monto'       => $ventas - $gastos,
            ];
        })->filter(fn ($r) => $r['ventas'] > 0 || $r['gastos'] > 0)->values();

        $totalGastosGeneral = (int) $turno->gastos
            ->where('tipo', \App\Enums\TipoGasto::General)
            ->sum('valor');
        $totalGastosTurno = (int) $turno->gastos
            ->where('tipo', \App\Enums\TipoGasto::Turno)
            ->sum('valor');

        return view('caja-dashboard.show', compact('turno', 'desglosePorMetodo', 'totalGastosGeneral', 'totalGastosTurno'));
    }
}
