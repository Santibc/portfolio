<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateVentaRequest;
use App\Models\MenuItem;
use App\Models\MetodoPago;
use App\Models\Venta;
use App\Services\VentaService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function __construct(private VentaService $ventas)
    {
    }

    public function edit(Venta $venta): View
    {
        $venta->load(['items', 'pagos.metodo', 'turno']);
        $items   = MenuItem::activos()->with('tipo')->orderBy('orden')->orderBy('nombre')->get();
        $tipos   = $items->pluck('tipo')->unique('id')->sortBy('orden')->values();
        $metodos = MetodoPago::activos()->orderBy('orden')->orderBy('nombre')->get();

        return view('caja-dashboard.venta-edit', compact('venta', 'items', 'tipos', 'metodos'));
    }

    public function update(UpdateVentaRequest $request, Venta $venta): RedirectResponse
    {
        try {
            $this->ventas->actualizar(
                $venta,
                $request->input('items', []),
                $request->input('pagos', []),
                $request->input('notas'),
            );
        } catch (DomainException $e) {
            return back()->withErrors(['caja' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('caja-dashboard.show', $venta->turno_caja_id)
            ->with('success', 'Venta actualizada.');
    }

    public function destroy(Venta $venta): RedirectResponse
    {
        $turnoId = (int) $venta->turno_caja_id;
        $this->ventas->eliminar($venta);

        return redirect()
            ->route('caja-dashboard.show', $turnoId)
            ->with('success', 'Venta eliminada.');
    }
}
