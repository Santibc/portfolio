<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Repositories\DashboardRepository;

class InvestorDashboardService
{
    public function __construct(
        private DashboardRepository $dashboardRepository
    ) {}

    /**
     * Obtener datos completos del dashboard inversionista
     */
    public function getDashboardData(User $user): array
    {
        return [
            'estadisticas' => $this->getEstadisticasInversionista($user),
            'rendimiento_portafolio' => $this->getRendimientoPortafolio($user),
            'inversiones_activas' => $this->getInversionesActivas($user),
            'proximos_dividendos' => $this->getProximosDividendos($user),
            'distribucion_inversiones' => $this->getDistribucionInversiones($user),
        ];
    }

    /**
     * Estadísticas del inversionista
     */
    private function getEstadisticasInversionista(User $user): array
    {
        $saldo = $this->dashboardRepository->getSaldoBilletera($user);
        $totalInvertido = $this->dashboardRepository->getTotalInvertido($user);
        $retornosAcumulados = $this->dashboardRepository->getRetornosAcumulados($user);
        $dividendosPendientes = $this->dashboardRepository->getDividendosPendientes($user);

        // Calcular ROI promedio
        $roi = $totalInvertido > 0 ? (($retornosAcumulados / $totalInvertido) * 100) : 0;

        return [
            [
                'titulo' => 'Saldo Disponible',
                'valor' => $this->formatearMoneda($saldo),
                'icono' => 'fas fa-wallet',
                'color' => 'primary',
                'descripcion' => 'En tu billetera',
            ],
            [
                'titulo' => 'Total Invertido',
                'valor' => $this->formatearMoneda($totalInvertido),
                'icono' => 'fas fa-chart-line',
                'color' => 'success',
                'descripcion' => 'En proyectos activos',
            ],
            [
                'titulo' => 'Retornos Acumulados',
                'valor' => $this->formatearMoneda($retornosAcumulados),
                'icono' => 'fas fa-hand-holding-usd',
                'color' => 'warning',
                'descripcion' => number_format($roi, 2) . '% ROI promedio',
            ],
            [
                'titulo' => 'Dividendos Pendientes',
                'valor' => $this->formatearMoneda($dividendosPendientes),
                'icono' => 'fas fa-coins',
                'color' => 'info',
                'descripcion' => 'Por cobrar',
            ],
        ];
    }

    /**
     * Rendimiento del portafolio (últimos 12 meses)
     */
    private function getRendimientoPortafolio(User $user): array
    {
        $rendimiento = $this->dashboardRepository->getRendimientoPortafolio($user, 12);

        $labels = $rendimiento->pluck('mes')->map(function ($mes) {
            return \Carbon\Carbon::createFromFormat('Y-m', $mes)->locale('es')->isoFormat('MMM YYYY');
        })->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Dividendos Recibidos',
                    'data' => $rendimiento->pluck('total')->toArray(),
                    'borderColor' => '#28a745',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    /**
     * Inversiones activas del usuario
     */
    private function getInversionesActivas(User $user): array
    {
        $inversiones = $this->dashboardRepository->getInversionesActivas($user);

        return $inversiones->map(function ($inversion) {
            return [
                'proyecto' => $inversion->proyecto->nombre,
                'categoria' => $inversion->proyecto->categoria->nombre,
                'monto' => $inversion->monto,
                'fecha' => $inversion->fecha_inversion,
                'retorno_esperado' => $inversion->retorno_esperado ?? 0,
                'progreso' => $this->calcularProgreso($inversion),
            ];
        })->toArray();
    }

    /**
     * Próximos dividendos a recibir
     */
    private function getProximosDividendos(User $user): array
    {
        $dividendos = $this->dashboardRepository->getProximosDividendos($user);

        return $dividendos->map(function ($dividendo) {
            return [
                'proyecto' => $dividendo->proyecto->nombre,
                'monto' => $dividendo->monto,
                'fecha_pago' => $dividendo->fecha_programada,
                'dias_restantes' => now()->diffInDays($dividendo->fecha_programada, false),
            ];
        })->toArray();
    }

    /**
     * Distribución de inversiones por categoría
     */
    private function getDistribucionInversiones(User $user): array
    {
        $inversiones = $this->dashboardRepository->getInversionesActivas($user);

        $distribucion = $inversiones->groupBy('proyecto.categoria.nombre')
            ->map(function ($items, $categoria) {
                return [
                    'categoria' => $categoria,
                    'total' => $items->sum('monto'),
                ];
            })
            ->values();

        return [
            'labels' => $distribucion->pluck('categoria')->toArray(),
            'data' => $distribucion->pluck('total')->toArray(),
            'backgroundColor' => [
                '#28a745', // STAKING
                '#007bff', // TRADING
                '#ffc107', // EAR
                '#17a2b8', // FUTUROS
                '#6f42c1', // CROSS_FUND
            ],
        ];
    }

    /**
     * Calcular progreso de una inversión
     */
    private function calcularProgreso($inversion): int
    {
        if (!$inversion->proyecto) {
            return 0;
        }

        $inicio = \Carbon\Carbon::parse($inversion->fecha_inversion);
        $fin = \Carbon\Carbon::parse($inversion->proyecto->fecha_cierre_recaudacion);
        $ahora = now();

        if ($ahora >= $fin) {
            return 100;
        }

        $total = $inicio->diffInDays($fin);
        $transcurrido = $inicio->diffInDays($ahora);

        return $total > 0 ? min(100, (int)(($transcurrido / $total) * 100)) : 0;
    }

    /**
     * Formatear valores monetarios de forma compacta
     */
    private function formatearMoneda(float $valor): string
    {
        if ($valor >= 1000000) {
            // Millones: 3.5M, 10M, etc.
            $millones = $valor / 1000000;
            if ($millones >= 10) {
                return '$' . number_format($millones, 0) . 'M';
            } else {
                return '$' . number_format($millones, 1, ',', '.') . 'M';
            }
        } elseif ($valor >= 1000) {
            // Miles: 500K, 999K, etc.
            $miles = $valor / 1000;
            return '$' . number_format($miles, 0) . 'K';
        } else {
            // Valores pequeños: formato normal
            return '$' . number_format($valor, 0, ',', '.');
        }
    }
}
