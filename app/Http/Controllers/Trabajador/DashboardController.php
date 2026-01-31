<?php

namespace App\Http\Controllers\Trabajador;

use App\Http\Controllers\Controller;
use App\Models\Trabajador;
use App\Services\TrabajadorDashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected TrabajadorDashboardService $dashboardService;

    public function __construct(TrabajadorDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;

        // Middleware para establecer el trabajador desde el usuario autenticado
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $trabajador = Trabajador::where('user_id', $user->id)->first();

            if (!$trabajador) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Tu cuenta no tiene un perfil de trabajador asociado.'
                    ], 403);
                }
                return redirect()->route('dashboard')
                    ->with('error', 'Tu cuenta no tiene un perfil de trabajador asociado.');
            }

            $this->dashboardService->setTrabajadorId($trabajador->id);
            return $next($request);
        });
    }

    /**
     * Vista principal del portal del trabajador
     */
    public function index(Request $request)
    {
        $kpis = $this->dashboardService->getKpis();
        $trabajador = $this->dashboardService->getTrabajador();
        $fichajeAbierto = $this->dashboardService->getFichajeAbiertoHoy();

        // Parsear fechas desde request
        $fechaDesde = $request->filled('fecha_desde')
            ? Carbon::parse($request->fecha_desde)
            : null;
        $fechaHasta = $request->filled('fecha_hasta')
            ? Carbon::parse($request->fecha_hasta)
            : null;

        return view('trabajador.dashboard.index', compact('kpis', 'trabajador', 'fichajeAbierto', 'fechaDesde', 'fechaHasta'));
    }

    /**
     * API: KPIs del trabajador
     */
    public function getKpis(): JsonResponse
    {
        return response()->json($this->dashboardService->getKpis());
    }

    /**
     * API: Fichajes del mes
     */
    public function getMisFichajes(Request $request): JsonResponse
    {
        $mes = $request->integer('mes', now()->month);
        $anio = $request->integer('anio', now()->year);

        $fichajes = $this->dashboardService->getMisFichajesMes($mes, $anio);
        $resumen = $this->dashboardService->getResumenHorasMes($mes, $anio);
        $fichajeAbierto = $this->dashboardService->getFichajeAbiertoHoy();

        return response()->json([
            'fichajes' => $fichajes,
            'resumen' => $resumen,
            'fichaje_abierto' => $fichajeAbierto,
        ]);
    }

    /**
     * API: Datos de vacaciones
     */
    public function getMisVacaciones(): JsonResponse
    {
        return response()->json($this->dashboardService->getMisVacaciones());
    }

    /**
     * API: Documentos visibles
     */
    public function getMisDocumentos(): JsonResponse
    {
        return response()->json($this->dashboardService->getMisDocumentos());
    }

    /**
     * API: Registrar lectura de documento
     */
    public function confirmarLecturaDocumento(Request $request, int $documentoId): JsonResponse
    {
        $resultado = $this->dashboardService->registrarLecturaDocumento(
            $documentoId,
            $request->ip(),
            $request->userAgent()
        );

        return response()->json($resultado, $resultado['success'] ? 200 : 403);
    }

    /**
     * API: EPIs asignados
     */
    public function getMisEpis(): JsonResponse
    {
        return response()->json($this->dashboardService->getMisEpis());
    }

    /**
     * API: Formaciones
     */
    public function getMisFormaciones(): JsonResponse
    {
        return response()->json($this->dashboardService->getMisFormaciones());
    }

    /**
     * API: Primas y bonos
     */
    public function getMisPrimas(): JsonResponse
    {
        return response()->json($this->dashboardService->getMisPrimas());
    }

    /**
     * API: Alertas personales
     */
    public function getMisAlertas(Request $request): JsonResponse
    {
        $limite = $request->integer('limite', 10);
        return response()->json($this->dashboardService->getMisAlertas($limite));
    }

    /**
     * API: Producción diaria
     */
    public function getProduccionDiaria(Request $request): JsonResponse
    {
        $fechaDesde = $request->filled('fecha_desde')
            ? Carbon::parse($request->fecha_desde)
            : null;
        $fechaHasta = $request->filled('fecha_hasta')
            ? Carbon::parse($request->fecha_hasta)
            : null;

        $produccion = $this->dashboardService->getProduccionDiaria($fechaDesde, $fechaHasta);
        return response()->json($produccion);
    }
}
