<?php

namespace App\Http\Controllers;

use App\Services\LeadSynchronizationService;
use App\Services\CalendlyEventImporter;
use App\Services\Contracts\ApiClientFactoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Lead;
use App\Models\Llamada;
use Yajra\DataTables\Facades\DataTables;
class LlamadasController extends Controller
{

    /**
     * Muestra una lista de los leads que pertenecen al closer autenticado.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(auth()->user()->getRoleNames()->first()=='admin'){
                $llamadas = Llamada::query();
                
            }else{
                $llamadas = Llamada::query()->where('user_id', Auth::user()->id);
            }

            return DataTables::of($llamadas)
                ->addColumn('action', function ($llamada) {
                    $editUrl = route('llamadas.form', $llamada->id);

                    $buttons = '<div class="d-flex flex-column align-items-center gap-1">';
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-outline-info btn-sm" title="Editar">';
                    $buttons .= '<i class="bi bi-pencil"></i> </a>';

                    $buttons .= '<button class="btn btn-outline-primary btn-sm ver-respuestas-btn" 
                                        data-id="' . $llamada->id . '" 
                                        title="Ver respuestas">';
                    $buttons .= '<i class="bi bi-chat-dots"></i></button>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('llamadas.llamadas_index');
    }
    public function respuestasJson($id)
    {
        $respuestas = Llamada::with('respuestas')->findOrFail($id)->respuestas;

        return response()->json($respuestas);
    }
}