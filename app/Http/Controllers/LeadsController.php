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
                $leads = Lead::query()->orderByDesc('id');;
                
            }else{
                $leads = Lead::query()->where('user_id', Auth::user()->id)->orderByDesc('id');;
            }

            return DataTables::of($leads)
                ->addColumn('action', function ($lead) {
                    $editUrl = route('leads.form', $lead->id);
                    $llamadasUrl = route('llamadas', ['lead_id' => $lead->id]);

                    $buttons = '<div class="d-flex justify-content-center gap-1">';
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-outline-info btn-sm" title="Editar"><i class="bi bi-pencil"></i></a>';
                    $buttons .= '<a href="' . $llamadasUrl . '" class="btn btn-outline-secondary btn-sm" title="Ver llamadas"><i class="bi bi-telephone"></i></a>';
                    $buttons .= '</div>';

                    return $buttons;
                })

                ->rawColumns(['action'])
                ->make(true);
        }
        if (!$request->ajax()) {
            if (auth()->user()->hasRole('admin')) {
                // 🔄 Importar leads para todos los usuarios (excepto el admin id=1 si quieres excluirlo)
                $usuarios = User::where('id', '!=', 1)->get();

                foreach ($usuarios as $usuario) {
                    $this->importar_leads($usuario->id);
                }
            } else {
                // 🔄 Importar solo para el usuario autenticado
                $this->importar_leads(Auth::id());
            }
        }
        return view('leads.leads_index');
    }
    public function infoJson($id)
{
    $lead = Lead::findOrFail($id);
    return response()->json($lead);
}

}