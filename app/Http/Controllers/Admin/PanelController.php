<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class PanelController extends Controller
{
    public function __invoke(DashboardService $dashboardService)
    {
        $stats = $dashboardService->getAdminStats();

        return view('admin.panel', compact('stats'));
    }
}
