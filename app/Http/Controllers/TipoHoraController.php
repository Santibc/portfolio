<?php

namespace App\Http\Controllers;

use App\Models\TipoHora;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipoHoraController extends Controller
{
    public function index(): View
    {
        $tipos = TipoHora::withCount('bonos')->orderBy('nombre')->get();
        return view('tipos-hora.index', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio_hora' => 'required|numeric|min:0',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'precio_hora.required' => 'El precio por hora es obligatorio.',
        ]);

        TipoHora::create([
            'nombre' => $request->nombre,
            'precio_hora' => $request->precio_hora,
            'activo' => $request->boolean('activo', true),
        ]);

        return back()->with('success', 'Tipo de hora creado correctamente.');
    }

    public function update(Request $request, TipoHora $tipoHora)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio_hora' => 'required|numeric|min:0',
        ]);

        $tipoHora->update([
            'nombre' => $request->nombre,
            'precio_hora' => $request->precio_hora,
            'activo' => $request->boolean('activo'),
        ]);

        return back()->with('success', 'Tipo de hora actualizado correctamente.');
    }

    public function destroy(TipoHora $tipoHora)
    {
        if ($tipoHora->bonos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: tiene bonos asociados. Puedes desactivarlo.');
        }

        $tipoHora->delete();
        return back()->with('success', 'Tipo de hora eliminado correctamente.');
    }
}
