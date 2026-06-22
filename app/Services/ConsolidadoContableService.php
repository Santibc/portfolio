<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Gasto;
use App\Models\GastoFijo;
use App\Models\MetodoPago;
use App\Models\PagoNomina;
use App\Models\RegistroMercado;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Calcula el consolidado contable de todos los módulos (caja, mercado, nómina,
 * gastos fijos) discriminado por método de pago, para un rango de fechas.
 *
 * Ingresos = ventas de caja (único origen de ingresos del negocio).
 * Egresos  = gastos de caja (valor + ahorro) + mercado + pagos de nómina + gastos fijos.
 * Cada fuente se cuenta UNA sola vez por su propia fecha, sin doble conteo.
 */
class ConsolidadoContableService
{
    /**
     * @return array{
     *     porMetodo: Collection<int, array<string, mixed>>,
     *     egresosPorModulo: array<string, int>,
     *     totalIngresos: int,
     *     totalEgresos: int,
     *     neto: int
     * }
     */
    public function resumen(Carbon $desde, Carbon $hasta): array
    {
        // ---- INGRESOS (solo ventas de caja, por método, con cambio descontado del efectivo) ----
        $ventas = Venta::with('pagos.metodo')
            ->whereBetween('created_at', [$desde, $hasta])
            ->get();

        $ingresos = $this->ingresosPorMetodo($ventas); // [metodo_pago_id => int]

        // ---- EGRESOS por módulo, agrupados por método (0 = sin método asignado) ----
        $egCaja = $this->sumarPorMetodo(
            Gasto::whereBetween('created_at', [$desde, $hasta])->get(['metodo_pago_id', 'valor', 'ahorro']),
            fn (Gasto $g) => (int) $g->valor + (int) $g->ahorro
        );

        $egMercado = $this->sumarPorMetodo(
            RegistroMercado::whereBetween('created_at', [$desde, $hasta])->get(['metodo_pago_id', 'valor']),
            fn (RegistroMercado $r) => (int) $r->valor
        );

        $egNomina = $this->sumarPorMetodo(
            PagoNomina::whereBetween('fecha_pago', [$desde, $hasta])->get(['metodo_pago_id', 'monto']),
            fn (PagoNomina $p) => (int) $p->monto
        );

        $egFijos = $this->sumarPorMetodo(
            GastoFijo::whereBetween('fecha', [$desde, $hasta])->get(['metodo_pago_id', 'valor']),
            fn (GastoFijo $f) => (int) $f->valor
        );

        // ---- Filas por método (incluye una fila "Sin método" para egresos sin metodo_pago_id) ----
        $metodos = MetodoPago::withTrashed()->orderBy('orden')->get();

        $porMetodo = $metodos->map(function (MetodoPago $m) use ($ingresos, $egCaja, $egMercado, $egNomina, $egFijos) {
            $ing = (int) ($ingresos[$m->id] ?? 0);
            $egr = (int) (($egCaja[$m->id] ?? 0) + ($egMercado[$m->id] ?? 0) + ($egNomina[$m->id] ?? 0) + ($egFijos[$m->id] ?? 0));

            return [
                'metodo_id' => $m->id,
                'nombre' => $m->nombre,
                'es_efectivo' => (bool) $m->es_efectivo,
                'ingresos' => $ing,
                'egresos' => $egr,
                'neto' => $ing - $egr,
            ];
        });

        $egrSinMetodo = (int) (($egCaja[0] ?? 0) + ($egMercado[0] ?? 0) + ($egNomina[0] ?? 0) + ($egFijos[0] ?? 0));
        if ($egrSinMetodo > 0) {
            $porMetodo->push([
                'metodo_id' => null,
                'nombre' => 'Sin método',
                'es_efectivo' => false,
                'ingresos' => 0,
                'egresos' => $egrSinMetodo,
                'neto' => -$egrSinMetodo,
            ]);
        }

        $porMetodo = $porMetodo
            ->filter(fn (array $r) => $r['ingresos'] > 0 || $r['egresos'] > 0)
            ->values();

        // ---- Totales ----
        $egresosPorModulo = [
            'caja' => (int) array_sum($egCaja),
            'mercado' => (int) array_sum($egMercado),
            'nomina' => (int) array_sum($egNomina),
            'fijos' => (int) array_sum($egFijos),
        ];

        $totalIngresos = (int) array_sum($ingresos);
        $totalEgresos = (int) array_sum($egresosPorModulo);

        return [
            'porMetodo' => $porMetodo,
            'egresosPorModulo' => $egresosPorModulo,
            'totalIngresos' => $totalIngresos,
            'totalEgresos' => $totalEgresos,
            'neto' => $totalIngresos - $totalEgresos,
        ];
    }

    /**
     * Ingresos por método de pago a partir de una colección de ventas.
     * Replica el algoritmo de DashboardCajaController::desglosePorMetodo: el cambio
     * siempre sale del efectivo, así que se descuenta proporcionalmente del monto
     * en efectivo de cada venta para reflejar el neto que realmente quedó en caja.
     *
     * @param  Collection<int, Venta>  $ventas
     * @return array<int, int> [metodo_pago_id => total]
     */
    private function ingresosPorMetodo(Collection $ventas): array
    {
        $acc = [];

        foreach ($ventas as $venta) {
            $cambio = (int) $venta->cambio;
            $efectivoVenta = (int) $venta->pagos
                ->filter(fn ($p) => optional($p->metodo)->es_efectivo)
                ->sum('monto');

            foreach ($venta->pagos->groupBy('metodo_pago_id') as $mid => $pagos) {
                $monto = (int) $pagos->sum('monto');
                $esEfectivo = (bool) optional($pagos->first()->metodo)->es_efectivo;

                if ($esEfectivo && $monto > 0 && $cambio > 0) {
                    $monto -= $efectivoVenta > 0
                        ? (int) round($cambio * $monto / $efectivoVenta)
                        : 0;
                }

                $acc[(int) $mid] = ($acc[(int) $mid] ?? 0) + max(0, $monto);
            }
        }

        return $acc;
    }

    /**
     * Suma registros por método de pago. La clave 0 acumula los registros sin
     * metodo_pago_id (columna nullable en gastos y registros de mercado).
     *
     * @param  Collection<int, object>  $registros
     * @param  callable(object): int  $valor
     * @return array<int, int> [metodo_pago_id => total]  (0 = sin método)
     */
    private function sumarPorMetodo(Collection $registros, callable $valor): array
    {
        $acc = [];

        foreach ($registros as $r) {
            $mid = (int) ($r->metodo_pago_id ?? 0);
            $acc[$mid] = ($acc[$mid] ?? 0) + (int) $valor($r);
        }

        return $acc;
    }
}
