<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaquinariaAsignacion extends Model
{
    protected $table = 'maquinaria_asignaciones';

    protected $fillable = [
        'maquinaria_id',
        'obra_id',
        'fecha_inicio',
        'fecha_fin',
        'notas',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function maquinaria(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class);
    }

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }
}
