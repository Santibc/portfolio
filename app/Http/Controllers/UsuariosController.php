<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\Parametros;
use App\Models\User;
use App\Services\Contracts\ApiClientFactoryInterface;
class UsuariosController extends Controller
{
    public function __construct(private readonly ApiClientFactoryInterface $apiFactory)
    {
    }
    public function index(Request $request)
    {
        
        if ($request->ajax()) {

            $users = User::query(); 

            return DataTables::of($users)
            ->addColumn('action', function ($user) {
                // Aquí puedes agregar botones de acción (editar, eliminar, etc.)
                return '<a href="#edit" class="text-blue-500 hover:text-blue-700">Editar</a>';
            })
            ->rawColumns(['action']) // Indicar a DataTables que la columna 'action' contiene HTML
            ->make(true);
        }

        return view('usuarios.usuarios_index');

        
        $url_organizacion = Parametros::Where('nombre_parametro', 'url_organizacion')->first();
        $token_organizacion = Parametros::Where('nombre_parametro', 'token_organizacion')->first();
        $organizacion = Parametros::Where('nombre_parametro', 'organizacion')->first();
        
        $apiClient = $this->apiFactory->createData(
            $url_organizacion->valor_parametro,
            $token_organizacion->valor_parametro
        );
        $usuarios = $apiClient->get('organization_memberships', [
            'organization' => $organizacion->valor_parametro
        ]);


        
        dd($usuarios);


        
    }
}
