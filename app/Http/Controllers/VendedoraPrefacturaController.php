<?php

namespace App\Http\Controllers;

use App\Models\VendedoraPrefactura;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendedoraPrefacturaController extends Controller
{
    public function index()
    {
        $vendedoras = VendedoraPrefactura::orderBy('nombre')->get();

        return view('pdv.vendedoras.index', compact('vendedoras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150|unique:vendedoras_prefactura,nombre',
        ], [
            'nombre.unique' => 'Ya existe una vendedora con ese nombre.',
        ]);

        VendedoraPrefactura::create([
            'nombre' => trim($request->nombre),
            'activo' => true,
        ]);

        return back()->with('success', 'Vendedora agregada.');
    }

    public function update(Request $request, $id)
    {
        $vendedora = VendedoraPrefactura::findOrFail($id);

        $request->validate([
            'nombre' => ['required', 'string', 'max:150', Rule::unique('vendedoras_prefactura', 'nombre')->ignore($vendedora->id)],
        ], [
            'nombre.unique' => 'Ya existe una vendedora con ese nombre.',
        ]);

        $vendedora->update(['nombre' => trim($request->nombre)]);

        return back()->with('success', 'Vendedora actualizada.');
    }

    public function toggle($id)
    {
        $vendedora = VendedoraPrefactura::findOrFail($id);
        $vendedora->update(['activo' => !$vendedora->activo]);

        return back()->with('success', $vendedora->activo ? 'Vendedora activada.' : 'Vendedora desactivada.');
    }

    public function destroy($id)
    {
        $vendedora = VendedoraPrefactura::findOrFail($id);
        $vendedora->delete();

        return back()->with('success', 'Vendedora eliminada.');
    }
}
