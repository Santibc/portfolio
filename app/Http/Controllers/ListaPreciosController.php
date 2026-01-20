<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListaPrecio;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ListaPreciosController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ListaPrecio::select('listas_precios.*');

            return DataTables::of($query)
                ->addColumn('activo', fn($l) => $l->activo
                    ? '<span class="badge bg-success">Activa</span>'
                    : '<span class="badge bg-secondary">Inactiva</span>')
                ->addColumn('clientes_count', fn($l) => $l->clientes()->count())
                ->addColumn('action', function($l) {
                    $urlEdit = route('listas-precios.form', $l->id);
                    $toggleText = $l->activo ? 'Desactivar' : 'Activar';
                    $toggleClass = $l->activo ? 'btn-outline-warning' : 'btn-outline-success';
                    $toggleIcon = $l->activo ? 'bi-toggle-on' : 'bi-toggle-off';

                    return <<<HTML
<div class="d-flex justify-content-center gap-1">
  <a href="{$urlEdit}" class="btn btn-outline-info btn-sm" title="Editar">
    <i class="bi bi-pencil"></i>
  </a>
  <button type="button" class="btn {$toggleClass} btn-sm btn-toggle-estado"
          data-id="{$l->id}" data-activo="{$l->activo}" title="{$toggleText}">
    <i class="bi {$toggleIcon}"></i>
  </button>
</div>
HTML;
                })
                ->rawColumns(['action', 'activo'])
                ->make(true);
        }

        return view('listas-precios.index');
    }

    public function form(ListaPrecio $listaPrecio = null)
    {
        $listaPrecio = $listaPrecio ?? new ListaPrecio(['activo' => true]);
        return view('listas-precios.form', compact('listaPrecio'));
    }

    public function guardar(Request $request)
    {
        $listaPrecio = $request->id
                     ? ListaPrecio::findOrFail($request->id)
                     : new ListaPrecio();

        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'codigo' => [
                'required', 'string', 'max:50',
                Rule::unique('listas_precios')->ignore($listaPrecio->id)
            ],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'orden' => ['required', 'integer', 'min:0'],
        ];

        $messages = [
            'required' => 'Este campo es obligatorio.',
            'max' => 'No debe superar los :max caracteres.',
            'unique' => 'Ya existe una lista con este código.',
            'integer' => 'Debe ser un número entero.',
        ];

        $data = $request->validate($rules, $messages);

        // Manejar checkbox de activo
        $data['activo'] = $request->has('activo');

        $listaPrecio->fill($data)->save();

        return redirect()->route('listas-precios')
                         ->with('success', 'Lista de precios guardada correctamente.');
    }

    public function toggleEstado(ListaPrecio $listaPrecio)
    {
        // Verificar si tiene clientes asignados antes de desactivar
        if ($listaPrecio->activo && $listaPrecio->clientes()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede desactivar esta lista porque tiene clientes asignados.'
            ], 422);
        }

        $listaPrecio->activo = !$listaPrecio->activo;
        $listaPrecio->save();

        return response()->json([
            'success' => true,
            'message' => $listaPrecio->activo
                ? 'Lista de precios activada correctamente.'
                : 'Lista de precios desactivada correctamente.',
            'activo' => $listaPrecio->activo
        ]);
    }
}
