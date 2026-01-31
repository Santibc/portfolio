<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaquinariaDocumento extends Model
{
    protected $table = 'maquinaria_documentos';

    protected $fillable = [
        'maquinaria_id',
        'nombre',
        'archivo_path',
        'subido_por',
    ];

    public function maquinaria(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class);
    }

    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }
}
