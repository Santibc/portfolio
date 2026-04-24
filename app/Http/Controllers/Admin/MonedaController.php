<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MonedaRequest;
use App\Models\Moneda;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class MonedaController extends Controller
{
    public function index(): View
    {
        $monedas = Moneda::orderBy('codigo')->get();

        return view('admin.monedas.index', compact('monedas'));
    }

    public function create(): View
    {
        return view('admin.monedas.form', ['moneda' => new Moneda]);
    }

    public function store(MonedaRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();

            if (! empty($data['es_predeterminada'])) {
                Moneda::query()->lockForUpdate()->update(['es_predeterminada' => false]);
            }

            Moneda::create($data);
        });

        return redirect()->route('admin.monedas.index')->with('success', 'Moneda creada.');
    }

    public function edit(Moneda $moneda): View
    {
        return view('admin.monedas.form', compact('moneda'));
    }

    public function update(MonedaRequest $request, Moneda $moneda): RedirectResponse
    {
        DB::transaction(function () use ($request, $moneda) {
            $data = $request->validated();

            if (! empty($data['es_predeterminada'])) {
                Moneda::query()->where('id', '!=', $moneda->id)->lockForUpdate()->update(['es_predeterminada' => false]);
            }

            $moneda->update($data);
        });

        return redirect()->route('admin.monedas.index')->with('success', 'Moneda actualizada.');
    }

    public function destroy(Moneda $moneda): RedirectResponse
    {
        if ($moneda->es_predeterminada) {
            return back()->with('error', 'No se puede eliminar la moneda predeterminada.');
        }

        $moneda->delete();

        return redirect()->route('admin.monedas.index')->with('success', 'Moneda eliminada.');
    }
}
