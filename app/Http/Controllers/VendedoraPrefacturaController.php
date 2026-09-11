<?php

namespace App\Http\Controllers;

use App\Models\VendedoraPrefactura;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendedoraPrefacturaController extends Controller
{
    public function index()
    {
        $vendedoras = VendedoraPrefactura::with('ubicacion')->orderBy('nombre')->get();
        $ubicaciones = Ubicacion::activas()->tiendas()->orderBy('nombre')->get();

        return view('pdv.vendedoras.index', compact('vendedoras', 'ubicaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150|unique:vendedoras_prefactura,nombre',
            'ubicacion_id' => 'nullable|exists:ubicaciones,id',
        ], [
            'nombre.unique' => 'Ya existe una vendedora con ese nombre.',
        ]);

        VendedoraPrefactura::create([
            'nombre' => trim($request->nombre),
            'ubicacion_id' => $request->ubicacion_id ?: null,
            'activo' => true,
        ]);

        return back()->with('success', 'Vendedora agregada.');
    }

    public function update(Request $request, $id)
    {
        $vendedora = VendedoraPrefactura::findOrFail($id);

        $request->validate([
            'nombre' => ['required', 'string', 'max:150', Rule::unique('vendedoras_prefactura', 'nombre')->ignore($vendedora->id)],
            'ubicacion_id' => 'nullable|exists:ubicaciones,id',
        ], [
            'nombre.unique' => 'Ya existe una vendedora con ese nombre.',
        ]);

        $vendedora->update([
            'nombre' => trim($request->nombre),
            'ubicacion_id' => $request->ubicacion_id ?: null,
        ]);

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
