<?php

namespace App\Http\Controllers;

use App\Services\LeadSynchronizationService;
use App\Services\CalendlyEventImporter;
use App\Services\Contracts\ApiClientFactoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Log;
use App\Models\PipelineStatus;
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
            // Construir la consulta base una sola vez
            $query = Lead::with('pipelineStatus', 'user')->orderByDesc('id');

            // Aplicar filtro por rol al query existente
            if (auth()->user()->getRoleNames()->first() !== 'admin') {
                $query->where('user_id', Auth::id());
            }
            
            $pipelineStatuses = PipelineStatus::all();

            return DataTables::of($query)
                ->addColumn('action', function ($lead) {
                    $llamadasUrl = route('llamadas', ['lead_id' => $lead->id]);
                    $buttons = '<div class="d-flex justify-content-start  gap-1">';
                    $buttons .= '<a href="' . $llamadasUrl . '" class="btn btn-outline-secondary btn-sm" title="Ver llamadas"><i class="bi bi-telephone"></i></a>';
                    $buttons .= '<button type="button" class="btn btn-outline-info btn-sm view-logs-btn" 
                        data-lead-id="' . $lead->id . '" title="Ver Historial de Cambios">
                        <i class="bi bi-clock-history"></i>
                    </button>';     
                    
                    if ($lead->sale) {
                        // Ya tiene una venta registrada: botón para ver modal
                        $buttons .= '<button type="button" class="btn btn-outline-primary btn-sm view-sale-btn" 
                            data-lead-id="' . $lead->id . '"
                            data-nombre="' . e($lead->sale->nombre_cliente) . '"
                            data-apellido="' . e($lead->sale->apellido_cliente) . '"
                            data-email="' . e($lead->sale->email_cliente) . '"
                            data-telefono="' . e($lead->sale->telefono_cliente) . '"
                            data-identificacion="' . e($lead->sale->identificacion_personal) . '"
                            data-domicilio="' . e($lead->sale->domicilio) . '"
                            data-metodo_pago="' . e($lead->sale->metodo_pago) . '"
                            data-tipo_acuerdo="' . e($lead->sale->tipo_acuerdo) . '"
                            data-comentarios="' . e($lead->sale->comentarios) . '"
                            data-comprobante="' . asset($lead->sale->comprobante_pago_path) . '"
                            title="Ver Detalles de la Venta">
                            <i class="bi bi-eye"></i>
                        </button>';
                    } elseif ($lead->pipelineStatus && $lead->pipelineStatus->name == 'Cerrada/Venta hecha') {
                        // Si no tiene venta pero está en estado de cierre: botón para registrar
                        $buttons .= '<a href="' . route('sales.form', $lead->id) . '" class="btn btn-outline-success btn-sm" title="Registrar Venta"><i class="bi bi-file-earmark-text"></i></a>';
                    }
                    
                    $buttons .= '</div>';
                    return $buttons;
                })
                ->addColumn('pipeline_status', function ($lead) use ($pipelineStatuses) {
                    $options = '';
                    foreach ($pipelineStatuses as $status) {
                        $selected = $lead->pipeline_status_id == $status->id ? 'selected' : '';
                        $options .= '<option value="' . $status->id . '" ' . $selected . '>' . $status->name . '</option>';
                    }
                    return '<select class="form-select form-select-sm pipeline-status-select" data-lead-id="' . $lead->id . '">' . $options . '</select>';
                })
                ->filterColumn('pipeline_status', function($query, $keyword) {
                    $query->whereHas('pipelineStatus', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['action', 'pipeline_status'])
                ->make(true);
        }

        if (!$request->ajax()) {
            if (auth()->user()->hasRole('admin')) {
                $usuarios = User::where('id', '!=', 1)->get();
                foreach ($usuarios as $usuario) {
                    $this->importar_leads($usuario->id);
                }
            } else {
                $this->importar_leads(Auth::id());
            }
        }
        
        return view('leads.leads_index');
    }

    public function updatePipelineStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|exists:pipeline_statuses,id',
            'comentario' => 'nullable|string',
        ]);

        $lead = Lead::findOrFail($id);
        $oldStatus = $lead->pipelineStatus->name;
        
        $lead->pipeline_status_id = $request->status_id;
        $lead->save();

        $newStatus = $lead->fresh()->pipelineStatus->name;

        Log::create([
            'id_tabla' => $lead->id,
            'tabla' => 'leads',
            'detalle' => $request->comentario ?? 'Cambio de estado sin comentario.',
            'valor_viejo' => $oldStatus,
            'valor_nuevo' => $newStatus,
            'id_usuario' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Estado del lead actualizado.']);
    }

    public function infoJson($id)
    {
        $lead = Lead::findOrFail($id);
        return response()->json($lead);
    }

    public function logs($id)
    {
        $lead = Lead::with(['logs.usuario'])->findOrFail($id);

        return response()->json($lead->logs->map(function ($log) {
            return [
                'estado_anterior' => $log->valor_viejo,
                'estado_nuevo' => $log->valor_nuevo,
                'comentario' => $log->detalle,
                'usuario' => $log->usuario->name ?? 'Desconocido',
                'fecha' => $log->created_at->format('Y-m-d H:i'),
            ];
        }));
    }
}