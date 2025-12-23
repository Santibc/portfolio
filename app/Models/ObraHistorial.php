<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObraHistorial extends Model
{
    protected $table = 'obra_historial';

    public $timestamps = false;

    protected $fillable = [
        'obra_id',
        'estado_anterior',
        'estado_nuevo',
        'comentario',
        'cambiado_por',
    ];

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function cambiadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cambiado_por');
    }
}
