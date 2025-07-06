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
        $llamadas = Llamada::query()->orderByDesc('id');;

        if (!auth()->user()->hasRole('admin')) {
            $llamadas->where('user_id', Auth::id());
        }

        // Filtro por lead_id (si se recibe)
        if ($request->has('lead_id') && is_numeric($request->lead_id)) {
            $llamadas->where('lead_id', $request->lead_id);
        }

        return DataTables::of($llamadas)
            ->addColumn('action', function ($llamada) {
                $editUrl = route('llamadas.form', $llamada->id);
                return '<div class="d-flex flex-column align-items-center gap-1">
                            <a href="' . $editUrl . '" class="btn btn-outline-info btn-sm" title="Editar"><i class="bi bi-pencil"></i></a>
                            <button class="btn btn-outline-primary btn-sm ver-respuestas-btn" data-id="' . $llamada->id . '" title="Ver respuestas">
                                <i class="bi bi-chat-dots"></i>
                            </button>
                        </div>';
            })
            ->addColumn('lead', function ($llamada) {
                $lead = $llamada->lead;
                if (!$lead) return '-';
                return '<button class="btn btn-sm btn-outline-dark ver-lead-btn" data-id="' . $lead->id . '"><i class="bi bi-person-circle"></i></button>';
            })
            ->rawColumns(['action', 'lead'])
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