<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Repositories\DashboardRepository;

class FarmerDashboardService
{
    public function __construct(
        private DashboardRepository $dashboardRepository
    ) {}

    /**
     * Obtener datos completos del dashboard agricultor
     */
    public function getDashboardData(User $user): array
    {
        return [
            'estadisticas' => $this->getEstadisticasAgricultor($user),
            'proyectos' => $this->getProyectos($user),
            'proyectos_por_estado' => $this->getProyectosPorEstado($user),
            'recaudacion_mensual' => $this->getRecaudacionMensual($user),
        ];
    }

    /**
     * Estadísticas del agricultor
     */
    private function getEstadisticasAgricultor(User $user): array
    {
        $proyectos = $this->dashboardRepository->getProyectosAgricultor($user);
        $totalRecaudado = $this->dashboardRepository->getTotalRecaudadoAgricultor($user);
        $proyectosEnRecaudacion = $this->dashboardRepository->getProyectosEnRecaudacion($user);

        return [
            [
                'titulo' => 'Mis Proyectos',
                'valor' => $proyectos->count(),
                'icono' => 'fas fa-seedling',
                'color' => 'primary',
                'descripcion' => 'Total de proyectos creados',
            ],
            [
                'titulo' => 'Total Recaudado',
                'valor' => $this->formatearMoneda($totalRecaudado),
                'icono' => 'fas fa-dollar-sign',
                'color' => 'success',
                'descripcion' => 'En todos los proyectos',
            ],
            [
                'titulo' => 'En Recaudación',
                'valor' => $proyectosEnRecaudacion->count(),
                'icono' => 'fas fa-chart-line',
                'color' => 'warning',
                'descripcion' => 'Proyectos activos',
            ],
            [
                'titulo' => 'Inversionistas',
                'valor' => $this->getInversionistasUnicos($user),
                'icono' => 'fas fa-users',
                'color' => 'info',
                'descripcion' => 'Que confían en tus proyectos',
            ],
        ];
    }

    /**
     * Lista de proyectos del agricultor
     */
    private function getProyectos(User $user): array
    {
        $proyectos = $this->dashboardRepository->getProyectosAgricultor($user);

        return $proyectos->map(function ($proyecto) {
            $progreso = $proyecto->monto_objetivo > 0
                ? ($proyecto->monto_recaudado / $proyecto->monto_objetivo) * 100
                : 0;

            return [
                'id' => $proyecto->id,
                'nombre' => $proyecto->nombre,
                'categoria' => $proyecto->categoria->nombre,
                'estado' => $proyecto->estado,
                'monto_objetivo' => $proyecto->monto_objetivo,
                'monto_recaudado' => $proyecto->monto_recaudado,
                'progreso' => min(100, round($progreso)),
                'fecha_inicio' => $proyecto->fecha_inicio_recaudacion,
                'fecha_fin' => $proyecto->fecha_cierre_recaudacion,
            ];
        })->toArray();
    }

    /**
     * Proyectos agrupados por estado
     */
    private function getProyectosPorEstado(User $user): array
    {
        $proyectos = $this->dashboardRepository->getProyectosAgricultor($user);

        $porEstado = $proyectos->groupBy('estado')->map(function ($items) {
            return $items->count();
        });

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

        $data = [];
        foreach ($labels as $key => $label) {
            if (isset($porEstado[$key])) {
                $data[] = [
                    'estado' => $label,
                    'total' => $porEstado[$key],
                ];
            }
        }

        return $data;
    }

    /**
     * Recaudación mensual (últimos 12 meses)
     */
    private function getRecaudacionMensual(User $user): array
    {
        $proyectos = $this->dashboardRepository->getProyectosAgricultor($user);

        // Obtener inversiones en proyectos del agricultor
        $inversiones = \DB::table('inversiones')
            ->whereIn('proyecto_id', $proyectos->pluck('id'))
            ->select(
                \DB::raw('DATE_FORMAT(fecha_inversion, "%Y-%m") as mes'),
                \DB::raw('SUM(monto_invertido) as total')
            )
            ->where('fecha_inversion', '>=', now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        $labels = $inversiones->pluck('mes')->map(function ($mes) {
            return \Carbon\Carbon::createFromFormat('Y-m', $mes)->locale('es')->isoFormat('MMM YYYY');
        })->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Recaudación',
                    'data' => $inversiones->pluck('total')->toArray(),
                    'borderColor' => '#28a745',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    /**
     * Contar inversionistas únicos
     */
    private function getInversionistasUnicos(User $user): int
    {
        $proyectos = $this->dashboardRepository->getProyectosAgricultor($user);

        return \DB::table('inversiones')
            ->whereIn('proyecto_id', $proyectos->pluck('id'))
            ->distinct('usuario_id')
            ->count('usuario_id');
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
