<?php

namespace App\Http\Controllers\Recepcion;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class PanelController extends Controller
{
    public function __invoke(DashboardService $dashboardService)
    {
        $stats = $dashboardService->getRecepcionStats();

        return view('recepcion.panel', compact('stats'));
    }
}
