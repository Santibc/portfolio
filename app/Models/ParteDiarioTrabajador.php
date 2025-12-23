<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParteDiarioTrabajador extends Model
{
    protected $table = 'parte_diario_trabajadores';

    public $timestamps = false;

    protected $fillable = [
        'parte_diario_id',
        'trabajador_id',
        'es_aplicador',
        'dni_aplicador',
        'horas_trabajadas',
    ];

    protected $casts = [
        'es_aplicador' => 'boolean',
        'horas_trabajadas' => 'decimal:2',
    ];

    public function parteDiario(): BelongsTo
    {
        return $this->belongsTo(ParteDiario::class);
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }
}
