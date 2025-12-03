<?php

namespace App\Services\Dashboard;

use App\Repositories\DashboardRepository;

class AdminDashboardService
{
    public function __construct(
        private DashboardRepository $dashboardRepository
    ) {}

    /**
     * Obtener datos completos del dashboard administrador
     */
    public function getDashboardData(): array
    {
        return [
            'estadisticas' => $this->getEstadisticasGenerales(),
            'fondos_por_categoria' => $this->getFondosPorCategoria(),
            'inversiones_por_mes' => $this->getInversionesPorMes(),
            'solicitudes_pendientes' => $this->getSolicitudesPendientes(),
            'proyectos_por_estado' => $this->getProyectosPorEstado(),
            'ultimos_depositos' => $this->getUltimosDepositos(),
        ];
    }

    /**
     * Estadísticas generales de la plataforma
     */
    private function getEstadisticasGenerales(): array
    {
        $stats = $this->dashboardRepository->getEstadisticasGenerales();

        return [
            [
                'titulo' => 'Total Recaudado',
                'valor' => $this->formatearMoneda($stats['total_recaudado']),
                'icono' => 'fas fa-dollar-sign',
                'color' => 'success',
                'descripcion' => 'En toda la plataforma',
            ],
            [
                'titulo' => 'En Billeteras',
                'valor' => $this->formatearMoneda($stats['total_billeteras']),
                'icono' => 'fas fa-wallet',
                'color' => 'primary',
                'descripcion' => 'Saldo disponible de usuarios',
            ],
            [
                'titulo' => 'Proyectos Activos',
                'valor' => $stats['proyectos_activos'],
                'icono' => 'fas fa-seedling',
                'color' => 'warning',
                'descripcion' => 'En recaudación o ejecución',
            ],
            [
                'titulo' => 'Inversiones Activas',
                'valor' => $stats['inversiones_activas'],
                'icono' => 'fas fa-chart-line',
                'color' => 'info',
                'descripcion' => 'Inversiones en curso',
            ],
        ];
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

    /**
     * Fondos agrupados por categoría
     */
    private function getFondosPorCategoria(): array
    {
        $fondos = $this->dashboardRepository->getFondosPorCategoria();

        return [
            'labels' => $fondos->pluck('nombre')->toArray(),
            'data' => $fondos->pluck('total')->toArray(),
            'backgroundColor' => [
                '#28a745', // STAKING - verde
                '#007bff', // TRADING - azul
                '#ffc107', // EAR - amarillo
                '#17a2b8', // FUTUROS - cyan
                '#6f42c1', // CROSS_FUND - morado
            ],
        ];
    }

    /**
     * Inversiones por mes (últimos 12 meses)
     */
    private function getInversionesPorMes(): array
    {
        $inversiones = $this->dashboardRepository->getInversionesPorMes(12);

        // Preparar datos para Chart.js
        $labels = $inversiones->pluck('mes')->map(function ($mes) {
            return \Carbon\Carbon::createFromFormat('Y-m', $mes)->locale('es')->isoFormat('MMM YYYY');
        })->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Monto Invertido',
                    'data' => $inversiones->pluck('total')->toArray(),
                    'borderColor' => '#28a745',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Cantidad de Inversiones',
                    'data' => $inversiones->pluck('cantidad')->toArray(),
                    'borderColor' => '#007bff',
                    'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
        ];
    }

    /**
     * Solicitudes pendientes de aprobación
     */
    private function getSolicitudesPendientes(): array
    {
        return [
            'retiros' => [
                'cantidad' => $this->dashboardRepository->getRetirosPendientes()->count(),
                'items' => $this->dashboardRepository->getRetirosPendientes()->take(5),
            ],
            'kyc' => [
                'cantidad' => $this->dashboardRepository->getKycPendientes()->count(),
                'items' => $this->dashboardRepository->getKycPendientes()->take(5),
            ],
            'proyectos' => [
                'cantidad' => $this->dashboardRepository->getProyectosPendientes()->count(),
                'items' => $this->dashboardRepository->getProyectosPendientes()->take(5),
            ],
        ];
    }

    /**
     * Proyectos agrupados por estado
     */
    private function getProyectosPorEstado(): array
    {
        $proyectos = $this->dashboardRepository->getProyectosPorEstado();

        $labels = [
            'borrador' => 'Borrador',
            'en_revision' => 'En Revisión',
            'aprobado' => 'Aprobado',
            'en_recaudacion' => 'En Recaudación',
            'fondeado' => 'Fondeado',
            'en_ejecucion' => 'En Ejecución',
            'finalizado' => 'Finalizado',
            'cancelado' => 'Cancelado',
            'rechazado' => 'Rechazado',
        ];

        return $proyectos->map(function ($item) use ($labels) {
            return [
                'estado' => $labels[$item->estado] ?? $item->estado,
                'total' => $item->total,
                'porcentaje' => 0, // Se calculará en el frontend
            ];
        })->toArray();
    }

    /**
     * Últimos depósitos realizados
     */
    private function getUltimosDepositos(): array
    {
        return $this->dashboardRepository->getUltimosDepositos(10)
            ->map(function ($deposito) {
                return [
                    'usuario' => $deposito->user->name ?? 'N/A',
                    'monto' => '$' . number_format($deposito->monto, 0, ',', '.'),
                    'estado' => $deposito->estado,
                    'fecha' => $deposito->created_at->diffForHumans(),
                ];
            })->toArray();
    }
}
