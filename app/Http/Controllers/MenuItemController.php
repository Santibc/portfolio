<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\MenuItem;
use App\Models\TipoMenuItem;
use App\Services\MenuItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function __construct(private MenuItemService $svc)
    {
    }

    public function index(Request $request): View
    {
        $tipoId = $request->integer('tipo_id') ?: null;
        $query  = MenuItem::with('tipo')->orderBy('orden')->orderBy('nombre');
        if ($tipoId !== null) {
            $query->where('tipo_id', $tipoId);
        }
        $items = $query->get();
        $tipos = TipoMenuItem::orderBy('orden')->orderBy('nombre')->get();

        return view('menu-items.index', compact('items', 'tipos', 'tipoId'));
    }

    public function create(): View
    {
        $tipos = TipoMenuItem::orderBy('orden')->orderBy('nombre')->pluck('nombre', 'id');

        return view('menu-items.create', compact('tipos'));
    }

    public function store(StoreMenuItemRequest $request): RedirectResponse
    {
        $data           = $request->validated();
        $data['activo'] = $request->boolean('activo', true);
        $imagen         = $request->file('imagen');
        unset($data['imagen']);

        $this->svc->crear($data, $imagen);

        return redirect()
            ->route('menu-items.index')
            ->with('success', 'Item creado correctamente.');
    }

    public function edit(MenuItem $menuItem): View
    {
        $tipos = TipoMenuItem::orderBy('orden')->orderBy('nombre')->pluck('nombre', 'id');

        return view('menu-items.edit', ['item' => $menuItem, 'tipos' => $tipos]);
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): RedirectResponse
    {
        $data           = $request->validated();
        $data['activo'] = $request->boolean('activo', false);
        $imagen         = $request->file('imagen');
        unset($data['imagen']);

        $this->svc->actualizar($menuItem, $data, $imagen);

        return redirect()
            ->route('menu-items.index')
            ->with('success', 'Item actualizado correctamente.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $this->svc->eliminar($menuItem);

        return redirect()
            ->route('menu-items.index')
            ->with('success', 'Item eliminado.');
    }
}
