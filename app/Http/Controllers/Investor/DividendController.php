<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Dividendo;
use App\Models\Proyecto;
use App\Services\Dividend\DividendCalculatorService;
use Illuminate\Http\Request;

class DividendController extends Controller
{
    public function __construct(
        private DividendCalculatorService $calculatorService
    ) {}

    /**
     * Dashboard de dividendos del inversionista
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Resumen de dividendos
        $summary = $this->calculatorService->getDividendSummary($user);

        // Filtros
        $filters = [
            'estado' => $request->get('estado'),
            'proyecto_id' => $request->get('proyecto_id'),
            'fecha_desde' => $request->get('fecha_desde'),
            'fecha_hasta' => $request->get('fecha_hasta'),
            'orden' => $request->get('orden', 'fecha_programada'),
            'direccion' => $request->get('direccion', 'asc'),
        ];

        // Dividendos paginados
        $dividendos = $this->calculatorService->getUserDividends($user, $filters);

        // Proyectos del usuario para filtro
        $proyectos = Proyecto::whereHas('inversiones', function ($q) use ($user) {
            $q->where('usuario_id', $user->id);
        })->select('id', 'nombre', 'codigo')->get();

        // Estados para filtro
        $estados = [
            'programado' => 'Programados',
            'pagado' => 'Pagados',
            'atrasado' => 'Atrasados',
            'cancelado' => 'Cancelados',
        ];

        return view('investor.dividends.index', compact(
            'summary',
            'dividendos',
            'proyectos',
            'estados',
            'filters'
        ));
    }

    /**
     * Historial completo de dividendos
     */
    public function history(Request $request)
    {
        $user = auth()->user();

        // Solo dividendos pagados
        $filters = [
            'estado' => 'pagado',
            'proyecto_id' => $request->get('proyecto_id'),
            'fecha_desde' => $request->get('fecha_desde'),
            'fecha_hasta' => $request->get('fecha_hasta'),
            'orden' => 'fecha_pagada',
            'direccion' => 'desc',
        ];

        $dividendos = $this->calculatorService->getUserDividends($user, $filters);

        // Estadísticas de historial
        $totalHistorico = Dividendo::where('usuario_id', $user->id)
            ->pagados()
            ->sum('monto');

        $totalAnual = Dividendo::where('usuario_id', $user->id)
            ->pagados()
            ->whereYear('fecha_pagada', now()->year)
            ->sum('monto');

        $promedioMensual = Dividendo::where('usuario_id', $user->id)
            ->pagados()
            ->whereYear('fecha_pagada', now()->year)
            ->selectRaw('AVG(monto) as promedio')
            ->value('promedio') ?? 0;

        // Proyectos del usuario para filtro
        $proyectos = Proyecto::whereHas('inversiones', function ($q) use ($user) {
            $q->where('usuario_id', $user->id);
        })->select('id', 'nombre', 'codigo')->get();

        return view('investor.dividends.history', compact(
            'dividendos',
            'proyectos',
            'totalHistorico',
            'totalAnual',
            'promedioMensual',
            'filters'
        ));
    }
}
