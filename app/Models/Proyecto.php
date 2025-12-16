<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proyecto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proyectos';

    protected $fillable = [
        'codigo',
        'categoria_id',
        'agricultor_id',
        'nombre',
        'descripcion',
        // Campos v2.0 - Datos del cultivo
        'tipo_cultivo',
        'area_hectareas',
        'etapa_cultivo',
        'ano_inicio_cultivo',
        'ubicacion',
        'coordenadas',
        'monto_objetivo',
        'monto_recaudado',
        'inversion_minima',
        'inversion_maxima',
        'roi_anual',
        'duracion_meses',
        'periodo_cosecha_meses',
        'periodo_dividendos_dias',
        'fecha_inicio_recaudacion',
        'fecha_cierre_recaudacion',
        'fecha_inicio_proyecto',
        'fecha_fin_proyecto',
        'fecha_primer_dividendo',
        'nivel_riesgo',
        'estado',
        'aprobado_por',
        'aprobado_at',
        'notas_aprobacion',
        'motivo_rechazo',
        'verificado',
        'destacado',
        'orden_destacado',
        'activo',
        'datos_adicionales',
        // Campos v2.0 - Detalles del proyecto
        'objetivo_proyecto',
        'detalle_proceso_productivo',
        'cronograma_estimado',
        // Campos v2.0 - JSON por categoría
        'datos_financieros',
        'datos_earn',
        'datos_futuros',
        'datos_farming',
        // Campos v2.0 - Creación por admin
        'creado_por_admin',
        'admin_creador_id',
    ];

    protected $casts = [
        'monto_objetivo' => 'decimal:2',
        'monto_recaudado' => 'decimal:2',
        'inversion_minima' => 'decimal:2',
        'inversion_maxima' => 'decimal:2',
        'roi_anual' => 'decimal:2',
        'duracion_meses' => 'integer',
        'periodo_cosecha_meses' => 'integer',
        'periodo_dividendos_dias' => 'integer',
        'fecha_inicio_recaudacion' => 'date',
        'fecha_cierre_recaudacion' => 'date',
        'fecha_inicio_proyecto' => 'date',
        'fecha_fin_proyecto' => 'date',
        'fecha_primer_dividendo' => 'date',
        'aprobado_at' => 'datetime',
        'verificado' => 'boolean',
        'destacado' => 'boolean',
        'orden_destacado' => 'integer',
        'activo' => 'boolean',
        'datos_adicionales' => 'array',
        // Casts v2.0
        'area_hectareas' => 'decimal:2',
        'ano_inicio_cultivo' => 'integer',
        'datos_financieros' => 'array',
        'datos_earn' => 'array',
        'datos_futuros' => 'array',
        'datos_farming' => 'array',
        'creado_por_admin' => 'boolean',
    ];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaProyecto::class, 'categoria_id');
    }

    public function agricultor()
    {
        return $this->belongsTo(User::class, 'agricultor_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function rechazador()
    {
        return $this->belongsTo(User::class, 'rechazado_por');
    }

    public function inversiones()
    {
        return $this->hasMany(Inversion::class, 'proyecto_id');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoProyecto::class, 'proyecto_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenProyecto::class, 'proyecto_id');
    }

    public function actualizaciones()
    {
        return $this->hasMany(ActualizacionProyecto::class, 'proyecto_id');
    }

    public function dividendos()
    {
        return $this->hasMany(Dividendo::class, 'proyecto_id');
    }

    public function proyectosCrossFund()
    {
        return $this->hasMany(ProyectoCrossFund::class, 'proyecto_id');
    }

    // Relación v2.0
    /**
     * Admin que registró este proyecto (cuando admin crea proyectos)
     */
    public function creadoPorAdmin()
    {
        return $this->belongsTo(User::class, 'admin_creador_id');
    }

    // Helpers
    /**
     * Verifica si el proyecto puede ser editado
     */
    public function canEdit(): bool
    {
        return in_array($this->estado, ['borrador', 'rechazado']);
    }

    /**
     * Obtiene la imagen principal del proyecto
     */
    public function imagenPrincipal()
    {
        return $this->imagenes()->where('es_principal', true)->first()
            ?? $this->imagenes()->orderBy('orden')->first();
    }

    // Scopes
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeEnRecaudacion($query)
    {
        return $query->where('estado', 'en_recaudacion');
    }

    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['en_recaudacion', 'fondeado', 'en_ejecucion']);
    }

    // ==================== SCOPES v2.0 ====================

    /**
     * Filtrar proyectos creados por admin
     */
    public function scopeCreadosPorAdmin($query)
    {
        return $query->where('creado_por_admin', true);
    }

    /**
     * Filtrar proyectos por categoría código
     */
    public function scopeCategoriaCodigo($query, string $codigo)
    {
        return $query->whereHas('categoria', function ($q) use ($codigo) {
            $q->where('codigo', $codigo);
        });
    }

    /**
     * Filtrar proyectos FARMING
     */
    public function scopeFarming($query)
    {
        return $query->categoriaCodigo('FARMING');
    }

    /**
     * Filtrar proyectos por tipo de cultivo
     */
    public function scopeTipoCultivo($query, string $tipo)
    {
        return $query->where('tipo_cultivo', $tipo);
    }

    // ==================== ACCESSORS v2.0 ====================

    /**
     * Verificar si el proyecto fue creado por admin
     */
    public function getFueCreadoPorAdminAttribute(): bool
    {
        return $this->creado_por_admin === true;
    }

    /**
     * Verificar si es proyecto tipo FARMING
     */
    public function getEsFarmingAttribute(): bool
    {
        return $this->categoria?->codigo === 'FARMING';
    }

    /**
     * Verificar si es proyecto tipo EAR
     */
    public function getEsEarnAttribute(): bool
    {
        return $this->categoria?->codigo === 'EAR';
    }

    /**
     * Verificar si es proyecto tipo FUTUROS
     */
    public function getEsFuturosAttribute(): bool
    {
        return $this->categoria?->codigo === 'FUTUROS';
    }

    /**
     * Obtener datos específicos según categoría
     */
    public function getDatosEspecificosAttribute(): ?array
    {
        $codigo = $this->categoria?->codigo;

        return match ($codigo) {
            'EAR' => $this->datos_earn,
            'FUTUROS' => $this->datos_futuros,
            'FARMING' => $this->datos_farming,
            default => null,
        };
    }
}
