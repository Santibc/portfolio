<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametros;
use App\Services\Contracts\ApiClientFactoryInterface;
class HomeController extends Controller
{
    public function __construct(private readonly ApiClientFactoryInterface $apiFactory)
    {
    }
    public function index()
    {
          return view('dashboard');
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
