<?php

namespace App\Http\Controllers;

use App\Models\TrabajadorBono;
use App\Models\Trabajador;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrabajadorBonoController extends Controller
{
    /**
     * Display a listing of bonos.
     */
    public function index(Request $request)
    {
        $query = TrabajadorBono::with(['trabajador', 'obra', 'registrador']);

        // Filters
        if ($request->filled('trabajador_id')) {
            $query->where('trabajador_id', $request->trabajador_id);
        }

        if ($request->filled('obra_id')) {
            $query->where('obra_id', $request->obra_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('pagado')) {
            $query->where('pagado', $request->pagado === 'si');
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Sort
        $sortBy = $request->get('sort', 'fecha');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $bonos = $query->paginate(25)->withQueryString();

        // For filters
        $trabajadores = Trabajador::where('activo', true)
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();

        $obras = Obra::activas()
            ->orderBy('nombre')
            ->get();

        // Summary
        $totalPendiente = TrabajadorBono::pendientesPago()->sum('importe');
        $totalPagado = TrabajadorBono::pagados()->sum('importe');

        return view('trabajadores.bonos.index', compact(
            'bonos',
            'trabajadores',
            'obras',
            'totalPendiente',
            'totalPagado'
        ));
    }

    /**
     * Show the form for creating a new bono.
     */
    public function create(Request $request)
    {
        $trabajadores = Trabajador::where('activo', true)
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();

        $obras = Obra::activas()
            ->orderBy('nombre')
            ->get();

        // Pre-select trabajador if provided
        $trabajadorId = $request->query('trabajador_id');
        $obraId = $request->query('obra_id');

        return view('trabajadores.bonos.create', compact('trabajadores', 'obras', 'trabajadorId', 'obraId'));
    }

    /**
     * Store a newly created bono.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'obra_id' => 'nullable|exists:obras,id',
            'tipo' => 'required|in:prima_produccion,bono_especial,plus_nocturnidad,horas,otro',
            'concepto' => 'required|string|max:255',
            'fecha' => 'required|date',
            'importe' => 'required|numeric|min:0',
            'horas' => 'nullable|numeric|min:0|max:999.99',
            'pagado' => 'nullable|boolean',
            'fecha_pago' => 'nullable|required_if:pagado,true|date',
            'notas' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $data = $validated;
            $data['registrado_por'] = Auth::id();
            $data['pagado'] = $validated['pagado'] ?? false;

            // If pagado is false, remove fecha_pago
            if (!$data['pagado']) {
                $data['fecha_pago'] = null;
            }

            $bono = TrabajadorBono::create($data);

            DB::commit();

            return redirect()
                ->route('trabajadores.bonos.index')
                ->with('success', 'Bono registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al registrar bono: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified bono.
     */
    public function edit(TrabajadorBono $bono)
    {
        $trabajadores = Trabajador::where('activo', true)
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();

        $obras = Obra::activas()
            ->orderBy('nombre')
            ->get();

        return view('trabajadores.bonos.edit', compact('bono', 'trabajadores', 'obras'));
    }

    /**
     * Update the specified bono.
     */
    public function update(Request $request, TrabajadorBono $bono)
    {
        $validated = $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'obra_id' => 'nullable|exists:obras,id',
            'tipo' => 'required|in:prima_produccion,bono_especial,plus_nocturnidad,horas,otro',
            'concepto' => 'required|string|max:255',
            'fecha' => 'required|date',
            'importe' => 'required|numeric|min:0',
            'horas' => 'nullable|numeric|min:0|max:999.99',
            'pagado' => 'nullable|boolean',
            'fecha_pago' => 'nullable|required_if:pagado,true|date',
            'notas' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $data = $validated;
            $data['pagado'] = $validated['pagado'] ?? false;

            // If pagado is false, remove fecha_pago
            if (!$data['pagado']) {
                $data['fecha_pago'] = null;
            }

            $bono->update($data);

            DB::commit();

            return redirect()
                ->route('trabajadores.bonos.index')
                ->with('success', 'Bono actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar bono: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified bono.
     */
    public function destroy(TrabajadorBono $bono)
    {
        try {
            $bono->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bono eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar bono: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark bono as paid.
     */
    public function marcarPagado(Request $request, TrabajadorBono $bono)
    {
        $validated = $request->validate([
            'fecha_pago' => 'nullable|date',
        ]);

        try {
            $fechaPago = $validated['fecha_pago'] ?? now();
            $bono->marcarPagado($fechaPago);

            return response()->json([
                'success' => true,
                'message' => 'Bono marcado como pagado.',
                'bono' => $bono->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar como pagado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark bono as pending.
     */
    public function marcarPendiente(TrabajadorBono $bono)
    {
        try {
            $bono->marcarPendiente();

            return response()->json([
                'success' => true,
                'message' => 'Bono marcado como pendiente.',
                'bono' => $bono->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar como pendiente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bonos for a specific trabajador (AJAX).
     */
    public function porTrabajador(Trabajador $trabajador)
    {
        $bonos = $trabajador->bonos()
            ->with('obra')
            ->orderByDesc('fecha')
            ->get();

        $totalPendiente = $trabajador->bonos()->pendientesPago()->sum('importe');
        $totalPagado = $trabajador->bonos()->pagados()->sum('importe');

        return response()->json([
            'success' => true,
            'bonos' => $bonos,
            'total_pendiente' => $totalPendiente,
            'total_pagado' => $totalPagado
        ]);
    }
}
