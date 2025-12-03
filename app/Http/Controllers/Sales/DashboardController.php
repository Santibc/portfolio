<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\SalesDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private SalesDashboardService $dashboardService
    ) {}

    /**
     * Mostrar el dashboard del vendedor
     */
    public function index(Request $request)
    {
        $data = $this->dashboardService->getDashboardData($request->user());

        return view('sales.dashboard', $data);
    }
}
