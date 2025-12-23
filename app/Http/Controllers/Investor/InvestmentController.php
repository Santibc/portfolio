<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\StoreInvestmentRequest;
use App\Models\Proyecto;
use App\Models\Inversion;
use App\Services\Investment\InvestmentService;
use App\Services\Contract\ContractService;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function __construct(
        private InvestmentService $investmentService,
        private ContractService $contractService,
        private WalletService $walletService
    ) {}

    /**
     * Mostrar listado de inversiones del usuario
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $filters = $request->only(['estado', 'proyecto_id', 'fecha_desde', 'fecha_hasta']);
        $investments = $this->investmentService->getUserInvestments($user, $filters);
        $summary = $this->investmentService->getInvestmentSummary($user);

        // Obtener proyectos para filtro
        $proyectosConInversiones = Proyecto::whereHas('inversiones', function ($q) use ($user) {
            $q->where('usuario_id', $user->id);
        })->get();

        return view('investor.investments.index', compact(
            'investments',
            'summary',
            'filters',
            'proyectosConInversiones'
        ));
    }

    /**
     * Mostrar formulario para invertir en un proyecto
     */
    public function create(Proyecto $proyecto)
    {
        $user = auth()->user();

        // Validar que el proyecto esté en recaudación
        if ($proyecto->estado !== 'en_recaudacion') {
            return redirect()->back()
                ->with('error', 'Este proyecto no está disponible para inversión en este momento.');
        }

        // Obtener billetera del usuario
        $billetera = $this->walletService->getOrCreateWallet($user);

        // Calcular monto restante del proyecto
        $montoRestante = $proyecto->monto_objetivo - $proyecto->monto_recaudado;

        // Calcular retornos estimados con monto mínimo (para referencia inicial)
        $estimacionInicial = $this->investmentService->calculateEstimatedReturns(
            $proyecto,
            $proyecto->inversion_minima
        );

        return view('investor.investments.create', compact(
            'proyecto',
            'billetera',
            'montoRestante',
            'estimacionInicial'
        ));
    }

    /**
     * Mostrar vista previa del contrato
     */
    public function showContract(Request $request, Proyecto $proyecto)
    {
        $user = auth()->user();

        // Validar monto
        $request->validate([
            'monto' => [
                'required',
                'numeric',
                'min:' . $proyecto->inversion_minima,
                'max:' . min($proyecto->inversion_maxima, $proyecto->monto_objetivo - $proyecto->monto_recaudado),
            ],
        ]);

        $monto = floatval($request->input('monto'));

        // Validar si puede invertir
        $canInvest = $this->investmentService->canInvest($user, $proyecto, $monto);
        if (!$canInvest['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $canInvest['message']);
        }

        // Obtener o crear plantilla de contrato
        $plantilla = $this->contractService->getOrCreateDefaultTemplate($proyecto->categoria);

        // Generar contenido del contrato
        $contenidoContrato = $this->contractService->generateContract(
            $plantilla,
            $user,
            $proyecto,
            $monto
        );

        // Calcular estimaciones
        $estimaciones = $this->investmentService->calculateEstimatedReturns($proyecto, $monto);

        return view('investor.investments.contract', compact(
            'proyecto',
            'plantilla',
            'contenidoContrato',
            'monto',
            'estimaciones'
        ));
    }

    /**
     * Procesar la inversión
     */
    public function store(StoreInvestmentRequest $request, Proyecto $proyecto)
    {
        $user = auth()->user();
        $monto = floatval($request->input('monto'));

        // Validar nuevamente si puede invertir (por seguridad)
        $canInvest = $this->investmentService->canInvest($user, $proyecto, $monto);
        if (!$canInvest['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $canInvest['message']);
        }

        try {
            // Crear inversión (incluye pago y activación)
            $inversion = $this->investmentService->createInvestment($user, $proyecto, $monto);

            // Obtener plantilla y generar contrato
            $plantilla = $this->contractService->getOrCreateDefaultTemplate($proyecto->categoria);
            $contenidoContrato = $this->contractService->generateContract(
                $plantilla,
                $user,
                $proyecto,
                $monto
            );

            // Registrar aceptación del contrato
            $this->contractService->acceptContract(
                $inversion,
                $plantilla,
                $contenidoContrato,
                $request->input('firma_digital'),
                $request->ip(),
                $request->userAgent()
            );

            return redirect()
                ->route('inversionista.investments.show', $inversion)
                ->with('success', '¡Inversión realizada exitosamente! Tu código de inversión es: ' . $inversion->codigo_inversion);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al procesar la inversión: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de una inversión
     */
    public function show(Inversion $inversion)
    {
        $user = auth()->user();

        // Verificar que la inversión pertenece al usuario
        if ($inversion->usuario_id !== $user->id) {
            abort(403, 'No tienes permiso para ver esta inversión.');
        }

        // Cargar relaciones
        $inversion->load([
            'proyecto',
            'proyecto.categoria',
            'proyecto.imagenes',
            'dividendos',
            'aceptacionContrato',
            'contrato',
        ]);

        // Calcular estimaciones actuales
        $estimaciones = $this->investmentService->calculateEstimatedReturns(
            $inversion->proyecto,
            $inversion->monto_invertido
        );

        return view('investor.investments.show', compact('inversion', 'estimaciones'));
    }

    /**
     * API: Calcular retornos estimados (para AJAX)
     */
    public function calculateReturns(Request $request, Proyecto $proyecto)
    {
        $monto = floatval($request->input('monto', $proyecto->inversion_minima));

        $estimaciones = $this->investmentService->calculateEstimatedReturns($proyecto, $monto);

        return response()->json($estimaciones);
    }
}
