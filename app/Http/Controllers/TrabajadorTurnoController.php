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
        $trabajadores = TrabajadorTurno::withSum('gastos as total_ahorrado', 'ahorro')
            ->withSum('pagosAhorro as total_pagado_ahorro', 'monto')
            ->orderBy('nombre')
            ->get();

        $rows = $trabajadores->map(function (TrabajadorTurno $t) {
            $acumulado = $t->ahorro_acumulado;
            $nombre = e($t->nombre);

            $editUrl = route('trabajadores-turno.edit', $t);
            $toggleUrl = route('trabajadores-turno.toggle-activo', $t);
            $csrf = csrf_token();

            $estadoTexto = $t->activo ? 'Activo' : 'Inactivo';
            $activo = '<form action="'.$toggleUrl.'" method="POST" class="inline-flex items-center gap-2">'
                .'<input type="hidden" name="_token" value="'.$csrf.'">'
                .'<input type="hidden" name="_method" value="PATCH">'
                .'<label class="relative inline-flex items-center cursor-pointer" title="'.($t->activo ? 'Desactivar' : 'Activar').'">'
                .'<input type="checkbox" onchange="this.form.submit()" '.($t->activo ? 'checked' : '').' class="sr-only peer">'
                .'<span class="w-11 h-6 rounded-full bg-cream-300 peer-checked:bg-primary-500 transition-colors duration-200 dark:bg-cream-700"></span>'
                .'<span class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform duration-200 peer-checked:translate-x-5"></span>'
                .'</label>'
                .'<span class="text-xs font-medium '.($t->activo ? 'text-emerald-700 dark:text-emerald-300' : 'text-cream-500').'">'.$estadoTexto.'</span>'
                .'</form>';

            $pagarBtn = '<button type="button"'
                .' data-id="'.$t->id.'" data-nombre="'.$nombre.'" data-acumulado="'.$acumulado.'"'
                .' onclick="window.dispatchEvent(new CustomEvent(\'abrir-pago-ahorro\',{detail:{id:this.dataset.id,nombre:this.dataset.nombre,acumulado:this.dataset.acumulado}}))"'
                .($acumulado > 0 ? '' : ' disabled')
                .' class="inline-flex items-center gap-1 font-medium '.($acumulado > 0 ? 'text-emerald-700 hover:text-emerald-900 dark:text-emerald-300 dark:hover:text-emerald-100' : 'text-cream-400 cursor-not-allowed').'">'
                .'<i data-lucide="banknote" class="w-3.5 h-3.5"></i>Pagar ahorro</button>';

            $historialBtn = '<button type="button"'
                .' data-id="'.$t->id.'" data-nombre="'.$nombre.'"'
                .' onclick="window.dispatchEvent(new CustomEvent(\'abrir-historial-ahorro\',{detail:{id:this.dataset.id,nombre:this.dataset.nombre}}))"'
                .' class="inline-flex items-center gap-1 text-sky-700 hover:text-sky-900 dark:text-sky-300 dark:hover:text-sky-100 font-medium">'
                .'<i data-lucide="clock" class="w-3.5 h-3.5"></i>Historial</button>';

            $acciones = '<div class="inline-flex items-center gap-3 flex-wrap">'
                .'<a href="'.$editUrl.'" class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium"><i data-lucide="edit" class="w-3.5 h-3.5"></i>Editar</a>'
                .$pagarBtn
                .$historialBtn
                .'</div>';

            return [
                'nombre' => $nombre,
                'valor' => '<span class="font-semibold tabular-nums text-cream-900 dark:text-cream-50">'.e($t->valor_turno_default_formateado).'</span>',
                'ahorro_default' => '<span class="font-semibold tabular-nums text-cream-900 dark:text-cream-50">'.e($t->valor_ahorro_default_formateado).'</span>',
                'ahorro' => '<span class="font-semibold tabular-nums '.($acumulado > 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-cream-500').'">'.e($t->ahorro_acumulado_formateado).'</span>',
                'activo' => $activo,
                'acciones' => $acciones,
            ];
        })->values()->all();

        $columns = [
            ['key' => 'nombre',         'label' => 'Nombre',              'sortable' => true],
            ['key' => 'valor',          'label' => 'Valor turno default', 'sortable' => true],
            ['key' => 'ahorro_default', 'label' => 'Valor ahorro default', 'sortable' => true],
            ['key' => 'ahorro',         'label' => 'Ahorro acumulado',    'sortable' => true],
            ['key' => 'activo',         'label' => 'Estado',              'sortable' => false],
            ['key' => 'acciones',       'label' => 'Acciones',            'sortable' => false],
        ];

        return view('trabajadores-turno.index', compact('rows', 'columns'));
    }

    public function historialAhorro(TrabajadorTurno $trabajadorTurno): View
    {
        $aportes = $trabajadorTurno->gastos()
            ->where('ahorro', '>', 0)
            ->get()
            ->map(fn ($g) => [
                'fecha' => $g->created_at,
                'tipo' => 'aporte',
                'monto' => (int) $g->ahorro,
                'detalle' => 'Ahorro registrado en pago de turno',
            ]);

        $pagos = $trabajadorTurno->pagosAhorro()
            ->get()
            ->map(fn ($p) => [
                'fecha' => $p->pagado_en,
                'tipo' => 'pago',
                'monto' => (int) $p->monto,
                'detalle' => $p->observacion ?: 'Pago de ahorro',
            ]);

        $movimientos = $aportes->concat($pagos)
            ->sortByDesc('fecha')
            ->values();

        return view('trabajadores-turno._historial-ahorro', [
            'trabajador' => $trabajadorTurno,
            'movimientos' => $movimientos,
        ]);
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

    public function toggleActivo(TrabajadorTurno $trabajadorTurno): RedirectResponse
    {
        $trabajadorTurno->update(['activo' => ! $trabajadorTurno->activo]);

        return redirect()
            ->route('trabajadores-turno.index')
            ->with('success', $trabajadorTurno->activo
                ? 'Trabajador activado. Volverá a aparecer en el formulario de gastos.'
                : 'Trabajador desactivado. Ya no aparecerá en el formulario de gastos.');
    }
}
