<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTipoProductoMercadoRequest;
use App\Http\Requests\UpdateTipoProductoMercadoRequest;
use App\Models\TipoProductoMercado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TipoProductoMercadoController extends Controller
{
    public function index(): View
    {
        $tipos = TipoProductoMercado::withCount('productos')
            ->orderBy('nombre')
            ->get();

        return view('productos-mercado.tipos.index', compact('tipos'));
    }

    public function store(StoreTipoProductoMercadoRequest $request): RedirectResponse
    {
        TipoProductoMercado::create([
            'nombre' => $request->nombre,
            'slug'   => Str::slug($request->nombre),
        ]);

        return redirect()
            ->route('productos-mercado.tipos.index')
            ->with('success', 'Tipo creado correctamente.');
    }

    public function update(UpdateTipoProductoMercadoRequest $request, TipoProductoMercado $tipo): RedirectResponse
    {
        $tipo->update([
            'nombre' => $request->nombre,
            'slug'   => Str::slug($request->nombre),
        ]);

        return redirect()
            ->route('productos-mercado.tipos.index')
            ->with('success', 'Tipo actualizado.');
    }

    public function destroy(TipoProductoMercado $tipo): RedirectResponse
    {
        if ($tipo->productos()->exists()) {
            return redirect()
                ->route('productos-mercado.tipos.index')
                ->with('error', 'No se puede eliminar "' . $tipo->nombre . '": hay productos asociados.');
        }

        $tipo->delete();

        return redirect()
            ->route('productos-mercado.tipos.index')
            ->with('success', 'Tipo eliminado.');
    }
}
