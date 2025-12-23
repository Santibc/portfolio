<?php

namespace App\Services\Dividend;

use App\Models\User;
use App\Models\Proyecto;
use App\Models\Inversion;
use App\Models\Dividendo;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DividendCalculatorService
{
    /**
     * Calcular el monto de dividendo por período
     * Fórmula: (monto_invertido * roi_anual / 100) / períodos_al_año
     */
    public function calculatePeriodDividend(Inversion $inversion): float
    {
        $proyecto = $inversion->proyecto;
        $periodoDias = $proyecto->periodo_dividendos_dias ?? 30;

        // Calcular cuántos períodos hay en un año
        $periodosAlAno = 365 / $periodoDias;

        // Dividendo por período = (monto * ROI anual) / períodos al año
        $dividendoPeriodo = ($inversion->monto_invertido * ($proyecto->roi_anual / 100)) / $periodosAlAno;

        return round($dividendoPeriodo, 2);
    }

    /**
     * Calcular número de períodos de dividendos según duración del proyecto
     */
    public function calculateNumberOfPeriods(Proyecto $proyecto): int
    {
        $duracionDias = $proyecto->duracion_meses * 30;
        $periodoDias = $proyecto->periodo_dividendos_dias ?? 30;

        return (int) floor($duracionDias / $periodoDias);
    }

    /**
     * Generar la programación completa de dividendos para una inversión
     */
    public function generateDividendSchedule(Inversion $inversion): array
    {
        $proyecto = $inversion->proyecto;
        $numeroPeriodos = $this->calculateNumberOfPeriods($proyecto);
        $montoPorPeriodo = $this->calculatePeriodDividend($inversion);
        $periodoDias = $proyecto->periodo_dividendos_dias ?? 30;

        // Fecha del primer dividendo
        $fechaPrimerDividendo = $proyecto->fecha_primer_dividendo
            ?? Carbon::parse($inversion->fecha_inversion)->addDays($periodoDias);

        $dividendos = [];

        for ($i = 1; $i <= $numeroPeriodos; $i++) {
            $fechaProgramada = Carbon::parse($fechaPrimerDividendo)->addDays($periodoDias * ($i - 1));

            // No programar dividendos en el pasado
            if ($fechaProgramada->isPast()) {
                continue;
            }

            $dividendos[] = [
                'numero_periodo' => $i,
                'monto' => $montoPorPeriodo,
                'fecha_programada' => $fechaProgramada->format('Y-m-d'),
            ];
        }

        return $dividendos;
    }

    /**
     * Crear todos los dividendos programados para una inversión
     */
    public function scheduleDividendsForInvestment(Inversion $inversion): Collection
    {
        return DB::transaction(function () use ($inversion) {
            $programacion = $this->generateDividendSchedule($inversion);
            $dividendosCreados = collect();

            foreach ($programacion as $data) {
                $dividendo = Dividendo::create([
                    'codigo_dividendo' => $this->generateDividendCode(),
                    'inversion_id' => $inversion->id,
                    'proyecto_id' => $inversion->proyecto_id,
                    'usuario_id' => $inversion->usuario_id,
                    'numero_periodo' => $data['numero_periodo'],
                    'monto' => $data['monto'],
                    'fecha_programada' => $data['fecha_programada'],
                    'estado' => 'programado',
                ]);

                $dividendosCreados->push($dividendo);
            }

            // Actualizar dividendos pendientes en la billetera
            $totalPendiente = $dividendosCreados->sum('monto');
            $billetera = $inversion->usuario->billetera;
            if ($billetera) {
                $billetera->increment('dividendos_pendientes', $totalPendiente);
            }

            return $dividendosCreados;
        });
    }

    /**
     * Generar código único para dividendo
     * Formato: DIV-YYYY-XXXXXX
     */
    private function generateDividendCode(): string
    {
        $year = date('Y');
        $lastDividendo = Dividendo::where('codigo_dividendo', 'like', "DIV-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastDividendo) {
            $lastNumber = (int) substr($lastDividendo->codigo_dividendo, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf("DIV-%s-%06d", $year, $newNumber);
    }

    /**
     * Obtener resumen de dividendos para un usuario
     */
    public function getDividendSummary(User $user): array
    {
        $totalRecibido = Dividendo::where('usuario_id', $user->id)
            ->pagados()
            ->sum('monto');

        $pendientes = Dividendo::where('usuario_id', $user->id)
            ->pendientes()
            ->sum('monto');

        $proximoDividendo = Dividendo::where('usuario_id', $user->id)
            ->pendientes()
            ->orderBy('fecha_programada')
            ->first();

        $dividendosMes = Dividendo::where('usuario_id', $user->id)
            ->pagados()
            ->whereMonth('fecha_pagada', now()->month)
            ->whereYear('fecha_pagada', now()->year)
            ->sum('monto');

        $countPendientes = Dividendo::where('usuario_id', $user->id)
            ->pendientes()
            ->count();

        return [
            'total_recibido' => [
                'valor' => $totalRecibido,
                'formateado' => '$' . number_format($totalRecibido, 0, ',', '.'),
                'titulo' => 'Total Recibido',
                'icono' => 'fas fa-money-bill-wave',
                'color' => 'success',
            ],
            'pendientes' => [
                'valor' => $pendientes,
                'formateado' => '$' . number_format($pendientes, 0, ',', '.'),
                'titulo' => 'Pendientes',
                'icono' => 'fas fa-hourglass-half',
                'color' => 'warning',
                'count' => $countPendientes,
            ],
            'proximo_dividendo' => [
                'valor' => $proximoDividendo?->monto ?? 0,
                'formateado' => $proximoDividendo
                    ? '$' . number_format($proximoDividendo->monto, 0, ',', '.')
                    : 'Sin programar',
                'fecha' => $proximoDividendo?->fecha_programada?->format('d/m/Y') ?? '-',
                'titulo' => 'Próximo Dividendo',
                'icono' => 'fas fa-calendar-check',
                'color' => 'info',
                'proyecto' => $proximoDividendo?->proyecto?->nombre ?? '-',
            ],
            'este_mes' => [
                'valor' => $dividendosMes,
                'formateado' => '$' . number_format($dividendosMes, 0, ',', '.'),
                'titulo' => 'Este Mes',
                'icono' => 'fas fa-calendar-alt',
                'color' => 'primary',
            ],
        ];
    }

    /**
     * Obtener dividendos de un usuario con filtros
     */
    public function getUserDividends(User $user, array $filters = [])
    {
        $query = Dividendo::where('usuario_id', $user->id)
            ->with(['proyecto', 'inversion']);

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
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
        $orderDir = $filters['direccion'] ?? 'desc';

        return $query->orderBy($orderBy, $orderDir)->paginate(15);
    }

    /**
     * Recalcular dividendos pendientes en billetera de usuario
     */
    public function recalculatePendingDividends(User $user): float
    {
        $totalPendiente = Dividendo::where('usuario_id', $user->id)
            ->pendientes()
            ->sum('monto');

        $billetera = $user->billetera;
        if ($billetera) {
            $billetera->update(['dividendos_pendientes' => $totalPendiente]);
        }

        return $totalPendiente;
    }
}
