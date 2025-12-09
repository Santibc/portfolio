<?php

namespace App\Http\Controllers;

use App\Models\LandingEstadistica;
use Illuminate\Http\Request;

class LandingEstadisticaController extends Controller
{
    public function index()
    {
        $estadisticas = LandingEstadistica::ordenadas()->paginate(10);
        return view('admin.landing.estadisticas.index', compact('estadisticas'));
    }

    public function create()
    {
        $estadistica = new LandingEstadistica();
        return view('admin.landing.estadisticas.form', compact('estadistica'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:50',
            'etiqueta' => 'required|string|max:100',
            'orden' => 'nullable|integer',
            'activo' => 'boolean',
        ]);

        LandingEstadistica::create([
            'numero' => $request->numero,
            'etiqueta' => $request->etiqueta,
            'orden' => $request->orden ?? 0,
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()->route('admin.landing.estadisticas.index')
                        ->with('success', 'Estadística creada correctamente');
    }

    public function edit($id)
    {
        $estadistica = LandingEstadistica::findOrFail($id);
        return view('admin.landing.estadisticas.form', compact('estadistica'));
    }

    public function update(Request $request, $id)
    {
        $estadistica = LandingEstadistica::findOrFail($id);

        $request->validate([
            'numero' => 'required|string|max:50',
            'etiqueta' => 'required|string|max:100',
            'orden' => 'nullable|integer',
            'activo' => 'boolean',
        ]);

        $estadistica->update([
            'numero' => $request->numero,
            'etiqueta' => $request->etiqueta,
            'orden' => $request->orden ?? 0,
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()->route('admin.landing.estadisticas.index')
                        ->with('success', 'Estadística actualizada correctamente');
    }

    public function destroy($id)
    {
        $estadistica = LandingEstadistica::findOrFail($id);
        $estadistica->delete();

        return redirect()->route('admin.landing.estadisticas.index')
                        ->with('success', 'Estadística eliminada correctamente');
    }
}
