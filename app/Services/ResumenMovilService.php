<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EstadoNomina;
use App\Models\Nomina;
use App\Models\ProductoMercado;
use App\Models\TurnoCaja;
use App\Models\Venta;
use App\Models\VentaItem;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Arma el resumen financiero que consume la app móvil "Mis Negocios": el consolidado contable
 * del rango más los indicadores que le sirven al dueño (ventas por día, más vendidos, caja
 * abierta en este momento y alertas). Todos los montos van en pesos enteros sin formato.
 */
class ResumenMovilService
{
    private const TOP_ITEMS = 5;

    public function __construct(
        private ConsolidadoContableService $consolidado,
        private InventarioService $inventario,
    ) {
    }

    /** @return array<string, mixed> */
    public function generar(Carbon $desde, Carbon $hasta): array
    {
        $resumen = $this->consolidado->resumen($desde, $hasta);

        return [
            'negocio'    => config('app.name'),
            'periodo'    => ['desde' => $desde->toDateString(), 'hasta' => $hasta->toDateString()],
            'totales'    => [
                'ingresos'         => $resumen['totalIngresos'],
                'egresos'          => $resumen['totalEgresos'],
                'neto'             => $resumen['neto'],
                'egresosPorModulo' => $resumen['egresosPorModulo'],
                'porMetodo'        => $resumen['porMetodo']->map(fn (array $m) => [
                    'nombre'     => $m['nombre'],
                    'esEfectivo' => $m['es_efectivo'],
                    'ingresos'   => $m['ingresos'],
                    'egresos'    => $m['egresos'],
                    'neto'       => $m['neto'],
                ])->values()->all(),
            ],
            'ventas'      => $this->ventas($desde, $hasta),
            'cajaAbierta' => $this->cajaAbierta(),
            'alertas'     => [
                'productosAgotados' => $this->productosAgotados(),
                'nominaPendiente'   => $this->nominaPendiente(),
            ],
            'generadoEn' => Carbon::now()->toIso8601String(),
        ];
    }

    /** @return array{cantidad: int, total: int, ticketPromedio: int, porDia: array, topItems: array} */
    private function ventas(Carbon $desde, Carbon $hasta): array
    {
        $diarias = Venta::whereBetween('created_at', [$desde, $hasta])
            ->selectRaw('DATE(created_at) as dia, COUNT(*) as cantidad, SUM(total) as total')
            ->groupBy('dia')
            ->get()
            ->keyBy('dia');

        // Serie continua: los días sin ventas van en 0 para que la gráfica no salte fechas.
        $porDia = [];
        foreach (CarbonPeriod::create($desde->copy()->startOfDay(), $hasta->copy()->startOfDay()) as $dia) {
            $fila     = $diarias->get($dia->toDateString());
            $porDia[] = [
                'fecha'    => $dia->toDateString(),
                'total'    => (int) ($fila->total ?? 0),
                'cantidad' => (int) ($fila->cantidad ?? 0),
            ];
        }

        $cantidad = (int) $diarias->sum('cantidad');
        $total    = (int) $diarias->sum('total');

        $topItems = VentaItem::query()
            ->whereHas('venta', fn ($q) => $q->whereBetween('created_at', [$desde, $hasta]))
            ->selectRaw('nombre_snapshot as nombre, SUM(cantidad) as cantidad, SUM(subtotal) as total')
            ->groupBy('nombre_snapshot')
            ->orderByDesc('total')
            ->limit(self::TOP_ITEMS)
            ->get()
            ->map(fn ($i) => [
                'nombre'   => (string) $i->nombre,
                'cantidad' => (int) $i->cantidad,
                'total'    => (int) $i->total,
            ])
            ->all();

        return [
            'cantidad'       => $cantidad,
            'total'          => $total,
            'ticketPromedio' => $cantidad > 0 ? intdiv($total, $cantidad) : 0,
            'porDia'         => $porDia,
            'topItems'       => $topItems,
        ];
    }

    /** Turno de caja abierto ahora mismo (el más reciente si hubiera varios). */
    private function cajaAbierta(): ?array
    {
        $turno = TurnoCaja::abierto()
            ->with(['ventas.pagos.metodo', 'gastos', 'gastosMercado'])
            ->latest('abierto_en')
            ->first();

        if ($turno === null) {
            return null;
        }

        return [
            'abiertoEn'        => $turno->abierto_en->toIso8601String(),
            'base'             => (int) $turno->base_inicial,
            'cantidadVentas'   => $turno->ventas->count(),
            'ventas'           => $turno->total_ventas,
            'gastos'           => $turno->total_gastos + $turno->total_ahorros + $turno->total_gastos_mercado,
            'efectivoEsperado' => $turno->efectivo_esperado,
        ];
    }

    /** @return array<int, array{nombre: string, unidad: ?string}> */
    private function productosAgotados(): array
    {
        $productos = ProductoMercado::conInventario()->activos()->orderBy('nombre')->get(['id', 'nombre', 'unidad_empaque']);
        $stock     = $this->inventario->stockDe($productos->pluck('id')->all());

        return $productos
            ->filter(fn (ProductoMercado $p) => ($stock[$p->id] ?? 0) <= 0)
            ->map(fn (ProductoMercado $p) => ['nombre' => $p->nombre, 'unidad' => $p->unidad_empaque])
            ->values()
            ->all();
    }

    /** Neto de nóminas aprobadas que aún no se ha pagado a los empleados. */
    private function nominaPendiente(): int
    {
        return (int) Nomina::where('estado', EstadoNomina::Aprobada)
            ->with(['detalles' => fn ($q) => $q->withSum('pagos', 'monto')])
            ->get()
            ->sum->total_pendiente;
    }
}
