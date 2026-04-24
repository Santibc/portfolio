<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TipoPagoRequest;
use App\Models\TipoPago;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TipoPagoController extends Controller
{
    public function index(): View
    {
        $tipos = TipoPago::orderBy('dias_credito')->get();

        return view('admin.tipos-pago.index', compact('tipos'));
    }

    public function create(): View
    {
        return view('admin.tipos-pago.form', ['tipo' => new TipoPago]);
    }

    public function store(TipoPagoRequest $request): RedirectResponse
    {
        TipoPago::create($request->validated());

        return redirect()->route('admin.tipos-pago.index')->with('success', 'Tipo de pago creado.');
    }

    public function edit(TipoPago $tipos_pago): View
    {
        return view('admin.tipos-pago.form', ['tipo' => $tipos_pago]);
    }

    public function update(TipoPagoRequest $request, TipoPago $tipos_pago): RedirectResponse
    {
        $tipos_pago->update($request->validated());

        return redirect()->route('admin.tipos-pago.index')->with('success', 'Tipo de pago actualizado.');
    }

    public function destroy(TipoPago $tipos_pago): RedirectResponse
    {
        $tipos_pago->delete();

        return redirect()->route('admin.tipos-pago.index')->with('success', 'Tipo de pago eliminado.');
    }
}
