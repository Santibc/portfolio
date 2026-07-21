<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class AlmacenController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('admin')) {
                abort(403, 'No autorizado.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Almacen::withCount('vendedores')->select('almacenes.*');

            return DataTables::of($query)
                ->addColumn('vendedores_count', fn($a) => $a->vendedores_count)
                ->addColumn('activo_label', fn($a) => $a->activo ? 'Sí' : 'No')
                ->addColumn('action', function ($a) {
                    $url = route('almacenes.form', $a->id);
                    return <<<HTML
<div class="d-flex justify-content-center gap-1">
  <a href="{$url}" class="btn btn-outline-info btn-sm" title="Editar">
    <i class="bi bi-pencil"></i>
  </a>
</div>
HTML;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('almacenes.almacenes_index');
    }

    public function form(Almacen $almacen = null)
    {
        $almacen = $almacen ?? new Almacen();

        $vendedoresAsignados = $almacen->exists
            ? User::where('almacen_id', $almacen->id)->orderBy('name')->get(['id', 'name', 'email'])
            : collect();

        $vendedoresDisponibles = User::role('vendedor')
            ->where(function ($q) use ($almacen) {
                $q->whereNull('almacen_id');
                if ($almacen->exists) {
                    $q->orWhere('almacen_id', $almacen->id);
                }
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('almacenes.almacenes_form', compact('almacen', 'vendedoresAsignados', 'vendedoresDisponibles'));
    }

    public function guardar(Request $request)
    {
        $almacen = $request->id
            ? Almacen::findOrFail($request->id)
            : new Almacen();

        $rules = [
            'codigo' => [
                'required', 'string', 'max:50',
                Rule::unique('almacenes')->ignore($almacen->id),
            ],
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'activo' => ['sometimes', 'boolean'],
            'vendedores' => ['nullable', 'array'],
            'vendedores.*' => ['integer', 'exists:users,id'],
        ];

        $data = $request->validate($rules, [
            'required' => 'Este campo es obligatorio.',
            'unique' => 'Ya existe un almacén con este código.',
            'max' => 'No debe superar los :max caracteres.',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $vendedoresIds = collect($data['vendedores'] ?? [])->map(fn($id) => (int) $id)->all();

        unset($data['vendedores']);
        $almacen->fill($data)->save();

        User::where('almacen_id', $almacen->id)
            ->whereNotIn('id', $vendedoresIds)
            ->update(['almacen_id' => null]);

        if (!empty($vendedoresIds)) {
            User::whereIn('id', $vendedoresIds)->update(['almacen_id' => $almacen->id]);
        }

        return redirect()->route('almacenes')
            ->with('success', 'Almacén guardado correctamente.');
    }
}
