<?php

namespace App\Http\Controllers;

use App\Models\GastoCategoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class GastoCategoriaController extends Controller
{
    /**
     * Mostrar listado de categorías de gastos.
     */
    public function index(): View
    {
        $categorias = GastoCategoria::withCount('gastos')
            ->orderBy('tipo')
            ->orderBy('nombre')
            ->get();

        $stats = [
            'total' => $categorias->count(),
            'directos' => $categorias->where('tipo', 'directo')->count(),
            'indirectos' => $categorias->where('tipo', 'indirecto')->count(),
        ];

        return view('gasto-categorias.index', compact('categorias', 'stats'));
    }

    /**
     * Crear nueva categoría (AJAX).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:gasto_categorias,nombre',
            'codigo' => 'nullable|string|max:20|unique:gasto_categorias,codigo',
            'tipo' => 'required|in:directo,indirecto',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con este nombre.',
            'codigo.unique' => 'Ya existe una categoría con este código.',
            'tipo.required' => 'El tipo es obligatorio.',
            'tipo.in' => 'El tipo debe ser directo o indirecto.',
        ]);

        try {
            $categoria = GastoCategoria::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada correctamente.',
                'categoria' => $categoria,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar categoría existente (AJAX).
     */
    public function update(Request $request, GastoCategoria $categoria): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:gasto_categorias,nombre,' . $categoria->id,
            'codigo' => 'nullable|string|max:20|unique:gasto_categorias,codigo,' . $categoria->id,
            'tipo' => 'required|in:directo,indirecto',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con este nombre.',
            'codigo.unique' => 'Ya existe una categoría con este código.',
            'tipo.required' => 'El tipo es obligatorio.',
            'tipo.in' => 'El tipo debe ser directo o indirecto.',
        ]);

        try {
            $categoria->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada correctamente.',
                'categoria' => $categoria,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar categoría (AJAX).
     */
    public function destroy(GastoCategoria $categoria): JsonResponse
    {
        // Verificar si tiene gastos asociados
        if ($categoria->gastos()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la categoría porque tiene gastos asociados.',
            ], 422);
        }

        try {
            $categoria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría: ' . $e->getMessage(),
            ], 500);
        }
    }
}
