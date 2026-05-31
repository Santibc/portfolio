<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMetodoPagoRequest;
use App\Http\Requests\UpdateMetodoPagoRequest;
use App\Models\MetodoPago;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MetodoPagoController extends Controller
{
    public function index(): View
    {
        $metodos = MetodoPago::orderBy('orden')->orderBy('nombre')->get();

        return view('metodos-pago.index', compact('metodos'));
    }

    public function store(StoreMetodoPagoRequest $request): RedirectResponse
    {
        MetodoPago::create([
            'codigo'      => $request->string('codigo')->toString(),
            'nombre'      => $request->string('nombre')->toString(),
            'es_efectivo' => $request->boolean('es_efectivo'),
            'activo'      => $request->boolean('activo', true),
            'orden'       => (int) ($request->input('orden') ?? 0),
        ]);

        return redirect()
            ->route('metodos-pago.index')
            ->with('success', 'Método de pago creado.');
    }

    public function update(UpdateMetodoPagoRequest $request, MetodoPago $metodoPago): RedirectResponse
    {
        $metodoPago->update([
            'codigo'      => $request->string('codigo')->toString(),
            'nombre'      => $request->string('nombre')->toString(),
            'es_efectivo' => $request->boolean('es_efectivo'),
            'activo'      => $request->boolean('activo', false),
            'orden'       => (int) ($request->input('orden') ?? $metodoPago->orden),
        ]);

        return redirect()
            ->route('metodos-pago.index')
            ->with('success', 'Método de pago actualizado.');
    }

    public function destroy(MetodoPago $metodoPago): RedirectResponse
    {
        // Soft delete: conserva los pagos ya registrados (la FK sigue válida) y
        // saca el método de la caja sin romper el histórico de ventas.
        $metodoPago->delete();

        return redirect()
            ->route('metodos-pago.index')
            ->with('success', 'Método de pago "' . $metodoPago->nombre . '" deshabilitado.');
    }
}
