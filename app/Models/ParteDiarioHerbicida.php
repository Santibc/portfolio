<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParteDiarioHerbicida extends Model
{
    protected $table = 'parte_diario_herbicidas';

    public $timestamps = false;

    protected $fillable = [
        'parte_diario_id',
        'producto',
        'numero_registro',
        'dosificacion',
        'cantidad',
        'unidad',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
    ];

    public function parteDiario(): BelongsTo
    {
        return $this->belongsTo(ParteDiario::class);
    }
}
