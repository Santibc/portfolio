<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PuntoCliente;
use App\Models\ConfiguracionPuntos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PuntosAdminController extends Controller
{
    /**
     * Dashboard de puntos - Estadísticas generales
     */
    public function dashboard()
    {
        $empresaId = auth()->user()->empresa->id;

        // Estadísticas generales (sin filtro de vendedor por ahora - single-tenant)
        $stats = [
            'total_puntos_circulacion' => PuntoCliente::where('tipo', 'ganados')
                ->where(function($q) {
                    $q->whereNull('fecha_expiracion')
                      ->orWhere('fecha_expiracion', '>', now());
                })
                ->sum('puntos'),

            'total_puntos_canjeados' => PuntoCliente::where('tipo', 'canjeados')
                ->sum(DB::raw('ABS(puntos)')),

            'total_referidos' => User::whereNotNull('referido_por')->count(),

            'puntos_por_referidos' => PuntoCliente::where('tipo', 'referido')->sum('puntos'),
        ];

        // Top 10 usuarios con más puntos (simplificado para single-tenant)
        $topUsuarios = DB::table('users')
            ->select('users.id', 'users.name', 'users.email',
                DB::raw('COALESCE(SUM(CASE WHEN puntos_cliente.tipo IN ("ganados", "referido", "registro", "primera_compra", "bonus")
                                           AND (puntos_cliente.fecha_expiracion IS NULL OR puntos_cliente.fecha_expiracion > NOW())
                                           THEN puntos_cliente.puntos ELSE 0 END), 0) as puntos_ganados'),
                DB::raw('COALESCE(SUM(CASE WHEN puntos_cliente.tipo = "canjeados" THEN ABS(puntos_cliente.puntos) ELSE 0 END), 0) as puntos_canjeados'),
                DB::raw('COALESCE(SUM(CASE WHEN puntos_cliente.tipo IN ("ganados", "referido", "registro", "primera_compra", "bonus")
                                           AND (puntos_cliente.fecha_expiracion IS NULL OR puntos_cliente.fecha_expiracion > NOW())
                                           THEN puntos_cliente.puntos ELSE 0 END), 0) -
                         COALESCE(SUM(CASE WHEN puntos_cliente.tipo = "canjeados" THEN ABS(puntos_cliente.puntos) ELSE 0 END), 0) as saldo'))
            ->leftJoin('puntos_cliente', 'users.id', '=', 'puntos_cliente.user_id')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('saldo', 'desc')
            ->limit(10)
            ->get();

        // Configuración actual
        $config = ConfiguracionPuntos::getConfiguracion($empresaId);

        return view('admin.puntos.dashboard', compact('stats', 'topUsuarios', 'config'));
    }

    /**
     * Listado de movimientos de puntos (DataTables)
     */
    public function movimientos(Request $request)
    {
        if ($request->ajax()) {
            $query = PuntoCliente::with('user')
                ->select('puntos_cliente.*')
                ->orderBy('puntos_cliente.created_at', 'desc');

            return DataTables::of($query)
                ->addColumn('usuario', function ($movimiento) {
                    return $movimiento->user->name . '<br><small class="text-muted">' . $movimiento->user->email . '</small>';
                })
                ->addColumn('puntos_formateado', function ($movimiento) {
                    if ($movimiento->tipo === 'canjeados') {
                        return '<span class="badge bg-danger">-' . abs($movimiento->puntos) . '</span>';
                    }
                    return '<span class="badge bg-success">+' . $movimiento->puntos . '</span>';
                })
                ->addColumn('tipo_badge', function ($movimiento) {
                    $colores = [
                        'ganados' => 'bg-success',
                        'canjeados' => 'bg-danger',
                        'referido' => 'bg-info',
                        'registro' => 'bg-primary',
                        'primera_compra' => 'bg-warning',
                        'bonus' => 'bg-purple',
                        'ajuste' => 'bg-secondary',
                    ];
                    $color = $colores[$movimiento->tipo] ?? 'bg-secondary';
                    return '<span class="badge ' . $color . '">' . ucfirst($movimiento->tipo) . '</span>';
                })
                ->addColumn('fecha', function ($movimiento) {
                    return $movimiento->created_at->format('d/m/Y H:i');
                })
                ->addColumn('expiracion', function ($movimiento) {
                    if (!$movimiento->fecha_expiracion) {
                        return '<span class="text-muted">-</span>';
                    }
                    $dias = now()->diffInDays($movimiento->fecha_expiracion, false);
                    if ($dias < 0) {
                        return '<span class="badge bg-secondary">Expirado</span>';
                    } elseif ($dias <= 30) {
                        return '<span class="badge bg-warning">Expira en ' . $dias . ' días</span>';
                    }
                    return '<small class="text-muted">' . \Carbon\Carbon::parse($movimiento->fecha_expiracion)->format('d/m/Y') . '</small>';
                })
                ->rawColumns(['usuario', 'puntos_formateado', 'tipo_badge', 'expiracion'])
                ->make(true);
        }

        return view('admin.puntos.movimientos');
    }

    /**
     * Reportes de referidos
     */
    public function referidos(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with(['referidos'])
                ->withCount('referidos')
                ->having('referidos_count', '>', 0)
                ->orderBy('referidos_count', 'desc');

            return DataTables::of($query)
                ->addColumn('usuario', function ($user) {
                    return $user->name . '<br><small class="text-muted">' . $user->email . '</small>';
                })
                ->addColumn('codigo', function ($user) {
                    return '<code class="bg-light p-1 rounded">' . ($user->codigo_referido ?? '-') . '</code>';
                })
                ->addColumn('total_referidos', function ($user) {
                    return '<span class="badge bg-primary">' . $user->referidos_count . '</span>';
                })
                ->addColumn('puntos_ganados', function ($user) {
                    $puntos = PuntoCliente::where('user_id', $user->id)
                        ->where('tipo', 'referido')
                        ->sum('puntos');
                    return '<span class="badge bg-success">' . $puntos . ' pts</span>';
                })
                ->addColumn('lista_referidos', function ($user) {
                    if ($user->referidos_count === 0) {
                        return '-';
                    }
                    $html = '<ul class="list-unstyled mb-0 small">';
                    foreach ($user->referidos->take(3) as $referido) {
                        $html .= '<li><i class="bi bi-person"></i> ' . $referido->name . '</li>';
                    }
                    if ($user->referidos_count > 3) {
                        $html .= '<li class="text-muted"><em>+' . ($user->referidos_count - 3) . ' más</em></li>';
                    }
                    $html .= '</ul>';
                    return $html;
                })
                ->rawColumns(['usuario', 'codigo', 'total_referidos', 'puntos_ganados', 'lista_referidos'])
                ->make(true);
        }

        return view('admin.puntos.referidos');
    }

    /**
     * Configuración del sistema de puntos
     */
    public function configuracion()
    {
        $empresaId = auth()->user()->empresa->id;
        $config = ConfiguracionPuntos::getConfiguracion($empresaId);

        return view('admin.puntos.configuracion', compact('config'));
    }

    /**
     * Guardar configuración
     */
    public function guardarConfiguracion(Request $request)
    {
        $empresaId = auth()->user()->empresa->id;

        $request->validate([
            'puntos_por_monto' => 'required|integer|min:1',
            'puntos_registro' => 'required|integer|min:0',
            'puntos_primera_compra' => 'required|integer|min:0',
            'puntos_referir' => 'required|integer|min:0',
            'puntos_ser_referido' => 'required|integer|min:0',
            'puntos_por_peso' => 'required|integer|min:1',
            'canje_minimo' => 'required|integer|min:1',
            'canje_maximo' => 'nullable|integer|min:1',
            'meses_expiracion' => 'required|integer|min:1',
            'sistema_activo' => 'boolean',
            'referidos_activo' => 'boolean',
        ]);

        $config = ConfiguracionPuntos::where('empresa_id', $empresaId)->first();

        if (!$config) {
            $config = new ConfiguracionPuntos();
            $config->empresa_id = $empresaId;
        }

        $config->fill($request->except('_token', '_method'));
        $config->sistema_activo = $request->boolean('sistema_activo');
        $config->referidos_activo = $request->boolean('referidos_activo');
        $config->save();

        return redirect()->route('admin.puntos.configuracion')
            ->with('success', 'Configuración actualizada correctamente');
    }

    /**
     * Ajustar puntos manualmente a un usuario
     */
    public function ajustarPuntos(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'puntos' => 'required|integer|not_in:0',
            'concepto' => 'required|string|max:500',
        ]);

        PuntoCliente::registrarPuntos(
            $request->user_id,
            $request->puntos,
            'ajuste',
            $request->concepto,
            null,
            null
        );

        return response()->json([
            'success' => true,
            'message' => 'Puntos ajustados correctamente'
        ]);
    }
}
