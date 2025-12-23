<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrabajadorFormacion extends Model
{
    protected $table = 'trabajador_formaciones';

    protected $fillable = [
        'trabajador_id',
        'formacion_tipo_id',
        'fecha_realizacion',
        'fecha_caducidad',
        'centro_formacion',
        'certificado_path',
        'notas',
    ];

    protected $casts = [
        'fecha_realizacion' => 'date',
        'fecha_caducidad' => 'date',
    ];

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(FormacionTipo::class, 'formacion_tipo_id');
    }

    public function getProximoACaducarAttribute(): bool
    {
        if (!$this->fecha_caducidad) return false;
        if ($this->fecha_caducidad->isPast()) return false; // Ya caducada
        return $this->fecha_caducidad->diffInDays(now()) <= 30;
    }

    public function getCaducadoAttribute(): bool
    {
        if (!$this->fecha_caducidad) return false;
        return $this->fecha_caducidad->isPast();
    }
}
