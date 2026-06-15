<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePagoAhorroNominaRequest;
use App\Models\Empleado;
use App\Models\PagoAhorroNomina;
use App\Services\PagoAhorroNominaService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PagoAhorroNominaController extends Controller
{
    public function __construct(private PagoAhorroNominaService $ahorros) {}

    public function index(): View
    {
        $empleados = Empleado::activos()
            ->withSum('detalles as total_ahorrado', 'ahorro')
            ->withSum('pagosAhorroNomina as total_pagado_ahorro', 'monto')
            ->orderBy('nombre')
            ->get();

        $pagos = PagoAhorroNomina::with('empleado', 'user')
            ->orderByDesc('pagado_en')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('nomina-ahorros.index', compact('empleados', 'pagos'));
    }

    public function store(StorePagoAhorroNominaRequest $request): RedirectResponse
    {
        $empleado = Empleado::findOrFail((int) $request->input('empleado_id'));

        try {
            $this->ahorros->registrar($empleado, $request->validated(), (int) $request->user()->id);
        } catch (DomainException $e) {
            return back()->withErrors(['monto' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('nomina-ahorros.index')
            ->with('success', 'Entrega de ahorro registrada.');
    }

    public function destroy(PagoAhorroNomina $pago): RedirectResponse
    {
        $this->ahorros->eliminar($pago);

        return redirect()
            ->route('nomina-ahorros.index')
            ->with('success', 'Entrega de ahorro eliminada.');
    }
}
