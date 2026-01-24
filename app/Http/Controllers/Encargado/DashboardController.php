<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Services\EncargadoDashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected EncargadoDashboardService $dashboardService;

    public function __construct(EncargadoDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;

        // Middleware para establecer el ID del encargado en cada petición
        $this->middleware(function ($request, $next) {
            $this->dashboardService->setEncargadoId(auth()->id());
            return $next($request);
        });
    }

    /**
     * Vista principal del dashboard
     */
    public function index(Request $request)
    {
        // Datos iniciales para la vista
        $kpis = $this->dashboardService->getKpis();
        $opcionesFiltros = $this->dashboardService->getOpcionesFiltros();

        return view('encargado.dashboard.index', compact('kpis', 'opcionesFiltros'));
    }

    /**
     * API: KPIs del encargado
     */
    public function getKpis(): JsonResponse
    {
        $kpis = $this->dashboardService->getKpis();
        return response()->json($kpis);
    }

    /**
     * API: Mis obras asignadas
     */
    public function getMisObras(): JsonResponse
    {
        $obras = $this->dashboardService->getMisObras();
        return response()->json($obras);
    }

    /**
     * API: Producción diaria
     */
    public function getProduccionDiaria(): JsonResponse
    {
        $produccion = $this->dashboardService->getProduccionDiaria();
        return response()->json($produccion);
    }

    /**
     * API: Horas por trabajador
     */
    public function getHorasTrabajadores(): JsonResponse
    {
        $horas = $this->dashboardService->getHorasTrabajadores();
        return response()->json($horas);
    }

    /**
     * API: Maquinaria asignada
     */
    public function getMaquinariaAsignada(): JsonResponse
    {
        $maquinaria = $this->dashboardService->getMaquinariaAsignada();
        return response()->json($maquinaria);
    }

    /**
     * API: Calendario semanal
     */
    public function getCalendarioSemanal(Request $request): JsonResponse
    {
        $fechaInicio = $request->input('fecha_inicio')
            ? Carbon::parse($request->input('fecha_inicio'))
            : null;

        $calendario = $this->dashboardService->getCalendarioSemanal($fechaInicio);
        return response()->json($calendario);
    }

    /**
     * API: Partes pendientes
     */
    public function getPartesPendientes(Request $request): JsonResponse
    {
        $limite = $request->integer('limite', 10);
        $partes = $this->dashboardService->getPartesPendientes($limite);
        return response()->json($partes);
    }

    /**
     * API: Alertas del encargado
     */
    public function getAlertas(Request $request): JsonResponse
    {
        $limite = $request->integer('limite', 10);
        $alertas = $this->dashboardService->getAlertasEncargado($limite);
        return response()->json($alertas);
    }
}
