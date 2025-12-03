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
        'datos_adicionales'
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
        'datos_adicionales' => 'array'
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
}
