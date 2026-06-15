<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MarcarPrestacionPagadaRequest;
use App\Http\Requests\StorePrestacionRequest;
use App\Models\Empleado;
use App\Models\MetodoPago;
use App\Models\PrestacionSocial;
use App\Services\PrestacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PrestacionController extends Controller
{
    public function __construct(private PrestacionService $prestaciones) {}

    public function index(): View
    {
        $prestaciones = PrestacionSocial::with(['empleado', 'metodoPago'])
            ->orderByDesc('fecha_fin')
            ->orderBy('empleado_id')
            ->get();

        return view('prestaciones.index', [
            'prestaciones' => $prestaciones,
            'metodosOptions' => $this->metodosOptions(),
        ]);
    }

    public function create(): View
    {
        $empleados = Empleado::activos()->orderBy('nombre')->get();

        return view('prestaciones.create', [
            'empleados' => $empleados,
            'factorInteres' => (float) config('nomina.factor_intereses_cesantias', 0.12),
        ]);
    }

    public function store(StorePrestacionRequest $request): RedirectResponse
    {
        $empleado = Empleado::findOrFail((int) $request->input('empleado_id'));

        $this->prestaciones->liquidar($empleado, $request->validated());

        return redirect()
            ->route('prestaciones.index')
            ->with('success', 'Prestación liquidada correctamente.');
    }

    public function marcarPagada(MarcarPrestacionPagadaRequest $request, PrestacionSocial $prestacion): RedirectResponse
    {
        $this->prestaciones->marcarPagada($prestacion, $request->validated());

        return redirect()
            ->route('prestaciones.index')
            ->with('success', 'Prestación marcada como pagada.');
    }

    public function destroy(PrestacionSocial $prestacion): RedirectResponse
    {
        $this->prestaciones->eliminar($prestacion);

        return redirect()
            ->route('prestaciones.index')
            ->with('success', 'Prestación eliminada.');
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
