<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\InvestorDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private InvestorDashboardService $dashboardService
    ) {}

    /**
     * Mostrar el dashboard del inversionista
     */
    public function index(Request $request)
    {
        $data = $this->dashboardService->getDashboardData($request->user());

        return view('investor.dashboard', $data);
    }
}
