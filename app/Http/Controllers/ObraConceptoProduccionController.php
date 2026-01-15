<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\ObraConceptoProduccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObraConceptoProduccionController extends Controller
{
    /**
     * Store a newly created concept for an obra.
     */
    public function store(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|in:desbroce,limpieza,herbicida,tala,poda,otro',
            'unidad' => 'required|in:m2,unidades,hectareas,jornal',
            'precio_unitario' => 'required|numeric|min:0',
            'activo' => 'boolean',
            'orden' => 'nullable|integer|min:0',
        ]);

        // Check if codigo already exists for this obra
        $exists = $obra->conceptosProduccion()
            ->where('codigo', $validated['codigo'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un concepto con este código en esta obra.'
            ], 422);
        }

        $concepto = $obra->conceptosProduccion()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Concepto de producción creado exitosamente.',
            'concepto' => $concepto
        ]);
    }

    /**
     * Update the specified concept.
     */
    public function update(Request $request, Obra $obra, ObraConceptoProduccion $concepto)
    {
        // Verify concept belongs to this obra
        if ($concepto->obra_id !== $obra->id) {
            return response()->json([
                'success' => false,
                'message' => 'El concepto no pertenece a esta obra.'
            ], 403);
        }

        $validated = $request->validate([
            'codigo' => 'required|string|max:20',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|in:desbroce,limpieza,herbicida,tala,poda,otro',
            'unidad' => 'required|in:m2,unidades,hectareas,jornal',
            'precio_unitario' => 'required|numeric|min:0',
            'activo' => 'boolean',
            'orden' => 'nullable|integer|min:0',
        ]);

        // Check if codigo already exists for this obra (excluding current concept)
        $exists = $obra->conceptosProduccion()
            ->where('codigo', $validated['codigo'])
            ->where('id', '!=', $concepto->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un concepto con este código en esta obra.'
            ], 422);
        }

        $concepto->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Concepto actualizado exitosamente.',
            'concepto' => $concepto
        ]);
    }

    /**
     * Remove the specified concept (soft delete by marking as inactive).
     */
    public function destroy(Obra $obra, ObraConceptoProduccion $concepto)
    {
        // Verify concept belongs to this obra
        if ($concepto->obra_id !== $obra->id) {
            return response()->json([
                'success' => false,
                'message' => 'El concepto no pertenece a esta obra.'
            ], 403);
        }

        // Check if concept has productions
        $hasProductions = $concepto->producciones()->exists();

        if ($hasProductions) {
            // Soft delete: mark as inactive instead of deleting
            $concepto->update(['activo' => false]);

            return response()->json([
                'success' => true,
                'message' => 'El concepto tiene producciones registradas y se marcó como inactivo.',
                'soft_deleted' => true
            ]);
        }

        // Hard delete if no productions
        $concepto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Concepto eliminado exitosamente.',
            'soft_deleted' => false
        ]);
    }

    /**
     * Duplicate concepts from another obra.
     */
    public function duplicate(Request $request, Obra $obra, Obra $obraOrigen)
    {
        $validated = $request->validate([
            'sobrescribir' => 'nullable|boolean',
        ]);

        $sobrescribir = $validated['sobrescribir'] ?? false;

        DB::beginTransaction();
        try {
            // Get concepts from source obra
            $conceptosOrigen = $obraOrigen->conceptosProduccion()
                ->where('activo', true)
                ->orderBy('orden')
                ->get();

            if ($conceptosOrigen->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La obra origen no tiene conceptos de producción activos.'
                ], 422);
            }

            // Delete existing concepts if sobrescribir is true
            if ($sobrescribir) {
                $obra->conceptosProduccion()->delete();
            }

            $duplicados = 0;
            $omitidos = 0;

            foreach ($conceptosOrigen as $conceptoOrigen) {
                // Check if codigo already exists
                $exists = $obra->conceptosProduccion()
                    ->where('codigo', $conceptoOrigen->codigo)
                    ->exists();

                if ($exists && !$sobrescribir) {
                    $omitidos++;
                    continue;
                }

                // Create new concept
                $obra->conceptosProduccion()->create([
                    'codigo' => $conceptoOrigen->codigo,
                    'nombre' => $conceptoOrigen->nombre,
                    'descripcion' => $conceptoOrigen->descripcion,
                    'categoria' => $conceptoOrigen->categoria,
                    'unidad' => $conceptoOrigen->unidad,
                    'precio_unitario' => $conceptoOrigen->precio_unitario,
                    'activo' => true,
                    'orden' => $conceptoOrigen->orden,
                ]);

                $duplicados++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Se duplicaron {$duplicados} conceptos exitosamente.",
                'duplicados' => $duplicados,
                'omitidos' => $omitidos
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al duplicar conceptos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get concepts for an obra (for AJAX requests).
     */
    public function index(Obra $obra)
    {
        $conceptos = $obra->conceptosProduccion()
            ->orderBy('orden')
            ->orderBy('codigo')
            ->get();

        return response()->json([
            'success' => true,
            'conceptos' => $conceptos
        ]);
    }
}
