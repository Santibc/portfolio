<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\SupervisorDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private SupervisorDashboardService $dashboardService
    ) {}

    /**
     * Mostrar el dashboard del supervisor
     */
    public function index()
    {
        $data = $this->dashboardService->getDashboardData();

        return view('supervisor.dashboard', $data);
    }
}
