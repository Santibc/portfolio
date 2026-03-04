<?php

namespace App\Http\Controllers;

use App\Models\Tablero;
use App\Models\TableroColumna;
use App\Services\TableroService;
use Illuminate\Http\Request;

class TableroColumnaController extends Controller
{
    protected TableroService $service;

    public function __construct(TableroService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request, Tablero $tablero)
    {
        $request->validate(['nombre' => 'required|string|max:255']);

        $maxPosicion = $tablero->columnas()->max('posicion') ?? -1;

        $columna = $tablero->columnas()->create([
            'nombre' => $request->nombre,
            'posicion' => $maxPosicion + 1,
        ]);

        $html = view('tableros.partials._column', [
            'columna' => $columna->load('tarjetas'),
            'puedeEditar' => true,
        ])->render();

        return response()->json([
            'success' => true,
            'columna' => $columna,
            'html' => $html,
        ]);
    }

    public function update(Request $request, TableroColumna $columna)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        $columna->update(['nombre' => $request->nombre]);

        return response()->json(['success' => true]);
    }

    public function destroy(TableroColumna $columna)
    {
        $columna->delete();
        return response()->json(['success' => true]);
    }

    public function reordenar(Request $request, Tablero $tablero)
    {
        $request->validate(['posiciones' => 'required|array']);
        $this->service->reordenarColumnas($tablero, $request->posiciones);

        return response()->json(['success' => true]);
    }
}
