<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmpleadoRequest;
use App\Http\Requests\UpdateEmpleadoRequest;
use App\Models\Empleado;
use App\Models\MetodoPago;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmpleadoController extends Controller
{
    public function index(): View
    {
        $empleados = Empleado::with('metodoPago')
            ->withSum('detalles as total_ahorrado', 'ahorro')
            ->withSum('pagosAhorroNomina as total_pagado_ahorro', 'monto')
            ->orderBy('nombre')
            ->get();

        $rows = $empleados->map(function (Empleado $e) {
            $acumulado = $e->ahorro_acumulado;
            $editUrl = route('empleados.edit', $e);
            $toggleUrl = route('empleados.toggle-activo', $e);
            $destroyUrl = route('empleados.destroy', $e);
            $csrf = csrf_token();

            $estadoTexto = $e->activo ? 'Activo' : 'Inactivo';
            $activo = '<form action="'.$toggleUrl.'" method="POST" class="inline-flex items-center gap-2">'
                .'<input type="hidden" name="_token" value="'.$csrf.'">'
                .'<input type="hidden" name="_method" value="PATCH">'
                .'<label class="relative inline-flex items-center cursor-pointer" title="'.($e->activo ? 'Desactivar' : 'Activar').'">'
                .'<input type="checkbox" onchange="this.form.submit()" '.($e->activo ? 'checked' : '').' class="sr-only peer">'
                .'<span class="w-11 h-6 rounded-full bg-cream-300 peer-checked:bg-primary-500 transition-colors duration-200 dark:bg-cream-700"></span>'
                .'<span class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform duration-200 peer-checked:translate-x-5"></span>'
                .'</label>'
                .'<span class="text-xs font-medium '.($e->activo ? 'text-emerald-700 dark:text-emerald-300' : 'text-cream-500').'">'.$estadoTexto.'</span>'
                .'</form>';

            $acciones = '<div class="inline-flex items-center gap-3 flex-wrap">'
                .'<a href="'.$editUrl.'" class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium"><i data-lucide="edit" class="w-3.5 h-3.5"></i>Editar</a>'
                .'<form action="'.$destroyUrl.'" method="POST" onsubmit="return confirm(\'¿Eliminar este empleado? Se ocultará de las listas.\');" class="inline">'
                .'<input type="hidden" name="_token" value="'.$csrf.'">'
                .'<input type="hidden" name="_method" value="DELETE">'
                .'<button type="submit" class="inline-flex items-center gap-1 text-rose-700 hover:text-rose-900 dark:text-rose-300 dark:hover:text-rose-100 font-medium"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i>Eliminar</button>'
                .'</form>'
                .'</div>';

            return [
                'nombre' => '<div class="font-medium text-cream-900 dark:text-cream-50">'.e($e->nombre).'</div><div class="text-xs text-cream-500">'.e($e->cargo ?? '—').'</div>',
                'documento' => '<span class="tabular-nums text-cream-700 dark:text-cream-300">'.e($e->documento).'</span>',
                'salario' => '<span class="font-semibold tabular-nums text-cream-900 dark:text-cream-50">'.e($e->salario_base_formateado).'</span>',
                'auxilio' => '<span class="tabular-nums text-cream-700 dark:text-cream-300">'.e($e->tiene_auxilio ? $e->auxilio_transporte_formateado : '—').'</span>',
                'ahorro' => '<span class="font-semibold tabular-nums '.($acumulado > 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-cream-500').'">'.e($e->ahorro_acumulado_formateado).'</span>',
                'activo' => $activo,
                'acciones' => $acciones,
            ];
        })->values()->all();

        $columns = [
            ['key' => 'nombre',    'label' => 'Empleado',         'sortable' => true],
            ['key' => 'documento', 'label' => 'Documento',        'sortable' => true],
            ['key' => 'salario',   'label' => 'Salario base',     'sortable' => true],
            ['key' => 'auxilio',   'label' => 'Auxilio',          'sortable' => false],
            ['key' => 'ahorro',    'label' => 'Ahorro acumulado', 'sortable' => true],
            ['key' => 'activo',    'label' => 'Estado',           'sortable' => false],
            ['key' => 'acciones',  'label' => 'Acciones',         'sortable' => false],
        ];

        return view('empleados.index', compact('rows', 'columns'));
    }

    public function create(): View
    {
        return view('empleados.create', [
            'metodosOptions' => $this->metodosOptions(),
            'auxilioDefault' => (int) config('nomina.auxilio_transporte'),
            'pctSalud' => (int) config('nomina.porcentaje_salud'),
            'pctPension' => (int) config('nomina.porcentaje_pension'),
        ]);
    }

    public function store(StoreEmpleadoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tiene_auxilio'] = $request->boolean('tiene_auxilio');
        $data['activo'] = $request->boolean('activo');

        Empleado::create($data);

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    public function edit(Empleado $empleado): View
    {
        return view('empleados.edit', [
            'empleado' => $empleado,
            'metodosOptions' => $this->metodosOptions(),
        ]);
    }

    public function update(UpdateEmpleadoRequest $request, Empleado $empleado): RedirectResponse
    {
        $data = $request->validated();
        $data['tiene_auxilio'] = $request->boolean('tiene_auxilio');
        $data['activo'] = $request->boolean('activo');

        $empleado->update($data);

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado): RedirectResponse
    {
        $empleado->delete();

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado eliminado.');
    }

    public function toggleActivo(Empleado $empleado): RedirectResponse
    {
        $empleado->update(['activo' => ! $empleado->activo]);

        return redirect()
            ->route('empleados.index')
            ->with('success', $empleado->activo
                ? 'Empleado activado. Entrará en las próximas liquidaciones de nómina.'
                : 'Empleado desactivado. No entrará en las próximas liquidaciones.');
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
