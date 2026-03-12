<?php

namespace App\Services;

use App\Models\DevolucionGarantia;
use App\Models\Orden;
use App\Models\OrdenPieza;
use App\Models\Pago;
use App\Models\User;

class DashboardService
{
    /**
     * Metricas del panel de Recepcion (6 widgets + garantias).
     */
    public function getRecepcionStats(): array
    {
        $hoy = today();
        $manana = today()->addDay();

        $baseActivas = fn() => Orden::noAnuladas()->noBorradores();
        $noEntregada = fn($q) => $q->where(function ($q2) {
            $q2->whereNull('estado_entrega')->orWhere('estado_entrega', '!=', 'entregada');
        });

        // Widget 1: Entregas pendientes hoy
        $entregasHoy = $baseActivas()
            ->whereDate('fecha_entrega', $hoy)
            ->where($noEntregada)
            ->count();

        // Widget 2: Entregas hoy/manana
        $entregasHoyManana = $baseActivas()
            ->whereBetween('fecha_entrega', [$hoy->toDateString(), $manana->toDateString()])
            ->where($noEntregada)
            ->count();

        // Widget 3: Entregas vencidas
        $entregasVencidas = $baseActivas()
            ->whereDate('fecha_entrega', '<', $hoy)
            ->where($noEntregada)
            ->count();

        // Widget 4: Ordenes abiertas (en proceso)
        $ordenesAbiertas = $baseActivas()
            ->whereIn('estado_trabajo', ['generada', 'en_ejecucion', 'ejecutada_parcialmente'])
            ->count();

        // Widget 5: Saldo pendiente total
        $saldoPendiente = $baseActivas()
            ->where('estado_pago', 'saldo_pendiente')
            ->sum('saldo');

        // Widget 6: Piezas para complementar (sin operario asignado)
        $paraComplementar = OrdenPieza::whereNull('operario_actual_id')
            ->where('porcentaje_avance', '<', 100)
            ->whereHas('orden', function ($q) {
                $q->noAnuladas()->noBorradores()
                    ->where(function ($q2) {
                        $q2->whereNull('estado_entrega')
                            ->orWhere('estado_entrega', '!=', 'entregada');
                    });
            })
            ->count();

        // Extra: Garantias activas
        $garantiasActivas = DevolucionGarantia::whereIn('estado', ['abierta', 'en_proceso'])->count();

        return [
            'entregas_hoy' => $entregasHoy,
            'entregas_hoy_manana' => $entregasHoyManana,
            'entregas_vencidas' => $entregasVencidas,
            'ordenes_abiertas' => $ordenesAbiertas,
            'saldo_pendiente' => $saldoPendiente,
            'para_complementar' => $paraComplementar,
            'garantias_activas' => $garantiasActivas,
        ];
    }

    /**
     * Garantias en proceso asignadas a un operario.
     */
    public function getGarantiasOperario(User $operario): int
    {
        return DevolucionGarantia::where('operario_asignado_id', $operario->id)
            ->where('estado', 'en_proceso')
            ->count();
    }

    /**
     * Garantias cobrables activas (para contabilidad).
     */
    public function getGarantiasCobrables(): array
    {
        $query = DevolucionGarantia::where('cobrable', true)
            ->whereIn('estado', ['abierta', 'en_proceso', 'completada']);

        return [
            'count' => (clone $query)->count(),
            'monto' => (clone $query)->sum('monto_cobro'),
        ];
    }

    /**
     * Metricas globales para el panel de Administrador.
     */
    public function getAdminStats(): array
    {
        $hoy = today();
        $baseActivas = fn() => Orden::noAnuladas()->noBorradores();

        return [
            'ordenes_activas' => $baseActivas()
                ->whereIn('estado_trabajo', ['generada', 'en_ejecucion', 'ejecutada_parcialmente'])
                ->count(),

            'entregas_vencidas' => $baseActivas()
                ->whereDate('fecha_entrega', '<', $hoy)
                ->where(function ($q) {
                    $q->whereNull('estado_entrega')->orWhere('estado_entrega', '!=', 'entregada');
                })
                ->count(),

            'saldo_pendiente_total' => $baseActivas()
                ->where('estado_pago', 'saldo_pendiente')
                ->sum('saldo'),

            'recaudado_hoy' => Pago::where('aprobado', true)
                ->whereDate('created_at', $hoy)
                ->sum('monto'),

            'garantias_activas' => DevolucionGarantia::whereIn('estado', ['abierta', 'en_proceso'])->count(),

            'pagos_por_aprobar' => Pago::where('aprobado', false)->count(),

            'ordenes_hoy' => Orden::whereDate('created_at', $hoy)->noAnuladas()->count(),
        ];
    }
}
