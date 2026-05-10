<?php

namespace App\Http\Controllers\ServicioTecnico;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\STEquipo;
use App\Models\STOrdenServicio;
use App\Models\STRepuesto;
use App\Models\STTecnico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardSTController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'ordenes_pendientes' => STOrdenServicio::whereIn('estado', ['recibida', 'asignada', 'en_proceso', 'pendiente_repuestos'])->count(),
            'ordenes_hoy' => STOrdenServicio::whereDate('fecha_recepcion', today())->count(),
            'ordenes_urgentes' => STOrdenServicio::where('prioridad', 'urgente')
                ->whereIn('estado', ['recibida', 'asignada', 'en_proceso'])
                ->count(),
            'ordenes_retrasadas' => STOrdenServicio::where('fecha_promesa_entrega', '<', now())
                ->whereNotIn('estado', ['completada', 'entregada', 'cancelada'])
                ->count(),
            'clientes_total' => Cliente::activos()->count(),
            'equipos_total' => STEquipo::activos()->count(),
            'equipos_en_reparacion' => STEquipo::where('estado', 'en_reparacion')->count(),
            'tecnicos_activos' => STTecnico::activos()->count(),
            'repuestos_bajo_stock' => STRepuesto::activos()->conStockBajo()->count(),
        ];

        // Órdenes por estado
        $ordenesPorEstado = STOrdenServicio::select('estado', DB::raw('count(*) as total'))
            ->whereIn('estado', ['recibida', 'asignada', 'en_proceso', 'pendiente_repuestos', 'completada'])
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado');

        // Últimas órdenes recibidas
        $ultimasOrdenes = STOrdenServicio::with(['cliente', 'tecnico', 'equipo'])
            ->latest('created_at')
            ->take(10)
            ->get();

        // Técnicos y su carga de trabajo
        $tecnicos = STTecnico::activos()
            ->withCount(['ordenesServicio as ordenes_activas' => function ($query) {
                $query->whereIn('estado', ['asignada', 'en_proceso', 'pendiente_repuestos']);
            }])
            ->get();

        // Servicios por tipo (últimos 30 días)
        $serviciosPorTipo = STOrdenServicio::select('tipo_servicio', DB::raw('count(*) as total'))
            ->where('fecha_recepcion', '>=', now()->subDays(30))
            ->groupBy('tipo_servicio')
            ->get();

        // Ingresos del mes actual
        $ingresosMesActual = STOrdenServicio::whereMonth('fecha_finalizacion', now()->month)
            ->whereYear('fecha_finalizacion', now()->year)
            ->whereIn('estado', ['completada', 'entregada'])
            ->sum('costo_total');

        return view('servicio-tecnico.dashboard', compact(
            'stats',
            'ordenesPorEstado',
            'ultimasOrdenes',
            'tecnicos',
            'serviciosPorTipo',
            'ingresosMesActual'
        ));
    }
}
