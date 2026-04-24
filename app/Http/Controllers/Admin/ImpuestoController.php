<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImpuestoRequest;
use App\Models\Impuesto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ImpuestoController extends Controller
{
    public function index(): View
    {
        $impuestos = Impuesto::orderBy('tipo')->orderBy('porcentaje')->get();

        return view('admin.impuestos.index', compact('impuestos'));
    }

    public function create(): View
    {
        return view('admin.impuestos.form', ['impuesto' => new Impuesto]);
    }

    public function store(ImpuestoRequest $request): RedirectResponse
    {
        Impuesto::create($request->validated());

        return redirect()->route('admin.impuestos.index')->with('success', 'Impuesto creado.');
    }

    public function edit(Impuesto $impuesto): View
    {
        return view('admin.impuestos.form', compact('impuesto'));
    }

    public function update(ImpuestoRequest $request, Impuesto $impuesto): RedirectResponse
    {
        $impuesto->update($request->validated());

        return redirect()->route('admin.impuestos.index')->with('success', 'Impuesto actualizado.');
    }

    public function destroy(Impuesto $impuesto): RedirectResponse
    {
        $impuesto->delete();

        return redirect()->route('admin.impuestos.index')->with('success', 'Impuesto eliminado.');
    }
}
