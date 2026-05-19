<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrabajadorTurnoRequest;
use App\Http\Requests\UpdateTrabajadorTurnoRequest;
use App\Models\TrabajadorTurno;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrabajadorTurnoController extends Controller
{
    public function index(): View
    {
        $trabajadores = TrabajadorTurno::orderBy('nombre')->get();

        $rows = $trabajadores->map(function (TrabajadorTurno $t) {
            $activo = $t->activo
                ? '<span class="inline-flex items-center font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200 text-xs px-2.5 py-1">Activo</span>'
                : '<span class="inline-flex items-center font-semibold rounded-full bg-cream-200 text-cream-800 dark:bg-cream-800 dark:text-cream-200 text-xs px-2.5 py-1">Inactivo</span>';

            $editUrl   = route('trabajadores-turno.edit', $t);
            $deleteUrl = route('trabajadores-turno.destroy', $t);
            $csrf      = csrf_token();

            $acciones = '<div class="inline-flex items-center gap-2">'
                . '<a href="' . $editUrl . '" class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium"><i data-lucide="edit" class="w-3.5 h-3.5"></i>Editar</a>'
                . '<form action="' . $deleteUrl . '" method="POST" class="inline" onsubmit="return confirm(\'¿Eliminar este trabajador?\');">'
                . '<input type="hidden" name="_token" value="' . $csrf . '">'
                . '<input type="hidden" name="_method" value="DELETE">'
                . '<button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i>Eliminar</button>'
                . '</form>'
                . '</div>';

            return [
                'nombre'   => e($t->nombre),
                'valor'    => '<span class="font-semibold tabular-nums text-cream-900 dark:text-cream-50">' . e($t->valor_turno_default_formateado) . '</span>',
                'activo'   => $activo,
                'acciones' => $acciones,
            ];
        })->values()->all();

        $columns = [
            ['key' => 'nombre',   'label' => 'Nombre',           'sortable' => true],
            ['key' => 'valor',    'label' => 'Valor turno default', 'sortable' => true],
            ['key' => 'activo',   'label' => 'Estado',           'sortable' => false],
            ['key' => 'acciones', 'label' => 'Acciones',         'sortable' => false],
        ];

        return view('trabajadores-turno.index', compact('rows', 'columns'));
    }

    public function create(): View
    {
        return view('trabajadores-turno.create');
    }

    public function store(StoreTrabajadorTurnoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['activo'] = $request->boolean('activo');

        TrabajadorTurno::create($data);

        return redirect()
            ->route('trabajadores-turno.index')
            ->with('success', 'Trabajador de turno creado correctamente.');
    }

    public function edit(TrabajadorTurno $trabajadorTurno): View
    {
        return view('trabajadores-turno.edit', ['trabajador' => $trabajadorTurno]);
    }

    public function update(UpdateTrabajadorTurnoRequest $request, TrabajadorTurno $trabajadorTurno): RedirectResponse
    {
        $data = $request->validated();
        $data['activo'] = $request->boolean('activo');

        $trabajadorTurno->update($data);

        return redirect()
            ->route('trabajadores-turno.index')
            ->with('success', 'Trabajador actualizado correctamente.');
    }

    public function destroy(TrabajadorTurno $trabajadorTurno): RedirectResponse
    {
        $trabajadorTurno->delete();

        return redirect()
            ->route('trabajadores-turno.index')
            ->with('success', 'Trabajador eliminado.');
    }
}
