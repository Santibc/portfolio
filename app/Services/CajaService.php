<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\SesionCaja;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CajaService
{
    public function abrirSesion(int $cajaId, int $usuarioId, float $montoApertura): array
    {
        $caja = Caja::findOrFail($cajaId);

        if ($caja->estaAbierta()) {
            return ['exito' => false, 'mensaje' => 'Esta caja ya tiene una sesión abierta'];
        }

        if (!$caja->activo) {
            return ['exito' => false, 'mensaje' => 'Esta caja no está activa'];
        }

        DB::beginTransaction();
        try {
            $sesion = SesionCaja::create([
                'caja_id' => $cajaId,
                'usuario_id' => $usuarioId,
                'estado' => 'abierta',
                'monto_apertura' => $montoApertura,
                'abierta_en' => now(),
            ]);

            $caja->update(['estado' => 'abierta']);

            DB::commit();

            return [
                'exito' => true,
                'sesion' => $sesion->load('caja', 'usuario'),
                'mensaje' => "Caja '{$caja->nombre}' abierta exitosamente",
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al abrir caja: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Error al abrir la caja: ' . $e->getMessage()];
        }
    }

    public function cerrarSesion(int $sesionId, float $montoContado, ?string $observaciones = null): array
    {
        $sesion = SesionCaja::with('caja')->findOrFail($sesionId);

        if (!$sesion->estaAbierta()) {
            return ['exito' => false, 'mensaje' => 'Esta sesión ya está cerrada'];
        }

        DB::beginTransaction();
        try {
            $sesion->caja->update(['estado' => 'en_cierre']);
            $sesion->cerrar($montoContado, $observaciones);

            DB::commit();

            return [
                'exito' => true,
                'sesion' => $sesion->fresh()->load('caja', 'usuario'),
                'mensaje' => "Caja '{$sesion->caja->nombre}' cerrada exitosamente",
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al cerrar caja: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Error al cerrar la caja: ' . $e->getMessage()];
        }
    }

    public function calcularResumenCierre(int $sesionId): array
    {
        $sesion = SesionCaja::with(['ventas', 'vales', 'caja'])->findOrFail($sesionId);

        $ventasCompletadas = $sesion->ventas()->where('estado', 'completada')->get();
        $ventasAnuladas = $sesion->ventas()->where('estado', 'anulada')->get();
        $valesActivos = $sesion->vales()->whereIn('estado', ['pendiente', 'redimido'])->get();

        $totalEfectivo = $ventasCompletadas->sum('monto_efectivo');
        $totalTransferencia = $ventasCompletadas->sum('monto_transferencia');
        $totalCambio = $ventasCompletadas->sum('cambio');
        $totalVales = $valesActivos->sum('monto');

        $montoEsperadoEfectivo = $sesion->monto_apertura + $totalEfectivo - $totalCambio - $totalVales;

        return [
            'sesion' => $sesion,
            'monto_apertura' => $sesion->monto_apertura,
            'ventas' => [
                'cantidad' => $ventasCompletadas->count(),
                'total' => $ventasCompletadas->sum('total'),
                'efectivo' => $totalEfectivo,
                'transferencia' => $totalTransferencia,
                'cambio_entregado' => $totalCambio,
            ],
            'anulaciones' => [
                'cantidad' => $ventasAnuladas->count(),
                'total' => $ventasAnuladas->sum('total'),
            ],
            'vales' => [
                'cantidad' => $valesActivos->count(),
                'total' => $totalVales,
            ],
            'monto_esperado_efectivo' => round($montoEsperadoEfectivo, 2),
            'por_metodo_pago' => $ventasCompletadas->groupBy('metodo_pago')->map(fn($g) => [
                'cantidad' => $g->count(),
                'total' => $g->sum('total'),
            ]),
            'por_tipo_transferencia' => $ventasCompletadas
                ->whereNotNull('tipo_transferencia')
                ->groupBy('tipo_transferencia')
                ->map(fn($g) => [
                    'cantidad' => $g->count(),
                    'total' => $g->sum('monto_transferencia'),
                ]),
        ];
    }

    public function obtenerSesionActiva(int $cajaId): ?SesionCaja
    {
        return SesionCaja::where('caja_id', $cajaId)
            ->where('estado', 'abierta')
            ->with('caja', 'usuario')
            ->first();
    }

    public function obtenerSesionActivaDeUsuario(int $usuarioId): ?SesionCaja
    {
        return SesionCaja::where('usuario_id', $usuarioId)
            ->where('estado', 'abierta')
            ->with('caja', 'usuario')
            ->first();
    }
}
