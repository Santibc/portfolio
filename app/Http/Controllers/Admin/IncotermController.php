<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IncotermRequest;
use App\Models\Incoterm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class IncotermController extends Controller
{
    public function index(): View
    {
        $incoterms = Incoterm::orderBy('codigo')->get();

        return view('admin.incoterms.index', compact('incoterms'));
    }

    public function create(): View
    {
        return view('admin.incoterms.form', ['incoterm' => new Incoterm]);
    }

    public function store(IncotermRequest $request): RedirectResponse
    {
        Incoterm::create($request->validated());

        return redirect()->route('admin.incoterms.index')->with('success', 'Incoterm creado.');
    }

    public function edit(Incoterm $incoterm): View
    {
        return view('admin.incoterms.form', compact('incoterm'));
    }

    public function update(IncotermRequest $request, Incoterm $incoterm): RedirectResponse
    {
        $incoterm->update($request->validated());

        return redirect()->route('admin.incoterms.index')->with('success', 'Incoterm actualizado.');
    }

    public function destroy(Incoterm $incoterm): RedirectResponse
    {
        $incoterm->delete();

        return redirect()->route('admin.incoterms.index')->with('success', 'Incoterm eliminado.');
    }
}
