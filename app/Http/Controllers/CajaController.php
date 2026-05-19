<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AbrirTurnoRequest;
use App\Http\Requests\CerrarTurnoRequest;
use App\Http\Requests\StoreVentaRequest;
use App\Models\MenuItem;
use App\Models\MetodoPago;
use App\Models\TurnoCaja;
use App\Services\TurnoCajaService;
use App\Services\VentaService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CajaController extends Controller
{
    public function __construct(
        private TurnoCajaService $turnos,
        private VentaService $ventas,
    ) {
    }

    public function index(): View
    {
        $turno = $this->turnos->turnoActivo()?->load(['aperturadoPor', 'ventas.pagos.metodo']);

        $items   = MenuItem::activos()->with('tipo')->orderBy('orden')->orderBy('nombre')->get();
        $tipos   = $items->pluck('tipo')->unique('id')->sortBy('orden')->values();
        $metodos = MetodoPago::activos()->orderBy('orden')->orderBy('nombre')->get();

        return view('caja.index', compact('turno', 'items', 'tipos', 'metodos'));
    }

    public function abrir(AbrirTurnoRequest $request): RedirectResponse
    {
        try {
            $this->turnos->abrir(
                (int) $request->input('base_inicial'),
                (int) $request->user()->id,
                $request->input('notas'),
            );
        } catch (DomainException $e) {
            return back()->withErrors(['caja' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('caja.index')
            ->with('success', 'Caja abierta correctamente.');
    }

    public function cerrar(CerrarTurnoRequest $request, TurnoCaja $turno): RedirectResponse
    {
        try {
            $this->turnos->cerrar(
                $turno,
                (int) $request->input('total_declarado'),
                (int) $request->user()->id,
                $request->input('notas'),
            );
        } catch (DomainException $e) {
            return back()->withErrors(['caja' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('caja-dashboard.show', $turno)
            ->with('success', 'Caja cerrada. Revisa el resumen del turno.');
    }

    public function storeVenta(StoreVentaRequest $request): RedirectResponse
    {
        try {
            $venta = $this->ventas->crear(
                $request->input('items', []),
                $request->input('pagos', []),
                (int) $request->user()->id,
                $request->input('notas'),
            );
        } catch (DomainException $e) {
            return back()->withErrors(['caja' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('caja.index')
            ->with('success', 'Venta registrada · Total ' . $venta->total_formateado . ' · Cambio ' . $venta->cambio_formateado);
    }
}
