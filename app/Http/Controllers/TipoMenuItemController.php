<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTipoMenuItemRequest;
use App\Http\Requests\UpdateTipoMenuItemRequest;
use App\Models\TipoMenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class TipoMenuItemController extends Controller
{
    public function store(StoreTipoMenuItemRequest $request): RedirectResponse
    {
        TipoMenuItem::create([
            'nombre' => $request->string('nombre')->toString(),
            'slug'   => Str::slug($request->string('nombre')->toString()),
            'orden'  => (int) ($request->input('orden') ?? 0),
        ]);

        return redirect()
            ->route('menu-items.index')
            ->with('success', 'Tipo creado correctamente.');
    }

    public function update(UpdateTipoMenuItemRequest $request, TipoMenuItem $tipo): RedirectResponse
    {
        $tipo->update([
            'nombre' => $request->string('nombre')->toString(),
            'slug'   => Str::slug($request->string('nombre')->toString()),
            'orden'  => (int) ($request->input('orden') ?? $tipo->orden),
        ]);

        return redirect()
            ->route('menu-items.index')
            ->with('success', 'Tipo actualizado.');
    }

    public function destroy(TipoMenuItem $tipo): RedirectResponse
    {
        if ($tipo->menuItems()->exists()) {
            return redirect()
                ->route('menu-items.index')
                ->with('error', 'No se puede eliminar "' . $tipo->nombre . '": hay items asociados.');
        }

        $tipo->delete();

        return redirect()
            ->route('menu-items.index')
            ->with('success', 'Tipo eliminado.');
    }
}
