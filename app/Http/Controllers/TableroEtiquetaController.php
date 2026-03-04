<?php

namespace App\Http\Controllers;

use App\Models\Tablero;
use App\Models\TableroEtiqueta;
use Illuminate\Http\Request;

class TableroEtiquetaController extends Controller
{
    public function index(Tablero $tablero)
    {
        return response()->json($tablero->etiquetas);
    }

    public function store(Request $request, Tablero $tablero)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'color' => 'required|string|max:7',
        ]);

        $etiqueta = $tablero->etiquetas()->create([
            'nombre' => $request->nombre,
            'color' => $request->color,
        ]);

        return response()->json(['success' => true, 'etiqueta' => $etiqueta]);
    }

    public function update(Request $request, TableroEtiqueta $etiqueta)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'color' => 'required|string|max:7',
        ]);

        $etiqueta->update($request->only('nombre', 'color'));
        return response()->json(['success' => true, 'etiqueta' => $etiqueta]);
    }

    public function destroy(TableroEtiqueta $etiqueta)
    {
        $etiqueta->tarjetas()->detach();
        $etiqueta->delete();
        return response()->json(['success' => true]);
    }
}
