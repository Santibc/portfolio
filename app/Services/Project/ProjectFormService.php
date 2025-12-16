<?php

namespace App\Services\Project;

use App\Enums\ProjectStatus;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\CategoriaProyecto;
use App\Models\PerfilAgricultor;
use App\Services\Farmer\FarmerCreationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProjectFormService
{
    public function __construct(
        private ProjectCodeGeneratorService $codeGenerator,
        private FarmerCreationService $farmerService
    ) {}

    // ==================== FASE 1: DATOS BÁSICOS ====================

    /**
     * Validar datos de la Fase 1
     *
     * @param array $data
     * @param bool $isAdmin Si es admin, se validan también datos del agricultor
     * @return array Datos validados
     * @throws ValidationException
     */
    public function validatePhase1Data(array $data, bool $isAdmin = false): array
    {
        $rules = [
            // Datos del proyecto
            'categoria_id' => 'required|exists:categorias_proyecto,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:5000',
            'tipo_cultivo' => 'required|string|max:100',
            'area_hectareas' => 'required|numeric|min:0.1|max:99999',
            'etapa_cultivo' => 'required|in:siembra,crecimiento,cosecha,transformacion,otro',
            'ano_inicio_cultivo' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'ubicacion' => 'required|string|max:500',
            'meta_financiamiento' => 'required|numeric|min:100000',
            'plazo_meses' => 'required|integer|min:1|max:240',
            'roi_proyectado' => 'required|numeric|min:0|max:100',
        ];

        // Si es admin, agregar validación de datos del agricultor
        if ($isAdmin) {
            $rules = array_merge($rules, [
                'agricultor_id' => 'nullable|exists:users,id',
                'agricultor_nuevo' => 'nullable|boolean',
                // Datos del nuevo agricultor
                'agricultor_name' => 'required_if:agricultor_nuevo,true|string|max:255',
                'agricultor_email' => 'required_if:agricultor_nuevo,true|email|max:255',
                'agricultor_telefono' => 'nullable|string|max:20',
                'agricultor_documento_identidad' => 'required_if:agricultor_nuevo,true|string|max:50',
                'agricultor_tipo_documento' => 'nullable|in:CC,CE,NIT,PASSPORT,DNI',
                'agricultor_fecha_nacimiento' => 'nullable|date|before:today',
                'agricultor_pais' => 'nullable|string|max:100',
                'agricultor_ciudad' => 'nullable|string|max:100',
                'agricultor_direccion' => 'nullable|string|max:500',
            ]);
        }

        $validator = Validator::make($data, $rules, $this->getValidationMessages());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Guardar Fase 1 del proyecto
     *
     * @param array $data
     * @param User $creador Usuario que crea (agricultor o admin)
     * @param bool $isAdmin
     * @return Proyecto
     * @throws \Exception
     */
    public function savePhase1(array $data, User $creador, bool $isAdmin = false): Proyecto
    {
        try {
            DB::beginTransaction();

            $agricultor = null;

            // Si es admin y se crea nuevo agricultor
            if ($isAdmin) {
                if (!empty($data['agricultor_nuevo']) && $data['agricultor_nuevo']) {
                    // Procesar foto si se subió
                    $fotoPath = null;
                    if (isset($data['agricultor_foto']) && $data['agricultor_foto'] instanceof UploadedFile) {
                        $fotoPath = $this->uploadFarmerPhoto($data['agricultor_foto']);
                    }

                    // Crear nuevo agricultor
                    $agricultor = $this->farmerService->createFarmerBasic([
                        'name' => $data['agricultor_name'],
                        'email' => $data['agricultor_email'],
                        'telefono' => $data['agricultor_telefono'] ?? null,
                        'documento_identidad' => $data['agricultor_documento_identidad'],
                        'tipo_documento' => $data['agricultor_tipo_documento'] ?? 'CC',
                        'fecha_nacimiento' => $data['agricultor_fecha_nacimiento'] ?? null,
                        'pais' => $data['agricultor_pais'] ?? 'Colombia',
                        'ciudad' => $data['agricultor_ciudad'] ?? null,
                        'direccion' => $data['agricultor_direccion'] ?? null,
                        'foto_perfil' => $fotoPath,
                    ], $creador);
                } elseif (!empty($data['agricultor_id'])) {
                    // Usar agricultor existente
                    $agricultor = User::findOrFail($data['agricultor_id']);
                } else {
                    throw new \Exception('Debe seleccionar un agricultor existente o crear uno nuevo.');
                }
            } else {
                // Si no es admin, el creador es el agricultor
                $agricultor = $creador;
            }

            // Generar código único
            $categoria = CategoriaProyecto::findOrFail($data['categoria_id']);
            $codigo = $this->codeGenerator->generateUniqueCode($categoria);

            // Calcular fechas de recaudación basadas en el plazo
            $fechaInicioRecaudacion = now()->format('Y-m-d');
            $fechaCierreRecaudacion = now()->addDays(30)->format('Y-m-d'); // 30 días para recaudar

            // Crear proyecto
            $proyecto = Proyecto::create([
                'codigo' => $codigo,
                'categoria_id' => $data['categoria_id'],
                'agricultor_id' => $agricultor->id,
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'tipo_cultivo' => $data['tipo_cultivo'],
                'area_hectareas' => $data['area_hectareas'],
                'etapa_cultivo' => $data['etapa_cultivo'],
                'ano_inicio_cultivo' => $data['ano_inicio_cultivo'] ?? null,
                'ubicacion' => $data['ubicacion'],
                // Campos financieros
                'monto_objetivo' => $data['meta_financiamiento'],
                'monto_recaudado' => 0,
                'inversion_minima' => 100000, // Mínimo por defecto: $100,000
                'inversion_maxima' => null,
                'roi_anual' => $data['roi_proyectado'],
                'duracion_meses' => $data['plazo_meses'],
                'periodo_cosecha_meses' => null,
                'periodo_dividendos_dias' => 30, // Dividendos mensuales por defecto
                // Fechas (provisionales, se pueden editar después)
                'fecha_inicio_recaudacion' => $fechaInicioRecaudacion,
                'fecha_cierre_recaudacion' => $fechaCierreRecaudacion,
                // Estado y configuración
                'estado' => ProjectStatus::BORRADOR->value,
                'nivel_riesgo' => 'medio', // Riesgo medio por defecto
                'activo' => true,
                'verificado' => false,
                'destacado' => false,
                'orden_destacado' => 0,
                'creado_por_admin' => $isAdmin,
                'admin_creador_id' => $isAdmin ? $creador->id : null,
            ]);

            DB::commit();

            Log::info("Proyecto Fase 1 creado", [
                'proyecto_id' => $proyecto->id,
                'codigo' => $codigo,
                'agricultor_id' => $agricultor->id,
                'creado_por_admin' => $isAdmin
            ]);

            return $proyecto;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en savePhase1: " . $e->getMessage());
            throw $e;
        }
    }

    // ==================== FASE 2: EVALUACIÓN TÉCNICA ====================

    /**
     * Validar datos de la Fase 2
     *
     * @param array $data
     * @return array Datos validados
     * @throws ValidationException
     */
    public function validatePhase2Data(array $data): array
    {
        $rules = [
            // Perfil del agricultor
            'tipo_persona' => 'required|in:natural,juridica',
            'nombre_empresa' => 'nullable|required_if:tipo_persona,juridica|string|max:255',
            'nit' => 'nullable|required_if:tipo_persona,juridica|string|max:50',
            'representante_legal' => 'nullable|string|max:255',
            'direccion_finca' => 'nullable|string|max:1000',
            'cultivo_asegurado' => 'boolean',

            // Experiencia
            'anos_experiencia' => 'nullable|integer|min:0|max:100',
            'formacion_capacitaciones' => 'nullable|string|max:2000',
            'cantidad_cosechas' => 'nullable|integer|min:0',
            'produccion_promedio' => 'nullable|string|max:500',

            // Equipo de trabajo
            'num_personas_trabajando' => 'nullable|integer|min:0',
            'familia_trabaja_cultivo' => 'boolean',
            'roles_principales' => 'nullable|string|max:2000',
            'nivel_tecnificacion' => 'nullable|in:manual,semi_tecnificado,tecnificado',

            // Estado del predio
            'tiene_riego' => 'boolean',
            'tiene_bodega' => 'boolean',
            'tiene_transformacion' => 'boolean',
            'tiene_transporte' => 'boolean',
            'accesibilidad' => 'nullable|string|max:1000',
            'riesgos_naturales' => 'nullable|string|max:1000',

            // Familiares (array)
            'familia' => 'nullable|array',
            'familia.*.parentesco' => 'required_with:familia.*.nombre|in:esposa,esposo,hijo,hija,padre,madre,hermano,hermana,otro',
            'familia.*.nombre' => 'required_with:familia.*.parentesco|string|max:255',
            'familia.*.edad' => 'nullable|integer|min:0|max:150',
            'familia.*.nivel_educativo' => 'nullable|in:ninguno,primaria,secundaria,tecnico,profesional,posgrado',
            'familia.*.estudia_actualmente' => 'nullable|in:si,no,estudio_aplazado',
            'familia.*.trabaja_en_cultivo' => 'boolean',

            // Datos adicionales del proyecto (Fase 2)
            'objetivo_proyecto' => 'nullable|string|max:5000',
            'detalle_proceso_productivo' => 'nullable|string|max:5000',
            'cronograma_estimado' => 'nullable|string|max:5000',
        ];

        $validator = Validator::make($data, $rules, $this->getValidationMessages());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Guardar Fase 2 del proyecto
     *
     * @param Proyecto $proyecto
     * @param array $data
     * @return Proyecto
     * @throws \Exception
     */
    public function savePhase2(Proyecto $proyecto, array $data): Proyecto
    {
        try {
            DB::beginTransaction();

            $agricultor = $proyecto->agricultor;

            // Datos del perfil del agricultor
            $perfilData = [
                'tipo_persona' => $data['tipo_persona'] ?? 'natural',
                'nombre_empresa' => $data['nombre_empresa'] ?? null,
                'nit' => $data['nit'] ?? null,
                'representante_legal' => $data['representante_legal'] ?? null,
                'direccion_finca' => $data['direccion_finca'] ?? null,
                'cultivo_asegurado' => $data['cultivo_asegurado'] ?? false,
                'anos_experiencia' => $data['anos_experiencia'] ?? null,
                'formacion_capacitaciones' => $data['formacion_capacitaciones'] ?? null,
                'cantidad_cosechas' => $data['cantidad_cosechas'] ?? null,
                'produccion_promedio' => $data['produccion_promedio'] ?? null,
                'num_personas_trabajando' => $data['num_personas_trabajando'] ?? null,
                'familia_trabaja_cultivo' => $data['familia_trabaja_cultivo'] ?? false,
                'roles_principales' => $data['roles_principales'] ?? null,
                'nivel_tecnificacion' => $data['nivel_tecnificacion'] ?? null,
                'tiene_riego' => $data['tiene_riego'] ?? false,
                'tiene_bodega' => $data['tiene_bodega'] ?? false,
                'tiene_transformacion' => $data['tiene_transformacion'] ?? false,
                'tiene_transporte' => $data['tiene_transporte'] ?? false,
                'accesibilidad' => $data['accesibilidad'] ?? null,
                'riesgos_naturales' => $data['riesgos_naturales'] ?? null,
            ];

            // Actualizar perfil del agricultor
            $this->farmerService->updateFarmerProfile(
                $agricultor,
                $perfilData,
                $data['familia'] ?? []
            );

            // Actualizar datos del proyecto
            $proyecto->update([
                'objetivo_proyecto' => $data['objetivo_proyecto'] ?? null,
                'detalle_proceso_productivo' => $data['detalle_proceso_productivo'] ?? null,
                'cronograma_estimado' => $data['cronograma_estimado'] ?? null,
            ]);

            DB::commit();

            Log::info("Proyecto Fase 2 guardado", [
                'proyecto_id' => $proyecto->id
            ]);

            return $proyecto->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en savePhase2: " . $e->getMessage());
            throw $e;
        }
    }

    // ==================== FASE 3: EVALUACIÓN FINANCIERA ====================

    /**
     * Validar datos de la Fase 3
     *
     * @param array $data
     * @param Proyecto $proyecto
     * @return array Datos validados
     * @throws ValidationException
     */
    public function validatePhase3Data(array $data, Proyecto $proyecto): array
    {
        $rules = [
            // Desglose de inversión solicitada
            'inversion_insumos' => 'nullable|numeric|min:0',
            'inversion_mano_obra' => 'nullable|numeric|min:0',
            'inversion_equipos' => 'nullable|numeric|min:0',
            'inversion_transporte' => 'nullable|numeric|min:0',
            'inversion_certificaciones' => 'nullable|numeric|min:0',
            'inversion_empaques' => 'nullable|numeric|min:0',
            'inversion_marketing' => 'nullable|numeric|min:0',

            // Proyecciones
            'produccion_estimada' => 'nullable|string|max:500',
            'precio_venta_estimado' => 'nullable|numeric|min:0',
            'canales_venta_actuales' => 'nullable|string|max:1000',
            'canales_venta_deseados' => 'nullable|string|max:1000',
            'proyeccion_ingresos' => 'nullable|string|max:2000',
            'punto_equilibrio' => 'nullable|string|max:500',
            'margen_ganancia' => 'nullable|numeric|min:0|max:100',

            // Riesgos
            'riesgo_plagas' => 'nullable|string|max:1000',
            'riesgo_clima' => 'nullable|string|max:1000',
            'riesgo_competencia' => 'nullable|string|max:1000',
            'riesgo_acceso_mercados' => 'nullable|string|max:1000',
            'riesgo_regulaciones' => 'nullable|string|max:1000',
        ];

        // Agregar reglas específicas según la categoría del proyecto
        $categoria = $proyecto->categoria;
        if ($categoria) {
            if ($categoria->codigo === 'EAR') {
                $rules = array_merge($rules, [
                    'earn_estado_empaque' => 'nullable|string|max:500',
                    'earn_certificaciones_pendientes' => 'nullable|array',
                    'earn_capacidad_produccion' => 'nullable|string|max:500',
                    'earn_laboratorio_procesamiento' => 'nullable|string|max:500',
                    'earn_costos_por_unidad' => 'nullable|numeric|min:0',
                    'earn_inventario_disponible' => 'nullable|string|max:500',
                    'earn_necesidades_escalar' => 'nullable|string|max:2000',
                ]);
            }

            if ($categoria->codigo === 'FUTUROS') {
                $rules = array_merge($rules, [
                    'futuros_plan_expansion' => 'nullable|string|max:2000',
                    'futuros_infraestructura_requerida' => 'nullable|string|max:2000',
                    'futuros_proyeccion_3_anos' => 'nullable|string|max:2000',
                    'futuros_proyeccion_5_anos' => 'nullable|string|max:2000',
                    'futuros_amenazas_largo_plazo' => 'nullable|string|max:2000',
                    'futuros_financiacion_por_fases' => 'nullable|string|max:2000',
                ]);
            }

            if ($categoria->codigo === 'FARMING') {
                $rules = array_merge($rules, [
                    'farming_tipo_asociacion' => 'nullable|string|max:255',
                    'farming_numero_asociados' => 'nullable|integer|min:1',
                    'farming_hectareas_totales' => 'nullable|numeric|min:0',
                    'farming_commodities' => 'nullable|array',
                    'farming_destino_exportacion' => 'nullable|string|max:500',
                    'farming_certificaciones_exportacion' => 'nullable|array',
                    'farming_proyeccion_dividendos' => 'nullable|string|max:2000',
                ]);
            }
        }

        $validator = Validator::make($data, $rules, $this->getValidationMessages());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Guardar Fase 3 del proyecto
     *
     * @param Proyecto $proyecto
     * @param array $data
     * @return Proyecto
     * @throws \Exception
     */
    public function savePhase3(Proyecto $proyecto, array $data): Proyecto
    {
        try {
            DB::beginTransaction();

            // Construir JSON de datos financieros
            $datosFinancieros = [
                'inversion_solicitada' => [
                    'insumos' => $data['inversion_insumos'] ?? 0,
                    'mano_obra' => $data['inversion_mano_obra'] ?? 0,
                    'equipos' => $data['inversion_equipos'] ?? 0,
                    'transporte' => $data['inversion_transporte'] ?? 0,
                    'certificaciones' => $data['inversion_certificaciones'] ?? 0,
                    'empaques' => $data['inversion_empaques'] ?? 0,
                    'marketing' => $data['inversion_marketing'] ?? 0,
                ],
                'proyecciones' => [
                    'produccion_estimada' => $data['produccion_estimada'] ?? null,
                    'precio_venta_estimado' => $data['precio_venta_estimado'] ?? 0,
                    'canales_venta_actuales' => $data['canales_venta_actuales'] ?? null,
                    'canales_venta_deseados' => $data['canales_venta_deseados'] ?? null,
                    'proyeccion_ingresos' => $data['proyeccion_ingresos'] ?? null,
                    'punto_equilibrio' => $data['punto_equilibrio'] ?? null,
                    'margen_ganancia' => $data['margen_ganancia'] ?? 0,
                ],
                'riesgos' => [
                    'plagas' => $data['riesgo_plagas'] ?? null,
                    'clima' => $data['riesgo_clima'] ?? null,
                    'competencia' => $data['riesgo_competencia'] ?? null,
                    'acceso_mercados' => $data['riesgo_acceso_mercados'] ?? null,
                    'regulaciones' => $data['riesgo_regulaciones'] ?? null,
                ],
            ];

            // Datos EARN si aplica
            $datosEarn = null;
            $categoria = $proyecto->categoria;
            if ($categoria && $categoria->codigo === 'EAR') {
                $datosEarn = [
                    'estado_empaque' => $data['earn_estado_empaque'] ?? null,
                    'certificaciones_pendientes' => $data['earn_certificaciones_pendientes'] ?? [],
                    'capacidad_produccion' => $data['earn_capacidad_produccion'] ?? null,
                    'laboratorio_procesamiento' => $data['earn_laboratorio_procesamiento'] ?? null,
                    'costos_por_unidad' => $data['earn_costos_por_unidad'] ?? 0,
                    'inventario_disponible' => $data['earn_inventario_disponible'] ?? null,
                    'necesidades_escalar' => $data['earn_necesidades_escalar'] ?? null,
                ];
            }

            // Datos FUTUROS si aplica
            $datosFuturos = null;
            if ($categoria && $categoria->codigo === 'FUTUROS') {
                $datosFuturos = [
                    'plan_expansion' => $data['futuros_plan_expansion'] ?? null,
                    'infraestructura_requerida' => $data['futuros_infraestructura_requerida'] ?? null,
                    'proyeccion_3_anos' => $data['futuros_proyeccion_3_anos'] ?? null,
                    'proyeccion_5_anos' => $data['futuros_proyeccion_5_anos'] ?? null,
                    'amenazas_largo_plazo' => $data['futuros_amenazas_largo_plazo'] ?? null,
                    'financiacion_por_fases' => $data['futuros_financiacion_por_fases'] ?? null,
                ];
            }

            // Datos FARMING si aplica
            $datosFarming = null;
            if ($categoria && $categoria->codigo === 'FARMING') {
                $datosFarming = [
                    'tipo_asociacion' => $data['farming_tipo_asociacion'] ?? null,
                    'numero_asociados' => $data['farming_numero_asociados'] ?? null,
                    'hectareas_totales' => $data['farming_hectareas_totales'] ?? null,
                    'commodities' => $data['farming_commodities'] ?? [],
                    'destino_exportacion' => $data['farming_destino_exportacion'] ?? null,
                    'certificaciones_exportacion' => $data['farming_certificaciones_exportacion'] ?? [],
                    'proyeccion_dividendos' => $data['farming_proyeccion_dividendos'] ?? null,
                ];
            }

            // Actualizar proyecto
            $proyecto->update([
                'datos_financieros' => $datosFinancieros,
                'datos_earn' => $datosEarn,
                'datos_futuros' => $datosFuturos,
                'datos_farming' => $datosFarming,
            ]);

            DB::commit();

            Log::info("Proyecto Fase 3 guardado", [
                'proyecto_id' => $proyecto->id
            ]);

            return $proyecto->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en savePhase3: " . $e->getMessage());
            throw $e;
        }
    }

    // ==================== UTILIDADES ====================

    /**
     * Subir foto del agricultor
     *
     * @param UploadedFile $file
     * @return string Ruta relativa de la foto
     */
    private function uploadFarmerPhoto(UploadedFile $file): string
    {
        $directory = public_path('uploads/agricultores/fotos');

        // Crear directorio si no existe
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Generar nombre único
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;

        // Mover archivo
        $file->move($directory, $fileName);

        return 'uploads/agricultores/fotos/' . $fileName;
    }

    /**
     * Obtener la fase actual de un proyecto
     *
     * @param Proyecto $proyecto
     * @return int
     */
    public function getCurrentPhase(Proyecto $proyecto): int
    {
        // Fase 1: Datos básicos
        if (empty($proyecto->objetivo_proyecto) && empty($proyecto->detalle_proceso_productivo)) {
            return 1;
        }

        // Fase 2: Evaluación técnica
        if (empty($proyecto->datos_financieros)) {
            return 2;
        }

        // Fase 3: Completo
        return 3;
    }

    /**
     * Verificar si el proyecto puede avanzar a la siguiente fase
     *
     * @param Proyecto $proyecto
     * @param int $nextPhase
     * @return bool
     */
    public function canAdvanceToPhase(Proyecto $proyecto, int $nextPhase): bool
    {
        $currentPhase = $this->getCurrentPhase($proyecto);
        return $currentPhase >= ($nextPhase - 1);
    }

    /**
     * Verificar si el proyecto está completo (3 fases)
     *
     * @param Proyecto $proyecto
     * @return bool
     */
    public function isComplete(Proyecto $proyecto): bool
    {
        return $this->getCurrentPhase($proyecto) >= 3;
    }

    /**
     * Obtener mensajes de validación personalizados
     *
     * @return array
     */
    private function getValidationMessages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'required_if' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'numeric' => 'El campo :attribute debe ser un número.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'email' => 'El campo :attribute debe ser un email válido.',
            'max' => 'El campo :attribute no puede tener más de :max caracteres.',
            'min' => 'El campo :attribute debe ser al menos :min.',
            'in' => 'El valor seleccionado para :attribute no es válido.',
            'exists' => 'El valor seleccionado para :attribute no existe.',
            'boolean' => 'El campo :attribute debe ser verdadero o falso.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
            'array' => 'El campo :attribute debe ser una lista.',
        ];
    }
}
