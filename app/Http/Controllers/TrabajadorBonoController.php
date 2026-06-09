<?php

namespace App\Http\Controllers;

use App\Models\TrabajadorBono;
use App\Models\Trabajador;
use App\Models\PrimaTrabajador;
use App\Models\TipoHora;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrabajadorBonoController extends Controller
{
    /**
     * Exportar el listado de bonos a Excel (respetando filtros).
     */
    public function exportExcel(Request $request)
    {
        $query = TrabajadorBono::with(['trabajador', 'obra']);
        if ($request->filled('trabajador_id')) $query->where('trabajador_id', $request->trabajador_id);
        if ($request->filled('obra_id')) $query->where('obra_id', $request->obra_id);
        if ($request->filled('tipo')) $query->where('tipo', $request->tipo);
        if ($request->filled('pagado')) $query->where('pagado', $request->pagado === 'si');
        if ($request->filled('fecha_desde')) $query->whereDate('fecha', '>=', $request->fecha_desde);
        if ($request->filled('fecha_hasta')) $query->whereDate('fecha', '<=', $request->fecha_hasta);
        $items = $query->orderByDesc('fecha')->get();

        $rows = $items->map(fn($b) => [
            optional($b->fecha)->format('d/m/Y'),
            $b->trabajador ? ($b->trabajador->apellidos . ', ' . $b->trabajador->nombre) : '-',
            $b->obra?->codigo ?? '-',
            $b->tipo_formateado,
            $b->concepto,
            (float) $b->importe,
            $b->pagado ? 'Pagado' : 'Pendiente',
        ])->toArray();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ListadoExport(['Fecha', 'Trabajador', 'Obra', 'Tipo', 'Concepto', 'Importe', 'Estado'], $rows),
            'bonos_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

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

        $tiposHora = TipoHora::activos()->orderBy('nombre')->get();

        // Pre-select trabajador if provided
        $trabajadorId = $request->query('trabajador_id');
        $obraId = $request->query('obra_id');

        return view('trabajadores.bonos.create', compact('trabajadores', 'obras', 'tiposHora', 'trabajadorId', 'obraId'));
    }

    /**
     * Store a newly created bono.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trabajadores' => 'required|array|min:1',
            'trabajadores.*' => 'exists:trabajadores,id',
            'obra_id' => 'nullable|exists:obras,id',
            'tipo' => 'required|in:prima_produccion,bono_especial,plus_nocturnidad,horas,otro',
            'tipo_hora_id' => 'nullable|exists:tipo_horas,id',
            'concepto' => 'required|string|max:255',
            'fecha' => 'required|date',
            'importe' => 'required|numeric|min:0',
            'horas' => 'nullable|numeric|min:0|max:999.99',
            'pagado' => 'nullable|boolean',
            'fecha_pago' => 'nullable|required_if:pagado,true|date',
            'notas' => 'nullable|string',
        ], [
            'trabajadores.required' => 'Selecciona al menos un trabajador.',
        ]);

        DB::beginTransaction();
        try {
            $pagado = $request->boolean('pagado');
            $fechaPago = $pagado ? ($validated['fecha_pago'] ?? now()) : null;
            // El tipo de hora solo aplica a bonos de tipo "horas"
            $tipoHoraId = $validated['tipo'] === 'horas' ? ($validated['tipo_hora_id'] ?? null) : null;

            // Crear el mismo bono para cada trabajador seleccionado
            foreach ($validated['trabajadores'] as $trabajadorId) {
                TrabajadorBono::create([
                    'trabajador_id' => $trabajadorId,
                    'obra_id' => $validated['obra_id'] ?? null,
                    'tipo' => $validated['tipo'],
                    'tipo_hora_id' => $tipoHoraId,
                    'concepto' => $validated['concepto'],
                    'fecha' => $validated['fecha'],
                    'importe' => $validated['importe'],
                    'horas' => $validated['horas'] ?? null,
                    'pagado' => $pagado,
                    'fecha_pago' => $fechaPago,
                    'notas' => $validated['notas'] ?? null,
                    'registrado_por' => Auth::id(),
                ]);
            }

            DB::commit();

            $n = count($validated['trabajadores']);
            return redirect()
                ->route('trabajadores.bonos.index')
                ->with('success', $n > 1 ? "Bono registrado para {$n} trabajadores." : 'Bono registrado exitosamente.');

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
     * Resumen de deuda (bonos + primas pendientes de pago) por trabajador.
     */
    public function deuda()
    {
        $trabajadores = Trabajador::where('activo', true)
            ->orderBy('apellidos')->orderBy('nombre')
            ->get()
            ->map(function ($t) {
                $bonosPend = (float) $t->bonos()->pendientesPago()->sum('importe');
                $primasPend = (float) PrimaTrabajador::where('trabajador_id', $t->id)
                    ->where('pagada', false)->sum('importe_prima');
                $t->deuda_bonos = $bonosPend;
                $t->deuda_primas = $primasPend;
                $t->deuda_total = $bonosPend + $primasPend;
                return $t;
            })
            ->filter(fn($t) => $t->deuda_total > 0)
            ->sortByDesc('deuda_total')
            ->values();

        $totalDeuda = $trabajadores->sum('deuda_total');

        return view('trabajadores.bonos.deuda', compact('trabajadores', 'totalDeuda'));
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
