<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreGastoFijoRequest;
use App\Http\Requests\UpdateGastoFijoRequest;
use App\Models\ConceptoGastoFijo;
use App\Models\GastoFijo;
use App\Models\MetodoPago;
use App\Services\GastoFijoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GastoFijoController extends Controller
{
    public function __construct(private GastoFijoService $gastosFijos) {}

    public function index(): View
    {
        $gastos = GastoFijo::with(['concepto', 'metodoPago', 'user'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        $rows = $gastos->map(function (GastoFijo $g) {
            $editUrl = route('gastos-fijos.edit', $g);
            $deleteUrl = route('gastos-fijos.destroy', $g);
            $csrf = csrf_token();

            $acciones = '<div class="inline-flex items-center gap-2">'
                .'<a href="'.$editUrl.'" class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium"><i data-lucide="edit" class="w-3.5 h-3.5"></i>Editar</a>'
                .'<form action="'.$deleteUrl.'" method="POST" class="inline" onsubmit="return confirm(\'¿Eliminar este gasto fijo?\');">'
                .'<input type="hidden" name="_token" value="'.$csrf.'">'
                .'<input type="hidden" name="_method" value="DELETE">'
                .'<button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i>Eliminar</button>'
                .'</form>'
                .'</div>';

            return [
                'fecha' => e($g->fecha->format('Y-m-d')),
                'concepto' => e($g->concepto?->nombre ?? '—'),
                'metodo' => e($g->metodoPago?->nombre ?? '—'),
                'valor' => '<span class="font-semibold tabular-nums text-cream-900 dark:text-cream-50">'.e($g->valor_formateado).'</span>',
                'observacion' => e((string) ($g->observacion ?? '—')),
                'cajero' => e($g->user?->name ?? '—'),
                'acciones' => $acciones,
            ];
        })->values()->all();

        $columns = [
            ['key' => 'fecha',       'label' => 'Fecha',       'sortable' => true],
            ['key' => 'concepto',    'label' => 'Concepto',    'sortable' => true],
            ['key' => 'metodo',      'label' => 'Método',      'sortable' => true],
            ['key' => 'valor',       'label' => 'Valor',       'sortable' => true],
            ['key' => 'observacion', 'label' => 'Observación', 'sortable' => false],
            ['key' => 'cajero',      'label' => 'Registró',    'sortable' => true],
            ['key' => 'acciones',    'label' => 'Acciones',    'sortable' => false],
        ];

        $totalMes = (int) GastoFijo::whereBetween('fecha', [now()->startOfMonth(), now()->endOfMonth()])->sum('valor');

        return view('gastos-fijos.index', compact('rows', 'columns', 'totalMes'));
    }

    public function create(): View
    {
        return view('gastos-fijos.create', [
            'gastoFijo' => null,
            'conceptosOptions' => $this->conceptosOptions(),
            'metodosOptions' => $this->metodosOptions(),
        ]);
    }

    public function store(StoreGastoFijoRequest $request): RedirectResponse
    {
        $this->gastosFijos->crear($request->validated(), (int) $request->user()->id);

        return redirect()
            ->route('gastos-fijos.index')
            ->with('success', 'Gasto fijo registrado correctamente.');
    }

    public function edit(GastoFijo $gastoFijo): View
    {
        $gastoFijo->load(['concepto', 'metodoPago']);

        return view('gastos-fijos.edit', [
            'gastoFijo' => $gastoFijo,
            'conceptosOptions' => $this->conceptosOptions($gastoFijo),
            'metodosOptions' => $this->metodosOptions(),
        ]);
    }

    public function update(UpdateGastoFijoRequest $request, GastoFijo $gastoFijo): RedirectResponse
    {
        $this->gastosFijos->actualizar($gastoFijo, $request->validated());

        return redirect()
            ->route('gastos-fijos.index')
            ->with('success', 'Gasto fijo actualizado correctamente.');
    }

    public function destroy(GastoFijo $gastoFijo): RedirectResponse
    {
        $this->gastosFijos->eliminar($gastoFijo);

        return redirect()
            ->route('gastos-fijos.index')
            ->with('success', 'Gasto fijo eliminado.');
    }

    /**
     * Conceptos activos para el select. En edición incluye el concepto actual
     * aunque esté inactivo, para no perderlo.
     *
     * @return array<int, string>
     */
    private function conceptosOptions(?GastoFijo $gastoFijo = null): array
    {
        $conceptos = ConceptoGastoFijo::activos()->orderBy('orden')->orderBy('nombre')->get();

        if ($gastoFijo?->concepto && ! $conceptos->contains('id', $gastoFijo->concepto_gasto_fijo_id)) {
            $conceptos->push($gastoFijo->concepto);
        }

        return $conceptos->pluck('nombre', 'id')->all();
    }

    /** @return array<int, string> */
    private function metodosOptions(): array
    {
        return MetodoPago::activos()->orderBy('orden')->pluck('nombre', 'id')->all();
    }
}
