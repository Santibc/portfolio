<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CategoriasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Categoria::select('categorias.*')
                ->where('activo', true);

            return DataTables::of($query)
                ->addColumn('action', function($c) {
                    $urlEditar = route('categorias.form', $c->id);
                    $urlEliminar = route('categorias.eliminar', $c->id);
                    return <<<HTML
<div class="d-flex justify-content-center gap-1">
  <a href="{$urlEditar}" class="btn btn-outline-info btn-sm" title="Editar">
    <i class="bi bi-pencil"></i>
  </a>
  <button type="button" class="btn btn-outline-danger btn-sm" title="Eliminar" onclick="confirmarEliminar({$c->id}, '{$urlEliminar}')">
    <i class="bi bi-trash"></i>
  </button>
</div>
HTML;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('categorias.categorias_index');
    }

    public function form(Categoria $categoria = null)
    {
        $categoria = $categoria ?? new Categoria();
        return view('categorias.categorias_form', compact('categoria'));
    }

    public function guardar(Request $request)
    {
        $categoria = $request->id
                   ? Categoria::findOrFail($request->id)
                   : new Categoria();

        $rules = [
            'referencia'  => [
                'required','string','max:50',
                Rule::unique('categorias')->ignore($categoria->id)
            ],
            'nombre'      => ['required','string','max:255'],
            'slug'        => [
                'nullable','string','max:255',
                Rule::unique('categorias')->ignore($categoria->id)
            ],
            'descripcion' => ['nullable','string'],
            'orden'       => ['required','integer'],
        ];

        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'max'      => 'No debe superar los :max caracteres.',
            'unique'   => 'Ya existe una categoría con este valor.',
            'boolean'  => 'Valor inválido para :attribute.',
            'integer'  => 'El campo :attribute debe ser un número entero.',
        ];

        $data = $request->validate($rules, $messages);

        // Si no proporcionó slug, el Model lo genera en boot()
        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        $categoria->fill($data)->save();

        return redirect()->route('categorias')
                         ->with('success','Categoría guardada correctamente.');
    }

    public function eliminar(Categoria $categoria)
    {
        // Verificar si tiene productos asociados (activos)
        $productosActivos = $categoria->productos()->where('activo', true)->count();

        if ($productosActivos > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar la categoría porque tiene {$productosActivos} producto(s) asociado(s)."
            ], 422);
        }

        // Eliminación lógica
        $categoria->activo = false;
        $categoria->save();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada correctamente.'
        ]);
    }
}
