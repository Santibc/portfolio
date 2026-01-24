<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Services\AlertaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertaController extends Controller
{
    protected AlertaService $alertaService;

    public function __construct(AlertaService $alertaService)
    {
        $this->alertaService = $alertaService;
    }

    /**
     * Dashboard de alertas para el usuario autenticado
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $roles = $user->getRoleNames()->toArray();

        // Filtros
        $filtros = [
            'tipo' => $request->input('tipo'),
            'prioridad' => $request->input('prioridad'),
            'estado' => $request->input('estado'),
            'fecha_desde' => $request->input('fecha_desde'),
            'fecha_hasta' => $request->input('fecha_hasta'),
        ];

        // Obtener alertas
        $alertas = $this->alertaService->getAlertasParaUsuario($user->id, $roles, $filtros);

        // Estadísticas
        $stats = $this->alertaService->getEstadisticasParaUsuario($user->id, $roles);

        // Tipos de alerta para el filtro
        $tiposAlerta = AlertaService::TIPOS_ALERTA;

        return view('alertas.index', compact('alertas', 'stats', 'tiposAlerta', 'filtros'));
    }

    /**
     * Ver detalle de una alerta
     */
    public function show(Alerta $alerta)
    {
        $user = Auth::user();
        $roles = $user->getRoleNames()->toArray();

        // Verificar que el usuario tiene acceso a esta alerta
        $tieneAcceso = $alerta->para_usuario_id === $user->id;
        if (!$tieneAcceso && $alerta->para_roles) {
            foreach ($roles as $rol) {
                if (in_array($rol, $alerta->para_roles)) {
                    $tieneAcceso = true;
                    break;
                }
            }
        }

        if (!$tieneAcceso) {
            abort(403, 'No tienes acceso a esta alerta.');
        }

        // Cargar el registro relacionado
        $alerta->load('alertable');

        // Obtener información del registro relacionado
        $registroRelacionado = $this->getInfoRegistroRelacionado($alerta);

        return view('alertas.show', compact('alerta', 'registroRelacionado'));
    }

    /**
     * Marcar alerta como leída (AJAX)
     */
    public function marcarLeida(Alerta $alerta)
    {
        $alerta->marcarLeida();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alerta marcada como leída',
            ]);
        }

        return back()->with('success', 'Alerta marcada como leída.');
    }

    /**
     * Marcar alerta como resuelta (AJAX)
     */
    public function marcarResuelta(Alerta $alerta)
    {
        $alerta->marcarResuelta();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alerta marcada como resuelta',
            ]);
        }

        return back()->with('success', 'Alerta marcada como resuelta.');
    }

    /**
     * Marcar múltiples alertas como leídas (AJAX)
     */
    public function marcarLeidasMultiple(Request $request)
    {
        $request->validate([
            'alertas' => 'required|array',
            'alertas.*' => 'exists:alertas,id',
        ]);

        $count = Alerta::whereIn('id', $request->alertas)
            ->where('leida', false)
            ->update([
                'leida' => true,
                'fecha_lectura' => now(),
            ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} alertas marcadas como leídas",
                'count' => $count,
            ]);
        }

        return back()->with('success', "{$count} alertas marcadas como leídas.");
    }

    /**
     * Obtener contador de alertas no leídas (AJAX para badge)
     */
    public function contadorNoLeidas()
    {
        $user = Auth::user();
        $roles = $user->getRoleNames()->toArray();

        $count = $this->alertaService->contarNoLeidas($user->id, $roles);

        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * Obtener alertas recientes para dropdown del header (AJAX)
     */
    public function recientes()
    {
        $user = Auth::user();
        $roles = $user->getRoleNames()->toArray();

        $alertas = $this->alertaService->getAlertasRecientes($user->id, $roles, 5);

        return response()->json([
            'alertas' => $alertas->map(function ($alerta) {
                return [
                    'id' => $alerta->id,
                    'titulo' => $alerta->titulo,
                    'prioridad' => $alerta->prioridad,
                    'prioridad_color' => AlertaService::getPrioridadColor($alerta->prioridad),
                    'tipo_icono' => AlertaService::getTipoIcono($alerta->tipo),
                    'fecha' => $alerta->created_at ? $alerta->created_at->diffForHumans() : 'Ahora',
                    'url' => route('alertas.show', $alerta),
                ];
            }),
            'total_no_leidas' => $this->alertaService->contarNoLeidas($user->id, $roles),
        ]);
    }

    /**
     * Obtener información del registro relacionado para mostrar en la vista
     */
    protected function getInfoRegistroRelacionado(Alerta $alerta): array
    {
        $info = [
            'tipo' => 'Registro',
            'nombre' => 'No disponible',
            'url' => null,
            'detalles' => [],
        ];

        if (!$alerta->alertable) {
            return $info;
        }

        $registro = $alerta->alertable;

        switch ($alerta->alertable_type) {
            case 'App\Models\TrabajadorFormacion':
                $info['tipo'] = 'Formación';
                $info['nombre'] = $registro->formacionTipo->nombre ?? 'Formación';
                $info['url'] = route('trabajadores.show', $registro->trabajador_id);
                $info['detalles'] = [
                    'Trabajador' => $registro->trabajador->nombre_completo ?? 'N/A',
                    'Fecha realización' => $registro->fecha_realizacion?->format('d/m/Y'),
                    'Fecha caducidad' => $registro->fecha_caducidad?->format('d/m/Y'),
                ];
                break;

            case 'App\Models\TrabajadorDocumento':
                $info['tipo'] = 'Documento de Trabajador';
                $info['nombre'] = $registro->nombre;
                $info['url'] = route('trabajadores.show', $registro->trabajador_id);
                $info['detalles'] = [
                    'Trabajador' => $registro->trabajador->nombre_completo ?? 'N/A',
                    'Tipo' => ucfirst(str_replace('_', ' ', $registro->tipo)),
                    'Fecha caducidad' => $registro->fecha_caducidad?->format('d/m/Y'),
                ];
                break;

            case 'App\Models\EpiInventario':
                $info['tipo'] = 'EPI';
                $info['nombre'] = $registro->catalogo->nombre ?? 'EPI';
                $info['url'] = route('epi-inventario.show', $registro->id);
                $info['detalles'] = [
                    'Número de serie' => $registro->numero_serie ?? 'N/A',
                    'Estado' => ucfirst($registro->estado),
                    'Fecha caducidad' => $registro->fecha_caducidad?->format('d/m/Y'),
                ];
                break;

            case 'App\Models\EpiRevision':
                $info['tipo'] = 'Revisión de EPI';
                $info['nombre'] = $registro->epiInventario->catalogo->nombre ?? 'Revisión EPI';
                $info['url'] = route('epi-inventario.show', $registro->epi_inventario_id);
                $info['detalles'] = [
                    'Última revisión' => $registro->fecha_revision?->format('d/m/Y'),
                    'Próxima revisión' => $registro->proxima_revision?->format('d/m/Y'),
                    'Resultado' => ucfirst(str_replace('_', ' ', $registro->resultado)),
                ];
                break;

            case 'App\Models\Vehiculo':
                $info['tipo'] = 'Vehículo';
                $info['nombre'] = "{$registro->matricula} - {$registro->marca} {$registro->modelo}";
                $info['url'] = route('vehiculos.show', $registro->id);
                $info['detalles'] = [
                    'Matrícula' => $registro->matricula,
                    'Próxima ITV' => $registro->fecha_proxima_itv?->format('d/m/Y'),
                    'Vencimiento seguro' => $registro->fecha_vencimiento_seguro?->format('d/m/Y'),
                ];
                break;

            case 'App\Models\VehiculoDocumento':
                $info['tipo'] = 'Documento de Vehículo';
                $info['nombre'] = $registro->nombre;
                $info['url'] = route('vehiculos.show', $registro->vehiculo_id);
                $info['detalles'] = [
                    'Vehículo' => $registro->vehiculo->matricula ?? 'N/A',
                    'Tipo' => ucfirst(str_replace('_', ' ', $registro->tipo)),
                    'Fecha caducidad' => $registro->fecha_caducidad?->format('d/m/Y'),
                ];
                break;

            case 'App\Models\Contrato':
                $info['tipo'] = 'Contrato';
                $info['nombre'] = $registro->titulo;
                $info['url'] = route('contratos.show', $registro->id);
                $info['detalles'] = [
                    'Cliente' => $registro->cliente->nombre_comercial ?? 'N/A',
                    'Fecha inicio' => $registro->fecha_inicio?->format('d/m/Y'),
                    'Fecha fin' => $registro->fecha_fin?->format('d/m/Y'),
                    'Estado' => ucfirst($registro->estado),
                ];
                break;

            case 'App\Models\SubcontrataDocumentoCae':
                $info['tipo'] = 'Documento CAE';
                $info['nombre'] = $registro->tipo;
                $info['url'] = route('subcontratas.show', $registro->subcontrata_id);
                $info['detalles'] = [
                    'Subcontrata' => $registro->subcontrata->nombre ?? 'N/A',
                    'Fecha documento' => $registro->fecha_documento?->format('d/m/Y'),
                    'Fecha caducidad' => $registro->fecha_caducidad?->format('d/m/Y'),
                ];
                break;

            case 'App\Models\CaducidadGeneral':
                $info['tipo'] = 'Caducidad de Empresa';
                $info['nombre'] = $registro->nombre;
                $info['url'] = route('caducidades-generales.show', $registro->id);
                $info['detalles'] = [
                    'Tipo' => ucfirst(str_replace('_', ' ', $registro->tipo)),
                    'Fecha emisión' => $registro->fecha_emision?->format('d/m/Y'),
                    'Fecha caducidad' => $registro->fecha_caducidad?->format('d/m/Y'),
                ];
                break;
        }

        return $info;
    }
}
