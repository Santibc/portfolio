<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use App\Models\TurnoCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        // Total por método de pago en todo el rango: lo recibido menos lo gastado
        // con ese método, sumando todos los turnos del filtro.
        $desglosePorMetodo = $this->desglosePorMetodo($turnos);

        return view('caja-dashboard.index', [
            'turnos'            => $turnos,
            'desde'             => $desde->toDateString(),
            'hasta'             => $hasta->toDateString(),
            'totalVentas'       => $totalVentas,
            'totalEfectivo'     => $totalEfectivo,
            'totalNoEfvo'       => $totalNoEfvo,
            'totalGastos'       => $totalGastos,
            'totalAhorros'      => $totalAhorros,
            'neto'              => $neto,
            'turnosAbiertos'    => $turnosAbiertos,
            'desglosePorMetodo' => $desglosePorMetodo,
        ]);
    }

    /**
     * Desglose agregado por método de pago para una colección de turnos:
     * lo recibido en ventas menos lo gastado (valor + ahorro) con cada método.
     *
     * @param  Collection<int, TurnoCaja>  $turnos
     * @return Collection<int, array<string, mixed>>
     */
    private function desglosePorMetodo(Collection $turnos): Collection
    {
        return MetodoPago::orderBy('orden')->get()->map(function (MetodoPago $m) use ($turnos) {
            $ventas = (int) $turnos->sum(fn ($turno) => (int) $turno->ventas->sum(function ($venta) use ($m) {
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
            }));

            // Gastos (valor + ahorro) registrados con este método se descuentan
            // del saldo que quedó en caja para ese método de pago.
            $gastos = (int) $turnos->sum(fn ($turno) => (int) $turno->gastos
                ->where('metodo_pago_id', $m->id)
                ->sum(fn ($g) => (int) $g->valor + (int) $g->ahorro));

            return [
                'codigo'      => $m->codigo,
                'nombre'      => $m->nombre,
                'es_efectivo' => $m->es_efectivo,
                'ventas'      => $ventas,
                'gastos'      => $gastos,
                'monto'       => $ventas - $gastos,
            ];
        })->filter(fn ($r) => $r['ventas'] > 0 || $r['gastos'] > 0)->values();
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

        $desglosePorMetodo = $this->desglosePorMetodo(collect([$turno]));

        $totalGastosGeneral = (int) $turno->gastos
            ->where('tipo', \App\Enums\TipoGasto::General)
            ->sum('valor');
        $totalGastosTurno = (int) $turno->gastos
            ->where('tipo', \App\Enums\TipoGasto::Turno)
            ->sum('valor');

        // Desglose de items vendidos en el turno: agrupados por item de menú,
        // con la cantidad total y el total vendido (suma de subtotales).
        $desglosePorItem = $turno->ventas
            ->flatMap(fn ($venta) => $venta->items)
            ->groupBy('menu_item_id')
            ->map(fn ($items) => [
                'nombre'   => $items->first()->nombre_snapshot,
                'cantidad' => (int) $items->sum('cantidad'),
                'total'    => (int) $items->sum('subtotal'),
            ])
            ->sortByDesc('total')
            ->values();

        // Datos de ventas pre-formateados para la tabla cliente (orden + paginación
        // en el navegador con Alpine). Se incluyen valores crudos para ordenar y
        // formateados para mostrar, además de items/pagos para el detalle expandible.
        $ventasData = $turno->ventas->map(fn ($v) => [
            'id'                    => $v->id,
            'hora'                  => $v->created_at->format('H:i:s'),
            'ts'                    => $v->created_at->timestamp,
            'cajero'                => $v->user?->name ?? '—',
            'items_count'           => (int) $v->items->sum('cantidad'),
            'total'                 => (int) $v->total,
            'total_fmt'             => $v->total_formateado,
            'cambio'                => (int) $v->cambio,
            'cambio_fmt'            => $v->cambio_formateado,
            'notas'                 => $v->notas,
            'efectivo_recibido'     => (int) $v->efectivo_recibido,
            'efectivo_recibido_fmt' => $v->efectivo_recibido_formateado,
            'edit_url'              => route('caja.venta.edit', $v),
            'destroy_url'           => route('caja.venta.destroy', $v),
            'pagos'                 => $v->pagos->map(fn ($p) => [
                'nombre'     => $p->metodo?->nombre ?? '—',
                'monto'      => (int) $p->monto,
                'referencia' => $p->referencia,
            ])->values(),
            'items'                 => $v->items->map(fn ($it) => [
                'label'        => $it->cantidad . ' × ' . $it->nombre_snapshot,
                'subtotal_fmt' => $it->subtotal_formateado,
            ])->values(),
        ])->values();

        return view('caja-dashboard.show', compact('turno', 'desglosePorMetodo', 'desglosePorItem', 'ventasData', 'totalGastosGeneral', 'totalGastosTurno'));
    }
}
