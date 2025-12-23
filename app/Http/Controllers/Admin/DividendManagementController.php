<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dividendo;
use App\Models\Proyecto;
use App\Models\User;
use App\Services\Dividend\DividendPaymentService;
use Illuminate\Http\Request;

class DividendManagementController extends Controller
{
    public function __construct(
        private DividendPaymentService $paymentService
    ) {}

    /**
     * Panel de gestión de dividendos
     */
    public function index(Request $request)
    {
        // Estadísticas
        $stats = $this->paymentService->getAdminStats();

        // Filtros
        $filters = [
            'estado' => $request->get('estado'),
            'proyecto_id' => $request->get('proyecto_id'),
            'usuario_id' => $request->get('usuario_id'),
            'fecha_desde' => $request->get('fecha_desde'),
            'fecha_hasta' => $request->get('fecha_hasta'),
        ];

        // Query base
        $query = Dividendo::with(['usuario', 'proyecto', 'inversion']);

        // Aplicar filtros
        if ($filters['estado']) {
            $query->where('estado', $filters['estado']);
        }

        if ($filters['proyecto_id']) {
            $query->where('proyecto_id', $filters['proyecto_id']);
        }

        if ($filters['usuario_id']) {
            $query->where('usuario_id', $filters['usuario_id']);
        }

        if ($filters['fecha_desde']) {
            $query->where('fecha_programada', '>=', $filters['fecha_desde']);
        }

        if ($filters['fecha_hasta']) {
            $query->where('fecha_programada', '<=', $filters['fecha_hasta']);
        }

        // Ordenar por fecha programada descendente (sin paginar para DataTables)
        $dividendos = $query->orderBy('fecha_programada', 'desc')->get();

        // Proyectos para filtro
        $proyectos = Proyecto::select('id', 'nombre', 'codigo')->get();

        // Estados para filtro
        $estados = [
            'programado' => 'Programados',
            'pagado' => 'Pagados',
            'atrasado' => 'Atrasados',
            'cancelado' => 'Cancelados',
        ];

        return view('admin.dividends.index', compact(
            'stats',
            'dividendos',
            'proyectos',
            'estados',
            'filters'
        ));
    }

    /**
     * Pagar dividendo manualmente
     */
    public function pay(Dividendo $dividendo)
    {
        try {
            // Validar que el dividendo pueda ser pagado
            if ($dividendo->estado === 'pagado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este dividendo ya fue pagado'
                ], 400);
            }

            if ($dividendo->estado === 'cancelado') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede pagar un dividendo cancelado'
                ], 400);
            }

            // Pagar el dividendo
            $dividendo = $this->paymentService->payDividend($dividendo, auth()->user());

            return response()->json([
                'success' => true,
                'message' => 'Dividendo pagado exitosamente',
                'data' => [
                    'codigo' => $dividendo->codigo_dividendo,
                    'monto' => '$' . number_format($dividendo->monto, 0, ',', '.'),
                    'usuario' => $dividendo->usuario->name,
                    'fecha_pagada' => $dividendo->fecha_pagada->format('d/m/Y H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al pagar dividendo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar todos los dividendos pendientes
     */
    public function processAll()
    {
        try {
            // Marcar atrasados primero
            $atrasados = $this->paymentService->markOverdueDividends();

            // Procesar todos los pendientes de hoy
            $result = $this->paymentService->processAllDueDividends();

            $message = "Procesamiento completado. ";
            $message .= "Pagados: {$result['paid']}. ";

            if ($result['failed'] > 0) {
                $message .= "Fallidos: {$result['failed']}. ";
            }

            if ($atrasados > 0) {
                $message .= "Marcados como atrasados: {$atrasados}.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'paid' => $result['paid'],
                    'failed' => $result['failed'],
                    'overdue_marked' => $atrasados,
                    'errors' => $result['errors'] ?? [],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar dividendos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar dividendos como atrasados
     */
    public function markOverdue()
    {
        try {
            $count = $this->paymentService->markOverdueDividends();

            return response()->json([
                'success' => true,
                'message' => $count > 0
                    ? "Se marcaron {$count} dividendos como atrasados"
                    : "No hay dividendos para marcar como atrasados",
                'data' => ['count' => $count]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver detalle de dividendo
     */
    public function show(Dividendo $dividendo)
    {
        $dividendo->load(['usuario', 'proyecto', 'inversion', 'pagadoPor']);

        return view('admin.dividends.show', compact('dividendo'));
    }

    /**
     * Cancelar dividendo
     */
    public function cancel(Request $request, Dividendo $dividendo)
    {
        try {
            if ($dividendo->estado === 'pagado') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cancelar un dividendo ya pagado'
                ], 400);
            }

            if ($dividendo->estado === 'cancelado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este dividendo ya está cancelado'
                ], 400);
            }

            $dividendo->update([
                'estado' => 'cancelado',
                'notas' => $request->get('motivo', 'Cancelado por administrador'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dividendo cancelado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
