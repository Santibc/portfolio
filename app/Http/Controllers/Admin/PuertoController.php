<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PuertoRequest;
use App\Models\Puerto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PuertoController extends Controller
{
    public function index(): View
    {
        $puertos = Puerto::orderBy('pais')->orderBy('nombre')->get();

        return view('admin.puertos.index', compact('puertos'));
    }

    public function create(): View
    {
        return view('admin.puertos.form', ['puerto' => new Puerto]);
    }

    public function store(PuertoRequest $request): RedirectResponse
    {
        Puerto::create($request->validated());

        return redirect()->route('admin.puertos.index')->with('success', 'Puerto creado.');
    }

    public function edit(Puerto $puerto): View
    {
        return view('admin.puertos.form', compact('puerto'));
    }

    public function update(PuertoRequest $request, Puerto $puerto): RedirectResponse
    {
        $puerto->update($request->validated());

        return redirect()->route('admin.puertos.index')->with('success', 'Puerto actualizado.');
    }

    public function destroy(Puerto $puerto): RedirectResponse
    {
        $puerto->delete();

        return redirect()->route('admin.puertos.index')->with('success', 'Puerto eliminado.');
    }
}
