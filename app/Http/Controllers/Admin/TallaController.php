<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TallaRequest;
use App\Models\Talla;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TallaController extends Controller
{
    public function index(): View
    {
        $tallas = Talla::orderBy('orden')->orderBy('nombre')->get();

        return view('admin.tallas.index', compact('tallas'));
    }

    public function create(): View
    {
        return view('admin.tallas.form', ['talla' => new Talla]);
    }

    public function store(TallaRequest $request): RedirectResponse
    {
        Talla::create($request->validated());

        return redirect()->route('admin.tallas.index')->with('success', 'Talla creada.');
    }

    public function edit(Talla $talla): View
    {
        return view('admin.tallas.form', compact('talla'));
    }

    public function update(TallaRequest $request, Talla $talla): RedirectResponse
    {
        $talla->update($request->validated());

        return redirect()->route('admin.tallas.index')->with('success', 'Talla actualizada.');
    }

    public function destroy(Talla $talla): RedirectResponse
    {
        $talla->delete();

        return redirect()->route('admin.tallas.index')->with('success', 'Talla eliminada.');
    }
}
