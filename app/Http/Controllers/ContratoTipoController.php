<?php

namespace App\Http\Controllers;

use App\Models\ContratoTipo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ContratoTipoController extends Controller
{
    /**
     * Mostrar listado de tipos de contrato.
     */
    public function index(): View
    {
        $tipos = ContratoTipo::withCount('contratos')
            ->orderBy('nombre')
            ->get();

        $stats = [
            'total' => $tipos->count(),
            'contratos_total' => $tipos->sum('contratos_count'),
        ];

        return view('contrato-tipos.index', compact('tipos', 'stats'));
    }

    /**
     * Guardar nuevo tipo de contrato (AJAX).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:contrato_tipos,nombre',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un tipo de contrato con este nombre.',
            'nombre.max' => 'El nombre no puede superar 100 caracteres.',
        ]);

        try {
            $tipo = ContratoTipo::create([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de contrato creado correctamente.',
                'tipo' => $tipo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar tipo de contrato (AJAX).
     */
    public function update(Request $request, ContratoTipo $tipo): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:contrato_tipos,nombre,' . $tipo->id,
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un tipo de contrato con este nombre.',
            'nombre.max' => 'El nombre no puede superar 100 caracteres.',
        ]);

        try {
            $tipo->update([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de contrato actualizado correctamente.',
                'tipo' => $tipo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar tipo de contrato (AJAX).
     */
    public function destroy(ContratoTipo $tipo): JsonResponse
    {
        // Verificar si tiene contratos asociados
        if ($tipo->contratos()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar porque tiene ' . $tipo->contratos()->count() . ' contrato(s) asociado(s).',
            ], 422);
        }

        try {
            $tipo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de contrato eliminado correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo: ' . $e->getMessage(),
            ], 500);
        }
    }
}
