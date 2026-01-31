<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\ObraDiscrepanciaValoracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ObraDiscrepanciaController extends Controller
{
    /**
     * Display a listing of discrepancies for an obra.
     */
    public function index(Obra $obra)
    {
        $discrepancias = $obra->discrepancias()
            ->with('registrador')
            ->orderByDesc('periodo_mes')
            ->paginate(20);

        return view('obras.discrepancias.index', compact('obra', 'discrepancias'));
    }

    /**
     * Show the form for creating a new discrepancy.
     */
    public function create(Obra $obra, Request $request)
    {
        // Get periodo from query param or default to current month
        $periodo = $request->query('periodo', now()->format('Y-m'));

        // Calculate produced amount for the period
        $importeProducidoManzer = $obra->partesDiarios()
            ->whereYear('fecha', '=', substr($periodo, 0, 4))
            ->whereMonth('fecha', '=', substr($periodo, 5, 2))
            ->whereIn('estado', ['completado', 'validado'])
            ->sum('importe_total_calculado');

        return view('obras.discrepancias.create', compact('obra', 'periodo', 'importeProducidoManzer'));
    }

    /**
     * Store a newly created discrepancy.
     */
    public function store(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'periodo_mes' => 'required|date_format:Y-m',
            'importe_producido_manzer' => 'required|numeric|min:0',
            'importe_validado_cuadrilla' => 'nullable|numeric|min:0',
            'importe_aceptado_cliente' => 'nullable|numeric|min:0',
            'fecha_respuesta_cliente' => 'nullable|date',
            'notas' => 'nullable|string',
            'documento_valoracion' => 'nullable|file|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $data = $validated;
            $data['obra_id'] = $obra->id;
            $data['registrado_por'] = Auth::id();

            // Calculate pending amount
            $aceptado = $validated['importe_aceptado_cliente'] ?? 0;
            $data['importe_pendiente'] = $validated['importe_producido_manzer'] - $aceptado;

            // Determine status
            if ($aceptado == 0) {
                $data['estado'] = 'pendiente';
            } elseif ($aceptado < $validated['importe_producido_manzer']) {
                $data['estado'] = 'parcial';
            } else {
                $data['estado'] = 'resuelto';
                $data['fecha_resolucion'] = now();
            }

            // Handle file upload
            if ($request->hasFile('documento_valoracion')) {
                $file = $request->file('documento_valoracion');
                $filename = 'valoracion_' . $obra->id . '_' . $validated['periodo_mes'] . '_' . time() . '.' . $file->extension();
                $directory = public_path('uploads/obras/' . $obra->id . '/discrepancias');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                $file->move($directory, $filename);
                $data['documento_valoracion_path'] = 'uploads/obras/' . $obra->id . '/discrepancias/' . $filename;
            }

            $discrepancia = ObraDiscrepanciaValoracion::create($data);

            // Update obra accumulated amounts
            $obra->actualizarImportesAcumulados();

            DB::commit();

            return redirect()
                ->route('obras.discrepancias.index', $obra)
                ->with('success', 'Discrepancia registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al registrar discrepancia: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified discrepancy.
     */
    public function edit(Obra $obra, ObraDiscrepanciaValoracion $discrepancia)
    {
        // Verify discrepancy belongs to this obra
        if ($discrepancia->obra_id !== $obra->id) {
            abort(403, 'Esta discrepancia no pertenece a la obra especificada.');
        }

        return view('obras.discrepancias.edit', compact('obra', 'discrepancia'));
    }

    /**
     * Update the specified discrepancy.
     */
    public function update(Request $request, Obra $obra, ObraDiscrepanciaValoracion $discrepancia)
    {
        // Verify discrepancy belongs to this obra
        if ($discrepancia->obra_id !== $obra->id) {
            abort(403, 'Esta discrepancia no pertenece a la obra especificada.');
        }

        $validated = $request->validate([
            'importe_validado_cuadrilla' => 'nullable|numeric|min:0',
            'importe_aceptado_cliente' => 'nullable|numeric|min:0',
            'fecha_respuesta_cliente' => 'nullable|date',
            'notas' => 'nullable|string',
            'documento_valoracion' => 'nullable|file|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Recalculate pending amount
            $aceptado = $validated['importe_aceptado_cliente'] ?? 0;
            $validated['importe_pendiente'] = $discrepancia->importe_producido_manzer - $aceptado;

            // Update status
            if ($aceptado == 0) {
                $validated['estado'] = 'pendiente';
                $validated['fecha_resolucion'] = null;
            } elseif ($aceptado < $discrepancia->importe_producido_manzer) {
                $validated['estado'] = 'parcial';
                $validated['fecha_resolucion'] = null;
            } else {
                $validated['estado'] = 'resuelto';
                $validated['fecha_resolucion'] = $validated['fecha_resolucion'] ?? now();
            }

            // Handle file upload
            if ($request->hasFile('documento_valoracion')) {
                // Delete old file if exists
                if ($discrepancia->documento_valoracion_path && file_exists(public_path($discrepancia->documento_valoracion_path))) {
                    unlink(public_path($discrepancia->documento_valoracion_path));
                }

                $file = $request->file('documento_valoracion');
                $filename = 'valoracion_' . $obra->id . '_' . $discrepancia->periodo_mes . '_' . time() . '.' . $file->extension();
                $directory = public_path('uploads/obras/' . $obra->id . '/discrepancias');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                $file->move($directory, $filename);
                $validated['documento_valoracion_path'] = 'uploads/obras/' . $obra->id . '/discrepancias/' . $filename;
            }

            $discrepancia->update($validated);

            // Update obra accumulated amounts
            $obra->actualizarImportesAcumulados();

            DB::commit();

            return redirect()
                ->route('obras.discrepancias.index', $obra)
                ->with('success', 'Discrepancia actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar discrepancia: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark discrepancy as resolved.
     */
    public function marcarResuelto(Request $request, Obra $obra, ObraDiscrepanciaValoracion $discrepancia)
    {
        // Verify discrepancy belongs to this obra
        if ($discrepancia->obra_id !== $obra->id) {
            return response()->json([
                'success' => false,
                'message' => 'Esta discrepancia no pertenece a la obra especificada.'
            ], 403);
        }

        $validated = $request->validate([
            'fecha_resolucion' => 'nullable|date',
            'notas' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $discrepancia->update([
                'estado' => 'resuelto',
                'fecha_resolucion' => $validated['fecha_resolucion'] ?? now(),
                'notas' => $validated['notas'] ?? $discrepancia->notas,
            ]);

            // Update obra accumulated amounts
            $obra->actualizarImportesAcumulados();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Discrepancia marcada como resuelta.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al marcar como resuelta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the specified discrepancy.
     */
    public function show(Obra $obra, ObraDiscrepanciaValoracion $discrepancia)
    {
        // Verify discrepancy belongs to this obra
        if ($discrepancia->obra_id !== $obra->id) {
            abort(403, 'Esta discrepancia no pertenece a la obra especificada.');
        }

        $discrepancia->load('registrador');

        return view('obras.discrepancias.show', compact('obra', 'discrepancia'));
    }
}
