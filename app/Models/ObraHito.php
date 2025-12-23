<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObraHito extends Model
{
    protected $table = 'obra_hitos';

    protected $fillable = [
        'obra_id',
        'nombre',
        'descripcion',
        'porcentaje_obra',
        'fecha_prevista',
        'fecha_completado',
        'importe_cobro',
        'completado',
        'orden',
    ];

    protected $casts = [
        'fecha_prevista' => 'date',
        'fecha_completado' => 'date',
        'importe_cobro' => 'decimal:2',
        'completado' => 'boolean',
    ];

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }
}
