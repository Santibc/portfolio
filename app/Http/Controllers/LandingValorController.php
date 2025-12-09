<?php

namespace App\Http\Controllers;

use App\Models\LandingValor;
use Illuminate\Http\Request;

class LandingValorController extends Controller
{
    public function index()
    {
        $valores = LandingValor::ordenados()->paginate(10);
        return view('admin.landing.valores.index', compact('valores'));
    }

    public function create()
    {
        $valor = new LandingValor();
        return view('admin.landing.valores.form', compact('valor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'icono' => 'required|string|max:100',
            'titulo' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'orden' => 'nullable|integer',
            'activo' => 'boolean',
        ]);

        LandingValor::create([
            'icono' => $request->icono,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'orden' => $request->orden ?? 0,
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()->route('admin.landing.valores.index')
                        ->with('success', 'Valor creado correctamente');
    }

    public function edit($id)
    {
        $valor = LandingValor::findOrFail($id);
        return view('admin.landing.valores.form', compact('valor'));
    }

    public function update(Request $request, $id)
    {
        $valor = LandingValor::findOrFail($id);

        $request->validate([
            'icono' => 'required|string|max:100',
            'titulo' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'orden' => 'nullable|integer',
            'activo' => 'boolean',
        ]);

        $valor->update([
            'icono' => $request->icono,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'orden' => $request->orden ?? 0,
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()->route('admin.landing.valores.index')
                        ->with('success', 'Valor actualizado correctamente');
    }

    public function destroy($id)
    {
        $valor = LandingValor::findOrFail($id);
        $valor->delete();

        return redirect()->route('admin.landing.valores.index')
                        ->with('success', 'Valor eliminado correctamente');
    }
}
