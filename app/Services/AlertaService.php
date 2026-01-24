<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\AlertaConfiguracion;
use App\Models\CaducidadGeneral;
use App\Models\TrabajadorFormacion;
use App\Models\TrabajadorDocumento;
use App\Models\EpiInventario;
use App\Models\EpiRevision;
use App\Models\Vehiculo;
use App\Models\VehiculoDocumento;
use App\Models\Contrato;
use App\Models\SubcontrataDocumentoCae;
use App\Models\User;
use App\Notifications\AlertaNotification;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AlertaService
{
    /**
     * Tipos de alerta y sus etiquetas
     */
    public const TIPOS_ALERTA = [
        'formacion' => 'Formación',
        'documento_trabajador' => 'Documento Trabajador',
        'apto_medico' => 'Apto Médico',
        'epi_caducidad' => 'EPI - Caducidad',
        'epi_revision' => 'EPI - Revisión',
        'itv' => 'ITV Vehículo',
        'seguro_vehiculo' => 'Seguro Vehículo',
        'documento_vehiculo' => 'Documento Vehículo',
        'contrato_vencimiento' => 'Contrato - Vencimiento',
        'contrato_garantia' => 'Contrato - Garantía',
        'documento_cae' => 'Documento CAE',
        'caducidad_general' => 'Caducidad Empresa',
    ];

    /**
     * Obtener alertas para un usuario según sus roles (paginadas)
     */
    public function getAlertasParaUsuario(int $userId, array $roles, array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Alerta::query()
            ->where(function ($q) use ($userId, $roles) {
                // Alertas dirigidas específicamente al usuario
                $q->where('para_usuario_id', $userId);

                // O alertas dirigidas a sus roles
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
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if (!empty($filtros['tipo'])) {
            $query->where('tipo', $filtros['tipo']);
        }

        if (!empty($filtros['prioridad'])) {
            $query->where('prioridad', $filtros['prioridad']);
        }

        if (!empty($filtros['estado'])) {
            switch ($filtros['estado']) {
                case 'no_leida':
                    $query->where('leida', false);
                    break;
                case 'leida':
                    $query->where('leida', true)->where('resuelta', false);
                    break;
                case 'resuelta':
                    $query->where('resuelta', true);
                    break;
            }
        }

        if (!empty($filtros['fecha_desde'])) {
            $query->where('fecha_vencimiento', '>=', $filtros['fecha_desde']);
        }

        if (!empty($filtros['fecha_hasta'])) {
            $query->where('fecha_vencimiento', '<=', $filtros['fecha_hasta']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Obtener estadísticas de alertas para un usuario
     */
    public function getEstadisticasParaUsuario(int $userId, array $roles): array
    {
        $baseQuery = function () use ($userId, $roles) {
            return Alerta::query()
                ->where(function ($q) use ($userId, $roles) {
                    $q->where('para_usuario_id', $userId);
                    foreach ($roles as $rol) {
                        $q->orWhereJsonContains('para_roles', $rol);
                    }
                });
        };

        return [
            'total' => $baseQuery()->count(),
            'no_leidas' => $baseQuery()->where('leida', false)->count(),
            'criticas' => $baseQuery()->where('prioridad', 'critica')->where('resuelta', false)->count(),
            'pendientes' => $baseQuery()->where('resuelta', false)->count(),
            'resueltas_hoy' => $baseQuery()->where('resuelta', true)
                ->whereDate('fecha_resolucion', today())
                ->count(),
        ];
    }

    /**
     * Obtener contador de alertas no leídas para un usuario
     */
    public function contarNoLeidas(int $userId, array $roles): int
    {
        return Alerta::query()
            ->where('leida', false)
            ->where(function ($q) use ($userId, $roles) {
                $q->where('para_usuario_id', $userId);
                foreach ($roles as $rol) {
                    $q->orWhereJsonContains('para_roles', $rol);
                }
            })
            ->count();
    }

    /**
     * Obtener alertas recientes para dropdown
     */
    public function getAlertasRecientes(int $userId, array $roles, int $limite = 5): Collection
    {
        return Alerta::query()
            ->where('leida', false)
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
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Generar todas las alertas de caducidad
     */
    public function generarAlertasCaducidades(?string $tipoEspecifico = null): array
    {
        $resultados = [];

        $configuraciones = AlertaConfiguracion::activas()->get();

        foreach ($configuraciones as $config) {
            if ($tipoEspecifico && $config->tipo !== $tipoEspecifico) {
                continue;
            }

            $metodo = 'generarAlertas' . str_replace('_', '', ucwords($config->tipo, '_'));

            if (method_exists($this, $metodo)) {
                $resultados[$config->tipo] = $this->$metodo($config->dias_antelacion);
            } else {
                $resultados[$config->tipo] = 0;
            }
        }

        return $resultados;
    }

    /**
     * Generar alertas de formaciones próximas a caducar
     */
    protected function generarAlertasFormacion(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        $formaciones = TrabajadorFormacion::with(['trabajador', 'tipo'])
            ->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<=', $fechaLimite)
            ->where('fecha_caducidad', '>=', now()->subDays(30)) // Incluye vencidas hasta 30 días
            ->get();

        foreach ($formaciones as $formacion) {
            $tipoNombre = $formacion->tipo ? $formacion->tipo->nombre : 'Formación';
            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'formacion',
                'titulo' => "Formación próxima a caducar: {$tipoNombre}",
                'mensaje' => "La formación '{$tipoNombre}' del trabajador {$formacion->trabajador->nombre_completo} caduca el " . $formacion->fecha_caducidad->format('d/m/Y'),
                'prioridad' => $this->calcularPrioridad($formacion->fecha_caducidad),
                'alertable_type' => TrabajadorFormacion::class,
                'alertable_id' => $formacion->id,
                'para_roles' => ['Administrador', 'RRHH'],
                'fecha_vencimiento' => $formacion->fecha_caducidad,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generar alertas de documentos de trabajador
     */
    protected function generarAlertasDocumentoTrabajador(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        $documentos = TrabajadorDocumento::with('trabajador')
            ->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<=', $fechaLimite)
            ->where('fecha_caducidad', '>=', now()->subDays(30))
            ->get();

        foreach ($documentos as $documento) {
            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'documento_trabajador',
                'titulo' => "Documento próximo a caducar: {$documento->nombre}",
                'mensaje' => "El documento '{$documento->nombre}' del trabajador {$documento->trabajador->nombre_completo} caduca el " . $documento->fecha_caducidad->format('d/m/Y'),
                'prioridad' => $this->calcularPrioridad($documento->fecha_caducidad),
                'alertable_type' => TrabajadorDocumento::class,
                'alertable_id' => $documento->id,
                'para_roles' => ['Administrador', 'RRHH'],
                'fecha_vencimiento' => $documento->fecha_caducidad,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generar alertas de aptos médicos
     */
    protected function generarAlertasAptoMedico(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        $documentos = TrabajadorDocumento::with('trabajador')
            ->where('tipo', 'apto_medico')
            ->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<=', $fechaLimite)
            ->where('fecha_caducidad', '>=', now()->subDays(30))
            ->get();

        foreach ($documentos as $documento) {
            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'apto_medico',
                'titulo' => "Apto médico próximo a caducar",
                'mensaje' => "El apto médico del trabajador {$documento->trabajador->nombre_completo} caduca el " . $documento->fecha_caducidad->format('d/m/Y'),
                'prioridad' => $this->calcularPrioridad($documento->fecha_caducidad),
                'alertable_type' => TrabajadorDocumento::class,
                'alertable_id' => $documento->id,
                'para_roles' => ['Administrador', 'RRHH'],
                'fecha_vencimiento' => $documento->fecha_caducidad,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generar alertas de EPIs por caducidad
     */
    protected function generarAlertasEpiCaducidad(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        $epis = EpiInventario::with('catalogo')
            ->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<=', $fechaLimite)
            ->where('fecha_caducidad', '>=', now()->subDays(30))
            ->where('estado', '!=', 'baja')
            ->get();

        foreach ($epis as $epi) {
            $titulo = $epi->numero_serie
                ? "EPI próximo a caducar: {$epi->catalogo->nombre} (S/N: {$epi->numero_serie})"
                : "EPI próximo a caducar: {$epi->catalogo->nombre} (ID: {$epi->id})";

            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'epi_caducidad',
                'titulo' => $titulo,
                'mensaje' => "El EPI '{$epi->catalogo->nombre}' caduca el " . $epi->fecha_caducidad->format('d/m/Y'),
                'prioridad' => $this->calcularPrioridad($epi->fecha_caducidad),
                'alertable_type' => EpiInventario::class,
                'alertable_id' => $epi->id,
                'para_roles' => ['Administrador', 'RRHH', 'Encargado'],
                'fecha_vencimiento' => $epi->fecha_caducidad,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generar alertas de EPIs por revisión pendiente
     */
    protected function generarAlertasEpiRevision(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        // Obtener la última revisión de cada EPI que requiere revisión
        $ultimasRevisiones = EpiRevision::select('epi_inventario_id', DB::raw('MAX(id) as ultima_revision_id'))
            ->groupBy('epi_inventario_id')
            ->pluck('ultima_revision_id');

        $revisiones = EpiRevision::with(['epiInventario.catalogo'])
            ->whereIn('id', $ultimasRevisiones)
            ->whereNotNull('proxima_revision')
            ->where('proxima_revision', '<=', $fechaLimite)
            ->where('proxima_revision', '>=', now()->subDays(30))
            ->get();

        foreach ($revisiones as $revision) {
            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'epi_revision',
                'titulo' => "Revisión EPI pendiente: {$revision->epiInventario->catalogo->nombre}",
                'mensaje' => "La revisión del EPI '{$revision->epiInventario->catalogo->nombre}' está programada para el " . $revision->proxima_revision->format('d/m/Y'),
                'prioridad' => $this->calcularPrioridad($revision->proxima_revision),
                'alertable_type' => EpiRevision::class,
                'alertable_id' => $revision->id,
                'para_roles' => ['Administrador', 'RRHH'],
                'fecha_vencimiento' => $revision->proxima_revision,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generar alertas de ITV de vehículos
     */
    protected function generarAlertasItv(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        $vehiculos = Vehiculo::whereNotNull('fecha_proxima_itv')
            ->where('fecha_proxima_itv', '<=', $fechaLimite)
            ->where('fecha_proxima_itv', '>=', now()->subDays(30))
            ->where('estado', '!=', 'baja')
            ->get();

        foreach ($vehiculos as $vehiculo) {
            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'itv',
                'titulo' => "ITV próxima: {$vehiculo->matricula}",
                'mensaje' => "El vehículo {$vehiculo->matricula} ({$vehiculo->marca} {$vehiculo->modelo}) tiene la ITV el " . $vehiculo->fecha_proxima_itv->format('d/m/Y'),
                'prioridad' => $this->calcularPrioridad($vehiculo->fecha_proxima_itv),
                'alertable_type' => Vehiculo::class,
                'alertable_id' => $vehiculo->id,
                'para_roles' => ['Administrador', 'Contabilidad'],
                'fecha_vencimiento' => $vehiculo->fecha_proxima_itv,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generar alertas de seguros de vehículos
     */
    protected function generarAlertasSeguroVehiculo(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        $vehiculos = Vehiculo::whereNotNull('fecha_vencimiento_seguro')
            ->where('fecha_vencimiento_seguro', '<=', $fechaLimite)
            ->where('fecha_vencimiento_seguro', '>=', now()->subDays(30))
            ->where('estado', '!=', 'baja')
            ->get();

        foreach ($vehiculos as $vehiculo) {
            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'seguro_vehiculo',
                'titulo' => "Seguro próximo a vencer: {$vehiculo->matricula}",
                'mensaje' => "El seguro del vehículo {$vehiculo->matricula} vence el " . $vehiculo->fecha_vencimiento_seguro->format('d/m/Y'),
                'prioridad' => $this->calcularPrioridad($vehiculo->fecha_vencimiento_seguro),
                'alertable_type' => Vehiculo::class,
                'alertable_id' => $vehiculo->id,
                'para_roles' => ['Administrador', 'Contabilidad'],
                'fecha_vencimiento' => $vehiculo->fecha_vencimiento_seguro,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generar alertas de documentos de vehículo
     */
    protected function generarAlertasDocumentoVehiculo(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        $documentos = VehiculoDocumento::with('vehiculo')
            ->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<=', $fechaLimite)
            ->where('fecha_caducidad', '>=', now()->subDays(30))
            ->get();

        foreach ($documentos as $documento) {
            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'documento_vehiculo',
                'titulo' => "Documento vehículo próximo a caducar: {$documento->nombre}",
                'mensaje' => "El documento '{$documento->nombre}' del vehículo {$documento->vehiculo->matricula} caduca el " . $documento->fecha_caducidad->format('d/m/Y'),
                'prioridad' => $this->calcularPrioridad($documento->fecha_caducidad),
                'alertable_type' => VehiculoDocumento::class,
                'alertable_id' => $documento->id,
                'para_roles' => ['Administrador', 'Contabilidad'],
                'fecha_vencimiento' => $documento->fecha_caducidad,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generar alertas de contratos próximos a vencer
     */
    protected function generarAlertasContratoVencimiento(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        $contratos = Contrato::with('cliente')
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<=', $fechaLimite)
            ->where('fecha_fin', '>=', now()->subDays(30))
            ->where('estado', 'activo')
            ->get();

        foreach ($contratos as $contrato) {
            $clienteNombre = $contrato->cliente ? $contrato->cliente->nombre_comercial : 'Sin cliente';

            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'contrato_vencimiento',
                'titulo' => "Contrato próximo a vencer: {$contrato->titulo}",
                'mensaje' => "El contrato '{$contrato->titulo}' con {$clienteNombre} vence el " . $contrato->fecha_fin->format('d/m/Y'),
                'prioridad' => $this->calcularPrioridad($contrato->fecha_fin),
                'alertable_type' => Contrato::class,
                'alertable_id' => $contrato->id,
                'para_roles' => ['Administrador', 'Contabilidad'],
                'fecha_vencimiento' => $contrato->fecha_fin,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generar alertas de liberación de garantías
     */
    protected function generarAlertasContratoGarantia(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        $contratos = Contrato::with('cliente')
            ->where('tiene_retencion', true)
            ->whereNotNull('fecha_liberacion_garantia')
            ->where('fecha_liberacion_garantia', '<=', $fechaLimite)
            ->where('fecha_liberacion_garantia', '>=', now()->subDays(30))
            ->get();

        foreach ($contratos as $contrato) {
            $importe = number_format($contrato->importe_retenido ?? 0, 2, ',', '.');

            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'contrato_garantia',
                'titulo' => "Liberación de garantía: {$contrato->titulo}",
                'mensaje' => "La garantía de {$importe} € del contrato '{$contrato->titulo}' puede liberarse el " . $contrato->fecha_liberacion_garantia->format('d/m/Y'),
                'prioridad' => $this->calcularPrioridad($contrato->fecha_liberacion_garantia),
                'alertable_type' => Contrato::class,
                'alertable_id' => $contrato->id,
                'para_roles' => ['Administrador', 'Contabilidad'],
                'fecha_vencimiento' => $contrato->fecha_liberacion_garantia,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generar alertas de documentos CAE de subcontratas
     */
    protected function generarAlertasDocumentoCae(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        $documentos = SubcontrataDocumentoCae::with('subcontrata')
            ->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<=', $fechaLimite)
            ->where('fecha_caducidad', '>=', now()->subDays(30))
            ->get();

        foreach ($documentos as $documento) {
            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'documento_cae',
                'titulo' => "Doc. CAE próximo a caducar: {$documento->tipo}",
                'mensaje' => "El documento CAE '{$documento->tipo}' de {$documento->subcontrata->nombre} caduca el " . $documento->fecha_caducidad->format('d/m/Y'),
                'prioridad' => $this->calcularPrioridad($documento->fecha_caducidad),
                'alertable_type' => SubcontrataDocumentoCae::class,
                'alertable_id' => $documento->id,
                'para_roles' => ['Administrador', 'RRHH'],
                'fecha_vencimiento' => $documento->fecha_caducidad,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generar alertas de caducidades generales de empresa
     */
    protected function generarAlertasCaducidadGeneral(int $diasAntelacion): int
    {
        $fechaLimite = now()->addDays($diasAntelacion);
        $count = 0;

        $caducidades = CaducidadGeneral::where('alerta_activa', true)
            ->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<=', $fechaLimite)
            ->where('fecha_caducidad', '>=', now()->subDays(30))
            ->get();

        foreach ($caducidades as $caducidad) {
            if ($this->crearAlertaSiNoExiste([
                'tipo' => 'caducidad_general',
                'titulo' => "Caducidad empresa: {$caducidad->nombre}",
                'mensaje' => "La {$caducidad->nombre} caduca el " . $caducidad->fecha_caducidad->format('d/m/Y') . ($caducidad->descripcion ? ". {$caducidad->descripcion}" : ''),
                'prioridad' => $this->calcularPrioridad($caducidad->fecha_caducidad),
                'alertable_type' => CaducidadGeneral::class,
                'alertable_id' => $caducidad->id,
                'para_roles' => ['Administrador'],
                'fecha_vencimiento' => $caducidad->fecha_caducidad,
            ])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Crear alerta si no existe una activa para el mismo registro
     */
    protected function crearAlertaSiNoExiste(array $data): bool
    {
        // Verificar si ya existe alerta activa (no resuelta) para este registro
        $existe = Alerta::where('alertable_type', $data['alertable_type'])
            ->where('alertable_id', $data['alertable_id'])
            ->where('tipo', $data['tipo'])
            ->where('resuelta', false)
            ->exists();

        if ($existe) {
            return false;
        }

        $alerta = Alerta::create(array_merge($data, [
            'created_at' => now(),
        ]));

        // Enviar notificación por correo a los usuarios correspondientes
        $this->enviarNotificacionCorreo($alerta);

        return true;
    }

    /**
     * Enviar notificación por correo a los usuarios correspondientes
     */
    protected function enviarNotificacionCorreo(Alerta $alerta): void
    {
        try {
            $usuarios = collect();

            // Si tiene roles asignados, buscar usuarios con esos roles
            if ($alerta->para_roles && count($alerta->para_roles) > 0) {
                foreach ($alerta->para_roles as $rol) {
                    $usuariosConRol = User::role($rol)->get();
                    $usuarios = $usuarios->merge($usuariosConRol);
                }
            }

            // Si tiene usuario específico, agregarlo
            if ($alerta->para_usuario_id) {
                $usuarioEspecifico = User::find($alerta->para_usuario_id);
                if ($usuarioEspecifico) {
                    $usuarios->push($usuarioEspecifico);
                }
            }

            // Eliminar duplicados
            $usuarios = $usuarios->unique('id');

            // Enviar notificación a cada usuario
            foreach ($usuarios as $usuario) {
                $usuario->notify(new AlertaNotification($alerta));
            }

            Log::info("Notificación de alerta enviada", [
                'alerta_id' => $alerta->id,
                'tipo' => $alerta->tipo,
                'usuarios_notificados' => $usuarios->count(),
            ]);
        } catch (\Exception $e) {
            Log::error("Error al enviar notificación de alerta", [
                'alerta_id' => $alerta->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Calcular prioridad basada en días restantes
     */
    protected function calcularPrioridad($fecha): string
    {
        if (!$fecha instanceof Carbon) {
            $fecha = Carbon::parse($fecha);
        }

        $diasRestantes = now()->startOfDay()->diffInDays($fecha->startOfDay(), false);

        if ($diasRestantes <= 0) {
            return 'critica'; // Ya vencido
        }
        if ($diasRestantes <= 7) {
            return 'alta';
        }
        if ($diasRestantes <= 15) {
            return 'media';
        }
        return 'baja';
    }

    /**
     * Obtener etiqueta legible para un tipo de alerta
     */
    public static function getTipoLabel(string $tipo): string
    {
        return self::TIPOS_ALERTA[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo));
    }

    /**
     * Obtener color de badge según prioridad
     */
    public static function getPrioridadColor(string $prioridad): string
    {
        return match ($prioridad) {
            'critica' => 'danger',
            'alta' => 'warning',
            'media' => 'info',
            'baja' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Obtener icono según tipo de alerta
     */
    public static function getTipoIcono(string $tipo): string
    {
        return match ($tipo) {
            'formacion' => 'bi-mortarboard',
            'documento_trabajador', 'documento_vehiculo', 'documento_cae' => 'bi-file-earmark-text',
            'apto_medico' => 'bi-heart-pulse',
            'epi_caducidad', 'epi_revision' => 'bi-shield-check',
            'itv' => 'bi-car-front',
            'seguro_vehiculo' => 'bi-shield',
            'contrato_vencimiento', 'contrato_garantia' => 'bi-file-earmark-ruled',
            'caducidad_general' => 'bi-building',
            default => 'bi-bell',
        };
    }
}
