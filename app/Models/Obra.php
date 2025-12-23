<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Obra extends Model
{
    use SoftDeletes;

    protected $table = 'obras';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'cliente_id',
        'obra_tipo_id',
        'direccion',
        'localidad',
        'provincia',
        'codigo_postal',
        'coordenadas_lat',
        'coordenadas_lng',
        'linea',
        'trayecto',
        'pk_inicio',
        'pk_fin',
        'gerencia_jefatura',
        'distrito',
        'fecha_inicio_prevista',
        'fecha_fin_prevista',
        'fecha_inicio_real',
        'fecha_fin_real',
        'presupuesto',
        'coste_estimado',
        'margen_previsto',
        'estado',
        'riesgo_operativo',
        'tiene_penalizaciones',
        'importe_penalizacion_prevista',
        'contrato_id',
        'centro_coste',
        'encargado_id',
        'notas',
    ];

    protected $casts = [
        'fecha_inicio_prevista' => 'date',
        'fecha_fin_prevista' => 'date',
        'fecha_inicio_real' => 'date',
        'fecha_fin_real' => 'date',
        'presupuesto' => 'decimal:2',
        'coste_estimado' => 'decimal:2',
        'margen_previsto' => 'decimal:2',
        'importe_penalizacion_prevista' => 'decimal:2',
        'coordenadas_lat' => 'decimal:8',
        'coordenadas_lng' => 'decimal:8',
        'tiene_penalizaciones' => 'boolean',
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(ObraTipo::class, 'obra_tipo_id');
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    public function encargado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encargado_id');
    }

    public function hitos(): HasMany
    {
        return $this->hasMany(ObraHito::class)->orderBy('orden');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(ObraDocumento::class);
    }

    public function historial(): HasMany
    {
        return $this->hasMany(ObraHistorial::class)->orderByDesc('created_at');
    }

    public function partesDiarios(): HasMany
    {
        return $this->hasMany(ParteDiario::class);
    }

    public function fichajes(): HasMany
    {
        return $this->hasMany(Fichaje::class);
    }

    public function trabajadores(): BelongsToMany
    {
        return $this->belongsToMany(Trabajador::class, 'obra_trabajadores')
            ->withPivot(['fecha_inicio', 'fecha_fin', 'rol', 'activo'])
            ->withTimestamps();
    }

    public function trabajadoresActivos(): BelongsToMany
    {
        return $this->trabajadores()->wherePivot('activo', true);
    }

    public function cuadrillas(): BelongsToMany
    {
        return $this->belongsToMany(Cuadrilla::class, 'obra_cuadrillas')
            ->withPivot(['fecha_inicio', 'fecha_fin', 'activo'])
            ->withTimestamps();
    }

    public function subcontratas(): BelongsToMany
    {
        return $this->belongsToMany(Subcontrata::class, 'obra_subcontratas')
            ->withPivot(['fecha_inicio', 'fecha_fin', 'importe_contratado', 'activa'])
            ->withTimestamps();
    }

    public function maquinarias(): HasMany
    {
        return $this->hasMany(Maquinaria::class, 'obra_asignada_id');
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class);
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }

    public function primas(): HasMany
    {
        return $this->hasMany(PrimaTrabajador::class);
    }

    // Accessors
    public function getCodigoNombreAttribute(): string
    {
        return "{$this->codigo} - {$this->nombre}";
    }

    // Scopes
    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso');
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['aprobada', 'en_curso']);
    }
}
