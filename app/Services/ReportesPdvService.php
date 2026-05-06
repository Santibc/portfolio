<?php

namespace App\Services;

use App\Models\VentaPdv;
use App\Models\ItemVentaPdv;
use App\Models\SesionCaja;
use App\Models\ValeCaja;
use App\Models\Prefactura;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class ReportesPdvService
{
    public function ventasDiarias(?int $cajaId = null, ?string $fecha = null): array
    {
        $fecha = $fecha ?? now()->toDateString();
        $query = VentaPdv::with('usuario', 'caja')
            ->completadas()
            ->whereDate('created_at', $fecha);

        if ($cajaId) {
            $query->where('caja_id', $cajaId);
        }

        $ventas = $query->get();

        return [
            'fecha' => $fecha,
            'total_ventas' => $ventas->count(),
            'monto_total' => $ventas->sum('total'),
            'por_caja' => $ventas->groupBy('caja_id')->map(fn($g) => [
                'caja' => $g->first()->caja->nombre ?? 'Sin caja',
                'cantidad' => $g->count(),
                'total' => $g->sum('total'),
            ]),
            'por_vendedor' => $ventas->groupBy('usuario_id')->map(fn($g) => [
                'vendedor' => $g->first()->usuario->name ?? 'Desconocido',
                'cantidad' => $g->count(),
                'total' => $g->sum('total'),
            ]),
            'por_metodo_pago' => $this->desglosarPorMetodoPago($ventas),
        ];
    }

    /**
     * Distribuye las ventas por método de pago, separando las "mixtas" en sus
     * partes de efectivo y transferencia. Una venta mixta cuenta +1 en cada
     * categoría donde aporta valor > 0.
     */
    protected function desglosarPorMetodoPago($ventas): \Illuminate\Support\Collection
    {
        $desglose = [
            'efectivo'      => ['cantidad' => 0, 'total' => 0.0],
            'transferencia' => ['cantidad' => 0, 'total' => 0.0],
        ];

        foreach ($ventas as $venta) {
            $efv = (float) ($venta->monto_efectivo ?? 0) - (float) ($venta->cambio ?? 0);
            $trv = (float) ($venta->monto_transferencia ?? 0);

            if ($venta->metodo_pago === 'mixto') {
                if ($efv > 0) {
                    $desglose['efectivo']['cantidad']++;
                    $desglose['efectivo']['total'] += $efv;
                }
                if ($trv > 0) {
                    $desglose['transferencia']['cantidad']++;
                    $desglose['transferencia']['total'] += $trv;
                }
            } elseif ($venta->metodo_pago === 'efectivo') {
                $desglose['efectivo']['cantidad']++;
                $desglose['efectivo']['total'] += $efv > 0 ? $efv : (float) $venta->total;
            } elseif ($venta->metodo_pago === 'transferencia') {
                $desglose['transferencia']['cantidad']++;
                $desglose['transferencia']['total'] += $trv > 0 ? $trv : (float) $venta->total;
            }
        }

        return collect($desglose)->filter(fn($d) => $d['cantidad'] > 0);
    }

    public function reporteCierre(int $sesionId): array
    {
        $sesion = SesionCaja::with(['caja', 'usuario', 'ventas', 'vales'])->findOrFail($sesionId);

        $ventasCompletadas = $sesion->ventas()->where('estado', 'completada')->get();
        $ventasAnuladas = $sesion->ventas()->where('estado', 'anulada')->get();
        $vales = $sesion->vales()->whereIn('estado', ['pendiente', 'redimido'])->get();

        return [
            'sesion' => $sesion,
            'caja' => $sesion->caja,
            'usuario' => $sesion->usuario,
            'ventas_completadas' => $ventasCompletadas,
            'ventas_anuladas' => $ventasAnuladas,
            'vales' => $vales,
            'resumen' => [
                'monto_apertura' => $sesion->monto_apertura,
                'total_ventas' => $ventasCompletadas->sum('total'),
                'cantidad_ventas' => $ventasCompletadas->count(),
                'total_efectivo' => $ventasCompletadas->sum(fn($v) => (float) ($v->monto_efectivo ?? 0) - (float) ($v->cambio ?? 0)),
                'total_transferencia' => $ventasCompletadas->sum('monto_transferencia'),
                'total_vales' => $vales->sum('monto'),
                'total_anulaciones' => $ventasAnuladas->sum('total'),
                'monto_esperado' => $sesion->monto_esperado_efectivo,
                'monto_contado' => $sesion->monto_contado,
                'diferencia' => $sesion->diferencia,
            ],
            'por_metodo_pago' => $this->desglosarPorMetodoPago($ventasCompletadas),
        ];
    }

    public function topProductos(?int $cajaId = null, ?string $fechaDesde = null, ?string $fechaHasta = null, int $limite = 10): array
    {
        $fechaDesde = $fechaDesde ?? now()->startOfMonth()->toDateString();
        $fechaHasta = $fechaHasta ?? now()->toDateString();

        $query = ItemVentaPdv::query()
            ->join('ventas_pdv', 'items_venta_pdv.venta_pdv_id', '=', 'ventas_pdv.id')
            ->where('ventas_pdv.estado', 'completada')
            ->whereBetween('ventas_pdv.created_at', [$fechaDesde . ' 00:00:00', $fechaHasta . ' 23:59:59']);

        if ($cajaId) {
            $query->where('ventas_pdv.caja_id', $cajaId);
        }

        return $query->select('items_venta_pdv.producto_id')
            ->selectRaw('SUM(items_venta_pdv.cantidad) as cantidad_total')
            ->selectRaw('SUM(items_venta_pdv.total) as monto_total')
            ->groupBy('items_venta_pdv.producto_id')
            ->orderByDesc('cantidad_total')
            ->limit($limite)
            ->get()
            ->map(function ($item) {
                $producto = Producto::find($item->producto_id);
                return [
                    'producto_id' => $item->producto_id,
                    'nombre' => $producto ? $producto->nombre : 'Producto eliminado',
                    'referencia' => $producto ? $producto->referencia : '-',
                    'cantidad_total' => $item->cantidad_total,
                    'monto_total' => $item->monto_total,
                ];
            })
            ->toArray();
    }

    public function comparativaCajas(string $fechaDesde, string $fechaHasta): array
    {
        return VentaPdv::with('caja')
            ->completadas()
            ->whereBetween('created_at', [$fechaDesde . ' 00:00:00', $fechaHasta . ' 23:59:59'])
            ->get()
            ->groupBy('caja_id')
            ->map(function ($ventas) {
                $caja = $ventas->first()->caja;
                $desglose = $this->desglosarPorMetodoPago($ventas);
                return [
                    'caja_id' => $caja->id ?? null,
                    'caja_nombre' => $caja->nombre ?? 'Sin caja',
                    'cantidad_ventas' => $ventas->count(),
                    'total_ventas' => $ventas->sum('total'),
                    'promedio' => $ventas->avg('total'),
                    'total_efectivo' => $desglose['efectivo']['total'] ?? 0,
                    'total_transferencia' => $desglose['transferencia']['total'] ?? 0,
                ];
            })
            ->values()
            ->toArray();
    }

    public function reporteVales(array $filtros = []): array
    {
        $query = ValeCaja::with('usuario', 'caja', 'sesionCaja');

        if (!empty($filtros['caja_id'])) {
            $query->where('caja_id', $filtros['caja_id']);
        }
        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }
        if (!empty($filtros['fecha_desde'])) {
            $query->whereDate('created_at', '>=', $filtros['fecha_desde']);
        }
        if (!empty($filtros['fecha_hasta'])) {
            $query->whereDate('created_at', '<=', $filtros['fecha_hasta']);
        }

        $vales = $query->orderByDesc('created_at')->get();

        return [
            'vales' => $vales,
            'total' => $vales->sum('monto'),
            'por_estado' => $vales->groupBy('estado')->map(fn($g) => [
                'cantidad' => $g->count(),
                'total' => $g->sum('monto'),
            ]),
        ];
    }

    public function reportePrefacturas(array $filtros = []): array
    {
        $query = Prefactura::with('usuarioCreador', 'usuarioCajero', 'cliente');

        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }
        if (!empty($filtros['fecha_desde'])) {
            $query->whereDate('created_at', '>=', $filtros['fecha_desde']);
        }
        if (!empty($filtros['fecha_hasta'])) {
            $query->whereDate('created_at', '<=', $filtros['fecha_hasta']);
        }

        $prefacturas = $query->orderByDesc('created_at')->get();

        // Calculate response times for accepted prefacturas
        $tiemposRespuesta = $prefacturas->where('estado', 'aceptada')
            ->map(function ($pf) {
                if ($pf->aceptada_en && $pf->created_at) {
                    return $pf->created_at->diffInMinutes($pf->aceptada_en);
                }
                return null;
            })
            ->filter()
            ->values();

        return [
            'prefacturas' => $prefacturas,
            'por_estado' => $prefacturas->groupBy('estado')->map(fn($g) => [
                'cantidad' => $g->count(),
                'total' => $g->sum('total'),
            ]),
            'tiempo_respuesta_promedio' => $tiemposRespuesta->avg() ?? 0,
            'total' => $prefacturas->sum('total'),
        ];
    }
}
