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
            $query = Categoria::withCount('productos')->select('categorias.*');

            return DataTables::of($query)
                ->addColumn('activo', fn($c) => $c->activo
                    ? '<span class="badge bg-success">Sí</span>'
                    : '<span class="badge bg-secondary">No</span>')
                ->addColumn('action', function($c) {
                    $editUrl   = route('categorias.form', $c->id);
                    $toggleUrl = route('categorias.toggle-activo', $c->id);
                    $deleteUrl = route('categorias.eliminar', $c->id);
                    $csrf      = csrf_token();

                    $toggleIcon  = $c->activo ? 'bi-toggle-on'        : 'bi-toggle-off';
                    $toggleClass = $c->activo ? 'btn-outline-warning' : 'btn-outline-success';
                    $toggleTitle = $c->activo ? 'Inactivar'           : 'Activar';

                    $tieneProductos = ($c->productos_count ?? 0) > 0;
                    $deleteDisabled = $tieneProductos ? 'disabled' : '';
                    $deleteTitle    = $tieneProductos
                        ? 'No se puede eliminar: tiene ' . $c->productos_count . ' producto(s) asociado(s)'
                        : 'Eliminar';
                    $confirmMsg = '¿Eliminar esta categoría? Esta acción no se puede deshacer.';

                    $html  = '<div class="d-flex justify-content-center gap-1">';
                    $html .= '<a href="'.$editUrl.'" class="btn btn-outline-info btn-sm" title="Editar"><i class="bi bi-pencil"></i></a>';

                    $html .= '<form method="POST" action="'.$toggleUrl.'" style="display:inline">';
                    $html .= '<input type="hidden" name="_token" value="'.$csrf.'">';
                    $html .= '<button type="submit" class="btn '.$toggleClass.' btn-sm" title="'.$toggleTitle.'"><i class="bi '.$toggleIcon.'"></i></button>';
                    $html .= '</form>';

                    $html .= '<form method="POST" action="'.$deleteUrl.'" style="display:inline" onsubmit="return confirm(\''.$confirmMsg.'\');">';
                    $html .= '<input type="hidden" name="_token" value="'.$csrf.'">';
                    $html .= '<input type="hidden" name="_method" value="DELETE">';
                    $html .= '<button type="submit" class="btn btn-outline-danger btn-sm" title="'.$deleteTitle.'" '.$deleteDisabled.'><i class="bi bi-trash"></i></button>';
                    $html .= '</form>';

                    $html .= '</div>';

                    return $html;
                })
                ->rawColumns(['action', 'activo'])
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

    public function toggleActivo(Categoria $categoria)
    {
        $categoria->update(['activo' => ! $categoria->activo]);

        $msg = $categoria->activo ? 'Categoría activada.' : 'Categoría inactivada.';
        return back()->with('success', $msg);
    }

    public function eliminar(Categoria $categoria)
    {
        if ($categoria->productos()->exists()) {
            return back()->with('error',
                'No se puede eliminar la categoría "'.$categoria->nombre.'": tiene productos asociados. Inactívala en lugar de eliminarla.'
            );
        }

        $categoria->delete();
        return redirect()->route('categorias')->with('success', 'Categoría eliminada.');
    }
}
