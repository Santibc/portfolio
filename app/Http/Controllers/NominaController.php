<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EstadoNomina;
use App\Http\Requests\LiquidarNominaRequest;
use App\Http\Requests\UpdateNominaDetallesRequest;
use App\Models\MetodoPago;
use App\Models\Nomina;
use App\Services\LiquidacionNominaService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NominaController extends Controller
{
    public function __construct(private LiquidacionNominaService $liquidacion) {}

    public function index(): View
    {
        $nominas = Nomina::with(['creadaPor', 'detalles' => fn ($q) => $q->withSum('pagos', 'monto')])
            ->orderByDesc('fecha_inicio')
            ->get();

        return view('nomina.index', compact('nominas'));
    }

    public function create(): View
    {
        return view('nomina.create');
    }

    public function store(LiquidarNominaRequest $request): RedirectResponse
    {
        try {
            $nomina = $this->liquidacion->liquidar($request->validated(), (int) $request->user()->id);
        } catch (DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('nomina.edit', $nomina)
            ->with('success', 'Nómina liquidada. Revisa y ajusta los valores por empleado antes de pagar.');
    }

    public function show(Nomina $nomina): View
    {
        $nomina->load([
            'creadaPor',
            'detalles' => fn ($q) => $q->withSum('pagos', 'monto')->orderBy('empleado_nombre'),
            'detalles.empleado.metodoPago',
            'detalles.pagos' => fn ($q) => $q->orderByDesc('fecha_pago'),
            'detalles.pagos.metodoPago',
            'detalles.pagos.user',
        ]);

        return view('nomina.show', [
            'nomina' => $nomina,
            'metodosOptions' => $this->metodosOptions(),
        ]);
    }

    public function edit(Nomina $nomina): View
    {
        if ($nomina->estado === EstadoNomina::Pagada) {
            return redirect()
                ->route('nomina.show', $nomina)
                ->with('error', 'La nómina ya está pagada y no se puede editar.');
        }

        $nomina->load(['detalles' => fn ($q) => $q->orderBy('empleado_nombre'), 'detalles.empleado']);

        return view('nomina.edit', compact('nomina'));
    }

    public function update(UpdateNominaDetallesRequest $request, Nomina $nomina): RedirectResponse
    {
        try {
            $this->liquidacion->actualizarLineas($nomina, $request->validated()['lineas'], (int) $request->user()->id);
        } catch (DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('nomina.show', $nomina)
            ->with('success', 'Valores de la nómina actualizados.');
    }

    public function aprobar(Nomina $nomina): RedirectResponse
    {
        $this->liquidacion->aprobar($nomina);

        return redirect()
            ->route('nomina.show', $nomina)
            ->with('success', 'Nómina aprobada. Ya puedes registrar los pagos.');
    }

    public function destroy(Nomina $nomina): RedirectResponse
    {
        $this->liquidacion->eliminar($nomina);

        return redirect()
            ->route('nomina.index')
            ->with('success', 'Nómina eliminada.');
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
