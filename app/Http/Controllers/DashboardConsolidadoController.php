<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ConsolidadoContableService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardConsolidadoController extends Controller
{
    public function index(Request $request, ConsolidadoContableService $consolidado): View
    {
        $desde = $request->filled('desde')
            ? Carbon::parse((string) $request->input('desde'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $hasta = $request->filled('hasta')
            ? Carbon::parse((string) $request->input('hasta'))->endOfDay()
            : Carbon::now()->endOfMonth();

        $resumen = $consolidado->resumen($desde, $hasta);

        return view('consolidado.index', array_merge($resumen, [
            'desde' => $desde->toDateString(),
            'hasta' => $hasta->toDateString(),
        ]));
    }
}
