<?php

namespace App\Services;

use App\Models\Trabajador;
use App\Models\Fichaje;
use App\Models\TrabajadorDocumento;
use App\Models\TrabajadorFormacion;
use App\Models\EpiEntrega;
use App\Models\TrabajadorBono;
use App\Models\PrimaTrabajador;
use App\Models\Alerta;
use App\Models\DocumentoLectura;
use App\Models\ParteDiario;
use App\Models\ParteDiarioProduccion;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TrabajadorDashboardService
{
    protected ?int $trabajadorId = null;
    protected ?Trabajador $trabajador = null;

    /**
     * Establecer el trabajador para todas las consultas
     */
    public function setTrabajadorId(int $trabajadorId): self
    {
        $this->trabajadorId = $trabajadorId;
        $this->trabajador = Trabajador::find($trabajadorId);
        return $this;
    }

    /**
     * Obtener el trabajador actual
     */
    public function getTrabajador(): ?Trabajador
    {
        return $this->trabajador;
    }

    /**
     * Verificar que el trabajador existe
     */
    public function trabajadorExiste(): bool
    {
        return $this->trabajador !== null;
    }

    /**
     * KPIs principales del trabajador
     */
    public function getKpis(): array
    {
        if (!$this->trabajador) {
            return $this->getKpisVacios();
        }

        $mesActual = now()->month;
        $anioActual = now()->year;

        return [
            'horas_mes_actual' => $this->getHorasMes($mesActual, $anioActual),
            'horas_extra_mes' => $this->getHorasExtraMes($mesActual, $anioActual),
            'dias_vacaciones_disponibles' => round($this->trabajador->vacaciones_acumuladas ?? 0, 1),
            'vacaciones_anuales' => $this->trabajador->vacaciones_anuales ?? 22,
            'documentos_pendientes' => $this->getDocumentosPendientesLectura(),
            'epis_activos' => $this->getEpisAsignadosCount(),
            'formaciones_vigentes' => $this->getFormacionesVigentesCount(),
            'formaciones_proximas_caducar' => $this->getFormacionesProximasCaducarCount(),
            'formaciones_caducadas' => $this->getFormacionesCaducadasCount(),
            'primas_pendientes' => $this->getPrimasPendientesImporte(),
            'alertas_no_leidas' => $this->getAlertasNoLeidasCount(),
        ];
    }

    /**
     * Obtener fichajes del mes actual
     */
    public function getMisFichajesMes(?int $mes = null, ?int $anio = null): Collection
    {
        if (!$this->trabajador) {
            return collect();
        }

        $mes = $mes ?? now()->month;
        $anio = $anio ?? now()->year;

        return Fichaje::where('trabajador_id', $this->trabajadorId)
            ->delMes($mes, $anio)
            ->with('obra:id,codigo,nombre')
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function ($fichaje) {
                return [
                    'id' => $fichaje->id,
                    'fecha' => $fichaje->fecha->format('d/m/Y'),
                    'dia_semana' => $fichaje->fecha->translatedFormat('l'),
                    'obra' => $fichaje->obra?->codigo ?? '-',
                    'obra_nombre' => $fichaje->obra?->nombre ?? '-',
                    'hora_entrada' => $fichaje->hora_entrada?->format('H:i'),
                    'hora_salida' => $fichaje->hora_salida?->format('H:i'),
                    'horas_trabajadas' => round($fichaje->horas_trabajadas ?? 0, 2),
                    'horas_extra' => round($fichaje->horas_extra ?? 0, 2),
                    'validado' => $fichaje->validado,
                    'abierto' => $fichaje->hora_entrada && !$fichaje->hora_salida,
                ];
            });
    }

    /**
     * Resumen de horas del mes
     */
    public function getResumenHorasMes(?int $mes = null, ?int $anio = null): array
    {
        if (!$this->trabajador) {
            return ['total_horas' => 0, 'total_extra' => 0, 'dias_trabajados' => 0, 'pendientes_validar' => 0];
        }

        $mes = $mes ?? now()->month;
        $anio = $anio ?? now()->year;

        $fichajes = Fichaje::where('trabajador_id', $this->trabajadorId)
            ->delMes($mes, $anio)
            ->get();

        return [
            'total_horas' => round($fichajes->sum('horas_trabajadas'), 2),
            'total_extra' => round($fichajes->sum('horas_extra'), 2),
            'dias_trabajados' => $fichajes->whereNotNull('hora_salida')->count(),
            'pendientes_validar' => $fichajes->where('validado', false)->whereNotNull('hora_salida')->count(),
            'mes_nombre' => Carbon::createFromDate($anio, $mes, 1)->translatedFormat('F Y'),
        ];
    }

    /**
     * Verificar si tiene fichaje abierto hoy
     */
    public function getFichajeAbiertoHoy(): ?array
    {
        if (!$this->trabajador) {
            return null;
        }

        $fichaje = Fichaje::where('trabajador_id', $this->trabajadorId)
            ->where('fecha', today())
            ->whereNotNull('hora_entrada')
            ->whereNull('hora_salida')
            ->with('obra:id,codigo,nombre')
            ->first();

        if (!$fichaje) {
            return null;
        }

        return [
            'id' => $fichaje->id,
            'hora_entrada' => $fichaje->hora_entrada->format('H:i'),
            'obra' => $fichaje->obra?->codigo ?? '-',
            'obra_nombre' => $fichaje->obra?->nombre ?? '-',
        ];
    }

    /**
     * Obtener datos de vacaciones
     */
    public function getMisVacaciones(): array
    {
        if (!$this->trabajador) {
            return ['acumuladas' => 0, 'anuales' => 22, 'porcentaje' => 0];
        }

        $acumuladas = $this->trabajador->vacaciones_acumuladas ?? 0;
        $anuales = $this->trabajador->vacaciones_anuales ?? 22;
        $porcentaje = $anuales > 0 ? min(100, ($acumuladas / $anuales) * 100) : 0;

        return [
            'acumuladas' => round($acumuladas, 1),
            'anuales' => $anuales,
            'porcentaje' => round($porcentaje, 1),
            'fecha_alta' => $this->trabajador->fecha_alta?->format('d/m/Y'),
            'antiguedad_anios' => $this->trabajador->fecha_alta
                ? $this->trabajador->fecha_alta->diffInYears(now())
                : 0,
            'antiguedad_texto' => $this->calcularAntiguedadTexto(),
        ];
    }

    /**
     * Obtener documentos visibles para el trabajador
     */
    public function getMisDocumentos(): Collection
    {
        if (!$this->trabajador) {
            return collect();
        }

        return TrabajadorDocumento::where('trabajador_id', $this->trabajadorId)
            ->where('visible_trabajador', true)
            ->orderBy('fecha_documento', 'desc')
            ->get()
            ->map(function ($doc) {
                $lectura = $doc->requiere_lectura
                    ? DocumentoLectura::where('documento_id', $doc->id)
                        ->where('trabajador_id', $this->trabajadorId)
                        ->where('aceptado', true)
                        ->first()
                    : null;

                return [
                    'id' => $doc->id,
                    'tipo' => $doc->tipo,
                    'tipo_formateado' => $this->formatearTipoDocumento($doc->tipo),
                    'nombre' => $doc->nombre,
                    'fecha_documento' => $doc->fecha_documento?->format('d/m/Y'),
                    'archivo_path' => $doc->archivo_path,
                    'requiere_lectura' => $doc->requiere_lectura,
                    'leido' => $lectura ? true : false,
                    'fecha_lectura' => $lectura?->fecha_lectura?->format('d/m/Y H:i'),
                ];
            });
    }

    /**
     * Registrar lectura de documento
     */
    public function registrarLecturaDocumento(int $documentoId, string $ip, string $userAgent): array
    {
        if (!$this->trabajador) {
            return ['success' => false, 'message' => 'Trabajador no encontrado'];
        }

        // Verificar que el documento pertenece al trabajador y es visible
        $documento = TrabajadorDocumento::where('id', $documentoId)
            ->where('trabajador_id', $this->trabajadorId)
            ->where('visible_trabajador', true)
            ->where('requiere_lectura', true)
            ->first();

        if (!$documento) {
            return ['success' => false, 'message' => 'Documento no encontrado o no requiere confirmación'];
        }

        // Verificar si ya existe lectura
        $existeLectura = DocumentoLectura::where('documento_id', $documentoId)
            ->where('trabajador_id', $this->trabajadorId)
            ->where('aceptado', true)
            ->exists();

        if ($existeLectura) {
            return ['success' => true, 'message' => 'Ya habías confirmado la lectura de este documento'];
        }

        DocumentoLectura::create([
            'documento_id' => $documentoId,
            'trabajador_id' => $this->trabajadorId,
            'fecha_lectura' => now(),
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'aceptado' => true,
        ]);

        return ['success' => true, 'message' => 'Lectura confirmada correctamente'];
    }

    /**
     * Obtener EPIs asignados al trabajador
     */
    public function getMisEpis(): Collection
    {
        if (!$this->trabajador) {
            return collect();
        }

        return EpiEntrega::where('trabajador_id', $this->trabajadorId)
            ->whereNull('fecha_devolucion')
            ->with(['inventario.catalogo'])
            ->orderBy('fecha_entrega', 'desc')
            ->get()
            ->map(function ($entrega) {
                $catalogo = $entrega->inventario?->catalogo;
                $inventario = $entrega->inventario;
                $fechaCaducidad = $inventario?->fecha_caducidad;

                $diasParaCaducar = null;
                $estado = 'ok';
                if ($fechaCaducidad) {
                    $diasParaCaducar = now()->diffInDays($fechaCaducidad, false);
                    if ($diasParaCaducar < 0) {
                        $estado = 'caducado';
                    } elseif ($diasParaCaducar <= 30) {
                        $estado = 'proximo';
                    }
                }

                return [
                    'id' => $entrega->id,
                    'nombre' => $catalogo?->nombre ?? 'EPI desconocido',
                    'categoria' => $catalogo?->categoria ?? '-',
                    'fecha_entrega' => $entrega->fecha_entrega?->format('d/m/Y'),
                    'fecha_caducidad' => $fechaCaducidad?->format('d/m/Y'),
                    'dias_para_caducar' => $diasParaCaducar !== null ? max(0, $diasParaCaducar) : null,
                    'estado' => $estado,
                    'requiere_revision' => $catalogo?->requiere_revision ?? false,
                ];
            });
    }

    /**
     * Obtener formaciones del trabajador
     */
    public function getMisFormaciones(): Collection
    {
        if (!$this->trabajador) {
            return collect();
        }

        return TrabajadorFormacion::where('trabajador_id', $this->trabajadorId)
            ->with('tipo')
            ->orderByRaw('CASE
                WHEN fecha_caducidad IS NULL THEN 1
                WHEN fecha_caducidad < NOW() THEN 0
                ELSE 2
            END')
            ->orderBy('fecha_caducidad', 'asc')
            ->get()
            ->map(function ($formacion) {
                $caducada = $formacion->caducado;
                $proximaCaducar = $formacion->proximo_a_caducar;

                $estado = 'vigente';
                if ($caducada) {
                    $estado = 'caducada';
                } elseif ($proximaCaducar) {
                    $estado = 'proxima';
                } elseif (!$formacion->fecha_caducidad) {
                    $estado = 'sin_caducidad';
                }

                return [
                    'id' => $formacion->id,
                    'tipo' => $formacion->tipo?->nombre ?? 'Sin tipo',
                    'fecha_realizacion' => $formacion->fecha_realizacion?->format('d/m/Y'),
                    'fecha_caducidad' => $formacion->fecha_caducidad?->format('d/m/Y'),
                    'centro_formacion' => $formacion->centro_formacion,
                    'estado' => $estado,
                    'dias_restantes' => $formacion->fecha_caducidad && !$caducada
                        ? max(0, now()->diffInDays($formacion->fecha_caducidad, false))
                        : null,
                    'tiene_certificado' => !empty($formacion->certificado_path),
                ];
            });
    }

    /**
     * Obtener primas y bonos del trabajador
     */
    public function getMisPrimas(): array
    {
        if (!$this->trabajador) {
            return ['items' => collect(), 'totales' => $this->getTotalesPrimasVacios()];
        }

        // Bonos manuales
        $bonos = TrabajadorBono::where('trabajador_id', $this->trabajadorId)
            ->with('obra:id,codigo,nombre')
            ->orderBy('fecha', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($bono) {
                return [
                    'id' => $bono->id,
                    'tipo' => 'bono',
                    'concepto' => $bono->concepto,
                    'tipo_bono' => $bono->tipo_formateado,
                    'fecha' => $bono->fecha?->format('d/m/Y'),
                    'obra' => $bono->obra?->codigo ?? '-',
                    'importe' => round($bono->importe, 2),
                    'pagado' => $bono->pagado,
                    'fecha_pago' => $bono->fecha_pago?->format('d/m/Y'),
                ];
            });

        // Primas de producción
        $primas = PrimaTrabajador::where('trabajador_id', $this->trabajadorId)
            ->with('obra:id,codigo,nombre')
            ->orderBy('fecha', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($prima) {
                return [
                    'id' => $prima->id,
                    'tipo' => 'prima',
                    'concepto' => 'Prima por producción',
                    'tipo_bono' => 'Prima Producción',
                    'fecha' => $prima->fecha?->format('d/m/Y'),
                    'obra' => $prima->obra?->codigo ?? '-',
                    'importe' => round($prima->importe_prima, 2),
                    'pagado' => $prima->pagada,
                    'fecha_pago' => $prima->fecha_pago?->format('d/m/Y'),
                ];
            });

        // Combinar y ordenar
        $items = $bonos->merge($primas)->sortByDesc('fecha')->values();

        // Totales
        $totalBonosPendientes = TrabajadorBono::where('trabajador_id', $this->trabajadorId)
            ->pendientesPago()
            ->sum('importe');

        $totalBonosPagados = TrabajadorBono::where('trabajador_id', $this->trabajadorId)
            ->pagados()
            ->sum('importe');

        $totalPrimasPendientes = PrimaTrabajador::where('trabajador_id', $this->trabajadorId)
            ->pendientes()
            ->sum('importe_prima');

        $totalPrimasPagadas = PrimaTrabajador::where('trabajador_id', $this->trabajadorId)
            ->pagadas()
            ->sum('importe_prima');

        return [
            'items' => $items,
            'totales' => [
                'pendiente' => round($totalBonosPendientes + $totalPrimasPendientes, 2),
                'pagado' => round($totalBonosPagados + $totalPrimasPagadas, 2),
                'total' => round($totalBonosPendientes + $totalPrimasPendientes + $totalBonosPagados + $totalPrimasPagadas, 2),
            ],
        ];
    }

    /**
     * Obtener alertas personales del trabajador
     */
    public function getMisAlertas(int $limite = 10): Collection
    {
        if (!$this->trabajador || !$this->trabajador->user_id) {
            return collect();
        }

        $userId = $this->trabajador->user_id;

        return Alerta::query()
            ->where('resuelta', false)
            ->where(function ($q) use ($userId) {
                $q->where('para_usuario_id', $userId)
                    ->orWhereJsonContains('para_roles', 'Trabajador');
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
    }

    /**
     * Producción diaria del trabajador (de partes donde está asignado)
     */
    public function getProduccionDiaria(?Carbon $fechaDesde = null, ?Carbon $fechaHasta = null): array
    {
        if (!$this->trabajador) {
            return $this->getProduccionVacia($fechaDesde, $fechaHasta);
        }

        // Fechas por defecto: hoy
        $fechaDesde = $fechaDesde ?? today();
        $fechaHasta = $fechaHasta ?? today();

        // Obtener IDs de partes diarios donde el trabajador está asignado en el período
        $partesDiariosIds = \App\Models\ParteDiarioTrabajador::where('trabajador_id', $this->trabajadorId)
            ->pluck('parte_diario_id');

        if ($partesDiariosIds->isEmpty()) {
            return $this->getProduccionVacia($fechaDesde, $fechaHasta);
        }

        // Fecha de comparación: mismo período anterior
        $diasDiferencia = $fechaDesde->diffInDays($fechaHasta);
        $fechaDesdeAnterior = $fechaDesde->copy()->subDays($diasDiferencia + 1);
        $fechaHastaAnterior = $fechaDesde->copy()->subDay();

        // Producción del período actual agrupada por categoría
        $produccionActualPorCat = ParteDiarioProduccion::query()
            ->join('partes_diarios', 'parte_diario_producciones.parte_diario_id', '=', 'partes_diarios.id')
            ->join('obra_conceptos_produccion', 'parte_diario_producciones.concepto_produccion_id', '=', 'obra_conceptos_produccion.id')
            ->whereIn('partes_diarios.id', $partesDiariosIds)
            ->whereBetween('partes_diarios.fecha', [$fechaDesde, $fechaHasta])
            ->whereIn('partes_diarios.estado', ['completado', 'validado', 'borrador'])
            ->select([
                'obra_conceptos_produccion.categoria',
                DB::raw('SUM(parte_diario_producciones.cantidad) as total')
            ])
            ->groupBy('obra_conceptos_produccion.categoria')
            ->pluck('total', 'categoria')
            ->toArray();

        // Producción del período anterior para variaciones
        $produccionAnteriorPorCat = ParteDiarioProduccion::query()
            ->join('partes_diarios', 'parte_diario_producciones.parte_diario_id', '=', 'partes_diarios.id')
            ->join('obra_conceptos_produccion', 'parte_diario_producciones.concepto_produccion_id', '=', 'obra_conceptos_produccion.id')
            ->whereIn('partes_diarios.id', $partesDiariosIds)
            ->whereBetween('partes_diarios.fecha', [$fechaDesdeAnterior, $fechaHastaAnterior])
            ->whereIn('partes_diarios.estado', ['completado', 'validado', 'borrador'])
            ->select([
                'obra_conceptos_produccion.categoria',
                DB::raw('SUM(parte_diario_producciones.cantidad) as total')
            ])
            ->groupBy('obra_conceptos_produccion.categoria')
            ->pluck('total', 'categoria')
            ->toArray();

        // Unidades por categoría
        $unidadesPorCategoria = ParteDiarioProduccion::query()
            ->join('partes_diarios', 'parte_diario_producciones.parte_diario_id', '=', 'partes_diarios.id')
            ->join('obra_conceptos_produccion', 'parte_diario_producciones.concepto_produccion_id', '=', 'obra_conceptos_produccion.id')
            ->whereIn('partes_diarios.id', $partesDiariosIds)
            ->whereBetween('partes_diarios.fecha', [$fechaDesde, $fechaHasta])
            ->whereIn('partes_diarios.estado', ['completado', 'validado', 'borrador'])
            ->select('obra_conceptos_produccion.categoria', 'obra_conceptos_produccion.unidad')
            ->distinct()
            ->get()
            ->pluck('unidad', 'categoria')
            ->toArray();

        // Resumen actual (importe y num partes)
        $resumenActual = ParteDiario::whereIn('id', $partesDiariosIds)
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->whereIn('estado', ['completado', 'validado', 'borrador'])
            ->select([
                DB::raw('COALESCE(SUM(importe_total_calculado), 0) as importe'),
                DB::raw('COUNT(*) as num_partes')
            ])
            ->first();

        // Resumen anterior para variación de importe
        $resumenAnterior = ParteDiario::whereIn('id', $partesDiariosIds)
            ->whereBetween('fecha', [$fechaDesdeAnterior, $fechaHastaAnterior])
            ->whereIn('estado', ['completado', 'validado', 'borrador'])
            ->select([
                DB::raw('COALESCE(SUM(importe_total_calculado), 0) as importe'),
            ])
            ->first();

        // Construir categorías con datos
        $categorias = [];
        foreach ($produccionActualPorCat as $cat => $cantidad) {
            $categorias[$cat] = [
                'cantidad' => $cantidad,
                'unidad' => $unidadesPorCategoria[$cat] ?? 'unidades',
            ];
        }

        // Calcular variaciones
        $variaciones = [];
        foreach ($produccionActualPorCat as $cat => $cantidadActual) {
            $cantidadAnterior = $produccionAnteriorPorCat[$cat] ?? 0;
            $variaciones[$cat] = $this->calcularVariacion($cantidadActual, $cantidadAnterior);
        }

        // Variación de importe
        $variaciones['importe'] = $this->calcularVariacion(
            $resumenActual->importe ?? 0,
            $resumenAnterior->importe ?? 0
        );

        return [
            'hoy' => [
                'categorias' => $categorias,
                'importe' => round($resumenActual->importe ?? 0, 2),
                'num_partes' => $resumenActual->num_partes ?? 0,
            ],
            'variaciones' => $variaciones,
            'fecha' => $fechaDesde->eq($fechaHasta)
                ? $fechaDesde->format('d/m/Y')
                : $fechaDesde->format('d/m/Y') . ' - ' . $fechaHasta->format('d/m/Y'),
        ];
    }

    /**
     * Obtener IDs de obras donde el trabajador ha fichado
     */
    protected function getObrasIdsDondeFicho(): Collection
    {
        if (!$this->trabajador) {
            return collect();
        }

        return Fichaje::where('trabajador_id', $this->trabajadorId)
            ->whereNotNull('obra_id')
            ->distinct()
            ->pluck('obra_id');
    }

    /**
     * Estructura vacía de producción
     */
    protected function getProduccionVacia(?Carbon $fechaDesde = null, ?Carbon $fechaHasta = null): array
    {
        $fechaDesde = $fechaDesde ?? today();
        $fechaHasta = $fechaHasta ?? today();

        return [
            'hoy' => [
                'categorias' => [],
                'importe' => 0,
                'num_partes' => 0,
            ],
            'variaciones' => [],
            'fecha' => $fechaDesde->eq($fechaHasta)
                ? $fechaDesde->format('d/m/Y')
                : $fechaDesde->format('d/m/Y') . ' - ' . $fechaHasta->format('d/m/Y'),
        ];
    }

    /**
     * Calcular variación porcentual
     */
    protected function calcularVariacion(float $actual, float $anterior): array
    {
        if ($anterior == 0) {
            return ['valor' => 0, 'tipo' => 'neutral'];
        }

        $porcentaje = (($actual - $anterior) / $anterior) * 100;
        $tipo = $porcentaje > 0 ? 'positive' : ($porcentaje < 0 ? 'negative' : 'neutral');

        return [
            'valor' => round(abs($porcentaje), 1),
            'tipo' => $tipo,
        ];
    }

    // ==========================================
    // MÉTODOS AUXILIARES PRIVADOS
    // ==========================================

    protected function getKpisVacios(): array
    {
        return [
            'horas_mes_actual' => 0,
            'horas_extra_mes' => 0,
            'dias_vacaciones_disponibles' => 0,
            'vacaciones_anuales' => 22,
            'documentos_pendientes' => 0,
            'epis_activos' => 0,
            'formaciones_vigentes' => 0,
            'formaciones_proximas_caducar' => 0,
            'formaciones_caducadas' => 0,
            'primas_pendientes' => 0,
            'alertas_no_leidas' => 0,
        ];
    }

    protected function getTotalesPrimasVacios(): array
    {
        return [
            'pendiente' => 0,
            'pagado' => 0,
            'total' => 0,
        ];
    }

    protected function getHorasMes(int $mes, int $anio): float
    {
        return round(
            Fichaje::where('trabajador_id', $this->trabajadorId)
                ->delMes($mes, $anio)
                ->sum('horas_trabajadas'),
            2
        );
    }

    protected function getHorasExtraMes(int $mes, int $anio): float
    {
        return round(
            Fichaje::where('trabajador_id', $this->trabajadorId)
                ->delMes($mes, $anio)
                ->sum('horas_extra'),
            2
        );
    }

    protected function getDocumentosPendientesLectura(): int
    {
        $documentosRequierenLectura = TrabajadorDocumento::where('trabajador_id', $this->trabajadorId)
            ->where('visible_trabajador', true)
            ->where('requiere_lectura', true)
            ->pluck('id');

        if ($documentosRequierenLectura->isEmpty()) {
            return 0;
        }

        $leidos = DocumentoLectura::where('trabajador_id', $this->trabajadorId)
            ->whereIn('documento_id', $documentosRequierenLectura)
            ->where('aceptado', true)
            ->pluck('documento_id');

        return $documentosRequierenLectura->diff($leidos)->count();
    }

    protected function getEpisAsignadosCount(): int
    {
        return EpiEntrega::where('trabajador_id', $this->trabajadorId)
            ->whereNull('fecha_devolucion')
            ->count();
    }

    protected function getFormacionesProximasCaducarCount(): int
    {
        return TrabajadorFormacion::where('trabajador_id', $this->trabajadorId)
            ->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<=', now()->addDays(30))
            ->where('fecha_caducidad', '>=', now())
            ->count();
    }

    protected function getFormacionesCaducadasCount(): int
    {
        return TrabajadorFormacion::where('trabajador_id', $this->trabajadorId)
            ->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<', now())
            ->count();
    }

    protected function getFormacionesVigentesCount(): int
    {
        return TrabajadorFormacion::where('trabajador_id', $this->trabajadorId)
            ->where(function ($q) {
                $q->whereNull('fecha_caducidad')
                    ->orWhere('fecha_caducidad', '>=', now());
            })
            ->count();
    }

    protected function getPrimasPendientesImporte(): float
    {
        $primas = PrimaTrabajador::where('trabajador_id', $this->trabajadorId)
            ->pendientes()
            ->sum('importe_prima');

        $bonos = TrabajadorBono::where('trabajador_id', $this->trabajadorId)
            ->pendientesPago()
            ->sum('importe');

        return round($primas + $bonos, 2);
    }

    protected function getAlertasNoLeidasCount(): int
    {
        if (!$this->trabajador || !$this->trabajador->user_id) {
            return 0;
        }

        return Alerta::where('resuelta', false)
            ->where('leida', false)
            ->where(function ($q) {
                $q->where('para_usuario_id', $this->trabajador->user_id)
                    ->orWhereJsonContains('para_roles', 'Trabajador');
            })
            ->count();
    }

    protected function formatearTipoDocumento(string $tipo): string
    {
        $tipos = [
            'contrato' => 'Contrato',
            'nomina' => 'Nómina',
            'dni' => 'DNI',
            'ss' => 'Seguridad Social',
            'certificado_formacion' => 'Certificado Formación',
            'apto_medico' => 'Apto Médico',
            'otro' => 'Otro',
        ];

        return $tipos[$tipo] ?? ucfirst($tipo);
    }

    protected function calcularAntiguedadTexto(): string
    {
        if (!$this->trabajador || !$this->trabajador->fecha_alta) {
            return '-';
        }

        $fecha = $this->trabajador->fecha_alta;
        $anios = $fecha->diffInYears(now());
        $meses = $fecha->diffInMonths(now()) % 12;

        if ($anios > 0 && $meses > 0) {
            return "{$anios} año" . ($anios > 1 ? 's' : '') . " y {$meses} mes" . ($meses > 1 ? 'es' : '');
        } elseif ($anios > 0) {
            return "{$anios} año" . ($anios > 1 ? 's' : '');
        } else {
            return "{$meses} mes" . ($meses > 1 ? 'es' : '');
        }
    }
}
