<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreConceptoGastoFijoRequest;
use App\Http\Requests\UpdateConceptoGastoFijoRequest;
use App\Models\ConceptoGastoFijo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConceptoGastoFijoController extends Controller
{
    public function index(): View
    {
        $conceptos = ConceptoGastoFijo::withCount('gastosFijos')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('gastos-fijos.conceptos.index', compact('conceptos'));
    }

    public function store(StoreConceptoGastoFijoRequest $request): RedirectResponse
    {
        ConceptoGastoFijo::create([
            'nombre' => $request->nombre,
            'orden' => (int) $request->input('orden', 0),
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()
            ->route('gastos-fijos.conceptos.index')
            ->with('success', 'Concepto creado correctamente.');
    }

    public function update(UpdateConceptoGastoFijoRequest $request, ConceptoGastoFijo $concepto): RedirectResponse
    {
        $concepto->update([
            'nombre' => $request->nombre,
            'orden' => (int) $request->input('orden', $concepto->orden),
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()
            ->route('gastos-fijos.conceptos.index')
            ->with('success', 'Concepto actualizado.');
    }

    public function destroy(ConceptoGastoFijo $concepto): RedirectResponse
    {
        if ($concepto->gastosFijos()->exists()) {
            return redirect()
                ->route('gastos-fijos.conceptos.index')
                ->with('error', 'No se puede eliminar "'.$concepto->nombre.'": tiene gastos fijos asociados.');
        }

        $concepto->delete();

        return redirect()
            ->route('gastos-fijos.conceptos.index')
            ->with('success', 'Concepto eliminado.');
    }
}
