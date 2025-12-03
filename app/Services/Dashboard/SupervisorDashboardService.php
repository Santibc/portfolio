<?php

namespace App\Services\Dashboard;

use App\Repositories\DashboardRepository;

class SupervisorDashboardService
{
    public function __construct(
        private DashboardRepository $dashboardRepository,
        private AdminDashboardService $adminDashboardService
    ) {}

    /**
     * Obtener datos completos del dashboard supervisor
     * Similar a Admin pero sin acciones de modificación
     */
    public function getDashboardData(): array
    {
        // Reutilizar la lógica del AdminDashboardService
        $adminData = $this->adminDashboardService->getDashboardData();

        return [
            'estadisticas' => $adminData['estadisticas'],
            'fondos_por_categoria' => $adminData['fondos_por_categoria'],
            'inversiones_por_mes' => $adminData['inversiones_por_mes'],
            'solicitudes_pendientes' => $adminData['solicitudes_pendientes'],
            'proyectos_por_estado' => $adminData['proyectos_por_estado'],
            'ultimos_depositos' => $adminData['ultimos_depositos'],
            'modo_lectura' => true, // Indicador para deshabilitar botones en la vista
        ];
    }
}
