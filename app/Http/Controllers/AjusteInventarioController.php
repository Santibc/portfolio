<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAjusteInventarioRequest;
use App\Models\ProductoMercado;
use App\Services\InventarioService;
use DomainException;
use Illuminate\Http\RedirectResponse;

class AjusteInventarioController extends Controller
{
    public function __construct(private InventarioService $inventario)
    {
    }

    public function store(StoreAjusteInventarioRequest $request, ProductoMercado $producto): RedirectResponse
    {
        try {
            $this->inventario->ajustar(
                $producto,
                $request->cantidadConSigno(),
                $request->validated('motivo'),
                $request->user()->id,
            );
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('inventario.show', $producto)
            ->with('success', 'Ajuste de inventario registrado.');
    }
}
