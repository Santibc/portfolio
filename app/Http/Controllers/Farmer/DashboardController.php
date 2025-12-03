<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\FarmerDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private FarmerDashboardService $dashboardService
    ) {}

    /**
     * Mostrar el dashboard del agricultor
     */
    public function index(Request $request)
    {
        $data = $this->dashboardService->getDashboardData($request->user());

        return view('farmer.dashboard', $data);
    }
}
