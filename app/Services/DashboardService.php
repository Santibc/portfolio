<?php

namespace App\Services;

use App\Models\Obra;
use App\Models\Ingreso;
use App\Models\Gasto;
use App\Models\Factura;
use App\Models\Fichaje;
use App\Models\ParteDiario;
use App\Models\Trabajador;
use App\Models\Cuadrilla;
use App\Models\Contrato;
use App\Models\Cliente;
use App\Models\Alerta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    protected AlertaService $alertaService;

    public function __construct(AlertaService $alertaService)
    {
        $this->alertaService = $alertaService;
    }

    /**
     * Obtener KPIs principales del dashboard
     */
    public function getKpis(?Carbon $fechaInicio = null, ?Carbon $fechaFin = null): array
    {
        $fechaInicio = $fechaInicio ?? now()->startOfYear();
        $fechaFin = $fechaFin ?? now();

        // Ingresos del periodo
        $ingresosPeriodo = Ingreso::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sum('importe_total');

        // Gastos del periodo
        $gastosPeriodo = Gasto::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sum('importe_total');

        return [
            // Financieros
            'ingresos_periodo' => $ingresosPeriodo,
            'gastos_periodo' => $gastosPeriodo,
            'beneficio_periodo' => $ingresosPeriodo - $gastosPeriodo,
            'cobros_pendientes' => Ingreso::pendientes()->sum('importe_total'),
            'pagos_pendientes' => Gasto::pendientes()->sum('importe_total'),

            // Facturación
            'facturas_emitidas' => Factura::delAnio()->count(),
            'facturas_pendientes_importe' => Factura::pendientes()->sum('total'),

            // Obras
            'obras_en_curso' => Obra::enCurso()->count(),
            'obras_activas' => Obra::activas()->count(),

            // Operativos
            'fichajes_pendientes' => Fichaje::pendientesValidar()->count(),
            'partes_borradores' => ParteDiario::borradores()->count(),

            // Personal
            'trabajadores_activos' => Trabajador::activos()->count(),
            'trabajadores_propios' => Trabajador::activos()->propios()->count(),
            'cuadrillas_activas' => Cuadrilla::activas()->count(),

            // Contratos
            'contratos' => Contrato::getEstadisticas(),

            // Alertas
            'alertas' => $this->alertaService->getEstadisticasParaUsuario(
                auth()->id(),
                auth()->user()->roles->pluck('name')->toArray()
            ),
        ];
    }

    /**
     * Datos para gráfico de rentabilidad mensual (solo meses con datos, máximo 12)
     */
    public function getRentabilidadMensual(int $meses = 12): array
    {
        // Ingresos por mes (todos los que existan)
        $ingresosMensuales = Ingreso::query()
            ->select(
                DB::raw("DATE_FORMAT(fecha, '%Y-%m') as periodo"),
                DB::raw("SUM(importe_total) as total")
            )
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->pluck('total', 'periodo')
            ->toArray();

        // Gastos por mes (todos los que existan)
        $gastosMensuales = Gasto::query()
            ->select(
                DB::raw("DATE_FORMAT(fecha, '%Y-%m') as periodo"),
                DB::raw("SUM(importe_total) as total")
            )
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->pluck('total', 'periodo')
            ->toArray();

        // Obtener todos los períodos únicos con datos
        $todosLosPeriodos = collect(array_keys($ingresosMensuales))
            ->merge(array_keys($gastosMensuales))
            ->unique()
            ->sort()
            ->values();

        // Si no hay datos, devolver vacío
        if ($todosLosPeriodos->isEmpty()) {
            return [
                'labels' => [],
                'ingresos' => [],
                'gastos' => [],
                'beneficio' => [],
            ];
        }

        // Limitar a los últimos N meses si hay más
        if ($todosLosPeriodos->count() > $meses) {
            $todosLosPeriodos = $todosLosPeriodos->slice(-$meses)->values();
        }

        // Construir arrays solo con los meses que tienen datos
        $labels = [];
        $ingresos = [];
        $gastos = [];
        $beneficio = [];

        foreach ($todosLosPeriodos as $periodo) {
            $fecha = \Carbon\Carbon::createFromFormat('Y-m', $periodo);
            $labels[] = $fecha->translatedFormat('M Y');
            $ingresos[] = round($ingresosMensuales[$periodo] ?? 0, 2);
            $gastos[] = round($gastosMensuales[$periodo] ?? 0, 2);
            $beneficio[] = round(($ingresosMensuales[$periodo] ?? 0) - ($gastosMensuales[$periodo] ?? 0), 2);
        }

        return compact('labels', 'ingresos', 'gastos', 'beneficio');
    }

    /**
     * Datos para gráfico de flujo de caja (cobros vs pagos reales)
     */
    public function getFlujoCajaMensual(int $meses = 12): array
    {
        $fechaInicio = now()->subMonths($meses)->startOfMonth();

        // Cobros reales (ingresos con fecha_cobro)
        $cobrosMensuales = Ingreso::query()
            ->select(
                DB::raw("DATE_FORMAT(fecha_cobro, '%Y-%m') as periodo"),
                DB::raw("SUM(importe_total) as total")
            )
            ->whereNotNull('fecha_cobro')
            ->where('fecha_cobro', '>=', $fechaInicio)
            ->where('estado', 'cobrado')
            ->groupBy('periodo')
            ->pluck('total', 'periodo')
            ->toArray();

        // Pagos reales (gastos con fecha_pago)
        $pagosMensuales = Gasto::query()
            ->select(
                DB::raw("DATE_FORMAT(fecha_pago, '%Y-%m') as periodo"),
                DB::raw("SUM(importe_total) as total")
            )
            ->whereNotNull('fecha_pago')
            ->where('fecha_pago', '>=', $fechaInicio)
            ->where('estado', 'pagado')
            ->groupBy('periodo')
            ->pluck('total', 'periodo')
            ->toArray();

        // Construir respuesta
        $labels = [];
        $cobros = [];
        $pagos = [];
        $saldoAcumulado = [];
        $saldo = 0;

        for ($i = $meses - 1; $i >= 0; $i--) {
            $periodo = now()->subMonths($i)->format('Y-m');
            $mesLabel = now()->subMonths($i)->translatedFormat('M Y');

            $cobroMes = round($cobrosMensuales[$periodo] ?? 0, 2);
            $pagoMes = round($pagosMensuales[$periodo] ?? 0, 2);
            $saldo += ($cobroMes - $pagoMes);

            $labels[] = $mesLabel;
            $cobros[] = $cobroMes;
            $pagos[] = $pagoMes;
            $saldoAcumulado[] = round($saldo, 2);
        }

        return compact('labels', 'cobros', 'pagos', 'saldoAcumulado');
    }

    /**
     * Top N obras más rentables y Bottom N menos rentables
     */
    public function getRentabilidadPorObra(int $top = 5, int $bottom = 5, array $filtros = []): array
    {
        $query = Obra::query()
            ->select([
                'obras.id',
                'obras.codigo',
                'obras.nombre',
                'obras.presupuesto',
                'obras.estado',
                'obras.cliente_id',
                DB::raw('COALESCE((SELECT SUM(importe_total) FROM ingresos WHERE ingresos.obra_id = obras.id), 0) as total_ingresos'),
                DB::raw('COALESCE((SELECT SUM(importe_total) FROM gastos WHERE gastos.obra_id = obras.id), 0) as total_gastos'),
            ])
            ->whereIn('obras.estado', ['aprobada', 'en_curso', 'finalizada'])
            ->with('cliente:id,nombre_comercial');

        // Aplicar filtros si existen
        if (!empty($filtros['cliente_id'])) {
            $query->where('obras.cliente_id', $filtros['cliente_id']);
        }

        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $query->where(function ($q) use ($filtros) {
                $q->whereBetween('obras.fecha_inicio_real', [$filtros['fecha_inicio'], $filtros['fecha_fin']])
                    ->orWhereBetween('obras.fecha_inicio_prevista', [$filtros['fecha_inicio'], $filtros['fecha_fin']]);
            });
        }

        $obras = $query->get()->map(function ($obra) {
            $obra->beneficio = $obra->total_ingresos - $obra->total_gastos;
            $obra->margen_porcentaje = $obra->presupuesto > 0
                ? round(($obra->beneficio / $obra->presupuesto) * 100, 1)
                : 0;
            return $obra;
        });

        // Top obras más rentables
        $topObras = $obras->sortByDesc('margen_porcentaje')
            ->take($top)
            ->values();

        // Bottom obras menos rentables (con actividad financiera)
        $bottomObras = $obras->filter(fn($o) => ($o->total_ingresos + $o->total_gastos) > 0)
            ->sortBy('margen_porcentaje')
            ->take($bottom)
            ->values();

        return [
            'top' => $topObras,
            'bottom' => $bottomObras,
        ];
    }

    /**
     * Ranking de cuadrillas por producción
     */
    public function getRentabilidadPorCuadrilla(int $limite = 10): array
    {
        $mesActual = now()->month;
        $anioActual = now()->year;

        $cuadrillas = Cuadrilla::query()
            ->where('activa', true)
            ->with(['trabajadoresActivos', 'capataz'])
            ->get()
            ->map(function ($cuadrilla) use ($mesActual, $anioActual) {
                // Contar trabajadores activos
                $numTrabajadores = $cuadrilla->trabajadoresActivos->count();

                // Obtener IDs de trabajadores de la cuadrilla
                $trabajadorIds = $cuadrilla->trabajadoresActivos->pluck('id');

                // Horas trabajadas del mes por trabajadores de la cuadrilla
                $horasTrabajadas = Fichaje::whereIn('trabajador_id', $trabajadorIds)
                    ->whereMonth('fecha', $mesActual)
                    ->whereYear('fecha', $anioActual)
                    ->sum('horas_trabajadas');

                // Coste hora promedio de la cuadrilla
                $costeHoraPromedio = $cuadrilla->trabajadoresActivos->avg('coste_hora') ?? 15;

                // Coste estimado
                $costeEstimado = $horasTrabajadas * $costeHoraPromedio;

                // Producción de las obras donde trabaja la cuadrilla
                $obraIds = $cuadrilla->obras()->wherePivot('activo', true)->pluck('obras.id');

                $produccionTotal = ParteDiario::whereIn('obra_id', $obraIds)
                    ->whereMonth('fecha', $mesActual)
                    ->whereYear('fecha', $anioActual)
                    ->whereIn('estado', ['completado', 'validado'])
                    ->sum('importe_total_calculado');

                return [
                    'id' => $cuadrilla->id,
                    'nombre' => $cuadrilla->nombre,
                    'capataz' => $cuadrilla->capataz ? $cuadrilla->capataz->nombre_completo : 'Sin asignar',
                    'num_trabajadores' => $numTrabajadores,
                    'horas_trabajadas' => round($horasTrabajadas, 1),
                    'produccion_total' => round($produccionTotal, 2),
                    'coste_estimado' => round($costeEstimado, 2),
                    'beneficio_estimado' => round($produccionTotal - $costeEstimado, 2),
                    'margen_porcentaje' => $produccionTotal > 0
                        ? round((($produccionTotal - $costeEstimado) / $produccionTotal) * 100, 1)
                        : 0,
                ];
            })
            ->sortByDesc('produccion_total')
            ->take($limite)
            ->values();

        return $cuadrillas->toArray();
    }

    /**
     * Lista de cobros pendientes con aging (tramos de vencimiento)
     */
    public function getCobrosPendientesConAging(): array
    {
        $hoy = now()->startOfDay();

        $pendientes = Ingreso::query()
            ->with(['obra:id,codigo,nombre', 'cliente:id,nombre_comercial'])
            ->pendientes()
            ->orderBy('fecha_prevista_cobro')
            ->get()
            ->map(function ($ingreso) use ($hoy) {
                $fechaPrevista = $ingreso->fecha_prevista_cobro;

                if ($fechaPrevista) {
                    $diasVencido = $fechaPrevista->startOfDay()->diffInDays($hoy, false);
                } else {
                    // Si no tiene fecha prevista, usar fecha del ingreso
                    $diasVencido = $ingreso->fecha->startOfDay()->diffInDays($hoy, false);
                }

                $ingreso->dias_vencido = max(0, $diasVencido);
                $ingreso->tramo_aging = match (true) {
                    $diasVencido <= 0 => 'al_dia',
                    $diasVencido <= 30 => '1_30',
                    $diasVencido <= 60 => '31_60',
                    $diasVencido <= 90 => '61_90',
                    default => 'mas_90',
                };

                return $ingreso;
            });

        // Agrupar por tramos
        $resumen = [
            'al_dia' => ['total' => 0, 'count' => 0],
            '1_30' => ['total' => 0, 'count' => 0],
            '31_60' => ['total' => 0, 'count' => 0],
            '61_90' => ['total' => 0, 'count' => 0],
            'mas_90' => ['total' => 0, 'count' => 0],
        ];

        foreach ($pendientes as $p) {
            $resumen[$p->tramo_aging]['total'] += $p->importe_total;
            $resumen[$p->tramo_aging]['count']++;
        }

        // Redondear totales
        foreach ($resumen as $tramo => $datos) {
            $resumen[$tramo]['total'] = round($datos['total'], 2);
        }

        return [
            'detalle' => $pendientes->take(15)->values(), // Limitar a 15 para la vista
            'resumen' => $resumen,
            'total_pendiente' => round($pendientes->sum('importe_total'), 2),
            'total_registros' => $pendientes->count(),
        ];
    }

    /**
     * Lista de obras con desviación negativa (gastos > coste estimado)
     */
    public function getObrasEnRiesgo(int $limite = 10): array
    {
        // Obtener obras en curso con coste estimado
        $obras = Obra::query()
            ->select([
                'obras.id',
                'obras.codigo',
                'obras.nombre',
                'obras.coste_estimado',
                'obras.presupuesto',
                'obras.estado',
                'obras.cliente_id',
                'obras.encargado_id',
            ])
            ->where('obras.estado', 'en_curso')
            ->whereNotNull('obras.coste_estimado')
            ->where('obras.coste_estimado', '>', 0)
            ->with(['cliente:id,nombre_comercial', 'encargado:id,name'])
            ->get();

        // Calcular gasto real y filtrar las que superan el coste estimado
        return $obras->map(function ($obra) {
                $gastoReal = Gasto::where('obra_id', $obra->id)->sum('importe_total');
                $obra->gasto_real = round($gastoReal, 2);
                $obra->desviacion = round($gastoReal - $obra->coste_estimado, 2);
                $obra->desviacion_porcentaje = $obra->coste_estimado > 0
                    ? round(($obra->desviacion / $obra->coste_estimado) * 100, 1)
                    : 0;
                return $obra;
            })
            ->filter(fn($obra) => $obra->gasto_real > $obra->coste_estimado)
            ->sortByDesc('desviacion')
            ->take($limite)
            ->values()
            ->toArray();
    }

    /**
     * Producción del mes (m², talas, podas con variación vs mes anterior)
     */
    public function getProduccionMes(?int $mes = null, ?int $anio = null): array
    {
        $mes = $mes ?? now()->month;
        $anio = $anio ?? now()->year;

        // Producción del mes actual agrupada por categoría
        $produccionPorCategoria = \App\Models\ParteDiarioProduccion::query()
            ->join('partes_diarios', 'parte_diario_producciones.parte_diario_id', '=', 'partes_diarios.id')
            ->join('obra_conceptos_produccion', 'parte_diario_producciones.concepto_produccion_id', '=', 'obra_conceptos_produccion.id')
            ->whereMonth('partes_diarios.fecha', $mes)
            ->whereYear('partes_diarios.fecha', $anio)
            ->whereIn('partes_diarios.estado', ['completado', 'validado'])
            ->select([
                'obra_conceptos_produccion.categoria',
                DB::raw('SUM(parte_diario_producciones.cantidad) as total')
            ])
            ->groupBy('obra_conceptos_produccion.categoria')
            ->pluck('total', 'categoria')
            ->toArray();

        // Datos complementarios (importe y num partes)
        $resumen = \App\Models\ParteDiario::query()
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->whereIn('estado', ['completado', 'validado'])
            ->select([
                DB::raw('COALESCE(SUM(importe_total_calculado), 0) as importe_total'),
                DB::raw('COUNT(*) as num_partes')
            ])
            ->first();

        // Mes anterior
        $mesAnterior = $mes == 1 ? 12 : $mes - 1;
        $anioAnterior = $mes == 1 ? $anio - 1 : $anio;

        // Producción del mes anterior agrupada por categoría
        $produccionAnteriorCat = \App\Models\ParteDiarioProduccion::query()
            ->join('partes_diarios', 'parte_diario_producciones.parte_diario_id', '=', 'partes_diarios.id')
            ->join('obra_conceptos_produccion', 'parte_diario_producciones.concepto_produccion_id', '=', 'obra_conceptos_produccion.id')
            ->whereMonth('partes_diarios.fecha', $mesAnterior)
            ->whereYear('partes_diarios.fecha', $anioAnterior)
            ->whereIn('partes_diarios.estado', ['completado', 'validado'])
            ->select([
                'obra_conceptos_produccion.categoria',
                DB::raw('SUM(parte_diario_producciones.cantidad) as total')
            ])
            ->groupBy('obra_conceptos_produccion.categoria')
            ->pluck('total', 'categoria')
            ->toArray();

        // Datos complementarios mes anterior
        $resumenAnterior = \App\Models\ParteDiario::query()
            ->whereMonth('fecha', $mesAnterior)
            ->whereYear('fecha', $anioAnterior)
            ->whereIn('estado', ['completado', 'validado'])
            ->select([
                DB::raw('COALESCE(SUM(importe_total_calculado), 0) as importe_total')
            ])
            ->first();

        // Obtener unidades por categoría desde los conceptos usados en el mes
        $unidadesPorCategoria = \App\Models\ParteDiarioProduccion::query()
            ->join('partes_diarios', 'parte_diario_producciones.parte_diario_id', '=', 'partes_diarios.id')
            ->join('obra_conceptos_produccion', 'parte_diario_producciones.concepto_produccion_id', '=', 'obra_conceptos_produccion.id')
            ->whereMonth('partes_diarios.fecha', $mes)
            ->whereYear('partes_diarios.fecha', $anio)
            ->select('obra_conceptos_produccion.categoria', 'obra_conceptos_produccion.unidad')
            ->distinct()
            ->get()
            ->pluck('unidad', 'categoria')
            ->toArray();

        // Construir categorías con datos (mostrar todas)
        $categorias = [];
        foreach ($produccionPorCategoria as $cat => $cantidad) {
            $categorias[$cat] = [
                'cantidad' => $cantidad,
                'unidad' => $unidadesPorCategoria[$cat] ?? 'unidades',
            ];
        }

        // Calcular variaciones para todas las categorías
        $variaciones = [];
        foreach ($produccionPorCategoria as $cat => $cantidadActual) {
            $cantidadAnterior = $produccionAnteriorCat[$cat] ?? 0;
            $variaciones[$cat] = $this->calcularVariacion($cantidadActual, $cantidadAnterior);
        }
        $variaciones['importe'] = $this->calcularVariacion($resumen->importe_total, $resumenAnterior->importe_total);

        return [
            'actual' => [
                'categorias' => $categorias,
                'importe_total' => round($resumen->importe_total, 2),
                'num_partes' => $resumen->num_partes,
            ],
            'anterior' => [
                'importe_total' => round($resumenAnterior->importe_total, 2),
            ],
            'variaciones' => $variaciones,
            'periodo' => \Carbon\Carbon::createFromDate($anio, $mes, 1)->translatedFormat('F Y'),
            'periodo_anterior' => \Carbon\Carbon::createFromDate($anioAnterior, $mesAnterior, 1)->translatedFormat('F Y'),
        ];
    }

    /**
     * Obtener alertas críticas para el dashboard
     */
    public function getAlertasCriticas(int $limite = 10): array
    {
        $userId = auth()->id();
        $roles = auth()->user()->roles->pluck('name')->toArray();

        $alertas = Alerta::query()
            ->where('resuelta', false)
            ->where(function ($q) use ($userId, $roles) {
                $q->where('para_usuario_id', $userId);
                foreach ($roles as $rol) {
                    $q->orWhereJsonContains('para_roles', $rol);
                }
            })
            ->orderByRaw("CASE prioridad
                WHEN 'critica' THEN 1
                WHEN 'alta' THEN 2
                WHEN 'media' THEN 3
                WHEN 'baja' THEN 4
                END")
            ->orderBy('fecha_vencimiento')
            ->limit($limite)
            ->get();

        return $alertas->toArray();
    }

    /**
     * Obtener opciones para filtros
     */
    public function getOpcionesFiltros(): array
    {
        return [
            'obras' => Obra::query()
                ->select('id', 'codigo', 'nombre')
                ->whereIn('estado', ['aprobada', 'en_curso', 'finalizada'])
                ->orderBy('codigo')
                ->get()
                ->map(fn($o) => ['id' => $o->id, 'codigo_nombre' => $o->codigo_nombre]),

            'clientes' => Cliente::query()
                ->select('id', 'nombre_comercial', 'razon_social')
                ->activos()
                ->orderBy('nombre_comercial')
                ->get()
                ->map(fn($c) => ['id' => $c->id, 'nombre_completo' => $c->nombre_comercial ?: $c->razon_social]),

            'cuadrillas' => Cuadrilla::query()
                ->select('id', 'nombre')
                ->activas()
                ->orderBy('nombre')
                ->get(),
        ];
    }

    /**
     * Calcular variación porcentual entre dos valores
     */
    protected function calcularVariacion($actual, $anterior): array
    {
        $actual = floatval($actual);
        $anterior = floatval($anterior);

        if (!$anterior || $anterior == 0) {
            return [
                'valor' => $actual > 0 ? 100 : 0,
                'tipo' => $actual > 0 ? 'positive' : 'neutral',
            ];
        }

        $variacion = (($actual - $anterior) / $anterior) * 100;

        return [
            'valor' => round($variacion, 1),
            'tipo' => $variacion > 0 ? 'positive' : ($variacion < 0 ? 'negative' : 'neutral'),
        ];
    }
}
