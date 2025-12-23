<?php

namespace App\Services\Dividend;

use App\Models\User;
use App\Models\Dividendo;
use App\Models\Inversion;
use App\Services\Wallet\WalletService;
use App\Notifications\DividendoPagadoNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class DividendPaymentService
{
    public function __construct(
        private WalletService $walletService
    ) {}

    /**
     * Pagar un dividendo individual
     */
    public function payDividend(Dividendo $dividendo, ?User $paidBy = null): Dividendo
    {
        if ($dividendo->estado !== 'programado' && $dividendo->estado !== 'atrasado') {
            throw new Exception("El dividendo no está en estado pagable. Estado actual: {$dividendo->estado}");
        }

        return DB::transaction(function () use ($dividendo, $paidBy) {
            $usuario = $dividendo->usuario;
            $billetera = $this->walletService->getOrCreateWallet($usuario);
            $proyecto = $dividendo->proyecto;
            $inversion = $dividendo->inversion;

            // Acreditar a la billetera
            $descripcion = "Dividendo período {$dividendo->numero_periodo} - {$proyecto->nombre}";
            $this->walletService->receiveDividend(
                $billetera,
                $dividendo->monto,
                $descripcion,
                $dividendo
            );

            // Actualizar dividendos acumulados en la inversión
            $inversion->increment('dividendos_acumulados', $dividendo->monto);

            // Descontar de dividendos pendientes en billetera
            $billetera->decrement('dividendos_pendientes', $dividendo->monto);

            // Marcar dividendo como pagado
            $dividendo->update([
                'estado' => 'pagado',
                'fecha_pagada' => Carbon::now(),
                'pagado_por' => $paidBy?->id,
            ]);

            // Enviar notificación
            try {
                $usuario->notify(new DividendoPagadoNotification($dividendo));
            } catch (Exception $e) {
                Log::warning("No se pudo enviar notificación de dividendo: " . $e->getMessage());
            }

            return $dividendo->fresh();
        });
    }

    /**
     * Procesar todos los dividendos vencidos (para cron)
     */
    public function processAllDueDividends(): array
    {
        $resultado = [
            'paid' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $dividendosPendientes = $this->getDueDividends();

        foreach ($dividendosPendientes as $dividendo) {
            try {
                $this->payDividend($dividendo);
                $resultado['paid']++;

                Log::info("Dividendo pagado: {$dividendo->codigo_dividendo} - Usuario: {$dividendo->usuario_id}");
            } catch (Exception $e) {
                $resultado['failed']++;
                $resultado['errors'][] = [
                    'dividendo_id' => $dividendo->id,
                    'codigo' => $dividendo->codigo_dividendo,
                    'error' => $e->getMessage(),
                ];

                Log::error("Error pagando dividendo {$dividendo->codigo_dividendo}: " . $e->getMessage());
            }
        }

        return $resultado;
    }

    /**
     * Marcar dividendos atrasados
     */
    public function markOverdueDividends(): int
    {
        $count = Dividendo::where('estado', 'programado')
            ->whereDate('fecha_programada', '<', Carbon::today())
            ->update(['estado' => 'atrasado']);

        if ($count > 0) {
            Log::info("Se marcaron {$count} dividendos como atrasados");
        }

        return $count;
    }

    /**
     * Obtener dividendos pendientes de pago (fecha <= hoy)
     */
    public function getDueDividends(): Collection
    {
        return Dividendo::whereIn('estado', ['programado', 'atrasado'])
            ->whereDate('fecha_programada', '<=', Carbon::today())
            ->with(['usuario', 'proyecto', 'inversion'])
            ->orderBy('fecha_programada')
            ->get();
    }

    /**
     * Obtener dividendos próximos (siguientes X días)
     */
    public function getUpcomingDividends(int $days = 7): Collection
    {
        return Dividendo::where('estado', 'programado')
            ->whereDate('fecha_programada', '>', Carbon::today())
            ->whereDate('fecha_programada', '<=', Carbon::today()->addDays($days))
            ->with(['usuario', 'proyecto', 'inversion'])
            ->orderBy('fecha_programada')
            ->get();
    }

    /**
     * Cancelar todos los dividendos pendientes de una inversión
     */
    public function cancelInvestmentDividends(Inversion $inversion, string $reason): int
    {
        return DB::transaction(function () use ($inversion, $reason) {
            $dividendosPendientes = Dividendo::where('inversion_id', $inversion->id)
                ->whereIn('estado', ['programado', 'atrasado'])
                ->get();

            $totalCancelado = $dividendosPendientes->sum('monto');
            $count = $dividendosPendientes->count();

            foreach ($dividendosPendientes as $dividendo) {
                $dividendo->update([
                    'estado' => 'cancelado',
                    'notas' => $reason,
                ]);
            }

            // Actualizar dividendos pendientes en billetera
            $billetera = $inversion->usuario->billetera;
            if ($billetera && $totalCancelado > 0) {
                $billetera->decrement('dividendos_pendientes', $totalCancelado);
            }

            Log::info("Cancelados {$count} dividendos de inversión {$inversion->codigo_inversion}: {$reason}");

            return $count;
        });
    }

    /**
     * Obtener estadísticas de dividendos para admin
     */
    public function getAdminStats(): array
    {
        $pendientesHoy = Dividendo::where('estado', 'programado')
            ->whereDate('fecha_programada', '<=', Carbon::today())
            ->count();

        $atrasados = Dividendo::where('estado', 'atrasado')->count();

        $pagadosMes = Dividendo::where('estado', 'pagado')
            ->whereMonth('fecha_pagada', now()->month)
            ->whereYear('fecha_pagada', now()->year)
            ->sum('monto');

        $programadosSemana = Dividendo::where('estado', 'programado')
            ->whereDate('fecha_programada', '>', Carbon::today())
            ->whereDate('fecha_programada', '<=', Carbon::today()->addDays(7))
            ->count();

        return [
            'pendientes_hoy' => $pendientesHoy,
            'atrasados' => $atrasados,
            'pagados_mes' => $pagadosMes,
            'proximos_7_dias' => $programadosSemana,
        ];
    }

    /**
     * Obtener todos los dividendos para admin con filtros
     */
    public function getAllDividends(array $filters = [])
    {
        $query = Dividendo::with(['usuario', 'proyecto', 'inversion']);

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (!empty($filters['buscar'])) {
            $buscar = $filters['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_dividendo', 'like', "%{$buscar}%")
                    ->orWhereHas('usuario', function ($u) use ($buscar) {
                        $u->where('name', 'like', "%{$buscar}%")
                            ->orWhere('email', 'like', "%{$buscar}%");
                    })
                    ->orWhereHas('proyecto', function ($p) use ($buscar) {
                        $p->where('nombre', 'like', "%{$buscar}%");
                    });
            });
        }

        if (!empty($filters['proyecto_id'])) {
            $query->where('proyecto_id', $filters['proyecto_id']);
        }

        if (!empty($filters['fecha_desde'])) {
            $query->whereDate('fecha_programada', '>=', $filters['fecha_desde']);
        }

        if (!empty($filters['fecha_hasta'])) {
            $query->whereDate('fecha_programada', '<=', $filters['fecha_hasta']);
        }

        $orderBy = $filters['orden'] ?? 'fecha_programada';
        $orderDir = $filters['direccion'] ?? 'asc';

        return $query->orderBy($orderBy, $orderDir)->paginate(20);
    }
}
