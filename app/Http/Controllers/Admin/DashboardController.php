<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Vista principal del dashboard
     */
    public function index(Request $request)
    {
        // Obtener filtros de sesión o usar valores por defecto
        $filtros = $this->obtenerFiltros($request);

        // Datos iniciales
        $kpis = $this->dashboardService->getKpis(
            $filtros['fecha_inicio'] ? Carbon::parse($filtros['fecha_inicio']) : null,
            $filtros['fecha_fin'] ? Carbon::parse($filtros['fecha_fin']) : null
        );

        $opcionesFiltros = $this->dashboardService->getOpcionesFiltros();

        return view('admin.dashboard.index', compact('kpis', 'opcionesFiltros', 'filtros'));
    }

    /**
     * API: KPIs filtrados
     */
    public function getKpisFiltered(Request $request): JsonResponse
    {
        $filtros = $this->obtenerFiltros($request);

        $kpis = $this->dashboardService->getKpis(
            $filtros['fecha_inicio'] ? Carbon::parse($filtros['fecha_inicio']) : null,
            $filtros['fecha_fin'] ? Carbon::parse($filtros['fecha_fin']) : null
        );

        return response()->json($kpis);
    }

    /**
     * API: Rentabilidad mensual (gráfico de barras)
     */
    public function getRentabilidadMensual(Request $request): JsonResponse
    {
        $meses = $request->integer('meses', 12);
        $data = $this->dashboardService->getRentabilidadMensual($meses);

        return response()->json($data);
    }

    /**
     * API: Flujo de caja mensual
     */
    public function getFlujoCaja(Request $request): JsonResponse
    {
        $meses = $request->integer('meses', 12);
        $data = $this->dashboardService->getFlujoCajaMensual($meses);

        return response()->json($data);
    }

    /**
     * API: Rentabilidad por obra (top y bottom)
     */
    public function getRentabilidadObras(Request $request): JsonResponse
    {
        $top = $request->integer('top', 5);
        $bottom = $request->integer('bottom', 5);

        $filtros = [
            'cliente_id' => $request->input('cliente_id'),
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
        ];

        $data = $this->dashboardService->getRentabilidadPorObra($top, $bottom, $filtros);

        return response()->json($data);
    }

    /**
     * API: Rentabilidad por cuadrilla
     */
    public function getRentabilidadCuadrillas(Request $request): JsonResponse
    {
        $limite = $request->integer('limite', 10);
        $data = $this->dashboardService->getRentabilidadPorCuadrilla($limite);

        return response()->json($data);
    }

    /**
     * API: Cobros pendientes con aging
     */
    public function getCobrosPendientes(): JsonResponse
    {
        $data = $this->dashboardService->getCobrosPendientesConAging();

        return response()->json($data);
    }

    /**
     * API: Obras en riesgo (gastos > coste estimado)
     */
    public function getObrasRiesgo(Request $request): JsonResponse
    {
        $limite = $request->integer('limite', 10);
        $data = $this->dashboardService->getObrasEnRiesgo($limite);

        return response()->json($data);
    }

    /**
     * API: Producción del mes
     */
    public function getProduccion(Request $request): JsonResponse
    {
        $mes = $request->integer('mes') ?: null;
        $anio = $request->integer('anio') ?: null;

        $data = $this->dashboardService->getProduccionMes($mes, $anio);

        return response()->json($data);
    }

    /**
     * API: Alertas críticas
     */
    public function getAlertasCriticas(Request $request): JsonResponse
    {
        $limite = $request->integer('limite', 10);
        $data = $this->dashboardService->getAlertasCriticas($limite);

        return response()->json($data);
    }

    /**
     * Obtener filtros del request o valores por defecto
     */
    protected function obtenerFiltros(Request $request): array
    {
        return [
            'fecha_inicio' => $request->input('fecha_inicio', now()->startOfYear()->format('Y-m-d')),
            'fecha_fin' => $request->input('fecha_fin', now()->format('Y-m-d')),
            'obra_id' => $request->input('obra_id'),
            'cliente_id' => $request->input('cliente_id'),
        ];
    }
}
