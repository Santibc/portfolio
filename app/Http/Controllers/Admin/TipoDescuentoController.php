<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TipoDescuentoRequest;
use App\Models\TipoDescuento;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TipoDescuentoController extends Controller
{
    public function index(): View
    {
        $tipos = TipoDescuento::orderBy('alcance')->orderBy('nombre')->get();

        return view('admin.tipos-descuento.index', compact('tipos'));
    }

    public function create(): View
    {
        return view('admin.tipos-descuento.form', ['tipo' => new TipoDescuento]);
    }

    public function store(TipoDescuentoRequest $request): RedirectResponse
    {
        TipoDescuento::create($request->validated());

        return redirect()->route('admin.tipos-descuento.index')->with('success', 'Tipo de descuento creado.');
    }

    public function edit(TipoDescuento $tipos_descuento): View
    {
        return view('admin.tipos-descuento.form', ['tipo' => $tipos_descuento]);
    }

    public function update(TipoDescuentoRequest $request, TipoDescuento $tipos_descuento): RedirectResponse
    {
        $tipos_descuento->update($request->validated());

        return redirect()->route('admin.tipos-descuento.index')->with('success', 'Tipo de descuento actualizado.');
    }

    public function destroy(TipoDescuento $tipos_descuento): RedirectResponse
    {
        $tipos_descuento->delete();

        return redirect()->route('admin.tipos-descuento.index')->with('success', 'Tipo de descuento eliminado.');
    }
}
