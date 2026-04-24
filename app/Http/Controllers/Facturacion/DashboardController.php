<?php

namespace App\Http\Controllers\Facturacion;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $mesActual = Carbon::now()->startOfMonth();

        $emitidasMes = Factura::query()
            ->whereIn('estado', ['emitida', 'enviada', 'pagada'])
            ->where('fecha', '>=', $mesActual)
            ->count();

        $pendientes = Factura::query()->where('estado', 'borrador')->count();

        $porMoneda = Factura::query()
            ->selectRaw('moneda_id, SUM(total) as total_moneda, SUM(total_cop) as total_cop_equiv')
            ->whereIn('estado', ['emitida', 'enviada', 'pagada'])
            ->where('fecha', '>=', $mesActual)
            ->groupBy('moneda_id')
            ->with('moneda')
            ->get();

        $ultimas = Factura::query()
            ->with(['cliente', 'moneda'])
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('facturacion.dashboard.index', [
            'stats' => [
                'emitidas_mes' => $emitidasMes,
                'pendientes' => $pendientes,
                'total_facturas' => Factura::count(),
            ],
            'porMoneda' => $porMoneda,
            'ultimas' => $ultimas,
        ]);
    }
}
