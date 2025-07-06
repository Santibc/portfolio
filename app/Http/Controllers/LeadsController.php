<?php

namespace App\Http\Controllers;

use App\Services\LeadSynchronizationService;
use App\Services\CalendlyEventImporter;
use App\Services\Contracts\ApiClientFactoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Lead;
use Yajra\DataTables\Facades\DataTables;
class LeadsController extends Controller
{
    protected $apiFactory;

    public function __construct(ApiClientFactoryInterface $apiFactory)
    {
        $this->apiFactory = $apiFactory;
    }
    /**
     * Inicia el proceso de importación de leads y llamadas 
     * para el usuario autenticado.
     */
       public function importar_leads($id_usuario = null)
    {
          set_time_limit(1000); // ← importante

        $user_to_sync = $id_usuario ? User::find($id_usuario) : Auth::user();

        if (!$user_to_sync) {
            return response()->json(['message' => 'No autorizado.'], 401);
        }

        $eventImporter = new CalendlyEventImporter($this->apiFactory);
        $syncService = new LeadSynchronizationService($eventImporter);
        $report = $syncService->synchronizeLeadsAndCalls($user_to_sync);

        return response()->json([
            'message' => 'Proceso de importación de leads y llamadas completado.',
            'imported_calls' => $report['imported'],
            'skipped_calls' => $report['skipped'],
            'errors' => $report['errors'],
        ]);
    }

    /**
     * Muestra una lista de los leads que pertenecen al closer autenticado.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(auth()->user()->getRoleNames()->first()=='admin'){
                $leads = Lead::query();
                
            }else{
                $leads = Lead::query()->where('user_id', Auth::user()->id);
            }

            return DataTables::of($leads)
                ->addColumn('action', function ($lead) {
                    $editUrl = route('leads.form', $lead->id);

                    $buttons = '<div class="d-flex justify-content-center align-items-center">';
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-outline-info btn-sm" title="Editar">';
                    $buttons .= '<i class="bi bi-pencil"></i>';
                    $buttons .= '</a>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('leads.leads_index');
    }
}