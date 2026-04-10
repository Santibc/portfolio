<?php

namespace App\Http\Controllers;

use App\Models\RegistroActividad;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ActividadController extends Controller
{
    /**
     * GET /[rol]/actividades - Vista personal de actividades.
     */
    public function personal(Request $request)
    {
        if ($request->ajax()) {
            $query = RegistroActividad::where('usuario_id', auth()->id())
                ->with('orden')
                ->select('registro_actividades.*');

            // Filtros
            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }
            if ($request->filled('accion')) {
                $query->where('accion', $request->accion);
            }

            return DataTables::of($query)
                ->addColumn('fecha_formatted', function ($r) {
                    return $r->created_at->format('d/m/Y H:i');
                })
                ->addColumn('accion_badge', function ($r) {
                    return RegistroActividad::badgeAccion($r->accion);
                })
                ->addColumn('orden_link', function ($r) {
                    if (!$r->orden_id || !$r->orden) {
                        return '<span class="text-muted">-</span>';
                    }
                    $url = route('recepcion.ordenes.show', $r->orden_id);
                    return '<a href="' . $url . '" class="fw-semibold text-decoration-none">'
                        . ($r->orden->numero_orden ?? "#{$r->orden_id}")
                        . '</a>';
                })
                ->addColumn('detalle_btn', function ($r) {
                    if (empty($r->datos_extra)) {
                        return '<span class="text-muted">-</span>';
                    }
                    $payload = htmlspecialchars(json_encode($r->datos_extra), ENT_QUOTES, 'UTF-8');
                    $accion = e(RegistroActividad::TIPOS_ACCION[$r->accion] ?? $r->accion);
                    $fecha = $r->created_at->format('d/m/Y H:i');
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-ver-detalle"'
                        . ' data-detalle="' . $payload . '"'
                        . ' data-accion="' . $accion . '"'
                        . ' data-fecha="' . $fecha . '"'
                        . ' title="Ver detalle del cambio"><i class="bi bi-eye"></i></button>';
                })
                ->orderColumn('fecha_formatted', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['accion_badge', 'orden_link', 'detalle_btn'])
                ->make(true);
        }

        // Stats
        $userId = auth()->id();
        $total = RegistroActividad::where('usuario_id', $userId)->count();
        $hoy = RegistroActividad::where('usuario_id', $userId)->whereDate('created_at', today())->count();

        $accionFrecuente = RegistroActividad::where('usuario_id', $userId)
            ->selectRaw('accion, count(*) as total')
            ->groupBy('accion')
            ->orderByDesc('total')
            ->first();

        $ultimaActividad = RegistroActividad::where('usuario_id', $userId)
            ->latest('created_at')
            ->first();

        $stats = [
            'total' => $total,
            'hoy' => $hoy,
            'accion_frecuente' => $accionFrecuente
                ? (RegistroActividad::TIPOS_ACCION[$accionFrecuente->accion] ?? $accionFrecuente->accion)
                : '-',
            'ultima_actividad' => $ultimaActividad
                ? $ultimaActividad->created_at->format('d/m/Y H:i')
                : '-',
        ];

        $tiposAccion = RegistroActividad::TIPOS_ACCION;

        return view('actividades.index', compact('stats', 'tiposAccion'));
    }

    /**
     * GET /[rol]/actividades-globales - Vista global de actividades (Admin/Recepcion).
     */
    public function global(Request $request)
    {
        if ($request->ajax()) {
            $query = RegistroActividad::with(['usuario.roles', 'orden'])
                ->select('registro_actividades.*');

            // Filtros
            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }
            if ($request->filled('accion')) {
                $query->where('accion', $request->accion);
            }
            if ($request->filled('usuario_id')) {
                $query->where('usuario_id', $request->usuario_id);
            }

            return DataTables::of($query)
                ->addColumn('fecha_formatted', function ($r) {
                    return $r->created_at->format('d/m/Y H:i');
                })
                ->addColumn('usuario_nombre', function ($r) {
                    return $r->usuario->name ?? '-';
                })
                ->addColumn('usuario_rol', function ($r) {
                    $rol = $r->usuario->roles->first()->name ?? '-';
                    $colores = [
                        'Administrador' => 'danger',
                        'Recepcion' => 'primary',
                        'Contabilidad' => 'success',
                        'Operario' => 'info',
                    ];
                    $color = $colores[$rol] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . e($rol) . '</span>';
                })
                ->addColumn('accion_badge', function ($r) {
                    return RegistroActividad::badgeAccion($r->accion);
                })
                ->addColumn('orden_link', function ($r) {
                    if (!$r->orden_id || !$r->orden) {
                        return '<span class="text-muted">-</span>';
                    }
                    $url = route('recepcion.ordenes.show', $r->orden_id);
                    return '<a href="' . $url . '" class="fw-semibold text-decoration-none">'
                        . ($r->orden->numero_orden ?? "#{$r->orden_id}")
                        . '</a>';
                })
                ->addColumn('detalle_btn', function ($r) {
                    if (empty($r->datos_extra)) {
                        return '<span class="text-muted">-</span>';
                    }
                    $payload = htmlspecialchars(json_encode($r->datos_extra), ENT_QUOTES, 'UTF-8');
                    $accion = e(RegistroActividad::TIPOS_ACCION[$r->accion] ?? $r->accion);
                    $fecha = $r->created_at->format('d/m/Y H:i');
                    $usuario = e($r->usuario->name ?? '-');
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-ver-detalle"'
                        . ' data-detalle="' . $payload . '"'
                        . ' data-accion="' . $accion . '"'
                        . ' data-fecha="' . $fecha . '"'
                        . ' data-usuario="' . $usuario . '"'
                        . ' title="Ver detalle del cambio"><i class="bi bi-eye"></i></button>';
                })
                ->filterColumn('usuario_nombre', function ($query, $keyword) {
                    $query->whereHas('usuario', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->orderColumn('fecha_formatted', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['usuario_rol', 'accion_badge', 'orden_link', 'detalle_btn'])
                ->make(true);
        }

        // Stats
        $totalHoy = RegistroActividad::whereDate('created_at', today())->count();
        $totalSemana = RegistroActividad::where('created_at', '>=', now()->startOfWeek())->count();
        $usuariosActivosHoy = RegistroActividad::whereDate('created_at', today())
            ->distinct('usuario_id')->count('usuario_id');
        $totalRegistros = RegistroActividad::count();

        $stats = [
            'total_hoy' => $totalHoy,
            'total_semana' => $totalSemana,
            'usuarios_activos_hoy' => $usuariosActivosHoy,
            'total_registros' => $totalRegistros,
        ];

        $tiposAccion = RegistroActividad::TIPOS_ACCION;
        $usuarios = User::orderBy('name')->get(['id', 'name']);

        return view('actividades.global', compact('stats', 'tiposAccion', 'usuarios'));
    }
}
