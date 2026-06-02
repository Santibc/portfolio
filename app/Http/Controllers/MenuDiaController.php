<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMenuDiaRequest;
use App\Models\DiaSemana;
use App\Models\MenuItem;
use App\Services\MenuDiaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MenuDiaController extends Controller
{
    public function __construct(private MenuDiaService $svc) {}

    public function index(): View
    {
        $dias = DiaSemana::with(['menuItems' => fn ($q) => $q->orderBy('orden')->orderBy('nombre')])
            ->orderBy('id')
            ->get();

        $items = MenuItem::activos()->with('tipo')->orderBy('orden')->orderBy('nombre')->get();
        $tipos = $items->pluck('tipo')->unique('id')->sortBy('orden')->values();

        // Mapa { diaId(string) => [itemIds(string)] } para el estado Alpine de los checkboxes.
        $seleccion = $dias->mapWithKeys(fn (DiaSemana $d) => [
            (string) $d->id => $d->menuItems->pluck('id')->map(fn ($id) => (string) $id)->values()->all(),
        ])->all();

        return view('menu-dia.index', compact('dias', 'items', 'tipos', 'seleccion'));
    }

    public function update(UpdateMenuDiaRequest $request, DiaSemana $dia): RedirectResponse
    {
        $this->svc->sincronizar($dia, $request->input('items', []));

        return redirect()
            ->route('menu-dia.index')
            ->with('success', "Menú de {$dia->nombre} actualizado.");
    }
}
