<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    use SoftDeletes;

    protected $table = 'vehiculos';

    protected $fillable = [
        'vehiculo_tipo_id',
        'matricula',
        'marca',
        'modelo',
        'numero_bastidor',
        'fecha_matriculacion',
        'fecha_compra',
        'fecha_ultima_itv',
        'fecha_proxima_itv',
        'compania_seguro',
        'numero_poliza',
        'fecha_vencimiento_seguro',
        'coste_adquisicion',
        'coste_dia',
        'estado',
        'kilometraje_actual',
        'conductor_habitual_id',
        'notas',
    ];

    protected $casts = [
        'fecha_matriculacion' => 'date',
        'fecha_compra' => 'date',
        'fecha_ultima_itv' => 'date',
        'fecha_proxima_itv' => 'date',
        'fecha_vencimiento_seguro' => 'date',
        'coste_adquisicion' => 'decimal:2',
        'coste_dia' => 'decimal:2',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(VehiculoTipo::class, 'vehiculo_tipo_id');
    }

    public function conductorHabitual(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'conductor_habitual_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(VehiculoDocumento::class);
    }

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->matricula} - {$this->marca} {$this->modelo}";
    }

    public function getItvProximaAttribute(): bool
    {
        if (!$this->fecha_proxima_itv) return false;
        return $this->fecha_proxima_itv->diffInDays(now()) <= 30;
    }

    public function getSeguroProximoAttribute(): bool
    {
        if (!$this->fecha_vencimiento_seguro) return false;
        return $this->fecha_vencimiento_seguro->diffInDays(now()) <= 30;
    }

    // Scopes
    public function scopeOperativos($query)
    {
        return $query->where('estado', 'operativo');
    }
}
