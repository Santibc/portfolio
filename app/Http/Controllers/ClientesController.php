<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\User;
use App\Models\ListaPrecio;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Departamento;
class ClientesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Cliente::with(['vendedor', 'listaPrecio',
                        'pais',        // <-- relación país
            'ciudad',      // <-- relación ciudad
            ])->select('clientes.*');

            return DataTables::of($query)
                        ->addColumn('pais', fn($c) => $c->pais?->nombre)
            ->addColumn('ciudad', fn($c) => $c->ciudad?->nombre)
                ->addColumn('vendedor', fn($c) => $c->vendedor?->name)
                ->addColumn('lista_precio', fn($c) => $c->listaPrecio?->nombre)
                ->addColumn('activo', fn($c) => $c->activo ? 'Sí' : 'No')
                ->addColumn('action', function($c) {
                    $url = route('clientes.form', $c->id);
                    return <<<HTML
<div class="d-flex justify-content-center">
  <a href="{$url}" class="btn btn-outline-info btn-sm" title="Editar">
    <i class="bi bi-pencil"></i>
  </a>
</div>
HTML;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('clientes.clientes_index');
    }

    public function form(Cliente $cliente = null)
    {
        $cliente    = $cliente ?? new Cliente();
        $vendedores = User::role('vendedor')->pluck('name', 'id');
        $listas     = ListaPrecio::activas()->pluck('nombre', 'id');
            $departamentos = Departamento::orderBy('nombre')->pluck('nombre','id');
    $pais_id       = 1;

    return view('clientes.clientes_form', compact(
        'cliente','departamentos','vendedores','listas','pais_id'
    ));

    }

    public function guardar(Request $request)
    {
        $cliente = $request->id
                 ? Cliente::findOrFail($request->id)
                 : new Cliente();

$rules = [
    'numero_identificacion' => [
        'required','string','max:255',
        Rule::unique('clientes')->ignore($cliente->id)
    ],
    'nombre_contacto'  => ['required','string','max:255'],
    'email'            => [
        'required','email','max:255',
        Rule::unique('clientes')->ignore($cliente->id)
    ],
    'telefono'         => ['nullable','string','max:100'],
    'pais_id'          => ['required','exists:paises,id'],        // ← Aquí
    'departamento_id'  => ['required','exists:departamentos,id'],
    'ciudad_id'        => ['required','exists:ciudades,id'],
    'vendedor_id'      => ['required','exists:users,id'],
    'lista_precio_id'  => ['required','exists:listas_precios,id'],
];


        $messages = [
            'required' => 'Este campo es obligatorio.',
            'email'    => 'Debe ser un correo válido.',
            'max'      => 'No debe superar los :max caracteres.',
            'unique'   => 'Ya existe un registro con este valor.',
            'exists'   => 'El valor seleccionado no es válido.',
        ];

        $data = $request->validate($rules,$messages);



    $cliente->fill($data)->save();

        return redirect()->route('clientes')
                         ->with('success','Cliente guardado correctamente.');
    }
}
