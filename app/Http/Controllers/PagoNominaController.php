<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePagoMasivoNominaRequest;
use App\Http\Requests\StorePagoNominaRequest;
use App\Models\MetodoPago;
use App\Models\Nomina;
use App\Models\NominaDetalle;
use App\Models\PagoNomina;
use App\Services\PagoNominaService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagoNominaController extends Controller
{
    public function __construct(private PagoNominaService $pagos) {}

    public function create(NominaDetalle $detalle): View
    {
        $detalle->load(['empleado.metodoPago', 'nomina', 'pagos.metodoPago']);

        return view('nomina-pagos.create', [
            'detalle' => $detalle,
            'metodosOptions' => $this->metodosOptions(),
        ]);
    }

    public function store(StorePagoNominaRequest $request): RedirectResponse
    {
        $detalle = NominaDetalle::findOrFail((int) $request->input('nomina_detalle_id'));

        try {
            $this->pagos->registrar($detalle, $request->validated(), (int) $request->user()->id);
        } catch (DomainException $e) {
            return back()->withErrors(['monto' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('nomina.show', $detalle->nomina_id)
            ->with('success', 'Pago registrado correctamente.');
    }

    public function pagoMasivo(Request $request): View
    {
        $query = NominaDetalle::with(['empleado.metodoPago', 'nomina'])
            ->withSum('pagos', 'monto')
            ->orderBy('empleado_nombre');

        if ($request->filled('nomina')) {
            $query->where('nomina_id', (int) $request->input('nomina'));
        }

        $detalles = $query->get()->filter(fn (NominaDetalle $d) => $d->saldo_pendiente > 0)->values();

        return view('nomina-pagos.masivo', [
            'detalles' => $detalles,
            'metodosOptions' => $this->metodosOptions(),
            'metodoDefault' => (int) (MetodoPago::where('activo', true)->orderBy('orden')->value('id') ?? 0),
            'nominaFiltro' => $request->filled('nomina') ? Nomina::find((int) $request->input('nomina')) : null,
        ]);
    }

    public function pagoMasivoStore(StorePagoMasivoNominaRequest $request): RedirectResponse
    {
        try {
            $cantidad = $this->pagos->registrarMasivo($request->validated()['items'], (int) $request->user()->id);
        } catch (DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('nomina.index')
            ->with('success', "Se registraron {$cantidad} pago(s) de nómina.");
    }

    public function destroy(PagoNomina $pago): RedirectResponse
    {
        $nominaId = $pago->detalle?->nomina_id;
        $this->pagos->eliminar($pago);

        return redirect()
            ->route('nomina.show', $nominaId)
            ->with('success', 'Pago eliminado.');
    }

    /** @return array<int, string> */
    private function metodosOptions(): array
    {
        return MetodoPago::where('activo', true)
            ->orderBy('orden')
            ->pluck('nombre', 'id')
            ->all();
    }
}
