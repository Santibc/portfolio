<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TipoGasto;
use App\Http\Requests\StoreGastoRequest;
use App\Http\Requests\UpdateGastoRequest;
use App\Models\Gasto;
use App\Models\TrabajadorTurno;
use App\Services\GastoService;
use App\Services\TurnoCajaService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GastoController extends Controller
{
    public function __construct(
        private GastoService $gastos,
        private TurnoCajaService $turnos,
    ) {
    }

    public function index(): View
    {
        $gastos = Gasto::with(['turno', 'trabajadorTurno', 'user'])
            ->latest()
            ->get();

        $rows = $gastos->map(function (Gasto $g) {
            $tipoBadge = $g->tipo === TipoGasto::Turno
                ? '<span class="inline-flex items-center font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200 text-xs px-2.5 py-1">Pago de turno</span>'
                : '<span class="inline-flex items-center font-semibold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200 text-xs px-2.5 py-1">Gasto general</span>';

            $concepto = $g->tipo === TipoGasto::Turno
                ? e($g->trabajadorTurno?->nombre ?? '—')
                : e((string) ($g->observacion ?? '—'));

            $turnoUrl = $g->turno ? route('caja-dashboard.show', $g->turno) : '#';
            $turnoLink = $g->turno
                ? '<a href="' . $turnoUrl . '" class="text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium">Turno #' . $g->turno->id . '</a>'
                : '<span class="text-cream-500">—</span>';

            $editUrl   = route('gastos.edit', $g);
            $deleteUrl = route('gastos.destroy', $g);
            $csrf      = csrf_token();

            $acciones = '<div class="inline-flex items-center gap-2">'
                . '<a href="' . $editUrl . '" class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium"><i data-lucide="edit" class="w-3.5 h-3.5"></i>Editar</a>'
                . '<form action="' . $deleteUrl . '" method="POST" class="inline" onsubmit="return confirm(\'¿Eliminar este gasto?\');">'
                . '<input type="hidden" name="_token" value="' . $csrf . '">'
                . '<input type="hidden" name="_method" value="DELETE">'
                . '<button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i>Eliminar</button>'
                . '</form>'
                . '</div>';

            return [
                'fecha'    => e($g->created_at->format('Y-m-d H:i')),
                'turno'    => $turnoLink,
                'tipo'     => $tipoBadge,
                'concepto' => $concepto,
                'valor'    => '<span class="font-semibold tabular-nums text-cream-900 dark:text-cream-50">' . e($g->valor_formateado) . '</span>',
                'cajero'   => e($g->user?->name ?? '—'),
                'acciones' => $acciones,
            ];
        })->values()->all();

        $columns = [
            ['key' => 'fecha',    'label' => 'Fecha',             'sortable' => true],
            ['key' => 'turno',    'label' => 'Turno',             'sortable' => false],
            ['key' => 'tipo',     'label' => 'Tipo',              'sortable' => true],
            ['key' => 'concepto', 'label' => 'Concepto/Trabajador', 'sortable' => true],
            ['key' => 'valor',    'label' => 'Valor',             'sortable' => true],
            ['key' => 'cajero',   'label' => 'Cajero',            'sortable' => true],
            ['key' => 'acciones', 'label' => 'Acciones',          'sortable' => false],
        ];

        return view('gastos.index', compact('rows', 'columns'));
    }

    public function create(): View
    {
        $turnoActivo = $this->turnos->turnoActivo();
        if ($turnoActivo !== null) {
            $turnoActivo->load('ventas');
        }

        $trabajadores = TrabajadorTurno::activos()->orderBy('nombre')->get();
        $trabajadoresOptions = $trabajadores->pluck('nombre', 'id')->all();
        $valoresTurnoDefault = $trabajadores->mapWithKeys(fn ($t) => [$t->id => (int) $t->valor_turno_default])->all();

        return view('gastos.create', [
            'turnoActivo'         => $turnoActivo,
            'trabajadoresOptions' => $trabajadoresOptions,
            'valoresTurnoDefault' => $valoresTurnoDefault,
            'gasto'               => null,
        ]);
    }

    public function store(StoreGastoRequest $request): RedirectResponse
    {
        try {
            $this->gastos->crear($request->validated(), (int) $request->user()->id);
        } catch (DomainException $e) {
            return redirect()
                ->route('gastos.create')
                ->withInput()
                ->withErrors(['gasto' => $e->getMessage()]);
        }

        return redirect()
            ->route('gastos.index')
            ->with('success', 'Gasto registrado correctamente.');
    }

    public function edit(Gasto $gasto): View
    {
        $gasto->load(['turno', 'trabajadorTurno']);

        $trabajadores = TrabajadorTurno::activos()->orderBy('nombre')->get();

        // Asegura que el trabajador del gasto aparezca aunque esté inactivo / soft-deleted
        if ($gasto->trabajadorTurno && ! $trabajadores->contains('id', $gasto->trabajador_turno_id)) {
            $trabajadores->push($gasto->trabajadorTurno);
        }

        $trabajadoresOptions = $trabajadores->pluck('nombre', 'id')->all();
        $valoresTurnoDefault = $trabajadores->mapWithKeys(fn ($t) => [$t->id => (int) $t->valor_turno_default])->all();

        return view('gastos.edit', [
            'gasto'               => $gasto,
            'trabajadoresOptions' => $trabajadoresOptions,
            'valoresTurnoDefault' => $valoresTurnoDefault,
        ]);
    }

    public function update(UpdateGastoRequest $request, Gasto $gasto): RedirectResponse
    {
        try {
            $this->gastos->actualizar($gasto, $request->validated());
        } catch (DomainException $e) {
            return redirect()
                ->route('gastos.edit', $gasto)
                ->withInput()
                ->withErrors(['gasto' => $e->getMessage()]);
        }

        return redirect()
            ->route('gastos.index')
            ->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroy(Gasto $gasto): RedirectResponse
    {
        $this->gastos->eliminar($gasto);

        return redirect()
            ->route('gastos.index')
            ->with('success', 'Gasto eliminado.');
    }
}
