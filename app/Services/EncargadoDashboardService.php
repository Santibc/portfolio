<?php

namespace App\Services;

use App\Models\Obra;
use App\Models\Fichaje;
use App\Models\ParteDiario;
use App\Models\Maquinaria;
use App\Models\Trabajador;
use App\Models\Alerta;
use App\Models\Cuadrilla;
use App\Services\AlertaService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EncargadoDashboardService
{
    protected AlertaService $alertaService;
    protected int $encargadoId;
    protected ?Collection $obrasIdsCache = null;

    public function __construct(AlertaService $alertaService)
    {
        $this->alertaService = $alertaService;
    }

    /**
     * Establecer el ID del encargado para filtrar datos
     */
    public function setEncargadoId(int $encargadoId): self
    {
        $this->encargadoId = $encargadoId;
        $this->obrasIdsCache = null; // Resetear caché
        return $this;
    }

    /**
     * Obtener IDs de obras asignadas al encargado
     */
    protected function getObrasIds(): Collection
    {
        if ($this->obrasIdsCache === null) {
            $this->obrasIdsCache = Obra::where('encargado_id', $this->encargadoId)
                ->whereIn('estado', ['aprobada', 'en_curso'])
                ->pluck('id');
        }
        return $this->obrasIdsCache;
    }

    /**
     * KPIs principales del encargado (sin datos financieros sensibles)
     */
    public function getKpis(): array
    {
        $obrasIds = $this->getObrasIds();

        if ($obrasIds->isEmpty()) {
            return $this->getKpisVacios();
        }

        // Obras asignadas
        $obrasTotal = $obrasIds->count();
        $obrasEnCurso = Obra::whereIn('id', $obrasIds)
            ->where('estado', 'en_curso')
            ->count();

        // Trabajadores activos en sus obras
        $trabajadoresActivos = Trabajador::whereHas('obras', function ($q) use ($obrasIds) {
            $q->whereIn('obras.id', $obrasIds)
                ->where('obra_trabajadores.activo', true);
        })->activos()->count();

        // Fichajes pendientes de validar de sus obras (solo de hoy)
        $fichajesPendientes = Fichaje::whereIn('obra_id', $obrasIds)
            ->where('validado', false)
            ->whereNotNull('hora_entrada')
            ->count();

        // Partes en borrador de sus obras
        $partesBorrador = ParteDiario::whereIn('obra_id', $obrasIds)
            ->where('estado', 'borrador')
            ->count();

        // Partes completados sin validar
        $partesCompletados = ParteDiario::whereIn('obra_id', $obrasIds)
            ->where('estado', 'completado')
            ->count();

        // Producción de hoy (m²)
        $produccionHoy = ParteDiario::whereIn('obra_id', $obrasIds)
            ->where('fecha', today())
            ->whereIn('estado', ['completado', 'validado'])
            ->sum('desbroce_total_m2');

        // Alertas no leídas de sus obras
        $alertasNoLeidas = $this->getAlertasCount();

        return [
            'obras_total' => $obrasTotal,
            'obras_en_curso' => $obrasEnCurso,
            'trabajadores_activos' => $trabajadoresActivos,
            'fichajes_pendientes' => $fichajesPendientes,
            'partes_borrador' => $partesBorrador,
            'partes_completados' => $partesCompletados,
            'produccion_hoy_m2' => round($produccionHoy, 0),
            'alertas_no_leidas' => $alertasNoLeidas,
        ];
    }

    /**
     * KPIs vacíos cuando no hay obras asignadas
     */
    protected function getKpisVacios(): array
    {
        return [
            'obras_total' => 0,
            'obras_en_curso' => 0,
            'trabajadores_activos' => 0,
            'fichajes_pendientes' => 0,
            'partes_borrador' => 0,
            'partes_completados' => 0,
            'produccion_hoy_m2' => 0,
            'alertas_no_leidas' => 0,
        ];
    }

    /**
     * Obtener lista de obras asignadas con estado y resumen
     */
    public function getMisObras(): Collection
    {
        $obrasIds = $this->getObrasIds();

        if ($obrasIds->isEmpty()) {
            return collect();
        }

        return Obra::whereIn('id', $obrasIds)
            ->with(['cliente:id,nombre_comercial'])
            ->select([
                'id', 'codigo', 'nombre', 'estado', 'cliente_id',
                'fecha_inicio_real', 'fecha_fin_prevista'
            ])
            ->get()
            ->map(function ($obra) {
                // Trabajadores activos en esta obra
                $trabajadoresActivos = $obra->trabajadores()
                    ->wherePivot('activo', true)
                    ->count();

                // Último parte diario
                $ultimoParte = $obra->partesDiarios()
                    ->orderBy('fecha', 'desc')
                    ->first();

                // Producción del mes actual
                $produccionMes = $obra->partesDiarios()
                    ->whereMonth('fecha', now()->month)
                    ->whereYear('fecha', now()->year)
                    ->whereIn('estado', ['completado', 'validado'])
                    ->sum('importe_total_calculado');

                return [
                    'id' => $obra->id,
                    'codigo' => $obra->codigo,
                    'nombre' => $obra->nombre,
                    'estado' => $obra->estado,
                    'cliente' => $obra->cliente->nombre_comercial ?? 'Sin cliente',
                    'fecha_inicio' => $obra->fecha_inicio_real?->format('d/m/Y'),
                    'fecha_fin_prevista' => $obra->fecha_fin_prevista?->format('d/m/Y'),
                    'trabajadores_activos' => $trabajadoresActivos,
                    'ultimo_parte_fecha' => $ultimoParte?->fecha?->format('d/m/Y'),
                    'produccion_mes' => round($produccionMes, 2),
                ];
            })
            ->sortBy('codigo')
            ->values();
    }

    /**
     * Producción diaria (hoy) agregada de todas sus obras
     */
    public function getProduccionDiaria(): array
    {
        $obrasIds = $this->getObrasIds();

        if ($obrasIds->isEmpty()) {
            return $this->getProduccionVacia();
        }

        $hoy = today();
        $ayer = today()->subDay();

        // Producción de hoy
        $produccionHoy = ParteDiario::whereIn('obra_id', $obrasIds)
            ->where('fecha', $hoy)
            ->whereIn('estado', ['completado', 'validado', 'borrador'])
            ->select([
                DB::raw('COALESCE(SUM(desbroce_total_m2), 0) as desbroce_m2'),
                DB::raw('COALESCE(SUM(talas_unidades), 0) as talas'),
                DB::raw('COALESCE(SUM(podas_unidades), 0) as podas'),
                DB::raw('COALESCE(SUM(importe_total_calculado), 0) as importe'),
                DB::raw('COUNT(*) as num_partes'),
            ])
            ->first();

        // Producción de ayer (para comparación)
        $produccionAyer = ParteDiario::whereIn('obra_id', $obrasIds)
            ->where('fecha', $ayer)
            ->whereIn('estado', ['completado', 'validado'])
            ->select([
                DB::raw('COALESCE(SUM(desbroce_total_m2), 0) as desbroce_m2'),
                DB::raw('COALESCE(SUM(talas_unidades), 0) as talas'),
                DB::raw('COALESCE(SUM(podas_unidades), 0) as podas'),
            ])
            ->first();

        return [
            'hoy' => [
                'desbroce_m2' => round($produccionHoy->desbroce_m2, 0),
                'talas' => intval($produccionHoy->talas),
                'podas' => intval($produccionHoy->podas),
                'importe' => round($produccionHoy->importe, 2),
                'num_partes' => $produccionHoy->num_partes,
            ],
            'variaciones' => [
                'desbroce' => $this->calcularVariacion($produccionHoy->desbroce_m2, $produccionAyer->desbroce_m2),
                'talas' => $this->calcularVariacion($produccionHoy->talas, $produccionAyer->talas),
                'podas' => $this->calcularVariacion($produccionHoy->podas, $produccionAyer->podas),
            ],
            'fecha' => $hoy->format('d/m/Y'),
        ];
    }

    /**
     * Producción vacía
     */
    protected function getProduccionVacia(): array
    {
        return [
            'hoy' => [
                'desbroce_m2' => 0,
                'talas' => 0,
                'podas' => 0,
                'importe' => 0,
                'num_partes' => 0,
            ],
            'variaciones' => [
                'desbroce' => ['valor' => 0, 'tipo' => 'neutral'],
                'talas' => ['valor' => 0, 'tipo' => 'neutral'],
                'podas' => ['valor' => 0, 'tipo' => 'neutral'],
            ],
            'fecha' => today()->format('d/m/Y'),
        ];
    }

    /**
     * Horas por trabajador (hoy y semana actual)
     */
    public function getHorasTrabajadores(): Collection
    {
        $obrasIds = $this->getObrasIds();

        if ($obrasIds->isEmpty()) {
            return collect();
        }

        // Obtener trabajadores activos en sus obras
        $trabajadores = Trabajador::whereHas('obras', function ($q) use ($obrasIds) {
            $q->whereIn('obras.id', $obrasIds)
                ->where('obra_trabajadores.activo', true);
        })
        ->activos()
        ->select('id', 'nombre', 'apellidos')
        ->get();

        if ($trabajadores->isEmpty()) {
            return collect();
        }

        $hoy = today();
        $inicioSemana = now()->startOfWeek();
        $finSemana = now()->endOfWeek();

        return $trabajadores->map(function ($trabajador) use ($obrasIds, $hoy, $inicioSemana, $finSemana) {
            // Horas de hoy
            $fichajeHoy = Fichaje::where('trabajador_id', $trabajador->id)
                ->whereIn('obra_id', $obrasIds)
                ->where('fecha', $hoy)
                ->first();

            $horasHoy = $fichajeHoy ? floatval($fichajeHoy->horas_trabajadas) : 0;
            $fichajeActivo = $fichajeHoy && $fichajeHoy->hora_entrada && !$fichajeHoy->hora_salida;

            // Obra actual (del fichaje de hoy o última asignación activa)
            $obraActual = null;
            if ($fichajeHoy && $fichajeHoy->obra) {
                $obraActual = $fichajeHoy->obra->codigo;
            } else {
                $ultimaObra = $trabajador->obras()
                    ->whereIn('obras.id', $obrasIds)
                    ->wherePivot('activo', true)
                    ->first();
                $obraActual = $ultimaObra?->codigo ?? '-';
            }

            // Horas de la semana
            $horasSemana = Fichaje::where('trabajador_id', $trabajador->id)
                ->whereIn('obra_id', $obrasIds)
                ->whereBetween('fecha', [$inicioSemana, $finSemana])
                ->sum('horas_trabajadas');

            return [
                'id' => $trabajador->id,
                'nombre_completo' => $trabajador->nombre_completo,
                'obra_actual' => $obraActual,
                'horas_hoy' => round($horasHoy, 1),
                'horas_semana' => round($horasSemana, 1),
                'fichaje_activo' => $fichajeActivo,
            ];
        })
        ->sortByDesc('horas_semana')
        ->values();
    }

    /**
     * Maquinaria asignada a sus obras
     */
    public function getMaquinariaAsignada(): Collection
    {
        $obrasIds = $this->getObrasIds();

        if ($obrasIds->isEmpty()) {
            return collect();
        }

        return Maquinaria::whereIn('obra_asignada_id', $obrasIds)
            ->with([
                'tipo:id,nombre',
                'obraAsignada:id,codigo',
                'trabajadorAsignado:id,nombre,apellidos'
            ])
            ->select('id', 'codigo_interno', 'marca', 'modelo', 'estado', 'maquinaria_tipo_id', 'obra_asignada_id', 'trabajador_asignado_id')
            ->get()
            ->map(function ($maquinaria) {
                // Última inspección
                $ultimaInspeccion = $maquinaria->inspecciones()
                    ->orderBy('fecha_inspeccion', 'desc')
                    ->first();

                return [
                    'id' => $maquinaria->id,
                    'codigo' => $maquinaria->codigo_interno,
                    'nombre' => $maquinaria->tipo?->nombre ?? 'Sin tipo',
                    'marca_modelo' => trim(($maquinaria->marca ?? '') . ' ' . ($maquinaria->modelo ?? '')),
                    'estado' => $maquinaria->estado,
                    'obra_codigo' => $maquinaria->obraAsignada?->codigo ?? '-',
                    'operador' => $maquinaria->trabajadorAsignado?->nombre_completo ?? 'Sin asignar',
                    'ultima_inspeccion' => $ultimaInspeccion?->fecha_inspeccion?->format('d/m/Y'),
                    'resultado_inspeccion' => $ultimaInspeccion?->resultado,
                ];
            })
            ->sortBy('obra_codigo')
            ->values();
    }

    /**
     * Eventos del calendario semanal
     */
    public function getCalendarioSemanal(?Carbon $fechaInicio = null): array
    {
        $obrasIds = $this->getObrasIds();

        if ($obrasIds->isEmpty()) {
            return $this->getCalendarioVacio($fechaInicio);
        }

        $inicio = $fechaInicio ?? now()->startOfWeek();
        $fin = $inicio->copy()->endOfWeek();

        $eventos = [];

        // Días de la semana
        for ($fecha = $inicio->copy(); $fecha <= $fin; $fecha->addDay()) {
            $fechaStr = $fecha->format('Y-m-d');
            $eventos[$fechaStr] = [
                'fecha' => $fechaStr,
                'dia' => $fecha->translatedFormat('D'),
                'dia_mes' => $fecha->day,
                'es_hoy' => $fecha->isToday(),
                'partes' => 0,
                'inspecciones' => 0,
                'vencimientos' => 0,
            ];
        }

        // Partes diarios
        $partes = ParteDiario::whereIn('obra_id', $obrasIds)
            ->whereBetween('fecha', [$inicio, $fin])
            ->select('fecha', DB::raw('COUNT(*) as total'))
            ->groupBy('fecha')
            ->get();

        foreach ($partes as $parte) {
            $fechaStr = $parte->fecha->format('Y-m-d');
            if (isset($eventos[$fechaStr])) {
                $eventos[$fechaStr]['partes'] = $parte->total;
            }
        }

        // Inspecciones de maquinaria pendientes
        $maquinariaIds = Maquinaria::whereIn('obra_asignada_id', $obrasIds)->pluck('id');
        if ($maquinariaIds->isNotEmpty()) {
            $inspecciones = DB::table('maquinaria_inspecciones')
                ->whereIn('maquinaria_id', $maquinariaIds)
                ->whereBetween('fecha_proxima_inspeccion', [$inicio, $fin])
                ->select('fecha_proxima_inspeccion as fecha', DB::raw('COUNT(*) as total'))
                ->groupBy('fecha_proxima_inspeccion')
                ->get();

            foreach ($inspecciones as $insp) {
                $fechaStr = Carbon::parse($insp->fecha)->format('Y-m-d');
                if (isset($eventos[$fechaStr])) {
                    $eventos[$fechaStr]['inspecciones'] = $insp->total;
                }
            }
        }

        // Fechas fin previstas de obras
        $vencimientos = Obra::whereIn('id', $obrasIds)
            ->whereBetween('fecha_fin_prevista', [$inicio, $fin])
            ->select('fecha_fin_prevista as fecha', DB::raw('COUNT(*) as total'))
            ->groupBy('fecha_fin_prevista')
            ->get();

        foreach ($vencimientos as $venc) {
            $fechaStr = $venc->fecha->format('Y-m-d');
            if (isset($eventos[$fechaStr])) {
                $eventos[$fechaStr]['vencimientos'] = $venc->total;
            }
        }

        return [
            'semana' => $inicio->translatedFormat('d M') . ' - ' . $fin->translatedFormat('d M Y'),
            'dias' => array_values($eventos),
        ];
    }

    /**
     * Calendario vacío
     */
    protected function getCalendarioVacio(?Carbon $fechaInicio = null): array
    {
        $inicio = $fechaInicio ?? now()->startOfWeek();
        $fin = $inicio->copy()->endOfWeek();

        $dias = [];
        for ($fecha = $inicio->copy(); $fecha <= $fin; $fecha->addDay()) {
            $dias[] = [
                'fecha' => $fecha->format('Y-m-d'),
                'dia' => $fecha->translatedFormat('D'),
                'dia_mes' => $fecha->day,
                'es_hoy' => $fecha->isToday(),
                'partes' => 0,
                'inspecciones' => 0,
                'vencimientos' => 0,
            ];
        }

        return [
            'semana' => $inicio->translatedFormat('d M') . ' - ' . $fin->translatedFormat('d M Y'),
            'dias' => $dias,
        ];
    }

    /**
     * Partes pendientes de completar/validar de sus obras
     */
    public function getPartesPendientes(int $limite = 10): Collection
    {
        $obrasIds = $this->getObrasIds();

        if ($obrasIds->isEmpty()) {
            return collect();
        }

        return ParteDiario::whereIn('obra_id', $obrasIds)
            ->whereIn('estado', ['borrador', 'completado'])
            ->with(['obra:id,codigo,nombre'])
            ->select('id', 'obra_id', 'fecha', 'estado', 'jornada', 'importe_total_calculado')
            ->orderBy('fecha', 'desc')
            ->limit($limite)
            ->get()
            ->map(function ($parte) {
                return [
                    'id' => $parte->id,
                    'obra_codigo' => $parte->obra?->codigo ?? '-',
                    'obra_nombre' => $parte->obra?->nombre ?? '-',
                    'fecha' => $parte->fecha->format('d/m/Y'),
                    'estado' => $parte->estado,
                    'jornada' => $parte->jornada,
                    'importe' => round($parte->importe_total_calculado ?? 0, 2),
                ];
            });
    }

    /**
     * Alertas relevantes para el encargado filtradas por sus obras
     */
    public function getAlertasEncargado(int $limite = 10): Collection
    {
        $obrasIds = $this->getObrasIds();

        // Alertas para el rol Encargado
        $alertas = Alerta::query()
            ->where('resuelta', false)
            ->where(function ($q) use ($obrasIds) {
                // Alertas para el rol Encargado
                $q->whereJsonContains('para_roles', 'Encargado');

                // O alertas de registros relacionados con sus obras
                if ($obrasIds->isNotEmpty()) {
                    $q->orWhere(function ($subQ) use ($obrasIds) {
                        $subQ->where('alertable_type', 'App\\Models\\Obra')
                            ->whereIn('alertable_id', $obrasIds);
                    });
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
            ->get()
            ->map(function ($alerta) {
                return [
                    'id' => $alerta->id,
                    'tipo' => $alerta->tipo,
                    'titulo' => $alerta->titulo,
                    'mensaje' => $alerta->mensaje,
                    'prioridad' => $alerta->prioridad,
                    'fecha_vencimiento' => $alerta->fecha_vencimiento?->format('d/m/Y'),
                    'leida' => $alerta->leida,
                ];
            });

        return $alertas;
    }

    /**
     * Contar alertas no leídas del encargado
     */
    protected function getAlertasCount(): int
    {
        $obrasIds = $this->getObrasIds();

        return Alerta::query()
            ->where('resuelta', false)
            ->where('leida', false)
            ->where(function ($q) use ($obrasIds) {
                $q->whereJsonContains('para_roles', 'Encargado');
                if ($obrasIds->isNotEmpty()) {
                    $q->orWhere(function ($subQ) use ($obrasIds) {
                        $subQ->where('alertable_type', 'App\\Models\\Obra')
                            ->whereIn('alertable_id', $obrasIds);
                    });
                }
            })
            ->count();
    }

    /**
     * Obtener opciones de filtros (obras del encargado)
     */
    public function getOpcionesFiltros(): array
    {
        $obras = Obra::where('encargado_id', $this->encargadoId)
            ->select('id', 'codigo', 'nombre', 'estado')
            ->orderBy('codigo')
            ->get()
            ->map(fn($o) => [
                'id' => $o->id,
                'codigo' => $o->codigo,
                'nombre' => $o->nombre,
                'codigo_nombre' => $o->codigo . ' - ' . $o->nombre,
            ]);

        return [
            'obras' => $obras,
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
